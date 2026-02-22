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
        
        .nav-item:active i { transform: scale(0.8); transition: 0.1s; }
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
                            @forelse($roomList as $room)
                            <tr class="group hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-6 font-black text-gray-800 text-lg group-hover:text-[#800000]">{{ $room['no'] }}</td>
                                <td class="px-6 py-6 italic font-bold text-gray-600 text-sm">{{ $room['left_status'] }}</td>
                                <td class="px-6 py-6 text-sm font-bold">{{ $room['payment'] }}</td>
                                <td class="px-6 py-6 text-center">
                                    <i class="fas {{ $room['is_paid'] ? 'fa-check text-green-500' : 'fa-exclamation text-red-500 animate-pulse' }}"></i>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <span class="px-4 py-1.5 rounded-full {{ $room['action_color'] }} text-white text-[9px] font-black uppercase tracking-widest">
                                        {{ $room['action'] }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="py-20 text-center opacity-30 font-bold uppercase tracking-widest text-xs">Kosong</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <p class="mt-10 text-center text-[10px] text-gray-300 font-bold uppercase tracking-[0.5em] pb-10">Hotel SIG System v2.1</p>
        </main>
    </div>

<nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-xl border-t border-gray-100 px-6 py-3 z-[100] safe-bottom flex justify-between items-center shadow-[0_-10px_25px_-5px_rgba(0,0,0,0.05)]">
        
        <a href="{{ route('dashboard') }}" class="nav-item flex flex-col items-center gap-1 {{ request()->routeIs('dashboard') ? 'text-[#800000]' : 'text-gray-400' }}">
            <i class="fas fa-th-large text-xl"></i>
            <span class="text-[9px] font-black uppercase tracking-tighter">Dash</span>
        </a>

        <a href="{{ route('rooms.index') }}" class="nav-item flex flex-col items-center gap-1 {{ request()->is('rooms*') ? 'text-[#800000]' : 'text-gray-400' }}">
            <i class="fas fa-bed text-xl"></i>
            <span class="text-[9px] font-bold uppercase tracking-tighter">Rooms</span>
        </a>
        
        <div class="relative -mt-14">
            <div class="absolute inset-0 -m-2 bg-gray-50 rounded-full border-t border-gray-100"></div>
            <a href="{{ route('reservations.registration') }}" class="relative w-14 h-14 bg-[#800000] rounded-2xl shadow-xl shadow-red-900/30 flex items-center justify-center text-white text-xl active:scale-90 transition-all border-4 border-white">
                <i class="fas fa-plus"></i>
            </a>
        </div>

        <a href="{{ route('reservations.index') }}" class="nav-item flex flex-col items-center gap-1 {{ request()->is('reservasi*') ? 'text-[#800000]' : 'text-gray-400' }}">
            <i class="fas fa-clipboard-list text-xl"></i>
            <span class="text-[9px] font-bold uppercase tracking-tighter">Orders</span>
        </a>
        
        <button @click="mobileMenu = true" class="nav-item flex flex-col items-center gap-1 text-gray-400 active:text-[#800000]">
            <i class="fas fa-bars text-xl"></i>
            <span class="text-[9px] font-bold uppercase tracking-tighter">Menu</span>
        </button>
    </nav>

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
                    <h2 class="text-2xl font-black text-gray-800 tracking-tighter">Main Navigation</h2>
                    <p class="text-[10px] font-bold text-[#800000] uppercase tracking-[0.2em]">Hotel SIG Management</p>
                </div>
                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-gray-100">
                    <i class="fas fa-grid-2 text-[#800000]"></i>
                </div>
            </div>
            
            <div class="grid grid-cols-3 gap-4 mb-8">
                <a href="{{ route('guests.index') }}" class="flex flex-col items-center p-5 bg-white rounded-[2rem] border border-gray-100 shadow-sm active:scale-95 transition-all">
                    <div class="w-12 h-12 bg-pink-50 rounded-2xl flex items-center justify-center mb-2">
                        <i class="fas fa-users text-pink-500 text-lg"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-tighter text-gray-600">Tamu</span>
                </a>
                
                @if(auth()->check() && auth()->user()->role && in_array(strtoupper(auth()->user()->role->name), ['SUPERADMIN', 'SUPER ADMIN']))
                <a href="{{ route('rooms.maintenance.page') }}" class="flex flex-col items-center p-5 bg-white rounded-[2rem] border border-gray-100 shadow-sm active:scale-95 transition-all">
                    <div class="w-12 h-12 bg-orange-50 rounded-2xl flex items-center justify-center mb-2">
                        <i class="fas fa-tools text-orange-500 text-lg"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-tighter text-gray-600">Status</span>
                </a>

                <a href="{{ route('employees.index') }}" class="flex flex-col items-center p-5 bg-white rounded-[2rem] border border-gray-100 shadow-sm active:scale-95 transition-all">
                    <div class="w-12 h-12 bg-purple-50 rounded-2xl flex items-center justify-center mb-2">
                        <i class="fas fa-user-shield text-purple-500 text-lg"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-tighter text-gray-600">Staff</span>
                </a>

                <a href="{{ route('reports.index') }}" class="flex flex-col items-center p-5 bg-white rounded-[2rem] border border-gray-100 shadow-sm active:scale-95 transition-all">
                    <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center mb-2">
                        <i class="fas fa-chart-line text-blue-500 text-lg"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-tighter text-gray-600">Laporan</span>
                </a>
                @endif
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full p-5 bg-white border border-red-100 text-red-600 rounded-[2rem] font-black uppercase text-[10px] tracking-[0.2em] flex items-center justify-center gap-3 shadow-sm active:bg-red-50 transition-all">
                    <div class="w-8 h-8 bg-red-500 text-white rounded-xl flex items-center justify-center shadow-lg shadow-red-200">
                        <i class="fas fa-power-off text-xs"></i>
                    </div>
                    Keluar Sistem
                </button>
            </form>
        </div>
    </div>

    <script>
        // Realtime Clock Dashboard
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

        // PWA Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(err => console.log('SW failed', err));
            });
        }
    </script>
</body>
</html>