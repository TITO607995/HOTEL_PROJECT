<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Hotel SIG</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .bg-gradient-maroon {
            background: linear-gradient(135deg, #8B0000 0%, #4A0000 100%);
        }

        .modern-curve {
            border-bottom-right-radius: 180px;
            border-top-right-radius: 40px;
        }

        .input-focus:focus {
            box-shadow: 0 0 0 4px rgba(139, 0, 0, 0.1);
            border-color: #8B0000;
        }
    </style>
</head>
<body class="bg-white antialiased overflow-hidden">

    <div class="flex min-h-screen">
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-maroon relative items-center justify-center p-16 modern-curve shadow-2xl">
            <div class="relative z-10 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-xl rounded-2xl mb-8 border border-white/20">
                    <i class="fas fa-key text-4xl text-white"></i>
                </div>
                <h1 class="text-4xl font-extrabold text-white tracking-tighter mb-4">
                    Atur Ulang <br><span class="text-red-400">Kata Sandi</span>
                </h1>
                <p class="text-white/60 text-sm font-light leading-relaxed max-w-xs mx-auto">
                    Keamanan akun Anda adalah prioritas kami. Silakan masukkan kata sandi baru Anda.
                </p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                
                <div class="mb-10">
                    <h2 class="text-3xl font-black text-gray-900 tracking-tight">Password Baru</h2>
                    <p class="text-gray-500 mt-2 font-medium">Buat kata sandi yang kuat dan mudah diingat.</p>
                </div>

                <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Email Anda</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                <i class="fas fa-envelope text-sm"></i>
                            </span>
                            <input type="email" name="email" value="{{ old('email', $request->email) }}" required readonly
                                class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none text-gray-500 font-semibold cursor-not-allowed">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Kata Sandi Baru</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                <i class="fas fa-lock text-sm"></i>
                            </span>
                            <input type="password" name="password" required autofocus autocomplete="new-password"
                                class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none transition-all input-focus text-gray-800 font-semibold"
                                placeholder="••••••••">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Konfirmasi Sandi</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                <i class="fas fa-shield-alt text-sm"></i>
                            </span>
                            <input type="password" name="password_confirmation" required autocomplete="new-password"
                                class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none transition-all input-focus text-gray-800 font-semibold"
                                placeholder="••••••••">
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <button type="submit" 
                        class="w-full py-4 bg-[#8B0000] text-white rounded-2xl font-bold text-sm hover:bg-black hover:shadow-2xl hover:-translate-y-1 active:scale-95 transition-all duration-300 shadow-lg">
                        Simpan Kata Sandi Baru
                    </button>

                    <div class="text-center mt-6">
                        <a href="{{ route('login') }}" class="text-xs font-bold text-gray-400 hover:text-[#8B0000] transition">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Login
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>

</body>
</html>