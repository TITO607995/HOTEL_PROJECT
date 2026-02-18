<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Registrasi Karyawan - Hotel SIG</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#F8F9FA] text-gray-800 antialiased">
  <x-header></x-header>
    <div class="flex min-h-screen">
        <aside class="w-64 fixed inset-y-0 left-0 z-50 shadow-2xl bg-white border-r border-gray-100">
            <x-sidebar></x-sidebar>
        </aside>

        <main class="flex-1 ml-64 flex flex-col min-h-screen">
        

            <div class="p-8 lg:p-12 max-w-5xl mx-auto w-full">
                
                {{-- Header Navigasi --}}
                <div class="mb-10">
                    <a href="{{ route('employees.index') }}" class="group text-[#800000] font-bold text-xs uppercase tracking-[0.2em] flex items-center gap-2 mb-4">
                        <i class="fas fa-chevron-left transition-transform group-hover:-translate-x-1"></i> 
                        Batal & Kembali
                    </a>
                    <h1 class="text-4xl font-black text-gray-900 leading-none tracking-tighter uppercase italic">
                        Registrasi <span class="text-[#800000] not-italic">Karyawan</span>
                    </h1>
                    <p class="text-gray-400 text-sm mt-3 font-medium">Input kredensial keamanan dan otoritas akses staff baru.</p>
                </div>

                {{-- Alert Error --}}
                @if ($errors->any())
                <div x-data="{ show: true }" x-show="show" x-transition class="mb-8 bg-red-50 border-l-4 border-red-500 p-6 rounded-r-2xl shadow-sm relative">
                    <div class="flex items-start">
                        <i class="fas fa-shield-virus text-red-500 mt-1 text-xl"></i>
                        <div class="ml-4">
                            <h3 class="text-xs font-black text-red-800 uppercase tracking-widest">Gagal Memproses Data</h3>
                            <ul class="mt-2 text-[11px] text-red-700 space-y-1 font-bold italic">
                                @foreach ($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button @click="show = false" class="absolute top-4 right-4 text-red-300 hover:text-red-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                @endif

                {{-- Form Card --}}
                <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 overflow-hidden border border-gray-100">
                    <div class="h-3 bg-gradient-to-r from-[#800000] via-red-700 to-[#800000]"></div>

                    <form action="{{ route('employees.store') }}" method="POST" class="p-10 lg:p-14">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-10">
                            
                            {{-- Nama Lengkap --}}
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Nama Lengkap Staff</label>
                                <div class="group relative">
                                    <i class="fas fa-signature absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-[#800000] transition-colors"></i>
                                    <input type="text" name="name" value="{{ old('name') }}" required 
                                           class="w-full pl-14 pr-5 py-4 rounded-2xl bg-gray-50 border-2 border-transparent focus:bg-white focus:border-[#800000] focus:ring-4 focus:ring-red-50 font-bold text-gray-700 transition-all outline-none"
                                           placeholder="Ahmad Subardjo">
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Email Corporate</label>
                                <div class="group relative">
                                    <i class="fas fa-at absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-[#800000] transition-colors"></i>
                                    <input type="email" name="email" value="{{ old('email') }}" required 
                                           class="w-full pl-14 pr-5 py-4 rounded-2xl bg-gray-50 border-2 border-transparent focus:bg-white focus:border-[#800000] focus:ring-4 focus:ring-red-50 font-bold text-gray-700 transition-all outline-none"
                                           placeholder="staff@hotelsig.com">
                                </div>
                            </div>

                            {{-- Password --}}
                            <div class="space-y-3" x-data="{ show: false }">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Password Sementara</label>
                                <div class="group relative">
                                    <i class="fas fa-lock-open absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-[#800000] transition-colors"></i>
                                    <input :type="show ? 'text' : 'password'" name="password" required 
                                           class="w-full pl-14 pr-14 py-4 rounded-2xl bg-gray-50 border-2 border-transparent focus:bg-white focus:border-[#800000] focus:ring-4 focus:ring-red-50 font-black text-gray-700 tracking-widest transition-all outline-none"
                                           placeholder="••••••••">
                                    <button type="button" @click="show = !show" class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#800000] transition-colors">
                                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Role Selection --}}
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Assign Jabatan</label>
                                <div class="group relative">
                                    <i class="fas fa-user-shield absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-[#800000] transition-colors"></i>
                                    <select name="role_id" required 
                                            class="w-full pl-14 pr-12 py-4 rounded-2xl bg-gray-50 border-2 border-transparent focus:bg-white focus:border-[#800000] focus:ring-4 focus:ring-red-50 font-black text-gray-700 appearance-none cursor-pointer transition-all outline-none">
                                        <option value="" disabled selected italic>Pilih Role Staff...</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ strtoupper($role->name) }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] pointer-events-none"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Submit --}}
                        <div class="mt-16 pt-10 border-t border-gray-50 flex flex-col items-center">
                            <button type="submit" 
                                    class="group w-full md:w-auto md:min-w-[350px] bg-[#800000] hover:bg-red-900 text-white font-black py-5 px-10 rounded-2xl shadow-2xl shadow-red-900/30 transition-all active:scale-95 flex items-center justify-center gap-4 uppercase tracking-[0.2em] text-xs">
                                <i class="fas fa-plus-circle text-lg group-hover:rotate-90 transition-transform duration-500"></i>
                                Daftarkan Karyawan Baru
                            </button>
                            <div class="mt-6 flex items-center gap-2 text-gray-300">
                                <i class="fas fa-info-circle text-[10px]"></i>
                                <span class="text-[9px] font-black uppercase tracking-widest">Otomatis sinkronisasi dengan database akses</span>
                            </div>
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