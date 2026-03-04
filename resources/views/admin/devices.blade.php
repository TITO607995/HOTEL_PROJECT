<!DOCTYPE html>
<html lang="id" class="h-full overflow-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Monitor - Hotel SIG</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; overscroll-behavior: none; }
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.6); }
        .maroon-gradient { background: linear-gradient(135deg, #800000 0%, #4a0000 100%); }
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 20px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #800000; }
    </style>
</head>
<body class="bg-[#F8FAFC] antialiased h-full">

    <div class="fixed top-0 left-0 right-0 z-[60] bg-white border-b border-slate-100">
        <x-header></x-header>
    </div>
    
    <div class="flex h-screen pt-16 overflow-hidden"> 
        <aside class="hidden lg:block w-72 flex-shrink-0 border-r border-slate-100 bg-white">
            <x-sidebar></x-sidebar>
        </aside>

        <main class="flex-1 h-full overflow-y-auto custom-scroll">
            <div class="max-w-7xl mx-auto p-6 lg:p-10">

                <header class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
                    <div class="space-y-1">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-red-50 rounded-full border border-red-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#800000] animate-pulse"></span>
                            <span class="text-[10px] font-black text-[#800000] uppercase tracking-[0.2em]">Firewall Active</span>
                        </div>
                        <h1 class="text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">
                            Device <span class="text-[#800000] italic">Activity</span>
                        </h1>
                        <p class="text-slate-500 font-medium text-sm lg:text-base">Monitor dan blokir akses mencurigakan secara real-time.</p>
                    </div>

                    <div class="bg-white p-1 rounded-[2rem] shadow-xl shadow-slate-200/60 border border-slate-100 min-w-[200px]">
                        <div class="bg-slate-50 rounded-[1.8rem] px-6 py-4 flex items-center gap-4">
                            <div class="w-12 h-12 maroon-gradient rounded-2xl flex items-center justify-center text-white shadow-lg">
                                <i class="fas fa-shield-virus"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sessions</p>
                                <p class="text-3xl font-black text-slate-800 leading-none mt-0.5">{{ count($devices) }}</p>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="glass-card rounded-[2.5rem] shadow-2xl shadow-slate-300/30 overflow-hidden mb-10 border border-white">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th class="px-8 py-7 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">User Profile</th>
                                    <th class="px-8 py-7 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">System</th>
                                    <th class="px-8 py-7 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">IP Address</th>
                                    <th class="px-8 py-7 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Status</th>
                                    <th class="px-8 py-7 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Firewall Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($devices as $device)
                                <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="relative flex-shrink-0">
                                                <div class="w-14 h-14 rounded-2xl maroon-gradient flex items-center justify-center text-white text-xl font-bold">
                                                    {{ strtoupper(substr($device['user_name'] ?? 'U', 0, 1)) }}
                                                </div>
                                                <div class="absolute -bottom-1 -right-1 w-5 h-5 border-4 border-white rounded-full {{ ($device['is_online'] ?? false) ? 'bg-green-500' : 'bg-slate-300' }}"></div>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-bold text-slate-900 truncate">{{ $device['user_name'] }}</h4>
                                                <p class="text-xs text-slate-400 truncate">{{ $device['email'] }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-3">
                                            <div class="text-2xl text-slate-400">
                                                @php $platform = strtolower($device['platform'] ?? ''); @endphp
                                                @if(str_contains($platform, 'win')) <i class="fab fa-windows"></i>
                                                @elseif(str_contains($platform, 'mac') || str_contains($platform, 'ios')) <i class="fab fa-apple"></i>
                                                @elseif(str_contains($platform, 'android')) <i class="fab fa-android"></i>
                                                @else <i class="fas fa-desktop"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-700 leading-tight">{{ $device['platform'] }}</p>
                                                <p class="text-[10px] text-slate-400 font-bold uppercase">{{ $device['browser'] }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-8 py-6 text-center">
                                        <span class="inline-block font-mono text-xs font-bold text-[#800000] bg-red-50 border border-red-100 px-3 py-1.5 rounded-lg">
                                            {{ $device['ip_address'] }}
                                        </span>
                                    </td>

                                    <td class="px-8 py-6">
                                        @if($device['is_online'] ?? false)
                                            <span class="text-[10px] font-black text-green-600 uppercase italic animate-pulse">● Active</span>
                                        @else
                                            <span class="text-[11px] font-bold text-slate-400 italic">{{ $device['last_active'] }}</span>
                                        @endif
                                    </td>

                                    <td class="px-8 py-6">
                                        <div class="flex items-center justify-center gap-2">
                                            @if(!($device['is_current'] ?? false))
                                                {{-- Kick Button --}}
                                                <form id="logout-form-{{ $device['id'] }}" action="{{ route('admin.devices.logout', $device['id']) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="button" onclick="confirmKick('{{ $device['id'] }}', '{{ $device['user_name'] }}')" 
                                                        class="w-10 h-10 rounded-xl border border-red-100 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center">
                                                        <i class="fas fa-power-off text-sm"></i>
                                                    </button>
                                                </form>

                                                {{-- Block IP Button --}}
                                                <form id="block-form-{{ $device['id'] }}" action="{{ route('admin.ip.block') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="ip_address" value="{{ $device['ip_address'] }}">
                                                    <button type="button" onclick="confirmBlock('{{ $device['id'] }}', '{{ $device['ip_address'] }}')" 
                                                        class="w-10 h-10 rounded-xl border border-slate-200 bg-white text-slate-900 hover:bg-black hover:text-white transition-all flex items-center justify-center">
                                                        <i class="fas fa-user-slash text-sm"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <div class="px-4 py-2 bg-slate-900 text-white rounded-xl shadow-lg">
                                                    <span class="text-[10px] font-black uppercase">Your Device</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <footer class="flex flex-col sm:flex-row justify-between items-center gap-4 py-8 border-t border-slate-200">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.4em]">© 2026 HOTEL SIG CYBER DEFENSE</p>
                </footer>
            </div>
        </main>
    </div>

    <script>
        // SweetAlert Kick
        function confirmKick(id, name) {
            Swal.fire({
                title: 'Putuskan Sesi?',
                text: "User " + name + " akan dipaksa keluar!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#800000',
                confirmButtonText: 'Ya, Kick!',
                customClass: { popup: 'rounded-[2rem]' }
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('logout-form-' + id).submit();
            })
        }

        // SweetAlert Block IP
        function confirmBlock(id, ip) {
            Swal.fire({
                title: 'BLACKLIST IP?',
                text: "IP " + ip + " tidak akan bisa akses web ini lagi!",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#000000',
                confirmButtonText: 'Ya, Blokir Permanen!',
                customClass: { popup: 'rounded-[2rem]' }
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('block-form-' + id).submit();
            })
        }

        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", confirmButtonColor: '#800000', customClass: { popup: 'rounded-[2rem]' } });
        @endif
    </script>
</body>
</html>