<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Barang;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Kategori;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BarangAdminController extends Controller
{
    // Tampilkan daftar barang
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kategori = $request->input('kategori'); 

        $query = Barang::with(['kategori', 'unitBarang' => function($q) {
            $q->whereNull('barang_keluar_id');
        }])
            ->when($search, function ($q) use ($search) {
                $q->where(function($subQuery) use ($search) {
                    $subQuery->where('nama', 'like', "%{$search}%")
                             ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            })
            ->when($kategori, function ($q) use ($kategori) {
                $q->where('kategori_id', $kategori);
            });

        $allBarang = $query->get()
            ->groupBy(function ($item) {
                return strtolower(trim($item->nama));
            })
            ->map(function ($group) {
                $firstItem = $group->first();
                return (object) [
                    'id' => $firstItem->id,
                    'nama' => $firstItem->nama,
                    'kategori' => $firstItem->kategori,
                    'harga' => $firstItem->harga,
                    'deskripsi' => $firstItem->deskripsi,
                    'gambar' => $firstItem->gambar,
                    'stok' => $group->sum('stok'),
                    'unitBarang' => $group->flatMap->unitBarang->unique('id')->values(),
                ];
            })
            ->filter(function ($item) {
                return $item->stok > 0;
            })
            ->values();

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $allBarang->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $barang = new LengthAwarePaginator(
            $currentItems,
            $allBarang->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $categories = Kategori::orderBy('nama')->get();

        return view('admin.BarangAdmin', compact('barang', 'categories'));
    }

    // Export Barang ke Excel
    public function export(Request $request)
    {
        $barangId = $request->input('barang_id');
        $category = $request->input('exportCategory', 'all');

        $query = Barang::with([
            'kategori:id,nama',
            'unitBarang' => fn($q) => $q->select('id', 'barang_id', 'serial_number'),
        ])->whereHas('unitBarang');

        if ($barangId) {
            $query->where('id', $barangId);
            $includeCategory = true;
        } else {
            if ($category !== 'all') {
                $query->where('kategori_id', $category);
            }
            $includeCategory = $category === 'all';
        }

        $barang = $query->where('stok', '>', 0)->get();
        
        if ($barang->isEmpty()) {
            abort(404, 'Data barang tidak ditemukan');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['No', 'Serial Number', 'Nama Barang', 'Harga'];
        if ($includeCategory) {
            $headers[] = 'Kategori';
        }

        $colIndex = 1;
        foreach ($headers as $h) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . '1';
            $sheet->setCellValue($cell, $h);
            $colIndex++;
        }

        // StyleHeader
        $lastCol = $sheet->getHighestColumn();
        $headerRange = 'A1:' . $lastCol . '1';
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFEEEEEE');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setARGB('FF4B5563');

        $rowNum = 2;
        $totalByName = [];
        foreach ($barang as $item) {
            foreach ($item->unitBarang as $unit) {
                $col = 1;

                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $rowNum, $rowNum - 1);

                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $rowNum, $unit->serial_number ?? '-');

                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $rowNum, $item->nama ?? '-');
                
                $sheet->setCellValue(
                    \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $rowNum,
                    'Rp ' . number_format((float) ($item->harga ?? 0), 0, ',', '.')
                );
                if ($includeCategory) {
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $rowNum, optional($item->kategori)->nama ?? '-');
                }

                $itemName = $item->nama ?? '-';
                if (!isset($totalByName[$itemName])) {
                    $totalByName[$itemName] = 0;
                }
                $totalByName[$itemName]++;

                $rowNum++;
            }
        }

        $highestRow = $sheet->getHighestRow();
        if ($highestRow >= 2) {
            $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B2:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            
            $sheet->getStyle('D2:D' . $highestRow)
                  ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }

        // Tabel Border
        $highestColumn = $sheet->getHighestColumn();
        $tableRange = 'A1:' . $highestColumn . $highestRow;
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Hitung Total Barang
        $summaryStartCol = ($includeCategory ? 6 : 5) + 1;
        $summaryStartColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol);
    
        $summaryHeaders = ['No', 'Nama Barang', 'Total'];
        $summaryRow = 1;
        for ($i = 0; $i < count($summaryHeaders); $i++) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + $i) . $summaryRow;
            $sheet->setCellValue($cell, $summaryHeaders[$i]);
        }
        
        // Style Header
        $summaryEndCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + count($summaryHeaders) - 1);
        $summaryHeaderRange = $summaryStartColLetter . '1:' . $summaryEndCol . '1';
        $sheet->getStyle($summaryHeaderRange)->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFEEEEEE');
        $sheet->getStyle($summaryHeaderRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($summaryHeaderRange)->getFont()->setBold(true)->getColor()->setARGB('FF4B5563');
        
        $summaryRow = 2;
        $summaryNum = 1;
        foreach ($totalByName as $name => $total) {
            $col = $summaryStartCol;
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $summaryRow, $summaryNum);
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $summaryRow, $name);
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $summaryRow, $total);
            $summaryRow++;
            $summaryNum++;
        }
        
        // Tabel Border
        $summaryLastRow = $summaryRow - 1;
        if ($summaryRow > 2) {
            $summaryTableRange = $summaryStartColLetter . '1:' . $summaryEndCol . $summaryLastRow;
            $sheet->getStyle($summaryTableRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $summaryNoCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol);
            $sheet->getStyle($summaryNoCol . '2:' . $summaryNoCol . $summaryLastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Filter: kolom A-E (data utama) dan G-I (ringkasan); kolom F tanpa filter
        $mainLastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $tableStyle = new TableStyle();
        $tableStyle->setTheme(TableStyle::TABLE_STYLE_NONE);

        if ($highestRow >= 2) {
            $mainTable = new Table('A1:' . $mainLastColLetter . $highestRow, 'DataBarang');
            $mainTable->setStyle(clone $tableStyle);
            $mainTable->setAllowFilter(true);
            $sheet->addTable($mainTable);
        }

        if ($summaryRow > 2) {
            $summaryTable = new Table(
                $summaryStartColLetter . '1:' . $summaryEndCol . $summaryLastRow,
                'RingkasanBarang'
            );
            $summaryTable->setStyle(clone $tableStyle);
            $summaryTable->setAllowFilter(true);
            $sheet->addTable($summaryTable);
        }

        // Resize Kolom
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        if ($barangId && $barang->count() === 1) {
            $barangNama = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $barang->first()->nama);
            $fileName = 'barang-' . strtolower(str_replace(' ', '-', $barangNama)) . '.xlsx';
        } else {
            $fileName = 'laporan-barang.xlsx';
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
        exit;
    }

    // Update Barang
    public function update(Request $request, $id)
    {
        try {
            $barang = Barang::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nama' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('barang', 'nama')->ignore($id),
                ],
                'harga' => 'required|numeric|min:1',
                'kategori_id' => 'required|integer',
                'deskripsi' => 'nullable|string',
                'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ], [
                'nama.unique' => 'Nama barang sudah ada, silakan gunakan nama lain!',
                'harga.min' => 'Harga harus lebih dari 0!',
            ]);

            $validator->after(function ($validator) use ($request) {
                if ($request->filled('kategori_id') && !Kategori::where('id', $request->kategori_id)->exists()) {
                    $validator->errors()->add('kategori_id', 'Kategori tidak valid.');
                }
            });

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            if ($request->hasFile('gambar')) {
                if ($barang->gambar) {
                    Storage::disk('public')->delete($barang->gambar);
                }

                $validated['gambar'] = $request->file('gambar')->store('barang', 'public');
            }

            $barang->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data barang berhasil diperbarui',
                'data' => $barang
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => ['nama' => ['Nama barang sudah ada, silakan gunakan nama lain!']]
                ], 422);
            }
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 400);
        }
    }
}
