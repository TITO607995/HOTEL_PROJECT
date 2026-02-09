<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Assign Role User</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
                @endif

                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="p-3 border">Nama User</th>
                            <th class="p-3 border">Email</th>
                            <th class="p-3 border">Role Saat Ini</th>
                            <th class="p-3 border text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="p-3 border">{{ $user->name }}</td>
                            <td class="p-3 border">{{ $user->email }}</td>
                            <td class="p-3 border">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-bold">
                                    {{ $user->role->name ?? 'Belum Ada Role' }}
                                </span>
                            </td>
                            <td class="p-3 border text-center">
                                @if($user->role && $user->role->name === 'Superadmin')
                                    <span class="text-gray-400 italic text-xs">Locked</span>
                                @else
                                    <a href="{{ route('assign-role.edit', $user->id) }}" class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">
                                        Assign Role
                                    </a>
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