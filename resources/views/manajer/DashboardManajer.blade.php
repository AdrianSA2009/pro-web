<!DOCTYPE html>
<html lang="id">
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Manajer</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
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
    </style>
</head>
<body class="bg-slate-50 text-gray-800 flex min-h-screen">

    <!-- Sidebar -->
    @include('layout.sidebar-manajer')
    <!-- End Sidebar -->

    <div class="flex-1 flex flex-col w-full md:ml-72 transition-all duration-300">
        <!-- Top Navbar -->
        <header class="bg-white/80 backdrop-blur-md sticky top-0 z-10 flex items-center justify-between px-8 py-4 border-b border-slate-100">
            <div class="flex items-center gap-4">
                <button data-drawer-target="sidebar-multi-level-sidebar" data-drawer-toggle="sidebar-multi-level-sidebar" aria-controls="sidebar-multi-level-sidebar" type="button" class="md:hidden p-2 rounded-lg hover:bg-slate-100 transition-colors">
                    <i class="fas fa-bars text-xl text-slate-600"></i>
                </button>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight">Dashboard Overview</h2>
            </div>
            @include('layout.partials.topbar-profile')
        </header>
        <!-- End Top Navbar -->

        <!-- Main Content -->
        <main class="flex-1 p-6 md:p-8 space-y-8 bg-slate-50">
            <!-- Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div data-aos="fade-up" data-aos-delay="100" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                    <p class="text-sm text-slate-500 font-medium">Total Barang</p>
                    <p class="text-3xl font-black text-slate-800">{{ $totalBarang }}</p>
                </div>

                <div data-aos="fade-up" data-aos-delay="200" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center text-xl group-hover:bg-green-600 group-hover:text-white transition-all duration-300">
                            <i class="fas fa-arrow-trend-down"></i>
                        </div>
                    </div>
                    <p class="text-sm text-slate-500 font-medium">Barang Masuk</p>
                    <p class="text-3xl font-black text-slate-800">{{ $totalBarangMasuk }}</p>
                </div>

                <div data-aos="fade-up" data-aos-delay="300" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl group-hover:bg-orange-600 group-hover:text-white transition-all duration-300">
                            <i class="fas fa-arrow-trend-up"></i>
                        </div>
                    </div>
                    <p class="text-sm text-slate-500 font-medium">Barang Keluar</p>
                    <p class="text-3xl font-black text-slate-800">{{ $totalBarangKeluar }}</p>
                </div>

                <div data-aos="fade-up" data-aos-delay="400" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl group-hover:bg-purple-600 group-hover:text-white transition-all duration-300">
                            <i class="fas fa-truck-fast"></i>
                        </div>
                    </div>
                    <p class="text-sm text-slate-500 font-medium">Total Supplier</p>
                    <p class="text-3xl font-black text-slate-800">{{ $totalSupplier }}</p>
                </div>
            </div>
            <!-- End Cards -->

            <!-- Aktivitas Terakhir -->
            <div data-aos="fade-up" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50">
                    <h3 class="text-lg font-bold text-slate-800">Aktivitas Terakhir</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-400 text-[10px] uppercase font-bold tracking-widest">
                            <tr>
                                <th class="px-6 py-4">Kode Transaksi</th>
                                <th class="px-6 py-4">Nama Supplier/Penerima</th>
                                <th class="px-6 py-4">Tipe Transaksi</th>
                                <th class="px-6 py-4">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($aktivitasTerakhir as $aktivitas)
                                <tr class="hover-row">
                                    <td class="px-6 py-4 font-mono text-xs text-blue-600 font-bold">
                                        {{ $aktivitas['kode'] }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-slate-700">
                                        {{ $aktivitas['penerima'] }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $aktivitas['tipe'] === 'Barang Masuk' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                            {{ $aktivitas['tipe'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-500">
                                        {{ $aktivitas['tanggal'] }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400 italic">
                                        Belum ada aktivitas transaksi terbaru.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- End Aktivitas Terakhir -->
        </main>
        <!-- End Main Content -->
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            easing: 'ease-in-out'
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