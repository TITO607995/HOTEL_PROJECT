<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Edit Karyawan - Hotel SIG</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8F9FA] text-gray-800 antialiased">

    <div class="flex min-h-screen">
        <aside class="w-64 fixed inset-y-0 left-0 z-50 shadow-2xl bg-white border-r border-gray-100">
            <x-sidebar></x-sidebar>
        </aside>

        <main class="flex-1 ml-64 flex flex-col min-h-screen">
            
            <header class="sticky top-0 z-40 w-full bg-white/80 backdrop-blur-md border-b border-gray-100 px-8 py-4 flex justify-end items-center">
                <x-header></x-header>
            </header>

            <div class="p-8 lg:p-12 max-w-4xl mx-auto w-full">
                
                {{-- Breadcrumb & Title --}}
                <div class="mb-10">
                    <a href="{{ route('employees.index') }}" class="group text-[#800000] font-bold text-xs uppercase tracking-[0.2em] flex items-center gap-2 mb-4">
                        <i class="fas fa-chevron-left transition-transform group-hover:-translate-x-1"></i> 
                        Batal & Kembali
                    </a>
                    <h1 class="text-4xl font-black text-gray-900 leading-none tracking-tighter uppercase italic">
                        Update <span class="text-[#800000] not-italic">Profile Staff</span>
                    </h1>
                    <p class="text-gray-400 text-sm mt-3 font-medium italic">Mengubah kredensial: <strong>{{ $employee->name }}</strong></p>
                </div>

                {{-- Form Card --}}
                <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 overflow-hidden border border-gray-100">
                    <div class="h-3 bg-[#800000]"></div>

                    <form action="{{ route('employees.update', $employee->id) }}" method="POST" class="p-10 lg:p-14">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            
                            {{-- Nama --}}
                            <div class="col-span-2 space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                                <div class="relative group">
                                    <i class="fas fa-user-edit absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-[#800000] transition-colors"></i>
                                    <input type="text" name="name" value="{{ old('name', $employee->name) }}" required 
                                           class="w-full pl-14 pr-5 py-4 rounded-2xl bg-gray-50 border-2 border-transparent focus:bg-white focus:border-[#800000] focus:ring-4 focus:ring-red-50 font-bold text-gray-700 transition-all outline-none">
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="col-span-2 md:col-span-1 space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Email Karyawan</label>
                                <div class="relative group">
                                    <i class="fas fa-envelope absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-[#800000] transition-colors"></i>
                                    <input type="email" name="email" value="{{ old('email', $employee->email) }}" required 
                                           class="w-full pl-14 pr-5 py-4 rounded-2xl bg-gray-50 border-2 border-transparent focus:bg-white focus:border-[#800000] focus:ring-4 focus:ring-red-50 font-bold text-gray-700 transition-all outline-none">
                                </div>
                            </div>

                            {{-- Role --}}
                            <div class="col-span-2 md:col-span-1 space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jabatan / Level</label>
                                <div class="relative group">
                                    <i class="fas fa-tags absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-[#800000] transition-colors"></i>
                                    <select name="role_id" required 
                                            class="w-full pl-14 pr-12 py-4 rounded-2xl bg-gray-50 border-2 border-transparent focus:bg-white focus:border-[#800000] focus:ring-4 focus:ring-red-50 font-black text-gray-700 appearance-none cursor-pointer transition-all outline-none">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ $employee->role_id == $role->id ? 'selected' : '' }}>
                                                {{ strtoupper($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                                </div>
                            </div>

                            {{-- Password Section --}}
                            <div class="col-span-2 mt-4 p-6 bg-red-50/50 rounded-3xl border border-red-100" x-data="{ showPass: false }">
                                <label class="flex items-center gap-2 text-[10px] font-black text-[#800000] uppercase tracking-widest mb-4">
                                    <i class="fas fa-shield-alt"></i> Keamanan Akun (Opsional)
                                </label>
                                <div class="relative group">
                                    <i class="fas fa-lock absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-[#800000] transition-colors"></i>
                                    <input :type="showPass ? 'text' : 'password'" name="password" 
                                           placeholder="Isi hanya jika ingin mengganti password" 
                                           class="w-full pl-14 pr-14 py-4 rounded-2xl bg-white border-2 border-transparent focus:border-[#800000] focus:ring-4 focus:ring-red-100 font-bold text-gray-700 transition-all outline-none text-sm">
                                    <button type="button" @click="showPass = !showPass" class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#800000]">
                                        <i class="fas" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                <p class="text-[9px] text-gray-400 mt-3 italic font-medium">
                                    *Kosongkan kolom password jika tidak ada perubahan pada akses login.
                                </p>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-12 flex flex-col md:flex-row items-center justify-between gap-6">
                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                Terakhir Update: <span class="text-gray-600">{{ $employee->updated_at->diffForHumans() }}</span>
                            </div>
                            <button type="submit" 
                                    class="w-full md:w-auto bg-[#800000] hover:bg-red-900 text-white font-black py-4 px-10 rounded-2xl shadow-xl shadow-red-900/20 transition-all active:scale-95 flex items-center justify-center gap-3 uppercase tracking-widest text-xs">
                                <i class="fas fa-save text-sm"></i> Simpan Pembaruan
                            </button>
                        </div>

                    </form>
                </div>

                <footer class="mt-12 text-center">
                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.5em]">Hotel SIG Security Protocol v2.0</p>
                </footer>
            </div>
        </main>
    </div>

</body>
</html>