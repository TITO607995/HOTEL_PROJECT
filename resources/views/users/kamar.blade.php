<?php
// Data Simulasi (Nanti dipindah ke Controller Laravel)
$username = "Galang resepsionis";
$rooms = [
    ['no' => '101', 'type' => 'Standard', 'status' => 'booked', 'color' => 'bg-[#F2A93B]', 'img' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=500'],
    ['no' => '101', 'type' => 'Standard', 'status' => 'occupied', 'color' => 'bg-[#E74C3C]', 'img' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=500'],
    ['no' => '101', 'type' => 'Standard', 'status' => 'available', 'color' => 'bg-[#2ECC71]', 'img' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=500'],
    ['no' => '101', 'type' => 'Standard', 'status' => 'vacan dirty', 'color' => 'bg-[#F1C40F]', 'img' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?w=500'],
    ['no' => '101', 'type' => 'Standard', 'status' => 'booked', 'color' => 'bg-[#F2A93B]', 'img' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=500'],
    ['no' => '101', 'type' => 'Standard', 'status' => 'occupied', 'color' => 'bg-[#E74C3C]', 'img' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=500'],
    ['no' => '101', 'type' => 'Standard', 'status' => 'available', 'color' => 'bg-[#2ECC71]', 'img' => 'https://images.unsplash.com/photo-1595576508898-0ad5c879a061?w=500'],
    ['no' => '101', 'type' => 'Standard', 'status' => 'vacan dirty', 'color' => 'bg-[#F1C40F]', 'img' => 'https://images.unsplash.com/photo-1540518614846-7eded433c457?w=500'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <script src="h  ttps://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <title>Kamar - Hotel SIG</title>
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-[#D9D9D9] min-h-screen flex flex-col">

    <header class="bg-[#F0E7E7] px-8 py-4 flex justify-between items-center border-b border-gray-300">
        <div class="flex items-center space-x-4">
            <div class="flex space-x-2">
                <img src="l_perhotelan.png" alt="Logo 1" class="h-10 w-10">
                <img src="SMKSG.png" alt="Logo 2" class="h-10 w-10">
            </div>
            <h1 class="text-2xl font-bold text-gray-700">Halo, <?php echo $username; ?></h1>
        </div>
    </header>

    <div class="flex flex-1">
        <?php include 'sidebar.php'; ?>

        <main class="flex-1 p-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-10">
                
                <?php foreach($rooms as $room): ?>
                <div class="bg-white rounded-[2rem] overflow-hidden shadow-md flex flex-col h-full">
                    <div class="h-40 w-full overflow-hidden">
                        <img src="<?= $room['img']; ?>" class="w-full h-full object-cover" alt="Kamar">
                    </div>

                    <div class="p-5 flex flex-col flex-grow">
                        <div class="mb-4">
                            <h3 class="text-lg font-extrabold text-gray-800 leading-tight">ROOM <?= $room['no']; ?></h3>
                            <p class="text-xs text-gray-500 font-semibold uppercase">type : <?= $room['type']; ?></p>
                        </div>

                        <div class="mt-auto">
                            <span class="inline-block px-5 py-1.5 rounded-xl text-[10px] font-bold text-white uppercase tracking-wider <?= $room['color']; ?>">
                                <?= $room['status']; ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>
        </main>
    </div>

</body>
</html>