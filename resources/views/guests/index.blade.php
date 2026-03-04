<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <title>{{ __('guest.management_title') }} | Hotel SIG</title>
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
        {{-- Sidebar --}}
        <aside class="fixed inset-y-0 left-0 w-64 z-50 bg-white border-r border-gray-100 hidden lg:block">
            <div class="pt-20 h-full">
                <x-sidebar></x-sidebar>
            </div>
        </aside>

        <div class="flex-1 lg:ml-64 flex flex-col pt-20">
            <main class="p-4 md:p-10 lg:p-12">
                
                {{-- Flash Message --}}
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 mb-6 rounded-2xl shadow-sm flex items-center gap-3">
                        <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                        <div>
                            <p class="font-bold text-sm">{{ __('guest.success_msg') }}</p>
                            <p class="text-xs">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                {{-- Header Section --}}
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
                    <div>
                        <nav class="flex items-center gap-2 mb-3">
                            <span class="w-8 h-[2px] bg-[#800000]"></span>
                            <span class="text-[10px] font-black text-[#800000] uppercase tracking-[0.3em]">{{ __('guest.admin_label') }}</span>
                        </nav>
                        <h1 class="text-2xl md:text-4xl font-extrabold text-slate-900 tracking-tight flex items-center gap-4">
                            <span class="p-3 bg-[#800000] text-white rounded-2xl shadow-lg shadow-[#800000]/20">
                                <i class="fas fa-users-cog text-xl"></i>
                            </span>
                            {{ strtoupper(__('guest.management_title')) }}
                        </h1>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        {{-- Bulk Delete Button --}}
                        <div x-show="selectedGuests.length > 0" x-cloak x-transition class="flex items-center">
                            <form action="{{ route('guests.bulk-delete') }}" method="POST" onsubmit="return confirm('{{ __('guest.delete_confirm') }}')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="ids" :value="selectedGuests.join(',')">
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-2xl text-[11px] font-black shadow-lg shadow-red-200 flex items-center gap-3 transition-all active:scale-95">
                                    <i class="fas fa-trash-alt"></i>
                                    {{ strtoupper(__('guest.bulk_delete', ['count' => ''])) }} <span x-text="selectedGuests.length"></span>
                                </button>
                            </form>
                        </div>

                        {{-- Selection Counter --}}
                        <div class="bg-white p-4 px-6 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-4">
                            <div>
                                <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ __('guest.selected_count') }}</span>
                                <span class="text-2xl font-black text-slate-800" x-text="selectedGuests.length">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Main Content --}}
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/40 overflow-hidden border border-slate-100">
                    
                    {{-- DESKTOP VIEW: TABLE --}}
                    <div class="hidden md:block overflow-x-auto table-container">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50">
                                    <th class="px-8 py-6 text-left border-b border-slate-100 w-10">
                                        <input type="checkbox" @click="toggleAll()" :checked="selectedGuests.length === allIds.length && allIds.length > 0" class="w-5 h-5 rounded-lg border-slate-300 text-[#800000] focus:ring-[#800000] cursor-pointer">
                                    </th>
                                    <th class="px-8 py-6 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.15em] border-b border-slate-100">{{ __('guest.info_profile') }}</th>
                                    <th class="px-8 py-6 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.15em] border-b border-slate-100">{{ __('guest.status_room') }}</th>
                                    <th class="px-8 py-6 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.15em] border-b border-slate-100">{{ __('guest.contact_info') }}</th>
                                    <th class="px-8 py-6 text-center text-[11px] font-black text-slate-400 uppercase tracking-[0.15em] border-b border-slate-100">{{ __('guest.options') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($guests as $guest)
                                <tr class="group hover:bg-slate-50/50 transition-all duration-300" x-data="{ editModal: false }" :class="selectedGuests.includes({{ $guest->id }}) ? 'bg-red-50/30' : ''">
                                    <td class="px-8 py-6">
                                        <input type="checkbox" x-model="selectedGuests" value="{{ $guest->id }}" class="w-5 h-5 rounded-lg border-slate-300 text-[#800000] focus:ring-[#800000] cursor-pointer">
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-5">
                                            <div class="w-14 h-14 rounded-2xl {{ $guest->is_incognito ? 'bg-slate-900 shadow-lg shadow-slate-900/20' : 'bg-red-50' }} flex items-center justify-center transition-all group-hover:rotate-6 duration-500">
                                                <i class="fas {{ $guest->is_incognito ? 'fa-user-secret text-slate-200' : 'fa-user text-[#800000]' }} text-xl"></i>
                                            </div>
                                            <div>
                                                <div class="text-base font-bold text-slate-800 {{ $guest->is_incognito ? 'italic text-slate-400' : '' }}">
                                                    {{ $guest->is_incognito ? __('guest.restricted') : $guest->guest_name }}
                                                </div>
                                                <div class="inline-block mt-1 px-2 py-0.5 bg-slate-100 text-[9px] font-bold text-slate-500 rounded uppercase tracking-tighter">UID: {{ strtoupper(substr($guest->id, 0, 8)) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col gap-2">
                                            @if($guest->room)
                                                <div class="flex items-center gap-2">
                                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-black border border-slate-200">RM-{{ $guest->room->room_number }}</span>
                                                    <span class="text-[10px] font-black {{ $guest->room->status == 'occupied' ? 'text-emerald-600' : 'text-blue-600' }} uppercase tracking-widest flex items-center gap-1.5">
                                                        <span class="relative flex h-2 w-2">
                                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $guest->room->status == 'occupied' ? 'bg-emerald-400' : 'bg-blue-400' }} opacity-75"></span>
                                                            <span class="relative inline-flex rounded-full h-2 w-2 {{ $guest->room->status == 'occupied' ? 'bg-emerald-500' : 'bg-blue-500' }}"></span>
                                                        </span>
                                                        {{ strtoupper($guest->room->status) }}
                                                    </span>
                                                </div>
                                            @else
                                                <div class="flex items-center gap-2 text-slate-300">
                                                    <span class="px-2.5 py-1 bg-slate-50 text-slate-400 rounded-lg text-xs font-black border border-slate-100 italic">N/A</span>
                                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-1.5"><i class="fas fa-user-slash text-[9px]"></i> {{ strtoupper(__('guest.no_reservation')) }}</span>
                                                </div>
                                            @endif
                                            <div class="inline-flex items-center gap-2 px-2 py-1 rounded-md w-max {{ $guest->is_incognito ? 'bg-indigo-50 text-indigo-600' : 'bg-blue-50 text-blue-600' }}">
                                                <i class="fas {{ $guest->is_incognito ? 'fa-eye-slash' : 'fa-globe' }} text-[9px]"></i>
                                                <span class="text-[9px] font-black uppercase tracking-tighter">{{ $guest->is_incognito ? __('guest.privacy_on') : __('guest.public_profile') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-semibold text-slate-600"><i class="fas fa-envelope text-slate-400 mr-2 text-xs"></i>{{ $guest->email }}</span>
                                            <span class="text-xs font-semibold text-slate-500 mt-1"><i class="fas fa-phone text-slate-400 mr-2 text-xs"></i>{{ $guest->phone }}</span>
                                            <span class="text-[10px] text-slate-400 flex items-center gap-2 mt-2 italic">{{ __('guest.joined') }} {{ $guest->created_at->diffForHumans() }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <form action="{{ route('guests.toggle-incognito', $guest->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-10 h-10 rounded-xl bg-white border-2 border-slate-100 text-slate-500 hover:border-indigo-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all flex items-center justify-center"><i class="fas fa-shield-alt"></i></button>
                                            </form>
                                            <button @click="editModal = true" class="w-10 h-10 rounded-xl bg-white border-2 border-slate-100 text-slate-500 hover:border-blue-500 hover:text-blue-600 hover:bg-blue-50 transition-all flex items-center justify-center"><i class="fas fa-pen"></i></button>
                                            <form action="{{ route('guests.destroy', $guest->id) }}" method="POST" id="delete-form-{{ $guest->id }}">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="confirmDelete({{ $guest->id }}, '{{ $guest->guest_name }}')" class="w-10 h-10 rounded-xl bg-white border-2 border-slate-100 text-slate-500 hover:border-red-500 hover:text-red-600 hover:bg-red-50 transition-all flex items-center justify-center"><i class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </div>

                                        {{-- MODAL EDIT (Desktop) --}}
                                        <div x-show="editModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto">
                                            <div class="flex items-center justify-center min-h-screen px-4">
                                                <div x-show="editModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="editModal = false"></div>
                                                <div x-show="editModal" x-transition class="bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-lg relative z-10 border border-slate-100">
                                                    <form action="{{ route('guests.update', $guest->id) }}" method="POST">
                                                        @csrf @method('PUT')
                                                        <div class="p-8">
                                                            <div class="flex items-center gap-4 mb-6 border-b border-slate-100 pb-4">
                                                                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl"><i class="fas fa-user-edit"></i></div>
                                                                <div>
                                                                    <h3 class="text-xl font-black text-slate-800 uppercase tracking-wider">{{ __('guest.edit_title') }}</h3>
                                                                    <p class="text-xs font-bold text-slate-400">{{ __('guest.edit_subtitle') }}</p>
                                                                </div>
                                                            </div>
                                                            <div class="space-y-5">
                                                                <div>
                                                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">{{ __('guest.full_name') }}</label>
                                                                    <input type="text" name="guest_name" value="{{ $guest->guest_name }}" class="w-full rounded-xl bg-slate-50 border-transparent focus:bg-white focus:border-[#800000] focus:ring-2 focus:ring-red-100 transition-all text-sm font-semibold p-3" required>
                                                                </div>
                                                                <div>
                                                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">{{ __('guest.email') }}</label>
                                                                    <input type="email" name="email" value="{{ $guest->email }}" class="w-full rounded-xl bg-slate-50 border-transparent focus:bg-white focus:border-[#800000] focus:ring-2 focus:ring-red-100 transition-all text-sm font-semibold p-3" required>
                                                                </div>
                                                                <div>
                                                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">{{ __('guest.phone') }}</label>
                                                                    <input type="text" name="phone" value="{{ $guest->phone }}" class="w-full rounded-xl bg-slate-50 border-transparent focus:bg-white focus:border-[#800000] focus:ring-2 focus:ring-red-100 transition-all text-sm font-semibold p-3" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="bg-slate-50 px-8 py-5 flex justify-end gap-3 border-t border-slate-100">
                                                            <button type="button" @click="editModal = false" class="px-6 py-3 bg-white border border-slate-200 rounded-xl text-xs font-black text-slate-600 hover:bg-slate-50 transition-all">{{ __('guest.cancel') }}</button>
                                                            <button type="submit" class="px-6 py-3 bg-[#800000] text-white rounded-xl text-xs font-black shadow-lg shadow-[#800000]/30 hover:bg-red-900 transition-all">{{ __('guest.save') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="py-32 text-center text-slate-400 uppercase font-black tracking-widest">{{ __('guest.empty') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- MOBILE VIEW: CARDS --}}
                    <div class="md:hidden divide-y divide-slate-100">
                        @forelse($guests as $guest)
                        <div class="p-5 flex flex-col gap-4" x-data="{ editModal: false }">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-4">
                                    <input type="checkbox" x-model="selectedGuests" value="{{ $guest->id }}" class="w-5 h-5 rounded-lg border-slate-300 text-[#800000]">
                                    <div class="w-12 h-12 rounded-2xl {{ $guest->is_incognito ? 'bg-slate-900' : 'bg-red-50' }} flex items-center justify-center">
                                        <i class="fas {{ $guest->is_incognito ? 'fa-user-secret text-slate-200' : 'fa-user text-[#800000]' }}"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800 leading-tight">{{ $guest->is_incognito ? __('guest.restricted') : $guest->guest_name }}</h4>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">UID: {{ strtoupper(substr($guest->id, 0, 8)) }}</p>
                                    </div>
                                </div>
                                @if($guest->room)
                                    <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded-lg text-[10px] font-black border border-slate-200">RM-{{ $guest->room->room_number }}</span>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 gap-2 bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-envelope text-slate-300 text-xs w-4"></i>
                                    <span class="text-xs font-semibold text-slate-600 truncate">{{ $guest->email }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-phone text-slate-300 text-xs w-4"></i>
                                    <span class="text-xs font-semibold text-slate-600">{{ $guest->phone }}</span>
                                </div>
                                <div class="mt-1">
                                    <span class="text-[9px] font-black px-2 py-1 rounded {{ $guest->room && $guest->room->status == 'occupied' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }} uppercase">
                                        {{ $guest->room ? strtoupper($guest->room->status) : __('guest.no_reservation') }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex gap-2">
                                    <form action="{{ route('guests.toggle-incognito', $guest->id) }}" method="POST">@csrf<button class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-400"><i class="fas fa-shield-alt"></i></button></form>
                                    <button @click="editModal = true" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-400"><i class="fas fa-pen"></i></button>
                                    <button onclick="confirmDelete({{ $guest->id }}, '{{ $guest->guest_name }}')" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-400"><i class="fas fa-trash-alt"></i></button>
                                </div>
                                <span class="text-[10px] text-slate-400 italic">{{ $guest->created_at->diffForHumans() }}</span>
                            </div>

                            {{-- MODAL EDIT (Mobile) --}}
                            <div x-show="editModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto">
                                <div class="flex items-end md:items-center justify-center min-h-screen">
                                    <div x-show="editModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="editModal = false"></div>
                                    <div x-show="editModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" class="bg-white rounded-t-[2rem] md:rounded-[2rem] text-left overflow-hidden shadow-2xl w-full max-w-lg relative z-10 border border-slate-100">
                                        <form action="{{ route('guests.update', $guest->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="p-8">
                                                <div class="flex items-center gap-4 mb-6 border-b border-slate-100 pb-4">
                                                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl"><i class="fas fa-user-edit"></i></div>
                                                    <div><h3 class="text-xl font-black text-slate-800 uppercase tracking-wider">{{ __('guest.edit_title') }}</h3></div>
                                                </div>
                                                <div class="space-y-4">
                                                    <div>
                                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ __('guest.full_name') }}</label>
                                                        <input type="text" name="guest_name" value="{{ $guest->guest_name }}" class="w-full rounded-xl bg-slate-50 border-transparent p-3 text-sm font-semibold" required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ __('guest.email') }}</label>
                                                        <input type="email" name="email" value="{{ $guest->email }}" class="w-full rounded-xl bg-slate-50 border-transparent p-3 text-sm font-semibold" required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ __('guest.phone') }}</label>
                                                        <input type="text" name="phone" value="{{ $guest->phone }}" class="w-full rounded-xl bg-slate-50 border-transparent p-3 text-sm font-semibold" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-slate-50 px-8 py-5 flex flex-col gap-2 border-t border-slate-100">
                                                <button type="submit" class="w-full py-4 bg-[#800000] text-white rounded-xl text-xs font-black shadow-lg">{{ __('guest.save') }}</button>
                                                <button type="button" @click="editModal = false" class="w-full py-4 bg-white border border-slate-200 rounded-xl text-xs font-black text-slate-600">{{ __('guest.cancel') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="py-20 text-center text-slate-400 uppercase font-black text-xs tracking-widest">{{ __('guest.empty') }}</div>
                        @endforelse
                    </div>

                </div>
            </main>
        </div>
    </div>

     <x-bottom-nav></x-bottom-nav>
    <x-mobile-menu></x-mobile-menu>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id, name) {
            Swal.fire({
                title: "{{ __('guest.swal_delete_title') }}",
                html: `{!! __('guest.swal_delete_text', ['name' => '${name}']) !!}<br><span class='text-red-500 text-[10px] mt-2 block font-bold uppercase'>{{ __('guest.swal_delete_warning') }}</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#800000',
                confirmButtonText: "{{ __('guest.swal_confirm') }}",
                cancelButtonText: "{{ __('guest.cancel') }}",
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-[2rem]',
                    confirmButton: 'rounded-xl text-xs font-black tracking-wider px-6 py-3',
                    cancelButton: 'rounded-xl text-xs font-black tracking-wider px-6 py-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
</body>
</html>