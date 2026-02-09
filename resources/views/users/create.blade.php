<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Registrasi Staff Baru</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Email Utama</label>
                        <input type="email" name="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500" required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700">Password Sistem</label>
                        <input type="password" name="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500" required>
                    </div>

                    <div class="flex items-center justify-between border-t pt-4">
                        <a href="{{ route('users.index') }}" class="text-sm text-gray-500">Batal</a>
                        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-md font-bold text-xs uppercase shadow-md hover:bg-blue-700 transition">
                            Simpan Biodata
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>