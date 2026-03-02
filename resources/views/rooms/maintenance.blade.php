<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>Maintenance - Hotel SIG</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.01em; }
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
        .bg-pattern { background-color: #f8f9fa; background-image: radial-gradient(#e5e7eb 0.5px, transparent 0.5px); background-size: 20px 20px; }
        .tab-active { background-color: #790000; color: white; shadow: 0 10px 15px -3px rgba(121, 0, 0, 0.3); }
    </style>
</head>
<body class="bg-pattern min-h-screen flex flex-col">

    <x-header></x-header>

    <div class="flex flex-1">
        <x-sidebar></x-sidebar>

        <main class="flex-1 p-6 lg:p-10">
            
            {{-- BREADCRUMB & TITLE --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <div class="px-3 py-1 bg-pink-100 rounded-full">
                            <span class="text-[10px] font-black text-pink-600 uppercase tracking-widest">Control Panel</span>
                        </div>
                        <span class="text-gray-300">/</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Engineering</span>
                    </div>
                    <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Room <span class="text-[#790000]">Status</span></h1>
                    <p class="text-gray-500 text-sm font-medium">Manajemen pemeliharaan berdasarkan kategori status unit.</p>
                </div>
                
                {{-- STATS WIDGET --}}
                <div class="flex gap-4">
                    <div class="bg-white px-6 py-4 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-500"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black text-gray-400 uppercase">OO (Order)</span>
                            <span class="text-2xl font-black text-gray-900">{{ \App\Models\Room::where('status', 'oo')->count() }}</span>
                        </div>
                    </div>
                    <div class="bg-white px-6 py-4 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center">
                            <i class="fas fa-tools text-orange-500"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black text-gray-400 uppercase">OS (Service)</span>
                            <span class="text-2xl font-black text-gray-900">{{ \App\Models\Room::where('status', 'os')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB NAVIGATION --}}
            <div class="flex flex-wrap gap-2 mb-8 bg-white/60 backdrop-blur-md p-2 rounded-[2.5rem] w-fit border border-gray-100 shadow-sm">
                @foreach([
                    'oo' => ['label' => 'Out of Order', 'icon' => 'fa-ban', 'color' => 'bg-red-600'],
                    'os' => ['label' => 'Out of Service', 'icon' => 'fa-wrench', 'color' => 'bg-orange-500'],
                    'vacant dirty' => ['label' => 'Dirty', 'icon' => 'fa-broom', 'color' => 'bg-yellow-600'],
                    'available' => ['label' => 'Available', 'icon' => 'fa-check', 'color' => 'bg-green-600']
                ] as $key => $tab)
                    <a href="{{ route('rooms.maintenance', ['status' => $key]) }}" 
                       class="flex items-center gap-3 px-6 py-3 rounded-full text-[11px] font-black uppercase tracking-widest transition-all duration-300 {{ ($statusFilter ?? 'oo') == $key ? 'bg-gray-900 text-white shadow-xl scale-105' : 'text-gray-400 hover:bg-white hover:text-gray-600' }}">
                        <i class="fas {{ $tab['icon'] }} text-[10px]"></i>
                        {{ $tab['label'] }}
                    </a>
                @endforeach
            </div>

            {{-- GRID SYSTEM --}}
            <div class="grid grid-cols-1 xl:grid-cols-2 2xl:grid-cols-3 gap-6">
                @forelse($rooms as $room)
                <div class="group bg-white rounded-[2.5rem] p-2 border border-transparent hover:border-pink-200 transition-all duration-500 hover:shadow-[0_20px_50px_rgba(121,0,0,0.05)]">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl bg-[#790000] text-white flex items-center justify-center shadow-lg shadow-red-900/20">
                                    <span class="text-xl font-black">{{ $room->room_number }}</span>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900">Room Unit</h3>
                                    <div class="flex items-center gap-2">
                                        @php
                                            $dotColor = match($room->status) {
                                                'available' => 'bg-green-500',
                                                'oo' => 'bg-red-500',
                                                'os' => 'bg-orange-500',
                                                default => 'bg-blue-500'
                                            };
                                        @endphp
                                        <span class="flex h-2 w-2 rounded-full {{ $dotColor }}"></span>
                                        <span class="text-[10px] font-black uppercase text-gray-400 tracking-tighter">{{ $room->status }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            @if($room->status != 'available')
                            <div class="px-3 py-1 bg-red-50 text-red-500 rounded-full animate-pulse">
                                <i class="fas fa-lock text-[10px]"></i>
                            </div>
                            @endif
                        </div>

                        <form action="{{ route('rooms.maintenance.update', $room->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-gray-400 uppercase ml-2 tracking-widest">Update Status</label>
                                <div class="relative">
                                    <select name="status" class="w-full bg-gray-50 border border-gray-100 text-gray-700 text-xs font-bold rounded-2xl px-5 py-4 outline-none focus:ring-2 focus:ring-pink-100 focus:bg-white transition-all appearance-none cursor-pointer">
                                        <option value="available" {{ $room->status == 'available' ? 'selected' : '' }}>✅ Available (Normal)</option>
                                        <option value="oo" {{ $room->status == 'oo' ? 'selected' : '' }}>🚫 Out of Order (OO)</option>
                                        <option value="os" {{ $room->status == 'os' ? 'selected' : '' }}>⚠️ Out of Service (OS)</option>
                                        <option value="vacant dirty" {{ $room->status == 'vacant dirty' ? 'selected' : '' }}>🧹 Vacant Dirty</option>
                                    </select>
                                    <i class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-gray-300 pointer-events-none"></i>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-gray-400 uppercase ml-2 tracking-widest">Maintenance Notes</label>
                                <textarea name="notes" rows="2" 
                                          placeholder="Tulis alasan atau detail perbaikan..." 
                                          class="w-full bg-gray-50 border border-gray-100 text-gray-700 text-xs font-medium rounded-2xl px-5 py-4 outline-none focus:ring-2 focus:ring-pink-100 focus:bg-white transition-all resize-none">{{ $room->maintenance_notes }}</textarea>
                            </div>

                            <button type="submit" class="w-full bg-gray-900 hover:bg-[#790000] text-white text-[11px] font-black py-4 rounded-2xl shadow-xl shadow-gray-200 hover:shadow-red-900/20 transition-all duration-300 flex items-center justify-center gap-3 uppercase tracking-widest active:scale-95 group/btn">
                                <span>Save Config</span>
                                <i class="fas fa-arrow-right text-[10px] group-hover/btn:translate-x-1 transition-transform"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="col-span-full flex flex-col items-center justify-center py-20 bg-white/50 rounded-[3rem] border-2 border-dashed border-gray-200">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-300">
                        <i class="fas fa-folder-open text-3xl"></i>
                    </div>
                    <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">No rooms found in this category</p>
                </div>
                @endforelse
            </div>

            {{-- PAGINATION --}}
            <div class="mt-12 mb-10">
                {{ $rooms->appends(['status' => $statusFilter ?? 'oo'])->links() }}
            </div>

            <footer class="mt-16 text-center">
                <div class="inline-flex flex-col items-center">
                    <div class="h-px w-12 bg-gray-200 mb-4"></div>
                    <p class="text-[10px] text-gray-300 font-bold uppercase tracking-[0.5em]">Hotel SIG Management System v2.1</p>
                </div>
            </footer>
        </main>
    </div>

</body>
</html>