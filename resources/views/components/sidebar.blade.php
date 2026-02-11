 <div class="flex flex-1">
       <aside class="w-64 bg-[#790000] text-white p-6 flex flex-col shadow-xl">
    <div class="mb-8 text-center">
        <span class="text-xl font-bold tracking-widest">HOTEL SIG</span>
        <hr class="opacity-20 mt-2">
    </div>

    <nav class="space-y-2 flex-1">
        <a href="{{ route('dashboard') }}" 
           class="flex items-center space-x-3 p-3 rounded-lg transition {{ request()->is('dashboard') ? 'bg-white/20 font-bold' : 'hover:bg-white/10' }}">
            <span class="text-lg">📊</span> 
            <span>Dashboard</span>
        </a>

        <a href="{{ route('rooms.index') }}" 
           class="flex items-center space-x-3 p-3 rounded-lg transition {{ request()->is('room*') ? 'bg-white/20 font-bold' : 'hover:bg-white/10' }}">
            <span class="text-lg">🛏️</span> 
            <span>Manajemen Kamar</span>
        </a>

        <a href="{{ route('reservations.index') }}" 
           class="flex items-center space-x-3 p-3 rounded-lg transition {{ request()->is('reservasi*') ? 'bg-white/20 font-bold' : 'hover:bg-white/10' }}">
            <span class="text-lg">📝</span> 
            <span>Reservasi</span>
        </a>

        <a href="{{ route('reservations.registration') }}" 
           class="flex items-center space-x-3 p-3 rounded-lg transition {{ request()->is('registration*') ? 'bg-white/20 font-bold' : 'hover:bg-white/10' }}">
            <span class="text-lg">🔑</span> 
            <span>Check-in</span>
        </a>

        @can('superadmin-only')
        <div class="pt-4 mt-4 border-t border-white/20">
            <p class="text-[10px] uppercase text-white/50 font-bold mb-2 ml-3">Admin Master</p>
            
            <a href="{{ route('users.index') }}" 
               class="flex items-center space-x-3 p-3 rounded-lg transition {{ request()->is('users*') ? 'bg-white/20 font-bold' : 'hover:bg-white/10' }}">
                <span class="text-lg">👥</span> 
                <span>Kelola User</span>
            </a>

            <a href="{{ route('assign-role.index') }}" 
               class="flex items-center space-x-3 p-3 rounded-lg transition {{ request()->is('assign-role*') ? 'bg-white/20 font-bold' : 'hover:bg-white/10' }}">
                <span class="text-lg">🛡️</span> 
                <span>Assign Role</span>
            </a>
        </div>
        @endcan
    </nav>

    <div class="pt-4 border-t border-white/20">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center space-x-3 p-3 rounded-lg hover:bg-red-700 transition text-red-200">
                <span class="text-lg">🚪</span> 
                <span>Keluar</span>
            </button>
        </form>
    </div>

    <div class="text-[10px] text-white/50 text-center mt-4 italic">Web by 5NYeni</div>
</aside>
