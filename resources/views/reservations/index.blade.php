<x-app-layout>
    <div class="flex min-h-screen bg-[#D9D9D9]">
        <aside class="w-64 bg-[#800000] text-white flex flex-col justify-between p-6">
            
            <p class="text-xs italic">Web by 5NYeni</p>
        </aside>

        <main class="flex-1">
            <header class="bg-white/50 p-6 flex justify-between items-center">
                <img src="{{ asset('images/avatar.png') }}" class="w-12 h-12 rounded-full border-2 border-white shadow-sm">
            </header>

            <div class="p-8">
                <form action="{{ route('reservations.store') }}" method="POST" class="flex gap-8">
                    @csrf
                    
                    <div class="flex-1 space-y-6">
                        <div class="bg-white p-6 rounded-2xl shadow-xl flex gap-4">
                            <div class="border-r-2 pr-4 flex items-center">
                                <span class="text-[#800000] font-black text-xl border-2 border-[#800000] px-4 py-2 rounded-xl">Check in</span>
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-gray-400 mb-1">Pilih Tanggal</label>
                                <input type="date" name="check_in" class="w-full border-none focus:ring-0 text-lg font-bold" required>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-xl flex gap-4">
                            <div class="border-r-2 pr-4 flex items-center">
                                <span class="text-[#800000] font-black text-xl border-2 border-[#800000] px-4 py-2 rounded-xl">Check out</span>
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-gray-400 mb-1">Pilih Tanggal</label>
                                <input type="date" name="check_out" class="w-full border-none focus:ring-0 text-lg font-bold" required>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-xl">
                            <label class="block text-[#800000] font-black mb-2">Pilih Kamar Tersedia:</label>
                            <select name="room_id" class="w-full border-gray-300 rounded-xl focus:ring-[#800000]" required>
                                <option value="">-- Cari Kamar --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->room_number }} - {{ $room->type }} (Rp{{ number_format($room->price, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="w-[450px] bg-white p-8 rounded-2xl shadow-2xl border border-gray-100 h-fit">
                        <h2 class="text-2xl font-black mb-6 text-gray-800 italic">Detail Tamu</h2>
                        
                        <div class="space-y-6">
                            <div class="flex items-center gap-2">
                                <label class="w-48 bg-[#D9D9D9] p-2 rounded text-xs font-bold text-[#800000]">Nama Tamu Utama (Lead Guest):</label>
                                <input type="text" name="guest_name" class="flex-1 border-b-2 border-dotted border-gray-400 focus:border-[#800000] outline-none px-2" placeholder="..........." required>
                            </div>

                            <div class="flex items-center gap-2">
                                <label class="w-48 bg-[#D9D9D9] p-2 rounded text-xs font-bold text-[#800000]">Jumlah Tamu:</label>
                                <input type="number" name="num_guests" class="flex-1 border-b-2 border-dotted border-gray-400 focus:border-[#800000] outline-none px-2" placeholder="..........." required>
                            </div>

                            <div class="flex items-center gap-2">
                                <label class="w-48 bg-[#D9D9D9] p-2 rounded text-xs font-bold text-[#800000]">Alamat Email:</label>
                                <input type="email" name="guest_email" class="flex-1 border-b-2 border-dotted border-gray-400 focus:border-[#800000] outline-none px-2" placeholder="..........." required>
                            </div>

                            <div class="flex items-center gap-2">
                                <label class="w-48 bg-[#D9D9D9] p-2 rounded text-xs font-bold text-[#800000]">Nomor Telepon/HP:</label>
                                <input type="text" name="guest_phone" class="flex-1 border-b-2 border-dotted border-gray-400 focus:border-[#800000] outline-none px-2" placeholder="..........." required>
                            </div>
                        </div>

                        <button type="submit" class="w-full mt-8 bg-[#800000] text-white font-black py-3 rounded-xl shadow-lg hover:bg-red-900 transition active:scale-95 shadow-[#800000]/30">
                            Pesan Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</x-app-layout>