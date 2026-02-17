<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kamar - Hotel SIG</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; overflow: hidden; }
        .bg-maroon { background-color: #800000; }
        .text-maroon { color: #800000; }
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #800000; }
    </style>
</head>
<body class="bg-[#F3F4F6] antialiased h-screen flex flex-col">

    <x-header></x-header>

    <div class="flex flex-1 overflow-hidden">
        <aside class="h-full flex-shrink-0 border-r border-gray-200 bg-white">
            <x-sidebar></x-sidebar>
        </aside>

        <main class="flex-1 p-6 lg:p-10 overflow-y-auto custom-scroll">
            
            <div class="mb-8">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Ketersediaan Kamar</h1>
                <p class="text-gray-500 font-medium">Data real-time kondisi operasional hotel.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-[28px] shadow-sm border border-gray-100 flex items-center gap-5">
                    <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Kamar</p>
                        <h4 class="text-2xl font-extrabold text-gray-800">{{ $rooms->count() }}</h4>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-[28px] shadow-sm border border-gray-100 flex items-center gap-5">
                    <div class="w-14 h-14 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center text-2xl">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Tersedia</p>
                        <h4 class="text-2xl font-extrabold text-gray-800">{{ $rooms->where('status', 'available')->count() }}</h4>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-[28px] shadow-sm border border-gray-100 flex items-center gap-5">
                    <div class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-2xl">
                        <i class="fas fa-user-tag"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Terisi</p>
                        <h4 class="text-2xl font-extrabold text-gray-800">{{ $rooms->where('status', 'occupied')->count() }}</h4>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-[28px] shadow-sm border border-gray-100 flex items-center gap-5">
                    <div class="w-14 h-14 bg-yellow-50 text-yellow-600 rounded-2xl flex items-center justify-center text-2xl">
                        <i class="fas fa-broom"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Kotor</p>
                        <h4 class="text-2xl font-extrabold text-gray-800">{{ $rooms->where('status', 'vacant dirty')->count() }}</h4>
                    </div>
                </div>
            </div>

            <div class="sticky top-0 z-20 pb-4">
                <form method="GET" action="{{ route('rooms.index') }}" class="bg-white/90 backdrop-blur-md p-5 rounded-[28px] shadow-md border border-gray-100 flex flex-wrap md:flex-nowrap gap-4 items-end">
                    <div class="flex-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] ml-1">Cari No. Kamar</label>
                        <div class="relative mt-1">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fas fa-search text-sm"></i>
                            </span>
                            <input 
                                type="text" 
                                name="room_number" 
                                value="{{ request('room_number') }}" 
                                placeholder="Cari nomor kamar..." 
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#800000] font-semibold text-gray-700 transition-all"
                            >
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-maroon text-white px-8 py-2.5 rounded-xl font-bold hover:bg-red-900 transition-all active:scale-95 shadow-lg shadow-red-900/20">
                            Cari
                        </button>
                        <a href="{{ route('rooms.index') }}" class="bg-gray-100 text-gray-500 px-6 py-2.5 rounded-xl font-bold hover:bg-gray-200 transition-all text-center">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 pb-10">
                @foreach($rooms as $room)
                <div class="group bg-white rounded-[32px] border border-gray-100 flex flex-col h-full hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="relative h-40 overflow-hidden m-2 rounded-[24px]">
                        <img src="{{ $room->foto_display }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="Kamar {{ $room->room_number }}">
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
                                    default => 'bg-gray-100 text-gray-600 icon-fa-question'
                                };
                                $parts = explode(' icon-', $style);
                                $colorClass = $parts[0];
                                $icon = $parts[1];
                            @endphp

                            <div class="{{ $colorClass }} px-4 py-1.5 rounded-lg text-[10px] font-extrabold uppercase tracking-widest flex items-center gap-2">
                                <i class="fas {{ $icon }}"></i>
                                {{ $room->status }}
                            </div>
                            
                            <button class="w-8 h-8 rounded-full hover:bg-gray-50 text-gray-300 hover:text-maroon transition-all flex items-center justify-center">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach 
            </div>

            @if($rooms->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 bg-white rounded-[40px] shadow-sm mt-10">
                    <i class="fas fa-door-closed text-5xl text-gray-200 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-400">Data kamar tidak ditemukan</h3>
                </div>
            @endif

        </main>
    </div>

</body>
</html>