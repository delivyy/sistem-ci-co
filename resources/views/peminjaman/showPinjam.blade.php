<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>

    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
        .btn[disabled] {
            pointer-events: none;
            opacity: 0.6;
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
        }
        .btn-primaryp{
            padding-top: 12px;
        }
        .modal-header,
        .modal-footer {
            border: none;
        }
    </style>
</head>

<body>
    @include('layouts.app') <form method="POST" action="{{ route('booking.completeCheckIn', ['kode_booking' => $booking['booking_code']]) }}">
        @csrf

        <input type="hidden" name="name" value="{{ $checkInData['name'] ?? old('name') }}">
        <input type="hidden" name="phone" value="{{ $checkInData['phone'] ?? old('phone') }}">
        <input type="hidden" name="signatureData" value="{{ $checkInData['signatureData'] ?? old('signatureData') }}">


        <div class="main-card border">
            <h4 class="text-center mb-4" style="color: #091F5B;">Formulir Peminjaman Barang</h4>

            <div class="info-card border mb-2 p-3">
                <div class="d-flex align-items-center">
                    <p class="mb-0">Nama Event</p>
                    <p class="mb-0" style="color: #091F5B; margin-left: 20px;"><strong>{{ $booking['name'] }}</strong></p>
                </div>
            </div>

            <div class="info-card border p-3 mb-3">
                <div class="row">
                    <div class="col-md-6 d-flex align-items-center">
                        <p class="mb-0">Ruangan</p>
                        <p class="mb-0" style="color: #091F5B; margin-left: 40px;">
                            {{ $ruangan['name'] ?? 'Tidak Tersedia' }}
                        </p>
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <p class="mb-0">PIC</p>
                        <p class="mb-0" style="color: #091F5B; margin-left: 25px;">{{ $booking['pic_name'] }}</p>
                    </div>
                    <div class="col-md-6 d-flex align-items-center mt-2">
                        <p class="mb-0">Tanggal</p>
                        <p class="mb-0 text-end" style="color: #091F5B; margin-left: 50px;">
                            {{ $booking['booking_items'][0]['booking_date'] }}
                        </p>
                    </div>
                    <div class="col-md-6 d-flex align-items-center mt-2">
                        <p class="mb-0">Jam</p>
                       <p class="mb-0 text-end" style="color: #091F5B; margin-left: 20px;">{{ $startTime }} -
    {{ $endTime }}
</p>
                    </div>
                </div>
            </div>
            <h5 class="section-title mt-4" style="color: #091F5B">List Barang yang Dipinjam</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Item</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Data dari database --}}
                    @forelse ($database_items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_item }}</td>
                            <td>{{ $item->jumlah }}</td>
                        </tr>
                    @empty
                        <tr>
                        </tr>
                    @endforelse

                    {{-- Data dari API --}}
                    @if (!$tools->isEmpty())
                        @php
                            $counter = $database_items->count() + 1; // Lanjutkan nomor dari data database
                        @endphp
                        @foreach ($tools as $tool)
                            <tr>
                                <td>{{ $counter++ }}</td>
                                <td>{{ $tool }}</td>
                                <td>Tidak ada jumlah spesifik</td>
                            </tr>
                        @endforeach
                    @else
                        @if ($database_items->isEmpty())
                            {{-- Tampilkan baris kosong hanya jika database juga kosong --}}
                            <tr>
                                <td colspan="3" class="text-center">Tidak ada peminjaman barang.</td>
                            </tr>
                        @endif
                    @endif
                </tbody>
            </table>

            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="agree" name="agree" required>
                <label class="form-check-label" for="agree" style="color: #091F5B; font-weight:500;">
                    Syarat dan Ketentuan
                </label>
            </div>

            <div class="signature-wrapper d-flex justify-content-between mt-2">
                <div class="signature-group mt-4 text-center">
                    <p class="signature-title">Mengetahui,<br> Marketing</p>
                    <p><img src="{{ asset('images/marketing_ttd.png') }}" alt="Tanda Tangan"
                            style="width: 80px; height: 80px;"></p>
                    <p>{{ $booking['history'][0]['pic_marketing'] }}</p>
                </div>
                <div class="d-flex" style="flex-basis: 35%; justify-content: space-between;">
                    <div class="signature-group mt-4 text-center">
                        <p class="signature-title">Mengetahui,<br> Peminjam</p>
                        <p>
                           @if (!empty($checkInData['signatureData']))
                                <img src="{{ $checkInData['signatureData'] }}" alt="Tanda Tangan Peminjam"
                                     style="width: 180px; height: 80px; padding-left: 45px; border: 1px solid #eee;">
                            @else
                                <span class="text-muted">Tanda tangan belum ada.</span>
                            @endif
                        </p>
                        <p>{{ $checkInData['name'] ?? 'Nama belum tersedia' }}</p>

                    </div>
                    <div class="signature-group mt-4 text-center">
                        @if(auth()->user() && auth()->user()->role === 'fo')
                            <p class="signature-title">Mengetahui,<br> FO</p>
                            <p><img src="{{ asset('images/fo_ttd.png') }}" alt="Tanda Tangan"
                                    style="width: 80px; height: 80px;"></p>
                            <p>{{ auth()->user()->name }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>


        <div class="text-center mt-4 mb-4">
            <button type="submit" class="btn btn-primary btn-primaryp" id="agreeButton" disabled>Setuju</button>
        </div>

    </form> <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 500px;">
            <div class="modal-content">
                <div class="modal-header text-center"
                    style="border-bottom: none; display: flex; justify-content: center; width: 100%;">
                    <h5 class="modal-title" id="termsModalLabel" style="color: #091F5B; text-align: center; margin: 0;">
                        Syarat dan Ketentuan</h5>
                </div>
                <div class="modal-body ">
                    <ul class="list-unstyled mb-1">
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
                    </ul>
                </div>
                <div class="modal-footer"
                    style="border-top: none; display: flex; justify-content: center; width: 100%;">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Setuju</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>

    <script>
        const agreeCheckbox = document.getElementById('agree');
        const agreeButton = document.getElementById('agreeButton');
        const termsModal = new bootstrap.Modal(document.getElementById('termsModal'));

        agreeCheckbox.addEventListener('change', function () {
            if (this.checked) {
                termsModal.show();
            } else {
                agreeButton.setAttribute('disabled', 'true');
            }
        });

        document.querySelector('#termsModal .btn-primary').addEventListener('click', function () {
            termsModal.hide();
            if (agreeCheckbox.checked) {
                agreeButton.removeAttribute('disabled');
            }
        });

        document.getElementById('termsModal').addEventListener('hidden.bs.modal', function () {
            if (!agreeCheckbox.checked) {
                agreeCheckbox.checked = false; // Batalkan centang jika modal ditutup tanpa setuju
                agreeButton.setAttribute('disabled', 'true');
            }
        });
    </script>
</body>
</html>