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

    <title>Assign Role - Hotel SIG</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); }
    </style>
</head>

<body class="bg-[#F8F9FA] text-gray-800 antialiased">
    <x-header></x-header>

    <div class="flex min-h-screen">
        <aside class="w-64 fixed inset-y-0 left-0 z-50 shadow-2xl bg-white border-r border-gray-100">
            <x-sidebar></x-sidebar>
        </aside>

        <main class="flex-1 ml-64 flex flex-col min-h-screen">
            <div class="p-8 lg:p-12 flex flex-col items-center">
                
                {{-- Header Section --}}
                <div class="w-full max-w-xl mb-10 text-center">
                    <h2 class="text-3xl font-black text-gray-900 uppercase italic tracking-tighter leading-none">
                        Assign <span class="text-[#800000] not-italic">Role</span>
                    </h2>
                    <p class="text-gray-400 text-sm mt-3 font-medium">Perbarui hak akses untuk <span class="text-gray-700 font-bold">{{ $user->name }}</span></p>
                </div>

                {{-- Form Card --}}
                <div class="w-full max-w-xl bg-white rounded-[2.5rem] p-8 lg:p-10 shadow-2xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden">
                    {{-- Decorative Element --}}
                    <div class="absolute top-0 right-0 w-32 h-32 bg-red-50 rounded-bl-[5rem] -mr-10 -mt-10 opacity-50"></div>

                    <form id="roleForm" action="{{ route('assign-role.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-8 relative z-10">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Informasi Pengguna</label>
                            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                                <div class="w-12 h-12 rounded-xl bg-[#800000] text-white flex items-center justify-center font-bold text-lg">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-sm text-gray-800">{{ $user->name }}</h4>
                                    <p class="text-[11px] text-gray-400 font-medium">{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-10 relative z-10">
                            <label for="role_id" class="block text-[10px] font-black text-[#800000] uppercase tracking-[0.2em] mb-3">Pilih Role Baru</label>
                            <div class="relative">
                                <select name="role_id" id="role_id" required
                                        class="w-full bg-white border-2 border-gray-100 rounded-2xl px-5 py-4 text-sm font-bold text-gray-700 outline-none focus:border-[#800000] focus:ring-4 focus:ring-red-50 transition-all appearance-none cursor-pointer">
                                    <option value="" disabled>-- Klik untuk memilih --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 pt-6 border-t border-gray-50 relative z-10">
                            <a href="{{ route('assign-role.index') }}" 
                               class="flex-1 text-center py-4 rounded-2xl text-xs font-bold text-gray-400 uppercase tracking-widest hover:bg-gray-50 transition-all">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="flex-[2] bg-[#800000] text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-red-900/20 hover:bg-red-900 hover:-translate-y-1 transition-all">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Footer Info --}}
                <p class="mt-8 text-[10px] font-black text-gray-300 uppercase tracking-[0.4em]">Hotel SIG Security Protocol v1.0</p>
            </div>
        </main>
    </div>

    <script>
        // SweetAlert untuk Konfirmasi Simpan
        document.getElementById('roleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const roleName = document.getElementById('role_id').options[document.getElementById('role_id').selectedIndex].text;

            Swal.fire({
                title: 'KONFIRMASI ROLE',
                html: `Apakah Anda yakin ingin mengubah role <br><b>{{ $user->name }}</b> menjadi <b class="text-[#800000]">${roleName}</b>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'YA, PERBARUI',
                cancelButtonText: 'CEK LAGI',
                reverseButtons: true,
                confirmButtonColor: '#800000',
                customClass: {
                    popup: 'rounded-[2rem]',
                    confirmButton: 'rounded-xl px-6 py-3',
                    cancelButton: 'rounded-xl px-6 py-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Notifikasi Sukses dari Session
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'BERHASIL',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2500,
                iconColor: '#800000',
                customClass: { popup: 'rounded-[2rem]' }
            });
        @endif
    </script>
</body>
</html>