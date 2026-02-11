<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <title>Dashboard - Hotel SIG</title>
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    @php
    $user = Auth::user();
    // Jika $stats tidak dikirim dari controller, gunakan data kosong agar tidak error
    $stats = $stats ?? [
        'Suite' => 15,
        'Standard' => 10,
        'Deluxe' => 5
    ];
@endphp

   <x-header></x-header>
   <x-sidebar></x-sidebar>

        <main class="flex-1 p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                @foreach($stats as $nama => $data)
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 text-center flex flex-col items-center justify-center">
                    <h3 class="text-red-900 font-bold text-lg">Kamar {{ $nama }}</h3>
                  <p class="text-6xl font-black my-4 text-grax`y-800">{{ $data }}</p>
                    <div class="flex items-center space-x-2 bg-gray-50 px-4 py-1 rounded-full">
                        <span class="text-gray-600 text-xs font-semibold uppercase">kamar sisa</span>
                    <span class="bg-red-800 text-white text-xs px-2 py-0.5 rounded-full font-bold">
                    {{ $data }}
                </span>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="bg-white rounded-2xl shadow-md p-6">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Room List</h2>
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                            <th class="p-4 rounded-l-lg">Room No</th>
                            <th class="p-4">Type</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 rounded-r-lg">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        @foreach($rooms as $room)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-bold">{{ $room['no'] }}</td>
                            <td class="p-4 text-gray-600">{{ $room['type'] }}</td>
                            <td class="p-4 italic text-gray-400">{{ $room['status'] }}</td>
                            <td class="p-4">
                                <span class="px-4 py-1 rounded-lg text-xs font-bold text-white {{ $room['color'] }} shadow-sm">
                                    {{ strtoupper($room['status']) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>    
                </table>
            </div>
        </main>
    </div>

    <script>
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').innerText = now.toLocaleString('id-ID', { 
                day: 'numeric', month: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' 
            });
        }
        setInterval(updateTime, 1000); updateTime();
    </script>
</body>
</html>