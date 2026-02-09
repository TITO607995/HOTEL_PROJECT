<!DOCTYPE html>
<html>
<head>
    <title>Tambah Role</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">
    <div class="container">
        <h2>Tambah Role Baru</h2>

        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label>Nama Role:</label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: Superadmin" required>
            </div>

            <hr>
            <label>Pilih Akses Menu:</label>
            <div class="row">
                @foreach($all_menus as $menu)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input type="checkbox" name="menu_ids[]" value="{{ $menu->id }}" class="form-check-input">
                            <label class="form-check-label">{{ $menu->name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary mt-3">Simpan Role</button>
        </form>
    </div>
</body>
</html>