<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>Laporan Operasional - Hotel SIG</title>
    <style>
        body { font-family: 'Poppins', sans-serif; scroll-behavior: smooth; }
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #800000; border-radius: 10px; }
        @media print {
            .no-print { display: none; }
            aside { display: none; }
            main { margin-left: 0 !important; }
        }
    </style>
</head>

<body class="bg-[#F8F9FA] text-gray-800 antialiased">
   <x-header></x-header>
    <div class="flex min-h-screen">
        <aside class="w-64 fixed inset-y-0 left-0 z-50 shadow-2xl bg-white border-r border-gray-100 no-print">
            <x-sidebar></x-sidebar>
        </aside>

        <main class="flex-1 ml-64 flex flex-col min-h-screen relative">
            
            <div class="p-6 lg:p-10 max-w-[1600px] mx-auto w-full">
                
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
                    <div>
                        <h1 class="text-3xl font-extrabold text-[#800000] tracking-tight uppercase italic leading-none">
                            Executive <span class="text-gray-900 not-italic">Reports</span>
                        </h1>
                        <p class="text-gray-400 text-sm font-medium mt-2">Data ringkasan operasional real-time Hotel SIG.</p>
                    </div>
                    
                    <div class="flex items-center gap-3 no-print">
                        <button onclick="window.print()" class="group flex items-center gap-2 bg-white border border-gray-200 text-gray-600 px-5 py-2.5 rounded-2xl font-bold text-xs hover:bg-gray-50 hover:border-[#800000] hover:text-[#800000] transition-all shadow-sm">
                            <i class="fas fa-print group-hover:scale-110 transition-transform"></i> Cetak Laporan
                        </button>
                        <button class="group flex items-center gap-2 bg-[#800000] text-white px-6 py-2.5 rounded-2xl font-bold text-xs hover:bg-red-900 transition-all shadow-lg shadow-red-900/20 active:scale-95">
                            <i class="fas fa-file-excel group-hover:bounce transition-transform"></i> Ekspor Excel
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    @php 
                        $cards = [
                            ['Available', $availableCount ?? '24', 'fas fa-door-open', 'green'],
                            ['Out of Order', $ooCount ?? '5', 'fas fa-tools', 'red'],
                            ['Staff Active', $staffCount ?? '12', 'fas fa-users', 'blue'],
                            ['Occupancy', '78%', 'fas fa-percentage', 'orange']
                        ];
                    @endphp

                    @foreach($cards as $card)
                    <div class="group bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-{{ $card[3] }}-50 text-{{ $card[3] }}-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="{{ $card[2] }} text-lg"></i>
                            </div>
                            <span class="text-[10px] font-black text-{{ $card[3] }}-500 uppercase tracking-widest">{{ $card[0] }}</span>
                        </div>
                        <h3 class="text-3xl font-black text-gray-800 tracking-tight">{{ $card[1] }}</h3>
                        <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Update terakhir: Baru saja</p>
                    </div>
                    @endforeach
                </div>

                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden transition-all">
                    <div class="p-8 border-b border-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-8 bg-[#800000] rounded-full"></div>
                            <h3 class="font-black text-gray-800 uppercase tracking-widest text-sm">Log Aktivitas Terbaru</h3>
                        </div>
                        <div class="relative w-full sm:w-auto">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
                            <input type="text" placeholder="Cari laporan..." 
                                class="w-full sm:w-64 pl-10 pr-4 py-3 bg-gray-50 border-transparent rounded-2xl text-xs focus:bg-white focus:ring-2 focus:ring-red-100 focus:border-[#800000] outline-none transition-all">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                    <th class="px-8 py-5">Tanggal</th>
                                    <th class="px-8 py-5">Kategori</th>
                                    <th class="px-8 py-5">Deskripsi</th>
                                    <th class="px-8 py-5">Petugas</th>
                                    <th class="px-8 py-5 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr class="hover:bg-red-50/30 transition-colors group">
                                    <td class="px-8 py-5 text-xs font-bold text-gray-500 italic">17 Feb 2026</td>
                                    <td class="px-8 py-5">
                                        <span class="text-[9px] font-black bg-red-100 text-red-600 px-3 py-1.5 rounded-lg uppercase tracking-wider">Maintenance</span>
                                    </td>
                                    <td class="px-8 py-5 text-xs font-semibold text-gray-700">Perbaikan AC Kamar 104</td>
                                    <td class="px-8 py-5 text-xs font-bold text-gray-900">Budi Santoso</td>
                                    <td class="px-8 py-5 text-center">
                                        <span class="text-[9px] font-black text-green-500 uppercase tracking-[0.15em] flex items-center justify-center gap-1">
                                            <i class="fas fa-check-circle"></i> Selesai
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-blue-50/30 transition-colors group">
                                    <td class="px-8 py-5 text-xs font-bold text-gray-500 italic">17 Feb 2026</td>
                                    <td class="px-8 py-5">
                                        <span class="text-[9px] font-black bg-blue-100 text-blue-600 px-3 py-1.5 rounded-lg uppercase tracking-wider">Housekeeping</span>
                                    </td>
                                    <td class="px-8 py-5 text-xs font-semibold text-gray-700">General Cleaning Kamar 205</td>
                                    <td class="px-8 py-5 text-xs font-bold text-gray-900">Siti Aminah</td>
                                    <td class="px-8 py-5 text-center">
                                        <span class="text-[9px] font-black text-orange-400 uppercase tracking-[0.15em] flex items-center justify-center gap-1 animate-pulse">
                                            <i class="fas fa-clock"></i> On Progress
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="p-8 bg-gray-50/30 flex flex-col sm:flex-row justify-between items-center gap-4 border-t border-gray-50">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Halaman 1 dari 12</p>
                        <div class="flex gap-2">
                            <button class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 text-gray-400 hover:bg-white hover:text-[#800000] hover:border-[#800000] transition-all"><i class="fas fa-chevron-left text-xs"></i></button>
                            <button class="w-10 h-10 flex items-center justify-center rounded-xl bg-[#800000] text-white font-black text-xs shadow-lg shadow-red-900/20">1</button>
                            <button class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 text-gray-600 hover:bg-white hover:text-[#800000] hover:border-[#800000] transition-all font-bold text-xs">2</button>
                            <button class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 text-gray-400 hover:bg-white hover:text-[#800000] hover:border-[#800000] transition-all"><i class="fas fa-chevron-right text-xs"></i></button>
                        </div>
                    </div>
                </div>

                <footer class="mt-16 text-center border-t border-gray-100 pt-8 no-print">
                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.5em]">Hotel SIG Internal Reporting System v1.0</p>
                    <p class="text-[9px] text-gray-300 mt-2 font-medium">Laporan ini dibuat otomatis oleh sistem pada {{ date('d/m/Y H:i') }}</p>
                </footer>
            </div>
        </main>
    </div>

</body>
</html>