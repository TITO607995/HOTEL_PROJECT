<!DOCTYPE html>
<html lang="id" class="h-full overflow-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blacklist Monitor - Hotel SIG</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .light-card { background: #ffffff; border: 1px solid #e5e7eb; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); }
        .red-glow-soft { box-shadow: 0 0 20px rgba(153, 27, 27, 0.08); }
        /* Custom Scrollbar biar tetep cakep */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-700 h-full">

    {{-- Header --}}
    <div class="fixed top-0 left-0 right-0 z-[60] bg-white border-b border-slate-200">
        <x-header></x-header>
    </div>
    
    <div class="flex h-screen pt-16 overflow-hidden"> 
        {{-- Sidebar --}}
        <aside class="hidden lg:block w-72 flex-shrink-0 border-r border-slate-200 bg-white">
            <x-sidebar></x-sidebar>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 h-full overflow-y-auto">
            <div class="max-w-7xl mx-auto p-6 lg:p-10">

                <header class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
                    <div class="space-y-1">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-red-50 rounded-full border border-red-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-600 animate-pulse"></span>
                            <span class="text-[10px] font-black text-red-600 uppercase tracking-[0.2em]">Restricted Access List</span>
                        </div>
                        <h1 class="text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">
                            Banned <span class="text-red-600 italic font-serif">Endpoints</span>
                        </h1>
                        <p class="text-slate-500 font-medium">Daftar IP yang diblokir permanen oleh sistem keamanan.</p>
                    </div>

                    <a href="{{ route('admin.devices.index') }}" class="px-6 py-3 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-2xl font-bold transition-all shadow-sm flex items-center gap-2 text-sm">
                        <i class="fas fa-arrow-left text-red-600"></i> Back to Monitor
                    </a>
                </header>

                <div class="light-card rounded-[2.5rem] overflow-hidden red-glow-soft">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Blocked IP Address</th>
                                    <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Reason / Notes</th>
                                    <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Banned Date</th>
                                    <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-widest text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($blacklisted as $ip)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-600 border border-red-100">
                                                <i class="fas fa-user-shield"></i>
                                            </div>
                                            <span class="font-mono font-bold text-slate-800 text-lg tracking-tight">{{ $ip->ip_address }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="text-sm text-slate-500 italic">"{{ $ip->reason ?? 'No specific reason' }}"</span>
                                    </td>
                                    <td class="px-8 py-6 text-sm text-slate-400">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-600">{{ $ip->created_at->format('d M Y') }}</span>
                                            <span class="text-xs">{{ $ip->created_at->format('H:i') }} WIB</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <form action="{{ route('admin.ip.unblock', $ip->id) }}" method="POST" class="flex justify-center">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="group px-5 py-2.5 bg-white hover:bg-green-600 text-green-600 hover:text-white border border-green-200 hover:border-green-600 rounded-xl transition-all duration-300 text-xs font-black uppercase tracking-widest shadow-sm">
                                                <i class="fas fa-unlock-alt mr-2 opacity-70 group-hover:opacity-100"></i> Release IP
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-24 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-300">
                                                <i class="fas fa-shield-check text-4xl"></i>
                                            </div>
                                            <p class="text-slate-400 font-bold uppercase tracking-[0.2em] text-sm">No IP Addresses currently in jail.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <footer class="py-12 text-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.5em]">SIG-CORE ENGINE V.3.1 SECURITY PROTOCOL</p>
                </footer>
            </div>
        </main>
    </div>

    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'RELEASED',
            text: "{{ session('success') }}",
            background: '#ffffff',
            color: '#1e293b',
            confirmButtonColor: '#10b981',
            customClass: { popup: 'rounded-[2rem] border-none shadow-2xl' }
        });
    </script>
    @endif
</body>
</html>