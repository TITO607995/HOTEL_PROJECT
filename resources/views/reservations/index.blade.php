<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Reservation - Hotel SIG</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #F8FAFC;
        }

        /* Custom scrollbar biar tetep estetik */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #800000; border-radius: 10px; }

        .input-underline {
            background-color: transparent !important;
            border-bottom: 2px solid #F1F5F9;
            transition: all 0.3s ease;
        }

        .input-underline:focus {
            border-bottom-color: #800000;
            outline: none;
        }
    </style>
</head>
<body class="antialiased">

    <x-header></x-header>

    <div class="flex">
        <x-sidebar></x-sidebar>


            <form action="{{ route('reservations.store') }}" method="POST" class="flex flex-col lg:flex-row gap-10 items-start">
                @csrf
                
                <div class="flex-1 space-y-8 w-full">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white p-7 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-md transition-all group">
                            <div class="flex items-center gap-5">
                                <div class="bg-red-50 p-4 rounded-2xl text-[#800000] group-hover:bg-[#800000] group-hover:text-white transition-colors">
                                    <i class="fas fa-calendar-check text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Check In Date</label>
                                    <input type="date" name="arrival_date" value="{{ old('arrival_date') }}" 
                                        class="w-full border-none focus:ring-0 text-lg font-bold text-gray-800 bg-transparent cursor-pointer" required>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-7 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-md transition-all group">
                            <div class="flex items-center gap-5">
                                <div class="bg-red-50 p-4 rounded-2xl text-[#800000] group-hover:bg-[#800000] group-hover:text-white transition-colors">
                                    <i class="fas fa-calendar-minus text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Check Out Date</label>
                                    <input type="date" name="departure_date" value="{{ old('departure_date', \Carbon\Carbon::now()->addDay()->format('Y-m-d')) }}" 
                                        class="w-full border-none focus:ring-0 text-lg font-bold text-gray-800 bg-transparent cursor-pointer" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-gray-100">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-2 h-8 bg-[#800000] rounded-full"></div>
                            <h3 class="text-lg font-bold text-gray-800 tracking-tight">Room Selection</h3>
                        </div>

                        <div class="space-y-8">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Pilih Kamar Tersedia</label>
                                <select name="room_id" class="w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 focus:border-[#800000] focus:ring-0 transition-all font-bold text-gray-700 appearance-none cursor-pointer" required>
                                    <option value="">-- Click to search available rooms --</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                            Kamar {{ $room->room_number }} — {{ $room->type }} (Rp{{ number_format($room->price, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Reservation Type</label>
                                    <select name="reservation_type" class="w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 focus:border-[#800000] focus:ring-0 transition-all font-bold text-sm text-gray-700">
                                        <option value="non-guaranteed">Non-Guaranteed</option>
                                        <option value="guaranteed">Guaranteed (DP)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Payment Method</label>
                                    <select name="payment_method" class="w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 focus:border-[#800000] focus:ring-0 transition-all font-bold text-sm text-gray-700">
                                        <option value="Cash">Cash</option>
                                        <option value="Transfer">Bank Transfer</option>
                                        <option value="Credit Card">Credit Card</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-[480px] lg:sticky lg:top-10">
                    <div class="bg-white rounded-[3rem] shadow-2xl shadow-gray-200 border border-gray-100 overflow-hidden">
                        <div class="bg-[#800000] p-10 text-white relative">
                            <div class="absolute top-0 right-0 opacity-10 p-6">
                                <i class="fas fa-hotel text-7xl"></i>
                            </div>
                            <div class="relative z-10 flex items-center gap-4">
                                <div class="bg-white/10 w-14 h-14 rounded-2xl flex items-center justify-center backdrop-blur-md border border-white/20">
                                    <i class="fas fa-id-card text-2xl text-white"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-black italic uppercase tracking-tighter leading-none">Registration Card</h2>
                                    <p class="text-[10px] text-red-200 mt-1 uppercase tracking-widest font-bold">Guest Identification</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-10 space-y-7">
                            <div class="group">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Lead Guest Name</label>
                                <input type="text" name="guest_name" value="{{ old('guest_name') }}" 
                                    class="w-full input-underline py-2 font-bold text-gray-800 placeholder-gray-300 italic" placeholder="Input guest full name..." required>
                            </div>

                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Guests</label>
                                    <input type="number" name="num_guests" value="{{ old('num_guests', 1) }}" 
                                        class="w-full input-underline py-2 font-bold text-gray-800" required>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Phone Number</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" 
                                        class="w-full input-underline py-2 font-bold text-gray-800" placeholder="08..." required>
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Email Address</label>
                                <input type="email" name="email" value="{{ old('email') }}" 
                                    class="w-full input-underline py-2 font-bold text-gray-800" placeholder="guest@example.com" required>
                            </div>

                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Country</label>
                                    <input type="text" name="country" value="{{ old('country', 'Indonesia') }}" 
                                        class="w-full input-underline py-2 font-bold text-gray-800">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">City</label>
                                    <input type="text" name="city" value="{{ old('city') }}" 
                                        class="w-full input-underline py-2 font-bold text-gray-800" placeholder="City name...">
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-[#800000] uppercase tracking-widest block mb-1">Place & Date of Birth</label>
                                <input type="text" name="place_birth" value="{{ old('place_birth') }}" 
                                    class="w-full input-underline py-2 font-bold text-gray-800" placeholder="e.g. Surabaya, 20-10-1990">
                            </div>

                            <button type="submit" 
                                class="w-full mt-6 bg-[#800000] text-white font-black py-5 rounded-3xl shadow-xl hover:bg-black hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 uppercase tracking-[0.2em] text-xs flex items-center justify-center gap-3">
                                <span>Complete Reservation</span>
                                <i class="fas fa-check-circle text-sm"></i>
                            </button>
                        </div>
                    </div>
                    
                    <p class="text-center mt-6 text-[10px] font-bold text-gray-300 uppercase tracking-widest">
                        &copy; 2026 Hotel SIG Management System
                    </p>
                </div>
            </form>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Opps!',
                text: 'Pastikan semua data terisi dengan benar.',
                confirmButtonColor: '#800000',
                customClass: { popup: 'rounded-[2rem]' }
            });
        @endif
    </script>
</body>
</html>