<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <title>Manajemen Tamu | Hotel SIG</title>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; overflow-x: hidden; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f8fafc; }
        ::-webkit-scrollbar-thumb { background: #800000; border-radius: 10px; }
        .table-container { scrollbar-gutter: stable; }
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-[#F8FAFC] text-slate-700" x-data="{ 
    selectedGuests: [], 
    allIds: {{ $guests->pluck('id') }},
    toggleAll() {
        if (this.selectedGuests.length === this.allIds.length) {
            this.selectedGuests = [];
        } else {
            this.selectedGuests = [...this.allIds];
        }
    }
}">

    <header class="fixed top-0 left-0 right-0 z-[60] bg-white/80 backdrop-blur-md border-b border-gray-100">
        <x-header></x-header>
    </header>

    <div class="flex min-h-screen">
        <aside class="fixed inset-y-0 left-0 w-64 z-50 bg-white border-r border-gray-100 hidden lg:block">
            <div class="pt-20 h-full">
                <x-sidebar></x-sidebar>
            </div>
        </aside>

        <div class="flex-1 lg:ml-64 flex flex-col pt-20">
            <main class="p-6 md:p-10 lg:p-12">
                
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
                    <div>
                        <nav class="flex items-center gap-2 mb-3">
                            <span class="w-8 h-[2px] bg-[#800000]"></span>
                            <span class="text-[10px] font-black text-[#800000] uppercase tracking-[0.3em]">Administrator</span>
                        </nav>
                        <h1 class="text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight flex items-center gap-4">
                            <span class="p-3 bg-[#800000] text-white rounded-2xl shadow-lg shadow-[#800000]/20">
                                <i class="fas fa-users-cog text-xl"></i>
                            </span>
                            MANAJEMEN TAMU
                        </h1>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <div x-show="selectedGuests.length > 0" x-cloak x-transition class="flex items-center animate-bounce">
                            <form action="{{ route('guests.bulk-delete') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tamu yang dipilih secara permanen?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="ids" :value="selectedGuests.join(',')">
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-2xl text-[11px] font-black shadow-lg shadow-red-200 flex items-center gap-3 transition-all active:scale-95">
                                    <i class="fas fa-trash-alt"></i>
                                    HAPUS <span x-text="selectedGuests.length"></span> TAMU
                                </button>
                            </form>
                        </div>

                        <div class="bg-white p-4 px-6 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-4">
                            <div>
                                <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Tamu Terpilih</span>
                                <span class="text-2xl font-black text-slate-800" x-text="selectedGuests.length">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/40 overflow-hidden border border-slate-100">
                    <div class="overflow-x-auto table-container">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50">
                                    <th class="px-8 py-6 text-left border-b border-slate-100 w-10">
                                        <input type="checkbox" 
                                               @click="toggleAll()" 
                                               :checked="selectedGuests.length === allIds.length && allIds.length > 0"
                                               class="w-5 h-5 rounded-lg border-slate-300 text-[#800000] focus:ring-[#800000] cursor-pointer">
                                    </th>
                                    <th class="px-8 py-6 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.15em] border-b border-slate-100">Informasi Profil</th>
                                    <th class="px-8 py-6 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.15em] border-b border-slate-100">Status Keamanan</th>
                                    <th class="px-8 py-6 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.15em] border-b border-slate-100">Kredensial</th>
                                    <th class="px-8 py-6 text-center text-[11px] font-black text-slate-400 uppercase tracking-[0.15em] border-b border-slate-100">Opsi Lanjutan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($guests as $guest)
                                <tr class="group hover:bg-slate-50/50 transition-all duration-300" :class="selectedGuests.includes({{ $guest->id }}) ? 'bg-red-50/30' : ''">
                                    <td class="px-8 py-6">
                                        <input type="checkbox" 
                                               x-model="selectedGuests" 
                                               value="{{ $guest->id }}"
                                               class="w-5 h-5 rounded-lg border-slate-300 text-[#800000] focus:ring-[#800000] cursor-pointer">
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-5">
                                            <div class="relative">
                                                <div class="w-14 h-14 rounded-2xl {{ $guest->is_incognito ? 'bg-slate-900 shadow-lg shadow-slate-900/20' : 'bg-red-50' }} flex items-center justify-center transition-all group-hover:rotate-6 duration-500">
                                                    <i class="fas {{ $guest->is_incognito ? 'fa-user-secret text-slate-200' : 'fa-user text-[#800000]' }} text-xl"></i>
                                                </div>
                                                @if(!$guest->is_incognito)
                                                    <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 border-4 border-white rounded-full"></span>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="text-base font-bold text-slate-800 {{ $guest->is_incognito ? 'italic text-slate-400' : '' }}">
                                                    {{ $guest->is_incognito ? 'Restricted Identity' : $guest->guest_name }}
                                                </div>
                                                <div class="inline-block mt-1 px-2 py-0.5 bg-slate-100 text-[9px] font-bold text-slate-500 rounded uppercase tracking-tighter">
                                                    UID: {{ strtoupper(substr($guest->id, 0, 8)) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-2xl {{ $guest->is_incognito ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100' }}">
                                            <span class="text-[10px] font-black uppercase tracking-widest">
                                                {{ $guest->is_incognito ? 'Incognito Mode' : 'Verified Public' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-semibold text-slate-600">{{ $guest->email }}</span>
                                            <span class="text-[11px] text-slate-400 flex items-center gap-2 mt-1 italic">
                                                Joined {{ $guest->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <form action="{{ route('guests.toggle-incognito', $guest->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-3 bg-white border-2 border-slate-100 text-slate-700 px-5 py-2.5 rounded-2xl text-[10px] font-black hover:border-[#800000] hover:text-[#800000] transition-all active:scale-95">
                                                <i class="fas fa-shield-alt"></i>
                                                PRIVACY
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-32 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-folder-open text-5xl text-slate-200 mb-6"></i>
                                            <h3 class="text-slate-800 font-bold text-lg">No Active Records</h3>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>