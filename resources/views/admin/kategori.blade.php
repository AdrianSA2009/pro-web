<!DOCTYPE html>
<html lang="id">
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kategori - Admin</title>
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('layout.partials.aos-head')
    
    <style>
        body { font-family: 'Inter', sans-serif; }

        .sidebar-nav::-webkit-scrollbar { width: 0px; background: transparent; }
        
        .glass-hover:hover {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
        }
    </style>
</head>
<body class="bg-slate-50 text-gray-800 h-screen flex overflow-hidden">

    <!-- Sidebar -->
    @include('layout.sidebar')
    <!-- End Sidebar -->

    <div class="flex-1 flex flex-col w-full md:ml-72 transition-all duration-300">
        <header class="bg-white/80 backdrop-blur-md sticky top-0 z-10 flex items-center justify-between px-8 py-4 border-b border-slate-100">
            <div class="flex items-center gap-4">
                <button data-drawer-target="sidebar-multi-level-sidebar" data-drawer-toggle="sidebar-multi-level-sidebar" aria-controls="sidebar-multi-level-sidebar" type="button" class="md:hidden p-2 rounded-lg hover:bg-slate-100 transition-colors">
                    <i class="fas fa-bars text-xl text-slate-600"></i>
                </button>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight">Kategori inventaris</h2>
            </div>
            @include('layout.partials.topbar-profile')
        </header>

        <main class="flex-1 min-h-0 overflow-y-auto p-6 md:p-8 space-y-6 bg-slate-50">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4" data-aos="fade-down">
                <div>
                    <nav class="flex text-sm text-slate-500 mb-2">
                        <span>Master Data</span>
                        <span class="mx-2">/</span>
                        <span class="text-slate-900 font-medium">Data Kategori</span>
                    </nav>
                    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Kategori Inventaris</h2>
                </div>
                <button data-modal-target="modalTambahKategori" data-modal-toggle="modalTambahKategori" class="flex items-center gap-2 px-6 py-3 bg-blue-600 rounded-xl text-white font-semibold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Kategori</span>
                </button>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100" data-aos="fade-up" data-aos-delay="100">
                <div class="relative w-full group">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 group-focus-within:text-blue-600 transition-colors">
                        <i class="fas fa-search text-sm"></i>
                    </span>
                    <input type="text" id="searchInput" value="{{ request('search') }}" class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-400 text-sm" placeholder="Cari nama kategori...">
                </div>
            </div>

            <div class="bg-white rounded-[24px] shadow-sm border border-slate-100 overflow-hidden" data-aos="fade-up" data-aos-delay="200">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="kategoriTable">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-10 py-5 text-[11px] uppercase tracking-[0.15em] font-bold text-slate-400 text-center w-28">NO</th>
                                <th class="px-10 py-5 text-[11px] uppercase tracking-[0.15em] font-bold text-slate-400">NAMA KATEGORI</th>
                                <th class="px-10 py-5 text-[11px] uppercase tracking-[0.15em] font-bold text-slate-400 text-center w-48">AKSI</th>
                            </tr>
                        </thead>
                        <tbody id="ajax-list-tbody" class="divide-y divide-slate-100">
                            @forelse($kategoris as $index => $kategori)
                            <tr class="kategori-row group hover:bg-slate-50/50 transition-all">
                                <td class="px-10 py-6 text-slate-500 font-bold text-center text-sm">{{ $kategoris->firstItem() + $index }}</td>
                                <td class="px-10 py-6 font-bold text-[15px] kategori-nama">{{ $kategori->nama }}</td>
                                <td class="px-10 py-6">
                                    <div class="flex items-center justify-center gap-5">
                                        <button data-modal-target="modalEdit" data-modal-toggle="modalEdit" onclick="editKategori({{ $kategori->id }}, '{{ $kategori->nama }}')" class="text-slate-500 hover:text-blue-600 transition-colors">
                                            <i class="fas fa-edit text-lg"></i>
                                        </button>
                                        @if($kategori->barang()->exists())
                                            <button type="button" disabled 
                                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-300 cursor-not-allowed" 
                                                    title="Kategori tidak bisa dihapus karena sedang digunakan pada data barang/transaksi">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        @else
                                            <button type="button" data-modal-target="modalDelete" data-modal-toggle="modalDelete" onclick="deleteKategori({{ $kategori->id }}, '{{ $kategori->nama }}')"
                                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all" 
                                                    title="Hapus Kategori">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <i class="fas fa-box-open text-5xl mb-4 text-slate-300"></i>
                                            <p class="font-medium text-sm">Data Kategori tidak ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div id="ajax-list-footer" class="p-6 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-slate-500">Menampilkan {{ $kategoris->count() }} dari {{ $kategoris->total() }} Kategori</p>
                    @if ($kategoris->hasPages())
                        <div class="flex items-center gap-2">
                            <a href="{{ $kategoris->previousPageUrl() ?: '#' }}" class="px-3 py-1.5 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition-all text-sm {{ $kategoris->onFirstPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                                Previous
                            </a>

                            @foreach ($kategoris->getUrlRange(1, $kategoris->lastPage()) as $page => $url)
                                @if ($page == $kategoris->currentPage())
                                    <span class="w-8 h-8 bg-blue-600 text-white rounded-lg text-sm font-bold flex items-center justify-center">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="w-8 h-8 hover:bg-slate-100 text-slate-600 rounded-lg text-sm transition-all flex items-center justify-center">{{ $page }}</a>
                                @endif
                            @endforeach

                            <a href="{{ $kategoris->nextPageUrl() ?: '#' }}" class="px-3 py-1.5 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition-all text-sm {{ $kategoris->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed pointer-events-none' }}">
                                Next
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </main> 
    </div>

    <!-- Modal Delete -->
    <div id="modalDelete" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div id="closeDeleteOverlay" class="fixed inset-0 bg-red-900/20 backdrop-blur-sm transition-opacity"></div>
        <div class="relative bg-white w-full max-w-sm rounded-[2.5rem] shadow-2xl overflow-hidden text-center border-4 border-red-50 mx-auto">
            <div class="p-10">
                <div class="relative w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <div class="absolute inset-0 rounded-full bg-red-100 animate-ping opacity-75"></div>
                    <i class="fas fa-trash-alt text-4xl text-red-600 relative"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-800 mb-3">Hapus Data?</h3>
                <p class="text-slate-500 text-sm mb-2">Kategori: <span id="deleteKategoriName" class="font-bold text-slate-700"></span></p>
                <p class="text-slate-500 text-sm mb-8">Data akan dihapus secara permanen.</p>
                <div class="space-y-3">
                    <form id="deleteForm" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                    <button onclick="document.getElementById('deleteForm').submit()" class="w-full py-4 bg-red-600 text-white rounded-2xl font-bold hover:bg-red-700 transition-all shadow-lg shadow-red-200">Ya, Hapus Sekarang</button>
                    <button data-modal-hide="modalDelete" class="w-full py-4 bg-white text-slate-400 rounded-2xl font-bold hover:text-slate-600 transition-all">Cancel Action</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal Delete -->

    <!-- Modal Tambah -->
    <div id="modalTambahKategori" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-[2px]">
        <div class="relative w-full max-w-md transition-all transform">
            <div class="relative bg-white rounded-[2rem] shadow-2xl overflow-hidden border border-slate-100">

                <div class="flex items-start justify-between p-6 pb-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white text-lg shadow-lg shadow-blue-200">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 tracking-tight">Kategori Baru</h3>
                            <p class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Tambahkan jenis barang</p>
                        </div>
                    </div>
                    <button type="button" data-modal-hide="modalTambahKategori" class="text-slate-300 hover:text-slate-900 transition-all">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form id="addKategoriForm" action="{{ route('admin.kategori.store') }}" method="POST" class="p-6 pt-2">
                    @csrf
                    <div class="mb-6">
                        <label class="block mb-2 text-[10px] font-bold text-slate-400 uppercase tracking-[0.15em] ml-1">Nama Kategori</label>
                        
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                                <i class="fas fa-tag text-xs"></i>
                            </div>
                            <input type="text" name="nama" id="inputTambahNama"
                                   placeholder="Contoh : Kulkas"
                                   class="w-full pl-10 pr-4 py-3 bg-slate-50/50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none text-sm text-slate-700 font-semibold placeholder:text-slate-300">
                        </div>

                        <span id="errorTambahNama" class="text-red-500 text-xs mt-1 hidden"></span>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" data-modal-hide="modalTambahKategori" class="flex-1 py-3 bg-slate-50 text-slate-500 rounded-2xl font-bold hover:bg-slate-100 transition-all text-xs">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-2xl font-bold shadow-md shadow-blue-100 hover:bg-blue-700 transition-all text-xs tracking-wide">
                            Tambah Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal Tambah -->

    <!-- Modal Edit -->
    <div id="modalEdit" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full transition-all duration-300">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/30 backdrop-blur-sm"></div>

            <div class="relative bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden border border-slate-100">

                <div class="flex items-start justify-between p-7 pb-6">
                    <div class="flex items-center gap-5">
                        <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center text-white text-xl shadow-lg shadow-amber-200">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Edit Kategori</h3>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Perbarui informasi kategori barang</p>
                        </div>
                    </div>
                    <button type="button" class="text-slate-400 bg-transparent hover:bg-slate-100 hover:text-slate-900 rounded-lg text-sm w-10 h-10 inline-flex justify-center items-center transition-all" data-modal-hide="modalEdit">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>

                <div class="border-b border-slate-100"></div>

                <form id="editForm" method="POST" class="p-8">
                    @csrf
                    @method('PUT')
                    <div class="mb-8">
                        <label class="block mb-2 text-[11px] font-bold text-slate-400 uppercase tracking-[0.15em] ml-1">Nama Kategori</label>

                        <input type="text" id="editNamaInput" name="nama" 
                               class="w-full px-5 py-3.5 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all outline-none text-slate-700 font-semibold placeholder:text-slate-300">
                        
                        <span id="errorEditNama" class="text-red-500 text-xs mt-1 hidden block"></span>
                    </div>

                    <div class="flex items-center justify-center gap-4">
                        <button type="button" data-modal-hide="modalEdit" class="px-10 py-3.5 bg-slate-100 text-slate-700 rounded-xl font-bold hover:bg-slate-200 transition-all text-sm tracking-tight flex-1">
                            Batal
                        </button>

                        <button type="submit" class="px-10 py-3.5 bg-amber-500 text-white rounded-xl font-bold shadow-lg shadow-amber-200 hover:bg-amber-600 transition-all text-sm tracking-tight flex-1">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal Edit -->

    @include('layout.partials.aos-scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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

        // Edit function
        function editKategori(id, nama) {
            document.getElementById('editNamaInput').value = nama;
            const editForm = document.getElementById('editForm');
            editForm.action = `/admin/kategori/${id}`;
        }

        // Delete function
        function deleteKategori(id, nama) {
            document.getElementById('deleteKategoriName').textContent = nama;
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `/admin/kategori/${id}`;
        }

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if(session('toast_success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('toast_success') }}"
            });
        @endif

        // Setup AJAX untuk Form Tambah
        document.getElementById('addKategoriForm').addEventListener('submit', async function(e) {
            e.preventDefault(); 
            
            let form = this;
            let submitBtn = form.querySelector('button[type="submit"]');
            let inputNama = document.getElementById('inputTambahNama');
            let errorMsg = document.getElementById('errorTambahNama');
            let originalText = submitBtn.innerHTML;
            
            inputNama.classList.remove('border-red-500');
            errorMsg.classList.add('hidden');
            errorMsg.classList.remove('block');
            errorMsg.innerText = '';
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Menyimpan...';

            try {
                let response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json' 
                    },
                    body: new FormData(form)
                });

                if (response.status === 422) {
                    let data = await response.json();
                    if (data.errors && data.errors.nama) {
                        inputNama.classList.add('border-red-500');
                        errorMsg.innerText = data.errors.nama[0];
                        errorMsg.classList.remove('hidden');
                        errorMsg.classList.add('block');
                    }
                } else if (response.ok) {
                    // Simpan status sukses di memori browser sementara
                    sessionStorage.setItem('showToastSuccess', 'Kategori berhasil ditambahkan!');
                    window.location.reload(); 
                }
            } catch (error) {
                console.error('Error:', error);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });

        // Setup AJAX untuk Form Edit
        document.getElementById('editForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            let form = this;
            let submitBtn = form.querySelector('button[type="submit"]');
            let inputNama = document.getElementById('editNamaInput');
            let errorMsg = document.getElementById('errorEditNama');
            let originalText = submitBtn.innerHTML;
            
            inputNama.classList.remove('border-red-500');
            errorMsg.classList.add('hidden');
            errorMsg.classList.remove('block');
            errorMsg.innerText = '';
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Menyimpan...';

            try {
                let response = await fetch(form.action, {
                    method: 'POST', 
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: new FormData(form)
                });

                if (response.status === 422) {
                    let data = await response.json();
                    if (data.errors && data.errors.nama) {
                        inputNama.classList.add('border-red-500');
                        errorMsg.innerText = data.errors.nama[0];
                        errorMsg.classList.remove('hidden');
                        errorMsg.classList.add('block');
                    }
                } else if (response.ok) {
                    // Simpan status sukses di memori browser sementara
                    sessionStorage.setItem('showToastSuccess', 'Kategori berhasil diperbarui!');
                    window.location.reload(); 
                }
            } catch (error) {
                console.error('Error:', error);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            if (sessionStorage.getItem('showToastSuccess')) {
                Toast.fire({
                    icon: 'success',
                    title: sessionStorage.getItem('showToastSuccess')
                });
                sessionStorage.removeItem('showToastSuccess');
            }

        });
    </script>
    @include('layout.partials.ajax-list-search-init', ['indexUrl' => route('admin.kategori.index')])
</body>
</html>