<x-app-layout>
    <div class="flex min-h-screen bg-[#D9D9D9]">
        <aside class="w-64 bg-[#800000] text-white p-6">
            <p class="text-xs italic italic">Web by 5NYeni</p>
        </aside>

        <main class="flex-1 p-8">
            <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-2xl overflow-hidden border-t-[15px] border-[#800000]">
                <div class="p-8">
                    <div class="flex justify-between items-start mb-10">
                        <h2 class="text-3xl font-black text-gray-800 italic uppercase">Hotel Invoice</h2>
                        <img src="{{ asset('images/logo.png') }}" class="w-20"> </div>

                    <form action="" id="checkoutForm" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-10 mb-8 text-sm">
                            <div class="space-y-4">
                                <div class="flex border-b border-dotted border-gray-400 pb-1">
                                    <span class="w-32 font-bold">Pilih Tamu:</span>
                                    <select id="resSelect" class="flex-1 border-none focus:ring-0 p-0 text-[#800000] font-bold">
                                        <option value="">-- Cari Nama/Kamar --</option>
                                        @foreach($reservations as $r)
                                            <option value="{{ $r->id }}" data-room="{{ $r->room->room_number }}" data-in="{{ $r->arrival_date }}" data-out="{{ $r->departure_date }}">
                                                {{ $r->room->room_number }} - {{ $r->guest_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <p><strong>Room No:</strong> <span id="displayRoom" class="ml-4">-</span></p>
                                <p><strong>Check-in:</strong> <span id="displayIn" class="ml-4">-</span></p>
                                <p><strong>Check-out:</strong> <span id="displayOut" class="ml-4">-</span></p>
                            </div>
                        </div>

                        <table class="w-full border border-gray-800 mb-6 text-center text-sm">
                            <thead class="bg-gray-100 border-b border-gray-800 font-black">
                                <tr>
                                    <th class="p-2 border-r border-gray-800">No</th>
                                    <th class="p-2 border-r border-gray-800">Quantity / Notes</th>
                                    <th class="p-2 border-r border-gray-800">Unit Price (Charge)</th>
                                    <th class="p-2">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="p-4 border-r border-gray-800">1</td>
                                    <td class="p-4 border-r border-gray-800">
                                        <input type="text" name="notes" placeholder="Contoh: Remote TV rusak" class="w-full border-none italic text-xs">
                                    </td>
                                    <td class="p-4 border-r border-gray-800">
                                        <input type="number" name="additional_charges" id="addCharge" value="0" class="w-full border-none text-center font-bold text-[#800000]">
                                    </td>
                                    <td class="p-4 font-bold">Rp <span id="displayTotal">0</span></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="flex justify-between items-end mt-12">
                            <div class="text-center italic text-xs">
                                <p>Guest Signature</p>
                                <div class="mt-16 border-t border-gray-800 w-48 mx-auto"></div>
                            </div>
                            <button type="submit" class="bg-[#800000] text-white font-black px-12 py-4 rounded-xl shadow-lg hover:scale-105 transition">
                                PROSES CHECK-OUT
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Script sederhana buat update tampilan saat tamu dipilih
        document.getElementById('resSelect').addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            document.getElementById('displayRoom').innerText = opt.dataset.room || '-';
            document.getElementById('displayIn').innerText = opt.dataset.in || '-';
            document.getElementById('displayOut').innerText = opt.dataset.out || '-';
            document.getElementById('checkoutForm').action = "/check-out/" + this.value;
        });
    </script>
</x-app-layout>