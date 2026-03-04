<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#800000">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">

    <title>{{ __('res.title_page') }} — Hotel SIG</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #F8FAFC; 
            overscroll-behavior-y: contain;
        }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: #800000; border-radius: 10px; }

        .input-underline { 
            background: transparent !important; 
            border-bottom: 2px solid #F1F5F9; 
            transition: all 0.4s ease; 
        }
        .input-underline:focus { 
            border-bottom-color: #800000; 
            outline: none; 
            padding-left: 4px;
        }

        .custom-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23800000'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1.2rem center;
            background-size: 1em;
        }

        .safe-top { padding-top: env(safe-area-inset-top); }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom); }
        [x-cloak] { display: none !important; }

        .glass-card { transition: all 0.3s ease; }
        .glass-card:focus-within { transform: translateY(-4px); }
        
        .status-radio:checked + div {
            background-color: #800000;
            color: white;
            border-color: #800000;
        }

        @media (max-width: 768px) {
            input, select, textarea { font-size: 16px !important; }
        }
    </style>
</head>

<body class="antialiased" x-data="{ mobileMenu: false, openModal: false, bookingStatus: '{{ old('status', 'tentative') }}' }">

    <div class="bg-white safe-top sticky top-0 z-[50] shadow-sm md:static">
        <x-header></x-header>
    </div>

    <div class="flex min-h-screen">
        <aside class="hidden md:block w-72 border-r border-gray-100 bg-white">
            <x-sidebar></x-sidebar>
        </aside>

        <main class="flex-1 p-4 lg:p-10 pb-32">
            @if ($errors->any())
                <div class="max-w-7xl mx-auto mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-2xl">
                    <ul class="list-disc list-inside text-sm text-red-600 font-bold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="reservationForm" action="{{ route('reservations.store') }}" method="POST" 
                  class="flex flex-col lg:flex-row gap-8 lg:gap-12 items-start max-w-7xl mx-auto">
                @csrf
                
                <div class="flex-1 space-y-6 w-full">
                    <div class="mb-2 px-2">
                        <h1 class="text-3xl font-black text-gray-900 tracking-tighter">{{ __('res.heading') }}</h1>
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">{{ __('res.subheading') }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 glass-card">
                            <label class="block text-[10px] font-black text-[#800000] uppercase tracking-[0.2em] mb-2">{{ __('res.label_checkin') }}</label>
                            <div class="relative flex items-center gap-3">
                                <i class="fas fa-calendar-plus text-gray-300"></i>
                                <input type="date" id="arrival_date" name="arrival_date" 
                                    value="{{ old('arrival_date', date('Y-m-d')) }}"
                                    min="{{ date('Y-m-d') }}"
                                    class="bg-transparent border-none p-0 font-bold text-gray-800 focus:ring-0 w-full cursor-pointer" required>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 glass-card">
                            <label class="block text-[10px] font-black text-[#800000] uppercase tracking-[0.2em] mb-2">{{ __('res.label_checkout') }}</label>
                            <div class="relative flex items-center gap-3">
                                <i class="fas fa-calendar-minus text-gray-300"></i>
                                <input type="date" id="departure_date" name="departure_date" 
                                    value="{{ old('departure_date', date('Y-m-d', strtotime('+1 day'))) }}"
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                    class="bg-transparent border-none p-0 font-bold text-gray-800 focus:ring-0 w-full cursor-pointer" required>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-8 md:p-10 rounded-[3rem] shadow-sm border border-gray-50 relative overflow-hidden group">
                        <h3 class="text-xl font-black text-gray-800 mb-8 flex items-center gap-4">
                            <div class="w-10 h-10 bg-red-50 text-[#800000] rounded-2xl flex items-center justify-center">
                                <i class="fas fa-bed"></i>
                            </div>
                            {{ __('res.section_room') }}
                        </h3>

                        <div class="space-y-8 relative z-10">
                            <div class="space-y-3">
                                <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">{{ __('res.field_room_select') }}</label>
                                @php $availableRooms = $rooms->where('status', 'available'); @endphp
                                @if ($availableRooms->count() > 0)
                                    <select name="room_id" class="custom-select w-full bg-gray-50 border-2 border-gray-50 rounded-[1.5rem] p-5 focus:border-[#800000] focus:bg-white transition-all font-bold text-gray-800 cursor-pointer outline-none shadow-inner" required>
                                        <option value="" disabled {{ old('room_id') ? '' : 'selected' }}>— {{ __('res.placeholder_room') }} —</option>
                                        @foreach($rooms as $room)
                                            @if($room->status == 'available')
                                                <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                                    Room {{ $room->room_number }} ({{ $room->type }}) — Rp{{ number_format($room->price, 0, ',', '.') }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                @else
                                    <div class="p-5 bg-red-50 text-[#800000] rounded-2xl border border-red-100 font-bold text-sm flex items-center gap-3">
                                        <i class="fas fa-info-circle"></i> {{ __('res.no_rooms_available') }}
                                    </div>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-3">
                                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">{{ __('res.field_res_type') }}</label>
                                    <select name="reservation_type" class="custom-select w-full bg-gray-50 border-2 border-gray-50 rounded-[1.5rem] p-4 font-bold text-gray-700 outline-none focus:border-[#800000] transition-all">
                                        <option value="non-guaranteed" {{ old('reservation_type') == 'non-guaranteed' ? 'selected' : '' }}>Non-Guaranteed</option>
                                        <option value="guaranteed" {{ old('reservation_type') == 'guaranteed' ? 'selected' : '' }}>Guaranteed (DP)</option>
                                    </select>
                                </div>
                                <div class="space-y-3">
                                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">{{ __('res.field_payment') }}</label>
                                    <select name="payment_method" class="custom-select w-full bg-gray-50 border-2 border-gray-50 rounded-[1.5rem] p-4 font-bold text-gray-700 outline-none focus:border-[#800000] transition-all">
                                        <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="Transfer" {{ old('payment_method') == 'Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="Credit Card" {{ old('payment_method') == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-8 md:p-10 rounded-[3rem] shadow-sm border border-gray-50">
                        <h3 class="text-xl font-black text-gray-800 mb-8 flex items-center gap-4">
                            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-plane-arrival"></i>
                            </div>
                            {{ __('res.section_arrival') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">{{ __('res.field_flight') }}</label>
                                <input type="text" name="flight_detail" value="{{ old('flight_detail') }}" class="w-full bg-gray-50 border-2 border-gray-50 rounded-[1.5rem] p-4 font-bold text-gray-700 outline-none focus:border-[#800000] focus:bg-white transition-all" placeholder="e.g.: GA-123 / 14:00">
                            </div>
                            <div class="space-y-3">
                                <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">{{ __('res.field_pickup') }}</label>
                                <select name="pickup_service" class="custom-select w-full bg-gray-50 border-2 border-gray-50 rounded-[1.5rem] p-4 font-bold text-gray-700 outline-none focus:border-[#800000] transition-all">
                                    <option value="No" {{ old('pickup_service') == 'No' ? 'selected' : '' }}>None</option>
                                    <option value="Airport" {{ old('pickup_service') == 'Airport' ? 'selected' : '' }}>Airport Pickup</option>
                                    <option value="Station" {{ old('pickup_service') == 'Station' ? 'selected' : '' }}>Station Pickup</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-[450px] lg:sticky lg:top-10">
                    <div class="bg-white rounded-[3.5rem] shadow-2xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                        <div class="bg-[#800000] p-10 text-white relative">
                            <div class="relative z-10 flex flex-col gap-5">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-2xl rounded-[1.5rem] flex items-center justify-center border border-white/30 shadow-lg">
                                    <i class="fas fa-user-check text-2xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-black uppercase tracking-tighter italic">{{ __('res.guest_card_title') }}</h2>
                                    <p class="text-[10px] text-red-200 font-bold uppercase tracking-[0.2em]">{{ __('res.guest_card_sub') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-10 space-y-7">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">{{ __('res.field_status') }}</label>
                                <div class="flex p-1 bg-gray-100 rounded-2xl gap-1">
                                    <label class="flex-1 relative cursor-pointer group">
                                        <input type="radio" name="status" value="tentative" class="hidden status-radio" x-model="bookingStatus">
                                        <div class="text-center py-2 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all border border-transparent text-gray-400 group-hover:text-gray-600">Tentative</div>
                                    </label>
                                    <label class="flex-1 relative cursor-pointer group">
                                        <input type="radio" name="status" value="confirmed" class="hidden status-radio" x-model="bookingStatus">
                                        <div class="text-center py-2 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all border border-transparent text-gray-400 group-hover:text-gray-600">Confirmed</div>
                                    </label>
                                </div>
                            </div>

                            <div class="group">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">{{ __('res.field_guest_name') }}</label>
                                <input type="text" name="guest_name" value="{{ old('guest_name') }}" 
                                    class="w-full input-underline py-3 font-extrabold text-xl text-gray-800 placeholder-gray-200 italic" 
                                    placeholder="{{ __('res.placeholder_name') }}" required>
                            </div>

                            <div class="group">
                                <label class="text-[10px] font-black text-[#800000] uppercase tracking-widest block mb-1">{{ __('res.field_id_number') }}</label>
                                <input type="text" name="identity_number" value="{{ old('identity_number') }}" 
                                    class="w-full input-underline py-2 font-bold text-gray-800 placeholder-gray-300 italic" 
                                    placeholder="3171xxxxxxxxxxxx" required>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">{{ __('res.field_num_guests') }}</label>
                                    <input type="number" min="1" name="num_guests" value="{{ old('num_guests', 1) }}" 
                                        class="w-full input-underline py-2 font-extrabold text-lg text-gray-800" required>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">{{ __('res.field_phone') }}</label>
                                    <input type="tel" name="phone" value="{{ old('phone') }}" 
                                        class="w-full input-underline py-2 font-extrabold text-lg text-gray-800" placeholder="08..." required>
                                </div>
                            </div>

                            <div class="group">
                                <label class="text-[10px] font-black text-[#800000] uppercase tracking-widest block mb-1">{{ __('res.field_email') }}</label>
                                <input type="email" name="email" value="{{ old('email') }}" 
                                    class="w-full input-underline py-2 font-bold text-lg text-gray-800 placeholder-gray-300 italic" 
                                    placeholder="guest@example.com" required>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="group">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">{{ __('res.field_country') }}</label>
                                    <input type="text" name="country" value="{{ old('country', 'Indonesia') }}" 
                                        class="w-full input-underline py-2 font-bold text-gray-800" required>
                                </div>
                                <div class="group">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">{{ __('res.field_city') }}</label>
                                    <input type="text" name="city" value="{{ old('city') }}" 
                                        class="w-full input-underline py-2 font-bold text-gray-800" placeholder="Jakarta" required>
                                </div>
                            </div>
                            
                            <div class="group">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">{{ __('res.field_pob') }}</label>
                                <input type="text" name="place_birth" value="{{ old('place_birth') }}" 
                                    class="w-full input-underline py-2 font-bold text-gray-800" placeholder="{{ __('res.placeholder_pob') }}" required>
                            </div>

                            <div class="group">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">{{ __('res.field_remarks') }}</label>
                                <textarea name="remarks" rows="2" class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl p-4 font-bold text-gray-700 outline-none focus:border-[#800000] focus:bg-white transition-all text-sm" placeholder="{{ __('res.placeholder_remarks') }}">{{ old('remarks') }}</textarea>
                            </div>

                            <div class="space-y-4 pt-4">
                                <button type="submit" 
                                    class="w-full bg-[#800000] text-white font-black py-6 rounded-[2rem] shadow-xl shadow-red-900/20 hover:bg-black transition-all duration-500 uppercase tracking-[0.2em] text-[11px] flex items-center justify-center gap-3 active:scale-95">
                                    <span x-text="bookingStatus === 'confirmed' ? '{{ __('res.btn_finalize') }}' : '{{ __('res.btn_save_tentative') }}'"></span>
                                    <i class="fas fa-check-circle text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <p class="text-center mt-8 text-[10px] font-black text-gray-300 uppercase tracking-[0.4em]">
                        &copy; 2026 Hotel SIG PWA System
                    </p>
                </div>
            </form>
        </main>
    </div>

    <x-bottom-nav></x-bottom-nav>
    <x-mobile-menu></x-mobile-menu>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const form = document.getElementById('reservationForm');
        form.addEventListener('submit', function(e) {
            if(!form.checkValidity()){
                return; 
            }

            e.preventDefault();
            Swal.fire({
                title: '{{ __('res.swal_title') }}',
                text: '{{ __('res.swal_text') }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#800000',
                cancelButtonColor: '#CBD5E1',
                confirmButtonText: '{{ __('res.swal_confirm') }}',
                cancelButtonText: '{{ __('res.swal_cancel') }}',
                customClass: { popup: 'rounded-[2.5rem]' }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: '{{ __('res.swal_processing') }}',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    form.submit();
                }
            });
        });

        const arrivalInput = document.getElementById('arrival_date');
        const departureInput = document.getElementById('departure_date');

        arrivalInput.addEventListener('change', function() {
            if (this.value) {
                let nextDay = new Date(this.value);
                nextDay.setDate(nextDay.getDate() + 1);
                
                const yyyy = nextDay.getFullYear();
                const mm = String(nextDay.getMonth() + 1).padStart(2, '0');
                const dd = String(nextDay.getDate()).padStart(2, '0');
                const formattedDate = `${yyyy}-${mm}-${dd}`;
                
                departureInput.min = formattedDate;
                if (departureInput.value <= this.value) {
                    departureInput.value = formattedDate;
                }
            }
        });
    </script>
</body>
</html>