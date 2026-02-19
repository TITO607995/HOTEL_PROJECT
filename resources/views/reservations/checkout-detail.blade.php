<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <title>Check-out | Website Hotel SIG</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .border-dotted-b { border-bottom: 2px dotted #9ca3af; }

        @media print {
            .no-print, x-sidebar, aside, x-header, button, .breadcrumb, nav {
                display: none !important;
            }
            body { background: white !important; padding: 0; margin: 0; }
            main { padding: 0 !important; margin: 0 !important; width: 100% !important; }
            .max-w-4xl { max-width: 100% !important; box-shadow: none !important; border-top: 15px solid #800000 !important; }
            .rounded-[2rem] { border-radius: 0 !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
</head>
<body class="bg-gray-100 antialiased">

    <div class="no-print">
        <x-header></x-header>
    </div>

    <div class="flex min-h-screen">
        <div class="no-print flex-shrink-0">
            <x-sidebar></x-sidebar>
        </div>

        <main class="flex-1 p-6 lg:p-12 transition-all duration-300">
            
            <div class="max-w-4xl mx-auto mb-6 flex justify-between items-center no-print breadcrumb">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Proses Check-out</h1>
                    <p class="text-sm text-gray-500 font-medium">Lengkapi rincian biaya sebelum konfirmasi pembayaran.</p>
                </div>
                <a href="{{ route('reservations.checkout.page') }}" class="flex items-center text-gray-400 hover:text-[#800000] transition font-bold text-sm">
                    <i class="fas fa-arrow-left mr-2"></i> KEMBALI
                </a>
            </div>

            <div id="invoiceArea" class="max-w-5xl mx-auto bg-white rounded-[2rem] shadow-2xl overflow-hidden border-t-[15px] border-[#800000] mb-10">
                <div class="p-8 md:p-12">
                    
                    <div class="flex justify-between items-start mb-12 border-b border-gray-100 pb-8">
                        <div>
                            <h2 class="text-5xl font-black text-gray-900 tracking-tighter italic uppercase leading-none">Invoice</h2>
                            <p class="text-[10px] text-gray-400 font-bold tracking-[0.3em] uppercase mt-2">Sistem Informasi Hotel SIG</p>
                            <p class="text-sm font-bold text-gray-500 mt-4">INV-{{ date('Ymd') }}-{{ $reservation->id }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-black text-gray-800 text-lg uppercase">Website Simulasi PH</p>
                            <p class="text-gray-500 text-xs italic">Jl. Arif Rahman Hakim No. 101, Gresik</p>
                            <p class="text-gray-500 text-xs italic">Telp: (031) 555-0123</p>
                        </div>
                    </div>

                    {{-- Form mengarah langsung ke proses berdasarkan ID Reservasi --}}
                    <form action="{{ route('reservations.process-checkout', $reservation->id) }}" id="checkoutForm" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
                            <div class="space-y-6">
                                {{-- Menampilkan Data Tamu Secara Fix (Tanpa Dropdown) --}}
                                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 shadow-sm">
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Ditagihkan Kepada</label>
                                    <p class="font-black text-2xl text-[#800000] uppercase">{{ $reservation->guest_name }}</p>
                                    <p class="text-sm text-gray-600 font-medium mt-1"><i class="fas fa-envelope mr-1 text-gray-400"></i> {{ $reservation->email }}</p>
                                    <p class="text-sm text-gray-600 font-medium"><i class="fas fa-phone mr-1 text-gray-400"></i> {{ $reservation->phone }}</p>
                                </div>
                            </div>

                            <div class="space-y-4 px-2">
                                <div class="flex justify-between border-dotted-b pb-2">
                                    <span class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Nomor Kamar</span>
                                    <span class="font-black text-gray-800">{{ $reservation->room->room_number }} ({{ $reservation->room->type }})</span>
                                </div>
                                <div class="flex justify-between border-dotted-b pb-2">
                                    <span class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Check-in</span>
                                    <span class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($reservation->arrival_date)->format('d F Y') }}</span>
                                </div>
                                <div class="flex justify-between border-dotted-b pb-2">
                                    <span class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Check-out</span>
                                    <span class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($reservation->departure_date)->format('d F Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-hidden rounded-2xl border border-gray-200 mb-10 shadow-sm">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-900 text-white uppercase text-[10px] tracking-[0.2em]">
                                        <th class="p-5 text-center w-16">No</th>
                                        <th class="p-5 text-left">Deskripsi Layanan</th>
                                        <th class="p-5 text-center">Durasi</th>
                                        <th class="p-5 text-right">Harga (Rp)</th>
                                        <th class="p-5 text-right">Subtotal (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    {{-- Baris 1: Harga Sewa Kamar (Otomatis dari DB) --}}
                                    <tr class="bg-gray-50/50">
                                        <td class="p-6 text-center font-bold text-gray-400">01</td>
                                        <td class="p-6">
                                            <p class="font-bold text-gray-800">Sewa Kamar ({{ $reservation->room->type }})</p>
                                            <p class="text-[10px] text-gray-400 uppercase italic">{{ \Carbon\Carbon::parse($reservation->arrival_date)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($reservation->departure_date)->format('d/m/Y') }}</p>
                                        </td>
                                        <td class="p-6 text-center font-bold text-gray-800">{{ $nights }} Malam</td>
                                        <td class="p-6 text-right font-bold text-gray-800">{{ number_format($hargaPerMalam, 0, ',', '.') }}</td>
                                        <td class="p-6 text-right font-black text-gray-900">{{ number_format($roomCharge, 0, ',', '.') }}</td>
                                    </tr>
                                    
                                    {{-- Baris 2: Input Biaya Tambahan --}}
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="p-6 text-center font-bold text-gray-400">02</td>
                                        <td class="p-6">
                                            <input type="text" name="notes" placeholder="Catatan: Minibar, Laundry, atau Kerusakan..." 
                                                   class="w-full bg-transparent border-none focus:ring-0 italic text-gray-600 placeholder:text-gray-300 p-0 no-print">
                                            <span class="hidden print:block text-gray-600 italic">Biaya Tambahan / Denda</span>
                                        </td>
                                        <td class="p-6 text-center text-gray-400">-</td>
                                        <td class="p-6 text-right text-gray-400 uppercase text-[10px] font-bold">Add Charge</td>
                                        <td class="p-6">
                                            <input type="number" name="additional_charges" id="addCharge" value="0" min="0"
                                                   class="w-full bg-transparent border-none focus:ring-0 text-right font-black text-[#800000] text-xl p-0 no-print">
                                            <span class="hidden print:block text-right font-black text-[#800000] text-xl">0</span>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-900">
                                        <td colspan="4" class="p-6 text-right font-bold text-white uppercase tracking-widest text-xs">Total Pembayaran Akhir</td>
                                        <td class="p-6 text-right font-black text-white text-3xl">
                                            <span class="text-sm mr-1">Rp</span><span id="grandTotalDisplay">{{ number_format($roomCharge, 0, ',', '.') }}</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="flex flex-col md:flex-row justify-between items-center gap-10 mt-12 pt-10 border-t border-gray-100">
                            <div class="text-center">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-20 italic">Petugas Front Office</p>
                                <div class="w-56 h-px bg-gray-300 mx-auto"></div>
                                <p class="text-[10px] mt-2 font-bold text-gray-800 uppercase tracking-widest">{{ Auth::user()->name ?? 'Administrator' }}</p>
                            </div>
                            
                            <div class="flex gap-4 no-print items-center">
                                <button type="button" onclick="window.print()" class="group bg-gray-100 text-gray-800 font-bold px-8 py-5 rounded-2xl hover:bg-gray-200 transition-all flex items-center gap-3 border border-gray-200">
                                    <i class="fas fa-print opacity-50 group-hover:opacity-100"></i>
                                    <span>CETAK</span>
                                </button>

                                <button type="submit" id="submitBtn" 
                                        class="bg-[#800000] text-white font-black px-12 py-5 rounded-2xl shadow-xl hover:shadow-red-900/40 hover:-translate-y-1 transition-all duration-300 flex items-center gap-3">
                                    <span>KONFIRMASI CHECK-OUT</span>
                                    <i class="fas fa-check-circle"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <p class="text-center text-[10px] text-gray-400 font-bold uppercase tracking-widest no-print mb-10 italic">Web Powered by 5NYeni</p>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Data dasar subtotal didapatkan langsung dari backend PHP
        const baseRoomTotal = {{ $roomCharge }};
        const addChargeInput = document.getElementById('addCharge');
        const grandTotalDisplay = document.getElementById('grandTotalDisplay');

        // Fungsi format Rupiah
        function formatNumber(num) {
            return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
        }

        // Hitung Grand Total otomatis pas resepsionis ngetik biaya tambahan
        addChargeInput.addEventListener('input', function() {
            const additional = parseInt(this.value) || 0;
            const totalSemua = baseRoomTotal + additional;
            grandTotalDisplay.innerText = formatNumber(totalSemua);
        });

        // Konfirmasi sebelum submit data ke laporan keuangan
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Check-out',
                text: "Pastikan pembayaran telah diterima sesuai total invoice.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#800000',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Ya, Selesaikan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    </script>
</body>
</html>