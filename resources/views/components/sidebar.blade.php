<aside class="w-64 bg-[#790000] text-white p-6 flex flex-col shadow-2xl h-screen sticky top-0 flex-shrink-0 z-50">
    
    {{-- HEADER: LOGO (BARU) --}}
    <div class="mb-10 text-center flex-shrink-0 px-2">
        <a href="{{ route('dashboard') }}" class="group block transition-transform duration-500">
            <div class="flex flex-col items-center">
                {{-- Ikon Minimalis --}}
                <div class="mb-3 p-3 bg-white/10 rounded-2xl group-hover:bg-red-500/20 transition-colors duration-500">
                    <i class="fas fa-hotel text-2xl text-red-200"></i>
                </div>
                {{-- Font Modern Tracking Luas --}}
                <span class="text-xl font-black tracking-[0.3em] uppercase italic font-sans leading-none">
                    Hotel <span class="text-red-300">SIG</span>
                </span>
                <div class="h-[1px] w-full bg-gradient-to-r from-transparent via-white/20 to-transparent mt-4"></div>
            </div>
        </a>
    </div>

    {{-- NAVIGATION MENU --}}
    <nav class="space-y-2 flex-1 overflow-y-auto custom-scroll pr-2">
        
        {{-- 1. DASHBOARD --}}
        <a href="{{ route('dashboard') }}" 
           class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-200 
           {{ request()->routeIs('dashboard') ? 'bg-white/20 font-bold shadow-inner text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
            <span class="text-lg w-6 text-center">📊</span> 
            <span class="text-[11px] font-bold tracking-[0.1em] uppercase">Dashboard</span>
        </a>

        {{-- 2. MANAJEMEN KAMAR --}}
        <a href="{{ route('rooms.index') }}" 
           class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-200 
           {{ request()->routeIs('rooms.index') || (request()->is('rooms*') && !request()->is('rooms/maintenance*')) ? 'bg-white/20 font-bold shadow-inner text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
            <span class="text-lg w-6 text-center">🛏️</span> 
            <span class="text-[11px] font-bold tracking-[0.1em] uppercase">Data Kamar</span>
        </a>

        {{-- 3. RESERVASI (DROPDOWN) --}}
        <div x-data="{ open: {{ request()->routeIs('reservations.*') || request()->is('reservasi*', 'registration*', 'check-out*') ? 'true' : 'false' }} }">
            <button type="button" 
                @click="open = !open" 
                class="w-full flex items-center justify-between p-3 rounded-xl transition-all duration-200 group 
                {{ request()->routeIs('reservations.*') || request()->is('reservasi*', 'registration*', 'check-out*') ? 'bg-white/20 font-bold text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <div class="flex items-center space-x-3">
                    <span class="text-lg w-6 text-center">📅</span> 
                    <span class="text-[11px] font-bold tracking-[0.1em] uppercase">Reservasi</span>
                </div>
                <i class="fas fa-chevron-down text-[10px] transition-transform duration-300 opacity-70 group-hover:opacity-100" :class="open ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="open" 
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="mt-1 ml-4 space-y-1 border-l-2 border-white/10 pl-3">
                
                <a href="{{ route('reservations.index') }}" 
                   class="block p-2 rounded-lg text-[10px] font-bold tracking-widest uppercase transition-colors {{ request()->routeIs('reservations.index') ? 'text-white bg-white/10' : 'text-white/50 hover:text-white hover:bg-white/5' }}">
                    📝 Monitoring
                </a>
                <a href="{{ route('reservations.registration') }}" 
                   class="block p-2 rounded-lg text-[10px] font-bold tracking-widest uppercase transition-colors {{ request()->routeIs('reservations.registration') ? 'text-white bg-white/10' : 'text-white/50 hover:text-white hover:bg-white/5' }}">
                    🔑 Check-in
                </a>
                <a href="{{ route('reservations.checkout.page') }}" 
                   class="block p-2 rounded-lg text-[10px] font-bold tracking-widest uppercase transition-colors {{ request()->routeIs('reservations.checkout.page') ? 'text-white bg-white/10' : 'text-white/50 hover:text-white hover:bg-white/5' }}">
                    🚪 Check-out
                </a>
            </div>
        </div>

        {{-- 4. TAMU --}}
        <a href="{{ route('guests.index') }}" 
           class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-200 
           {{ request()->routeIs('guests.*') ? 'bg-white/20 font-bold shadow-inner text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
            <span class="text-lg w-6 text-center">👥</span> 
            <span class="text-[11px] font-bold tracking-[0.1em] uppercase">Buku Tamu</span>
        </a>

        {{-- 5. ABOUT --}}
        <a href="{{ route('about.about') }}" 
           class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-200 
           {{ request()->routeIs('about.*') ? 'bg-white/20 font-bold shadow-inner text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
            <span class="text-lg w-6 text-center">ℹ️</span> 
            <span class="text-[11px] font-bold tracking-[0.1em] uppercase">About</span>
        </a>

        {{-- PEMBATAS SECTION ADMIN --}}
        @if(auth()->check() && auth()->user()->role && (auth()->user()->role->name === 'Superadmin' || auth()->user()->role->name === 'SUPERADMIN'))
        
        <div class="pt-6 mt-2">
            <p class="px-3 text-[9px] uppercase text-white/40 font-black mb-3 tracking-[0.2em]">Management</p>
            <div class="border-t border-white/10 mb-3 mx-2"></div>
            
            <a href="{{ route('rooms.maintenance') }}" 
               class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-200 
               {{ request()->routeIs('rooms.maintenance.*') ? 'bg-white/20 font-bold shadow-inner text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                 <span class="text-lg w-6 text-center">🛠️</span> 
                 <span class="text-[11px] font-bold tracking-[0.1em] uppercase">Status OO/OS</span>
            </a>
            
            <div x-data="{ open: {{ request()->routeIs('employees.*') || request()->routeIs('roles.*') ? 'true' : 'false' }} }">
                <button type="button" 
                    @click="open = !open" 
                    class="w-full flex items-center justify-between p-3 rounded-xl transition-all duration-200 group 
                    {{ request()->routeIs('employees.*') || request()->routeIs('roles.*') ? 'bg-white/20 font-bold text-white shadow-inner' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    
                    <div class="flex items-center space-x-3">
                        <span class="text-lg w-6 text-center">👥</span> 
                        <span class="text-[11px] font-bold tracking-[0.1em] uppercase">Users</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] transition-transform duration-300 opacity-70 group-hover:opacity-100" :class="open ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="open" 
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="mt-1 ml-4 space-y-1 border-l-2 border-white/10 pl-3">
                    
                    <a href="{{ route('employees.index') }}" 
                       class="block p-2 rounded-lg text-[10px] font-bold tracking-widest uppercase transition-colors {{ request()->routeIs('employees.*') ? 'text-white bg-white/10' : 'text-white/50 hover:text-white hover:bg-white/5' }}">
                        👔 Karyawan
                    </a>
                    <a href="{{ route('roles.index') }}" 
                       class="block p-2 rounded-lg text-[10px] font-bold tracking-widest uppercase transition-colors {{ request()->routeIs('roles.*') ? 'text-white bg-white/10' : 'text-white/50 hover:text-white hover:bg-white/5' }}">
                        🔐 Role & Akses
                    </a>
                </div>
            </div>

            <div x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
                <button type="button" 
                    @click="open = !open" 
                    class="w-full flex items-center justify-between p-3 rounded-xl transition-all duration-200 group 
                    {{ request()->routeIs('reports.*') ? 'bg-white/20 font-bold text-white shadow-inner' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    
                    <div class="flex items-center space-x-3">
                        <span class="text-lg w-6 text-center">📈</span> 
                        <span class="text-[11px] font-bold tracking-[0.1em] uppercase">Laporan</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] transition-transform duration-300 opacity-70 group-hover:opacity-100" :class="open ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="open" 
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="mt-1 ml-4 space-y-1 border-l-2 border-white/10 pl-3">
                    
                    <a href="{{ route('reports.index') }}" 
                       class="block p-2 rounded-lg text-[10px] font-bold tracking-widest uppercase transition-colors {{ request()->routeIs('reports.index') ? 'text-white bg-white/10' : 'text-white/50 hover:text-white hover:bg-white/5' }}">
                        📊 Operasional
                    </a>
                    <a href="{{ route('reports.financial') }}" 
                       class="block p-2 rounded-lg text-[10px] font-bold tracking-widest uppercase transition-colors {{ request()->routeIs('reports.financial') ? 'text-white bg-white/10' : 'text-white/50 hover:text-white hover:bg-white/5' }}">
                        💰 Keuangan
                    </a>
                </div>
            </div>
        </div>
        @endif

        {{-- 6. DEVICE MONITORING --}}
        <a href="{{ route('admin.devices') }}" 
           class="flex items-center space-x-3 p-3 rounded-xl transition-all duration-200 
           {{ request()->routeIs('admin.devices') ? 'bg-white/20 font-bold shadow-inner text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
            <span class="text-lg w-6 text-center">📱</span> 
            <span class="text-[11px] font-bold tracking-[0.1em] uppercase">Device Monitor</span>
        </a>
    </nav>

    {{-- FOOTER --}}
    <div class="pt-4 border-t border-white/20 flex-shrink-0 mt-2">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center space-x-3 p-3 rounded-xl hover:bg-red-900 transition-colors text-red-100 font-bold uppercase tracking-widest text-[10px] group">
                <span class="text-lg w-6 text-center group-hover:-translate-x-1 transition-transform">🚪</span> 
                <span>Keluar Sistem</span>
            </button>
        </form>
        <div class="text-[8px] text-white/30 text-center mt-4 font-bold tracking-[0.2em] uppercase">
            Hotel SIG Management v2.1
        </div>
    </div>
</aside>