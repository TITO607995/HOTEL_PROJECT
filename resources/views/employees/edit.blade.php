<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6">
                <a href="{{ route('employees.index') }}" class="text-gray-500 text-xs font-bold uppercase tracking-widest hover:text-[#800000] transition">
                    <i class="fas fa-arrow-left mr-1"></i> Batal & Kembali
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gray-50 px-8 py-6 border-b border-gray-100">
                    <h2 class="text-xl font-black text-gray-800 uppercase italic">Edit Data: <span class="text-[#800000]">{{ $employee->name }}</span></h2>
                </div>

                <form action="{{ route('employees.update', $employee->id) }}" method="POST" class="p-8">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        {{-- Nama --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $employee->name) }}" required 
                                   class="w-full rounded-lg border-gray-300 focus:border-[#800000] focus:ring focus:ring-red-200 transition">
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2">Email Login</label>
                            <input type="email" name="email" value="{{ old('email', $employee->email) }}" required 
                                   class="w-full rounded-lg border-gray-300 focus:border-[#800000] focus:ring focus:ring-red-200 transition">
                        </div>

                        {{-- Role (Fungsi Assign Role ada di sini) --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2">Jabatan / Role</label>
                            <select name="role_id" class="w-full rounded-lg border-gray-300 focus:border-[#800000] focus:ring focus:ring-red-200 transition font-bold text-gray-700">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ $employee->role_id == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Ganti Password (Opsional) --}}
                        <div class="pt-6 border-t border-gray-100">
                            <label class="block text-xs font-black text-red-600 mb-2 uppercase tracking-wide">
                                <i class="fas fa-key mr-1"></i> Ubah Password (Opsional)
                            </label>
                            <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password" 
                                   class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:border-[#800000] focus:ring focus:ring-red-200 transition text-sm">
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-4">
                        <button type="submit" class="bg-[#800000] text-white px-8 py-3 rounded-xl font-bold uppercase text-xs tracking-widest hover:bg-red-900 transition shadow-lg">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>