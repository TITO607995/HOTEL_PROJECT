<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#800000">

    <title>{{ __('rooms.title_page') }} - Hotel SIG</title>
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
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
        [x-cloak] { display: none !important; }
        .room-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>

<body class="bg-[#F3F4F6] antialiased h-screen flex flex-col mb-20 lg:mb-0" 
      x-data="{ openModal: false, mobileMenu: false, activeTab: 'All' }">

    <div class="relative z-[60] pt-[env(safe-area-inset-top)] bg-white lg:bg-transparent">
        <x-header></x-header>
    </div>

    <div class="flex flex-1 overflow-hidden">
        <aside class="h-full flex-shrink-0 border-r border-gray-200 bg-white hidden lg:block w-64">
            <x-sidebar></x-sidebar>
        </aside>

        <main class="flex-1 p-4 md:p-8 lg:p-10 overflow-y-auto custom-scroll">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ __('rooms.heading') }}</h1>
                    <p class="text-gray-500 font-medium">{{ __('rooms.subheading') }}</p>
                </div>
                
                @if(auth()->check() && auth()->user()->role && (auth()->user()->role->name === 'Superadmin' || auth()->user()->role->name === 'SUPERADMIN'))
                <button @click="openModal = true" class="flex bg-maroon text-white px-6 py-3 rounded-2xl font-bold hover:bg-red-900 transition-all items-center gap-2 shadow-lg shadow-red-900/20 active:scale-95">
                    <i class="fas fa-plus-circle"></i>
                    {{ __('rooms.btn_add_room') }}
                </button>
                @endif
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-8">
                <div class="bg-white p-4 md:p-6 rounded-[24px] shadow-sm border border-gray-100 flex items-center gap-3 md:gap-5">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <div>
                        <p class="text-[8px] md:text-xs font-bold text-gray-400 uppercase tracking-widest">{{ __('rooms.stat_total') }}</p>
                        <h4 class="text-lg md:text-2xl font-extrabold text-gray-800">{{ $rooms->total() }}</h4>
                    </div>
                </div>

                <div class="bg-white p-4 md:p-6 rounded-[24px] shadow-sm border border-gray-100 flex items-center gap-3 md:gap-5">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-green-50 text-green-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <p class="text-[8px] md:text-xs font-bold text-gray-400 uppercase tracking-widest">{{ __('rooms.stat_available') }}</p>
                        <h4 class="text-lg md:text-2xl font-extrabold text-gray-800">{{ $rooms->where('status', 'available')->count() }}</h4>
                    </div>
                </div>

                <div class="bg-white p-4 md:p-6 rounded-[24px] shadow-sm border border-gray-100 flex items-center gap-3 md:gap-5">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-red-50 text-red-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fas fa-user-tag"></i>
                    </div>
                    <div>
                        <p class="text-[8px] md:text-xs font-bold text-gray-400 uppercase tracking-widest">{{ __('rooms.stat_occupied') }}</p>
                        <h4 class="text-lg md:text-2xl font-extrabold text-gray-800">{{ $rooms->where('status', 'occupied')->count() }}</h4>
                    </div>
                </div>

                <div class="bg-white p-4 md:p-6 rounded-[24px] shadow-sm border border-gray-100 flex items-center gap-3 md:gap-5">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-yellow-50 text-yellow-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fas fa-broom"></i>
                    </div>
                    <div>
                        <p class="text-[8px] md:text-xs font-bold text-gray-400 uppercase tracking-widest">{{ __('rooms.stat_dirty') }}</p>
                        <h4 class="text-lg md:text-2xl font-extrabold text-gray-800">{{ $rooms->where('status', 'vacant dirty')->count() }}</h4>
                    </div>
                </div>
            </div>

            {{-- Tabs & Search Row --}}
            <div class="flex flex-col xl:flex-row gap-4 mb-6">
                <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1">
                    @foreach(['All', 'Deluxe', 'Superior', 'Suite'] as $tab)
                    <button @click="activeTab = '{{ $tab }}'"
                        :class="activeTab === '{{ $tab }}' ? 'bg-maroon text-white shadow-md' : 'bg-white text-gray-500 border-gray-100'"
                        class="px-6 py-2.5 rounded-xl font-bold border transition-all whitespace-nowrap text-sm">
                        {{ $tab === 'All' ? __('rooms.tab_all') : $tab }}
                    </button>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('rooms.index') }}" class="flex-1 bg-white p-2 rounded-2xl shadow-sm border border-gray-100 flex gap-2">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <i class="fas fa-search text-xs"></i>
                        </span>
                        <input type="text" name="room_number" value="{{ request('room_number') }}" placeholder="{{ __('rooms.search_placeholder') }}" 
                            class="w-full pl-9 pr-4 py-2 bg-gray-50 rounded-xl focus:outline-none focus:ring-2 focus:ring-maroon font-semibold text-sm">
                    </div>
                    <button type="submit" class="bg-maroon text-white px-5 py-2 rounded-xl font-bold text-sm active:scale-95 transition-all">{{ __('rooms.btn_search') }}</button>
                    @if(request('room_number'))
                    <a href="{{ route('rooms.index') }}" class="bg-gray-100 text-gray-500 px-4 py-2 rounded-xl font-bold text-sm flex items-center">{{ __('rooms.btn_reset') }}</a>
                    @endif
                </form>
            </div>

            {{-- Room Grid --}}
            <div class="pb-10">
                @foreach(['All', 'Deluxe', 'Superior', 'Suite'] as $tabType)
                <div x-show="activeTab === '{{ $tabType }}'" x-transition:enter="duration-300">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
                        @php
                            $filteredRooms = ($tabType === 'All') ? $rooms : $rooms->where('type', $tabType);
                        @endphp

                       @forelse($filteredRooms as $room)
                        <div class="room-card group bg-white rounded-[32px] border border-gray-100 flex flex-col h-full hover:shadow-xl overflow-hidden">
                            <div class="relative h-40 overflow-hidden m-2 rounded-[24px]">
                                <img src="{{ $room->foto_display }}" 
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                                    alt="Room {{ $room->room_number }}">
                                
                                <div class="absolute top-3 right-3 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider text-gray-800 italic">
                                    {{ $room->type }}
                                </div>
                            </div>

                            <div class="p-5 flex flex-col justify-between flex-1">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="text-xl font-extrabold text-gray-800 tracking-tighter">{{ $room->room_number }}</h3>
                                    <div class="text-right">
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest leading-none">{{ __('rooms.label_price') }}</p>
                                        <p class="text-md font-black text-maroon leading-tight">Rp{{ number_format($room->price, 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                                    @php
                                        $status = strtolower($room->status);
                                        $statusConfig = match($status) {
                                            'available'    => ['class' => 'bg-green-100 text-green-600', 'icon' => 'fa-check-circle'],
                                            'occupied'     => ['class' => 'bg-red-100 text-red-600', 'icon' => 'fa-user-check'],
                                            'booked'       => ['class' => 'bg-orange-100 text-orange-600', 'icon' => 'fa-calendar-alt'],
                                            'vacant dirty' => ['class' => 'bg-yellow-100 text-yellow-700', 'icon' => 'fa-broom'],
                                            default        => ['class' => 'bg-gray-100 text-gray-600', 'icon' => 'fa-question'],
                                        };
                                    @endphp

                                    <div class="{{ $statusConfig['class'] }} px-4 py-1.5 rounded-lg text-[10px] font-extrabold uppercase tracking-widest flex items-center gap-2">
                                        <i class="fas {{ $statusConfig['icon'] }}"></i>
                                        {{ strtoupper(__('rooms.status_' . str_replace(' ', '_', $status))) }}
                                    </div>
                                    
                                    @if(auth()->check() && auth()->user()->role && in_array(strtoupper(auth()->user()->role->name), ['SUPERADMIN']))
                                    <button class="w-10 h-10 rounded-full hover:bg-gray-50 text-gray-300 hover:text-maroon transition-all">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full text-center py-20 bg-white rounded-[32px] border border-dashed border-gray-200">
                            <p class="text-gray-400 font-medium italic">{{ __('rooms.empty_state') }}</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-4 mb-24">
                {{ $rooms->appends(request()->query())->links() }}
            </div>

        </main>
    </div>

    {{-- MODAL ADD ROOM --}}
    @if(auth()->check() && auth()->user()->role && (auth()->user()->role->name === 'Superadmin' || auth()->user()->role->name === 'SUPERADMIN'))
    <div x-show="openModal" 
         class="fixed inset-0 z-[100] overflow-y-auto" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
        
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="openModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-[32px] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">{{ __('rooms.modal_title') }}</h3>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form action="{{ route('rooms.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">{{ __('rooms.field_number') }}</label>
                            <input type="text" name="room_number" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-maroon outline-none">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">{{ __('rooms.field_type') }}</label>
                                <select name="type" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none">
                                    <option value="Deluxe">Deluxe</option>
                                    <option value="Superior">Superior</option>
                                    <option value="Suite">Suite</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">{{ __('rooms.field_price') }}</label>
                                <input type="number" name="price" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">{{ __('rooms.field_image') }}</label>
                            <input type="file" name="image" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-maroon hover:file:bg-red-100">
                        </div>
                    </div>

                    <div class="mt-8 flex gap-3">
                        <button type="button" @click="openModal = false" class="flex-1 px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-2xl hover:bg-gray-200 transition-all">{{ __('rooms.btn_cancel') }}</button>
                        <button type="submit" class="flex-1 px-6 py-3 bg-maroon text-white font-bold rounded-2xl hover:bg-red-900 shadow-lg shadow-red-900/20 transition-all">{{ __('rooms.btn_save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <x-bottom-nav></x-bottom-nav>
    <x-mobile-menu></x-mobile-menu>

</body>
</html>