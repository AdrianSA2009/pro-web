<!DOCTYPE html>
<html lang="id">
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barang Keluar - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('layout.partials.aos-head')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-nav::-webkit-scrollbar { width: 0px; background: transparent; }
        .sidebar-nav { -ms-overflow-style: none; scrollbar-width: none; }
        .glass-hover:hover { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(5px); }
        tr.hover-row:hover { background-color: #f8fafc; transition: all 0.2s; }
    </style>
</head>
<body class="bg-slate-50 text-gray-800 h-screen flex overflow-hidden">

    @include('layout.sidebar')
    <div class="flex-1 flex flex-col w-full md:ml-72 transition-all duration-300">
        <header class="bg-white/80 backdrop-blur-md sticky top-0 z-10 flex items-center justify-between px-8 py-4 border-b border-slate-100">
            <div class="flex items-center gap-4">
                <button data-drawer-target="sidebar-multi-level-sidebar" data-drawer-toggle="sidebar-multi-level-sidebar" class="md:hidden p-2 rounded-lg hover:bg-slate-100">
                    <i class="fas fa-bars text-xl text-slate-600"></i>
                </button>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight">Manajemen Transaksi</h2>
            </div>
            @include('layout.partials.topbar-profile')
        </header>

        <main class="flex-1 min-h-0 overflow-y-auto p-6 md:p-8 space-y-6 bg-slate-50">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4" data-aos="fade-down">
                <div>
                    <nav class="flex text-sm text-slate-500 mb-2">
                        <span>Transaksi</span>
                        <span class="mx-2">/</span>
                        <span class="text-slate-900 font-medium">Barang Keluar</span>
                    </nav>
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">Riwayat Barang Keluar</h2>
                </div>
                <button onclick="bukaTambah()" class="flex items-center gap-2 px-6 py-3 bg-blue-600 rounded-xl text-white font-semibold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Barang</span>
                </button>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100" data-aos="fade-up" data-aos-delay="100">
                {{-- Search + Filter inline, sama persis dengan barangmasuk --}}
                <div class="flex flex-col md:flex-row items-center gap-4">
                    {{-- Search --}}
                    <div class="relative w-full md:w-2/3 group">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 group-focus-within:text-blue-600 transition-colors pointer-events-none">
                            <i class="fas fa-search text-sm"></i>
                        </span>
                        <input id="searchInput" name="search" type="text" value="{{ request('search') }}" class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-400 text-sm" placeholder="Cari kode transaksi / penerima / PIC...">
                    </div>
                    {{-- Dari Tanggal --}}
                    <div class="relative w-full md:w-1/3 group">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 group-focus-within:text-blue-600 transition-colors pointer-events-none">
                            <i class="fas fa-calendar-alt text-sm"></i>
                        </span>
                        <input id="dateFrom" name="date_from" type="text" value="{{ request('date_from') }}" class="tgl-filter w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-400 text-sm" placeholder="Dari Tanggal">
                    </div>
                    {{-- Sampai Tanggal --}}
                    <div class="relative w-full md:w-1/3 group">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 group-focus-within:text-blue-600 transition-colors pointer-events-none">
                            <i class="fas fa-calendar-alt text-sm"></i>
                        </span>
                        <input id="dateTo" name="date_to" type="text" value="{{ request('date_to') }}" class="tgl-filter w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-400 text-sm" placeholder="Sampai Tanggal">
                    </div>
                    {{-- Reset --}}
                    <a id="resetBtn" href="{{ route('barang-keluar.index') }}" class="w-full md:w-auto px-4 py-2.5 bg-slate-100 text-slate-600 font-semibold rounded-xl hover:bg-slate-200 transition-all text-center {{ request('search') || request('date_from') || request('date_to') ? '' : 'hidden' }}">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden" data-aos="fade-up" data-aos-delay="200">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 py-5 text-[11px] uppercase tracking-widest font-bold text-slate-400">Kode Transaksi</th>
                                <th class="px-6 py-5 text-[11px] uppercase tracking-widest font-bold text-slate-400">Jumlah</th>
                                <th class="px-6 py-5 text-[11px] uppercase tracking-widest font-bold text-slate-400 text-center">Penerima</th>
                                <th class="px-6 py-5 text-[11px] uppercase tracking-widest font-bold text-slate-400 text-center">Tanggal Keluar</th>
                                <th class="px-8 py-5 text-[11px] uppercase tracking-widest font-bold text-slate-400 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50" id="ajax-list-tbody">
                            @forelse($barangKeluar as $item)
                            <tr class="hover-row group" data-id="{{ $item->id }}" data-nama="{{ $item->unitBarang->map(fn($u) => $u->barang->nama ?? '')->unique()->implode(', ') }}" data-kode="{{ $item->kode_transaksi }}">
                                <td class="px-8 py-6 font-bold text-slate-800 uppercase tracking-tight">{{ $item->kode_transaksi }}</td>
                                <td class="px-6 py-6 text-sm text-slate-500 font-medium">{{ $item->jumlah }} UNIT</td>
                                <td class="px-6 py-6 text-center">
                                    <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-semibold">{{ $item->penerima }}</span>
                                </td>
                                <td class="px-6 py-6 text-sm font-medium text-slate-500 text-center">
                                    {{ \Carbon\Carbon::parse($item->tgl_keluar)->locale('id')->translatedFormat('d F Y') }}
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        
                                        <button type="button"
                                            onclick="bukaDetail({
                                                id: {{ $item->id }},
                                                barang: '{{ addslashes($item->unitBarang->map(fn($u) => $u->barang->nama ?? '')->unique()->implode(', ')) }}',
                                                jumlah: {{ $item->jumlah }},
                                                tgl: '{{ $item->tgl_keluar }}',
                                                kategori: '{{ addslashes($item->unitBarang->map(fn($u) => $u->barang->kategori->nama ?? '')->filter()->unique()->implode(', ')) }}',
                                                penerima: '{{ addslashes($item->penerima) }}',
                                                pic: '{{ $item->karyawan->nama ?? '-' }}',
                                                units: {{ json_encode($item->unitBarang->map(fn($u) => ['sn' => $u->serial_number, 'nama' => $u->barang->nama ?? '-'])) }}
                                            })"
                                            class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <button type="button"
                                            onclick="bukaEdit({
                                                id: {{ $item->id }},
                                                tgl: '{{ $item->tgl_keluar }}',
                                                penerima: '{{ addslashes($item->penerima) }}',
                                                units: {{ json_encode($item->unitBarang->map(fn($u) => ['id' => $u->id, 'sn' => $u->serial_number, 'nama' => $u->barang->nama ?? '-'])) }}
                                            })"
                                            class="p-2 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-all" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button onclick="bukaDelete({{ $item->id }}, '{{ addslashes($item->unitBarang->map(fn($u) => $u->barang->nama ?? '')->unique()->implode(', ')) }}')" class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-8 py-6 text-center text-slate-500">Belum ada data barang keluar</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div id="ajax-list-footer" class="p-6 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-slate-500">Menampilkan {{ $barangKeluar->count() }} dari {{ $barangKeluar->total() }} transaksi</p>
                    @if ($barangKeluar->hasPages())
                        <div class="flex items-center gap-2">
                            <a href="{{ $barangKeluar->previousPageUrl() ?: '#' }}" class="px-3 py-1.5 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition-all text-sm {{ $barangKeluar->onFirstPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                                Previous
                            </a>

                            @foreach ($barangKeluar->getUrlRange(1, $barangKeluar->lastPage()) as $page => $url)
                                @if ($page == $barangKeluar->currentPage())
                                    <span class="w-8 h-8 bg-blue-600 text-white rounded-lg text-sm font-bold flex items-center justify-center">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="w-8 h-8 hover:bg-slate-100 text-slate-600 rounded-lg text-sm transition-all flex items-center justify-center">{{ $page }}</a>
                                @endif
                            @endforeach

                            <a href="{{ $barangKeluar->nextPageUrl() ?: '#' }}" class="px-3 py-1.5 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition-all text-sm {{ $barangKeluar->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed pointer-events-none' }}">
                                Next
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Tambah Transaksi -->
    <div id="modalTambahTransaksi" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" data-modal-hide="modalTambahTransaksi"></div>
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden">
                <div class="flex items-start justify-between p-6 border-b border-slate-100">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center text-white text-xl shadow-lg shadow-blue-200">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">Tambah Barang Keluar</h3>
                            <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Tambahkan transaksi barang keluar ke sistem</p>
                        </div>
                    </div>
                    <button type="button" onclick="hideModal('modalTambahTransaksi')" class="text-slate-400 bg-transparent hover:bg-slate-100 hover:text-slate-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition-colors" data-modal-hide="modalTambahTransaksi">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <form id="formTambah" method="POST" action="{{ route('barang-keluar.store') }}" class="p-8">
                    @csrf
                    <div class="space-y-6 mb-6">
                        <div>
                            <label class="block mb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tanggal Keluar</label>
                            <input type="text" name="tgl_keluar" class="tgl-input w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none text-sm font-semibold  transition-all" placeholder="DD/MM/YYYY" required>
                        </div>
                        <div>
                            <label class="block mb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Penerima</label>
                            <input type="text" name="penerima" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none text-sm font-semibold placeholder:text-slate-400 transition-all" placeholder="Masukkan nama penerima" required>
                        </div>
                    </div>

                    <div id="hidden-fields-container"></div>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center px-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Daftar Unit</label>
                            <button type="button" id="btnOpenInputUnit"
                                class="px-4 py-1.5 bg-blue-600 text-white text-[10px] font-black rounded-lg shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all uppercase tracking-wider">
                                Input
                            </button>
                        </div>

                        <div class="border border-slate-100 rounded-[2rem] overflow-hidden shadow-sm bg-white">
                            <table class="w-full text-left">
                                <thead class="bg-slate-50/80 border-b border-slate-100">
                                    <tr>
                                        <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest text-left w-16">No</th>
                                        <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest text-left">Serial Number</th>
                                        <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest text-left">Nama Barang</th>
                                        <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest text-left">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                            <div class="overflow-y-auto" id="unitTambahScroll" style="max-height: 80px;">
                                <table class="w-full text-left">
                                    <tbody id="unitTambahBody" class="divide-y divide-slate-50">
                                        <tr id="emptyUnitRow">
                                            <td colspan="4" class="px-6 py-6 text-center text-xs text-slate-400">
                                                Belum ada unit. Klik <span class="font-bold text-blue-500">Input</span> untuk menambahkan.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100 mt-6">
                        <button onclick="hideModal('modalTambahTransaksi')" type="button" class="px-8 py-2.5 bg-slate-100 text-slate-500 rounded-xl font-bold hover:bg-slate-200 transition-all text-sm">
                            Batal
                        </button>
                        <button type="submit" id="btnSubmitTambah" disabled
                            class="px-8 py-2.5 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all text-sm uppercase disabled:opacity-40 disabled:cursor-not-allowed">
                            Tambah Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal Tambah -->

    <!-- Modal Input Unit -->
    <div id="modalInputUnit" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-[60] justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40"></div>
        <div class="relative p-4 w-full max-w-md max-h-full z-50">
            <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden">
                <div class="flex items-start justify-between p-5 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-barcode text-blue-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Input Detail Unit</h3>
                    </div>
                    <button type="button" id="btnCloseInputUnit"
                        class="text-slate-400 bg-transparent hover:bg-slate-100 hover:text-slate-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Serial Number</label>
                            <button type="button" id="btnScanBarcode"
                                class="flex items-center gap-1.5 px-3 py-1 bg-emerald-600 text-white text-[10px] font-bold rounded-lg shadow hover:bg-emerald-700 transition-all uppercase tracking-wider">
                                <i class="fas fa-camera"></i>
                                <span>Scan</span>
                            </button>
                        </div>
                        <input type="text" id="input-sn"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none text-sm font-semibold transition-all uppercase placeholder:text-slate-400"
                            placeholder="Masukkan serial number">
                        <p id="sn-error" class="mt-1 text-xs text-red-500 hidden"></p>
                        <!-- Scanner Viewport -->
                        <div id="scanner-container" class="mt-3 hidden">
                            <div class="relative rounded-xl overflow-hidden border-2 border-emerald-300 bg-black">
                                <div id="reader" class="w-full"></div>
                                <button type="button" id="btnStopScan"
                                    class="absolute top-2 right-2 z-10 px-3 py-1.5 bg-red-600 text-white text-[10px] font-bold rounded-lg shadow hover:bg-red-700 transition-all uppercase tracking-wider flex items-center gap-1">
                                    <i class="fas fa-stop-circle"></i>
                                    <span>Stop</span>
                                </button>
                            </div>
                            <p id="scan-status" class="mt-1.5 text-[10px] text-emerald-600 font-semibold flex items-center gap-1">
                                <i class="fas fa-circle-notch fa-spin"></i>
                                <span>Mengarahkan kamera ke barcode...</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 p-5 border-t border-slate-100">
                    <button id="btnCancelInputUnit" type="button"
                        class="px-5 py-2 bg-slate-100 text-slate-500 rounded-xl font-bold hover:bg-slate-200 transition-all text-sm">
                        Batal
                    </button>
                    <button id="btnSaveUnit" type="button" onclick="simpanDataUnit()"
                        class="px-5 py-2 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all text-sm">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal Input Unit -->

    <!-- Modal Detail -->
    <div id="modalDetail" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full transition-all duration-300">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="relative p-4 w-full max-w-lg max-h-full">
            <div class="relative bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-100">
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 bg-sky-500 rounded-lg flex items-center justify-center text-white shadow shadow-sky-100">
                            <i class="fas fa-file-alt text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Detail Transaksi</p>
                            <h2 class="text-base font-black text-slate-800 tracking-tight leading-tight">Barang Keluar</h2>
                        </div>
                    </div>
                    <button type="button" onclick="hideModal('modalDetail')" class="text-slate-400 hover:text-slate-600 transition-colors w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-100">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
 
                <div class="px-5 py-4">
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="col-span-2">
                            <label class="block mb-1 text-[9px] font-bold text-slate-400 uppercase tracking-widest">Tanggal Keluar</label>
                            <input type="text" id="detail-tgl" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg outline-none text-xs font-semibold text-slate-700" readonly>
                        </div>
                        <div>
                            <label class="block mb-1 text-[9px] font-bold text-slate-400 uppercase tracking-widest">Penerima</label>
                            <input type="text" id="detail-penerima" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg outline-none text-xs font-semibold text-slate-700" readonly>
                        </div>
                        <div>
                            <label class="block mb-1 text-[9px] font-bold text-slate-400 uppercase tracking-widest">Jumlah Unit</label>
                            <input type="text" id="detail-jumlah" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg outline-none text-xs font-semibold text-slate-700" readonly>
                        </div>
                        <div class="col-span-2">
                            <label class="block mb-1 text-[9px] font-bold text-slate-400 uppercase tracking-widest">PIC</label>
                            <input type="text" id="detail-pic" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg outline-none text-xs font-semibold text-slate-700" readonly>
                        </div>
                    </div>
 
                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] px-1">Daftar Unit</label>
                        <div class="border border-slate-100 rounded-xl overflow-hidden shadow-sm bg-white">
                            <table class="w-full text-left">
                                <thead class="bg-slate-50/80 border-b border-slate-100">
                                    <tr>
                                        <th class="px-4 py-2.5 text-[8px] font-black text-slate-400 uppercase tracking-widest text-left w-10">No</th>
                                        <th class="px-4 py-2.5 text-[8px] font-black text-slate-400 uppercase tracking-widest text-left">Serial Number</th>
                                        <th class="px-4 py-2.5 text-[8px] font-black text-slate-400 uppercase tracking-widest text-left">Nama Barang</th>
                                    </tr>
                                </thead>
                            </table>
                            <div class="overflow-y-auto" style="max-height: 100px;">
                                <table class="w-full text-left">
                                    <tbody id="detail-unit-body" class="divide-y divide-slate-50"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
 
                    <div class="pt-4 flex justify-end">
                        <button type="button" onclick="hideModal('modalDetail')" class="px-6 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-50 transition-all text-xs uppercase tracking-widest shadow-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal Detail -->

    <!-- Modal Edit -->
    <div id="modalEdit" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 justify-center items-center w-full h-full transition-all duration-300">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="hideModal('modalEdit')"></div>
        
        <div class="relative p-4 w-full max-w-2xl max-h-full z-10 my-auto">
            <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden">
                
                <div class="flex items-start justify-between p-6 border-b border-slate-100">
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 bg-amber-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-edit text-amber-600 text-lg"></i>
                        </div>
                        <div>
                            <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-600 text-[10px] font-bold uppercase tracking-widest">Update Registry</span>
                            <h2 class="text-xl font-extrabold text-slate-800 mt-1 tracking-tight">Edit Barang Keluar</h2>
                        </div>
                    </div>
                    <button type="button" onclick="hideModal('modalEdit')" class="bg-slate-100 hover:bg-slate-200 text-slate-400 w-10 h-10 rounded-full transition-all flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="formEdit" method="POST" class="px-8 py-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6 mb-6">
                        <div>
                            <label class="block mb-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tanggal Keluar</label>
                            <input type="text" name="tgl_keluar" id="edit-tgl"
                                class="tgl-input w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none text-sm font-semibold transition-all text-slate-700" placeholder="DD/MM/YYYY" required>
                        </div>
                        <div>
                            <label class="block mb-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Penerima</label>
                            <input type="text" name="penerima" id="edit-penerima"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none text-sm font-semibold transition-all text-slate-700" placeholder="Masukkan nama penerima" required>
                        </div>
                    </div>

                    <div id="hidden-edit-fields-container"></div>

                    <div class="space-y-4 mt-6">
                        <div class="flex justify-between items-center px-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Daftar Unit</label>
                            <button type="button" id="btnOpenEditInputUnit"
                                class="px-4 py-1.5 bg-blue-600 text-white text-[10px] font-black rounded-lg shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all uppercase tracking-wider">
                                Input
                            </button>
                        </div>

                        <div class="border border-slate-100 rounded-[2rem] overflow-hidden shadow-sm bg-white">
                            <table class="w-full text-left">
                                <thead class="bg-slate-50/80 border-b border-slate-100">
                                    <tr>
                                        <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest text-left w-16">No</th>
                                        <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest text-left">Serial Number</th>
                                        <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest text-left">Nama Barang</th>
                                        <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase tracking-widest text-left">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                            <div class="overflow-y-auto" style="max-height: 80px;">
                                <table class="w-full text-left">
                                    <tbody id="unitEditBody" class="divide-y divide-slate-50">
                                        <tr id="emptyEditUnitRow">
                                            <td colspan="4" class="px-6 py-6 text-left text-xs text-slate-400">
                                                Belum ada unit. Klik <span class="font-bold text-blue-500">Input</span> untuk menambahkan.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100 mt-6">
                        <button type="button" onclick="hideModal('modalEdit')" class="px-8 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-50 transition-all text-xs uppercase tracking-widest">
                            Batal
                        </button>
                        <button type="submit" id="btnSubmitEdit" class="px-8 py-2.5 bg-amber-500 text-white rounded-xl font-bold hover:bg-amber-600 transition-all text-xs uppercase tracking-widest shadow-lg shadow-amber-100">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Delete -->
    <div id="modalDelete" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="fixed inset-0 bg-red-900/20 backdrop-blur-sm transition-opacity"></div>
        <div class="relative bg-white w-full max-w-sm rounded-[2.5rem] shadow-2xl overflow-hidden text-center border-4 border-red-50 mx-auto">
            <div class="p-10">
                <div class="relative w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <div class="absolute inset-0 rounded-full bg-red-100 animate-ping opacity-75"></div>
                    <i class="fas fa-trash-alt text-4xl text-red-600 relative"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-800 mb-3">Hapus Data?</h3>
                <p class="text-slate-500 text-sm mb-8">Data akan dihapus secara permanen.</p>
                <div class="space-y-3">
                    <form id="formDelete" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-4 bg-red-600 text-white rounded-2xl font-bold hover:bg-red-700 transition-all shadow-lg shadow-red-200">Ya, Hapus Sekarang</button>
                    </form>
                    <button type="button" onclick="hideModal('modalDelete')" class="w-full py-4 bg-white text-slate-400 rounded-2xl font-bold hover:text-slate-600 transition-all">Batal</button>
                </div>
            </div>
        </div>
    </div>

    @include('layout.partials.aos-scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ── Flatpickr date picker (DD/MM/YYYY) ─────────────────────────
        function formatDateID(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            if (isNaN(d)) return dateStr;
            const dd = String(d.getDate()).padStart(2, '0');
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const yyyy = d.getFullYear();
            return dd + '/' + mm + '/' + yyyy;
        }

        function initFlatpickr() {
            document.querySelectorAll('.tgl-input').forEach(function(input) {
                flatpickr(input, {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd/m/Y',
                    locale: 'id',
                    allowInput: true,
                    disableMobile: true,
                });
            });
            document.querySelectorAll('.tgl-filter').forEach(function(input) {
                flatpickr(input, {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd/m/Y',
                    locale: 'id',
                    allowInput: true,
                    disableMobile: true,
                    onChange: function() {
                        if (typeof window.toggleResetBtn === 'function') window.toggleResetBtn();
                        if (typeof window.fetchList === 'function') window.fetchList();
                    }
                });
            });
        }
        document.addEventListener('DOMContentLoaded', initFlatpickr);

        function showModal(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.remove('hidden');
            el.classList.add('flex');
        }
        
        function hideModal(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.add('hidden');
            el.classList.remove('flex');
        }

        function bukaTambah() {
            currentBarangKeluarId = null; 
            
            activeUnitMode = 'add';
            activeUnitIndex = null;
            
            addUnitList.length = 0; 
            renderUnitTable('add');
            rebuildHiddenFields('add');
            
            // 4. Kosongkan form input
            document.getElementById('formTambah').reset();
            document.getElementById('input-sn').value = '';
            const snError = document.getElementById('sn-error');
            if (snError) {
                snError.classList.add('hidden');
                snError.textContent = '';
            }
            
            showModal('modalTambahTransaksi'); 
        }

        function bukaDetail(data) {
            document.getElementById('detail-jumlah').value = data.jumlah + ' Unit' || '-';
            document.getElementById('detail-tgl').value = formatDateID(data.tgl) || '-';
            document.getElementById('detail-penerima').value = data.penerima || '-';
            document.getElementById('detail-pic').value = data.pic || '-';
            
            const tbody = document.getElementById('detail-unit-body');
            tbody.innerHTML = '';
            if (!data.units || data.units.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="px-5 py-5 text-left text-xs text-slate-400">Tidak ada unit.</td></tr>';
            } else {
                data.units.forEach(function(unit, i) {
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-slate-50/50 transition-colors';
                    tr.innerHTML =
                        '<td class="px-5 py-3.5 text-left text-xs font-bold text-slate-800">' + (i + 1) + '</td>' +
                        '<td class="px-5 py-3.5">' +
                            '<span class="text-xs font-mono font-bold text-blue-600 px-2 py-1 bg-blue-50 rounded-lg uppercase tracking-tight">' + unit.sn + '</span>' +
                        '</td>' +
                        '<td class="px-5 py-3.5 text-sm text-slate-700">' + (unit.nama || data.barang || '-') + '</td>';
                    tbody.appendChild(tr);
                });
            }

            showModal('modalDetail');
        }

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500
        });

        @if(session('toast_success'))
            Toast.fire({ icon: 'success', title: "{{ session('toast_success') }}" });
        @endif

        @if(session('error'))
            Toast.fire({ icon: 'error', title: "{{ session('error') }}" });
        @endif
        
        const addUnitList = [];
        let editUnitList = [];
        let activeUnitMode = 'add';
        let activeUnitIndex = null;
        let isSavingUnit = false;
        let currentBarangKeluarId = null;

        function normalizeSerial(value) {
            return String(value).trim().toUpperCase();
        }

        function updateSubmitButtonState() {
            const submitBtn = document.getElementById('btnSubmitTambah');
            if (submitBtn) {
                submitBtn.disabled = addUnitList.length === 0;
            }
            const submitEditBtn = document.getElementById('btnSubmitEdit');
            if (submitEditBtn) {
                submitEditBtn.disabled = editUnitList.length === 0;
            }
        }

        function getUnitList(mode) {
            return mode === 'edit' ? editUnitList : addUnitList;
        }

        function renderUnitTable(mode) {
            const tbody = document.getElementById(mode === 'edit' ? 'unitEditBody' : 'unitTambahBody');
            const units = getUnitList(mode);
            const emptyRowId = mode === 'edit' ? 'emptyEditUnitRow' : 'emptyUnitRow';

            tbody.innerHTML = '';
            if (units.length === 0) {
                const emptyRow = document.createElement('tr');
                emptyRow.id = emptyRowId;
                emptyRow.innerHTML = '<td colspan="' + (mode === 'edit' ? '4' : '4') + '" class="px-6 py-6 text-center text-xs text-slate-400">Belum ada unit. Klik <span class="font-bold text-blue-500">Input</span> untuk menambahkan.</td>';
                tbody.appendChild(emptyRow);
                return;
            }

            units.forEach(function(unit, index) {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-50/50 transition-colors';
                if (mode === 'edit') {
                    tr.innerHTML =
                        '<td class="px-6 py-4 text-left text-xs font-bold text-slate-800">' + (index + 1) + '</td>' +
                        '<td class="px-6 py-4 text-left"><span class="text-xs font-mono font-bold text-blue-600 px-2 py-1 bg-blue-50 rounded-lg uppercase tracking-tight">' + unit.sn + '</span></td>' +
                        '<td class="px-6 py-4 text-left text-xs font-medium text-slate-700">' + unit.nama + '</td>' +
                        '<td class="px-6 py-4 text-left text-xs font-medium text-slate-700 flex items-center gap-2">' +
                            '<button type="button" onclick="editUnitRow(' + index + ')" class="px-3 py-1 bg-amber-50 text-amber-700 rounded-lg border border-amber-100 hover:bg-amber-100 transition">Edit</button>' +
                            '<button type="button" onclick="deleteEditUnit(' + index + ')" class="px-3 py-1 bg-red-50 text-red-700 rounded-lg border border-red-100 hover:bg-red-100 transition">Hapus</button>' +
                        '</td>';
                } else {
                    tr.innerHTML =
                        '<td class="px-6 py-4 text-left text-xs font-bold text-slate-800">' + (index + 1) + '</td>' +
                        '<td class="px-6 py-4 text-left"><span class="text-xs font-mono font-bold text-blue-600 px-2 py-1 bg-blue-50 rounded-lg uppercase tracking-tight">' + unit.sn + '</span></td>' +
                        '<td class="px-6 py-4 text-left text-xs font-medium text-slate-700">' + unit.nama + '</td>' +
                        '<td class="px-6 py-4 text-left text-xs font-medium text-slate-700 flex items-center gap-2">' +
                            '<button type="button" onclick="editUnitRow(' + index + ')" class="px-3 py-1 bg-amber-50 text-amber-700 rounded-lg border border-amber-100 hover:bg-amber-100 transition">Edit</button>' +
                            '<button type="button" onclick="deleteTambahUnit(' + index + ')" class="px-3 py-1 bg-red-50 text-red-700 rounded-lg border border-red-100 hover:bg-red-100 transition">Hapus</button>' +
                        '</td>';
                }
                tbody.appendChild(tr);
            });
            updateSubmitButtonState();
        }

        function rebuildHiddenFields(mode) {
            const container = document.getElementById(mode === 'edit' ? 'hidden-edit-fields-container' : 'hidden-fields-container');
            container.innerHTML = '';
            const units = getUnitList(mode);

            units.forEach(function(unit) {
                const hiddenSn = document.createElement('input');
                hiddenSn.type = 'hidden';
                hiddenSn.name = 'serial_number[]';
                hiddenSn.value = unit.sn;
                container.appendChild(hiddenSn);
            });
        }

        function resetInputUnitForm() {
            document.getElementById('input-sn').value = '';
            const snError = document.getElementById('sn-error');
            if (snError) {
                snError.classList.add('hidden');
                snError.textContent = '';
            }
            activeUnitIndex = null;
        }

        function setupInputUnit(mode) {
            activeUnitMode = mode;
            resetInputUnitForm();
            
            // Reset scanner state when opening modal
            if (isScanning) { stopScanner(); }
            document.getElementById('scanner-container').classList.add('hidden');
            
            showModal('modalInputUnit');
            setTimeout(function() { document.getElementById('input-sn').focus(); }, 100);
        }

        document.getElementById('btnOpenInputUnit').addEventListener('click', function() {
            setupInputUnit('add');
        });

        document.getElementById('btnOpenEditInputUnit').addEventListener('click', function() {
            setupInputUnit('edit');
        });

        document.getElementById('btnCloseInputUnit').addEventListener('click', function() {
            hideModal('modalInputUnit');
        });

        document.getElementById('btnCancelInputUnit').addEventListener('click', function() {
            hideModal('modalInputUnit');
        });

        async function simpanDataUnit() {
            if (isSavingUnit) return;
            
            const snRaw = document.getElementById('input-sn').value;
            const sn = normalizeSerial(snRaw);
            const errorSn = document.getElementById('sn-error');

            errorSn.classList.add('hidden');

            if (!sn) {
                errorSn.textContent = 'Serial number tidak boleh kosong.';
                errorSn.classList.remove('hidden');
                return;
            }

            const currentList = getUnitList(activeUnitMode);

            // Cek duplikat di list lokal (tabel UI)
            const duplicateIndex = currentList.findIndex(function(u, idx) {
                return normalizeSerial(u.sn) === sn && idx !== activeUnitIndex;
            });
            
            if (duplicateIndex !== -1) {
                errorSn.textContent = 'Serial number sudah ada di daftar tabel.';
                errorSn.classList.remove('hidden');
                return;
            }

            isSavingUnit = true;
            document.getElementById('btnSaveUnit').innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            try {
                const url = `/admin/barangkeluar/check-serial?serial_number=${encodeURIComponent(sn)}&barang_keluar_id=${currentBarangKeluarId || ''}`;
                const response = await fetch(url);
                const result = await response.json();

                if (!result.valid) {
                    errorSn.textContent = result.message || 'Serial number tidak valid.';
                    errorSn.classList.remove('hidden');
                } else {
                    if (activeUnitIndex !== null) {
                        currentList[activeUnitIndex].sn = sn;
                        currentList[activeUnitIndex].nama = result.nama_barang;
                    } else {
                        currentList.push({
                            sn: sn,
                            nama: result.nama_barang,
                            id: result.unit_id
                        });
                    }

                    renderUnitTable(activeUnitMode);
                    rebuildHiddenFields(activeUnitMode);
                    hideModal('modalInputUnit');
                    resetInputUnitForm();
                }
            } catch (error) {
                console.error(error);
                errorSn.textContent = 'Terjadi kesalahan saat memeriksa serial number.';
                errorSn.classList.remove('hidden');
            } finally {
                isSavingUnit = false;
                document.getElementById('btnSaveUnit').innerHTML = 'Simpan';
            }
        }

        function editUnitRow(index) {
            const unit = getUnitList(activeUnitMode)[index];
            activeUnitIndex = index;
            document.getElementById('input-sn').value = unit.sn;
            showModal('modalInputUnit');
        }

        function deleteTambahUnit(index) {
            addUnitList.splice(index, 1);
            renderUnitTable('add');
            rebuildHiddenFields('add');
        }

        function deleteEditUnit(index) {
            editUnitList.splice(index, 1);
            renderUnitTable('edit');
            rebuildHiddenFields('edit');
        }

        function bukaEdit(data) {
            activeUnitMode = 'edit';
            activeUnitIndex = null;
            document.getElementById('edit-penerima').value = data.penerima || '';
            const editTglEl = document.getElementById('edit-tgl');
            if (editTglEl._flatpickr) {
                editTglEl._flatpickr.setDate(data.tgl || '', true);
            } else {
                editTglEl.value = data.tgl || '';
            }
            currentBarangKeluarId = data.id;
            
            const form = document.getElementById('formEdit');
            form.action = '/admin/barangkeluar/' + data.id;
            
            editUnitList = data.units || [];
            renderUnitTable('edit');
            rebuildHiddenFields('edit');

            showModal('modalEdit');
        }

        function bukaDelete(id) {
            const form = document.getElementById('formDelete');
            form.action = '/admin/barangkeluar/' + id;
            showModal('modalDelete');
        }

        // Barcode Scanner (html5-qrcode)
        let html5QrCode = null;
        let isScanning = false;

        function startScanner() {
            const scannerContainer = document.getElementById('scanner-container');
            const scanStatus = document.getElementById('scan-status');
            scannerContainer.classList.remove('hidden');
            scanStatus.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i><span>Mengarahkan kamera ke barcode...</span>';

            if (html5QrCode && isScanning) {
                return;
            }

            html5QrCode = new Html5Qrcode("reader");

            const config = {
                fps: 15,
                qrbox: function(viewfinderWidth, viewfinderHeight) {
                    const w = viewfinderWidth;
                    const h = viewfinderHeight;
                    const boxWidth = Math.floor(w * 0.85);
                    const boxHeight = Math.floor(h * 0.45);
                    return { width: boxWidth, height: boxHeight };
                },
                aspectRatio: 1.333334,
                formatsToSupport: [
                    Html5QrcodeSupportedFormats.QR_CODE,
                    Html5QrcodeSupportedFormats.CODE_128,
                    Html5QrcodeSupportedFormats.CODE_39,
                    Html5QrcodeSupportedFormats.EAN_13,
                    Html5QrcodeSupportedFormats.EAN_8,
                    Html5QrcodeSupportedFormats.UPC_A,
                    Html5QrcodeSupportedFormats.UPC_E,
                    Html5QrcodeSupportedFormats.CODE_93,
                    Html5QrcodeSupportedFormats.CODABAR,
                    Html5QrcodeSupportedFormats.ITF,
                ]
            };

            function onScanSuccess(decodedText, decodedResult) {
                const snInput = document.getElementById('input-sn');
                const normalized = normalizeSerial(decodedText);
                snInput.value = normalized;

                scanStatus.innerHTML = '<i class="fas fa-check-circle text-emerald-500"></i><span class="text-emerald-700 font-bold">Barcode terdeteksi: ' + normalized + '</span>';

                setTimeout(function() { stopScanner(); }, 1200);

                if (navigator.vibrate) { navigator.vibrate(200); }

                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioCtx.createOscillator();
                    const gainNode = audioCtx.createGain();
                    oscillator.connect(gainNode);
                    gainNode.connect(audioCtx.destination);
                    oscillator.frequency.value = 1200;
                    oscillator.type = 'sine';
                    gainNode.gain.value = 0.15;
                    oscillator.start();
                    setTimeout(() => { oscillator.stop(); audioCtx.close(); }, 150);
                } catch(e) {}
            }

            function onScanError(errorMessage) {
            }

            function startCameraWithId(deviceId, cfg, statusEl) {
                html5QrCode.start(
                    { deviceId: { exact: deviceId } },
                    cfg,
                    onScanSuccess,
                    onScanError
                ).then(function() {
                    isScanning = true;
                }).catch(function(err) {
                    startCameraWithConstraints({ facingMode: "user" }, cfg, statusEl);
                });
            }

            function startCameraWithConstraints(constraints, cfg, statusEl) {
                html5QrCode.start(
                    constraints,
                    cfg,
                    onScanSuccess,
                    onScanError
                ).then(function() {
                    isScanning = true;
                }).catch(function(err) {
                    statusEl.innerHTML = '<i class="fas fa-exclamation-triangle text-red-500"></i><span class="text-red-500">Gagal mengakses kamera. Pastikan mengizinkan akses kamera dan menggunakan HTTPS atau localhost.</span>';
                    isScanning = false;
                });
            }

            Html5Qrcode.getCameras().then(function(devices) {
                if (devices && devices.length) {
                    var cameraId = devices[0].id;
                    for (var i = 0; i < devices.length; i++) {
                        var label = (devices[i].label || '').toLowerCase();
                        if (label.includes('back') || label.includes('rear') || label.includes('environment')) {
                            cameraId = devices[i].id;
                            break;
                        }
                    }
                    startCameraWithId(cameraId, config, scanStatus);
                } else {
                    startCameraWithConstraints({ facingMode: "user" }, config, scanStatus);
                }
            }).catch(function(err) {
                startCameraWithConstraints({ facingMode: "user" }, config, scanStatus);
            });
        }

        function stopScanner() {
            const scannerContainer = document.getElementById('scanner-container');
            if (html5QrCode && isScanning) {
                html5QrCode.stop().then(function() {
                    isScanning = false;
                    scannerContainer.classList.add('hidden');
                    html5QrCode.clear();
                }).catch(function(err) {
                    isScanning = false;
                    scannerContainer.classList.add('hidden');
                });
            } else {
                scannerContainer.classList.add('hidden');
            }
        }

        document.getElementById('btnScanBarcode').addEventListener('click', function() {
            const scannerContainer = document.getElementById('scanner-container');
            if (isScanning) {
                stopScanner();
            } else {
                startScanner();
            }
        });

        document.getElementById('btnStopScan').addEventListener('click', function() {
            stopScanner();
        });

        const originalHideModal = hideModal;
        hideModal = function(id) {
            if (id === 'modalInputUnit' && isScanning) {
                stopScanner();
            }
            originalHideModal(id);
        };

        document.getElementById('btnCloseInputUnit').addEventListener('click', function() {
            if (isScanning) stopScanner();
        });
        document.getElementById('btnCancelInputUnit').addEventListener('click', function() {
            if (isScanning) stopScanner();
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
    </script>
    @include('layout.partials.ajax-list-search-init', ['indexUrl' => route('barang-keluar.index')])
</body>
</html>