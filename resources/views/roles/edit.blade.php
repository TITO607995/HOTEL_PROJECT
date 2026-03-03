<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>Edit Akses Role - Hotel SIG</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* Custom Checkbox Maroon */
        .custom-checkbox:checked {
            background-color: #800000;
            border-color: #800000;
        }
    </style>
</head>

<body class="bg-[#F8F9FA] text-gray-800 antialiased">
    <x-header></x-header>

    <div class="flex min-h-screen">
        <aside class="w-64 fixed inset-y-0 left-0 z-50 shadow-2xl bg-white border-r border-gray-100">
            <x-sidebar></x-sidebar>
        </aside>

        <main class="flex-1 ml-64 flex flex-col min-h-screen">
            <div class="p-8 lg:p-12">
                
                {{-- Header Section --}}
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 uppercase italic tracking-tighter leading-none">
                            Edit Akses <span class="text-[#800000] not-italic">Role</span>
                        </h2>
                        <p class="text-gray-400 text-sm mt-3 font-medium">Mengatur hak akses menu untuk tingkat otoritas: <span class="text-gray-700 font-bold">{{ $role->name }}</span></p>
                    </div>
                    <a href="{{ route('roles.index') }}" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-[#800000] transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke List
                    </a>
                </div>

                {{-- Main Form Card --}}
                <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-2xl shadow-gray-200/50 overflow-hidden">
                    <form id="roleUpdateForm" action="{{ route('roles.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="p-8 lg:p-10">
                            {{-- Role Name Input --}}
                            <div class="mb-10">
                                <label class="block text-[10px] font-black text-[#800000] uppercase tracking-[0.2em] mb-3">Nama Identitas Role</label>
                                <input type="text" name="name" value="{{ $role->name }}" required
                                       class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl px-6 py-4 text-sm font-bold text-gray-700 outline-none focus:border-[#800000] focus:bg-white transition-all shadow-inner"
                                       placeholder="Contoh: Manager Operasional">
                            </div>

                            {{-- Permissions Grid --}}
                            <div class="mb-6">
                                <label class="block text-[10px] font-black text-[#800000] uppercase tracking-[0.2em] mb-5">Otoritas Checklist Menu</label>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($all_menus as $menu)
                                    <label for="menu_{{ $menu->id }}" 
                                           class="relative flex items-center p-5 rounded-2xl border-2 border-gray-50 cursor-pointer hover:border-red-100 hover:bg-red-50/30 transition-all group">
                                        
                                        <div class="flex items-center gap-4 w-full">
                                            <div class="relative flex items-center">
                                                <input type="checkbox" name="menu_ids[]" value="{{ $menu->id }}" id="menu_{{ $menu->id }}"
                                                       {{ $role->menus->contains($menu->id) ? 'checked' : '' }}
                                                       class="custom-checkbox w-5 h-5 rounded-lg border-2 border-gray-200 text-[#800000] focus:ring-[#800000] transition-all">
                                            </div>
                                            
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-gray-700 group-hover:text-[#800000] transition-colors">
                                                    {{ $menu->name }}
                                                </span>
                                                <span class="text-[10px] text-gray-400 font-medium uppercase tracking-tighter">Akses Modul</span>
                                            </div>
                                        </div>

                                        {{-- Icon Dekoratif --}}
                                        <div class="text-gray-100 group-hover:text-red-100 transition-colors">
                                            <i class="fas fa-shield-check text-xl"></i>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Action Footer --}}
                        <div class="bg-gray-50/50 px-8 py-6 flex items-center justify-between border-t border-gray-100">
                            <div class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                <i class="fas fa-info-circle text-[#800000]"></i>
                                Pastikan role memiliki minimal satu akses menu.
                            </div>
                            
                            <div class="flex items-center gap-4">
                                <button type="submit" 
                                        class="bg-[#800000] text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-red-900/20 hover:bg-red-900 hover:-translate-y-1 transition-all">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Info Section --}}
                <div class="mt-8 px-4 flex justify-between items-center text-[10px] font-black text-gray-300 uppercase tracking-[0.4em]">
                    <span>Security Level: High</span>
                    <span>Last Updated: {{ now()->format('d/m/Y') }}</span>
                </div>
            </div>
        </main>
    </div>

    <script>
        // SweetAlert untuk Konfirmasi Update
        document.getElementById('roleUpdateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;

            Swal.fire({
                title: 'UPDATE HAK AKSES?',
                text: "Perubahan ini akan langsung berdampak pada semua pengguna dengan role {{ $role->name }}.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'YA, PERBARUI',
                cancelButtonText: 'BATAL',
                reverseButtons: true,
                confirmButtonColor: '#800000',
                customClass: {
                    popup: 'rounded-[2.5rem]',
                    confirmButton: 'rounded-xl px-6 py-3',
                    cancelButton: 'rounded-xl px-6 py-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Notifikasi Sukses
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'BERHASIL',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2500,
                iconColor: '#800000',
                customClass: { popup: 'rounded-[2.5rem]' }
            });
        @endif
    </script>
</body>
</html>