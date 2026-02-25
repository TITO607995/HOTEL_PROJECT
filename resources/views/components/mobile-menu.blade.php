<div x-cloak>
    <div x-show="mobileMenu" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         @click="mobileMenu = false"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[110] lg:hidden"></div>

    <div x-show="mobileMenu"
         x-transition:enter="transition transform duration-300 cubic-bezier(0.4, 0, 0.2, 1)"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition transform duration-300 ease-in"
         class="fixed bottom-0 left-0 right-0 bg-[#F8FAFC] rounded-t-[3rem] z-[120] p-8 lg:hidden safe-bottom shadow-[0_-20px_50px_rgba(0,0,0,0.2)]">
        
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-8" @click="mobileMenu = false"></div>
        
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-black text-gray-800 tracking-tighter">Navigasi Utama</h2>
                <p class="text-[10px] font-bold text-[#800000] uppercase tracking-[0.2em]">Hotel SIG System</p>
            </div>
        </div>
        
        <div class="grid grid-cols-3 gap-4 mb-8">
            <a href="{{ route('employees.index') }}" class="flex flex-col items-center p-5 bg-white rounded-[2rem] border border-gray-100 shadow-sm active:scale-95 transition-all">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-2"><i class="fas fa-users"></i></div>
                <span class="text-[9px] font-black uppercase tracking-tighter">Staff</span>
            </a>

            <a href="{{ route('reports.index') }}" class="flex flex-col items-center p-5 bg-white rounded-[2rem] border border-gray-100 shadow-sm active:scale-95 transition-all">
                <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center mb-2"><i class="fas fa-chart-line"></i></div>
                <span class="text-[9px] font-black uppercase tracking-tighter">Laporan</span>
            </a>

            <a href="{{ route('profile.edit') }}" class="flex flex-col items-center p-5 bg-white rounded-[2rem] border border-gray-100 shadow-sm active:scale-95 transition-all">
                <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center mb-2"><i class="fas fa-user-cog"></i></div>
                <span class="text-[9px] font-black uppercase tracking-tighter">Profil</span>
            </a>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full p-4 bg-white border border-red-100 text-red-600 rounded-[2rem] font-black uppercase text-[10px] tracking-[0.2em] flex items-center justify-center gap-3 shadow-sm active:bg-red-50 transition-all">
                <div class="w-8 h-8 bg-red-500 text-white rounded-xl flex items-center justify-center"><i class="fas fa-power-off text-xs"></i></div>
                Keluar Sistem
            </button>
        </form>
    </div>
</div>