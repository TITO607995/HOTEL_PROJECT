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

    <title>New Reservation — Hotel SIG</title>
    
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

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: #800000; border-radius: 10px; }

        /* Input Underline Style */
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

        /* Select Custom Arrow */
        .custom-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23800000'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1.2rem center;
            background-size: 1em;
        }

        /* Utility */
        .safe-top { padding-top: env(safe-area-inset-top); }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom); }
        [x-cloak] { display: none !important; }

        .glass-card { transition: all 0.3s ease; }
        .glass-card:focus-within { transform: translateY(-4px); }
        
        @media (max-width: 768px) {
            input, select { font-size: 16px !important; }
        }
    </style>
</head>

<body class="antialiased" x-data="{ mobileMenu: false, openModal: false }">

    <div class="bg-white safe-top sticky top-0 z-[50] shadow-sm md:static">
        <x-header></x-header>
    </div>

    <div class="flex min-h-screen">
        <aside class="hidden md:block w-72 border-r border-gray-100 bg-white">
            <x-sidebar></x-sidebar>
        </aside>

        <main class="flex-1 p-4 lg:p-10 pb-32">
            <form id="reservationForm" action="{{ route('reservations.store') }}" method="POST" 
                  class="flex flex-col lg:flex-row gap-8 lg:gap-12 items-start max-w-7xl mx-auto">
                @csrf
                
                <div class="flex-1 space-y-6 w-full">
                    <div class="mb-2 px-2">
                        <h1 class="text-3xl font-black text-gray-900 tracking-tighter">Reservasi Baru</h1>
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Sistem Manajemen Kamar</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 glass-card">
                            <label class="block text-[10px] font-black text-[#800000] uppercase tracking-[0.2em] mb-2">Check In</label>
                            <div class="relative flex items-center gap-3">
                                <i class="fas fa-calendar-plus text-gray-300"></i>
                                <input type="date" id="arrival_date" name="arrival_date" 
                                    value="{{ old('arrival_date', date('Y-m-d')) }}"
                                    min="{{ date('Y-m-d') }}"
                                    class="bg-transparent border-none p-0 font-bold text-gray-800 focus:ring-0 w-full cursor-pointer" required>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 glass-card">
                            <label class="block text-[10px] font-black text-[#800000] uppercase tracking-[0.2em] mb-2">Check Out</label>
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
                            Konfigurasi Kamar
                        </h3>

                        <div class="space-y-8 relative z-10">
                            <div class="space-y-3">
                                <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Pilih Kamar Tersedia</label>
                                @if ($rooms->where('status', 'available')->count() > 0)
                                    <select name="room_id" class="custom-select w-full bg-gray-50 border-2 border-gray-50 rounded-[1.5rem] p-5 focus:border-[#800000] focus:bg-white transition-all font-bold text-gray-800 cursor-pointer outline-none shadow-inner" required>
                                        <option value="" disabled selected>— Cari Nomor Kamar —</option>
                                        @foreach($rooms as $room)
                                            @if($room->status == 'available')
                                                <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                                    Kamar {{ $room->room_number }} ({{ $room->type }}) — Rp{{ number_format($room->price, 0, ',', '.') }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                @else
                                    <div class="p-5 bg-red-50 text-[#800000] rounded-2xl border border-red-100 font-bold text-sm flex items-center gap-3">
                                        <i class="fas fa-info-circle"></i> Maaf, saat ini tidak ada kamar tersedia.
                                    </div>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-3">
                                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Tipe Reservasi</label>
                                    <select name="reservation_type" class="custom-select w-full bg-gray-50 border-2 border-gray-50 rounded-[1.5rem] p-4 font-bold text-gray-700 outline-none focus:border-[#800000] transition-all">
                                        <option value="non-guaranteed">Non-Guaranteed</option>
                                        <option value="guaranteed">Guaranteed (DP)</option>
                                    </select>
                                </div>
                                <div class="space-y-3">
                                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Metode Pembayaran</label>
                                    <select name="payment_method" class="custom-select w-full bg-gray-50 border-2 border-gray-50 rounded-[1.5rem] p-4 font-bold text-gray-700 outline-none focus:border-[#800000] transition-all">
                                        <option value="Cash">Tunai (Cash)</option>
                                        <option value="Transfer">Bank Transfer</option>
                                        <option value="Credit Card">Credit Card</option>
                                    </select>
                                </div>
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
                                    <h2 class="text-2xl font-black uppercase tracking-tighter italic">Guest Data</h2>
                                    <p class="text-[10px] text-red-200 font-bold uppercase tracking-[0.2em]">Identitas Pemesan Utama</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-10 space-y-7">
                            <div class="group">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Nama Lengkap Tamu</label>
                                <input type="text" name="guest_name" value="{{ old('guest_name') }}" 
                                    class="w-full input-underline py-3 font-extrabold text-xl text-gray-800 placeholder-gray-200 italic" 
                                    placeholder="Input nama sesuai KTP..." required>
                            </div>

                            <div class="group">
                                <label class="text-[10px] font-black text-[#800000] uppercase tracking-widest block mb-1">Tempat & Tanggal Lahir</label>
                                <input type="text" name="place_birth" value="{{ old('place_birth') }}" 
                                    class="w-full input-underline py-2 font-bold text-gray-800 placeholder-gray-300 italic" 
                                    placeholder="Contoh: Surabaya, 12-05-1995" required>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Jumlah Tamu</label>
                                    <input type="number" min="1" name="num_guests" value="{{ old('num_guests', 1) }}" 
                                        class="w-full input-underline py-2 font-extrabold text-lg text-gray-800" required>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">No. WhatsApp</label>
                                    <input type="tel" name="phone" value="{{ old('phone') }}" 
                                        class="w-full input-underline py-2 font-extrabold text-lg text-gray-800" placeholder="08..." required>
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" 
                                    class="w-full input-underline py-2 font-extrabold text-lg text-gray-800" placeholder="guest@example.com" required>
                            </div>

                            <div class="space-y-4 pt-4">
                                <button type="submit" 
                                    class="w-full bg-[#800000] text-white font-black py-6 rounded-[2rem] shadow-xl shadow-red-900/20 hover:bg-black transition-all duration-500 uppercase tracking-[0.2em] text-[11px] flex items-center justify-center gap-3 active:scale-95">
                                    <span>Simpan Reservasi</span>
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
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Booking',
                text: "Apakah data reservasi sudah sesuai?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#800000',
                cancelButtonColor: '#CBD5E1',
                confirmButtonText: 'Ya, Selesaikan!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-[2.5rem]' }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
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

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
</body>
</html>