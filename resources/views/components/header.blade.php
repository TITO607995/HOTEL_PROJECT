 <header class="bg-[#F0E7E7] px-8 py-4 flex justify-between items-center shadow-sm border-b border-gray-300">
        <div class="flex items-center space-x-4">
            <div class="flex space-x-2">
                <img src="{{ asset('image/logoph.png') }}" alt="Logo" class="h-12 w-12">
                <img src="{{ asset('image/smksig.png') }}" alt="Logo" class="h-12 w-12">
            </div>
            <h1 class="text-xl font-semibold text-gray-700">Halo, {{ Auth::user()->name }}</h1>
        </div>
        <div class="text-sm font-medium text-gray-500" id="current-time"></div>
    </header>
