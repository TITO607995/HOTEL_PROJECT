<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Header Navigasi --}}
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <a href="{{ route('employees.index') }}" class="text-[#800000] font-bold text-sm hover:underline">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Karyawan
                    </a>
                    <h1 class="text-3xl font-black text-gray-800 mt-2 uppercase italic tracking-tighter">
                        Registrasi <span class="text-[#800000]">Karyawan Baru</span>
                    </h1>
                </div>
            </div>

            {{-- Alert Error --}}
            @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada input Anda:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            {{-- Form Card --}}
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 relative">
                
                {{-- Decorative Header --}}
                <div class="h-2 bg-gradient-to-r from-[#800000] to-red-600"></div>

                <form action="{{ route('employees.store') }}" method="POST" class="p-8 md:p-12">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        {{-- Nama Lengkap --}}
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nama Lengkap</label>
                            <div class="relative">
                                <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                                <input type="text" name="name" value="{{ old('name') }}" required 
                                       class="w-full pl-12 pr-4 py-3 rounded-xl bg-gray-50 border-gray-200 focus:border-[#800000] focus:ring-red-100 font-semibold text-gray-700 transition-all"
                                       placeholder="Nama sesuai KTP">
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Email Login</label>
                            <div class="relative">
                                <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                                <input type="email" name="email" value="{{ old('email') }}" required 
                                       class="w-full pl-12 pr-4 py-3 rounded-xl bg-gray-50 border-gray-200 focus:border-[#800000] focus:ring-red-100 font-semibold text-gray-700 transition-all"
                                       placeholder="email@hotelsig.com">
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Password Akses</label>
                            <div class="relative">
                                <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                                <input type="password" name="password" required 
                                       class="w-full pl-12 pr-4 py-3 rounded-xl bg-gray-50 border-gray-200 focus:border-[#800000] focus:ring-red-100 font-semibold text-gray-700 transition-all"
                                       placeholder="Minimal 6 karakter">
                            </div>
                        </div>

                        {{-- Role Selection --}}
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Jabatan / Role</label>
                            <div class="relative">
                                <i class="fas fa-briefcase absolute left-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                                <select name="role_id" required 
                                        class="w-full pl-12 pr-10 py-3 rounded-xl bg-gray-50 border-gray-200 focus:border-[#800000] focus:ring-red-100 font-bold text-gray-700 appearance-none cursor-pointer hover:bg-white transition-all">
                                    <option value="" disabled selected>Pilih Jabatan...</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Submit --}}
                    <div class="pt-6 border-t border-gray-100">
                        <button type="submit" 
                                class="w-full bg-[#800000] hover:bg-red-900 text-white font-black py-4 rounded-xl shadow-lg shadow-red-900/20 transition-transform active:scale-[0.98] flex items-center justify-center gap-3 uppercase tracking-widest text-sm">
                            <i class="fas fa-save text-lg"></i> Simpan Data Karyawan
                        </button>
                    </div>

                </form>
            </div>
            
            <p class="text-center text-xs text-gray-400 mt-8 font-medium uppercase tracking-widest">
                Secured by Hotel SIG System
            </p>
        </div>
    </div>
</x-app-layout>