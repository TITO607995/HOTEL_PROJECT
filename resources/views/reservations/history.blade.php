<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Guest History — Hotel SIG</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #F8FAFC; 
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: #800000; border-radius: 10px; }

        .glass-card { transition: all 0.3s ease; }
        .glass-card:hover { transform: translateY(-4px); }

        .safe-top { padding-top: env(safe-area-inset-top); }
        
        @media print {
            .no-print { display: none !important; }
            .ml-72 { margin-left: 0 !important; }
        }
    </style>
</head>

<body class="antialiased">

    <div class="bg-white safe-top sticky top-0 z-[50] shadow-sm md:static no-print">
        <x-header></x-header>
    </div>

    <div class="flex min-h-screen">
        {{-- Sidebar Identik --}}
        <aside class="hidden md:block w-72 border-r border-gray-100 bg-white no-print">
            <x-sidebar></x-sidebar>
        </aside>

        <main class="flex-1 p-4 lg:p-10 pb-32">
            <div class="max-w-7xl mx-auto">
                
                {{-- TITLE SECTION --}}
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-4">
                    <div class="px-2">
                        <h1 class="text-4xl font-black text-gray-900 tracking-tighter italic uppercase">History <span class="text-gray-300">Tamu</span></h1>
                        <p class="text-[11px] font-black text-[#800000] uppercase tracking-[0.3em] mt-1">Arsip Data & Rekapitulasi Reservasi</p>
                    </div>
                    
                    <div class="flex gap-3 no-print">
                        <button onclick="window.print()" class="px-6 py-3 bg-white border border-gray-200 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-50 transition-all flex items-center gap-2">
                            <i class="fas fa-print"></i> Export PDF
                        </button>
                    </div>
                </div>

                {{-- STATS SECTION (Identik dengan gaya Card kamu) --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
                    <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 glass-card">
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Stay</label>
                        <div class="text-2xl font-black text-gray-800 tracking-tight">{{ $reservations->count() }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 glass-card border-l-4 border-l-green-500">
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Checked Out</label>
                        <div class="text-2xl font-black text-green-600 tracking-tight">{{ $reservations->where('status', 'checked-out')->count() }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 glass-card">
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Revenue</label>
                        <div class="text-2xl font-black text-[#800000] tracking-tight italic">Rp{{ number_format($reservations->sum('total_price'), 0, ',', '.') }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 glass-card">
                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Active Now</label>
                        <div class="text-2xl font-black text-blue-600 tracking-tight">{{ $reservations->where('status', 'checked-in')->count() }}</div>
                    </div>
                </div>

                {{-- TABLE SECTION --}}
                <div class="bg-white rounded-[3.5rem] shadow-2xl shadow-gray-200/50 border border-gray-50 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em]">Master Database</h3>
                        <div class="relative no-print">
                            <input type="text" placeholder="Cari Tamu..." class="bg-white border-none rounded-full px-6 py-2 text-xs font-bold shadow-sm focus:ring-2 focus:ring-[#800000] w-64">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[10px] font-black text-[#800000] uppercase tracking-widest border-b border-gray-50">
                                    <th class="px-8 py-6">Profil Tamu</th>
                                    <th class="px-8 py-6">Kamar</th>
                                    <th class="px-8 py-6 text-center">Periode Inap</th>
                                    <th class="px-8 py-6 text-center">Status Akhir</th>
                                    <th class="px-8 py-6 text-right no-print">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($reservations as $res)
                                <tr class="hover:bg-red-50/30 transition-all group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-[#800000] rounded-2xl flex items-center justify-center text-white font-black shadow-lg shadow-red-900/20 group-hover:scale-110 transition-transform">
                                                {{ strtoupper(substr($res->guest_name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="font-black text-gray-800 text-sm uppercase tracking-tight leading-none mb-1">{{ $res->guest_name }}</div>
                                                <div class="text-[10px] font-bold text-gray-400 italic tracking-tighter">{{ $res->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="inline-flex items-center px-4 py-2 bg-gray-100 rounded-xl border border-gray-200">
                                            <span class="text-xs font-black text-gray-700 tracking-widest">RM-{{ $res->room->room_number ?? '??' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <div class="text-[11px] font-black text-gray-600 uppercase italic">
                                            {{ \Carbon\Carbon::parse($res->arrival_date)->format('d M') }} — {{ \Carbon\Carbon::parse($res->departure_date)->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        @if($res->status == 'checked-out')
                                            <span class="px-4 py-1.5 bg-green-50 text-green-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-green-100">
                                                Completed
                                            </span>
                                        @else
                                            <span class="px-4 py-1.5 bg-gray-50 text-gray-400 rounded-full text-[9px] font-black uppercase tracking-widest border border-gray-100 italic">
                                                {{ $res->status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 text-right no-print">
                                        <button class="w-10 h-10 bg-white border border-gray-100 rounded-xl text-gray-300 hover:text-[#800000] hover:border-[#800000] transition-all shadow-sm">
                                            <i class="fas fa-file-invoice"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center opacity-20">
                                            <i class="fas fa-history text-6xl mb-4 text-[#800000]"></i>
                                            <p class="font-black uppercase tracking-widest">Database Kosong</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <p class="text-center mt-12 text-[10px] font-black text-gray-300 uppercase tracking-[0.5em] italic">
                    &copy; 2026 Hotel SIG Archive Management System
                </p>
            </div>
        </main>
    </div>

    <x-bottom-nav class="no-print"></x-bottom-nav>

</body>
</html>