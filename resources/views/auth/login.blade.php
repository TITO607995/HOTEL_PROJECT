<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perhotelan SMKSIG</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #ffffff;
        }

        .bg-gradient-maroon {
            background: linear-gradient(135deg, #8B0000 0%, #4A0000 100%);
        }

        /* Border radius custom untuk lekukan yang lebih nyeni */
        .modern-curve {
            border-bottom-right-radius: 180px;
            border-top-right-radius: 40px;
        }

        .input-focus:focus {
            box-shadow: 0 0 0 4px rgba(139, 0, 0, 0.1);
            border-color: #8B0000;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade { animation: fadeIn 0.5s ease-out forwards; }
    </style>
</head>
<body class="antialiased overflow-hidden">

    <div class="flex min-h-screen">
        
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-maroon relative items-center justify-center p-16 modern-curve shadow-2xl">
            <div class="absolute top-10 left-10 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
            <div class="absolute bottom-20 right-20 w-64 h-64 bg-black/20 rounded-full blur-3xl"></div>
            
            <div class="relative z-10 text-center animate-fade">
                <div class="inline-flex items-center justify-center w-28 h-28 bg-white/10 backdrop-blur-xl rounded-[30%_70%_70%_30%/30%_30%_70%_70%] mb-8 border border-white/20 shadow-inner">
                    <i class="fas fa-hotel text-5xl text-white"></i>
                </div>
                <h1 class="text-5xl font-extrabold text-white tracking-tighter mb-4">
                    Website Practice Skill <br><span class="text-red-400">Perhotelan </span>
                </h1>
                <p class="text-white/60 text-lg font-light leading-relaxed max-w-sm mx-auto">
                    Sistem Manajemen Perhotelan Modern untuk Masa Depan Digital.
                </p>
            </div>

            <div class="absolute bottom-10 left-10 text-white/30 text-xs tracking-widest uppercase">
                Premium v2.0 &bull; Built for Excellence
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
            <div class="w-full max-w-md animate-fade" style="animation-delay: 0.2s;">
                
                <div class="flex justify-center items-center gap-6 mb-12">
                    <div class="group relative">
                        <div class="absolute -inset-1 bg-gradient-to-r from-red-600 to-maroon-600 rounded-full blur opacity-25 group-hover:opacity-50 transition"></div>
                        <img src="{{ asset('image/logoph.png') }}" alt="Logo SIG" class="relative w-16 h-16 rounded-full object-cover border-2 border-white shadow-md">
                    </div>
                    <div class="w-px h-10 bg-gray-200"></div>
                    <div class="group relative">
                        <div class="absolute -inset-1 bg-gradient-to-r from-red-600 to-maroon-600 rounded-full blur opacity-25 group-hover:opacity-50 transition"></div>
                        <img src="{{ asset('image/smksig.png') }}" alt="Logo PH" class="relative w-16 h-16 rounded-full object-cover border-2 border-white shadow-md">
                    </div>
                </div>

                <div class="text-center lg:text-left mb-10">
                    <h2 class="text-3xl font-black text-gray-900 tracking-tight">Portal Manajemen</h2>
                    <p class="text-gray-500 mt-2 font-medium">Silakan login dengan kredensial Anda.</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Email Address</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 group-focus-within:text-[#8B0000] transition-colors">
                                <i class="fas fa-envelope text-sm"></i>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none transition-all input-focus text-gray-800 font-semibold"
                                placeholder="nama@email.com">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center ml-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-bold text-[#8B0000] hover:underline transition">Lupa?</a>
                            @endif
                        </div>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 group-focus-within:text-[#8B0000] transition-colors">
                                <i class="fas fa-lock text-sm"></i>
                            </span>
                            <input type="password" name="password" id="password" required autocomplete="current-password"
                                class="w-full pl-12 pr-12 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none transition-all input-focus text-gray-800 font-semibold"
                                placeholder="••••••••">
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-[#8B0000]">
                                <i class="fas fa-eye" id="eye-icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-[#8B0000] shadow-sm focus:ring-[#8B0000]">
                        <label for="remember_me" class="ml-2 text-sm text-gray-600">Ingat saya di perangkat ini</label>
                    </div>

                    <button type="submit" 
                        class="w-full py-4 bg-[#1A1A1A] text-white rounded-2xl font-bold text-sm hover:bg-black hover:shadow-2xl hover:-translate-y-1 active:scale-95 transition-all duration-300">
                        Masuk ke Dashboard
                    </button>
                </form>

                <p class="mt-16 text-center text-gray-400 text-[10px] font-bold uppercase tracking-[0.2em]">
                    &copy; 2026 5NYENI AND AI &bull; XII RPL 1 @2026
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (pwd.type === "password") {
                pwd.type = "text";
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                pwd.type = "password";
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
        // Cek apakah ada error dari Laravel
    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Login Gagal!',
            text: '{{ $errors->first() }}', // Mengambil pesan error pertama (misal: "These credentials do not match our records.")
            confirmButtonColor: '#8B0000', // Warna Maroon senada dengan tema kamu
            background: '#ffffff',
            customClass: {
                title: 'text-2xl font-black text-gray-900',
                popup: 'rounded-3xl shadow-2xl border-none'
            }
        });
    @endif

    // Cek jika ada session status (misal setelah reset password)
    @if (session('status'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('status') }}',
            confirmButtonColor: '#8B0000',
            customClass: {
                popup: 'rounded-3xl'
            }
        });
    @endif
    </script>
</body>
</html>