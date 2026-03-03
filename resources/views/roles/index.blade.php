<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>Manajemen Role - Hotel SIG</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .table-shadow { box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04); }
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
                        <div class="flex items-center gap-3 mb-2">
                            <span class="bg-[#800000] p-2 rounded-lg text-white text-xs">
                                <i class="fas fa-shield-halved"></i>
                            </span>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Security Settings</span>
                        </div>
                        <h2 class="text-3xl font-black text-gray-900 uppercase italic tracking-tighter leading-none">
                            Role & <span class="text-[#800000] not-italic">Hak Akses</span>
                        </h2>
                        <p class="text-gray-400 text-sm mt-3 font-medium">Konfigurasi izin menu untuk setiap level jabatan karyawan.</p>
                    </div>
                    
                    <a href="{{ route('roles.create') }}" 
                       class="bg-[#800000] text-white px-6 py-4 rounded-2xl font-black text-[10px] shadow-xl shadow-red-900/20 hover:bg-red-900 hover:-translate-y-1 transition-all flex items-center gap-3 uppercase tracking-[0.2em]">
                        <i class="fas fa-plus-circle text-sm"></i> Tambah Role Baru
                    </a>
                </div>

                {{-- Table Card --}}
                <div class="bg-white rounded-[2.5rem] border border-gray-100 overflow-hidden table-shadow">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Jabatan (Role)</th>
                                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Izin Akses Menu</th>
                                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Konfigurasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($roles as $role)
                                <tr class="hover:bg-gray-50/30 transition-colors group">
                                    <td class="p-6">
                                        <div class="flex flex-col">
                                            <span class="font-black text-gray-800 text-sm uppercase tracking-tight group-hover:text-[#800000] transition-colors">
                                                {{ $role->name }}
                                            </span>
                                            <span class="text-[10px] text-gray-300 font-bold uppercase tracking-widest mt-1 italic">Level #{{ $role->id }}</span>
                                        </div>
                                    </td>
                                    <td class="p-6">
                                        <div class="flex flex-wrap gap-2">
                                            @forelse($role->menus as $menu)
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-tighter bg-white border border-gray-100 shadow-sm text-gray-500 group-hover:border-[#800000]/20 transition-all">
                                                    <i class="fas fa-circle text-[4px] mr-2 text-[#800000]"></i>
                                                    {{ $menu->name }}
                                                </span>
                                            @empty
                                                <span class="text-[10px] text-gray-300 font-bold italic tracking-widest bg-gray-50 px-4 py-2 rounded-xl border border-dashed border-gray-200">
                                                    <i class="fas fa-ban mr-1"></i> No Permissions
                                                </span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="p-6">
                                        <div class="flex justify-center gap-2">
                                            @if($role->name !== 'Superadmin')
                                                <a href="{{ route('roles.edit', $role->id) }}" 
                                                   class="bg-white border border-gray-200 text-gray-700 px-4 py-2.5 rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-[#800000] hover:text-white hover:border-[#800000] transition-all shadow-sm">
                                                    <i class="fas fa-pen-nib mr-1"></i> Edit
                                                </a>

                                                <form id="delete-form-{{ $role->id }}" action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" onclick="confirmDelete('{{ $role->id }}', '{{ $role->name }}')"
                                                            class="bg-gray-50 text-gray-400 px-4 py-2.5 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <div class="flex items-center gap-2 px-4 py-2.5 bg-red-50/50 border border-red-100 rounded-xl text-[9px] font-black text-red-700 uppercase tracking-widest">
                                                    <i class="fas fa-crown"></i> Master Access
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Footer Info --}}
                <footer class="mt-12 flex justify-between items-center px-6">
                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.5em]">RBAC System • Hotel SIG v2.0</p>
                    <div class="flex items-center gap-2 text-gray-300">
                        <i class="fas fa-fingerprint text-sm opacity-20"></i>
                    </div>
                </footer>
            </div>
        </main>
    </div>

    <script>
        // SweetAlert2 Konfirmasi Hapus
        function confirmDelete(id, name) {
            Swal.fire({
                title: 'HAPUS ROLE?',
                html: `Apakah Anda yakin ingin menghapus role <b class="text-[#800000]">${name}</b>? <br> <small class="text-gray-400">Tindakan ini tidak dapat dibatalkan.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#800000',
                cancelButtonColor: '#d33',
                confirmButtonText: 'YA, HAPUS!',
                cancelButtonText: 'BATAL',
                reverseButtons: true,
                background: '#ffffff',
                customClass: {
                    popup: 'rounded-[2rem] border border-gray-100',
                    confirmButton: 'rounded-xl px-6 py-3 font-bold text-xs uppercase tracking-widest',
                    cancelButton: 'rounded-xl px-6 py-3 font-bold text-xs uppercase tracking-widest'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }

        // Notifikasi Sukses dari Session
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'BERHASIL!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2500,
                iconColor: '#800000',
                background: '#ffffff',
                customClass: {
                    popup: 'rounded-[2rem] border border-gray-100 shadow-2xl',
                    title: 'text-sm font-black tracking-widest text-gray-800'
                }
            });
        @endif
    </script>
</body>
</html>