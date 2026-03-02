<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Monitoring - Hotel SIG</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-[#F8F9FA] font-sans antialiased">

    <x-header></x-header>

    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="w-64 fixed inset-y-0 left-0 z-50 shadow-2xl bg-white border-r border-gray-100 no-print">
            <x-sidebar></x-sidebar>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 ml-64 flex flex-col min-h-screen relative">
            <div class="p-6 lg:p-10 max-w-[1600px] mx-auto w-full">
                
                {{-- Header Title --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
                    <div>
                        <h1 class="text-3xl font-extrabold text-[#800000] tracking-tight uppercase italic leading-none">
                            Device <span class="text-gray-900 not-italic">Monitoring</span>
                        </h1>
                        <p class="text-gray-400 text-sm font-medium mt-2">Daftar perangkat yang sedang login ke sistem Hotel SIG.</p>
                    </div>
                </div>

                {{-- Tabel Device Card --}}
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden transition-all">
                    <div class="p-8 border-b border-gray-50 flex items-center gap-3">
                        <div class="w-2 h-8 bg-[#800000] rounded-full"></div>
                        <h3 class="font-black text-gray-800 uppercase tracking-widest text-sm">Sesi Aktif Saat Ini</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                    <th class="px-8 py-5">User / Email</th>
                                    <th class="px-8 py-5">Perangkat</th>
                                    <th class="px-8 py-5 text-center">IP Address</th>
                                    <th class="px-8 py-5">Aktif Terakhir</th>
                                    <th class="px-8 py-5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($devices as $device)
                                <tr class="transition-colors group {{ $device['is_current'] ? 'bg-green-50/30' : 'hover:bg-red-50/30' }}">
                                    {{-- User Info --}}
                                    <td class="px-8 py-5">
                                        <div class="text-xs font-bold text-gray-800 uppercase">{{ $device['user_name'] }}</div>
                                        <div class="text-[10px] font-medium text-gray-400 italic">{{ $device['email'] }}</div>
                                        @if($device['is_current'])
                                            <span class="mt-1 inline-block text-[8px] font-black bg-green-500 text-white px-2 py-0.5 rounded-full tracking-tighter uppercase">Perangkat Ini</span>
                                        @endif
                                    </td>

                                    {{-- Device Info --}}
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg opacity-80 group-hover:opacity-100 transition-opacity">
                                                {{ str_contains($device['device_type'], 'Desktop') ? '💻' : '📱' }}
                                            </span>
                                            <div>
                                                <div class="text-xs font-bold text-gray-700">{{ $device['platform'] }}</div>
                                                <div class="text-[10px] text-gray-400 font-semibold italic">{{ $device['browser'] }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- IP Address --}}
                                    <td class="px-8 py-5 text-center">
                                        <span class="font-mono text-[11px] bg-gray-100 px-3 py-1 rounded-md text-gray-600 border border-gray-200">
                                            {{ $device['ip_address'] }}
                                        </span>
                                    </td>

                                    {{-- Last Active --}}
                                    <td class="px-8 py-5 text-xs font-semibold text-gray-500 uppercase tracking-tighter italic">
                                        <i class="fa-regular fa-clock mr-1"></i> {{ $device['last_active'] }}
                                    </td>

                                    {{-- Action --}}
                                    <td class="px-8 py-5 text-center">
                                        @if(!$device['is_current'])
                                        <form action="{{ route('admin.devices.logout', $device['id']) }}" method="POST" 
                                              onsubmit="return confirm('Keluarkan perangkat ini secara paksa?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-9 h-9 rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all duration-300 flex items-center justify-center mx-auto shadow-sm group-hover:scale-110">
                                                <i class="fas fa-sign-out-alt"></i>
                                            </button>
                                        </form>
                                        @else
                                        <div class="flex flex-col items-center">
                                            <span class="text-[10px] font-black text-green-600 uppercase tracking-widest bg-green-100 px-3 py-1 rounded-lg">Online</span>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Footer Info --}}
                <footer class="mt-16 text-center border-t border-gray-100 pt-8 no-print">
                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.5em]">Hotel SIG Security Monitoring v2.0</p>
                </footer>
            </div>
        </main>
    </div>

</body>
</html>