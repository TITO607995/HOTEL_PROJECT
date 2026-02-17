<aside class="w-64 bg-[#790000] text-white p-6 flex flex-col shadow-xl h-screen sticky top-0 flex-shrink-0">
    <div class="mb-8 text-center flex-shrink-0">
        <span class="text-xl font-bold tracking-widest uppercase italic">Hotel <span class="text-red-200">SIG</span></span>
        <hr class="opacity-20 mt-2 border-white">
    </div>

    <nav class="space-y-2 flex-1 overflow-y-auto custom-scroll pr-2">
        
        <a href="{{ route('dashboard') }}" 
           class="flex items-center space-x-3 p-3 rounded-lg transition {{ request()->is('dashboard') ? 'bg-white/20 font-bold shadow-inner' : 'hover:bg-white/10' }}">
            <span class="text-lg">📊</span> 
            <span>Dashboard</span>
        </a>

        <a href="{{ route('rooms.index') }}" 
           class="flex items-center space-x-3 p-3 rounded-lg transition {{ request()->is('room*') && !request()->is('rooms/maintenance*') ? 'bg-white/20 font-bold shadow-inner' : 'hover:bg-white/10' }}">
            <span class="text-lg">🛏️</span> 
            <span>Manajemen Kamar</span>
        </a>

        <div x-data="{ open: {{ request()->is('reservasi*', 'registration*', 'check-out*') ? 'true' : 'false' }} }">
            <button type="button" 
                @click="open = !open" 
                class="w-full flex items-center justify-between p-3 rounded-lg transition {{ request()->is('reservasi*', 'registration*', 'check-out*') ? 'bg-white/20 font-bold' : 'hover:bg-white/10' }}">
                <div class="flex items-center space-x-3">
                    <span class="text-lg">📅</span> 
                    <span>Reservasi</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="open" 
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="mt-2 ml-4 space-y-1 border-l-2 border-white/10 pl-4">
                
                <a href="{{ route('reservations.index') }}" class="block p-2 text-sm {{ request()->is('reservasi*') ? 'text-white font-bold' : 'text-white/70 hover:text-white' }}">📝 Monitoring</a>
                <a href="{{ route('reservations.registration') }}" class="block p-2 text-sm {{ request()->is('registration*') ? 'text-white font-bold' : 'text-white/70 hover:text-white' }}">🔑 Check-in</a>
                <a href="{{ route('reservations.checkout.page') }}" class="block p-2 text-sm {{ request()->is('check-out*') ? 'text-white font-bold' : 'text-white/70 hover:text-white' }}">🚪 Check-out</a>
            </div>
        </div>

        <a href="{{ route('guests.index') }}" 
           class="flex items-center space-x-3 p-3 rounded-lg transition {{ request()->is('guests*') ? 'bg-white/20 font-bold shadow-inner' : 'hover:bg-white/10' }}">
            <span class="text-lg">👥</span> 
            <span>Tamu</span>
        </a>

        @if(auth()->user()->role && strtoupper(auth()->user()->role->name) === 'SUPERADMIN')
        <div class="pt-4 mt-4 border-t border-white/10">
            <p class="text-[10px] uppercase text-white/40 font-bold mb-2 ml-3 tracking-widest">Administrator</p>
            
            <a href="{{ route('rooms.maintenance.page') }}" 
                class="flex items-center space-x-3 p-3 rounded-lg transition {{ request()->is('rooms/maintenance*') ? 'bg-white/20 font-bold shadow-inner' : 'hover:bg-white/10' }}">
                 <span class="text-lg">🛠️</span> 
                 <span>Status OO/OS</span>
            </a>
            
            <a href="{{ route('employees.create') }}" 
                class="flex items-center space-x-3 p-3 rounded-lg transition {{ request()->is('employees/create*') ? 'bg-white/20 font-bold shadow-inner' : 'hover:bg-white/10' }}">
                 <span class="text-lg">👔</span> 
                 <span>Tambah Karyawan</span>
            </a>
             <a href="{{route('reports.index')}}" 
                class="flex items-center space-x-3 p-3 rounded-lg transition {{ request()->is('reports*') ? 'bg-white/20 font-bold shadow-inner' : 'hover:bg-white/10' }}">
                 <span class="text-lg">📊</span> 
                 <span>Laporan</span>
            </a>
        </div>
        @endif

    </nav>

    <div class="pt-4 border-t border-white/20 flex-shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center space-x-3 p-3 rounded-lg hover:bg-red-800 transition text-red-100 font-semibold group">
                <span class="text-lg group-hover:scale-110 transition-transform">🚪</span> 
                <span>Keluar</span>
            </button>
        </form>
        <div class="text-[10px] text-white/30 text-center mt-4 italic tracking-wide">Web by 5NYeni</div>
    </div>
</aside>