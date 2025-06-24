<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Marketing Edit</title>

    <!-- Fonts and Styles -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }

        .main-card {
            border-radius: 10px;
            background-color: #fff;
            border-color: #091F5B;
            padding: 60px;
            width: 650px;
            margin: auto;
            margin-top: 30px;
        }

        .section-title {
            font-size: 16px;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .info-card {
            border-radius: 15px;
            border-color: #091F5B;
            font-size: 14px;
        }

        .table-custom {
            border-radius: 10px;
            overflow: hidden;
            font-size: 12px;
        }

        .table th {
            background-color: #e3f2fd;
            text-align: center;
        }

        .table th,
        .table td {
            border: none;
        }

        .form-check-input {
            border-color: #091F5B;
        }

        .form-check-input:checked {
            background-color: #091F5B;
            border-color: #091F5B;
        }

        .signature-wrapper {
            display: flex;
            justify-content: space-between;
            padding-top: 15px;
            margin-top: 25px;
            font-size: 14px;
        }

        .signature-group {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .signature-img {
            width: 100px;
            height: auto;
        }

        .signature-title {
            margin-bottom: 10px;
        }

        .signature-group p {
            margin: 5px 0;
        }

        .btn-primary {
            background-color: #091F5B;
            border: none;
            width: 200px;
            height: 50px;
            border-radius: 30px;
            font-weight: 600;
            padding-top: 12px;
        }

        .modal-header,
        .modal-footer {
            border: none;
            /* Menghilangkan border */
        }
    </style>
</head>

<body>
    @include('layouts.app') <!-- Include navbar -->

    <div class="main-card border">
        <h4 class="text-center mb-4" style="color: #091F5B;">Formulir Peminjaman Barang</h4>

        <!-- Info Card untuk Nama Event -->
        <div class="info-card border mb-2 p-3">
            <div class="d-flex align-items-center">
                <p class="mb-0">Nama Event</p>
                <p class="mb-0" style="color: #091F5B; margin-left: 20px;"><strong>{{ $nama_event }}</strong></p>
            </div>
        </div>

        <!-- Card untuk Ruangan, PIC, Tanggal, dan Jam -->
        <div class="info-card border p-3 mb-3">
            <div class="row">
                <div class="col-md-6 d-flex align-items-center">
                    <p class="mb-0">Ruangan</p>
                    <p class="mb-0" style="color: #091F5B; margin-left: 40px;">{{ $ruangan }}</p>
                </div>
                <div class="col-md-6 d-flex align-items-center">
                    <p class="mb-0">PIC</p>
                    <p class="mb-0" style="color: #091F5B; margin-left: 25px;">{{ $pic }}</p>
                </div>
                <div class="col-md-6 d-flex align-items-center mt-2">
                    <p class="mb-0">Tanggal</p>
                    <p class="mb-0 text-end" style="color: #091F5B; margin-left: 50px;">{{ $tanggal }}</p>
                </div>
                <div class="col-md-6 d-flex align-items-center mt-2">
                    <p class="mb-0">Jam</p>
                    <p class="mb-0 text-end" style="color: #091F5B; margin-left: 20px;">{{ $jam }}</p>
                </div>
                
            </div>
        </div>

        <!-- Form List Barang yang Dipinjam -->
        <h5 class="section-title mt-4" style="color: #091F5B">List Barang yang Dipinjam</h5>
        <form method="POST" action="{{ route('peminjaman.store') }}">
            @csrf
            <table class="table table-striped table-custom text-center mb-4">
            <input type="hidden" name="kode_booking" value="{{ $kode_booking }}">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Item</th>
                        <th>Jumlah</th>
                        <th>Lokasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="barangList">
                    @foreach ($peminjamans as $index => $peminjaman)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><input type="text" name="items[{{ $index }}][nama_item]" value="{{ $peminjaman->nama_item }}" class="form-control"></td>
                        <td><input type="number" name="items[{{ $index }}][jumlah]" value="{{ $peminjaman->jumlah }}" class="form-control"></td>
                        <td><input type="text" name="items[{{ $index }}][lokasi]" value="{{ $peminjaman->lokasi }}" class="form-control"></td>
                        <td><button type="button" class="btn btn-danger removeItem" onclick="removeItem(this)">Hapus</button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <!-- Tambah Barang -->
            <button type="button" class="btn btn-success" onclick="addItem()">Tambah Barang</button>

            <div class="text-center mt-4 mb-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>

        <!-- Checkbox Syarat dan Ketentuan -->
        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="agree" name="agree" required>
            <label class="form-check-label" for="agree" style="color: #091F5B; font-weight:500;">
                Syarat dan Ketentuan
            </label>
        </div>

        <!-- Bagian Tanda Tangan -->
        <div class="signature-wrapper d-flex justify-content-between mt-2">
            <div class="signature-group mt-4 text-center">
                <p class="signature-title">Mengetahui,<br> Marketing</p>
                <p><img src="{{ asset('images/marketing_ttd.png') }}" alt="Tanda Tangan"
                        style="width: 80px; height: 80px;"></p>
                <p>{{ $peminjaman->marketing }}</p>
            </div>
            <div class="d-flex" style="flex-basis: 35%; justify-content: space-between;">
                <div class="signature-group mt-4 text-center">
                    <p class="signature-title">Mengetahui,<br> Peminjam</p>
                    <p>
                        @if (isset($signature) && $signature != 'Tidak Tersedia')
                            <img src="{{ $signature }}" alt="Tanda Tangan"
                                style="width: 180px; height: 80px; padding-left: 45px;">
                        @else
                            <p>Tanda tangan tidak tersedia.</p>
                        @endif
                    </p>
                    <p>{{ $name }}</p>
                </div>
                <div class="signature-group mt-4 text-center">
                    <p class="signature-title">Mengetahui,<br> FO</p>
                    <p><img src="{{ asset('images/fo_ttd.png') }}" alt="Tanda Tangan"
                            style=" width: 80px; height: 80px;"></p>
                    <p>{{ $peminjaman->FO }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Setuju -->
    <div class="text-center mt-4 mb-4">
        <a href="{{ route('bookings.index') }}" class="btn btn-primary">Setuju</a>
    </div>

    <!-- Modal Syarat dan Ketentuan -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 500px;"> <!-- Mengatur ukuran modal menjadi 600px -->
            <div class="modal-content">
                <div class="modal-header text-center"
                    style="border-bottom: none; display: flex; justify-content: center; width: 100%;">
                    <h5 class="modal-title" id="termsModalLabel" style="color: #091F5B; text-align: center; margin: 0;">
                        Syarat dan Ketentuan</h5>
                </div>
                <div class="modal-body ">
                <ol>
                        <li>Penyelenggara bisa menggunakan Ruangan di <em style="color:blue;">Gedung MCC</em> dan melakukan publikasi <strong>setelah menerima Surat Konfirmasi</strong> dari <em style="color:blue;">Manajemen Pengelola Gedung MCC</em>.</li>
                        <li>Penggunaan Ruangan <strong>maksimal</strong> pada <strong>pukul 21.00 WIB</strong>. Lebih dari jam tersebut <em style="color:blue;">Manajemen Pengelola Gedung MCC</em> berhak untuk menghentikan acara.</li>
                        <li>Acara yang diselenggarakan <strong>tidak boleh mengandung</strong> atau <strong>membawa</strong> issue <strong>SARA, politik dan kekerasan</strong>.</li>
                        <li>Penyelenggara <strong>wajib</strong> memberikan data atau informasi yang sebenar - benarnya mengenai kegiatan yang akan dilaksanakan.</li>
                        <li>Setiap penyelenggara <strong>wajib melakukan proses Check-in dan Check-out</strong> di meja Customer Service (Lantai 2). Pada saat proses Check-in setiap penyelenggara <strong>wajib</strong> memberikan kartu identitas / ID-Card dan akan diserahkan kembali saat proses Check-out.</li>
                        <li>Setiap penyelenggara <strong>wajib membuat form data peserta/pengunjung yang hadir</strong> (nama, telp, alamat & email) dan diserahkan ke Customer Service pada saat proses Check-out.</li>
                        <li><strong>Menjaga fasilitas, sarana, dan prasarana</strong> yang tersedia dalam <em style="color:blue;">Ruangan</em> / <em style="color:blue;">Gedung MCC</em>.</li>
                        <li>Penggunaan <em style="color:blue;">Ruangan Empowerment</em> / <em style="color:blue;">Pemberdayaan</em> di <em style="color:blue;">Gedung MCC</em> <strong>tidak dipungut biaya / gratis</strong>.</li>
                        <li><strong>Melengkapi sendiri kebutuhan yang tidak tersedia/kurang</strong> seperti kabel roll, alat tulis, kursi, meja, level stage, dekorasi, dll.</li>
                        <li><strong>Peminjaman / Membawa peralatan yang ada di ruangan</strong> harus atas seijin <em style="color:blue;">Manajemen Pengelola Gedung MCC</em>.</li>
                        <li><strong>Menjaga ketertiban, kebersihan dan keamanan </strong>penyelenggaraan acara.</li>
                        <li><strong><em style="color:red;">Dilarang</em> </strong> menempel, memaku benda apapun pada <em style="color:blue;">dinding Ruangan</em> / <em style="color:blue;">Gedung MCC</em>.</li>
                        <li><strong><em style="color:red;">Dilarang</em> </strong> memasang atribut Partai Politik, atau Ormas Keagamaan yang berbau politik di <em style="color:blue;">Ruangan</em> / <em style="color:blue;">Gedung MCC</em>.</li>
                        <li><strong>Loading in</strong> barang dilakukan pada <strong>H-1 Jam 22.00 - 06.00 wib</strong>.</li>
                        <li><strong>Loading out</strong> barang dilakukan <strong>di hari yang sama</strong> setelah rundown acara selesai.</li>
                        <li>Jika proses <strong>loading out melebihi batas waktu</strong> yg ditentukan, <em style="color:blue;">Manajemen Pengelola Gedung MCC</em> berhak memindahkan property dan tidak bertanggung jawab atas kerusakan property.</li>
                        <li>Ruangan yang sudah selesai digunakan serta peralatannya <strong>wajib</strong> dikembalikan pada posisi semula dan memberikan konfirmasi ke Customer Service pada saat Check-out.</li>
                        <li>Mengumpulkan sampah pada titik/tempat sampah yang tersedia. Petugas Kebersihan <em style="color:blue;">Gedung MCC</em> akan melakukan pembuangan sampah yang telah terkumpul.</li>
                        <li><strong>Pembatalan/Cancelation</strong> dilakukan <strong>maksimal</strong> pada <strong>H-2</strong>.</li>
                        <li><strong>Apabila ditemukan pelanggaran</strong> pada <strong>poin-poin Syarat dan Ketentuan ini</strong>, maka <em style="color:blue;">Manajemen Pengelola Gedung MCC</em> berhak untuk menjatuhkan sanksi kepada Penyelenggara.</li>
                    </ol>
                </div>
                <div class="modal-footer"
                    style="border-top: none; display: flex; justify-content: center; width: 100%;">
                    <!-- Menggunakan flexbox untuk memusatkan tombol -->
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Setuju</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addItem() {
            let table = document.getElementById('barangList');
            let rowCount = table.rows.length;
            let row = table.insertRow(rowCount);

            row.innerHTML = `
                <td>${rowCount + 1}</td>
                <td><input type="text" name="items[${rowCount}][nama_item]" class="form-control"></td>
                <td><input type="number" name="items[${rowCount}][jumlah]" class="form-control"></td>
                <td><input type="text" name="items[${rowCount}][lokasi]" class="form-control"></td>
                <td><button type="button" class="btn btn-danger removeItem" onclick="removeItem(this)">Hapus</button></td>
            `;
        }

        function removeItem(button) {
            let row = button.parentElement.parentElement;
            row.remove();
        }
    </script>
</body>

</html>
