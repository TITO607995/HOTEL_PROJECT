<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>Registrasi Karyawan - Hotel SIG</title>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #800000; border-radius: 10px; }
        .glass-effect { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 antialiased">

    <div class="flex min-h-screen">
        <aside class="w-64 fixed inset-y-0 left-0 z-50 shadow-2xl bg-white">
            <x-sidebar></x-sidebar>
        </aside>

        <main class="flex-1 ml-64 flex flex-col min-h-screen">
            
            <nav class="sticky top-0 z-40 w-full">
                <x-header></x-header>
            </nav>

            <div class="p-8 lg:p-12">
                <div class="max-w-4xl mx-auto">
                    
                    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
                        <div>
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-[#800000] text-sm font-semibold mb-3 hover:translate-x-[-5px] transition-all">
                                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
                            </a>
                            <h1 class="text-4xl font-extrabold text-[#800000] tracking-tight uppercase italic leading-tight">
                                Registrasi <span class="text-gray-900 not-italic">Karyawan</span>
                            </h1>
                            <p class="text-gray-500 mt-1 font-medium">Sistem Manajemen Sumber Daya Manusia Hotel SIG</p>
                        </div>
                        <div class="hidden md:block">
                            <span class="bg-red-50 text-[#800000] text-[10px] border border-red-100 font-black px-5 py-2.5 rounded-full uppercase tracking-[0.2em] shadow-sm">
                                Admin Access Only
                            </span>
                        </div>
                    </div>

                    @if ($errors->any())
                    <div class="mb-8 bg-red-50 border-l-4 border-red-500 p-5 rounded-r-2xl shadow-sm animate-pulse">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-red-500 mt-1 mr-4"></i>
                            <div>
                                <p class="text-sm text-red-800 font-bold">Input tidak valid:</p>
                                <ul class="mt-1 list-disc list-inside text-xs text-red-600 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/60 overflow-hidden border border-gray-100">
                        <div class="bg-gradient-to-br from-[#800000] via-[#a00000] to-red-900 p-10 text-white relative">
                            <div class="relative z-10 flex items-center gap-8">
                                <div class="w-20 h-20 bg-white/10 backdrop-blur-xl rounded-3xl flex items-center justify-center border border-white/20 shadow-2xl">
                                    <i class="fas fa-id-card-alt text-3xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-black uppercase tracking-[0.15em] text-xl">Detail Personal Karyawan</h3>
                                    <p class="text-sm text-white/60 mt-1 font-light italic">Lengkapi kredensial untuk akses operasional sistem.</p>
                                </div>
                            </div>
                            <div class="absolute -right-16 -top-16 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
                        </div>

                        <form action="{{ route('employees.store') }}" method="POST" class="p-10 lg:p-14">
                            @csrf
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8">
                                <div class="group">
                                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-3 block group-focus-within:text-[#800000] transition-all">
                                        Nama Lengkap
                                    </label>
                                    <div class="relative">
                                        <i class="fas fa-user absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-[#800000] transition-colors"></i>
                                        <input type="text" name="name" value="{{ old('name') }}" required 
                                            placeholder="Nama lengkap sesuai KTP"
                                            class="w-full pl-14 pr-6 py-4.5 rounded-2xl bg-gray-50 border-2 border-transparent focus:border-[#800000] focus:bg-white focus:shadow-xl focus:shadow-red-900/5 transition-all outline-none text-sm font-semibold">
                                    </div>
                                </div>

                                <div class="group">
                                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-3 block group-focus-within:text-[#800000] transition-all">
                                        Email Perusahaan
                                    </label>
                                    <div class="relative">
                                        <i class="fas fa-at absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-[#800000] transition-colors"></i>
                                        <input type="email" name="email" value="{{ old('email') }}" required 
                                            placeholder="nama@hotelsig.com"
                                            class="w-full pl-14 pr-6 py-4.5 rounded-2xl bg-gray-50 border-2 border-transparent focus:border-[#800000] focus:bg-white focus:shadow-xl focus:shadow-red-900/5 transition-all outline-none text-sm font-semibold">
                                    </div>
                                </div>

                                <div class="group" x-data="{ 
                                    open: false, 
                                    selectedName: 'Pilih Jabatan...', 
                                    selectedId: '' 
                                }">
                                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-3 block group-focus-within:text-[#800000]">
                                        Penempatan Role
                                    </label>
                                    <div class="relative">
                                        <button type="button" @click="open = !open" 
                                            class="w-full pl-14 pr-6 py-4.5 rounded-2xl bg-gray-50 border-2 border-transparent text-left focus:border-[#800000] focus:bg-white transition-all outline-none flex items-center justify-between shadow-sm relative z-20">
                                            <div class="flex items-center gap-3">
                                                <i class="fas fa-briefcase absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 transition-colors" :class="open ? 'text-[#800000]' : ''"></i>
                                                <span class="text-sm font-bold text-gray-700" x-text="selectedName"></span>
                                            </div>
                                            <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-300" :class="open ? 'rotate-180 text-[#800000]' : ''"></i>
                                        </button>

                                        <input type="hidden" name="role_id" :value="selectedId" required>

                                        <div x-show="open" @click.away="open = false" 
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            class="absolute z-50 w-full mt-3 bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-gray-100 overflow-hidden p-2">
                                            
                                            @foreach($roles as $role)
                                            <div @click="selectedName = '{{ $role->NAME }}'; selectedId = '{{ $role->id }}'; open = false"
                                                class="flex items-center justify-between px-5 py-4 rounded-xl cursor-pointer transition-all duration-200 group/item"
                                                :class="selectedId == '{{ $role->id }}' ? 'bg-red-50 text-[#800000]' : 'hover:bg-gray-50 text-gray-600'">
                                                
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all"
                                                        :class="selectedId == '{{ $role->id }}' ? 'bg-white shadow-md' : 'bg-gray-100 group-hover/item:bg-white'">
                                                        <i class="fas fa-user-shield text-[12px]"></i>
                                                    </div>
                                                    <span class="text-xs font-black uppercase tracking-[0.1em]">{{ $role->NAME }}</span>
                                                </div>

                                                <i class="fas fa-check-circle text-sm text-[#800000]" x-show="selectedId == '{{ $role->id }}'"></i>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="group">
                                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-3 block group-focus-within:text-[#800000] transition-all">
                                        Kata Sandi Akses
                                    </label>
                                    <div class="relative">
                                        <i class="fas fa-key absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-[#800000] transition-colors"></i>
                                        <input type="password" name="password" required 
                                            placeholder="Gunakan minimal 8 karakter"
                                            class="w-full pl-14 pr-6 py-4.5 rounded-2xl bg-gray-50 border-2 border-transparent focus:border-[#800000] focus:bg-white focus:shadow-xl focus:shadow-red-900/5 transition-all outline-none text-sm font-semibold">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-16">
                                <button type="submit" 
                                    class="w-full bg-gradient-to-r from-[#800000] to-red-700 hover:from-[#600000] hover:to-[#800000] text-white font-black py-6 rounded-2xl shadow-2xl shadow-red-900/30 transform transition-all hover:-translate-y-1.5 active:scale-[0.97] flex items-center justify-center gap-4 tracking-[0.3em] uppercase text-sm">
                                    <i class="fas fa-paper-plane text-lg"></i> Finalisasi & Daftarkan Karyawan
                                </button>
                                
                                <div class="flex items-center justify-center gap-2 mt-8 opacity-40">
                                    <span class="h-[1px] w-12 bg-gray-400"></span>
                                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">
                                        Secured by Hotel SIG Security
                                    </p>
                                    <span class="h-[1px] w-12 bg-gray-400"></span>
                                </div>
                            </div>
                        </form>
                    </div>

                    <footer class="mt-12 mb-8 border-t border-gray-200 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.4em]">© 2026 Hotel SIG Internasional</p>
                        <div class="flex gap-6 text-gray-300">
                            <i class="fas fa-shield-alt hover:text-[#800000] transition-colors cursor-help"></i>
                            <i class="fas fa-fingerprint hover:text-[#800000] transition-colors cursor-help"></i>
                            <i class="fas fa-network-wired hover:text-[#800000] transition-colors cursor-help"></i>
                        </div>
                    </footer>
                </div>
            </div>
        </main>
    </div>

</body>
</html>