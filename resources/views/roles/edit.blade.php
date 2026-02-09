<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Akses Role: ') . $role->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-2">Nama Role</label>
                        <input type="text" name="name" value="{{ $role->name }}" 
                               class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                    </div>

                    <h3 class="font-bold text-gray-700 mb-4">Checklist Menu untuk Role Ini:</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
                        @foreach($all_menus as $menu)
                            <div class="flex items-center p-2 border border-gray-100 rounded hover:bg-gray-50">
                                <input type="checkbox" name="menu_ids[]" value="{{ $menu->id }}" id="menu_{{ $menu->id }}"
                                    {{ $role->menus->contains($menu->id) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <label for="menu_{{ $menu->id }}" class="ml-2 text-sm text-gray-600 cursor-pointer">
                                    {{ $menu->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-4 pt-6 border-t border-gray-100">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                            Simpan Perubahan
                        </button>
                        
                        <a href="{{ route('roles.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                            Batal
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>