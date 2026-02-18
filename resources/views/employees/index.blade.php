<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h2 class="text-3xl font-black text-gray-800 uppercase italic tracking-tighter">
                        Manajemen <span class="text-[#800000]">Karyawan</span>
                    </h2>
                    <p class="text-gray-500 text-sm mt-1">Kelola akses dan data pegawai hotel.</p>
                </div>
                <a href="{{ route('employees.create') }}" 
                   class="bg-[#800000] text-white px-6 py-3 rounded-xl font-bold text-sm shadow-lg shadow-red-900/20 hover:bg-red-900 transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> Tambah Karyawan
                </a>
            </div>

            {{-- Alert --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-xl border border-green-200 font-bold text-sm flex items-center gap-2">
                    <i class="fas fa-check-circle text-lg"></i> {{ session('success') }}
                </div>
            @endif

            {{-- Table Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="p-5 text-xs font-black text-gray-400 uppercase tracking-widest">Nama Pegawai</th>
                            <th class="p-5 text-xs font-black text-gray-400 uppercase tracking-widest">Role / Jabatan</th>
                            <th class="p-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Bergabung</th>
                            <th class="p-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($employees as $employee)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="p-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-[#800000]/10 text-[#800000] flex items-center justify-center font-bold text-lg">
                                        {{ substr($employee->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $employee->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $employee->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-5">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wide border 
                                    {{ $employee->role && $employee->role->name == 'Superadmin' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-blue-100 text-blue-700 border-blue-200' }}">
                                    {{ $employee->role->name ?? 'No Role' }}
                                </span>
                            </td>
                            <td class="p-5 text-center text-xs font-medium text-gray-500">
                                {{ $employee->created_at->format('d M Y') }}
                            </td>
                            <td class="p-5">
                                <div class="flex justify-center gap-2">
                                    {{-- Edit Button --}}
                                    <a href="{{ route('employees.edit', $employee->id) }}" 
                                       class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-600 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-sm"
                                       title="Edit Data">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>

                                    {{-- Delete Button (Kecuali user sendiri) --}}
                                    @if(auth()->id() !== $employee->id)
                                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Hapus karyawan ini secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-600 rounded-lg hover:bg-red-600 hover:text-white transition shadow-sm"
                                                title="Hapus Pegawai">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($employees->isEmpty())
                    <div class="p-10 text-center text-gray-400">
                        <i class="fas fa-users text-4xl mb-3 opacity-30"></i>
                        <p class="text-sm font-medium">Belum ada data karyawan.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>