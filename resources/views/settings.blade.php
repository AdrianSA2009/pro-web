<!DOCTYPE html>
<html lang="id">
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengaturan - Inviniux</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('layout.partials.aos-head')
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-nav::-webkit-scrollbar { width: 0px; background: transparent; }
    </style>
</head>
<body class="bg-slate-50 text-gray-800 flex min-h-screen">

    @if(auth()->user()->role === 'manajer')
        @include('layout.sidebar-manajer')
    @else
        @include('layout.sidebar')
    @endif
    <div class="flex-1 flex flex-col w-full md:ml-72 transition-all duration-300">
        <header class="bg-white/80 backdrop-blur-md sticky top-0 z-10 flex items-center justify-between px-8 py-4 border-b border-slate-100">
            <div class="flex items-center gap-4">
                <button data-drawer-target="sidebar-multi-level-sidebar" data-drawer-toggle="sidebar-multi-level-sidebar" aria-controls="sidebar-multi-level-sidebar" type="button" class="md:hidden p-2 rounded-lg hover:bg-slate-100 transition-colors">
                    <i class="fas fa-bars text-xl text-slate-600"></i>
                </button>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight">Pengaturan</h2>
            </div>
            @include('layout.partials.topbar-profile')
        </header>

        <main class="flex-1 p-6 md:p-8 space-y-8 bg-slate-50">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4" data-aos="fade-down">
                <div>
                    <nav class="flex text-sm text-slate-500 mb-2">
                        <span class="text-slate-900 font-medium">Pengaturan Akun</span>
                    </nav>
                    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Kelola Profil & Keamanan</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Profil -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                        <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Informasi Profil</h3>
                            <p class="text-xs text-slate-400">Ubah nama dan email akun Anda</p>
                        </div>
                    </div>
                    <form id="formProfile" class="p-6 space-y-5">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Username</label>
                            <input type="text" name="username" id="profileUsername" value="{{ $user->username }}" placeholder="Masukkan Username" 
                                class="placeholder:text-slate-400 w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none text-sm font-semibold transition-all">
                            <span id="errorUsername" class="text-red-500 text-xs mt-1 hidden block"></span>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Nama Lengkap</label>
                            <input type="text" name="nama" id="profileNama" value="{{ $user->nama }}" placeholder="Masukkan Nama Anda"
                                class="placeholder:text-slate-400 w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none text-sm font-semibold transition-all">
                            <span id="errorNama" class="text-red-500 text-xs mt-1 hidden block"></span>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Email</label>
                            <input type="email" name="email" id="profileEmail" value="{{ $user->email }}" placeholder="Masukkan Email Anda"
                                class="placeholder:text-slate-400 w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none text-sm font-semibold transition-all">
                            <span id="errorEmail" class="text-red-500 text-xs mt-1 hidden block"></span>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" id="btnSaveProfile"
                                class="px-8 py-2.5 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all text-sm shadow-lg shadow-blue-200">
                                Simpan Profil
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Password -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden" data-aos="fade-up" data-aos-delay="200">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                        <div class="w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-shield-alt text-amber-600"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Keamanan</h3>
                            <p class="text-xs text-slate-400">Ubah password akun Anda</p>
                        </div>
                    </div>
                    <form id="formPassword" class="p-6 space-y-5">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Password Lama</label>
                            <div class="relative">
                                <input type="password" name="current_password" id="currentPassword" placeholder="Masukkan Password Lama"
                                    class="placeholder:text-slate-400 w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none text-sm font-semibold transition-all pr-11" required>
                                <button type="button" onclick="togglePw('currentPassword', this)" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <span id="errorCurrentPassword" class="text-red-500 text-xs mt-1 hidden block"></span>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Password Baru</label>
                            <div class="relative">
                                <input type="password" name="password" id="newPassword" placeholder="Masukkan Password Baru"
                                    class="placeholder:text-slate-400 w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none text-sm font-semibold transition-all pr-11" required>
                                <button type="button" onclick="togglePw('newPassword', this)" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <span id="errorPassword" class="text-red-500 text-xs mt-1 hidden block"></span>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Konfirmasi Password Baru</label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="confirmPassword" placeholder="Masukkan Konfirmasi Password Baru"
                                    class="placeholder:text-slate-400 w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none text-sm font-semibold transition-all pr-11" required>
                                <button type="button" onclick="togglePw('confirmPassword', this)" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <span id="errorPasswordConfirmation" class="text-red-500 text-xs mt-1 hidden block"></span>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" id="btnSavePassword"
                                class="px-8 py-2.5 bg-amber-500 text-white rounded-xl font-bold hover:bg-amber-600 transition-all text-sm shadow-lg shadow-amber-200">
                                Ubah Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    @include('layout.partials.aos-scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        
        (function () {
            const scrollContainer = document.querySelector('main');
            if (scrollContainer) {
                scrollContainer.addEventListener('scroll', function () {
                    window.dispatchEvent(new Event('scroll'));
                }, { passive: true });
            }
            // Also force a refresh shortly after load in case content is
            // already fully visible without any scroll happening at all.
            window.addEventListener('load', function () {
                if (window.AOS) {
                    setTimeout(() => AOS.refresh(), 200);
                }
            });
        })();

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        function togglePw(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        function showFieldError(id, msg) {
            const el = document.getElementById(id);
            if (!el) return;
            el.textContent = msg;
            el.classList.remove('hidden');
            el.classList.add('block');
        }

        function clearFieldError(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.textContent = '';
            el.classList.add('hidden');
            el.classList.remove('block');
        }

        function clearAllErrors(ids) {
            ids.forEach(clearFieldError);
        }

        // Profile form
        document.getElementById('formProfile').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearAllErrors(['errorUsername', 'errorNama', 'errorEmail']);

            const btn = document.getElementById('btnSaveProfile');
            const orig = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Menyimpan...';

            try {
                const res = await fetch('{{ route('settings.profile') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: new FormData(this),
                });
                const data = await res.json();

                if (res.ok && data.success) {
                    Toast.fire({ icon: 'success', title: data.message });
                    setTimeout(() => location.reload(), 800);
                } else if (data.errors) {
                    if (data.errors.username) showFieldError('errorUsername', data.errors.username[0]);
                    if (data.errors.nama) showFieldError('errorNama', data.errors.nama[0]);
                    if (data.errors.email) showFieldError('errorEmail', data.errors.email[0]);
                }
            } catch (err) {
                Toast.fire({ icon: 'error', title: 'Terjadi kesalahan.' });
            } finally {
                btn.disabled = false;
                btn.textContent = orig;
            }
        });

        // Password form
        document.getElementById('formPassword').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearAllErrors(['errorCurrentPassword', 'errorPassword', 'errorPasswordConfirmation']);

            const btn = document.getElementById('btnSavePassword');
            const orig = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Menyimpan...';

            try {
                const res = await fetch('{{ route('settings.password') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: new FormData(this),
                });
                const data = await res.json();

                if (res.ok && data.success) {
                    Toast.fire({ icon: 'success', title: data.message });
                    this.reset();
                } else if (data.errors) {
                    if (data.errors.current_password) showFieldError('errorCurrentPassword', data.errors.current_password[0]);
                    if (data.errors.password) showFieldError('errorPassword', data.errors.password[0]);
                    if (data.errors.password_confirmation) showFieldError('errorPasswordConfirmation', data.errors.password_confirmation[0]);
                }
            } catch (err) {
                Toast.fire({ icon: 'error', title: 'Terjadi kesalahan.' });
            } finally {
                btn.disabled = false;
                btn.textContent = orig;
            }
        });

        // Clear error on input
        ['profileUsername', 'profileNama', 'profileEmail', 'currentPassword', 'newPassword', 'confirmPassword'].forEach(function(id) {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', function() {
                    const errorMap = {
                        profileUsername: 'errorUsername',
                        profileNama: 'errorNama',
                        profileEmail: 'errorEmail',
                        currentPassword: 'errorCurrentPassword',
                        newPassword: 'errorPassword',
                        confirmPassword: 'errorPasswordConfirmation',
                    };
                    clearFieldError(errorMap[id]);
                });
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
    </script>
</body>
</html>