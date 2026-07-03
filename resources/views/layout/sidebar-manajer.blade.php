<aside id="sidebar-multi-level-sidebar" 
       data-drawer-backdrop="false" 
       class="fixed top-0 left-0 z-40 w-72 h-screen max-h-screen overflow-hidden transition-transform -translate-x-full md:translate-x-0 bg-slate-900 text-white flex flex-col shadow-2xl" 
       aria-label="Sidebar">        <div class="p-6 flex items-center gap-3 border-b border-slate-700">
            <div class="bg-blue-600 p-2 rounded-lg">
                <i class="fas fa-laptop-code text-white"></i>
            </div>
            <h1 class="text-xl font-bold tracking-tight">Inviniux</h1>
            <button data-drawer-hide="sidebar-multi-level-sidebar" aria-controls="sidebar-multi-level-sidebar" class="md:hidden ml-auto text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
    
        <nav class="sidebar-nav flex-1 min-h-0 px-4 py-6 space-y-1 overflow-y-auto">
            
            {{-- Dashboard --}}
            <a href="{{ route('dashboardmanajer') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('dashboardmanajer') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:text-white glass-hover' }}">
                <i class="fas fa-tachometer-alt w-5 text-center"></i>
                <span class="font-medium">Dashboard</span>
            </a>
            
            <div class="text-[10px] uppercase text-slate-500 font-bold pt-6 pb-2 px-4 tracking-widest">Master Data</div>
    
            {{-- Data Barang --}}
            <a href="{{ route('brgmanajer') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('brgmanajer') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white glass-hover' }}">
                <i class="fas fa-box w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Data Barang</span>
            </a>
        </nav>

        <div class="p-4 border-t border-slate-700 space-y-1 bg-slate-900">
            <a href="{{ route('settings') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->routeIs('settings') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white glass-hover' }}">
                <i class="fas fa-cog w-5 text-center"></i>
                <span class="font-medium">Pengaturan</span>
            </a>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center text-slate-400 gap-3 px-4 py-2.5 hover:text-white transition-all">
                <i class="fas fa-power-off w-5 text-center"></i>
                <span class="font-medium">Logout</span>
            </a>
    
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </aside>

<div id="sidebar-overlay-custom" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-30 hidden"></div>