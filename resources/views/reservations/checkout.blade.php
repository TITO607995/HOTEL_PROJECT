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
                <a href="{{ route('reservations.index') }}" class="flex items-center text-gray-400 hover:text-[#800000] transition font-bold text-sm">
                    <i class="fas fa-arrow-left mr-2"></i> KEMBALI
                </a>
            </div>

            <div id="invoiceArea" class="max-w-5xl mx-auto bg-white rounded-[2rem] shadow-2xl overflow-hidden border-t-[15px] border-[#800000] mb-10">
                <div class="p-8 md:p-12">
                    
                    <div class="flex justify-between items-start mb-12 border-b border-gray-100 pb-8">
                        <div>
                            <h2 class="text-5xl font-black text-gray-900 tracking-tighter italic uppercase leading-none">Invoice</h2>
                            <p class="text-[10px] text-gray-400 font-bold tracking-[0.3em] uppercase mt-2">Sistem Informasi Hotel SIG</p>
                        </div>
                        <div class="text-right">
                            <p class="font-black text-gray-800 text-lg uppercase">Website Simulasi PH</p>
                            <p class="text-gray-500 text-xs italic">Jl. Arif Rahman Hakim No. 101, Gresik</p>
                            <p class="text-gray-500 text-xs italic">Telp: (031) 555-0123</p>
                        </div>
                    </div>

                    <form action="" id="checkoutForm" method="POST">
                        @csrf
                        <input type="hidden" name="guest_name" id="inputGuestName">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
                            <div class="space-y-6">
                                <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100 shadow-sm no-print">
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Cari Reservasi Aktif</label>
                                    <select id="resSelect" name="reservation_id" class="w-full bg-transparent border-none focus:ring-0 font-black text-lg text-[#800000] cursor-pointer" required>
                                        <option value="">-- PILIH TAMU / KAMAR --</option>
                                        @foreach($reservations as $r)
                                            <option value="{{ $r->id }}" 
                                                    data-room="{{ $r->room->room_number }}" 
                                                    data-type="{{ $r->room->type }}"
                                                    data-price="{{ $r->room->price }}"
                                                    data-in="{{ $r->arrival_date }}" 
                                                    data-out="{{ $r->departure_date }}"
                                                    data-name="{{ $r->guest_name }}">
                                                {{ $r->room->room_number }} - {{ $r->guest_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="hidden print:block border-dotted-b pb-2">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase">Nama Tamu</p>
                                    <p id="printGuestName" class="font-bold text-xl">-</p>
                                </div>
                            </div>

                            <div class="space-y-4 px-2">
                                <div class="flex justify-between border-dotted-b pb-2">
                                    <span class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Nomor Kamar</span>
                                    <span id="displayRoom" class="font-black text-gray-800">-</span>
                                </div>
                                <div class="flex justify-between border-dotted-b pb-2">
                                    <span class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Check-in</span>
                                    <span id="displayIn" class="font-bold text-gray-800">-</span>
                                </div>
                                <div class="flex justify-between border-dotted-b pb-2">
                                    <span class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Check-out</span>
                                    <span id="displayOut" class="font-bold text-gray-800">-</span>
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
                                    <tr class="bg-gray-50/50">
                                        <td class="p-6 text-center font-bold text-gray-400">01</td>
                                        <td class="p-6">
                                            <p class="font-bold text-gray-800" id="detailRoomType">Sewa Kamar</p>
                                            <p class="text-[10px] text-gray-400 uppercase italic" id="detailStayPeriod">Menunggu pilihan...</p>
                                        </td>
                                        <td class="p-6 text-center font-bold text-gray-800" id="displayNights">0</td>
                                        <td class="p-6 text-right font-bold text-gray-800" id="displayPricePerNight">0</td>
                                        <td class="p-6 text-right font-black text-gray-900" id="displayRoomSubtotal">0</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="p-6 text-center font-bold text-gray-400">02</td>
                                        <td class="p-6">
                                            <input type="text" name="notes" placeholder="Catatan: Minibar, Laundry, atau Kerusakan..." 
                                                   class="w-full bg-transparent border-none focus:ring-0 italic text-gray-600 placeholder:text-gray-300 p-0">
                                        </td>
                                        <td class="p-6 text-center text-gray-400">-</td>
                                        <td class="p-6 text-right text-gray-400 uppercase text-[10px] font-bold">Add Charge</td>
                                        <td class="p-6">
                                            <input type="number" name="additional_charges" id="addCharge" value="0" 
                                                   class="w-full bg-transparent border-none focus:ring-0 text-right font-black text-[#800000] text-xl p-0">
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-900">
                                        <td colspan="4" class="p-6 text-right font-bold text-white uppercase tracking-widest text-xs">Total Pembayaran Akhir</td>
                                        <td class="p-6 text-right font-black text-white text-3xl">
                                            <span class="text-sm mr-1">Rp</span><span id="grandTotalDisplay">0</span>
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

                                <button type="submit" id="submitBtn" disabled 
                                        class="bg-[#800000] disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed text-white font-black px-12 py-5 rounded-2xl shadow-xl hover:shadow-red-900/40 hover:-translate-y-1 transition-all duration-300 flex items-center gap-3">
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
        const resSelect = document.getElementById('resSelect');
        const addChargeInput = document.getElementById('addCharge');
        const grandTotalDisplay = document.getElementById('grandTotalDisplay');
        const submitBtn = document.getElementById('submitBtn');
        const inputGuestName = document.getElementById('inputGuestName');

        function formatNumber(num) {
            return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
        }

        resSelect.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            
            if (!this.value) {
                resetTampilan();
                return;
            }

            // Hitung Selisih Hari
            const dateIn = new Date(opt.dataset.in);
            const dateOut = new Date(opt.dataset.out);
            const price = parseInt(opt.dataset.price) || 0;
            
            const diffTime = Math.abs(dateOut - dateIn);
            const nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) || 1;
            const roomSubtotal = nights * price;

            // Update UI & Hidden Input
            document.getElementById('displayRoom').innerText = opt.dataset.room;
            document.getElementById('displayIn').innerText = opt.dataset.in;
            document.getElementById('displayOut').innerText = opt.dataset.out;
            document.getElementById('printGuestName').innerText = opt.dataset.name;
            inputGuestName.value = opt.dataset.name; // Simpan nama tamu ke hidden input

            // Update Tabel
            document.getElementById('detailRoomType').innerText = "Sewa Kamar (" + opt.dataset.type + ")";
            document.getElementById('detailStayPeriod').innerText = opt.dataset.in + " s/d " + opt.dataset.out;
            document.getElementById('displayNights').innerText = nights + " Malam";
            document.getElementById('displayPricePerNight').innerText = formatNumber(price);
            document.getElementById('displayRoomSubtotal').innerText = formatNumber(roomSubtotal);

            window.currentRoomSubtotal = roomSubtotal;
            calculateGrandTotal();

            // Atur URL Action Form secara dinamis
            document.getElementById('checkoutForm').action = "/reservasi/check-out/" + this.value + "/process";
            submitBtn.disabled = false;
        });

        addChargeInput.addEventListener('input', calculateGrandTotal);

        function calculateGrandTotal() {
            const additional = parseInt(addChargeInput.value) || 0;
            const roomTotal = window.currentRoomSubtotal || 0;
            const totalSemua = roomTotal + additional;
            grandTotalDisplay.innerText = formatNumber(totalSemua);
        }

        function resetTampilan() {
            submitBtn.disabled = true;
            grandTotalDisplay.innerText = "0";
            document.getElementById('displayRoom').innerText = "-";
            document.getElementById('displayNights').innerText = "0";
            document.getElementById('displayRoomSubtotal').innerText = "0";
            inputGuestName.value = "";
        }

        // Alert Konfirmasi sebelum Submit
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Check-out',
                text: "Tamu akan otomatis dihapus dari daftar tamu aktif.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#800000',
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