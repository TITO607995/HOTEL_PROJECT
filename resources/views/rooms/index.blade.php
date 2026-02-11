<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    
</body>
</html>
<x-header></x-header>
<x-sidebar></x-sidebar>
        <main class="flex-1 ml-64 p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($rooms as $room)
                <div class="bg-white rounded-[30px] shadow-xl overflow-hidden border border-gray-100 flex flex-col h-full">
                    <div class="h-40 overflow-hidden">
                        <img src="{{ asset('storage/rooms/' . ($room->image ?? 'default.jpg')) }}" 
                             alt="Room Image" class="w-full h-full object-cover">
                    </div>

                    <div class="p-5 flex flex-col justify-between flex-1">
                        <div>
                            <h3 class="text-xl font-black text-gray-800 uppercase">ROOM {{ $room->room_number }}</h3>
                            <p class="text-[10px] text-gray-500 font-bold mb-4 italic">type : {{ $room->type }}</p>
                            <p class="text-sm font-black text-[#800000] mb-4">Rp {{ number_format($room->price, 0, ',', '.') }}</p>
                        </div>

                        <div>
                            @php
                                $statusClasses = [
                                    'booking' => 'bg-orange-400',
                                    'occupied' => 'bg-red-500',
                                    'available' => 'bg-green-500',
                                    'vacant dirty' => 'bg-yellow-600',
                                ];
                                $color = $statusClasses[$room->status] ?? 'bg-gray-400';
                            @endphp
                            <span class="{{ $color }} text-white text-[10px] font-black px-6 py-1.5 rounded-full inline-block uppercase shadow-md">
                                {{ $room->status }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </main>
    </div>