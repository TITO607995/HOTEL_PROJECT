<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar User & Staff</h2>
            <a href="{{ route('users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm shadow-md hover:bg-blue-700 transition">
                + Tambah User
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <table class="w-full border-collapse border border-gray-200">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="border p-3 text-left">Nama</th>
                            <th class="border p-3 text-left">Email</th>
                            <th class="border p-3 text-left">Role</th>
                            <th class="border p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="border p-3">{{ $user->name }}</td>
                            <td class="border p-3">{{ $user->email }}</td>
                            <td class="border p-3">
                                {{-- Gunakan $user->role?->name agar tidak error jika null --}}
                                <span class="px-2 py-1 {{ $user->role ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-500' }} rounded text-xs font-bold shadow-sm">
                                    {{ $user->role?->name ?? 'Belum Ada Role' }}
                                </span>
                                @if($user->id === auth()->id())
                                    <span class="bg-blue-100 text-blue-600 text-[10px] px-2 py-0.5 rounded-full font-black uppercase tracking-tighter shadow-sm border border-blue-200">
                                        Akun Anda
                                    </span>
                                @endif
                            </td>
                            <td class="border p-3 text-center">
                                {{-- PROTEKSI: Cek role dengan aman menggunakan ?-> --}}
                                @if($user->role?->name !== 'Superadmin')
                                    <div class="flex justify-center gap-3">
                                        <a href="{{ route('users.edit', $user->id) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                            Edit
                                        </a>

                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin mau hapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    {{-- Jika Superadmin atau Role Null, bagian ini aman --}}
                                    <span class="text-gray-400 text-xs italic">System Locked</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>