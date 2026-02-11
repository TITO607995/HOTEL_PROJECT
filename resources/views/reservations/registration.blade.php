<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Check-in System</title>
    <style>
        /* Memastikan font terlihat modern */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<x-header></x-header>
<body class="bg-gray-100">

    <div class="flex">
        
        <x-sidebar></x-sidebar>

        <div class="flex-1 ml-64 min-h-screen flex flex-col">
            
            

                <div class="max-w-6xl mx-auto mb-8">
                    <h1 class="text-3xl font-black text-[#800000] uppercase italic tracking-tighter">
                        Registrasi Kedatangan <span class="text-gray-400 font-light">(Check-in)</span>
                    </h1>
                </div>

                @if(session('success'))
                    <div class="max-w-6xl mx-auto mb-6 p-4 bg-green-500 text-white rounded-2xl shadow-lg flex items-center gap-3">
                        <span>✅</span> {{ session('success') }}
                    </div>
                @endif

                <div class="max-w-6xl mx-auto bg-white rounded-[30px] shadow-2xl overflow-hidden border border-gray-100">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="p-6 text-[11px] font-black uppercase text-gray-400 tracking-widest">Info Tamu</th>
                                <th class="p-6 text-[11px] font-black uppercase text-gray-400 tracking-widest text-center">Nomor Kamar</th>
                                <th class="p-6 text-[11px] font-black uppercase text-gray-400 tracking-widest">Status Pembayaran</th>
                                <th class="p-6 text-[11px] font-black uppercase text-gray-400 tracking-widest text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($reservations as $res)
                            <tr class="hover:bg-red-50/20 transition-all">
                                <td class="p-6">
                                    <div class="font-black text-gray-800 text-base mb-1 uppercase tracking-tight">{{ $res->guest_name }}</div>
                                    <span class="text-[9px] px-3 py-1 rounded-full font-black uppercase tracking-widest {{ $res->reservation_type == 'guaranteed' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                        {{ $res->reservation_type }}
                                    </span>
                                </td>

                                <td class="p-6 text-center">
                                    <div class="inline-flex items-center justify-center bg-[#800000] text-white w-12 h-12 rounded-2xl font-black shadow-lg shadow-red-200">
                                        {{ $res->room->room_number }}
                                    </div>
                                </td>

                                <td class="p-6">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase">Metode</span>
                                        <span class="text-sm font-black text-gray-700">{{ $res->payment_method }}</span>
                                    </div>
                                </td>

                                <td class="p-6">
                                    <div class="flex justify-center">
                                        @if($res->reservation_type == 'non-guaranteed')
                                            <button onclick="openPaymentModal({{ $res->id }}, '{{ $res->guest_name }}')" 
                                                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black px-6 py-3 rounded-2xl shadow-xl shadow-blue-100 transition-all active:scale-95 uppercase tracking-wider">
                                                💳 Bayar & In
                                            </button>
                                        @else
                                            <form action="{{ route('reservations.checkin', $res->id) }}" method="POST" class="w-full flex justify-center">
                                                @csrf
                                                <button type="submit" 
                                                    class="flex items-center gap-2 bg-[#800000] hover:bg-black text-white text-xs font-black px-6 py-3 rounded-2xl shadow-xl shadow-red-100 transition-all active:scale-95 uppercase tracking-wider">
                                                    🔑 Check-In
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    @if($reservations->isEmpty())
                        <div class="p-20 text-center">
                            <span class="text-6xl block mb-4">🏨</span>
                            <div class="text-gray-400 font-black text-lg uppercase italic">Kosong Melompong, Bos!</div>
                            <p class="text-gray-300 text-sm">Belum ada tamu yang daftar hari ini.</p>
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

</body>
</html>