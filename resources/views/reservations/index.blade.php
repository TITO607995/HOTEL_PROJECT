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

    <title>New Reservation - Hotel SIG</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #F8FAFC; 
            color: #1e293b; 
            /* Mencegah bounce scroll di iOS */
            overscroll-behavior-y: contain;
        }
        
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #800000; border-radius: 10px; }

        .input-underline { 
            background-color: transparent !important; 
            border-bottom: 2px solid #F1F5F9; 
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
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
            background-position: right 1rem center;
            background-size: 1.2em;
        }

        /* Safe area untuk Notch iPhone */
        .safe-top { padding-top: env(safe-area-inset-top); }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom); }

        @media (max-width: 768px) {
            .mobile-card-stack { gap: 1.25rem !important; }
            .mobile-padding { padding: 1.25rem !important; }
            input, select { font-size: 16px !important; } /* Mencegah auto-zoom di iPhone */
        }
    </style>
</head>
<body class="antialiased pb-24 md:pb-0">

    <div class="bg-white safe-top sticky top-0 z-[50] shadow-sm md:static">
        <x-header></x-header>
    </div>

    <div class="flex">
        <div class="hidden md:block">
            <x-sidebar></x-sidebar>
        </div>

        <main class="flex-1 p-4 lg:p-10">
            <form id="reservationForm" action="{{ route('reservations.store') }}" method="POST" class="flex flex-col lg:flex-row gap-6 lg:gap-10 items-start max-w-7xl mx-auto">
                @csrf
                
                <div class="flex-1 space-y-6 w-full mobile-card-stack">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white p-5 rounded-[2rem] shadow-sm border border-gray-100 group transition-all duration-500">
                            <div class="flex items-center gap-4">
                                <div class="bg-red-50 p-3 rounded-xl text-[#800000] group-focus-within:bg-[#800000] group-focus-within:text-white transition-all">
                                    <i class="fas fa-calendar-check text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Check In</label>
                                    <input type="date" id="arrival_date" name="arrival_date" value="{{ old('arrival_date', date('Y-m-d')) }}" 
                                        min="{{ date('Y-m-d') }}"
                                        class="w-full border-none focus:ring-0 text-md font-bold text-gray-800 bg-transparent cursor-pointer p-0" required>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-5 rounded-[2rem] shadow-sm border border-gray-100 group transition-all duration-500">
                            <div class="flex items-center gap-4">
                                <div class="bg-red-50 p-3 rounded-xl text-[#800000] group-focus-within:bg-[#800000] group-focus-within:text-white transition-all">
                                    <i class="fas fa-calendar-minus text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Check Out</label>
                                    <input type="date" id="departure_date" name="departure_date" value="{{ old('departure_date', \Carbon\Carbon::now()->addDay()->format('Y-m-d')) }}" 
                                        min="{{ \Carbon\Carbon::now()->addDay()->format('Y-m-d') }}"
                                        class="w-full border-none focus:ring-0 text-md font-bold text-gray-800 bg-transparent cursor-pointer p-0" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 md:p-10 rounded-[2.5rem] shadow-sm border border-gray-100">
                        <div class="flex items-center gap-4 mb-6 md:mb-8">
                            <div class="w-1 h-6 bg-[#800000] rounded-full"></div>
                            <h3 class="text-lg font-extrabold text-gray-800 tracking-tight">Room Selection</h3>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3 ml-1">Pilih Kamar Tersedia</label>
                                @if ($rooms->where('status', 'available')->count() > 0)
                                    <select name="room_id" class="custom-select w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 focus:border-[#800000] focus:ring-2 focus:ring-red-50 transition-all font-bold text-gray-700 cursor-pointer" required>
                                        <option value="" disabled selected>-- Click to search rooms --</option>
                                        @foreach($rooms as $room)
                                            @if($room->status == 'available')
                                                <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                                    Kamar {{ $room->room_number }} — {{ $room->type }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                @else
                                    <div class="w-full bg-red-50 border border-red-100 text-[#800000] px-5 py-4 rounded-2xl font-bold flex items-center gap-3">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <span class="text-sm">Kamar sedang penuh.</span>
                                    </div>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3 ml-1">Reservation Type</label>
                                    <select name="reservation_type" class="custom-select w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 focus:border-[#800000] transition-all font-bold text-sm text-gray-700">
                                        <option value="non-guaranteed">Non-Guaranteed</option>
                                        <option value="guaranteed">Guaranteed (DP)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3 ml-1">Payment Method</label>
                                    <select name="payment_method" class="custom-select w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 focus:border-[#800000] transition-all font-bold text-sm text-gray-700">
                                        <option value="Cash">Cash</option>
                                        <option value="Transfer">Bank Transfer</option>
                                        <option value="Credit Card">Credit Card</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-[450px] lg:sticky lg:top-10">
                    <div class="bg-white rounded-[3rem] shadow-2xl shadow-gray-200 border border-gray-50 overflow-hidden">
                        <div class="bg-[#800000] p-8 md:p-10 text-white relative overflow-hidden">
                            <div class="relative z-10 flex items-center gap-5">
                                <div class="bg-white/10 w-12 h-12 rounded-xl flex items-center justify-center backdrop-blur-md border border-white/20">
                                    <i class="fas fa-id-card text-xl text-white"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-black italic uppercase tracking-tighter leading-none">Registration</h2>
                                    <p class="text-[9px] text-red-200 mt-1 uppercase tracking-widest font-bold">Guest Identification</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-8 md:p-10 space-y-6">
                            <div class="group">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Lead Guest Name</label>
                                <input type="text" name="guest_name" value="{{ old('guest_name') }}" 
                                    class="w-full input-underline py-2 font-bold text-gray-800 placeholder-gray-300 italic" placeholder="Input guest full name..." required>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Guests</label>
                                    <input type="number" min="1" name="num_guests" value="{{ old('num_guests', 1) }}" 
                                        class="w-full input-underline py-2 font-bold text-gray-800" required>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Phone</label>
                                    <input type="tel" name="phone" value="{{ old('phone') }}" 
                                        class="w-full input-underline py-2 font-bold text-gray-800" placeholder="08..." required>
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" 
                                    class="w-full input-underline py-2 font-bold text-gray-800" placeholder="guest@example.com" required>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Country</label>
                                    <input type="text" name="country" value="{{ old('country', 'Indonesia') }}" 
                                        class="w-full input-underline py-2 font-bold text-gray-800">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">City</label>
                                    <input type="text" name="city" value="{{ old('city') }}" 
                                        class="w-full input-underline py-2 font-bold text-gray-800" placeholder="...">
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-[#800000] uppercase tracking-widest block mb-1">Place & Date of Birth</label>
                                <input type="text" name="place_birth" value="{{ old('place_birth') }}" 
                                    class="w-full input-underline py-2 font-bold text-gray-800" placeholder="e.g. Surabaya, 20-10-1990">
                            </div>

                            <button type="submit" 
                                class="w-full mt-4 bg-[#800000] text-white font-black py-5 rounded-3xl shadow-lg hover:bg-black transition-all duration-300 uppercase tracking-[0.2em] text-[10px] flex items-center justify-center gap-3 active:scale-95">
                                <span>Complete Reservation</span>
                                <i class="fas fa-check-circle"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-center mt-6 text-[9px] font-bold text-gray-300 uppercase tracking-[0.3em] pb-10">
                        &copy; 2026 Hotel SIG PWA
                    </p>
                </div>
            </form>
        </main>
    </div>

    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-xl border-t border-gray-100 px-6 py-3 z-[100] safe-bottom flex justify-between items-center shadow-[0_-10px_25px_-5px_rgba(0,0,0,0.05)]">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 text-gray-400">
            <i class="fas fa-th-large text-xl"></i>
            <span class="text-[9px] font-bold uppercase">Home</span>
        </a>
        <a href="{{ route('rooms.index') }}" class="flex flex-col items-center gap-1 text-gray-400">
            <i class="fas fa-bed text-xl"></i>
            <span class="text-[9px] font-bold uppercase">Rooms</span>
        </a>
        <div class="relative -mt-10">
            <div class="w-14 h-14 bg-[#800000] rounded-2xl shadow-xl shadow-red-900/30 flex items-center justify-center text-white border-4 border-[#F8FAFC]">
                <i class="fas fa-plus text-xl"></i>
            </div>
        </div>
        <a href="{{ route('reservations.index') }}" class="flex flex-col items-center gap-1 text-[#800000]">
            <i class="fas fa-clipboard-list text-xl"></i>
            <span class="text-[9px] font-bold uppercase">Orders</span>
        </a>
        <a href="#" class="flex flex-col items-center gap-1 text-gray-400">
            <i class="fas fa-user-circle text-xl"></i>
            <span class="text-[9px] font-bold uppercase">User</span>
        </a>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Logika form & validasi tetap sama seperti kode awal Anda
        const form = document.getElementById('reservationForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Data',
                text: "Apakah data tamu dan reservasi sudah benar?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#800000',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Cek Lagi',
                customClass: { popup: 'rounded-[2rem]' }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sedang Menyimpan...',
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
                const formattedDate = nextDay.toISOString().split('T')[0];
                departureInput.min = formattedDate;
                if (departureInput.value < formattedDate) {
                    departureInput.value = formattedDate;
                }
            }
        });

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Ada Kesalahan!',
                text: 'Periksa kembali kelengkapan data.',
                confirmButtonColor: '#800000',
                customClass: { popup: 'rounded-[2rem]' }
            });
        @endif

        // PWA Service Worker (Opsional namun disarankan)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
</body>
</html>