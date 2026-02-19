<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>Payment Settlement | Hotel SIG</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F9FAFB; }
        /* Warna Merah Khas Hotel SIG */
        .price-card { background: linear-gradient(135deg, #800000 0%, #4a0000 100%); }
        .text-sig { color: #800000; }
        .bg-sig { background-color: #800000; }
    </style>
</head>
<body class="min-h-screen">

    <x-header></x-header>

    <div class="flex">
        <aside class="w-72 bg-white border-r border-gray-100 min-h-screen fixed h-full z-10">
            <x-sidebar></x-sidebar>
        </aside>

        <div class="flex-1 ml-72 p-10 lg:p-14">
            
            <div class="max-w-4xl mx-auto mb-12">
                <div class="flex items-center gap-2 mb-3">
                    <span class="h-1.5 w-10 bg-sig rounded-full"></span>
                    <span class="text-[10px] font-black text-sig uppercase tracking-[0.4em]">Cashier Desk</span>
                </div>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">
                    Penyelesaian <span class="text-sig">Pembayaran</span>
                </h1>
                <p class="text-gray-400 text-sm mt-2 font-medium">Selesaikan administrasi untuk unit <b>Room {{ $reservation->room->room_number }}</b>.</p>
            </div>

            <div class="max-w-4xl mx-auto">
                <form action="{{ route('payments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                    
                    {{-- Pastikan total_price terhitung --}}
                    @php 
                        $totalHarga = $reservation->total_price ?? ($reservation->room->price * ($nights ?? 1));
                    @endphp
                    <input type="hidden" name="total_amount" value="{{ $totalHarga }}">

                    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-10 mb-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            
                            <div class="space-y-8">
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-3">Informasi Reservasi</label>
                                    <div class="p-5 bg-gray-50 rounded-3xl border border-gray-100">
                                        <div class="flex items-center gap-4 mb-4">
                                            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-sig shadow-sm border border-gray-100">
                                                <i class="fas fa-user-tie"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-800">{{ $reservation->guest_name }}</div>
                                                <div class="text-[10px] font-black text-sig uppercase tracking-tight">{{ $reservation->room->type }}</div>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4 border-t border-gray-200 pt-4">
                                            <div>
                                                <span class="block text-[9px] font-bold text-gray-400 uppercase">Durasi</span>
                                                <span class="text-xs font-bold text-gray-700">{{ $nights ?? 1 }} Malam</span>
                                            </div>
                                            <div>
                                                <span class="block text-[9px] font-bold text-gray-400 uppercase">Rate / Malam</span>
                                                <span class="text-xs font-bold text-gray-700">Rp {{ number_format($reservation->room->price, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-3">Metode Pembayaran</label>
                                    <div class="relative">
                                        <select name="payment_method" class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl text-sm font-bold appearance-none focus:bg-white focus:border-[#800000] outline-none transition-all cursor-pointer">
                                            <option value="Cash">Cash (Tunai)</option>
                                            <option value="Debit Card">Debit Card</option>
                                            <option value="Credit Card">Credit Card</option>
                                            <option value="QRIS">QRIS / E-Wallet</option>
                                        </select>
                                        <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                            <i class="fas fa-chevron-down text-xs"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="price-card rounded-[2.5rem] p-10 text-white flex flex-col justify-between shadow-2xl shadow-red-900/20 relative overflow-hidden">
                                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
                                
                                <div class="relative z-10">
                                    <div class="flex justify-between items-start mb-8">
                                        <i class="fas fa-receipt text-4xl opacity-50"></i>
                                        <span class="bg-white/20 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest">Billing Info</span>
                                    </div>
                                    <span class="block text-[11px] font-bold uppercase tracking-[0.3em] opacity-80 mb-1">Total Settlement</span>
                                    <h2 class="text-5xl font-black tracking-tighter">
                                        <span class="text-2xl font-medium opacity-70">Rp</span> 
                                        {{ number_format($totalHarga, 0, ',', '.') }}
                                    </h2>
                                </div>

                                <div class="relative z-10 pt-8 border-t border-white/20">
                                    <div class="flex justify-between items-center mb-2 text-xs opacity-80">
                                        <span>Status</span>
                                        <span class="font-bold">Waiting Payment</span>
                                    </div>
                                    <p class="text-[10px] font-medium italic opacity-60 leading-relaxed">
                                        Sistem akan otomatis mengaktifkan kunci kamar setelah konfirmasi dilakukan.
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="flex items-center justify-between px-6">
                        <a href="{{ url()->previous() }}" class="text-xs font-bold text-gray-400 uppercase tracking-widest hover:text-sig transition-colors flex items-center gap-2">
                            <i class="fas fa-arrow-left"></i> Kembali ke Antrean
                        </a>
                        <button type="submit" class="group bg-sig text-white px-12 py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-red-800 transition-all shadow-xl shadow-red-900/30 active:scale-95 flex items-center gap-3">
                            <i class="fas fa-check-circle text-sm group-hover:scale-125 transition-transform"></i> 
                            Konfirmasi & Check-in
                        </button>
                    </div>
                </form>
            </div>

            <div class="max-w-4xl mx-auto mt-20 flex justify-between items-center opacity-40">
                <p class="text-[9px] font-black text-gray-500 uppercase tracking-[0.5em]">Hotel SIG Management v2.1</p>
                <div class="h-px flex-1 mx-8 bg-gray-200"></div>
                <p class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em]">Cashier Desk Module</p>
            </div>
        </div>
    </div>
</body>
</html>