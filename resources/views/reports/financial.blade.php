<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>Laporan Keuangan - Hotel SIG</title>
    <style>
        body { font-family: 'Poppins', sans-serif; scroll-behavior: smooth; }
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #800000; border-radius: 10px; }
        @media print {
            .no-print { display: none !important; }
            aside { display: none !important; }
            main { margin-left: 0 !important; }
        }
    </style>
</head>

{{-- TAMBAHAN: x-data untuk kontrol checkbox --}}
<body class="bg-[#F8F9FA] text-gray-800 antialiased" 
      x-data="{ 
        selected: [], 
        allIds: {{ $transactions->pluck('id') }},
        toggleAll() {
            this.selected = (this.selected.length === this.allIds.length) ? [] : [...this.allIds];
        }
      }">
    <x-header></x-header>
    <div class="flex min-h-screen">
        <aside class="w-64 fixed inset-y-0 left-0 z-50 shadow-2xl bg-white border-r border-gray-100 no-print">
            <x-sidebar></x-sidebar>
        </aside>

        <main class="flex-1 ml-64 flex flex-col min-h-screen relative">
            
            <div class="p-6 lg:p-10 max-w-[1600px] mx-auto w-full">
                
                {{-- HEADER TITLE --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
                    <div>
                        <h1 class="text-3xl font-extrabold text-[#800000] tracking-tight uppercase italic leading-none">
                            Financial <span class="text-gray-900 not-italic">Reports</span>
                        </h1>
                        <p class="text-gray-400 text-sm font-medium mt-2">Laporan pendapatan dan transaksi real-time Hotel SIG.</p>
                    </div>
                    
                    <div class="flex items-center gap-3 no-print">
                        {{-- BUTTON HAPUS TERPILIH (Muncul jika ada yang dicentang) --}}
                        <div x-show="selected.length > 0" x-cloak x-transition class="flex items-center">
                            <form action="{{ route('transactions.bulkDelete') }}" method="POST" 
                                  onsubmit="return confirm('Hapus ' + selected.length + ' transaksi yang dipilih?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="ids" :value="selected.join(',')">
                                <button type="submit" class="flex items-center gap-2 bg-red-600 text-white px-6 py-2.5 rounded-2xl font-bold text-xs hover:bg-red-700 transition-all shadow-lg shadow-red-900/20 active:scale-95 mr-2">
                                    <i class="fas fa-trash-alt"></i> Hapus Terpilih (<span x-text="selected.length"></span>)
                                </button>
                            </form>
                            <div class="w-px h-8 bg-gray-200 mx-2"></div>
                        </div>

                        <button onclick="window.print()" class="group flex items-center gap-2 bg-white border border-gray-200 text-gray-600 px-5 py-2.5 rounded-2xl font-bold text-xs hover:bg-gray-50 hover:border-[#800000] hover:text-[#800000] transition-all shadow-sm">
                            <i class="fas fa-print group-hover:scale-110 transition-transform"></i> Cetak Laporan
                        </button>
                        <button class="group flex items-center gap-2 bg-[#800000] text-white px-6 py-2.5 rounded-2xl font-bold text-xs hover:bg-red-900 transition-all shadow-lg shadow-red-900/20 active:scale-95">
                            <i class="fas fa-file-excel group-hover:bounce transition-transform"></i> Ekspor Excel
                        </button>
                    </div>
                </div>

                {{-- KARTU STATISTIK KEUANGAN --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    @php 
                        $cards = [
                            ['Total Pendapatan', 'Rp ' . number_format($totalPendapatan, 0, ',', '.'), 'fas fa-wallet', 'green', 'Semua transaksi dibayar'],
                            ['Pendapatan Kamar', 'Rp ' . number_format($pendapatanKamar, 0, ',', '.'), 'fas fa-bed', 'blue', 'Murni tarif sewa kamar'],
                            ['Biaya Tambahan', 'Rp ' . number_format($pendapatanTambahan, 0, ',', '.'), 'fas fa-plus-circle', 'yellow', 'Denda, layanan ekstra, dll'],
                            ['Total Check-out', $totalTransaksi . ' Transaksi', 'fas fa-receipt', 'purple', 'Jumlah tamu selesai']
                        ];
                    @endphp

                    @foreach($cards as $card)
                    <div class="group bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform
                                {{ $card[3] == 'green' ? 'bg-green-50 text-green-600' : '' }}
                                {{ $card[3] == 'blue' ? 'bg-blue-50 text-blue-600' : '' }}
                                {{ $card[3] == 'yellow' ? 'bg-yellow-50 text-yellow-600' : '' }}
                                {{ $card[3] == 'purple' ? 'bg-purple-50 text-purple-600' : '' }}">
                                <i class="{{ $card[2] }} text-lg"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest
                                {{ $card[3] == 'green' ? 'text-green-500' : '' }}
                                {{ $card[3] == 'blue' ? 'text-blue-500' : '' }}
                                {{ $card[3] == 'yellow' ? 'text-yellow-500' : '' }}
                                {{ $card[3] == 'purple' ? 'text-purple-500' : '' }}">
                                {{ $card[0] }}
                            </span>
                        </div>
                        <h3 class="text-2xl font-black text-gray-800 tracking-tight truncate" title="{{ $card[1] }}">{{ $card[1] }}</h3>
                        <p class="text-gray-400 text-[9px] font-bold uppercase tracking-widest mt-1">{{ $card[4] }}</p>
                    </div>
                    @endforeach
                </div>

                {{-- TABEL RIWAYAT TRANSAKSI CHECK-OUT --}}
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden transition-all">
                    <div class="p-8 border-b border-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-8 bg-[#800000] rounded-full"></div>
                            <h3 class="font-black text-gray-800 uppercase tracking-widest text-sm">Riwayat Transaksi Check-out</h3>
                        </div>
                        <div class="relative w-full sm:w-auto no-print">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
                            <input type="text" placeholder="Cari transaksi..." 
                                class="w-full sm:w-64 pl-10 pr-4 py-3 bg-gray-50 border-transparent rounded-2xl text-xs focus:bg-white focus:ring-2 focus:ring-red-100 focus:border-[#800000] outline-none transition-all">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                    {{-- HEADER CHECKBOX MASTER --}}
                                    <th class="px-8 py-5 no-print">
                                        <input type="checkbox" @click="toggleAll()" :checked="selected.length === allIds.length && allIds.length > 0"
                                               class="w-4 h-4 rounded border-gray-300 text-[#800000] focus:ring-[#800000] cursor-pointer">
                                    </th>
                                    <th class="px-8 py-5">Tanggal C/O</th>
                                    <th class="px-8 py-5">Detail Tamu</th>
                                    <th class="px-8 py-5">Tarif Kamar</th>
                                    <th class="px-8 py-5">Biaya Tambahan</th>
                                    <th class="px-8 py-5 text-center">Total Dibayar</th>
                                    <th class="px-8 py-5 text-center no-print">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($transactions as $trx)
                                    <tr class="transition-colors group" :class="selected.includes({{ $trx->id }}) ? 'bg-red-50/50' : 'hover:bg-red-50/30'">
                                        {{-- CHECKBOX PER BARIS --}}
                                        <td class="px-8 py-5 no-print">
                                            <input type="checkbox" value="{{ $trx->id }}" x-model.number="selected"
                                                   class="w-4 h-4 rounded border-gray-300 text-[#800000] focus:ring-[#800000] cursor-pointer">
                                        </td>

                                        {{-- Tanggal --}}
                                        <td class="px-8 py-5">
                                            <div class="text-xs font-bold text-gray-800">{{ \Carbon\Carbon::parse($trx->checkout_at)->format('d M Y') }}</div>
                                            <div class="text-[10px] font-bold text-gray-500 italic mt-0.5">{{ \Carbon\Carbon::parse($trx->checkout_at)->format('H:i') }} WIB</div>
                                        </td>
                                        
                                        {{-- Detail Tamu --}}
                                        <td class="px-8 py-5">
                                            <div class="text-xs font-bold text-gray-900 uppercase">{{ $trx->reservation->guest_name ?? 'Data Terhapus' }}</div>
                                            <div class="text-[10px] font-bold text-[#800000] mt-1 tracking-wider">
                                                KAMAR {{ $trx->reservation->room->room_number ?? '-' }}
                                            </div>
                                        </td>
                                        
                                        {{-- Tarif Kamar --}}
                                        <td class="px-8 py-5 text-xs font-semibold text-gray-700">
                                            Rp {{ number_format($trx->total_amount - $trx->additional_charges, 0, ',', '.') }}
                                        </td>
                                        
                                        {{-- Biaya Tambahan --}}
                                        <td class="px-8 py-5">
                                            @if($trx->additional_charges > 0)
                                                <div class="text-xs font-bold text-yellow-600">
                                                    + Rp {{ number_format($trx->additional_charges, 0, ',', '.') }}
                                                </div>
                                                <div class="text-[9px] text-gray-400 font-medium italic mt-1 max-w-[150px] truncate" title="{{ $trx->notes }}">
                                                    {{ $trx->notes ?: 'Tanpa keterangan' }}
                                                </div>
                                            @else
                                                <span class="text-gray-300 font-bold">-</span>
                                            @endif
                                        </td>
                                        
                                        {{-- Total --}}
                                        <td class="px-8 py-5 text-center">
                                            <span class="text-[11px] font-black text-green-600 uppercase tracking-widest bg-green-50 px-4 py-2 rounded-xl">
                                                Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                                            </span>
                                        </td>

                                        {{-- AKSI DELETE TUNGGAL --}}
                                        <td class="px-8 py-5 text-center no-print">
                                            <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST" 
                                                  onsubmit="return confirm('Hapus transaksi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-9 h-9 rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all duration-300 flex items-center justify-center mx-auto shadow-sm group-hover:scale-110">
                                                    <i class="fas fa-trash-alt text-[10px]"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-8 py-10 text-center text-gray-400">
                                            <i class="fas fa-file-invoice-dollar text-4xl mb-3 opacity-30"></i>
                                            <p class="text-sm font-medium">Belum ada data transaksi keuangan.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINATION --}}
                    <div class="p-8 bg-gray-50/30 border-t border-gray-50 no-print">
                        @if($transactions instanceof \Illuminate\Pagination\LengthAwarePaginator && $transactions->hasPages())
                            {{ $transactions->links() }}
                        @else
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Menampilkan {{ $transactions->count() }} data transaksi</p>
                        @endif
                    </div>
                </div>

                <footer class="mt-16 text-center border-t border-gray-100 pt-8 no-print">
                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.5em]">Hotel SIG Financial Reporting System v2.0</p>
                    <p class="text-[9px] text-gray-300 mt-2 font-medium">Laporan ini dibuat otomatis oleh sistem pada {{ date('d/m/Y H:i') }}</p>
                </footer>
            </div>
        </main>
    </div>
</body>
</html>