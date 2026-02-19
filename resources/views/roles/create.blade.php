<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>Tambah Role - Hotel SIG</title>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-[#F8F9FA] text-gray-800 antialiased">
    <div class="flex min-h-screen">
        {{-- Sidebar (Opsional, sesuaikan dengan layout utama lo) --}}
        <aside class="w-64 fixed inset-y-0 left-0 z-50 shadow-2xl bg-white border-r border-gray-100 no-print">
            <x-sidebar></x-sidebar>
        </aside>

        <main class="flex-1 ml-64 p-6 lg:p-10">
            <div class="max-w-4xl mx-auto">
                
                {{-- Breadcrumb & Title --}}
                <div class="mb-10">
                    <div class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                        <a href="{{ route('roles.index') }}" class="hover:text-[#800000] transition-colors">Roles</a>
                        <i class="fas fa-chevron-right text-[8px]"></i>
                        <span class="text-[#800000]">Tambah Role Baru</span>
                    </div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight uppercase italic leading-none">
                        Create <span class="text-[#800000] not-italic">New Role</span>
                    </h1>
                </div>

                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    
                    {{-- Card Input Nama Role --}}
                    <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-8 mb-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-10 h-10 rounded-2xl bg-red-50 flex items-center justify-center text-[#800000]">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div>
                                <h3 class="font-black text-gray-800 uppercase tracking-widest text-sm">Informasi Role</h3>
                                <p class="text-gray-400 text-[10px] font-medium">Tentukan nama jabatan atau level akses user.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Nama Role</label>
                            <input type="text" name="name" 
                                class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:border-[#800000] focus:ring-4 focus:ring-red-50 outline-none transition-all placeholder:text-gray-300"
                                placeholder="Misal: Manager, Receptionist, atau Admin" required>
                        </div>
                    </div>

                    {{-- Card Pilih Akses Menu --}}
                    <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-8">
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600">
                                    <i class="fas fa-list-check"></i>
                                </div>
                                <div>
                                    <h3 class="font-black text-gray-800 uppercase tracking-widest text-sm">Hak Akses Menu</h3>
                                    <p class="text-gray-400 text-[10px] font-medium">Pilih menu mana saja yang bisa diakses role ini.</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($all_menus as $menu)
                            <label class="group relative flex items-center p-4 bg-gray-50 rounded-2xl cursor-pointer border-2 border-transparent hover:border-red-100 hover:bg-red-50/30 transition-all">
                                <input type="checkbox" name="menu_ids[]" value="{{ $menu->id }}" 
                                    class="w-5 h-5 rounded-lg border-gray-300 text-[#800000] focus:ring-[#800000] transition-all cursor-pointer">
                                <div class="ml-4">
                                    <span class="block text-xs font-bold text-gray-700 uppercase tracking-wide group-hover:text-[#800000] transition-colors">
                                        {{ $menu->name }}
                                    </span>
                                    <span class="block text-[9px] text-gray-400 font-medium italic">Izin Akses Menu</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="mt-10 flex items-center justify-end gap-4">
                        <a href="{{ route('roles.index') }}" class="text-xs font-bold text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="group flex items-center gap-3 bg-[#800000] text-white px-10 py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-red-900 transition-all shadow-xl shadow-red-900/20 active:scale-95">
                            <i class="fas fa-save group-hover:bounce"></i> Simpan Role
                        </button>
                    </div>
                </form>

                <footer class="mt-16 text-center border-t border-gray-100 pt-8">
                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.5em]">Hotel SIG Authority System v2.0</p>
                </footer>
            </div>
        </main>
    </div>
</body>
</html>