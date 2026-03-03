<!DOCTYPE html>
<html lang="id" x-data="{ mobileMenu: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#800000">
    <link rel="manifest" href="/manifest.json">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>Dashboard - Hotel SIG</title>

    <style>
        [x-cloak] { display: none !important; }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            overflow-x: hidden; 
            overscroll-behavior-y: contain; 
        }
        
        @media (min-width: 1024px) {
            .custom-scrollbar::-webkit-scrollbar { width: 6px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: #F0E7E7; border-radius: 10px; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #800000; border-radius: 10px; }
        }

        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-blur: 10px; border: 1px solid rgba(240, 231, 231, 1); }
        .safe-top { padding-top: env(safe-area-inset-top); }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom); }
        
        /* Custom Pagination Styling */
        .pagination-container nav div:first-child { display: none; }
        .pagination-container svg { width: 20px; height: 20px; }
        .pagination-container span[aria-current="page"] span { 
            background-color: #800000 !important; 
            color: white !important; 
            border-color: #800000 !important;
        }
        .pagination-container a, .pagination-container span {
            border-radius: 10px !important;
            font-weight: 700;
            font-size: 12px;
            transition: all 0.2s;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col mb-24 lg:mb-0" :class="mobileMenu ? 'overflow-hidden' : ''">

    <div class="relative z-[60] safe-top">
        <x-header></x-header>
    </div>

    <div class="flex flex-1">
        <aside id="sidebar" class="hidden lg:block w-72 bg-white flex-shrink-0 border-r border-gray-100 h-screen sticky top-0">
            <x-sidebar></x-sidebar>
        </aside>

        <main class="flex-1 p-4 md:p-8 lg:p-10 overflow-y-auto">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-10 text-balance">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="h-1 w-8 bg-pink-400 rounded-full"></span>
                        <span class="text-[10px] font-black text-pink-500 uppercase tracking-[0.3em]">Overview Panel</span>
                    </div>
                    <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-800 tracking-tight">
                        Dashboard <span class="text-[#800000]">Hotel SIG</span>
                    </h1>
                    <p class="text-gray-400 text-sm mt-1 font-medium italic">Status real-time ketersediaan kamar.</p>
                </div>

                <div class="bg-[#F0E7E7] px-6 py-4 rounded-3xl border border-pink-200 flex items-center gap-4 shadow-sm min-w-[240px] w-full md:w-auto">
                    <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center shadow-sm text-pink-500">
                        <i class="fas fa-satellite-dish animate-pulse"></i>
                    </div>
                    <div>
                        <span id="current-time-dash" class="block font-black text-gray-800 text-xl leading-none tracking-tighter">00:00:00</span>
                        <span id="current-date-dash" class="text-[9px] font-bold text-pink-600 uppercase tracking-widest mt-1 block tracking-tighter">Memuat...</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                @foreach($stats as $nama => $jumlah)
                <div class="group relative overflow-hidden bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-500">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-[#F0E7E7] rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10">
                        <h3 class="text-gray-400 font-bold text-[10px] uppercase tracking-[0.2em] mb-4">Unit Kamar</h3>
                        <div class="flex items-baseline gap-2">
                            <span class="text-6xl font-black text-gray-800 tracking-tighter group-hover:text-[#800000] transition-colors">{{ $jumlah }}</span>
                            <span class="text-xs font-bold text-gray-400 uppercase">{{ $nama }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden mb-10">
                <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-gradient-to-r from-white to-gray-50">
                    <h2 class="text-xl font-extrabold text-gray-800 tracking-tight">Kamar Terisi & Maintenance</h2>
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Live</span>
                    </span>
                </div>

                <div class="overflow-x-auto p-4 lg:p-8">
                    <table class="w-full text-left min-w-[800px]">
                        <thead>
                            <tr class="text-pink-400 uppercase text-[10px] font-black tracking-[0.2em] border-b border-gray-50">
                                <th class="px-6 py-4">No. Kamar</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Payment</th>
                                <th class="px-6 py-4 text-center">Status Bayar</th>
                                <th class="px-6 py-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($rooms as $room)
                            <tr class="group hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-6 font-black text-gray-800 text-lg group-hover:text-[#800000]">{{ $room['no'] }}</td>
                                <td class="px-6 py-6 italic font-bold text-gray-600 text-sm">{{ $room['left_status'] }}</td>
                                <td class="px-6 py-6 text-sm font-bold">{{ $room['payment'] }}</td>
                                
                                <td class="px-6 py-6 text-center">
                                    <div class="flex justify-center">
                                        {{-- LOGIKA STATUS BAYAR --}}
                                        @if($room['is_paid'])
                                            <div class="w-9 h-9 rounded-xl bg-green-50 text-green-500 flex items-center justify-center border border-green-100 shadow-sm" title="Sudah Bayar">
                                                <i class="fas fa-check text-sm"></i>
                                            </div>
                                        @else
                                            <div class="w-9 h-9 rounded-xl bg-red-50 text-red-500 flex items-center justify-center border border-red-100 shadow-sm animate-pulse" title="Belum Bayar">
                                                <i class="fas fa-times text-sm"></i>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-6 text-center">
                                    <span class="px-4 py-1.5 rounded-full {{ $room['action_color'] }} text-white text-[9px] font-black uppercase tracking-widest shadow-sm">
                                        {{ $room['action'] }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-20 text-center">
                                    <div class="flex flex-col items-center opacity-20">
                                        <i class="fas fa-bed text-4xl mb-3"></i>
                                        <p class="font-bold uppercase tracking-widest text-xs text-gray-400">Semua Kamar Kosong</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-8 py-6 bg-gray-50 border-t border-gray-100 pagination-container">
                    {{ $rooms->onEachSide(1)->links() }}
                </div>
            </div>
            
            <p class="mt-10 text-center text-[10px] text-gray-300 font-bold uppercase tracking-[0.5em] pb-10">Hotel SIG System v2.1</p>
        </main>
    </div>

    <x-bottom-nav></x-bottom-nav>
    <x-mobile-menu></x-mobile-menu>

    <script>
        function updateTime() {
            const now = new Date();
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
            const dateOptions = { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric' };
            
            const timeEl = document.getElementById('current-time-dash');
            const dateEl = document.getElementById('current-date-dash');
            
            if(timeEl) timeEl.innerText = now.toLocaleTimeString('id-ID', timeOptions).replace(/\./g, ':');
            if(dateEl) dateEl.innerText = now.toLocaleDateString('id-ID', dateOptions).toUpperCase();
        }

        setInterval(updateTime, 1000);
        updateTime();
    </script>
</body>
</html>