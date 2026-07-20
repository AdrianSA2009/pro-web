<!DOCTYPE html>
<html lang="id">
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Barang - Admin</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS Animation -->
    @include('layout.partials.aos-head')
    
    <style>
        body { font-family: 'Inter', sans-serif; }

        .sidebar-nav::-webkit-scrollbar { width: 0px; background: transparent; }

        .glass-hover:hover {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
        }

        tr.hover-row:hover {
            background-color: #f8fafc;
            transition: all 0.2s;
        }

        /* Toast handled by SweetAlert2 like halaman data pengguna */
    </style>
</head>
<body class="bg-slate-50 text-gray-800 h-screen flex overflow-hidden">

    <!-- Sidebar -->
    @include('layout.sidebar')
    <!-- End Sidebar -->

    <div class="flex-1 flex flex-col w-full md:ml-72 transition-all duration-300">
        <!-- Top Navbar -->
        <header class="bg-white/80 backdrop-blur-md sticky top-0 z-10 flex items-center justify-between px-8 py-4 border-b border-slate-100">
            <div class="flex items-center gap-4">
                <button data-drawer-target="sidebar-multi-level-sidebar" data-drawer-toggle="sidebar-multi-level-sidebar" aria-controls="sidebar-multi-level-sidebar" type="button" class="md:hidden p-2 rounded-lg hover:bg-slate-100 transition-colors">
                    <i class="fas fa-bars text-xl text-slate-600"></i>
                </button>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight">Manajemen Inventaris</h2>
            </div>
            @include('layout.partials.topbar-profile')
        </header>
        <!-- End Top Navbar -->

        <!-- Main Content -->
        <main class="flex-1 min-h-0 overflow-y-auto p-6 md:p-8 space-y-6 bg-slate-50">
            <!-- Komponen Atas Tabel -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4" data-aos="fade-down">
                <div>
                    <nav class="flex text-sm text-slate-500 mb-2">
                        <span>Master Data</span>
                        <span class="mx-2">/</span>
                        <span class="text-slate-900 font-medium">Data Barang</span>
                    </nav>
                    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen Inventaris</h2>
                </div>
                <button type="button" onclick="showModal('modalExport')" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-600 font-semibold hover:bg-slate-50 transition-all shadow-sm">
                        <i class="fas fa-file-export"></i>
                        <span>Export</span>
                </button>
            </div>
        
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-wrap gap-4 items-center justify-between" data-aos="fade-up" data-aos-delay="100">
                <div class="relative w-full md:w-96 group">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 group-focus-within:text-blue-600 transition-colors">
                        <i class="fas fa-search text-sm"></i>
                    </span>
                    <input type="text" id="searchBarang"
                        value="{{ request('search') }}"
                        class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-400 text-sm" 
                        placeholder="Cari nama barang...">
                </div>
                
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <div class="relative flex-1 md:w-52">
                        <select id="filterKategori" class="w-full appearance-none px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-600 text-sm font-medium cursor-pointer transition-all pr-10">
                            <option value="" {{ !request('kategori') ? 'selected' : '' }}>Semua Jenis</option>
                            @foreach($categories as $kategori)
                                <option value="{{ $kategori->id }}" {{ request('kategori') == $kategori->id ? 'selected' : '' }}>{{ $kategori->nama }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400">
                            <i class="fas fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Komponen Atas Tabel -->
        
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden" data-aos="fade-up" data-aos-delay="200">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 py-4 text-[11px] uppercase tracking-widest font-bold text-slate-400">Gambar</th>
                                <th class="px-6 py-4 text-[11px] uppercase tracking-widest font-bold text-slate-400">Nama Barang</th>
                                <th class="px-6 py-4 text-[11px] uppercase tracking-widest font-bold text-slate-400">Jenis Barang</th>
                                <th class="px-6 py-4 text-[11px] uppercase tracking-widest font-bold text-slate-400">Harga</th>
                                <th class="px-6 py-4 text-[11px] uppercase tracking-widest font-bold text-slate-400">Stok</th>
                                <th class="px-6 py-4 text-[11px] uppercase tracking-widest font-bold text-slate-400 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="ajax-list-tbody" class="divide-y divide-slate-100">
                            @forelse($barang as $item)
                                <tr class="group hover:bg-slate-50/50 transition-all">
                                    <td class="px-6 py-4">
                                        @if($item->gambar)
                                            <button type="button" onclick="previewGambar('{{ asset('storage/' . $item->gambar) }}')" class="block rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/30" title="Preview gambar">
                                                <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->nama }}" class="w-14 h-14 object-cover rounded-xl border border-slate-100">
                                            </button>
                                        @else
                                            <div class="w-14 h-14 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-800">{{ $item->nama }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-semibold">{{ optional($item->kategori)->nama ?? 'Tidak diketahui' }}</span>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-slate-700">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-slate-700 font-semibold">{{ number_format($item->stok) }} Unit</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                                    <button type="button"
                                                onclick='bukaDetail({
                                                    nama: @json($item->nama),
                                                    harga: @json($item->harga),
                                                    deskripsi: @json($item->deskripsi),
                                                    gambar_url: @json($item->gambar ? asset('storage/' . $item->gambar) : null),
                                                    units: @json($item->unitBarang->map(fn($u) => ["sn" => $u->serial_number, "nama" => $item->nama ?? "-"]))
                                                })'
                                                class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button"
                                                onclick='bukaEdit({
                                                    id: @json($item->id),
                                                    nama: @json($item->nama),
                                                    harga: @json($item->harga),
                                                    kategori_id: @json($item->kategori?->id),
                                                    deskripsi: @json($item->deskripsi),
                                                    gambar_url: @json($item->gambar ? asset('storage/' . $item->gambar) : null)
                                                })'
                                                class="p-2 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-all" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" onclick='bukaExportBarang(@json($item->id), @json($item->nama))' class="p-2 rounded-lg text-slate-400 hover:text-green-600 hover:bg-green-50 transition-all" title="Export">
                                                <i class="fas fa-file-export"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">Belum ada data barang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            
                <div id="ajax-list-footer" class="p-6 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-slate-500">Menampilkan {{ $barang->count() }} dari {{ $barang->total() }} Barang</p>
                    @if ($barang->hasPages())
                        <div class="flex items-center gap-2">
                            <a href="{{ $barang->previousPageUrl() ?: '#' }}" class="px-3 py-1.5 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition-all text-sm {{ $barang->onFirstPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                                Previous
                            </a>

                            @foreach ($barang->getUrlRange(1, $barang->lastPage()) as $page => $url)
                                @if ($page == $barang->currentPage())
                                    <span class="w-8 h-8 bg-blue-600 text-white rounded-lg text-sm font-bold flex items-center justify-center">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="w-8 h-8 hover:bg-slate-100 text-slate-600 rounded-lg text-sm transition-all flex items-center justify-center">{{ $page }}</a>
                                @endif
                            @endforeach

                            <a href="{{ $barang->nextPageUrl() ?: '#' }}" class="px-3 py-1.5 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition-all text-sm {{ $barang->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed pointer-events-none' }}">
                                Next
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Modal Detail -->
            <div id="modalDetail" class="fixed inset-0 z-50 hidden overflow-hidden p-4" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center h-full text-center w-full">
                    <div id="closeModalOverlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

                    <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-5xl max-h-full border border-slate-200 flex flex-col">

                        <div class="bg-white px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-slate-800">
                                <i class="fas fa-file-alt text-blue-600 mr-2"></i> Rincian Inventaris
                            </h3>
                            <button data-modal-hide="modalDetail" onclick="hideModal('modalDetail')" class="text-slate-400 hover:text-slate-600">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                    
                        <div class="px-6 py-6 space-y-6 overflow-y-auto">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Gambar Barang</label>
                                <button type="button" id="detailGambarButton" class="hidden w-full rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/30" title="Preview gambar">
                                    <img id="detailGambar" src="" alt="Gambar barang" class="w-full max-h-72 object-contain rounded-xl border border-slate-100 bg-slate-50">
                                </button>
                                <div id="detailGambarEmpty" class="w-full h-40 rounded-xl bg-slate-50 border border-slate-100 text-slate-400 flex items-center justify-center">
                                    <i class="fas fa-image text-2xl"></i>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Nama Barang</label>
                                    <p id="detailNama" class="text-lg font-bold text-slate-900">LG GN-B372SQBK 312L Inverter</p>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Harga Satuan</label>
                                    <p id="detailHarga" class="text-lg font-bold text-blue-600">Rp 7.499.900</p>
                                </div>
                            </div>
                        
                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Deskripsi Spesifikasi</label>
                                <p id="detailDeskripsi" class="text-sm text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-100 leading-relaxed max-h-24 overflow-y-auto">
                                    -
                                </p>
                            </div>
                        
                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 text-blue-600">Daftar Unit Tersedia</label>
                                <div class="border border-slate-100 rounded-xl overflow-hidden">
                                    <div class="max-h-32 overflow-y-auto">
                                        <table class="w-full text-sm text-left">
                                            <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                                                <tr>
                                                    <th class="px-4 py-2.5 w-12 text-center">No</th>
                                                    <th class="px-4 py-2.5">Kode Barang (SKU/SN)</th>
                                                    <th class="px-4 py-2.5">Nama Barang</th>
                                                </tr>
                                            </thead>
                                            <tbody id="detail-unit-body" class="divide-y divide-slate-100 text-slate-700">
                                                <tr>
                                                    <td colspan="3" class="px-4 py-4 text-slate-500 text-sm text-center">Pilih barang untuk melihat daftar unit.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                            <button data-modal-hide="modalDetail" onclick="hideModal('modalDetail')" class="px-6 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 text-sm font-bold hover:bg-slate-100 transition-all">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Modal Detail -->

            <!-- Modal Edit -->
            <div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center p-4 overflow-hidden">
                <div id="closeEditOverlay" class="fixed inset-0 bg-slate-900/40 backdrop-blur-md transition-opacity"></div>

                <div class="glass-card animate-modal relative bg-white w-full max-w-5xl max-h-full rounded-[2rem] shadow-2xl overflow-hidden border border-white flex flex-col">
                    <div class="h-2 bg-gradient-to-r from-amber-400 to-orange-500"></div>
                
                    <form id="formEditBarang" class="flex min-h-0 flex-1 flex-col">
                        <input type="hidden" id="editBarangId" name="id">
                        <div class="p-8 overflow-y-auto">
                            <div class="flex justify-between items-start mb-8">
                                <div>
                                    <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-600 text-[10px] font-bold uppercase tracking-widest">Update Registry</span>
                                    <h2 class="text-3xl font-extrabold text-slate-800 mt-2 tracking-tight">Edit Data Barang</h2>
                                </div>
                                <button type="button" data-modal-hide="modalEdit" onclick="hideModal('modalEdit')" class="bg-slate-100 hover:bg-slate-200 text-slate-400 w-10 h-10 rounded-full transition-all flex items-center justify-center">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2 space-y-1.5">
                                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Barang</label>
                                    <input type="text" id="editNama" name="nama" required
                                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all outline-none text-slate-700 font-medium">
                                    <span id="errorEditNama" class="text-red-500 text-xs mt-1 hidden block"></span>
                                </div>
                            
                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Harga Satuan (Rp)</label>
                                    <div class="relative">
                                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">Rp</span>
                                        <input type="number" id="editHarga" name="harga" required min="1"
                                            class="w-full pl-12 pr-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all outline-none text-slate-700 font-bold">
                                    </div>
                                    <span id="errorEditHarga" class="text-red-500 text-xs mt-1 hidden block"></span>
                                </div>
                            
                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Kategori</label>
                                    <select id="editKategori" name="kategori_id" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all outline-none text-slate-700 font-medium appearance-none">
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $kategori)
                                            <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            
                                <div class="md:col-span-2 space-y-1.5">
                                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Deskripsi Spesifikasi</label>
                                    <textarea id="editDeskripsi" name="deskripsi" rows="3"
                                        class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all outline-none text-slate-600 text-sm leading-relaxed"></textarea>
                                </div>

                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Gambar Barang</label>
                                    <div class="flex flex-col md:flex-row gap-4 md:items-center">
                                        <button type="button" id="editGambarPreviewButton" class="hidden rounded-2xl focus:outline-none focus:ring-2 focus:ring-amber-500/30" title="Preview gambar">
                                            <img id="editGambarPreview" src="" alt="Preview gambar barang" class="w-28 h-28 object-cover rounded-2xl border border-slate-100 bg-slate-50">
                                        </button>
                                        <div id="editGambarEmpty" class="w-28 h-28 rounded-2xl bg-slate-50 border border-slate-100 text-slate-400 flex items-center justify-center">
                                            <i class="fas fa-image text-xl"></i>
                                        </div>
                                        <input type="file" id="editGambar" name="gambar" accept="image/jpeg,image/png,image/webp"
                                            class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all outline-none text-sm text-slate-600">
                                    </div>
                                    <p class="text-xs text-slate-400 ml-1">Format JPG, PNG, atau WEBP. Maksimal 2MB.</p>
                                </div>
                            </div>
                        </div>
                    
                        <div class="bg-slate-50 p-6 flex justify-end gap-3">
                            <button type="button" data-modal-hide="modalEdit" onclick="hideModal('modalEdit')" class="px-6 py-3 text-slate-400 font-bold hover:text-slate-600 transition-all">
                                Batal
                            </button>
                            <button type="submit" id="submitEditBtn" class="px-10 py-3 bg-slate-900 text-white rounded-xl font-bold hover:bg-amber-500 transition-all shadow-xl shadow-slate-200">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- End Modal Edit -->

            <!-- Modal Export -->
            <div id="modalExport" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
                <div id="closeExportOverlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity"></div>

                <div class="animate-export relative bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100 flex flex-col">
                    <div class="bg-slate-900 px-8 py-6 text-white flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-green-500 rounded-2xl flex items-center justify-center shadow-lg shadow-green-500/20">
                                <i class="fas fa-file-excel text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold">Export to Excel</h2>
                                <p class="text-slate-400 text-xs mt-0.5">Filter dan pilih kategori untuk diunduh</p>
                            </div>
                        </div>
                        <button type="button" onclick="hideModal('modalExport')" class="text-slate-400 hover:text-white transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                
                    <form id="formExport" action="{{ route('brgadmin.export') }}" method="GET" class="p-8">
                        <div class="mb-6 relative group">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Cari Kategori</label>
                            <div class="relative">
                                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-green-500 transition-colors"></i>
                                <input type="text" id="searchCategory" placeholder="Ketik nama kategori (ex: Kulkas, Kipas...)" 
                                    class="w-full pl-12 pr-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all outline-none text-sm font-medium text-slate-700">
                            </div>
                        </div>

                        <!-- Kategori Export -->
                        <div id="categoryGrid" class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                            <label class="category-item relative cursor-pointer group">
                                <input type="radio" name="exportCategory" value="all" class="peer hidden" checked>
                                <div class="p-4 text-center rounded-2xl border-2 border-slate-50 bg-slate-50 text-slate-600 font-bold text-[11px] transition-all peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 group-hover:bg-slate-100 items-center">
                                    <span class="category-name">Semua Kategori</span>
                                </div>
                            </label>
                            @foreach ($categories as $kategori)
                                <label class="category-item relative cursor-pointer group">
                                    <input type="radio" name="exportCategory" value="{{ $kategori->id }}" class="peer hidden">
                                    <div class="p-4 text-center rounded-2xl border-2 border-slate-50 bg-slate-50 text-slate-600 font-bold text-[11px] transition-all peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 group-hover:bg-slate-100 items-center">
                                        <span class="category-name">{{ $kategori->nama }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <!-- End Kategori Export -->

                        <div id="noResult" class="hidden py-10 text-center">
                            <i class="fas fa-search text-slate-200 text-4xl mb-3"></i>
                            <p class="text-slate-400 text-sm">Kategori tidak ditemukan...</p>
                        </div>
                    
                        <div class="mt-8 flex gap-3">
                            <button type="submit" class="w-full py-4 bg-green-600 text-white rounded-2xl font-bold hover:bg-green-700 shadow-lg shadow-green-200 transition-all flex items-center justify-center gap-3">
                                <i class="fas fa-file-download"></i> Generate Excel Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- End Modal Export -->

            <!-- Modal Export Konfirmasi Barang -->
            <div id="modalExportConfirm" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center w-full">
                    <div id="closeExportConfirmOverlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
                
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div class="inline-block align-bottom bg-white rounded-2xl text-center shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-8 border-b border-slate-100">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-file-excel text-3xl text-green-600"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 mb-1">Export Barang</h3>
                            <p id="exportBarangNama" class="text-sm text-slate-600 font-semibold">-</p>
                        </div>
                    
                        <div class="px-6 py-6">
                            <p class="text-sm text-slate-600 mb-6">Barang ini akan diekspor menjadi file Excel dengan detail lengkap unit yang tersedia.</p>
                            
                            <div class="bg-slate-50 rounded-xl p-4 mb-6 border border-slate-100">
                                <div class="text-xs text-slate-500 uppercase tracking-widest font-bold mb-2">File akan berisi:</div>
                                <ul class="text-xs text-slate-600 space-y-1.5">
                                    <li class="flex items-center gap-2">
                                        <i class="fas fa-check text-green-600"></i>
                                        <span>Data Barang</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <i class="fas fa-check text-green-600"></i>
                                        <span>Daftar Unit (Serial Number)</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <i class="fas fa-check text-green-600"></i>
                                        <span>Informasi Harga</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-center gap-3">
                            <button type="button" onclick="hideModal('modalExportConfirm')" class="px-6 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-600 text-sm font-bold hover:bg-slate-100 transition-all">
                                Batal
                            </button>
                            <button type="button" id="btnConfirmExport" class="px-6 py-2.5 bg-green-600 text-white rounded-xl text-sm font-bold hover:bg-green-700 transition-all flex items-center gap-2">
                                <i class="fas fa-download"></i>
                                <span>Export Sekarang</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Modal Export Konfirmasi Barang -->
        </main>
        <!-- End Main Content -->
        
        <!-- Modal Preview Gambar -->
        <div id="modalPreviewGambar" class="fixed inset-0 z-[70] hidden items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm" onclick="hideModal('modalPreviewGambar')"></div>
            <div class="relative w-full max-w-6xl h-full flex flex-col">
                <div class="flex justify-end pb-3">
                    <button type="button" onclick="hideModal('modalPreviewGambar')" class="w-10 h-10 rounded-full bg-white/10 text-white hover:bg-white/20 transition-all flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="min-h-0 flex-1 flex items-center justify-center">
                    <img id="previewGambarFull" src="" alt="Preview gambar barang" class="max-w-full max-h-full object-contain rounded-2xl shadow-2xl">
                </div>
            </div>
        </div>
        <!-- End Modal Preview Gambar -->
    </div>

    @include('layout.partials.aos-scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

        // Pencarian kategori pada modal
        document.getElementById('searchCategory').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const categories = document.querySelectorAll('.category-item');
            const noResult = document.getElementById('noResult');
            let hasMatch = false;
        
            categories.forEach(item => {
                const categoryName = item.querySelector('.category-name').innerText.toLowerCase();

                if (categoryName.includes(searchTerm)) {
                    item.classList.remove('hidden');
                    hasMatch = true;
                } else {
                    item.classList.add('hidden');
                }
            });
        
            if (hasMatch) {
                noResult.classList.add('hidden');
            } else {
                noResult.classList.remove('hidden');
            }
        });

        const sidebar = document.getElementById('sidebar-multi-level-sidebar');
        const customOverlay = document.getElementById('sidebar-overlay-custom');
    
        const observer = new MutationObserver(() => {
            const isOpened = !sidebar.classList.contains('-translate-x-full');
            
            if (isOpened) {
                customOverlay.classList.remove('hidden');
            } else {
                customOverlay.classList.add('hidden');
            }
        });
    
        observer.observe(sidebar, { attributes: true, attributeFilter: ['class'] });

        function showModal(id) {
            const element = document.getElementById(id);
            if (!element) return;
            element.classList.remove('hidden');
            element.classList.add('flex');
        }

        function hideModal(id) {
            const element = document.getElementById(id);
            if (!element) return;
            element.classList.add('hidden');
            element.classList.remove('flex');
        }

        function formatRupiah(value) {
            if (value === null || value === undefined) {
                return '-';
            }
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(value);
        }

        function previewGambar(url) {
            if (!url) return;
            document.getElementById('previewGambarFull').src = url;
            showModal('modalPreviewGambar');
        }

        function bukaDetail(data) {
            document.getElementById('detailNama').textContent = data.nama || '-';
            document.getElementById('detailHarga').textContent = formatRupiah(data.harga);
            document.getElementById('detailDeskripsi').textContent = data.deskripsi || '-';
            const detailGambar = document.getElementById('detailGambar');
            const detailGambarButton = document.getElementById('detailGambarButton');
            const detailGambarEmpty = document.getElementById('detailGambarEmpty');

            if (data.gambar_url) {
                detailGambar.src = data.gambar_url;
                detailGambarButton.onclick = function() {
                    previewGambar(data.gambar_url);
                };
                detailGambarButton.classList.remove('hidden');
                detailGambarEmpty.classList.add('hidden');
            } else {
                detailGambar.removeAttribute('src');
                detailGambarButton.onclick = null;
                detailGambarButton.classList.add('hidden');
                detailGambarEmpty.classList.remove('hidden');
            }

            const tbody = document.getElementById('detail-unit-body');
            tbody.innerHTML = '';

            if (!data.units || data.units.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-4 text-slate-500 text-sm text-center">Tidak ada unit tersedia.</td></tr>';
                return;
            }

            data.units.forEach(function(unit, index) {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-50/50';
                tr.innerHTML =
                    '<td class="px-4 py-3 text-center text-xs font-semibold text-slate-700">' + (index + 1) + '</td>' +
                    '<td class="px-4 py-3 text-xs text-slate-700">' + (unit.sn || '-') + '</td>' +
                    '<td class="px-4 py-3 text-sm text-slate-700">' + (unit.nama || '-') + '</td>';
                tbody.appendChild(tr);
            });

            showModal('modalDetail');
        }

        // Buka modal Edit dengan data barang
        function bukaEdit(data) {
            document.getElementById('editBarangId').value = data.id;
            document.getElementById('editNama').value = data.nama || '';
            document.getElementById('editHarga').value = data.harga ? parseInt(data.harga) : '';
            document.getElementById('editKategori').value = data.kategori_id || '';
            document.getElementById('editDeskripsi').value = data.deskripsi || '';
            document.getElementById('editGambar').value = '';
            // Reset error
            const inputNama = document.getElementById('editNama');
            const errorMsg = document.getElementById('errorEditNama');
            const inputHarga = document.getElementById('editHarga');
            const errorHarga = document.getElementById('errorEditHarga');
            inputNama.classList.remove('border-red-500');
            errorMsg.classList.add('hidden');
            errorMsg.classList.remove('block');
            errorMsg.innerText = '';
            inputHarga.classList.remove('border-red-500');
            errorHarga.classList.add('hidden');
            errorHarga.classList.remove('block');
            errorHarga.innerText = '';
            setEditGambarPreview(data.gambar_url || null);
            showModal('modalEdit');
        }

        function setEditGambarPreview(url) {
            const preview = document.getElementById('editGambarPreview');
            const previewButton = document.getElementById('editGambarPreviewButton');
            const empty = document.getElementById('editGambarEmpty');

            if (url) {
                preview.src = url;
                previewButton.onclick = function() {
                    previewGambar(url);
                };
                previewButton.classList.remove('hidden');
                empty.classList.add('hidden');
                return;
            }

            preview.removeAttribute('src');
            previewButton.onclick = null;
            previewButton.classList.add('hidden');
            empty.classList.remove('hidden');
        }

        document.getElementById('editGambar').addEventListener('change', function() {
            const file = this.files && this.files[0] ? this.files[0] : null;

            if (!file) {
                return;
            }

            setEditGambarPreview(URL.createObjectURL(file));
        });

        // Handle Form Submit Edit
        document.getElementById('formEditBarang').addEventListener('submit', async function(e) {
            e.preventDefault();

            const barangId = document.getElementById('editBarangId').value;

            if (!barangId) {
                alert('ID barang tidak ditemukan!');
                return;
            }

            const submitBtn = document.getElementById('submitEditBtn');
            const originalText = submitBtn.textContent;
            const inputNama = document.getElementById('editNama');
            const errorMsg = document.getElementById('errorEditNama');
            const inputHarga = document.getElementById('editHarga');
            const errorHarga = document.getElementById('errorEditHarga');

            // Reset errors
            inputNama.classList.remove('border-red-500');
            errorMsg.classList.add('hidden');
            errorMsg.classList.remove('block');
            errorMsg.innerText = '';
            inputHarga.classList.remove('border-red-500');
            errorHarga.classList.add('hidden');
            errorHarga.classList.remove('block');
            errorHarga.innerText = '';

            submitBtn.disabled = true;
            submitBtn.textContent = 'Menyimpan...';

            try {
                const formData = new FormData(this);
                formData.append('_method', 'PUT');

                const response = await fetch(`/admin/barang/${barangId}`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: formData
                });

                let data;
                try {
                    data = await response.json();
                } catch (parseError) {
                    data = { success: false, message: 'Respon server tidak valid.' };
                }

                if (response.ok && data.success) {
                    hideModal('modalEdit');
                    showToast('Data barang berhasil diperbarui!', 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 700);
                } else if (response.status === 422 && data.errors) {
                    let hasInlineError = false;
                    if (data.errors.nama) {
                        inputNama.classList.add('border-red-500');
                        errorMsg.innerText = data.errors.nama[0];
                        errorMsg.classList.remove('hidden');
                        errorMsg.classList.add('block');
                        hasInlineError = true;
                    }
                    if (data.errors.harga) {
                        inputHarga.classList.add('border-red-500');
                        errorHarga.innerText = data.errors.harga[0];
                        errorHarga.classList.remove('hidden');
                        errorHarga.classList.add('block');
                        hasInlineError = true;
                    }
                    if (!hasInlineError) {
                        const firstError = Object.values(data.errors)[0][0] || 'Validasi gagal.';
                        showToast(firstError, 'error');
                    }
                } else {
                    showToast(data.message || 'Gagal menyimpan data', 'error');
                }
            } catch (error) {
                showToast('Terjadi kesalahan: ' + error.message, 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });

        // Clear error on input
        document.getElementById('editNama').addEventListener('input', function() {
            this.classList.remove('border-red-500');
            const errorMsg = document.getElementById('errorEditNama');
            errorMsg.classList.add('hidden');
            errorMsg.classList.remove('block');
            errorMsg.innerText = '';
        });

        document.getElementById('editHarga').addEventListener('input', function() {
            this.classList.remove('border-red-500');
            const errorHarga = document.getElementById('errorEditHarga');
            errorHarga.classList.add('hidden');
            errorHarga.classList.remove('block');
            errorHarga.innerText = '';
        });

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        function showToast(message, type = 'success') {
            Toast.fire({
                icon: type === 'error' ? 'error' : 'success',
                title: message
            });
        }

        // Export Barang Individual
        let currentExportBarangId = null;

        function bukaExportBarang(barangId, barangNama) {
            currentExportBarangId = barangId;
            document.getElementById('exportBarangNama').textContent = barangNama;
            showModal('modalExportConfirm');
        }

        document.getElementById('btnConfirmExport').addEventListener('click', async function() {
            if (!currentExportBarangId) return;

            const btn = this;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Memproses...</span>';

            try {
                const form = document.createElement('form');
                form.method = 'GET';
                form.action = `{{ route('brgadmin.export') }}`;
                form.target = '_blank';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'barang_id';
                input.value = currentExportBarangId;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);

                setTimeout(() => {
                    hideModal('modalExportConfirm');
                    showToast('File berhasil diunduh!', 'success');
                }, 500);
            } catch (error) {
                showToast('Gagal mengexport: ' + error.message, 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            const closeExport = document.getElementById('closeExportConfirmOverlay');
            if (closeExport) {
                closeExport.addEventListener('click', function() {
                    hideModal('modalExportConfirm');
                });
            }
        });

        // Close overlay pada modal export confirm
        document.getElementById('closeExportConfirmOverlay').addEventListener('click', function() {
            hideModal('modalExportConfirm');
        });
    </script>
    @include('layout.partials.ajax-list-search-init', [
        'indexUrl' => route('brgadmin'),
        'searchInputId' => 'searchBarang',
        'filterSelectId' => 'filterKategori',
        'filterParam' => 'kategori',
    ])
</body>
</html>