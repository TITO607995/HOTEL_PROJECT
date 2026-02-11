<?php
// Data Simulasi (Nanti dipindah ke Controller Laravel)
$username = "Resepsionis";
$rooms = [
    ['no' => '101', 'type' => 'Deluxe', 'status' => 'Occupied', 'color' => 'bg-red-500'],
    ['no' => '102', 'type' => 'Suite', 'status' => 'Dirty', 'color' => 'bg-yellow-400'],
    ['no' => '103', 'type' => 'Standard', 'status' => 'Available', 'color' => 'bg-green-500'],
    ['no' => '104', 'type' => 'Standard', 'status' => 'In-house', 'color' => 'bg-orange-400'],
    ['no' => '105', 'type' => 'Executive', 'status' => 'Available', 'color' => 'bg-green-500']
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <title>Dashboard - Hotel SIG</title>
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    <header class="bg-[#F0E7E7] px-8 py-4 flex justify-between items-center shadow-sm border-b border-gray-300">
        <div class="flex items-center space-x-4">
            <div class="flex space-x-2">
                <img src="l_perhotelan.png" alt="Logo Perhotelan" class="h-12 w-12">
                <img src="SMKSG.png" alt="Logo SMK" class="h-12 w-12">
            </div>
            <h1 clas="text-xl font-semibold text-gray-700">Halo, <?php echo $username; ?></h1>
        </div>
        <div class="text-sm font-medium text-gray-500" id="current-time"></div>
    </header>

    <div class="flex flex-1">
        <aside class="w-64 bg-[#800000] text-white p-6 flex flex-col shadow-xl">
            <nav class="space-y-2 mt-4 flex-1">
                <a href="index.php" class="flex items-center space-x-3 bg-white/20 p-3 rounded-lg font-bold">
                    <span>📊</span> <span>Dashboard</span>
                </a>
                <a href="reservasi.php" class="flex items-center space-x-3 hover:bg-white/10 p-3 rounded-lg transition">
                    <span>📝</span> <span>Reservasi</span>
                </a>
                <a href="#" class="flex items-center space-x-3 hover:bg-white/10 p-3 rounded-lg transition">
                    <span>🛏️</span> <span>Kamar</span>
                </a>
                <a href="#" class="flex items-center space-x-3 hover:bg-white/10 p-3 rounded-lg transition">
                    <span>👤</span> <span>Tamu</span>
                </a>
                <a href="#" class="flex items-center space-x-3 hover:bg-white/10 p-3 rounded-lg transition">
                    <span>📈</span> <span>Laporan</span>
                </a>
            </nav>
            <div class="text-[10px] text-white/50 text-center mt-auto">Web by 5NYeni</div>
        </aside>

        <main class="flex-1 p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                <?php 
                $stats = [
                    'Standard' => ['total' => 15, 'sisa' => 10],
                    'Suite' => ['total' => 10, 'sisa' => 5],
                    'Deluxe' => ['total' => 5, 'sisa' => 1]
                ];
                foreach($stats as $nama => $data): 
                ?>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 text-center flex flex-col items-center justify-center">
                    <h3 class="text-red-900 font-bold text-lg">Kamar <?php echo $nama; ?></h3>
                    <p class="text-6xl font-black my-4 text-gray-800"><?php echo $data['total']; ?></p>
                    <div class="flex items-center space-x-2 bg-gray-50 px-4 py-1 rounded-full">
                        <span class="text-gray-600 text-xs font-semibold uppercase">kamar sisa</span>
                        <span class="bg-red-800 text-white text-xs px-2 py-0.5 rounded-full font-bold">
                            <?php echo $data['sisa']; ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
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
                        <?php foreach($rooms as $room): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-bold"><?php echo $room['no']; ?></td>
                            <td class="p-4 text-gray-600"><?php echo $room['type']; ?></td>
                            <td class="p-4 italic text-gray-400"><?php echo $room['status']; ?></td>
                            <td class="p-4">
                                <span class="px-4 py-1 rounded-lg text-xs font-bold text-white <?php echo $room['color']; ?> shadow-sm">
                                    <?php echo strtoupper($room['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
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