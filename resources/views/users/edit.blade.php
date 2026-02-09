<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Biodata: {{ $user->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700">Nama</label>
                        <input type="text" name="name" value="{{ $user->name }}" class="w-full border-gray-300 rounded-md">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700">Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" class="w-full border-gray-300 rounded-md">
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded font-bold text-xs uppercase shadow-md">
                        Update Biodata
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>