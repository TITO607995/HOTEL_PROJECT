<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Manajemen Role - Hotel SIG</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); }
    </style>
</head>
    <x-header></x-header>
<body class="bg-[#F8F9FA] text-gray-800 antialiased">

    <div class="flex min-h-screen">
        <aside class="w-64 fixed inset-y-0 left-0 z-50 shadow-2xl bg-white border-r border-gray-100">
            <x-sidebar></x-sidebar>
        </aside>

        <main class="flex-1 ml-64 flex flex-col min-h-screen">
            

            <div class="p-8 lg:p-12">
                
                {{-- Header Section --}}
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="bg-[#800000] p-2 rounded-lg text-white text-xs">
                                <i class="fas fa-shield-halved"></i>
                            </span>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Security Settings</span>
                        </div>
                        <h2 class="text-3xl font-black text-gray-900 uppercase italic tracking-tighter leading-none">
                            Role & <span class="text-[#800000] not-italic">Hak Akses</span>
                        </h2>
                        <p class="text-gray-400 text-sm mt-3 font-medium">Konfigurasi izin menu untuk setiap level jabatan karyawan.</p>
                    </div>
                    
                    <a href="{{ route('roles.create') }}" 
                       class="bg-[#800000] text-white px-6 py-3.5 rounded-xl font-bold text-xs shadow-xl shadow-red-900/20 hover:bg-red-900 hover:-translate-y-0.5 transition-all flex items-center gap-3 uppercase tracking-widest">
                        <i class="fas fa-plus-circle text-sm"></i> Tambah Role Baru
                    </a>
                </div>

                {{-- Alert Sukses --}}
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition 
                     class="mb-8 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                        <span class="text-sm font-bold text-green-800">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-400 hover:text-green-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                @endif

                {{-- Table Card --}}
                <div class="bg-white rounded-[2rem] border border-gray-100 overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] w-1/4">Nama Jabatan (Role)</th>
                                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] w-1/2">Izin Akses Menu</th>
                                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($roles as $role)
                                <tr class="hover:bg-gray-50/30 transition-colors group">
                                    <td class="p-6">
                                        <div class="flex flex-col">
                                            <span class="font-black text-gray-800 text-sm uppercase tracking-tight group-hover:text-[#800000] transition-colors">
                                                {{ $role->name }}
                                            </span>
                                            <span class="text-[10px] text-gray-400 font-medium">ID Privilege: #00{{ $role->id }}</span>
                                        </div>
                                    </td>
                                    <td class="p-6">
                                        <div class="flex flex-wrap gap-1.5">
                                            @forelse($role->menus as $menu)
                                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-bold bg-gray-50 text-gray-600 border border-gray-200 group-hover:border-[#800000]/20 group-hover:text-[#800000] transition-all">
                                                    <i class="fas fa-check-circle mr-1.5 text-[8px] opacity-50"></i>
                                                    {{ strtoupper($menu->name) }}
                                                </span>
                                            @empty
                                                <span class="text-[10px] text-gray-400 font-medium italic bg-gray-50 px-3 py-1 rounded-lg">
                                                    <i class="fas fa-ban mr-1"></i> Belum ada izin menu
                                                </span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="p-6">
                                        <div class="flex justify-center gap-3">
                                            @if($role->name !== 'Superadmin')
                                                <a href="{{ route('roles.edit', $role->id) }}" 
                                                   class="bg-blue-50 text-blue-600 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                                    <i class="fas fa-user-gear mr-1.5"></i> Edit Akses
                                                </a>

                                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Hapus role ini? User dengan role ini mungkin kehilangan akses.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="bg-gray-50 text-gray-400 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <div class="flex items-center gap-2 bg-red-50 border border-red-100 px-4 py-2 rounded-xl">
                                                    <i class="fas fa-lock text-red-600 text-xs"></i>
                                                    <span class="text-[10px] font-black text-red-700 uppercase tracking-tighter">Full Access Locked</span>
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

                {{-- Footer Info --}}
                <footer class="mt-12 text-center">
                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.5em]">RBAC System • Hotel SIG Security Protocol</p>
                </footer>
            </div>
        </main>
    </div>

</body>
</html>