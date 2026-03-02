<!DOCTYPE html>
<html lang="id" x-data="loginSystem()" x-init="init()" :class="isLimited ? 'system-locked' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perhotelan SMKSIG</title>
    
    <script>
        // Pencegahan dini sebelum Alpine.js load
        (function() {
            const lockUntil = localStorage.getItem('login_lock_until');
            const now = Math.floor(Date.now() / 1000);
            if (lockUntil && (parseInt(lockUntil) - now > 0)) {
                document.documentElement.classList.add('system-locked');
            }
        })();
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #ffffff; }
        .bg-gradient-maroon { background: linear-gradient(135deg, #8B0000 0%, #4A0000 100%); }
        .modern-curve { border-bottom-right-radius: 180px; border-top-right-radius: 40px; }
        .input-focus:focus { box-shadow: 0 0 0 4px rgba(139, 0, 0, 0.1); border-color: #8B0000; }
        
        /* CSS HARD FREEZE - KURSOR DILARANG & INPUT MATI */
        .system-locked, .system-locked * {
            cursor: not-allowed !important; /* Kursor jadi tanda dilarang di seluruh layar */
        }

        .system-locked .freeze-field {
            pointer-events: none !important; /* Gak bisa diklik/disentuh */
            user-select: none !important; 
            background-color: #f3f4f6 !important;
            color: #9ca3af !important;
            opacity: 0.6;
        }

        .system-locked .btn-login-main {
            filter: grayscale(1);
            opacity: 0.5;
            pointer-events: none !important;
        }

        [x-cloak] { display: none !important; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
        .animate-fade { animation: fadeIn 0.5s ease-out forwards; }
        .animate-shake { animation: shake 0.2s ease-in-out 0s 2; }
    </style>
</head>

@php
    use Illuminate\Support\Facades\RateLimiter;
    $ip = request()->ip();
    // Cek sisa detik dari berbagai kemungkinan key Laravel
    $secondsLeft = RateLimiter::availableIn('login:'.$ip) 
                   ?: RateLimiter::availableIn($ip) 
                   ?: 0;
@endphp

<body class="antialiased overflow-hidden">

    {{-- Alert Floating --}}
    <div x-show="isLimited" x-transition x-cloak class="fixed top-5 right-5 z-50">
        <div class="bg-red-50 border-l-4 border-red-600 p-4 shadow-lg rounded-r-xl flex items-center gap-3">
            <i class="fas fa-snowflake text-blue-500 animate-pulse text-xl"></i>
            <div>
                <p class="text-xs font-bold text-red-800 uppercase tracking-tighter">System Frozen</p>
                <p class="text-[10px] text-red-600 font-medium italic">Tunggu: <span x-text="seconds"></span>s</p>
            </div>
        </div>
    </div>

    <div class="flex min-h-screen">
        {{-- Sisi Kiri (Desktop Only) --}}
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-maroon relative items-center justify-center p-16 modern-curve shadow-2xl">
            <div class="absolute top-10 left-10 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
            <div class="relative z-10 text-center animate-fade">
                <div class="inline-flex items-center justify-center w-28 h-28 bg-white/10 backdrop-blur-xl rounded-3xl mb-8 border border-white/20">
                    <i class="fas fa-hotel text-5xl text-white"></i>
                </div>
                <h1 class="text-5xl font-extrabold text-white tracking-tighter mb-4">Practice Skill <br><span class="text-red-400">Hotel SIG</span></h1>
                <p class="text-white/60 text-lg font-light max-w-sm mx-auto">Manajemen Perhotelan SMKSIG.</p>
            </div>
        </div>

        {{-- Sisi Kanan (Form) --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
            <div class="w-full max-w-md animate-fade">
                
                <div class="flex justify-center items-center gap-6 mb-12">
                    <img src="{{ asset('image/logoph.png') }}" alt="Logo PH" class="w-16 h-16 rounded-full border-2 border-gray-100 shadow-sm">
                    <div class="w-px h-10 bg-gray-200"></div>
                    <img src="{{ asset('image/smksig.png') }}" alt="Logo SIG" class="w-16 h-16 rounded-full border-2 border-gray-100 shadow-sm">
                </div>

                <div class="text-center lg:text-left mb-10">
                    <h2 class="text-3xl font-black text-gray-900 tracking-tight">Selamat Datang</h2>
                    <p class="text-gray-500 mt-3 font-medium text-sm italic border-l-2 border-red-600 pl-3 uppercase tracking-widest">Authorized Personnel Only</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6" @submit="handleSubmit($event)">
                    @csrf

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Email Address</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                <i class="fas fa-user-shield text-sm"></i>
                            </span>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required 
                                :disabled="isLimited"
                                :readonly="isLimited"
                                class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none transition-all input-focus text-gray-800 font-semibold shadow-sm"
                                :class="isLimited ? 'freeze-field' : ''"
                                placeholder="Email terdaftar">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Security Password</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                <i class="fas fa-key text-sm"></i>
                            </span>
                            <input type="password" name="password" id="password" required
                                :disabled="isLimited"
                                :readonly="isLimited"
                                class="w-full pl-12 pr-12 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none transition-all input-focus text-gray-800 font-semibold shadow-sm"
                                :class="isLimited ? 'freeze-field' : ''"
                                placeholder="••••••••">
                            <button type="button" x-show="!isLimited" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400">
                                <i class="fas fa-eye" id="eye-icon"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Timer Progress --}}
                    <div x-show="isLimited" x-cloak class="space-y-2 animate-fade">
                        <div class="flex justify-between text-[10px] font-bold text-red-600 uppercase">
                            <span>Akses Dibekukan...</span>
                            <span x-text="seconds + 's'"></span>
                        </div>
                        <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-red-600 h-full transition-all duration-1000 ease-linear shadow-[0_0_10px_rgba(220,38,38,0.5)]"
                                 :style="`width: ${(seconds / maxSeconds) * 100}%` "></div>
                        </div>
                    </div>

                    <button type="submit" 
                        class="btn-login-main w-full py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] transition-all duration-300 flex items-center justify-center gap-3"
                        x-bind:disabled="isLoading || isLimited"
                        x-bind:class="{
                            'bg-gray-100 text-gray-300 cursor-not-allowed': isLimited,
                            'bg-[#1A1A1A] hover:bg-black hover:shadow-2xl hover:-translate-y-1 text-white shadow-lg': !isLimited,
                            'animate-shake': shakeTrigger
                        }">
                        <template x-if="isLoading && !isLimited">
                            <i class="fas fa-circle-notch animate-spin text-sm"></i>
                        </template>
                        <i x-show="isLimited" class="fas fa-snowflake text-blue-400"></i>
                        <span x-text="isLimited ? 'LOCKED (' + seconds + 's)' : (isLoading ? 'Memproses...' : 'Akses Dashboard')"></span>
                    </button>
                </form>

                <div class="mt-12 pt-6 border-t border-gray-50 flex justify-between items-center text-gray-400">
                    <p class="text-[10px] font-bold uppercase">&copy; 2026 Hotel SIG &bull; Security v2.9</p>
                    <div class="flex gap-4"><i class="fab fa-fingerprint"></i><i class="fas fa-microchip"></i></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function loginSystem() {
            return {
                isLoading: false,
                isLimited: false,
                seconds: 0,
                maxSeconds: 60,
                shakeTrigger: false,

                init() {
                    let serverSeconds = {{ $secondsLeft ?? 0 }};
                    let localLock = localStorage.getItem('login_lock_until');
                    let now = Math.floor(Date.now() / 1000);
                    let localSeconds = localLock ? (parseInt(localLock) - now) : 0;
                    
                    this.seconds = Math.max(serverSeconds, localSeconds);

                    if (this.seconds > 0) {
                        this.lockSystem();
                        this.startTimer();
                    }
                },

                lockSystem() {
                    this.isLimited = true;
                    this.maxSeconds = Math.max(this.seconds, 60);
                    
                    // Tambahkan class global ke HTML
                    document.documentElement.classList.add('system-locked');
                    
                    // Simpan ke localStorage agar awet meski di-refresh
                    const lockUntil = Math.floor(Date.now() / 1000) + this.seconds;
                    localStorage.setItem('login_lock_until', lockUntil);

                    // PAKSA LEPAS KURSOR dari semua input (Anti-Typing)
                    this.$nextTick(() => {
                        const inputs = document.querySelectorAll('input');
                        inputs.forEach(input => {
                            input.blur(); 
                            if(this.seconds > 5) input.value = ""; // Kosongkan jika limit masih lama
                        });
                    });
                },

                startTimer() {
                    let timer = setInterval(() => {
                        this.seconds--;
                        if (this.seconds <= 0) {
                            this.isLimited = false;
                            localStorage.removeItem('login_lock_until');
                            document.documentElement.classList.remove('system-locked');
                            clearInterval(timer);
                        }
                    }, 1000);
                },

                handleSubmit(e) {
                    if (this.isLimited) {
                        e.preventDefault();
                        this.shakeTrigger = true;
                        setTimeout(() => this.shakeTrigger = false, 500);
                        return false;
                    }
                    this.isLoading = true;
                }
            }
        }

        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            pwd.type = pwd.type === "password" ? "text" : "password";
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }

        // Tangkap Error 429 dari Laravel
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'AKSES DITOLAK',
                text: '{{ $errors->first() }}',
                confirmButtonColor: '#8B0000',
                customClass: {
                    popup: 'rounded-[2rem]',
                    confirmButton: 'rounded-xl px-10 py-3 font-bold'
                }
            });
        @endif
    </script>
</body>
</html>