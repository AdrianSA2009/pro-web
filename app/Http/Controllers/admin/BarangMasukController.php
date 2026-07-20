<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Traits\HasStockAlert;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\UnitBarang;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BarangMasukController extends Controller
{
    use HasStockAlert;
    public function index(Request $request)
    {
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $barangMasuk = BarangMasuk::when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('kode_transaksi', 'like', "%{$search}%")
                        ->orWhereHas('supplier', function($sq) use ($search) {
                            $sq->where('nama', 'like', "%{$search}%");
                        })
                        ->orWhereHas('karyawan', function($sq) use ($search) {
                            $sq->where('nama', 'like', "%{$search}%");
                        });
                });
            })
            ->when($dateFrom, function ($query, $dateFrom) {
                return $query->whereDate('tgl_masuk', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query, $dateTo) {
                return $query->whereDate('tgl_masuk', '<=', $dateTo);
            })
        ->with(['barang.kategori', 'supplier', 'karyawan', 'unitBarang'])
        ->orderBy('tgl_masuk', 'desc')
        ->paginate(10)
        ->withQueryString();
        
        $suppliers = DB::table('suppliers')->get();
        $kategori = DB::table('kategori')->get();
    
        return view('admin.barangmasuk', compact('barangMasuk', 'kategori', 'suppliers'));
    }

    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required|exists:kategori,id',
            'nama_barang' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'tgl_masuk' => 'required|date',
            'serial_number' => 'required|array|min:1',
            'serial_number.*' => 'required|string|distinct|unique:unit_barang,serial_number',
        ], [
            'serial_number.unique' => 'Serial number ini sudah terdaftar!',
        ]);

        $validator->after(function ($validator) use ($request) {
            $serialNumbers = collect($request->input('serial_number', []))
                ->map(fn($sn) => strtoupper(trim($sn)))
                ->filter()
                ->all();

            if (count($serialNumbers) !== count(array_unique($serialNumbers))) {
                $validator->errors()->add('serial_number', 'Terdapat serial number duplikat dalam daftar unit.');
            }
        });

        $validator->validate();

        $request->merge([
            'serial_number' => collect($request->input('serial_number', []))
                ->map(fn($sn) => strtoupper(trim($sn)))
                ->all(),
        ]);

        DB::beginTransaction();

        try {
            $tanggal = date('Ymd', strtotime($request->tgl_masuk));
            $hitungTransaksi = BarangMasuk::where('tgl_masuk', $request->tgl_masuk)->count() + 1;
            $kodeTransaksi = 'IN-' . $tanggal . '-' . str_pad($hitungTransaksi, 3, '0', STR_PAD_LEFT);

            // cari barang berdasarkan nama dan kategori, jika tidak ada maka buat baru dengan harga kosong (0)
            $barang = Barang::firstOrCreate(
                [
                    'nama' => trim($request->nama_barang),
                    'kategori_id' => $request->kategori_id
                ],
                [
                    'harga' => 0,
                    'deskripsi' => '-',
                    'stok' => 0
                ]
            );

            $jumlahUnit = count($request->serial_number);

            $supplier = DB::table('suppliers')->where('id', $request->supplier_id)->first();

            $barangMasuk = BarangMasuk::create([
                'kode_transaksi' => $kodeTransaksi,
                'barang_id' => $barang->id,
                'supplier_id' => $request->supplier_id,
                'supplier_nama' => $supplier->nama ?? '-',
                'karyawan_id' => Auth::id(),
                'tgl_masuk' => $request->tgl_masuk,
                'jumlah' => $jumlahUnit,
            ]);

            foreach ($request->serial_number as $serial) {
                UnitBarang::create([
                    'barang_id' => $barang->id,
                    'barang_masuk_id' => $barangMasuk->id,
                    'serial_number' => strtoupper(trim($serial)),
                ]);
            }

            // Sinkronisasi stok dari jumlah unit aktual
            $b = $this->syncStok($barang->id);
            if ($b) {
                $this->checkAndCreateLowStockAlert($b);
            }

            DB::commit();

            return redirect()->route('barang-masuk.index')
                ->with('toast_success', 'Transaksi barang masuk berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'gagal menyimpan transaksi: ' . $e->getMessage());
        }
    }

    public function checkSerial(Request $request)
    {
        $serial = strtoupper(trim($request->query('serial_number', '')));
        $exceptId = $request->query('except_id');
        $exists = false;

        if ($serial !== '') {
            $query = UnitBarang::where('serial_number', $serial);
            if ($exceptId) {
                $query->where('id', '!=', $exceptId);
            }
            $exists = $query->exists();
        }

        return response()->json(['exists' => $exists]);
    }

    public function checkBarangName(Request $request)
    {
        $namaBarang = trim($request->query('nama_barang', ''));
        $kategoriId = $request->query('kategori_id');

        if ($namaBarang === '' || !$kategoriId) {
            return response()->json(['exists' => false]);
        }

        $existingBarang = Barang::with('kategori')
            ->where('nama', $namaBarang)
            ->where('kategori_id', '!=', $kategoriId)
            ->first();

        if ($existingBarang) {
            return response()->json([
                'exists' => true,
                'kategori_nama' => $existingBarang->kategori->nama ?? '-',
            ]);
        }

        return response()->json(['exists' => false]);
    }

    public function update(Request $request, $id)
    {
        $barangMasuk = BarangMasuk::with('unitBarang', 'barang')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama_barang' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'tgl_masuk' => 'required|date',
            'serial_number' => 'required|array|min:1',
            'serial_number.*' => 'required|string',
            'unit_id' => 'required|array|min:1',
            'unit_id.*' => 'nullable|integer',
        ]);

        $validator->after(function ($validator) use ($request, $barangMasuk) {
            $serialNumbers = collect($request->input('serial_number', []))
                ->map(fn($sn) => strtoupper(trim($sn)))
                ->filter()
                ->all();

            if (count($serialNumbers) !== count(array_unique($serialNumbers))) {
                $validator->errors()->add('serial_number', 'Terdapat serial number duplikat dalam daftar unit.');
            }

            $unitIds = collect($request->input('unit_id', []))->map(fn($id) => $id === null || $id === '' ? null : (int) $id)->all();

            // Check that locked units (already in barang_keluar) are not modified or deleted
            $originalUnits = $barangMasuk->unitBarang->keyBy('id');
            $submittedUnitIds = collect($unitIds)->filter()->all();

            // Detect deleted units
            $deletedUnitIds = $originalUnits->keys()->diff($submittedUnitIds);
            foreach ($deletedUnitIds as $deletedId) {
                $unit = $originalUnits->get($deletedId);
                if ($unit && $unit->barang_keluar_id) {
                    $validator->errors()->add('serial_number', 'Unit "' . $unit->serial_number . '" sudah keluar dan tidak dapat dihapus.');
                    break;
                }
            }

            // Detect modified serial numbers on locked units
            foreach ($serialNumbers as $index => $serial) {
                $unitId = $unitIds[$index] ?? null;
                if ($unitId && $originalUnits->has($unitId)) {
                    $original = $originalUnits->get($unitId);
                    if ($original->barang_keluar_id && strtoupper(trim($original->serial_number)) !== $serial) {
                        $validator->errors()->add('serial_number', 'Unit "' . $original->serial_number . '" sudah keluar dan tidak dapat diubah.');
                        break;
                    }
                }
            }

            foreach ($serialNumbers as $index => $serial) {
                $exceptId = $unitIds[$index] ?? null;
                $exists = UnitBarang::where('serial_number', $serial)
                    ->when($exceptId, fn($query) => $query->where('id', '!=', $exceptId))
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('serial_number', 'Serial number "' . $serial . '" sudah terdaftar di transaksi lain.');
                    break;
                }
            }
        });

        $validator->validate();

        $serialNumbers = collect($request->input('serial_number', []))
            ->map(fn($sn) => strtoupper(trim($sn)))
            ->filter()
            ->values()
            ->all();

        $unitIds = collect($request->input('unit_id', []))
            ->map(fn($id) => $id === null || $id === '' ? null : (int) $id)
            ->all();

        DB::beginTransaction();

        try {
            $barang = Barang::firstOrCreate(
                [
                    'nama' => trim($request->nama_barang),
                    'kategori_id' => $barangMasuk->barang->kategori_id,
                ],
                [
                    'harga' => 0,
                    'deskripsi' => '-',
                    'stok' => 0,
                ]
            );

            $oldBarang = $barangMasuk->barang;
            $oldJumlah = $barangMasuk->jumlah;
            $newJumlah = count($serialNumbers);

            $supplier = DB::table('suppliers')->where('id', $request->supplier_id)->first();

            $barangMasuk->barang_id = $barang->id;
            $barangMasuk->supplier_id = $request->supplier_id;
            $barangMasuk->supplier_nama = $supplier->nama ?? '-';
            $barangMasuk->tgl_masuk = $request->tgl_masuk;
            $barangMasuk->jumlah = $newJumlah;
            $barangMasuk->save();

            $existingUnits = $barangMasuk->unitBarang->keyBy('id');
            $processedIds = [];

            foreach ($serialNumbers as $index => $serial) {
                $unitId = $unitIds[$index] ?? null;
                if ($unitId && $existingUnits->has($unitId)) {
                    $unit = $existingUnits->get($unitId);
                    $unit->serial_number = $serial;
                    $unit->barang_id = $barang->id;
                    $unit->save();
                    $processedIds[] = $unitId;
                } else {
                    UnitBarang::create([
                        'barang_id' => $barang->id,
                        'barang_masuk_id' => $barangMasuk->id,
                        'serial_number' => $serial,
                    ]);
                }
            }

            $toDelete = $existingUnits->filter(fn($unit) => !in_array($unit->id, $processedIds));
            foreach ($toDelete as $unit) {
                $unit->delete();
            }

            // Kumpulkan barang_id yang terpengaruh
            $affectedBarangIds = collect([$barang->id]);
            if ($oldBarang && $oldBarang->id !== $barang->id) {
                $affectedBarangIds->push($oldBarang->id);
            }

            // Sinkronisasi stok dari jumlah unit aktual
            foreach ($affectedBarangIds->unique() as $bId) {
                $b = $this->syncStok($bId);
                if ($b) {
                    $this->checkAndCreateLowStockAlert($b);
                }
            }

            DB::commit();

            return redirect()->route('barang-masuk.index')
                ->with('toast_success', 'Transaksi barang masuk berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'gagal memperbarui transaksi: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $barangMasuk = BarangMasuk::with('unitBarang')->findOrFail($id);

        // Check if any unit has already been used in barang keluar
        $lockedUnits = $barangMasuk->unitBarang->filter(fn($u) => $u->barang_keluar_id !== null);
        if ($lockedUnits->isNotEmpty()) {
            $lockedSns = $lockedUnits->pluck('serial_number')->implode(', ');
            return redirect()->back()->with('error', 'Transaksi tidak dapat dihapus karena unit ' . $lockedSns . ' sudah keluar.');
        }

        DB::beginTransaction();

        try {
            $barang = $barangMasuk->barang;

            UnitBarang::where('barang_masuk_id', $id)->delete();
            $barangMasuk->delete();

            // Sinkronisasi stok dari jumlah unit aktual
            if ($barang) {
                $b = $this->syncStok($barang->id);
                if ($b) {
                    $this->checkAndCreateLowStockAlert($b);
                }
            }

            DB::commit();

            return redirect()->route('barang-masuk.index')
                ->with('toast_success', 'Transaksi barang masuk berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

}
