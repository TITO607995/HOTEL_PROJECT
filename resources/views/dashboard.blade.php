<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>Dashboard - Hotel SIG</title>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }
        
        @media (min-width: 1024px) {
            .custom-scrollbar::-webkit-scrollbar { width: 6px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: #F0E7E7; border-radius: 10px; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #800000; border-radius: 10px; }
        }

        #sidebar { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-blur: 10px; border: 1px solid rgba(240, 231, 231, 1); }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">

    <div class="relative z-[60]">
        <x-header></x-header>
    </div>

    <div class="flex flex-1">
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-72 bg-white shadow-2xl lg:shadow-none transform -translate-x-full lg:translate-x-0 lg:static lg:block flex-shrink-0 border-r border-gray-100 pt-20 lg:pt-0">
            <x-sidebar></x-sidebar>
        </aside>

        <main class="flex-1 p-4 md:p-8 lg:p-10 custom-scrollbar overflow-y-auto">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-10">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="h-1 w-8 bg-pink-400 rounded-full"></span>
                        <span class="text-[10px] font-black text-pink-500 uppercase tracking-[0.3em]">Overview Panel</span>
                    </div>
                    <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-800 tracking-tight">
                        Dashboard <span class="text-[#800000]">Hotel SIG</span>
                    </h1>
                    <p class="text-gray-400 text-sm mt-1 font-medium italic">Status real-time ketersediaan kamar dan reservasi.</p>
                </div>

                <div class="bg-[#F0E7E7] px-6 py-4 rounded-3xl border border-pink-200 flex items-center gap-4 shadow-sm min-w-[240px]">
                    <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center shadow-sm">
                        <i class="fas fa-satellite-dish text-pink-500 animate-pulse"></i>
                    </div>
                    <div>
                        <span id="current-time-dash" class="block font-black text-gray-800 text-xl leading-none tracking-tighter">00:00:00</span>
                        <span id="current-date-dash" class="text-[9px] font-bold text-pink-600 uppercase tracking-widest mt-1 block">Memuat Data...</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 mb-12">
                @foreach($stats as $nama => $jumlah)
                <div class="group relative overflow-hidden bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-500">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-[#F0E7E7] rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                    
                    <div class="relative z-10">
                        <h3 class="text-gray-400 font-bold text-[10px] uppercase tracking-[0.2em] mb-4">Unit Kamar</h3>
                        <div class="flex items-baseline gap-2">
                            <span class="text-6xl font-black text-gray-800 tracking-tighter group-hover:text-[#800000] transition-colors">{{ $jumlah }}</span>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $nama }}</span>
                        </div>
                        <div class="mt-6 flex items-center gap-2">
                            <div class="flex -space-x-2">
                                <div class="w-6 h-6 rounded-full border-2 border-white bg-pink-100"></div>
                                <div class="w-6 h-6 rounded-full border-2 border-white bg-pink-200"></div>
                            </div>
                            <span class="text-[9px] font-bold text-pink-500 uppercase tracking-widest italic">Total Inventory</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row justify-between items-center gap-4 bg-gradient-to-r from-white to-gray-50">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-[#800000] flex items-center justify-center text-white">
                            <i class="fas fa-list-ul"></i>
                        </div>
                        <h2 class="text-xl font-extrabold text-gray-800 tracking-tight">Kamar Terisi & Maintenance</h2>
                    </div>
                    <div class="flex items-center gap-2">
                         <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                         <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Realtime Updates</span>
                    </div>
                </div>

                <div class="overflow-x-auto p-4 lg:p-8">
                    <table class="w-full text-left min-w-[800px]">
                        <thead>
                            <tr class="text-pink-400 uppercase text-[10px] font-black tracking-[0.2em] border-b border-gray-50">
                                <th class="px-6 py-4">No. Kamar</th>
                                <th class="px-6 py-4">Tipe Unit</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Payment</th>
                                <th class="px-6 py-4 text-center">Status Bayar</th>
                                <th class="px-6 py-4 text-center">Action</th>
                                <th class="px-6 py-4 text-right">Visibility</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($roomList as $room)
                            <tr class="group hover:bg-gray-50 transition-all duration-300">
                                <td class="px-6 py-6 font-black text-gray-800 text-lg group-hover:text-[#800000]">{{ $room['no'] }}</td>
                                <td class="px-6 py-6">
                                    <span class="px-3 py-1 bg-gray-100 text-gray-500 text-[10px] font-bold rounded-lg uppercase tracking-wider italic">
                                        {{ $room['type'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-6">
                                    <span class="flex items-center gap-2 font-bold text-gray-600 italic text-sm">
                                        <i class="fas fa-door-closed text-pink-300"></i>
                                        {{ $room['left_status'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-6">
                                    <p class="text-gray-800 font-bold text-sm">{{ $room['payment'] }}</p>
                                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-widest mt-1">Metode</p>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    @if($room['is_paid'])
                                        <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-50 text-green-500 border border-green-100 shadow-sm">
                                            <i class="fas fa-check text-xs"></i>
                                        </div>
                                    @else
                                        <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-50 text-red-500 border border-red-100 shadow-sm animate-pulse">
                                            <i class="fas fa-exclamation text-xs"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <span class="px-4 py-1.5 rounded-full {{ $room['action_color'] }} text-white text-[9px] font-black uppercase tracking-widest shadow-lg shadow-gray-200">
                                        {{ $room['action'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-6 text-right">
                                    <span class="text-xs font-black text-gray-300 group-hover:text-pink-400 transition-colors uppercase tabular-nums">
                                        {{ $room['visibility'] }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-20 text-center">
                                    <div class="flex flex-col items-center opacity-30">
                                        <i class="fas fa-bed text-5xl mb-4"></i>
                                        <p class="font-bold text-sm uppercase tracking-[0.3em]">Semua Kamar Ready (Available)</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <p class="mt-10 text-center text-[10px] text-gray-300 font-bold uppercase tracking-[0.5em] pb-10">Hotel SIG Management System v2.0</p>
        </main>
    </div>

    <div id="overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden lg:hidden"></div>

    <script>
        // Sidebar Toggle for Mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

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
    </script>
</body>
</html>