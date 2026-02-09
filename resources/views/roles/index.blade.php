<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Role & Akses') }}
            </h2>
            <a href="{{ route('roles.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm shadow-md hover:bg-blue-700 transition">
            + Tambah Role
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                {{-- Alert Sukses --}}
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-800 text-white">
                            <th class="p-3 uppercase font-bold text-sm">Nama Role</th>
                            <th class="p-3 uppercase font-bold text-sm">Akses Menu</th>
                            <th class="p-3 uppercase font-bold text-sm text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach($roles as $role)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                            <td class="p-3 font-medium text-gray-700">{{ $role->name }}</td>
                            <td class="p-3">
                                @forelse($role->menus as $menu)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1 mb-1 border border-blue-200">
                                        {{ $menu->name }}
                                    </span>
                                @empty
                                    <span class="text-gray-400 text-xs italic">Belum ada akses menu</span>
                                @endforelse
                            </td>
                            <td class="p-3">
                                <div class="flex justify-center gap-2">
                                    @if($role->name !== 'Superadmin')
                                        {{-- Tombol Edit Warna Kuning/Amber --}}
                                        <a href="{{ route('roles.edit', $role->id) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-bold text-[10px] text-white uppercase tracking-widest hover:bg-blue-700 transition ease-in-out duration-150 shadow-sm">
                                            Edit Akses
                                        </a>

                                        {{-- Tombol Hapus Warna Merah --}}
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Yakin mau hapus role ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded-md font-bold text-[10px] text-white uppercase tracking-widest hover:bg-red-700 transition ease-in-out duration-150 shadow-sm">
                                                Hapus
                                            </button>
                                        </form>
                                    @else
                                        {{-- Penanda Role Superadmin Locked --}}
                                        <span class="px-3 py-1 bg-red-100 text-red-700 border border-red-200 rounded-md text-[10px] font-black uppercase tracking-tighter">
                                            Full Access (Locked)
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>