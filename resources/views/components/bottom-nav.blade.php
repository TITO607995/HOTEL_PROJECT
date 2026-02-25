<nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-xl border-t border-gray-100 px-6 py-3 z-[100] safe-bottom flex justify-between items-center shadow-[0_-10px_25px_-5px_rgba(0,0,0,0.05)]">
    
    <a href="{{ route('rooms.index') }}" class="flex flex-col items-center gap-1 transition-all {{ request()->is('rooms*') ? 'text-[#800000]' : 'text-gray-400' }}">
        <div class="relative">
            <i class="fas fa-bed text-xl"></i>
            @if(request()->is('rooms*'))
                <span class="absolute -top-1 -right-1 flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-[#800000]"></span>
                </span>
            @endif
        </div>
        <span class="text-[9px] font-bold uppercase tracking-tighter">Rooms</span>
    </a>

    <a href="{{ route('reservations.index') }}" class="flex flex-col items-center gap-1 transition-all {{ request()->is('reservasi') || request()->routeIs('reservations.index') ? 'text-[#800000]' : 'text-gray-400' }}">
        <i class="fas fa-clipboard-list text-xl"></i>
        <span class="text-[9px] font-bold uppercase tracking-tighter">Orders</span>
    </a>
    
    <div class="relative -mt-14">
        <div class="absolute inset-0 -m-2 bg-[#F8FAFC] rounded-full border-t border-gray-100"></div>
        
        <a href="{{ route('dashboard') }}" 
           class="relative w-14 h-14 rounded-2xl shadow-xl flex items-center justify-center transition-all active:scale-90 border-4 border-white
                  {{ request()->routeIs('dashboard') ? 'bg-[#800000] text-white shadow-red-900/30' : 'bg-white text-gray-400 shadow-gray-200' }}">
            <i class="fas fa-th-large text-xl"></i>
        </a>
        
        <div class="text-center mt-1">
            <span class="text-[9px] font-black uppercase tracking-tighter {{ request()->routeIs('dashboard') ? 'text-[#800000]' : 'text-gray-400' }}">Dash</span>
        </div>
    </div>

    <a href="{{ route('reservations.checkout.page') }}" class="flex flex-col items-center gap-1 transition-all {{ request()->routeIs('reservations.checkout.page') ? 'text-[#800000]' : 'text-gray-400' }}">
        <i class="fas fa-sign-out-alt text-xl"></i>
        <span class="text-[9px] font-bold uppercase tracking-tighter">Out</span>
    </a>
    
    <button @click="mobileMenu = true" class="flex flex-col items-center gap-1 text-gray-400 active:text-[#800000] bg-transparent border-none outline-none">
        <i class="fas fa-bars text-xl"></i>
        <span class="text-[9px] font-bold uppercase tracking-tighter">Menu</span>
    </button>
</nav>