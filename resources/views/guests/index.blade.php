<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
    <title>Manajemen Tamu</title>
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-[#D9D9D9] min-h-screen">

    <x-header></x-header>
    <x-sidebar></x-sidebar>

    <div class="flex">
        <main class="flex-1 ml-64 p-8">
            
            <div class="mb-8">
                <h1 class="text-3xl font-black text-[#800000] italic uppercase tracking-tighter">
                    Manajemen Tamu
                </h1>
                <p class="text-gray-600 text-sm">Kelola status privasi dan informasi kontak tamu hotel.</p>
            </div>

            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border-t-[10px] border-[#800000]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="p-5 text-xs font-black uppercase text-gray-400 tracking-widest">Nama Tamu</th>
                                <th class="p-5 text-xs font-black uppercase text-gray-400 tracking-widest">Status Privasi</th>
                                <th class="p-5 text-xs font-black uppercase text-gray-400 tracking-widest">Kontak</th>
                                <th class="p-5 text-xs font-black uppercase text-gray-400 tracking-widest text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($guests as $guest)
                            <tr class="hover:bg-gray-50/50 transition-all duration-200">
                                <td class="p-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-[#800000]">
                                            <i class="fas {{ $guest->is_incognito ? 'fa-user-secret' : 'fa-user' }}"></i>
                                        </div>
                                        <div class="font-bold text-gray-800 {{ $guest->is_incognito ? 'italic text-gray-400' : '' }}">
                                            {{ $guest->is_incognito ? 'Guest Masked' : $guest->guest_name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="p-5">
                                    <span class="inline-flex items-center gap-2 text-[10px] font-black px-4 py-1.5 rounded-full {{ $guest->is_incognito ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $guest->is_incognito ? 'bg-purple-700' : 'bg-blue-700' }}"></span>
                                        {{ $guest->is_incognito ? 'INCOGNITO' : 'PUBLIC' }}
                                    </span>
                                </td>
                                <td class="p-5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-700">{{ $guest->email }}</span>
                                        <span class="text-[10px] text-gray-400">Terdaftar pada {{ $guest->created_at->format('d M Y') }}</span>
                                    </div>
                                </td>
                                <td class="p-5 text-center">
                                    <form action="{{ route('guests.toggle-incognito', $guest->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="group flex items-center gap-2 mx-auto text-[10px] font-bold border-2 border-[#800000] text-[#800000] px-4 py-2 rounded-xl hover:bg-[#800000] hover:text-white transition-all duration-300 shadow-sm active:scale-95">
                                            <i class="fas fa-sync-alt group-hover:rotate-180 transition-transform duration-500"></i>
                                            SWITCH MODE
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-10 text-center text-gray-400 italic">
                                    <i class="fas fa-users-slash text-4xl mb-3 block"></i>
                                    Belum ada data tamu.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <footer class="mt-6 text-center">
                <p class="text-xs text-gray-500 italic">Web by 5NYeni &copy; 2026</p>
            </footer>

        </main>
    </div>

</body>
</html>