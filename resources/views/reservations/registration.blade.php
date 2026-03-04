<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Check-in System | Hotel SIG</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F9FAFB; }
        .btn-checkin { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .btn-checkin:hover { transform: translateY(-3px); box-shadow: 0 10px 20px -5px rgba(128, 0, 0, 0.1); }
        
        @keyframes swing {
            0% { transform: rotate(0deg); }
            10% { transform: rotate(10deg); }
            30% { transform: rotate(-10deg); }
            50% { transform: rotate(5deg); }
            70% { transform: rotate(-5deg); }
            100% { transform: rotate(0deg); }
        }
        .animate-swing { animation: swing 2s infinite; }
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
                <div class="max-w-6xl mx-auto mb-8 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-3">
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

                            <td class="px-8 py-7 text-center">
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
                                    <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-[#800000]">
                                        <i class="fas fa-wallet text-xs"></i>
                                    </div>
                                    <div>
                                        <span class="block text-[8px] font-bold text-gray-400 uppercase leading-none mb-1">Via</span>
                                        <span class="text-xs font-black text-gray-700 tracking-tight">{{ $res->payment_method }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-8 py-7 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick='showGuestDetail(@json($res->load("room")))' 
                                        class="btn-checkin bg-gray-100 text-gray-600 text-[10px] font-black px-4 py-3.5 rounded-xl uppercase tracking-widest inline-flex items-center gap-2 hover:bg-gray-200">
                                        <i class="fas fa-eye text-sm"></i> Detail
                                    </button>

                                    <button onclick="cancelReservation({{ $res->id }}, '{{ $res->guest_name }}')" 
                                        class="btn-checkin bg-white border border-red-200 text-red-500 text-[10px] font-black px-4 py-3.5 rounded-xl uppercase tracking-widest inline-flex items-center gap-2 hover:bg-red-50">
                                        <i class="fas fa-times-circle text-sm"></i> Cancel
                                    </button>

                                    @if($res->reservation_type == 'non-guaranteed')
                                        <button onclick="redirectToPayment({{ $res->id }}, '{{ $res->guest_name }}')" 
                                            class="btn-checkin bg-blue-600 text-white text-[10px] font-black px-6 py-3.5 rounded-xl shadow-lg shadow-blue-100 uppercase tracking-widest inline-flex items-center gap-2">
                                            <i class="fas fa-hand-holding-usd"></i> Settlement & In
                                        </button>
                                    @else
                                        <form action="{{ route('reservations.checkin', $res->id) }}" method="POST" class="inline-block" onsubmit="return handleCheckin(this)">
                                            @csrf
                                            <button type="submit" 
                                                class="btn-checkin bg-[#800000] text-white text-[10px] font-black px-6 py-3.5 rounded-xl shadow-lg shadow-red-100 uppercase tracking-widest inline-flex items-center gap-2">
                                                <i class="fas fa-key-skeleton"></i> Activate Room
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-32 text-center text-gray-300">
                                <i class="fas fa-calendar-check text-4xl mb-4"></i>
                                <p class="uppercase font-black text-xs tracking-widest">Tidak ada tamu menunggu</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function showGuestDetail(data) {
            if (!data) return;
            Swal.fire({
                title: '<span class="text-xs uppercase tracking-[0.3em] font-black text-gray-400">Complete Reservation Details</span>',
                width: '600px',
                html: `
                    <div class="text-left mt-6 space-y-4 px-2 max-h-[60vh] overflow-y-auto pr-2">
                        <div class="bg-gray-50 p-5 rounded-[2rem] border border-gray-100">
                            <p class="text-[9px] font-black text-[#800000] uppercase tracking-widest mb-3">Personal Information</p>
                            <p class="font-black text-gray-900 text-xl tracking-tight">${data.guest_name || 'N/A'}</p>
                            <p class="text-[11px] font-bold text-gray-400 mt-2 italic">${data.email || '-'} | ${data.phone || '-'}</p>
                        </div>
                        <div class="bg-gray-50 p-5 rounded-[2rem] border border-gray-100">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Room Details</p>
                            <p class="text-lg font-black text-gray-800">Room ${data.room ? data.room.room_number : '-'}</p>
                            <p class="text-[10px] font-bold text-[#800000]">${data.room ? data.room.type : '-'}</p>
                        </div>
                    </div>
                `,
                confirmButtonColor: '#10b981',
                confirmButtonText: 'Tutup',
                customClass: { popup: 'rounded-[3rem]' }
            });
        }

        function cancelReservation(id, name) {
            Swal.fire({
                title: 'Batalkan Reservasi?',
                text: "Anda akan membatalkan reservasi atas nama " + name,
                icon: 'warning',
                input: 'select',
                inputOptions: {
                    'No Show': 'Tamu Tidak Datang (No Show)',
                    'Guest Request': 'Permintaan Tamu',
                    'Double Booking': 'Kesalahan Input (Double Booking)',
                    'Force Majeure': 'Keadaan Darurat (Force Majeure)',
                    'Other': 'Lainnya'
                },
                inputPlaceholder: '-- Pilih Alasan Pembatalan --',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#f1f5f9',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: '<span class="text-gray-500 font-bold">Tutup</span>',
                inputValidator: (value) => {
                    if (!value) return 'Anda harus memilih alasan!'
                },
                customClass: {
                    popup: 'rounded-[2rem]',
                    confirmButton: 'rounded-xl px-6 py-3 text-[10px] font-black uppercase',
                    cancelButton: 'rounded-xl px-6 py-3 text-[10px] font-black uppercase'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ url('reservations') }}/" + id + "/cancel"; 
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    
                    const reasonInput = document.createElement('input');
                    reasonInput.type = 'hidden';
                    reasonInput.name = 'cancel_reason';
                    reasonInput.value = result.value;
                    
                    form.appendChild(csrfInput);
                    form.appendChild(reasonInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            })
        }

        function redirectToPayment(id, name) {
            Swal.fire({
                title: 'Lanjutkan ke Pembayaran?',
                text: "Tamu " + name + " membutuhkan settlement pembayaran.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Buka Kasir'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('payments.index') }}?reservation_id=" + id;
                }
            })
        }

        function handleCheckin(form) {
            const btn = form.querySelector('button');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner animate-spin"></i> Processing...';
            return true;
        }
    </script>
</body>
</html>