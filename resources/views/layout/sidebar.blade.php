<aside id="sidebar-multi-level-sidebar" 
       data-drawer-backdrop="false" 
       class="fixed top-0 left-0 z-40 w-72 overflow-hidden transition-transform -translate-x-full md:translate-x-0 bg-slate-900 text-white flex flex-col shadow-2xl" 
       style="height: 100vh; height: 100dvh; max-height: 100vh; max-height: 100dvh;"
       aria-label="Sidebar">        <div class="p-4 flex items-center gap-2.5 border-b border-slate-700">
            <div class="bg-blue-600 p-1.5 rounded-lg">
                <i class="fas fa-laptop-code text-white text-sm"></i>
            </div>
            <h1 class="text-lg font-bold tracking-tight">Inviniux</h1>
            <button data-drawer-hide="sidebar-multi-level-sidebar" aria-controls="sidebar-multi-level-sidebar" class="md:hidden ml-auto text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
    
        <nav class="sidebar-nav flex-1 min-h-0 px-3 py-5 space-y-1 overflow-y-auto">
            
            {{-- Dashboard --}}
            <a href="{{ route('dashboardadmin') }}" 
               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all duration-300 text-sm {{ request()->routeIs('dashboardadmin') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:text-white glass-hover' }}">
                <i class="fas fa-tachometer-alt w-4 text-center"></i>
                <span class="font-medium">Dashboard</span>
            </a>
            
            <div class="text-[9px] uppercase text-slate-500 font-bold pt-5 pb-2 px-3 tracking-widest">Master Data</div>
            
            {{-- Data Kategori --}}
            <a href="{{ route('admin.kategori.index') }}" 
               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all group text-sm {{ request()->routeIs('admin.kategori.index') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white glass-hover' }}">
                <i class="fas fa-tags w-4 text-center group-hover:scale-110 transition-transform"></i>
                <span>Data Kategori</span>
            </a>
    
            {{-- Data Barang --}}
            <a href="{{ route('brgadmin') }}" 
               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all group text-sm {{ request()->routeIs('brgadmin') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white glass-hover' }}">
                <i class="fas fa-box w-4 text-center group-hover:scale-110 transition-transform"></i>
                <span>Data Barang</span>
            </a>
    
            {{-- Data Supplier --}}
            <a href="{{ route('admin.supplier.index') }}" 
               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all group text-sm {{ request()->routeIs('admin.supplier.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white glass-hover' }}">
                <i class="fas fa-truck w-4 text-center group-hover:scale-110 transition-transform"></i>
                <span>Data Supplier</span>
            </a>
    
            <!-- {{-- Data Pengguna --}}
            <a href="{{ route('admin.pengguna.index') }}" 
               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all group text-sm {{ request()->routeIs('admin.pengguna.index') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white glass-hover' }}">
                <i class="fas fa-users w-4 text-center group-hover:scale-110 transition-transform"></i>
                <span>Data Pengguna</span>
            </a> -->
    
            <div class="text-[9px] uppercase text-slate-500 font-bold pt-5 pb-2 px-3 tracking-widest">Transaksi</div>
            
            {{-- Barang Masuk --}}
            <a href="{{ route('barang-masuk.index') }}" 
               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('barang-masuk.index') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white glass-hover' }}">
                <i class="fas fa-arrow-down-long w-4 text-center text-green-500 {{ request()->routeIs('barang-masuk.index') }}"></i>
                <span>Barang Masuk</span>
            </a>
    
            {{-- Barang Keluar --}}
            <a href="{{ route('barang-keluar.index') }}" 
               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('barang-keluar.index') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white glass-hover' }}">
                <i class="fas fa-arrow-up-long w-4 text-center text-orange-500 {{ request()->routeIs('barang-keluar.index') }}"></i>
                <span>Barang Keluar</span>
            </a>
        </nav>

        <div class="p-3 border-t border-slate-700 space-y-1 bg-slate-900">
            <a href="{{ route('settings') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition-all text-sm {{ request()->routeIs('settings') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white glass-hover' }}">
                <i class="fas fa-cog w-4 text-center"></i>
                <span class="font-medium">Pengaturan</span>
            </a>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center text-slate-400 gap-2.5 px-3 py-2.5 hover:text-white transition-all text-sm">
                <i class="fas fa-power-off w-4 text-center"></i>
                <span class="font-medium">Logout</span>
            </a>
    
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </aside>

<div id="sidebar-overlay-custom" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-30 hidden"></div>