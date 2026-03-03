<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Detailed Documentation & Team - SIMP</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; scroll-behavior: smooth; }
        .gradient-text {
            background: linear-gradient(to right, #800000, #4a0000);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 2rem;
        }
        .hover-lift { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .hover-lift:hover { transform: translateY(-10px); }
        
        /* Custom scrollbar untuk estetika */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #800000; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#F8F9FA] text-gray-800 antialiased">
    <x-header></x-header>

    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="w-64 fixed inset-y-0 left-0 z-50 shadow-2xl bg-white border-r border-gray-100">
            <x-sidebar></x-sidebar>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 ml-64 min-h-screen">
            <div class="p-8 lg:p-12 max-w-6xl mx-auto">
                
                {{-- Hero Section: Professional Branding --}}
                <div class="mb-24 text-center">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-50 border border-red-100 mb-8">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-2 rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#800000]"></span>
                        </span>
                        <span class="text-[10px] font-black text-[#800000] uppercase tracking-[0.3em]">UKK 2026 Innovation Project</span>
                    </div>
                    <h1 class="text-7xl font-black text-gray-900 uppercase italic tracking-tighter leading-[0.9] mb-8">
                        Mastering <br> <span class="gradient-text not-italic underline decoration-[#800000] decoration-8 underline-offset-8">Hospitality Tech</span>
                    </h1>
                    <p class="max-w-3xl mx-auto text-gray-500 font-medium text-lg leading-relaxed">
                        SIMP (Sistem Informasi Manajemen Perhotelan) adalah ekosistem digital mutakhir yang dirancang untuk merevolusi efisiensi operasional hotel melalui integrasi cerdas data dan antarmuka pengguna yang intuitif.
                    </p>
                </div>

                {{-- Section: Technical Deep Dive --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-24">
                    <div class="lg:col-span-2 bg-white p-12 rounded-[4rem] shadow-2xl shadow-gray-200/50 border border-gray-50 relative overflow-hidden">
                        <div class="relative z-10">
                            <h3 class="text-2xl font-black text-gray-900 mb-8 uppercase italic flex items-center gap-4">
                                <span class="w-12 h-1 bg-[#800000]"></span> Core Methodology
                            </h3>
                            <div class="space-y-6 text-gray-600 leading-relaxed italic font-medium">
                                <p>
                                    Pengembangan SIMP menerapkan standar **SDLC (Software Development Life Cycle)** yang ketat. Dimulai dari analisis kebutuhan industri perhotelan SIG, perancangan skema database relasional yang kompleks, hingga implementasi keamanan data tingkat tinggi.
                                </p>
                                <p>
                                    Sistem ini menangani alur kerja kritis: dari sistem pemesanan *front-desk*, status ketersediaan kamar secara *atomic*, hingga laporan audit finansial yang mendetail untuk keperluan manajerial.
                                </p>
                            </div>
                        </div>
                        <i class="fas fa-quote-right absolute bottom-10 right-10 text-9xl text-gray-50"></i>
                    </div>
                    <div class="space-y-6">
                        <div class="bg-[#800000] p-8 rounded-[3rem] text-white shadow-xl shadow-red-900/20">
                            <h4 class="text-xs font-black uppercase tracking-widest mb-4 opacity-70">Infrastructure</h4>
                            <ul class="space-y-3 font-bold text-sm">
                                <li class="flex items-center gap-3"><i class="fas fa-check-circle text-red-400"></i> PHP 8.2 Runtime</li>
                                <li class="flex items-center gap-3"><i class="fas fa-check-circle text-red-400"></i> Laravel 10.x Framework</li>
                                <li class="flex items-center gap-3"><i class="fas fa-check-circle text-red-400"></i> Vite Asset Bundling</li>
                            </ul>
                        </div>
                        <div class="bg-gray-900 p-8 rounded-[3rem] text-white shadow-xl shadow-black/20">
                            <h4 class="text-xs font-black uppercase tracking-widest mb-4 opacity-70">UI Engine</h4>
                            <ul class="space-y-3 font-bold text-sm">
                                <li class="flex items-center gap-3"><i class="fas fa-bolt text-yellow-400"></i> Tailwind CSS 3.0</li>
                                <li class="flex items-center gap-3"><i class="fas fa-bolt text-yellow-400"></i> Alpine.js Reactive</li>
                                <li class="flex items-center gap-3"><i class="fas fa-bolt text-yellow-400"></i> Jakarta Sans Typography</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Section: Meet The Masterminds (Detailed Role) --}}
                <div class="mb-32">
                    <div class="text-center mb-16">
                        <h2 class="text-xs font-black text-[#800000] uppercase tracking-[0.5em] mb-4">The Development Team</h2>
                        <h3 class="text-4xl font-black italic tracking-tighter uppercase leading-none">Creative & Engineering <br> <span class="gradient-text not-italic">Department</span></h3>
                    </div>
                    
                    <div class="team-grid">
                        {{-- Galang --}}
                        <div class="bg-white p-8 rounded-[3rem] border border-gray-100 shadow-xl hover-lift text-center">
                            <div class="w-24 h-24 bg-[#800000] rounded-full mx-auto mb-6 flex items-center justify-center text-white text-3xl font-black shadow-lg">G</div>
                            <h4 class="font-black text-gray-900 text-sm uppercase">Galang</h4>
                            <p class="text-[9px] font-black text-[#800000] uppercase tracking-widest mb-4 italic leading-tight">Fullstack Engineer & <br>System Integrator</p>
                            <p class="text-[10px] text-gray-400 font-medium">Bertanggung jawab atas arsitektur server-side dan sinkronisasi API.</p>
                        </div>

                        {{-- Tito --}}
                        <div class="bg-white p-8 rounded-[3rem] border border-gray-100 shadow-xl hover-lift text-center">
                            <div class="w-24 h-24 bg-[#800000] rounded-full mx-auto mb-6 flex items-center justify-center text-white text-3xl font-black shadow-lg">T</div>
                            <h4 class="font-black text-gray-900 text-sm uppercase">Tito</h4>
                            <p class="text-[9px] font-black text-[#800000] uppercase tracking-widest mb-4 italic leading-tight">Backend Specialist & <br>Database Architect</p>
                            <p class="text-[10px] text-gray-400 font-medium">Merancang skema database relasional dan logika bisnis transaksi.</p>
                        </div>

                        {{-- Maul --}}
                        <div class="bg-white p-8 rounded-[3rem] border border-gray-100 shadow-xl hover-lift text-center">
                            <div class="w-24 h-24 bg-gray-900 rounded-full mx-auto mb-6 flex items-center justify-center text-white text-3xl font-black shadow-lg">M</div>
                            <h4 class="font-black text-gray-900 text-sm uppercase">Keyzo</h4>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4 italic leading-tight">Lead UI/UX &<br>Creative Designer</p>
                            <p class="text-[10px] text-gray-400 font-medium">Mengelola identitas visual dan pengalaman pengguna sistem SIMP.</p>
                        </div>

                        {{-- Keyzo --}}
                        <div class="bg-white p-8 rounded-[3rem] border border-gray-100 shadow-xl hover-lift text-center">
                            <div class="w-24 h-24 bg-gray-50 rounded-full mx-auto mb-6 flex items-center justify-center text-gray-900 text-3xl font-black border-2 border-dashed border-gray-200">K</div>
                            <h4 class="font-black text-gray-900 text-sm uppercase">Maul</h4>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4 italic leading-tight">Visual Interface &<br>Front End Developer</p>
                            <p class="text-[10px] text-gray-400 font-medium">Memastikan konsistensi komponen UI dan estetika navigasi dashboard.</p>
                        </div>

                        {{-- Farel --}}
                        <div class="bg-white p-8 rounded-[3rem] border border-gray-100 shadow-xl hover-lift text-center">
                            <div class="w-24 h-24 bg-gray-50 rounded-full mx-auto mb-6 flex items-center justify-center text-gray-900 text-3xl font-black border-2 border-dashed border-gray-200">F</div>
                            <h4 class="font-black text-gray-900 text-sm uppercase">Farel</h4>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4 italic leading-tight">Interactive Design &<br>UI/UX Designer</p>
                            <p class="text-[10px] text-gray-400 font-medium">Melakukan riset fungsionalitas tombol dan alur prototipe aplikasi.</p>
                        </div>
                    </div>
                </div>

                {{-- Section: Contact & Support (Ultimate Detail) --}}
                <div class="mb-24 relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#800000] to-gray-900 rounded-[5rem] transform -rotate-1"></div>
                    <div class="relative z-10 p-12 lg:p-20 text-white">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                            <div>
                                <h2 class="text-4xl font-black uppercase italic tracking-tighter mb-8 leading-none">Hubungi <br> <span class="text-red-400">Tim Pengembang</span></h2>
                                <p class="text-red-50/60 text-sm font-medium leading-loose mb-10 max-w-md">
                                    Butuh dukungan teknis atau ingin berkolaborasi lebih lanjut mengenai implementasi sistem? Tim kami siap memberikan konsultasi gratis mengenai skalabilitas proyek ini.
                                </p>
                                <div class="space-y-6">
                                    <div class="flex items-center gap-6 group cursor-pointer">
                                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center group-hover:bg-white group-hover:text-[#800000] transition-all shadow-lg">
                                            <i class="fab fa-instagram text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black uppercase tracking-widest text-red-300">Instagram Official</p>
                                            <a href="https://www.instagram.com/mollskuy_/" class="text-lg font-bold hover:text-red-300 transition-colors">@mollskuy_</a>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-6 group cursor-pointer">
                                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center group-hover:bg-white group-hover:text-[#800000] transition-all shadow-lg">
                                            <i class="fas fa-envelope-open-text text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black uppercase tracking-widest text-red-300">Business Inquiry</p>
                                            <p class="text-lg font-bold">engineering@hotel-sig.sch.id</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-6 group cursor-pointer">
                                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center group-hover:bg-white group-hover:text-[#800000] transition-all shadow-lg">
                                            <i class="fas fa-map-marker-alt text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black uppercase tracking-widest text-red-300">Development Hub</p>
                                            <p class="text-lg font-bold">Lab XII RPL 1, SMK SIG Innovation</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Decorative Contact Card --}}
                            <div class="glass-card p-10 rounded-[4rem] text-gray-900 border-white/20">
                                <div class="flex justify-between items-start mb-10">
                                    <i class="fas fa-fingerprint text-4xl text-[#800000]"></i>
                                    <span class="text-[10px] font-black uppercase tracking-widest bg-red-100 text-[#800000] px-3 py-1 rounded-full">Secure Project</span>
                                </div>
                                <h5 class="text-xl font-black uppercase italic mb-4 leading-tight">Project Verified by <br> Industry Expert</h5>
                                <p class="text-xs text-gray-500 leading-loose mb-8">
                                    Setiap baris kode dalam SIMP telah melewati proses *Quality Assurance* (QA) untuk menjamin performa maksimal saat implementasi di Lab Perhotelan.
                                </p>
                                <a href="https://www.instagram.com/mollskuy_/" class="block text-center bg-[#800000] text-white py-5 rounded-3xl text-xs font-black uppercase tracking-[0.2em] hover:bg-black transition-colors shadow-2xl shadow-red-900/40">Kirim Pesan Sekarang</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Copyright --}}
                <footer class="mt-20 text-center pb-12">
                    <div class="h-px w-32 bg-gray-200 mx-auto mb-10"></div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.5em] mb-4">&copy; 2026 Hotel SIG Digital Innovation</p>
                    <div class="flex justify-center gap-4 text-gray-300">
                        <i class="fab fa-laravel"></i>
                        <i class="fab fa-github"></i>
                        <i class="fab fa-docker"></i>
                    </div>
                </footer>

            </div>
        </main>
    </div>
</body>
</html>