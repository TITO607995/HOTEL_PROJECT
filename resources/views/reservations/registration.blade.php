<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>Check-in System | Hotel SIG</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F9FAFB; }
        .glass-header { background: rgba(255, 255, 255, 0.8); backdrop-blur: 10px; }
        .card-shadow { shadow-[0_20px_50px_rgba(0,0,0,0.02)]; }
        /* Animasi halus untuk button */
        .btn-checkin { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .btn-checkin:hover { transform: translateY(-3px); box-shadow: 0 10px 20px -5px rgba(128, 0, 0, 0.3); }
    </style>
</head>
<body class="min-h-screen">

    <x-header></x-header>

    <div class="flex">
        <aside class="w-72 bg-white border-r border-gray-100 min-h-screen fixed h-full z-10">
            <x-sidebar></x-sidebar>
        </aside>

        <div class="flex-1 ml-72 p-10 lg:p-14">
            
            <div class="max-w-6xl mx-auto mb-12">
                <div class="flex items-end justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="h-1.5 w-10 bg-[#800000] rounded-full"></span>
                            <span class="text-[10px] font-black text-[#800000] uppercase tracking-[0.4em]">Reception Desk</span>
                        </div>
                        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">
                            Registrasi <span class="text-[#800000]">Kedatangan</span>
                        </h1>
                        <p class="text-gray-400 text-sm mt-2 font-medium">Verifikasi data tamu dan aktivasi kunci kamar secara real-time.</p>
                    </div>
                    
                    <div class="hidden md:flex bg-white p-2 rounded-2xl border border-gray-100 shadow-sm items-center gap-3 pr-6">
                        <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-[#800000]">
                            <i class="fas fa-bell animate-swing"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Antrean Hari Ini</span>
                            <span class="text-sm font-black text-gray-800">{{ $reservations->count() }} Tamu Menunggu</span>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="max-w-6xl mx-auto mb-8 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-3 animate-fade-in-down">
                    <i class="fas fa-check-circle"></i>
                    <span class="text-xs font-bold uppercase tracking-wider">{{ session('success') }}</span>
                </div>
            @endif

            <div class="max-w-6xl mx-auto bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50 text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] border-b border-gray-100">
                            <th class="px-8 py-6">Informasi Tamu</th>
                            <th class="px-8 py-6 text-center">Unit Kamar</th>
                            <th class="px-8 py-6">Status Pembayaran</th>
                            <th class="px-8 py-6 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($reservations as $res)
                        <tr class="group hover:bg-gray-50/80 transition-all duration-300">
                            <td class="px-8 py-7">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-[#800000] group-hover:text-white group-hover:rotate-6 transition-all duration-500">
                                        <i class="fas fa-user-tag text-lg"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800 text-base">{{ $res->guest_name }}</div>
                                        <span class="text-[9px] font-black uppercase tracking-[0.15em] {{ $res->reservation_type == 'guaranteed' ? 'text-emerald-500' : 'text-amber-500' }}">
                                            <i class="fas fa-circle text-[6px] mr-1 mb-0.5"></i>{{ $res->reservation_type }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-8 py-7">
                                <div class="flex flex-col items-center">
                                    <span class="text-2xl font-black text-gray-900 group-hover:text-[#800000] transition-colors">
                                        {{ $res->room->room_number }}
                                    </span>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                                        {{ $res->room->type }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-8 py-7">
                                <div class="inline-flex items-center gap-3 bg-gray-50 px-4 py-2 rounded-xl border border-gray-100">
                                    <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center">
                                        <i class="fas fa-wallet text-[#800000] text-xs"></i>
                                    </div>
                                    <div>
                                        <span class="block text-[8px] font-bold text-gray-400 uppercase leading-none mb-1">Via</span>
                                        <span class="text-xs font-black text-gray-700 tracking-tight">{{ $res->payment_method }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-8 py-7 text-right">
                                @if($res->reservation_type == 'non-guaranteed')
                                    <button onclick="openPaymentModal({{ $res->id }}, '{{ $res->guest_name }}')" 
                                        class="btn-checkin bg-blue-600 text-white text-[10px] font-black px-6 py-3.5 rounded-xl shadow-lg shadow-blue-100 uppercase tracking-widest inline-flex items-center gap-2">
                                        <i class="fas fa-hand-holding-usd"></i> Settlement & In
                                    </button>
                                @else
                                    <form action="{{ route('reservations.checkin', $res->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" 
                                            class="btn-checkin bg-[#800000] text-white text-[10px] font-black px-6 py-3.5 rounded-xl shadow-lg shadow-red-100 uppercase tracking-widest inline-flex items-center gap-2">
                                            <i class="fas fa-key-skeleton"></i> Activate Room
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-32 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-calendar-check text-3xl text-gray-200"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-400 uppercase tracking-widest">Clear Schedule</h3>
                                    <p class="text-gray-300 text-sm italic">Tidak ada tamu yang menunggu check-in saat ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="max-w-6xl mx-auto mt-10 flex justify-between items-center opacity-40">
                <p class="text-[9px] font-black text-gray-500 uppercase tracking-[0.5em]">Hotel SIG Management v2.1</p>
                <div class="h-px flex-1 mx-8 bg-gray-200"></div>
                <p class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em]">Front Office Module</p>
            </div>
        </div>
    </div>

    <script>
        function openPaymentModal(id, name) {
            // Animasi sederhana sebelum membuka modal (opsional)
            console.log('Processing payment for:', name);
            alert('Lanjutkan ke modul pembayaran untuk tamu: ' + name);
        }
    </script>
</body>
</html>