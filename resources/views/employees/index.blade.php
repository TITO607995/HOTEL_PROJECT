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
    <title>Manajemen Karyawan - Hotel SIG</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .table-shadow { box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04); }
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
                        <h2 class="text-3xl font-black text-gray-900 uppercase italic tracking-tighter leading-none">
                            Manajemen <span class="text-[#800000] not-italic">Karyawan</span>
                        </h2>
                        <p class="text-gray-400 text-sm mt-3 font-medium">Otoritas akses dan database staf operasional Hotel SIG.</p>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        {{-- Search Bar --}}
                        <div class="relative hidden sm:block">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
                            <input type="text" placeholder="Cari nama atau email..." 
                                   class="pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-red-100 outline-none w-64 transition-all">
                        </div>

                        <a href="{{ route('employees.create') }}" 
                           class="bg-[#800000] text-white px-6 py-3.5 rounded-xl font-bold text-xs shadow-xl shadow-red-900/20 hover:bg-red-900 hover:-translate-y-0.5 transition-all flex items-center gap-3 uppercase tracking-widest">
                            <i class="fas fa-user-plus text-sm"></i> Tambah Karyawan
                        </a>
                    </div>
                </div>

                {{-- Alert Success --}}
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
                <div class="bg-white rounded-[2rem] border border-gray-100 overflow-hidden table-shadow">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Data Pegawai</th>
                                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Jabatan</th>
                                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Tanggal Join</th>
                                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($employees as $employee)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="p-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#800000] to-red-700 text-white flex items-center justify-center font-black text-lg shadow-lg shadow-red-900/10 transition-transform group-hover:scale-110">
                                                {{ strtoupper(substr($employee->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-800 text-sm group-hover:text-[#800000] transition-colors">{{ $employee->name }}</div>
                                                <div class="text-[11px] text-gray-400 font-medium">{{ $employee->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-6">
                                        @php
                                            $isAdmin = $employee->role && str_contains(strtolower($employee->role->name), 'admin');
                                        @endphp
                                        <span class="px-4 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-wider border
                                            {{ $isAdmin ? 'bg-red-50 text-red-600 border-red-100' : 'bg-blue-50 text-blue-600 border-blue-100' }}">
                                            <i class="fas {{ $isAdmin ? 'fa-user-shield' : 'fa-user-tie' }} mr-1"></i>
                                            {{ $employee->role->name ?? 'Staff' }}
                                        </span>
                                    </td>
                                    <td class="p-6 text-center text-xs font-bold text-gray-500 italic">
                                        {{ $employee->created_at->format('d M, Y') }}
                                    </td>
                                    <td class="p-6">
                                        <div class="flex justify-center gap-3">
                                            {{-- Edit --}}
                                            <a href="{{ route('employees.edit', $employee->id) }}" 
                                               class="w-9 h-9 flex items-center justify-center bg-gray-50 text-gray-400 rounded-xl hover:bg-[#800000] hover:text-white transition-all shadow-sm"
                                               title="Edit Profil">
                                                <i class="fas fa-pen-nib text-xs"></i>
                                            </a>

                                            {{-- Delete --}}
                                            @if(auth()->id() !== $employee->id)
                                            <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus akses karyawan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="w-9 h-9 flex items-center justify-center bg-gray-50 text-gray-400 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm"
                                                        title="Hapus Akses">
                                                    <i class="fas fa-trash-can text-xs"></i>
                                                </button>
                                            </form>
                                            @else
                                            <div class="w-9 h-9 flex items-center justify-center bg-gray-100 text-gray-300 rounded-xl cursor-not-allowed" title="Anda tidak bisa menghapus diri sendiri">
                                                <i class="fas fa-lock text-xs"></i>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="p-20 text-center">
                                        <div class="flex flex-col items-center opacity-20">
                                            <i class="fas fa-users-slash text-6xl mb-4"></i>
                                            <p class="text-sm font-black uppercase tracking-[0.3em]">Data Kosong</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Footer Info --}}
                <div class="mt-8 flex justify-between items-center px-4">
                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.4em]">Total Karyawan: {{ $employees->count() }}</p>
                    <div class="flex gap-2 text-xs font-bold text-gray-400">
                        <span>Database v1.0</span>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
</html>