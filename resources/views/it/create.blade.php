<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .custom-alert {
            margin-bottom: 20px;
        }
        .form-actions {
            display: flex;
            justify-content: space-between; /* Membuat tombol sejajar */
            margin-top: 20px; /* Menambahkan jarak antara form dan tombol */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="https://event.mcc.or.id/assets/images/logo.png" width="250" alt="Event Malang Creative Center">
            </a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Tambah Pengguna</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('it.users.store') }}" method="POST">
                            @csrf

                            @if (session('success'))
                                <div class="alert alert-success custom-alert" id="success-alert">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger custom-alert" id="error-alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="" disabled selected>Pilih Role</option>
                                    <option value="frontoffice" {{ old('role') == 'frontoffice' ? 'selected' : '' }}>Front Office</option>
                                    <option value="marketing" {{ old('role') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                    <option value="produksi" {{ old('role') == 'produksi' ? 'selected' : '' }}>Produksi</option>
                                    <option value="it" {{ old('role') == 'it' ? 'selected' : '' }}>IT</option>
                                    <option value="kabid" {{ old('role') == 'kabid' ? 'selected' : '' }}>Kabid</option>
                                    <option value="kadin" {{ old('role') == 'kadin' ? 'selected' : '' }}>Kadin</option>
                                </select>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="{{ route('it.index') }}" class="btn btn-secondary">Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Menghilangkan alert setelah beberapa detik
        window.onload = function() {
            const successAlert = document.getElementById('success-alert');
            const errorAlert = document.getElementById('error-alert');
            
            // Jika ada alert success atau error, maka hilangkan setelah 3 detik
            if (successAlert) {
                setTimeout(function() {
                    successAlert.style.display = 'none';
                }, 3000);
            }

            if (errorAlert) {
                setTimeout(function() {
                    errorAlert.style.display = 'none';
                }, 5000);  // Menghilangkan setelah 5 detik
            }
        };
    </script>
</body>
</html>
