<!DOCTYPE html>
<html lang="id">
@php
    use Carbon\Carbon;
    Carbon::setLocale('id');
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Check-In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
        #signature {
            border: 1px solid #ccc;
            width: 200px;
            height: 150px;
            cursor: crosshair;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 0 auto;
        }
        .container {
            max-width: 500px;
        }
        .form-label,
        .form-control {
            display: inline-block;
            width: 48%;
        }
        .form-label {
            width: 25%;
        }
        .form-control {
            margin-bottom: 15px;
            width: 70%;
            background-color: #f0f8ff;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .form-control:focus {
            background-color: #f0f8ff;
            border-color: #091F5B;
            box-shadow: none;
        }
        .text-center {
            text-align: center;
        }
        .judul {
            color: #091F5B;
            font-weight: 800;
            font-size: 20px;
        }
        .small-btn {
            width: 150px;
            display: block;
            margin: 0 auto;
            background-color: #091F5B;
            border-radius: 50px;
            border: none;
            color: #fff;
            font-weight: 800;
        }
        .modal-dialog {
            max-width: 600px;
        }
        .modal-footer {
            justify-content: center;
        }
        .btn-center {
            display: block;
            margin: 0 auto;
            background-color: #091F5B;
            border-radius: 50px;
            width: 150px;
            font-weight: 800;
        }
        .bold-text {
            font-weight: 600;
        }
        .detail {
            width: 350px;
            font-size: 16px;
        }

    .btn-center {
        display: block;
        margin: 0 auto;
        border-radius: 50px;
        width: 150px;
        font-weight: 800;
        transition: background-color 0.3s ease; /* Efek transisi warna */
    }

    /* GANTI .btn-setuju DENGAN DUA CLASS DI BAWAH INI */
    
    /* 1. Style untuk tombol saat NONAKTIF */
    .btn-inactive {
        color: #fff;
        background-color: #7A829F; /* Warna abu-biru (sebelum diisi) */
        border-color: #7A829F;
    }

    /* 2. Style untuk tombol saat AKTIF */
    .btn-active {
        color: #fff;
        background-color: #091F5B; /* Warna biru tua/cerah (setelah diisi) */
        border-color: #091F5B;
    }

    .btn-active:hover {
        color: #fff;
        background-color: #0d2d82; /* Warna hover untuk tombol aktif */
        border-color: #0d2d82;
    }
    </style>
</head>

<body>
    @include('layouts.app')

    <div class="container py-4">
        <div class="card p-4 mb-4">
            <h4 class="text-center mb-4" style="font-weight: 800; font-size: 22px;">Isi Data Check-In</h4>

            <form id="checkinForm" action="{{ route('peminjaman.show', ['kode_booking' => $booking['booking_code']]) }}" method="POST">
                @csrf
                <input type="hidden" name="kode_booking" value="{{ $booking['booking_code'] }}">
                <input type="hidden" id="signatureData" name="signatureData"> <div>
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div>
                    <label for="phone" class="form-label">No Telepon</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required maxlength="15"
                        oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                        onblur="convertPhone(this)"
                        title="Nomor telepon harus terdiri dari angka">
                </div>
                
                <script>
                    function convertPhone(input) {
                        if (input.value.startsWith('0')) {
                            input.value = '62' + input.value.slice(1);
                        }
                    }
                </script>


                <h5 class="mb-4 mt-4 text-center" style="font-weight: 800; font-size: 18px;">Detail Booking</h5>
                <div class="card detail p-3 bg-white mb-3">
                    <h4 class="text-center judul">{{ $booking['name'] }}</h4>
                    <p class="text-center">{{ $roomDetails['room_name'] }},  {{ $roomDetails['room_floor'] }}</p>
                    <p class="text-center">{{ $dayOfWeek }},{{ $formattedDate }}</p>
                    <p class="text-center">
                        <strong>{{ $startTime }} - {{ $endTime }}</strong>
                    </p>
                    <p class="text-center"><strong>{{ $booking['pic_name'] }}</strong></p>
                </div>

                <button type="button" class="btn btn-primary small-btn" data-bs-toggle="modal" data-bs-target="#termsModal">Lanjutkan</button>
            </form>
        </div>

        <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header justify-content-center">
                        <h5 class="modal-title" style="color: #091F5B; font-weight:800;">Syarat dan Ketentuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="list-unstyled mb-1">    
                            <ol style="text-align: justify; text-justify: inter-word; margin: .25rem !important;">
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
                        <div class="d-flex flex-column align-items-start">
                            <canvas id="signature" class="mb-3"></canvas>
                            <button type="button" class="btn btn-secondary" id="clearSignature">Clear</button>
                            <p style="font-style: italic; font-size: 12px;">*Silahkan menandatangani untuk menyetujui syarat dan ketentuan.</p>
                            <p id="signatureAlert" style="color: red; font-weight: bold; font-size: 12px; margin-top: -10px;">*Tanda tangan wajib diisi.</p>
                            </div>
                    </div>
                    <div class="modal-footer">
                    <button type="submit" class="btn btn-inactive btn-center" form="checkinForm" id="agreeButton" disabled>Setuju</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
    const canvas = document.getElementById('signature');
    const ctx = canvas.getContext('2d');
    const clearButton = document.getElementById('clearSignature');
    const checkinForm = document.getElementById('checkinForm');
    const signatureDataInput = document.getElementById('signatureData');
    const agreeButton = document.getElementById('agreeButton');
    const signatureAlert = document.getElementById('signatureAlert');

    function isCanvasEmpty() {
        const blank = document.createElement('canvas');
        blank.width = canvas.width;
        blank.height = canvas.height;
        return canvas.toDataURL() === blank.toDataURL();
    }

    // --- FUNGSI INI KITA MODIFIKASI ---
    function updateAgreeButtonState() {
        if (isCanvasEmpty()) {
            agreeButton.disabled = true;
            signatureAlert.style.display = 'block';
            // Atur class untuk warna nonaktif
            agreeButton.classList.add('btn-inactive');
            agreeButton.classList.remove('btn-active');
        } else {
            agreeButton.disabled = false;
            signatureAlert.style.display = 'none';
            // Atur class untuk warna aktif
            agreeButton.classList.add('btn-active');
            agreeButton.classList.remove('btn-inactive');
        }
    }

    const startDrawing = (e) => {
        isDrawing = true;
        ctx.beginPath();
        const rect = canvas.getBoundingClientRect();
        const x = (e.clientX || e.touches[0].clientX) - rect.left;
        const y = (e.clientY || e.touches[0].clientY) - rect.top;
        ctx.moveTo(x, y);
    };

    const draw = (e) => {
        if (!isDrawing) return;
        e.preventDefault();
        const rect = canvas.getBoundingClientRect();
        const x = (e.clientX || e.touches[0].clientX) - rect.left;
        const y = (e.clientY || e.touches[0].clientY) - rect.top;
        ctx.lineTo(x, y);
        ctx.stroke();
    };

    const stopDrawing = () => {
        if (!isDrawing) return;
        isDrawing = false;
        ctx.closePath();
        updateAgreeButtonState();
    };

    const clearCanvas = () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        updateAgreeButtonState();
    };

    // --- (Sisa script tidak berubah) ---
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);

    canvas.addEventListener('touchstart', startDrawing);
    canvas.addEventListener('touchmove', draw);
    canvas.addEventListener('touchend', stopDrawing);

    clearButton.addEventListener('click', clearCanvas);

    checkinForm.addEventListener('submit', function(e) {
        if (isCanvasEmpty()) {
            e.preventDefault();
        } else {
            signatureDataInput.value = canvas.toDataURL('image/png');
        }
    });

    updateAgreeButtonState();
});
    </script>
</body>

</html>