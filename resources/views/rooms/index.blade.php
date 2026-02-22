<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#800000">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">

    <title>Manajemen Kamar - Hotel SIG</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow: hidden;
            overscroll-behavior-y: contain;
        }

        .bg-maroon { background-color: #800000; }
        .text-maroon { color: #800000; }

        @media (min-width: 1024px) {
            .custom-scroll::-webkit-scrollbar { width: 6px; }
            .custom-scroll::-webkit-scrollbar-track { background: transparent; }
            .custom-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
            .custom-scroll::-webkit-scrollbar-thumb:hover { background: #800000; }
        }

        .safe-top { padding-top: env(safe-area-inset-top); }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom); }

        [x-cloak] { display: none !important; }

        .room-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .nav-item:active i { transform: scale(0.8); transition: 0.1s; }
    </style>
</head>

<body class="bg-[#F3F4F6] antialiased h-screen flex flex-col mb-20 lg:mb-0" x-data="{ openModal: false, mobileMenu: false }">

    <div class="relative z-[60] safe-top bg-white lg:bg-transparent">
        <x-header></x-header>
    </div>

    <div class="flex flex-1 overflow-hidden">
        <aside class="h-full flex-shrink-0 border-r border-gray-200 bg-white hidden lg:block">
            <x-sidebar></x-sidebar>
        </aside>

        <main class="flex-1 p-4 md:p-8 lg:p-10 overflow-y-auto custom-scroll">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Ketersediaan Kamar</h1>
                    <p class="text-gray-500 font-medium">Data real-time kondisi operasional hotel.</p>
                </div>
                
                @if(auth()->check() && auth()->user()->role && in_array(strtoupper(auth()->user()->role->name), ['SUPERADMIN', 'ADMIN']))
                <button @click="openModal = true" class="hidden md:flex bg-maroon text-white px-6 py-3 rounded-2xl font-bold hover:bg-red-900 transition-all items-center gap-2 shadow-lg shadow-red-900/20 active:scale-95">
                    <i class="fas fa-plus-circle"></i>
                    Tambah Kamar Baru
                </button>
                @endif
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-8">
                <div class="bg-white p-4 md:p-6 rounded-[24px] md:rounded-[28px] shadow-sm border border-gray-100 flex items-center gap-3 md:gap-5">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-blue-50 text-blue-600 rounded-xl md:rounded-2xl flex items-center justify-center text-xl md:text-2xl">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <div>
                        <p class="text-[8px] md:text-xs font-bold text-gray-400 uppercase tracking-widest">Total</p>
                        <h4 class="text-lg md:text-2xl font-extrabold text-gray-800">{{ $rooms->count() }}</h4>
                    </div>
                </div>

                <div class="bg-white p-4 md:p-6 rounded-[24px] md:rounded-[28px] shadow-sm border border-gray-100 flex items-center gap-3 md:gap-5">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-green-50 text-green-600 rounded-xl md:rounded-2xl flex items-center justify-center text-xl md:text-2xl">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <p class="text-[8px] md:text-xs font-bold text-gray-400 uppercase tracking-widest">Tersedia</p>
                        <h4 class="text-lg md:text-2xl font-extrabold text-gray-800">{{ $rooms->where('status', 'available')->count() }}</h4>
                    </div>
                </div>

                <div class="bg-white p-4 md:p-6 rounded-[24px] md:rounded-[28px] shadow-sm border border-gray-100 flex items-center gap-3 md:gap-5">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-red-50 text-red-600 rounded-xl md:rounded-2xl flex items-center justify-center text-xl md:text-2xl">
                        <i class="fas fa-user-tag"></i>
                    </div>
                    <div>
                        <p class="text-[8px] md:text-xs font-bold text-gray-400 uppercase tracking-widest">Terisi</p>
                        <h4 class="text-lg md:text-2xl font-extrabold text-gray-800">{{ $rooms->where('status', 'occupied')->count() }}</h4>
                    </div>
                </div>

                <div class="bg-white p-4 md:p-6 rounded-[24px] md:rounded-[28px] shadow-sm border border-gray-100 flex items-center gap-3 md:gap-5">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-yellow-50 text-yellow-600 rounded-xl md:rounded-2xl flex items-center justify-center text-xl md:text-2xl">
                        <i class="fas fa-broom"></i>
                    </div>
                    <div>
                        <p class="text-[8px] md:text-xs font-bold text-gray-400 uppercase tracking-widest">Kotor</p>
                        <h4 class="text-lg md:text-2xl font-extrabold text-gray-800">{{ $rooms->where('status', 'vacant dirty')->count() }}</h4>
                    </div>
                </div>
            </div>

            <div class="sticky top-0 z-20 pb-4">
                <form method="GET" action="{{ route('rooms.index') }}" class="bg-white/90 backdrop-blur-md p-4 md:p-5 rounded-[24px] md:rounded-[28px] shadow-md border border-gray-100 flex flex-wrap md:flex-nowrap gap-3 items-end">
                    <div class="flex-1 min-w-[150px]">
                        <label class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.2em] ml-1">No. Kamar</label>
                        <div class="relative mt-1">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fas fa-search text-xs"></i>
                            </span>
                            <input type="text" name="room_number" value="{{ request('room_number') }}" placeholder="Cari..." 
                                class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#800000] font-semibold text-gray-700 text-sm">
                        </div>
                    </div>
                    <div class="flex gap-2 w-full md:w-auto">
                        <button type="submit" class="flex-1 md:flex-none bg-maroon text-white px-6 py-2 rounded-xl font-bold active:scale-95 shadow-lg shadow-red-900/10">Cari</button>
                        <a href="{{ route('rooms.index') }}" class="flex-1 md:flex-none bg-gray-100 text-gray-500 px-4 py-2 rounded-xl font-bold text-center flex items-center justify-center text-sm">Reset</a>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6 pb-24">
                @foreach($rooms as $room)
                <div class="room-card group bg-white rounded-[32px] border border-gray-100 flex flex-col h-full hover:shadow-xl">
                    <div class="relative h-40 overflow-hidden m-2 rounded-[24px]">
                        <img src="{{ $room->image ? asset('storage/' . $room->image) : ($room->foto_display ?? 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=500') }}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                             alt="Kamar {{ $room->room_number }}">
                        <div class="absolute top-3 right-3 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider text-gray-800 italic">
                            {{ $room->type }}
                        </div>
                    </div>

                    <div class="p-5 flex flex-col justify-between flex-1">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-xl font-extrabold text-gray-800 tracking-tighter">{{ $room->room_number }}</h3>
                            <div class="text-right">
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest leading-none">Price</p>
                                <p class="text-md font-black text-maroon leading-tight">Rp{{ number_format($room->price, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                            @php
                                $status = strtolower($room->status);
                                $style = match($status) {
                                    'available' => 'bg-green-100 text-green-600 icon-fa-check-circle',
                                    'occupied' => 'bg-red-100 text-red-600 icon-fa-user-check',
                                    'booked' => 'bg-orange-100 text-orange-600 icon-fa-calendar-alt',
                                    'vacant dirty' => 'bg-yellow-100 text-yellow-700 icon-fa-broom',
                                    'oo', 'os' => 'bg-gray-800 text-white icon-fa-tools',
                                    default => 'bg-gray-100 text-gray-600 icon-fa-question'
                                };
                                $parts = explode(' icon-', $style);
                            @endphp

                            <div class="{{ $parts[0] }} px-4 py-1.5 rounded-lg text-[10px] font-extrabold uppercase tracking-widest flex items-center gap-2">
                                <i class="fas {{ $parts[1] }}"></i>
                                {{ $room->status }}
                            </div>
                            
                            <button class="w-10 h-10 rounded-full hover:bg-gray-50 text-gray-300 hover:text-maroon transition-all flex items-center justify-center">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach 
            </div>
        </main>
    </div>

    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-xl border-t border-gray-100 px-6 py-3 z-[100] safe-bottom flex justify-between items-center shadow-[0_-10px_25px_-5px_rgba(0,0,0,0.05)]">
        <a href="{{ route('dashboard') }}" class="nav-item flex flex-col items-center gap-1 {{ request()->routeIs('dashboard') ? 'text-maroon' : 'text-gray-400' }}">
            <i class="fas fa-th-large text-xl"></i>
            <span class="text-[9px] font-black uppercase">Dash</span>
        </a>

        <a href="{{ route('rooms.index') }}" class="nav-item flex flex-col items-center gap-1 {{ request()->routeIs('rooms.*') ? 'text-maroon' : 'text-gray-400' }}">
            <i class="fas fa-bed text-xl"></i>
            <span class="text-[9px] font-bold uppercase tracking-tighter">Rooms</span>
        </a>
        
        <div class="relative -mt-12">
            <button @click="openModal = true" class="w-14 h-14 bg-maroon rounded-2xl shadow-xl shadow-red-900/30 flex items-center justify-center text-white text-xl active:scale-90 transition-all border-4 border-[#F3F4F6]">
                <i class="fas fa-plus"></i>
            </button>
        </div>

        <a href="{{ route('reservations.index') }}" class="nav-item flex flex-col items-center gap-1 {{ request()->routeIs('reservations.*') ? 'text-maroon' : 'text-gray-400' }}">
            <i class="fas fa-clipboard-list text-xl"></i>
            <span class="text-[9px] font-bold uppercase tracking-tighter">Orders</span>
        </a>

        <button @click="mobileMenu = true" class="nav-item flex flex-col items-center gap-1 text-gray-400">
            <i class="fas fa-bars text-xl"></i>
            <span class="text-[9px] font-bold uppercase tracking-tighter">Menu</span>
        </button>
    </nav>

    <div x-show="mobileMenu" x-cloak class="fixed inset-0 z-[120] lg:hidden" role="dialog" aria-modal="true">
        <div x-show="mobileMenu" 
             x-transition:enter="transition-opacity ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition-opacity ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             @click="mobileMenu = false" 
             class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div x-show="mobileMenu" 
             x-transition:enter="transition ease-out duration-300 transform" 
             x-transition:enter-start="translate-y-full" 
             x-transition:enter-end="translate-y-0" 
             x-transition:leave="transition ease-in duration-200 transform" 
             x-transition:leave-start="translate-y-0" 
             x-transition:leave-end="translate-y-full" 
             class="fixed inset-x-0 bottom-0 bg-white rounded-t-[32px] p-8 pb-12 shadow-2xl">
            
            <div class="w-12 h-1.5 bg-gray-200 rounded-full mx-auto mb-8"></div>
            <h3 class="text-xl font-black text-gray-900 mb-6">Menu Navigasi</h3>
            
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('employees.index') }}" class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl active:scale-95 transition-all">
                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center"><i class="fas fa-users"></i></div>
                    <span class="font-bold text-gray-700 text-sm">Staff</span>
                </a>
                <a href="{{ route('reports.index') }}" class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl active:scale-95 transition-all">
                    <div class="w-10 h-10 bg-green-100 text-green-600 rounded-xl flex items-center justify-center"><i class="fas fa-chart-line"></i></div>
                    <span class="font-bold text-gray-700 text-sm">Laporan</span>
                </a>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl active:scale-95 transition-all">
                    <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center"><i class="fas fa-user-cog"></i></div>
                    <span class="font-bold text-gray-700 text-sm">Profil</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-4 p-4 bg-red-50 rounded-2xl active:scale-95 transition-all text-red-600 text-left">
                        <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center"><i class="fas fa-sign-out-alt"></i></div>
                        <span class="font-bold text-sm">Keluar</span>
                    </button>
                </form>
            </div>

            <button @click="mobileMenu = false" class="w-full mt-8 py-4 bg-gray-100 rounded-2xl font-bold text-gray-500 active:bg-gray-200 transition-all">Tutup</button>
        </div>
    </div>

    <div x-show="openModal" x-cloak class="fixed inset-0 z-[110] flex items-end md:items-center justify-center p-0 md:p-4 bg-black/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        
        <div class="bg-white w-full max-w-lg rounded-t-[32px] md:rounded-[40px] shadow-2xl overflow-hidden mb-safe-bottom" 
             @click.away="openModal = false"
             x-transition:enter="transition ease-out duration-300 transform" 
             x-transition:enter-start="translate-y-full md:scale-90" 
             x-transition:enter-end="translate-y-0 md:scale-100">
            
            <div class="w-12 h-1.5 bg-gray-200 rounded-full mx-auto mt-4 md:hidden"></div>

            <div class="p-6 md:p-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">Tambah Kamar</h2>
                    <button @click="openModal = false" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="{{ route('rooms.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 md:space-y-5">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">No. Kamar</label>
                            <input type="text" name="room_number" required class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-maroon focus:outline-none font-bold text-gray-700">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Tipe</label>
                            <select name="type" required class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-maroon focus:outline-none font-bold text-gray-700">
                                <option value="Standard">Standard</option>
                                <option value="Deluxe">Deluxe</option>
                                <option value="Suite">Suite</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Harga/Malam</label>
                        <input type="number" name="price" required class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-maroon font-bold text-gray-700">
                    </div>
                    <div class="pt-4 flex gap-3 pb-safe-bottom">
                        <button type="button" @click="openModal = false" class="flex-1 px-6 py-4 rounded-2xl font-bold text-gray-500 bg-gray-100">Batal</button>
                        <button type="submit" class="flex-[2] bg-maroon text-white px-6 py-4 rounded-2xl font-bold shadow-lg shadow-red-900/20">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>
</body>

</html>