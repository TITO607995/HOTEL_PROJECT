<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>Check-out | Website Hotel SIG</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .border-dotted-b { border-bottom: 2px dotted #9ca3af; }
        [x-cloak] { display: none !important; }

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
<body class="bg-gray-100 antialiased" x-data="checkoutPage()">

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
                    <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Proses Check-out & Penagihan</h1>
                    <p class="text-sm text-gray-500 font-medium">Lengkapi rincian biaya atau perpanjang masa menginap tamu.</p>
                </div>
                <a href="{{ route('reservations.index') }}" class="flex items-center text-gray-400 hover:text-[#800000] transition font-bold text-sm">
                    <i class="fas fa-arrow-left mr-2"></i> KEMBALI
                </a>
            </div>

            @if(session('success'))
                <div class="max-w-5xl mx-auto mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-xl shadow-sm animate-fade-in-down no-print">
                    <p class="font-bold"><i class="fas fa-check-circle mr-2"></i>Berhasil!</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            @endif

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
                                {{-- Area Pencarian Tamu & Tombol Extend --}}
                                <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100 shadow-sm no-print relative">
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Cari Tamu In-House</label>
                                        
                                        {{-- TOMBOL EXTEND (Muncul via Alpine JS kalau tamu sudah dipilih) --}}
                                        <button type="button" x-show="selectedResId" x-cloak @click="openExtendModal()" 
                                            class="bg-emerald-100 text-emerald-700 hover:bg-emerald-500 hover:text-white px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-wider transition-all flex items-center gap-1.5 border border-emerald-200">
                                            <i class="fas fa-calendar-plus"></i> Extend
                                        </button>
                                    </div>
                                    
                                    <select id="resSelect" name="reservation_id" @change="handleGuestSelect($event)" class="w-full bg-transparent border-none focus:ring-0 font-black text-lg text-[#800000] cursor-pointer" required>
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
                                    <tr class="hover:bg-gray-50 transition no-print">
                                        <td class="p-6 text-center font-bold text-gray-400">02</td>
                                        <td class="p-6">
                                            <input type="text" name="notes" placeholder="Catatan Tambahan (Minibar, Laundry...)" class="w-full bg-transparent border-none focus:ring-0 italic text-gray-600 placeholder:text-gray-300 p-0">
                                        </td>
                                        <td class="p-6 text-center text-gray-400">-</td>
                                        <td class="p-6 text-right text-gray-400 uppercase text-[10px] font-bold">Add Charge</td>
                                        <td class="p-6">
                                            <input type="number" name="additional_charges" id="addCharge" value="0" @input="calculateGrandTotal" class="w-full bg-transparent border-none focus:ring-0 text-right font-black text-[#800000] text-xl p-0">
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-900">
                                        <td colspan="4" class="p-6 text-right font-bold text-white uppercase tracking-widest text-xs">Total Akhir</td>
                                        <td class="p-6 text-right font-black text-white text-3xl">
                                            <span class="text-sm mr-1">Rp</span><span x-text="grandTotal">0</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="flex flex-col md:flex-row justify-between items-center gap-10 mt-12 pt-10 border-t border-gray-100">
                            <div class="text-center">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-20 italic">Front Office Staff</p>
                                <div class="w-56 h-px bg-gray-300 mx-auto"></div>
                                <p class="text-[10px] mt-2 font-bold text-gray-800 uppercase tracking-widest">{{ Auth::user()->name ?? 'Administrator' }}</p>
                            </div>
                            
                            <div class="flex gap-4 no-print items-center">
                                <button type="button" onclick="window.print()" class="group bg-gray-100 text-gray-800 font-bold px-8 py-5 rounded-2xl hover:bg-gray-200 transition-all flex items-center gap-3">
                                    <i class="fas fa-print opacity-50 group-hover:opacity-100"></i> CETAK
                                </button>
                                <button type="submit" id="submitBtn" :disabled="!selectedResId" class="bg-[#800000] disabled:bg-gray-200 disabled:text-gray-400 text-white font-black px-12 py-5 rounded-2xl shadow-xl hover:bg-red-900 transition-all flex items-center gap-3">
                                    <span>KONFIRMASI CHECK-OUT</span> <i class="fas fa-check-circle"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    {{-- MODAL EXTEND MENGINAP --}}
    <div x-show="extendModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto no-print" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="extendModal" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="extendModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div x-show="extendModal" x-transition class="inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                <form :action="'/reservasi/extend/' + selectedResId" method="POST">
                    @csrf
                    <div class="bg-gradient-to-r from-emerald-600 to-emerald-500 px-8 py-6 text-white relative overflow-hidden">
                        <i class="fas fa-calendar-plus absolute -right-4 -bottom-4 text-7xl opacity-20"></i>
                        <h3 class="text-xl font-black uppercase tracking-wider flex items-center gap-2 relative z-10">
                            <i class="fas fa-calendar-plus"></i> Perpanjang Menginap
                        </h3>
                        <p class="text-emerald-100 text-sm mt-1 relative z-10">Kamar <span x-text="guestInfo.room"></span> a.n <span x-text="guestInfo.name"></span></p>
                    </div>
                    
                    <div class="bg-white px-8 pt-6 pb-6 text-left">
                        <div class="mb-5 p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Check-out Awal</p>
                            <p class="text-lg font-black text-gray-800" x-text="guestInfo.outDate"></p>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Pilih Tanggal Kepulangan Baru</label>
                            <input type="date" name="new_departure_date" :min="guestInfo.minNewDate" class="w-full rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-100 text-sm font-bold text-gray-700 p-3" required>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-8 py-5 flex justify-end gap-3 border-t border-gray-100">
                        <button type="button" @click="extendModal = false" class="px-6 py-3 bg-white border border-gray-200 rounded-xl text-xs font-black text-gray-600 hover:bg-gray-50 transition-all">BATAL</button>
                        <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-xl text-xs font-black shadow-lg shadow-emerald-600/30 hover:bg-emerald-700 transition-all active:scale-95">SIMPAN PERPANJANGAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function checkoutPage() {
            return {
                selectedResId: '',
                roomSubtotal: 0,
                grandTotal: '0',
                extendModal: false,
                guestInfo: { name: '', room: '', outDate: '', minNewDate: '' },

                formatNumber(num) {
                    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
                },

                handleGuestSelect(e) {
                    const opt = e.target.options[e.target.selectedIndex];
                    this.selectedResId = e.target.value;
                    
                    if (!this.selectedResId) {
                        this.resetUI();
                        return;
                    }

                    const dateIn = new Date(opt.dataset.in);
                    const dateOut = new Date(opt.dataset.out);
                    const price = parseInt(opt.dataset.price) || 0;
                    
                    const diffTime = Math.abs(dateOut - dateIn);
                    const nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) || 1;
                    this.roomSubtotal = nights * price;

                    // Update Info untuk Form Check-out
                    document.getElementById('displayRoom').innerText = opt.dataset.room;
                    document.getElementById('displayIn').innerText = opt.dataset.in;
                    document.getElementById('displayOut').innerText = opt.dataset.out;
                    document.getElementById('printGuestName').innerText = opt.dataset.name;
                    document.getElementById('inputGuestName').value = opt.dataset.name;
                    document.getElementById('detailRoomType').innerText = "Sewa Kamar (" + opt.dataset.type + ")";
                    document.getElementById('detailStayPeriod').innerText = opt.dataset.in + " s/d " + opt.dataset.out;
                    document.getElementById('displayNights').innerText = nights + " Malam";
                    document.getElementById('displayPricePerNight').innerText = this.formatNumber(price);
                    document.getElementById('displayRoomSubtotal').innerText = this.formatNumber(this.roomSubtotal);
                    document.getElementById('checkoutForm').action = "/reservasi/check-out/" + this.selectedResId + "/process";

                    // Update Data untuk Modal Extend
                    this.guestInfo.name = opt.dataset.name;
                    this.guestInfo.room = opt.dataset.room;
                    this.guestInfo.outDate = opt.dataset.out;
                    let nextDay = new Date(dateIn);
                    nextDay.setDate(nextDay.getDate() + 1);
                    this.guestInfo.minNewDate = nextDay.toISOString().split('T')[0];

                    this.calculateGrandTotal();
                },

                calculateGrandTotal() {
                    const additional = parseInt(document.getElementById('addCharge').value) || 0;
                    this.grandTotal = this.formatNumber(this.roomSubtotal + additional);
                },

                resetUI() {
                    this.roomSubtotal = 0;
                    this.grandTotal = '0';
                    document.getElementById('displayRoom').innerText = "-";
                    document.getElementById('displayIn').innerText = "-";
                    document.getElementById('displayOut').innerText = "-";
                    document.getElementById('printGuestName').innerText = "-";
                    document.getElementById('inputGuestName').value = "";
                    document.getElementById('checkoutForm').action = "";
                },

                openExtendModal() {
                    if(this.selectedResId) this.extendModal = true;
                }
            }
        }

        // SweetAlert Konfirmasi
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Check-out',
                text: "Pastikan tagihan Rp" + document.getElementById('grandTotalDisplay').innerText + " telah lunas.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#800000',
                confirmButtonText: 'Ya, Selesaikan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) this.submit();
            });
        });
    </script>
</body>
</html>