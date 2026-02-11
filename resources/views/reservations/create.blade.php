<form action="{{ route('reservations.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <label>Lead Guest:</label>
            <input type="text" name="guest_name" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label>Pilih Kamar:</label>
            <select name="room_id" class="form-control">
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}">No. {{ $room->room_number }} - {{ $room->type }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <button type="submit" class="btn btn-danger mt-3">SUBMIT RESERVASI</button>
</form>