<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>Assign Role - Hotel SIG</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .table-shadow { box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04); }
    </style>
</head>

<body class="bg-[#F8F9FA] text-gray-800 antialiased">
    <x-header></x-header>

    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="w-64 fixed inset-y-0 left-0 z-50 shadow-2xl bg-white border-r border-gray-100">
            <x-sidebar></x-sidebar>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 ml-64 flex flex-col min-h-screen">
            <div class="p-8 lg:p-12">
                
                {{-- Header Section --}}
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 uppercase italic tracking-tighter leading-none">
                            Assign <span class="text-[#800000] not-italic">Role</span>
                        </h2>
                        <p class="text-gray-400 text-sm mt-3 font-medium">Manajemen otoritas tingkat tinggi untuk akses sistem Hotel SIG.</p>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <div class="relative hidden sm:block">
                            <i class="fas fa-shield-halved absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
                            <input type="text" placeholder="Cari user..." 
                                   class="pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-red-100 outline-none w-64 transition-all">
                        </div>
                    </div>
                </div>

                {{-- Table Card --}}
                <div class="bg-white rounded-[2rem] border border-gray-100 overflow-hidden table-shadow">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Pengguna</th>
                                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Kontak</th>
                                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Otoritas Saat Ini</th>
                                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Konfigurasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($users as $user)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="p-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-sm group-hover:bg-[#800000] group-hover:text-white transition-all">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div class="font-bold text-gray-800 text-sm italic tracking-tight">{{ $user->name }}</div>
                                        </div>
                                    </td>
                                    <td class="p-6">
                                        <div class="text-xs font-medium text-gray-500">{{ $user->email }}</div>
                                    </td>
                                    <td class="p-6">
                                        @php
                                            $isSuper = $user->role && str_contains(strtolower($user->role->name), 'super');
                                        @endphp
                                        <span class="px-4 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest border
                                            {{ $isSuper ? 'bg-red-50 text-red-600 border-red-100' : 'bg-gray-50 text-gray-500 border-gray-100' }}">
                                            <i class="fas {{ $isSuper ? 'fa-crown' : 'fa-user-gear' }} mr-1"></i>
                                            {{ $user->role->name ?? 'Tanpa Role' }}
                                        </span>
                                    </td>
                                    <td class="p-6">
                                        <div class="flex justify-center">
                                            @if($user->role && $user->role->name === 'Superadmin')
                                                <div class="flex items-center gap-2 px-4 py-2 bg-gray-100 rounded-xl text-[10px] font-black text-gray-300 uppercase tracking-widest cursor-not-allowed">
                                                    <i class="fas fa-lock"></i> System Locked
                                                </div>
                                            @else
                                                <a href="{{ route('assign-role.edit', $user->id) }}" 
                                                   class="bg-white border border-gray-200 text-gray-700 px-5 py-2.5 rounded-xl font-bold text-[10px] uppercase tracking-widest hover:bg-[#800000] hover:text-white hover:border-[#800000] transition-all flex items-center gap-2 shadow-sm group-hover:shadow-md">
                                                    <i class="fas fa-user-shield"></i> Ubah Role
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Footer Info --}}
                <div class="mt-8 flex justify-between items-center px-4">
                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.4em]">Total User Terdaftar: {{ $users->count() }}</p>
                    <div class="flex gap-2 text-xs font-bold text-gray-400">
                        <i class="fas fa-shield-virus"></i>
                        <span>Security Protocol Active</span>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Notifikasi Sukses dengan SweetAlert2
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'BERHASIL',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2500,
                iconColor: '#800000',
                background: '#ffffff',
                customClass: {
                    popup: 'rounded-[2rem] border border-gray-100 shadow-2xl',
                    title: 'text-sm font-black tracking-widest'
                }
            });
        @endif
    </script>
</body>
</html>