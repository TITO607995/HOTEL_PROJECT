<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Assign Role untuk: {{ $user->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-md border">
                <form action="{{ route('assign-role.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Role Baru:</label>
                        <select name="role_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500" required>
                            <option value="">-- Pilih Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t">
                        <a href="{{ route('assign-role.index') }}" class="text-sm text-gray-500 hover:underline">Batal</a>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md font-bold text-xs uppercase hover:bg-blue-700 shadow-md transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>