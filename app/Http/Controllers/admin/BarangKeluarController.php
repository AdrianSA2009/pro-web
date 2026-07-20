<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Traits\HasStockAlert;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\UnitBarang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BarangKeluarController extends Controller
{
    use HasStockAlert;

    public function index(Request $request)
    {
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $barangKeluar = BarangKeluar::when($search, function ($query, $search) {
            return $query->where(function($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                  ->orWhere('penerima', 'like', "%{$search}%");
            })
            ->orWhereHas('karyawan', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        })
        ->when($dateFrom, function ($query, $dateFrom) {
            return $query->whereDate('tgl_keluar', '>=', $dateFrom);
        })
        ->when($dateTo, function ($query, $dateTo) {
            return $query->whereDate('tgl_keluar', '<=', $dateTo);
        })
        ->with(['barang.kategori', 'karyawan', 'user', 'unitBarang.barang'])
        ->orderBy('tgl_keluar', 'desc')
        ->paginate(10)
        ->withQueryString();

        $barang = Barang::with('kategori')->get();
        $karyawan = \App\Models\User::all();

        return view('admin.barangkeluar', compact('barangKeluar', 'barang', 'karyawan'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'penerima' => 'required|string|max:255',
            'tgl_keluar' => 'required|date',
            'serial_number' => 'required|array|min:1',
            'serial_number.*' => 'required|string|exists:unit_barang,serial_number',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Data tidak lengkap atau Serial Number tidak valid.');
        }

        DB::beginTransaction();

        try {
            $serialNumbers = $request->input('serial_number');
            $units = UnitBarang::whereIn('serial_number', $serialNumbers)
                               ->whereNull('barang_keluar_id')
                               ->get();

            if ($units->count() != count($serialNumbers)) {
                return redirect()->back()->with('error', 'Beberapa unit sudah tidak tersedia atau tidak valid.');
            }

            $dateStr = date('Ymd', strtotime($request->tgl_keluar));
            $randomSeq = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            
            $barangKeluar = new BarangKeluar();
            $barangKeluar->kode_transaksi = "OUT-{$dateStr}-{$randomSeq}";
            $barangKeluar->barang_id = $units->first()->barang_id; 
            $barangKeluar->user_id = Auth::id(); 
            $barangKeluar->penerima = $request->penerima;
            $barangKeluar->jumlah = $units->count();
            $barangKeluar->tgl_keluar = $request->tgl_keluar;
            $barangKeluar->save();

            $groupedUnits = $units->groupBy('barang_id');

            // Tandai unit sebagai keluar
            foreach ($groupedUnits as $barangId => $group) {
                foreach ($group as $unit) {
                    $unit->barang_keluar_id = $barangKeluar->id;
                    $unit->save();
                }
            }

            // Sinkronisasi stok dari jumlah unit aktual
            foreach ($groupedUnits as $barangId => $group) {
                $b = $this->syncStok($barangId);
                if ($b) {
                    $this->checkAndCreateLowStockAlert($b);
                }
            }

            DB::commit();

            return redirect()->route('barang-keluar.index')
                ->with('toast_success', 'Transaksi barang keluar berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }
    }

    public function checkSerial(Request $request)
    {
        $serial = strtoupper(trim($request->query('serial_number', '')));
        $barangKeluarId = $request->query('barang_keluar_id');

        if ($serial === '') {
            return response()->json(['valid' => false, 'message' => 'Serial number kosong']);
        }

        $unit = UnitBarang::with('barang')->where('serial_number', $serial)->first();

        if (!$unit) {
            return response()->json(['valid' => false, 'message' => 'Unit tidak ditemukan']);
        }

        if ($unit->barang_keluar_id !== null && $unit->barang_keluar_id != $barangKeluarId) {
            return response()->json(['valid' => false, 'message' => 'Unit tidak tersedia (sudah keluar)']);
        }

        return response()->json([
            'valid' => true, 
            'nama_barang' => $unit->barang->nama ?? '-',
            'unit_id' => $unit->id
        ]);
    }

    public function update(Request $request, $id)
    {
        $barangKeluar = BarangKeluar::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'penerima' => 'required|string|max:255',
            'tgl_keluar' => 'required|date',
            'serial_number' => 'required|array|min:1',
            'serial_number.*' => 'required|string',
        ]);
        
        $validator->validate();

        $serialNumbers = collect($request->input('serial_number', []))
            ->map(fn($sn) => strtoupper(trim($sn)))
            ->filter()
            ->unique()
            ->all();

        $unitsCheck = UnitBarang::whereIn('serial_number', $serialNumbers)->get();

        if ($unitsCheck->count() !== count($serialNumbers)) {
            return redirect()->back()->with('error', 'Beberapa serial number tidak ditemukan dalam sistem.');
        }

        $invalidUnits = $unitsCheck->filter(function($unit) use ($barangKeluar) {
            return $unit->barang_keluar_id != null && $unit->barang_keluar_id != $barangKeluar->id;
        });

        if ($invalidUnits->isNotEmpty()) {
            return redirect()->back()->with('error', 'Beberapa unit tidak tersedia (sudah keluar).');
        }

        DB::beginTransaction();

        try {
            // 1. Kumpulkan barang_id yang terpengaruh (lama + baru)
            $oldUnits = UnitBarang::where('barang_keluar_id', $barangKeluar->id)->get();
            $affectedBarangIds = $oldUnits->pluck('barang_id')->unique();

            // 2. Lepaskan unit lama
            UnitBarang::where('barang_keluar_id', $barangKeluar->id)->update([
                'barang_keluar_id' => null
            ]);

            // 3. UPDATE TRANSAKSI UTAMA
            $barangKeluar->penerima = $request->penerima;
            $barangKeluar->tgl_keluar = $request->tgl_keluar;
            $barangKeluar->barang_id = $unitsCheck->first()->barang_id;
            $barangKeluar->jumlah = count($serialNumbers);
            $barangKeluar->save();

            // 4. Pasangkan unit baru
            UnitBarang::whereIn('serial_number', $serialNumbers)->update([
                'barang_keluar_id' => $barangKeluar->id
            ]);

            // 5. Tambahkan barang_id baru ke affected list
            $affectedBarangIds = $affectedBarangIds
                ->merge($unitsCheck->pluck('barang_id'))
                ->unique();

            // 6. Sinkronisasi stok dari jumlah unit aktual
            foreach ($affectedBarangIds as $bId) {
                $b = $this->syncStok($bId);
                if ($b) {
                    $this->checkAndCreateLowStockAlert($b);
                }
            }

            DB::commit();

            return redirect()->route('barang-keluar.index')
                ->with('toast_success', 'Transaksi barang keluar berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $barangKeluar = BarangKeluar::findOrFail($id);

        DB::beginTransaction();

        try {
            $units = UnitBarang::where('barang_keluar_id', $id)->get();
            $affectedBarangIds = $units->pluck('barang_id')->unique();

            // Lepaskan relasi unit
            foreach ($units as $u) {
                $u->barang_keluar_id = null;
                $u->save();
            }

            // Sinkronisasi stok dari jumlah unit aktual
            foreach ($affectedBarangIds as $bId) {
                $b = $this->syncStok($bId);
                if ($b) {
                    $this->checkAndCreateLowStockAlert($b);
                }
            }

            $barangKeluar->delete();

            DB::commit();

            return redirect()->route('barang-keluar.index')
                ->with('toast_success', 'Transaksi barang keluar berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
}
