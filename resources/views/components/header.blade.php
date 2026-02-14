<header class="bg-[#F0E7E7]/90 backdrop-blur-md sticky top-0 z-[60] px-8 py-3 flex justify-between items-center shadow-sm border-b border-pink-200 no-print">
    <div class="flex items-center space-x-6">
        <div class="flex items-center space-x-3 bg-white/60 p-1.5 rounded-xl border border-pink-100 shadow-inner">
            <img src="{{ asset('image/logoph.png') }}" alt="Logo Hotel" class="h-10 w-auto object-contain select-none">
            <div class="h-6 w-[1.5px] bg-pink-200 rounded-full"></div>
            <img src="{{ asset('image/smksig.png') }}" alt="Logo SMK" class="h-10 w-auto object-contain select-none">
        </div>
        
        <div class="flex flex-col">
            <span class="text-[10px] font-black text-pink-500 uppercase tracking-[0.2em] leading-none mb-1">
                {{ Auth::user()->role->name ?? 'SUPERADMIN' }}
            </span>
            <h1 class="text-lg font-bold text-gray-700 tracking-tight leading-tight">
                Selamat Datang, <span class="text-pink-600">{{ Auth::user()->name ?? 'Guest' }}</span> 
                <span class="inline-block animate-bounce origin-bottom">👋</span>
            </h1>
        </div>
    </div>

    <div class="flex items-center space-x-8">
        <div class="hidden lg:flex flex-col items-end border-r-2 border-pink-100 pr-8">
            <div id="current-time" class="text-2xl font-black text-gray-700 tracking-tighter tabular-nums leading-none">
                00:00:00
            </div>
            <div id="current-date" class="text-[10px] font-bold text-pink-400 uppercase tracking-widest mt-1">
                Memuat tanggal...
            </div>
        </div>

        <div class="flex items-center space-x-4 group cursor-pointer relative">
            <div class="text-right hidden sm:block">
                <p class="text-xs font-black text-gray-600 group-hover:text-pink-600 transition-colors leading-none mb-1">
                    {{ Auth::user()->email ?? 'online' }}
                </p>
                <div class="flex items-center justify-end gap-1.5">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-pink-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-pink-500"></span>
                    </span>
                    <span class="text-[10px] text-pink-600 font-bold uppercase tracking-wider">Aktif</span>
                </div>
            </div>

            <div class="relative">
                <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-pink-400 to-pink-300 flex items-center justify-center text-white font-black shadow-lg shadow-pink-200 group-hover:scale-105 transition-all duration-300 ring-2 ring-white">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    function updateClock() {
        const timeEl = document.getElementById('current-time');
        const dateEl = document.getElementById('current-date');
        if (!timeEl || !dateEl) return;

        const now = new Date();
        timeEl.textContent = now.toLocaleTimeString('id-ID', { 
            hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false 
        }).replace(/\./g, ':');
        
        dateEl.textContent = now.toLocaleDateString('id-ID', { 
            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
        });
    }
    setInterval(updateClock, 1000);
    document.addEventListener('DOMContentLoaded', updateClock);
</script>