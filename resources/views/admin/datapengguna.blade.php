<!DOCTYPE html>
<html lang="id">
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengguna - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

    @include('layout.sidebar')
    
    <div class="flex-1 flex flex-col w-full md:ml-72 transition-all duration-300">
        <header class="bg-white/80 backdrop-blur-md sticky top-0 z-10 flex items-center justify-between px-8 py-4 border-b border-slate-100">
            <div class="flex items-center gap-4">
                <button data-drawer-target="sidebar-multi-level-sidebar" data-drawer-toggle="sidebar-multi-level-sidebar" aria-controls="sidebar-multi-level-sidebar" type="button" class="md:hidden p-2 rounded-lg hover:bg-slate-100 transition-colors">
                    <i class="fas fa-bars text-xl text-slate-600"></i>
                </button>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight">Manajemen Pengguna</h2>
            </div>
            @include('layout.partials.topbar-profile')
        </header>
        <main class="flex-1 min-h-0 overflow-y-auto p-6 md:p-8 space-y-6 bg-slate-50">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4" data-aos="fade-down">
                <div>
                    <nav class="flex text-sm text-slate-500 mb-2">
                        <span>Master Data</span>
                        <span class="mx-2">/</span>
                        <span class="text-slate-900 font-medium">Data Pengguna</span>
                    </nav>
                    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen Pengguna</h2>
                </div>
                <button data-modal-target="modalTambahUser" data-modal-toggle="modalTambahUser" id="btnTambahPengguna" class="flex items-center gap-2 px-6 py-3 bg-blue-600 rounded-xl text-white font-semibold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Pengguna</span>
                </button>
            </div>
        
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-wrap gap-4 items-center justify-between" data-aos="fade-up" data-aos-delay="100">
                <div class="relative w-full md:w-96 group">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 group-focus-within:text-blue-600 transition-colors">
                        <i class="fas fa-search text-sm"></i>
                    </span>
                    <input type="text" 
                           id="search-input" 
                           value="{{ request('search') }}"
                           class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-400 text-sm" 
                           placeholder="Cari nama atau username...">
                </div>
                
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <div class="relative flex-1 md:w-52">
                        <select id="filterJabatan" class="w-full appearance-none px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-600 text-sm font-medium cursor-pointer transition-all pr-10">
                            <option value="" {{ !request('role') ? 'selected' : '' }}>Semua Jabatan</option>
                            <option value="admin_gudang" {{ request('role') == 'admin_gudang' ? 'selected' : '' }}>Admin Gudang</option>
                            <option value="manajer" {{ request('role') == 'manajer' ? 'selected' : '' }}>Manajer</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400">
                            <i class="fas fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden" data-aos="fade-up" data-aos-delay="200">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 py-4 text-[11px] uppercase tracking-widest font-bold text-slate-400">No</th>
                                <th class="px-6 py-4 text-[11px] uppercase tracking-widest font-bold text-slate-400">Nama Lengkap</th>
                                <th class="px-6 py-4 text-[11px] uppercase tracking-widest font-bold text-slate-400">Username</th>
                                <th class="px-6 py-4 text-[11px] uppercase tracking-widest font-bold text-slate-400">Jabatan</th>
                                <th class="px-6 py-4 text-[11px] uppercase tracking-widest font-bold text-slate-400 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="ajax-list-tbody" class="divide-y divide-slate-100">
                            @forelse($pengguna as $index => $user)
                            <tr class="group hover:bg-slate-50/50 transition-all">
                                <td class="px-6 py-4 font-medium text-slate-500">
                                    {{ $pengguna->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800">{{ $user->nama }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-semibold">{{ $user->username }}</span>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-700">
                                    {{ $user->role == 'admin_gudang' ? 'Admin Gudang' : ucfirst($user->role) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Menambahkan data email ke dalam parameter JavaScript openEditModal --}}
                                        <button onclick="openEditModal('{{ $user->id }}', '{{ $user->nama }}', '{{ $user->username }}', '{{ $user->email }}', '{{ $user->role }}')" data-modal-target="modalEdit" data-modal-toggle="modalEdit" class="p-2 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-all">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="openDeleteModal('{{ $user->id }}')" data-modal-target="modalDelete" data-modal-toggle="modalDelete" class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                                        <i class="fas fa-user-slash text-2xl mb-2 block"></i>
                                        Data pengguna tidak ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div id="ajax-list-footer" class="p-6 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-slate-500">Menampilkan {{ $pengguna->count() }} dari {{ $pengguna->total() }} Pengguna</p>
                    @if ($pengguna->hasPages())
                        <div class="flex items-center gap-2">
                            <a href="{{ $pengguna->previousPageUrl() ?: '#' }}" class="px-3 py-1.5 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition-all text-sm {{ $pengguna->onFirstPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                                Previous
                            </a>

                            @foreach ($pengguna->getUrlRange(1, $pengguna->lastPage()) as $page => $url)
                                @if ($page == $pengguna->currentPage())
                                    <span class="w-8 h-8 bg-blue-600 text-white rounded-lg text-sm font-bold flex items-center justify-center">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="w-8 h-8 hover:bg-slate-100 text-slate-600 rounded-lg text-sm transition-all flex items-center justify-center">{{ $page }}</a>
                                @endif
                            @endforeach

                            <a href="{{ $pengguna->nextPageUrl() ?: '#' }}" class="px-3 py-1.5 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition-all text-sm {{ $pengguna->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed pointer-events-none' }}">
                                Next
                            </a>
                        </div>
                    @endif
                </div>
        </main>
    </div>

    <div id="modalTambahUser" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div id="closeModalOverlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100">
                <div class="bg-slate-900 px-8 py-6 text-white flex items-center justify-between">
                    <h3 class="text-lg font-bold tracking-tight">Tambah Pengguna Baru</h3>
                    <button type="button" data-modal-hide="modalTambahUser" class="text-slate-400 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="{{ route('admin.pengguna.store') }}" method="POST" class="px-8 mb-8 space-y-4">
                    @csrf
                    <div>
                        <label class="block mb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ old('id') ? '' : old('nama') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none text-sm font-semibold transition-all placeholder:text-slate-400" placeholder="Masukkan nama..." required>
                    </div>
                    <div>
                        <label class="block mb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Username</label>
                        <input type="text" name="username" value="{{ old('id') ? '' : old('username') }}" class="w-full px-4 py-3 bg-slate-50 border @if(!old('id') && $errors->has('username')) border-red-500 @else border-slate-200 @endif rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none text-sm font-semibold transition-all placeholder:text-slate-400" placeholder="Contoh: adrian.sa" required>

                        @if(!old('id'))
                            @error('username')
                                <p class="text-red-500 text-xs mt-2 font-semibold"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                    {{-- Tambah Field Email --}}
                    <div>
                        <label class="block mb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Email</label>
                        <input type="email" name="email" value="{{ old('id') ? '' : old('email') }}" class="w-full px-4 py-3 bg-slate-50 border @if(!old('id') && $errors->has('email')) border-red-500 @else border-slate-200 @endif rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none text-sm font-semibold transition-all placeholder:text-slate-400" placeholder="Contoh: adrian@gmail.com" required>

                        @if(!old('id'))
                            @error('email')
                                <p class="text-red-500 text-xs mt-2 font-semibold"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                    <div>
                        <label class="block mb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Password</label>
                        <input type="password" name="password" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none text-sm font-semibold transition-all placeholder:text-slate-400" placeholder="••••••••" required>
                    </div>
                    <div>
                        <label class="block mb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Jabatan</label>
                        <select name="role" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none text-sm font-semibold transition-all text-slate-700" required>
                            <option value="" disabled {{ !old('role') ? 'selected' : '' }}>Pilih Role</option>
                            <option value="admin_gudang" {{ (!old('id') && old('role') == 'admin_gudang') ? 'selected' : '' }}>Admin Gudang</option>
                            <option value="manajer" {{ (!old('id') && old('role') == 'manajer') ? 'selected' : '' }}>Manajer</option>
                        </select>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button data-modal-hide="modalTambahUser" type="button" class="flex-1 py-3 bg-slate-100 text-slate-500 rounded-xl font-bold">Batal</button>
                        <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-200">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div id="modalEdit" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div id="closeModalOverlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100">
                <div class="bg-amber-500 px-8 py-6 text-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-user-edit"></i>
                        <h3 class="text-lg font-bold tracking-tight">Edit Data Pengguna</h3>
                    </div>
                    <button type="button" data-modal-hide="modalEdit" class="text-amber-100 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="formEditUser" action="#" method="POST" class="px-8 mb-8 space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="id" id="edit-id" value="{{ old('id') }}">
                
                    <div>
                        <label class="block mb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nama Lengkap</label>
                        <input type="text" name="nama" id="edit-nama" value="{{ old('id') ? old('nama') : '' }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none text-sm font-semibold transition-all placeholder:text-slate-400" placeholder="Masukkan nama..." required>
                    </div>
                    <div>
                        <label class="block mb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Username</label>
                        <input type="text" name="username" id="edit-username" value="{{ old('id') ? old('username') : '' }}" class="w-full px-4 py-3 bg-slate-50 border @if(old('id') && $errors->has('username')) border-red-500 @else border-slate-200 @endif rounded-xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none text-sm font-semibold transition-all placeholder:text-slate-400" placeholder="Masukkan username..." required>
                        
                        @if(old('id'))
                            @error('username')
                                <p class="text-red-500 text-xs mt-1 font-semibold"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                    {{-- Tambah Field Email pada Edit --}}
                    <div>
                        <label class="block mb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Email</label>
                        <input type="email" name="email" id="edit-email" value="{{ old('id') ? old('email') : '' }}" class="w-full px-4 py-3 bg-slate-50 border @if(old('id') && $errors->has('email')) border-red-500 @else border-slate-200 @endif rounded-xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none text-sm font-semibold transition-all placeholder:text-slate-400" placeholder="Masukkan email..." required>
                        
                        @if(old('id'))
                            @error('email')
                                <p class="text-red-500 text-xs mt-1 font-semibold"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                    <div>
                        <label class="block mb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Password Baru (Opsional)</label>
                        <input type="password" name="password" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none text-sm font-semibold transition-all placeholder:text-slate-400" placeholder="Kosongkan jika tidak diubah">
                    </div>
                    <div>
                        <label class="block mb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Jabatan</label>
                        <select name="role" id="edit-role" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none text-sm font-semibold transition-all text-slate-700">
                            <option value="admin_gudang" {{ (old('id') && old('role') == 'admin_gudang') ? 'selected' : '' }}>Admin Gudang</option>
                            <option value="manajer" {{ (old('id') && old('role') == 'manajer') ? 'selected' : '' }}>Manajer</option>
                        </select>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button data-modal-hide="modalEdit" type="button" class="flex-1 py-3 bg-slate-100 text-slate-500 rounded-xl font-bold">Batal</button>
                        <button type="submit" class="flex-1 py-3 bg-amber-500 text-white rounded-xl font-bold shadow-lg shadow-amber-200">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal Edit -->

    <!-- Modal Delete -->
    <div id="modalDelete" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div id="closeDeleteOverlay" class="fixed inset-0 bg-red-900/20 backdrop-blur-sm transition-opacity"></div>
        <div class="animate-modal relative bg-white w-full max-w-sm rounded-[2.5rem] shadow-2xl overflow-hidden text-center border-4 border-red-50">
            <div class="p-10">
                <div class="relative w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <div class="absolute inset-0 rounded-full bg-red-100 animate-ping opacity-75"></div>
                    <i class="fas fa-trash-alt text-4xl text-red-600 relative"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-800 mb-3">Hapus Data?</h3>
                <p class="text-slate-500 text-sm mb-8">Data akan dihapus secara permanen dari database.</p>
            
                <form id="formDeleteUser" method="POST" class="space-y-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-4 bg-red-600 text-white rounded-2xl font-bold hover:bg-red-700 hover:scale-[1.02] transition-all shadow-lg shadow-red-200">
                        Ya, Hapus Sekarang
                    </button>
                    <button type="button" data-modal-hide="modalDelete" class="w-full py-4 bg-white text-slate-400 rounded-2xl font-bold hover:text-slate-600 transition-all">
                        Batal
                    </button>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal Delete -->

    @include('layout.partials.aos-scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        function openEditModal(id, nama, username, email, role) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nama').value = nama;
            document.getElementById('edit-username').value = username;
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-role').value = role.toLowerCase();
            document.getElementById('formEditUser').action = `/admin/pengguna/${id}`;
        }

        function openDeleteModal(id) {
            document.getElementById('formDeleteUser').action = `/admin/pengguna/${id}`;
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

        document.addEventListener("DOMContentLoaded", function() {
            @if($errors->any())
                @if(old('_method') == 'PUT')
                    const modalEdit = document.getElementById('modalEdit');
                    if(modalEdit) {
                        modalEdit.classList.remove('hidden');
                        modalEdit.classList.add('flex');
                    }
                    
                    const editId = "{{ old('id') }}";
                    if(editId) {
                        document.getElementById('formEditUser').action = `/admin/pengguna/${editId}`;
                    }
                @else
                    const modalTambah = document.getElementById('modalTambahUser');
                    modalTambah.classList.remove('hidden');
                    modalTambah.classList.add('flex');
                @endif
            @endif

        });
    </script>
    @include('layout.partials.ajax-list-search-init', [
        'indexUrl' => route('admin.pengguna.index'),
        'searchInputId' => 'search-input',
        'filterSelectId' => 'filterJabatan',
        'filterParam' => 'role',
    ])
</body>
</html>