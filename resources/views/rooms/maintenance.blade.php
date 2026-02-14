<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>Maintenance - Hotel SIG</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-blur: 10px; }
        .custom-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23800000'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1rem; }
    </style>
</head>
<body class="bg-[#F8F9FA] min-h-screen">

    <x-header></x-header>

    <div class="flex">
        <aside class="w-72 bg-white border-r border-gray-100 min-h-screen fixed h-full z-10">
            <x-sidebar></x-sidebar>
        </aside>

        <main class="flex-1 ml-72 p-8 lg:p-12">
            
            <div class="flex justify-between items-center mb-10">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="h-1 w-8 bg-red-600 rounded-full"></span>
                        <span class="text-[10px] font-black text-red-600 uppercase tracking-[0.3em]">Engineering Department</span>
                    </div>
                    <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">Maintenance & <span class="text-[#800000]">Room Blocking</span></h1>
                    <p class="text-gray-400 text-sm mt-1 font-medium italic">Atur ketersediaan unit untuk perbaikan (OO) atau pembersihan (OS).</p>
                </div>
                
                <div class="flex gap-3">
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 text-center min-w-[100px]">
                        <span class="block text-[10px] font-bold text-gray-400 uppercase">Out of Order</span>
                        <span class="text-xl font-black text-red-600">{{ $rooms->where('status', 'oo')->count() }}</span>
                    </div>
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 text-center min-w-[100px]">
                        <span class="block text-[10px] font-bold text-gray-400 uppercase">Out of Service</span>
                        <span class="text-xl font-black text-orange-500">{{ $rooms->where('status', 'os')->count() }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50 text-gray-400 text-[10px] font-black uppercase tracking-widest border-b border-gray-100">
                            <th class="px-8 py-6">Room Number</th>
                            <th class="px-8 py-6">Live Status</th>
                            <th class="px-8 py-6">Change Status</th>
                            <th class="px-8 py-6">Notes / Reason</th>
                            <th class="px-8 py-6 text-center">Save Changes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($rooms as $room)
                        <tr class="group hover:bg-red-50/30 transition-all duration-300">
                            <td class="px-8 py-6">
                                <span class="text-xl font-black text-gray-800 group-hover:text-[#800000]">{{ $room->room_number }}</span>
                            </td>
                            <td class="px-8 py-6">
                                @php
                                    $statusClasses = [
                                        'available' => 'bg-green-100 text-green-600',
                                        'oo' => 'bg-red-100 text-red-600',
                                        'os' => 'bg-orange-100 text-orange-600',
                                        'occupied' => 'bg-blue-100 text-blue-600'
                                    ];
                                    $currentClass = $statusClasses[$room->status] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $currentClass }}">
                                    {{ $room->status }}
                                </span>
                            </td>
                            
                            <form action="{{ route('rooms.maintenance.update', $room->id) }}" method="POST">
                                @csrf
                                <td class="px-8 py-6">
                                    <select name="status" class="custom-select w-full bg-gray-50 border border-gray-200 text-gray-700 text-xs font-bold rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-200 focus:border-[#800000] outline-none transition-all">
                                        <option value="available" {{ $room->status == 'available' ? 'selected' : '' }}>Available (Normal)</option>
                                        <option value="oo" {{ $room->status == 'oo' ? 'selected' : '' }}>Out of Order (OO)</option>
                                        <option value="os" {{ $room->status == 'os' ? 'selected' : '' }}>Out of Service (OS)</option>
                                        <option value="vacant dirty" {{ $room->status == 'vacant dirty' ? 'selected' : '' }}>Vacant Dirty</option>
                                    </select>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="relative">
                                        <i class="fas fa-pen-nib absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
                                        <input type="text" name="notes" value="{{ $room->maintenance_notes }}" 
                                               placeholder="Keterangan perbaikan..." 
                                               class="w-full bg-gray-50 border border-gray-200 text-gray-700 text-xs rounded-xl pl-10 pr-4 py-3 focus:ring-2 focus:ring-red-200 focus:border-[#800000] outline-none transition-all">
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <button type="submit" class="inline-flex items-center gap-2 bg-[#800000] text-white text-[10px] font-black px-6 py-3 rounded-xl hover:bg-red-900 hover:shadow-lg hover:shadow-red-200 transition-all active:scale-95 uppercase tracking-widest">
                                        <i class="fas fa-save"></i> Update
                                    </button>
                                </td>
                            </form>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="mt-10 text-center text-[10px] text-gray-300 font-bold uppercase tracking-[0.5em]">Engineering Module v1.0</p>
        </main>
    </div>

</body>
</html>