<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#800000">

    <title>Check-out | Hotel SIG</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow: hidden;
            overscroll-behavior-y: contain;
        }

        .bg-maroon { background-color: #800000; }
        .text-maroon { color: #800000; }
        .border-dotted-b { border-bottom: 2px dotted #d1d5db; }

        @media (min-width: 1024px) {
            .custom-scroll::-webkit-scrollbar { width: 6px; }
            .custom-scroll::-webkit-scrollbar-track { background: transparent; }
            .custom-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
            .custom-scroll::-webkit-scrollbar-thumb:hover { background: #800000; }
        }

        .safe-top { padding-top: env(safe-area-inset-top); }
        [x-cloak] { display: none !important; }

        @media print {
            .no-print { display: none !important; }
            body { overflow: visible !important; background: white !important; }
            .print-area { box-shadow: none !important; border: none !important; margin: 0 !important; width: 100% !important; border-top: none !important; }
            .invoice-card { border: 1px solid #eee !important; }
        }
    </style>
</head>

<body class="bg-[#F3F4F6] antialiased h-screen flex flex-col mb-20 lg:mb-0" x-data="checkoutPage()">

    <header class="relative z-[60] safe-top bg-white lg:bg-transparent no-print">
        <x-header></x-header>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <aside class="h-full flex-shrink-0 border-r border-gray-200 bg-white hidden lg:block no-print">
            <x-sidebar></x-sidebar>
        </aside>

        <main class="flex-1 p-4 md:p-8 lg:p-10 overflow-y-auto custom-scroll">
            
            <div class="max-w-5xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 no-print">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Proses Check-out</h1>
                    <p class="text-gray-500 font-medium">Lengkapi rincian biaya atau perpanjang masa menginap.</p>
                </div>
                <a href="{{ route('reservations.index') }}" class="flex items-center text-gray-400 hover:text-maroon transition-all font-bold text-xs tracking-widest group">
                    <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> KEMBALI
                </a>
            </div>

            <div id="invoiceArea" class="max-w-5xl mx-auto bg-white rounded-[32px] shadow-2xl overflow-hidden border-t-[12px] border-[#800000] mb-10 print-area invoice-card transition-all">
                <div class="p-6 md:p-12">
                    
                    <div class="flex flex-col md:flex-row justify-between items-start gap-6 mb-12 border-b border-gray-100 pb-8">
                        <div>
                            <h2 class="text-5xl font-black text-gray-900 tracking-tighter italic uppercase leading-none">Invoice</h2>
                            <p class="text-[10px] text-gray-400 font-bold tracking-[0.3em] uppercase mt-3">Sistem Informasi Hotel SIG</p>
                        </div>
                        <div class="text-left md:text-right space-y-1">
                            <p class="font-black text-gray-800 text-lg uppercase tracking-tight">Website Simulasi PH</p>
                            <p class="text-gray-500 text-xs italic font-medium">Jl. Arif Rahman Hakim No. 101, Gresik</p>
                            <p class="text-gray-500 text-xs italic font-medium">Telp: (031) 555-0123</p>
                        </div>
                    </div>

                    <form action="" id="checkoutForm" method="POST">
                        @csrf
                        <input type="hidden" name="guest_name" id="inputGuestName">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
                            <div class="space-y-6">
                                <div class="bg-gray-50 p-6 rounded-3xl border border-gray-100 shadow-sm no-print relative group transition-all hover:border-red-100">
                                    <div class="flex justify-between items-center mb-3">
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pilih Reservasi Aktif</label>
                                        
                                        <button type="button" x-show="selectedResId" x-cloak @click="openExtendModal()" 
                                            class="bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-wider transition-all flex items-center gap-2 border border-emerald-100">
                                            <i class="fas fa-calendar-plus"></i> Perpanjang
                                        </button>
                                    </div>
                                    
                                    <select id="resSelect" name="reservation_id" @change="handleGuestSelect($event)" 
                                        class="w-full bg-transparent border-none focus:ring-0 font-black text-xl text-maroon cursor-pointer appearance-none">
                                        <option value="">-- PILIH TAMU --</option>
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
                                    <div class="absolute right-6 bottom-7 text-gray-300 pointer-events-none group-hover:text-maroon transition-colors">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>

                                <div class="hidden print:block border-dotted-b pb-3">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nama Tamu</p>
                                    <p id="printGuestName" class="font-extrabold text-2xl text-gray-900">-</p>
                                </div>
                            </div>

                            <div class="bg-white p-2 space-y-5">
                                <div class="flex justify-between items-end border-dotted-b pb-2">
                                    <span class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Nomor Kamar</span>
                                    <span id="displayRoom" class="font-black text-xl text-gray-800">-</span>
                                </div>
                                <div class="flex justify-between items-end border-dotted-b pb-2">
                                    <span class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Check-in</span>
                                    <span id="displayIn" class="font-bold text-sm text-gray-700 tracking-tight">-</span>
                                </div>
                                <div class="flex justify-between items-end border-dotted-b pb-2">
                                    <span class="text-gray-400 text-[10px] font-bold uppercase tracking-widest text-maroon">Check-out</span>
                                    <span id="displayOut" class="font-extrabold text-sm text-maroon tracking-tight">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-hidden rounded-3xl border border-gray-100 mb-10 shadow-sm">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-900 text-white uppercase text-[9px] font-bold tracking-[0.2em]">
                                        <th class="py-5 px-6 text-center w-16">No</th>
                                        <th class="py-5 px-6 text-left">Deskripsi Layanan</th>
                                        <th class="py-5 px-6 text-center">Durasi</th>
                                        <th class="py-5 px-6 text-right">Harga Satuan</th>
                                        <th class="py-5 px-6 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <tr class="bg-gray-50/30">
                                        <td class="p-6 text-center font-bold text-gray-300">01</td>
                                        <td class="p-6">
                                            <p class="font-extrabold text-gray-800 text-base" id="detailRoomType">Sewa Kamar</p>
                                            <p class="text-[10px] text-gray-400 uppercase font-bold mt-1 italic" id="detailStayPeriod">Silakan pilih reservasi</p>
                                        </td>
                                        <td class="p-6 text-center font-bold text-gray-700" id="displayNights">0</td>
                                        <td class="p-6 text-right font-bold text-gray-700" id="displayPricePerNight">0</td>
                                        <td class="p-6 text-right font-black text-gray-900" id="displayRoomSubtotal">0</td>
                                    </tr>
                                    <tr class="no-print group hover:bg-red-50/30 transition-colors">
                                        <td class="p-6 text-center font-bold text-gray-300">02</td>
                                        <td class="p-6">
                                            <input type="text" name="notes" placeholder="Tambahkan Catatan (Minimarket, Laundry, dll)..." 
                                                class="w-full bg-transparent border-none focus:ring-0 italic text-sm text-gray-600 placeholder:text-gray-300 p-0 font-medium">
                                        </td>
                                        <td class="p-6 text-center text-gray-300">-</td>
                                        <td class="p-6 text-right text-gray-400 uppercase text-[9px] font-black tracking-widest">Biaya Tambahan</td>
                                        <td class="p-6">
                                            <div class="flex items-center justify-end gap-2">
                                                <span class="text-xs font-bold text-gray-400 italic">Rp</span>
                                                <input type="number" name="additional_charges" id="addCharge" value="0" @input="calculateGrandTotal" 
                                                    class="w-32 bg-transparent border-b border-transparent group-hover:border-red-200 focus:border-maroon focus:ring-0 text-right font-black text-maroon text-xl p-0 transition-all">
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-900 border-t-4 border-maroon">
                                        <td colspan="4" class="p-8 text-right font-bold text-white uppercase tracking-[0.3em] text-[10px]">Total Pembayaran Akhir</td>
                                        <td class="p-8 text-right">
                                            <div class="flex items-start justify-end gap-1 text-white">
                                                <span class="text-xs font-bold mt-2 opacity-50">Rp</span>
                                                <span class="text-4xl font-black tracking-tighter" x-text="grandTotal">0</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="flex flex-col md:flex-row justify-between items-center gap-12 mt-12 pt-10 border-t border-gray-50">
                            <div class="text-center order-2 md:order-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-20 italic">Front Office Approval</p>
                                <div class="w-64 h-px bg-gray-200 mx-auto"></div>
                                <p class="text-[11px] mt-3 font-extrabold text-gray-800 uppercase tracking-widest">{{ Auth::user()->name ?? 'Administrator' }}</p>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row gap-4 no-print items-center w-full md:w-auto order-1 md:order-2">
                                <button type="button" onclick="window.print()" 
                                    class="w-full sm:w-auto bg-gray-100 text-gray-600 font-bold px-10 py-5 rounded-2xl hover:bg-gray-200 transition-all flex justify-center items-center gap-3 text-xs tracking-widest active:scale-95">
                                    <i class="fas fa-print"></i> CETAK INVOICE
                                </button>
                                <button type="submit" id="submitBtn" :disabled="!selectedResId" 
                                    class="w-full sm:w-auto bg-maroon disabled:bg-gray-200 disabled:text-gray-400 text-white font-black px-14 py-5 rounded-2xl shadow-xl shadow-red-900/20 hover:bg-red-900 hover:-translate-y-1 transition-all flex justify-center items-center gap-3 text-xs tracking-widest active:scale-95">
                                    KONFIRMASI CHECK-OUT <i class="fas fa-check-double"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <div x-show="extendModal" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
        
        <div class="bg-white w-full max-w-md rounded-[32px] overflow-hidden shadow-2xl" @click.away="extendModal = false">
            <div class="bg-maroon p-8 text-white">
                <div class="flex justify-between items-center">
                    <h3 class="text-2xl font-black italic uppercase tracking-tighter">Perpanjang</h3>
                    <button @click="extendModal = false" class="text-white/50 hover:text-white"><i class="fas fa-times"></i></button>
                </div>
                <p class="text-white/70 text-xs font-bold uppercase tracking-widest mt-1">Update masa menginap</p>
            </div>
            
            <form :action="'/reservasi/perpanjang/' + selectedResId" method="POST" class="p-8 space-y-6">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Nama Tamu</label>
                    <p class="font-bold text-gray-800 text-lg" x-text="guestInfo.name"></p>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Check-out Baru</label>
                    <input type="date" name="new_departure_date" :min="guestInfo.minNewDate" required
                           class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-4 font-bold text-gray-700 focus:border-maroon focus:ring-0 transition-all">
                    <p class="text-[9px] text-gray-400 mt-2 italic font-medium">*Minimal H+1 dari jadwal awal.</p>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" @click="extendModal = false"
                            class="flex-1 bg-gray-100 text-gray-500 font-bold py-4 rounded-2xl hover:bg-gray-200 transition-all text-xs tracking-widest">
                        BATAL
                    </button>
                    <button type="submit"
                            class="flex-1 bg-maroon text-white font-black py-4 rounded-2xl shadow-lg shadow-red-900/20 hover:bg-red-900 transition-all text-xs tracking-widest">
                        SIMPAN
                    </button>
                </div>
            </form>
        </div>
    </div>

    <x-bottom-nav></x-bottom-nav>
    <x-mobile-menu></x-mobile-menu>

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
                    return num.toLocaleString('id-ID');
                },

                handleGuestSelect(e) {
                    const opt = e.target.options[e.target.selectedIndex];
                    this.selectedResId = e.target.value;
                    
                    if (!this.selectedResId) {
                        this.resetUI();
                        return;
                    }

                    // Calculation Logic
                    const dateIn = new Date(opt.dataset.in);
                    const dateOut = new Date(opt.dataset.out);
                    const price = parseInt(opt.dataset.price) || 0;
                    
                    const diffTime = Math.abs(dateOut - dateIn);
                    const nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) || 1;
                    this.roomSubtotal = nights * price;

                    // Update UI Elements
                    this.updateUI(opt, nights, price);
                    
                    // Sync Guest Info for Extend Modal
                    this.guestInfo.name = opt.dataset.name;
                    let d = new Date(dateOut);
                    d.setDate(d.getDate() + 1);
                    this.guestInfo.minNewDate = d.toISOString().split('T')[0];

                    this.calculateGrandTotal();
                },

                updateUI(opt, nights, price) {
                    const elements = {
                        'displayRoom': opt.dataset.room,
                        'displayIn': opt.dataset.in,
                        'displayOut': opt.dataset.out,
                        'printGuestName': opt.dataset.name,
                        'inputGuestName': opt.dataset.name,
                        'detailRoomType': `Sewa Kamar (${opt.dataset.type})`,
                        'detailStayPeriod': `${opt.dataset.in} s/d ${opt.dataset.out}`,
                        'displayNights': `${nights} Malam`,
                        'displayPricePerNight': this.formatNumber(price),
                        'displayRoomSubtotal': this.formatNumber(this.roomSubtotal)
                    };

                    for (const [id, val] of Object.entries(elements)) {
                        const el = document.getElementById(id);
                        if (el) el[el.tagName === 'INPUT' ? 'value' : 'innerText'] = val;
                    }

                    document.getElementById('checkoutForm').action = `/reservasi/check-out/${this.selectedResId}/process`;
                },

                calculateGrandTotal() {
                    const additional = parseInt(document.getElementById('addCharge').value) || 0;
                    this.grandTotal = this.formatNumber(this.roomSubtotal + additional);
                },

                resetUI() {
                    this.roomSubtotal = 0;
                    this.grandTotal = '0';
                    this.selectedResId = '';
                    const resetIds = ['displayRoom', 'displayIn', 'displayOut', 'printGuestName', 'displayNights', 'displayPricePerNight', 'displayRoomSubtotal'];
                    resetIds.forEach(id => {
                        const el = document.getElementById(id);
                        if(el) el.innerText = id.includes('display') && !id.includes('Nights') ? "-" : "0";
                    });
                    document.getElementById('detailStayPeriod').innerText = "Silakan pilih reservasi";
                },

                openExtendModal() {
                    if(this.selectedResId) {
                        this.extendModal = true;
                    }
                }
            }
        }

        // Global Alert handler untuk SweetAlert Laravel (Optional)
        @if(session('success'))
            Swal.fire('Berhasil!', "{{ session('success') }}", 'success');
        @endif

        // Submit Protection
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const total = document.querySelector('[x-text="grandTotal"]').innerText;
            
            Swal.fire({
                title: 'KONFIRMASI',
                html: `Pastikan tagihan <b>Rp ${total}</b> sudah lunas.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#800000',
                confirmButtonText: 'YA, CHECK-OUT',
                cancelButtonText: 'BATAL'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    </script>
</body>
</html>