<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketing</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }

        .modal-content {
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin: 10px;
        }

        .modal-header {
            border-bottom: none;
        }

        .table th,
        .table td {
            vertical-align: middle;
            text-align: center;
        }

        .table-light {
            background-color: #e9ecef;
        }

        .table-bordered {
            border-color: #dee2e6;
        }

        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }

        .btn-close {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 20px;
        }

        .main-card {
            border-radius: 10px;
            background-color: #fff;
            border-color: #091F5B;
            padding: 65px;
            margin: auto;
        }

        .info-card {
            border-radius: 15px;
            border-color: #091F5B;
            font-size: 14px;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        @media (max-width: 768px) {
            .modal-dialog {
                max-width: 100%;
                margin: 0;
            }

            .modal-content {
                border-radius: 8px;
            }

            .main-card {
                padding: 30px;
            }

            .table th,
            .table td {
                font-size: 12px;
            }

            .col-md-3,
            .col-md-2,
            .col-md-1 {
                font-size: 12px;
            }

            .d-flex {
                flex-wrap: wrap;
            }

            .d-flex>.col-md-3,
            .d-flex>.col-md-2 {
                flex: 1 1 100%;
                margin-bottom: 10px;
            }

            .modal-body {
                padding: 0px;
            }

            .pagination .page-item .page-link {
                color: #091F5B;
                background-color: transparent;
                border: 1px solid #091F5B;
            }

            .pagination .page-item.active .page-link {
                color: #fff;
                background-color: #091F5B;
                border-color: #091F5B;
            }

            .pagination .page-item:hover .page-link {
                color: #fff;
                background-color: #091F5B;
                border-color: #091F5B;
            }

            .pagination {
                margin-top: 20px;
            }
        }
    </style>
</head>

<body class="bg-light">
    <!-- Header -->
    @include('layouts.app')

    <div class="container py-4">
        <h1 class="display-4 mb-4 text-center">Peminjaman List</h1>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Combined Filters -->
        <div class="row mb-4">
            <form method="GET" action="{{ route('marketing.peminjaman') }}"
                class="d-flex align-items-center justify-content-between">
                <!-- Date Filter -->
                <div class="me-3">
                    <input type="date" name="date" class="form-control"
                        value="{{ old('date', request('date', $filterDate)) }}" onchange="this.form.submit()">
                </div>
                <!-- Search Filter -->
                <div>
                    <input type="text" name="search" class="form-control" placeholder="Search by Event Name"
                        value="{{ old('search', request('search')) }}" onkeyup="this.form.submit()">
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="container mt-4">
            <div class="card-body text-white my-2 shadow-lg" style="background-color:#091F5B; border-radius: 8px;">
                <div class="row align-items-center">
                    <div class="d-none">Aksi</div>
                    <div class="col-md-3 text-left" style="font-weight: bold">Nama Event</div>
                    <div class="col-md-2 text-left" style="font-weight: bold">Nama Organisasi</div>
                    <div class="col-md-2 text-left" style="font-weight: bold">Tanggal</div>
                    <div class="col-md-2 text-left" style="font-weight: bold">Ruangan dan Waktu</div>
                    <div class="col-md-2 text-left" style="font-weight: bold">Nama PIC</div>
                    <div class="col-md-1 text-left" style="font-weight: bold">Aksi</div>
                </div>
            </div>

            @forelse ($bookings as $booking)
                <div class="card-header text-dark my-2 shadow-sm" style="background-color:white; border-radius: 5px;">
                    <div class="row align-items-center">
                        <div class="d-none">
                            {{ $booking['booking_code'] }}
                        </div>
                        <div class="col-md-3 text-left">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#eventModal{{ $booking['id'] }}"
                                class="fw-bold" style="color: #091F5B;">
                                {{ $booking['name'] }}
                            </a>
                        </div>
                        <div class="col-md-2 text-left" style="color:#091F5B; font-weight: 600;">
                            {{ $booking['user_name'] }}
                        </div>
                        <div class="col-md-2 text-left" style="color:#091F5B; font-weight: 600;">
                            {{ $filterDate ?? 'No booking date available' }}
                        </div>
                        <div class="col-md-2 text-left" style="color:#091F5B; font-weight: 600;">
                            @foreach ($booking['ruangans'] as $ruangan)
                                <p>{{ $ruangan['name'] }}<br>
                                    <span>{{ $ruangan['floor'] }}</span><br>
                                    <span>{{ $booking['start_time'] ?? 'N/A' }} -
                                        {{ $booking['end_time'] ?? 'N/A' }}</span>
                                </p>
                            @endforeach
                        </div>
                        <div class="col-md-2 text-left" style="color:#091F5B; font-weight: 600;">
                            {{ $booking['pic_name'] }} <br>
                            <a href="https://wa.me/{{ $booking['pic_phone_number'] }}" target="_blank"
                                style="color: #25D366;">
                                {{ $booking['pic_phone_number'] }}</a>
                        </div>
                        <div class="col-md-1 text-left">
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editModal{{ $booking['id'] }}">Edit</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info text-center">
                    Tidak ada data booking untuk tanggal ini.
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3 mx-3">
            <!-- Dropdown untuk memilih jumlah data per halaman -->
            <div class="mb-3">
                <label for="per-page" class="form-label">Jumlah Data Per Halaman:</label>
                <select id="per-page" class="form-select" onchange="updatePerPage()">
                    <option value="6" {{ request('per_page') == 6 ? 'selected' : '' }}>6</option>
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                </select>
            </div>
            <!-- Pagination Section -->
            <div>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-end">
                        @for ($page = 1; $page <= $totalPages; $page++)
                            <li class="page-item {{ $currentPage == $page ? 'active' : '' }}">
                                <a class="page-link"
                                    href="{{ url()->current() }}?page={{ $page }}&per_page={{ $perPage }}&date={{ request('date') }}&search={{ request('search') }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endfor
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Modal for Event Details -->
        @foreach ($bookings as $booking)
            <div class="modal fade" id="eventModal{{ $booking['id'] }}" tabindex="-1"
                aria-labelledby="eventModalLabel{{ $booking['id'] }}" aria-hidden="true">
                <div class="modal-dialog" style="max-width: 600px;">
                    <div class="modal-content p-0 rounded-3">
                        <div class="modal-header"
                            style="border: none; padding-bottom: 0px; display: flex; justify-content: space-between; align-items: center;">
                            <h3 class="modal-title w-100 text-center" id="eventModalLabel{{ $booking['id'] }}"
                                style="color: #091F5B; font-weight: 400;">
                                Detail Acara
                            </h3>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="padding-top: 0px;">
                            <div class="text-center mb-2"
                                style="border-bottom: 3px solid #091F5B; padding-bottom: 5px; justify-content: center;">
                                <div style="font-size: 1.5rem;">
                                    {{ $booking['name'] }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Nama PIC:</strong></p>
                                    <p>{{ $booking['pic_name'] }}</p>
                                    <p><strong>Kategori Ekraf:</strong></p>
                                    <p>{{ $booking['kategori_ekraf'] }}</p>
                                    <p><strong>Jumlah Peserta:</strong></p>
                                    <p>{{ $booking['participant'] }} Orang</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>No Telp:</strong></p>
                                    <p>{{ $booking['pic_phone_number'] }}</p>
                                    <p><strong>Kategori Event:</strong></p>
                                    <p>{{ $booking['kategori_event'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @foreach ($bookings as $booking)
            <form id="pinjamForm{{ $booking['id'] }}" method="POST" action="{{ route('marketing.store') }}">
                @csrf
                <div class="modal fade" id="editModal{{ $booking['id'] }}" tabindex="-1"
                    aria-labelledby="editModalLabel{{ $booking['id'] }}" aria-hidden="true">
                    <div class="modal-dialog" style="max-width: 900px;">
                        <div class="modal-content">
                            <div class="main-card border">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                                <h4 class="text-center mb-4 fw-bold" id="editModalLabel">Formulir Peminjaman
                                    Barang</h4>
                                <div class="info-card border mb-2 p-3">
                                    <div class="d-flex align-items-center">
                                        <p class="mb-0">Nama Event</p>
                                        <p class="mb-0" style="color: #091F5B; margin-left: 20px;">
                                            <strong>{{ $booking['name'] }}</strong>
                                        </p>
                                    </div>
                                </div>
                                <div class="info-card border p-3 mb-3">
                                    <div class="row">
                                        <input type="hidden" name="kode_booking"
                                            value="{{ $booking['booking_code'] }}">
                                        <div class="col-md-6 d-flex align-items-center">
                                            <p class="mb-0 fw-bold flex-shrink-0" style="width: 100px;">Ruangan</p>
                                            <p class="mb-0" style="color: #091F5B;">
                                                @foreach ($booking['ruangans'] as $ruangan)
                                                    {{ $ruangan['name'] }} <br>
                                                @endforeach
                                            </p>
                                        </div>
                                        <div class="col-md-6 d-flex align-items-center">
                                            <p class="mb-0 fw-bold flex-shrink-0" style="width: 100px;">PIC</p>
                                            <p class="mb-0" style="color: #091F5B;">
                                                {{ $booking['pic_name'] }}
                                            </p>
                                        </div>
                                        <div class="col-md-6 d-flex align-items-center mt-2">
                                            <p class="mb-0 fw-bold flex-shrink-0" style="width: 100px;">Tanggal</p>
                                            <p class="mb-0 text-end ms-3" style="color: #091F5B;">
                                                @php
                                                    $matchingBookingItem = collect($booking['booking_items'])->firstWhere('booking_date', $filterDate);
                                                @endphp
                                                {{ $matchingBookingItem['booking_date'] ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <div class="col-md-6 d-flex align-items-center mt-2">
                                            <p class="mb-0 fw-bold flex-shrink-0" style="width: 100px;">Jam</p>
                                            <p class="mb-0 text-end" style="color: #091F5B;">
                                                {{ $booking['start_time'] ?? 'N/A' }} -
                                                {{ $booking['end_time'] ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <h6 class="fw-bold">List Barang yang Tersedia</h6>
                                <table class="table table-bordered" id="barangList{{ $booking['id'] }}">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Barang</th>
                                            <th>Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody id="availableItems{{ $booking['id'] }}">
                                        @php $no = 1; @endphp
                                        @foreach ($listBarang as $barang)
                                            <tr id="row-available-{{ $barang->id }}-{{ $booking['id'] }}">
                                                <td>{{ $no++ }}</td>
                                                <td class="text-align-left">{{ $barang->nama_barang }}</td>
                                                <td class="text-center">{{ $barang->jumlah }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <h6 class="fw-bold">List Barang yang Dipinjam</h6>
                                <table class="table table-bordered" id="borrowedList{{ $booking['id'] }}">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Barang</th>
                                            <th>Jumlah</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="borrowedItems{{ $booking['id'] }}">
                                        @php $no = 1; @endphp
                                        @if (!empty($booking['tools']))
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td class="text-align-left">{{ $booking['tools'] }}</td>
                                                <td class="text-center">-</td>
                                                <td><button type="button" class="btn btn-danger removeItem"
                                                        onclick="removeItem(this)">Hapus</button></td>
                                            </tr>
                                        @endif
                                        @foreach ($booking['database_items'] as $dbItem)
                                            <tr id="row-{{ $dbItem->id }}-{{ $booking['id'] }}">
                                                <td>{{ $no++ }}</td>
                                                <td class="text-align-left">{{ $dbItem->nama_item }}</td>
                                                <td class="text-center">{{ $dbItem->jumlah }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-danger removeItem"
                                                        onclick="removeItem(this)" data-id="{{ $dbItem->id }}"
                                                        data-row-id="row-{{ $dbItem->id }}-{{ $booking['id'] }}">
                                                        Hapus
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <button
                                    type="button"
                                    class="btn btn-primary btn-sm"
                                    data-id="{{ $booking['id'] }}"
                                    onclick="addItemFromButton(this)">Tambah Barang</button>
                                <div class="text-center mt-4 mb-4">
                                    <button type="button" class="btn btn-primary" id="simpanButton{{ $booking['id'] }}">Simpan</button>
                                </div>
                                <div class="modal-footer border-0">
                                    <div class="d-flex justify-content-between w-100">
                                        <div class="signature-group mt-4 text-center">
                                            <p class="signature-title">Mengetahui,<br> Marketing</p>
                                            <p><img src="{{ asset('images/marketing_ttd.png') }}" alt="Tanda Tangan"
                                                    style="width: 80px; height: 80px;"></p>
                                            <p>{{ $booking['history'][0]['pic_marketing'] ?? 'N/A' }}</p>
                                        </div>
                                        <div class="signature-group mt-4 text-center">
                                            <p class="signature-title">Mengetahui,<br> Peminjam</p>
                                            <p><img src="{{ asset('images/marketing_ttd.png') }}" alt="Tanda Tangan"
                                                    style="width: 80px; height: 80px;"></p>
                                            <p>{{ $booking['pic_name'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endforeach
    </div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.all.min.js"></script>
<script>
    let borrowedCounts = {};

    function addItem(bookingId) {
        if (!borrowedCounts[bookingId]) {
            borrowedCounts[bookingId] = document.querySelectorAll(`#borrowedItems${bookingId} tr`).length + 1;
        }
        let table = document.getElementById('borrowedList' + bookingId);
        let row = table.insertRow();
        let index = borrowedCounts[bookingId]++;
        
        row.innerHTML = `
            <td>${index}</td>
            <td><input type="text" name="items[${index}][nama_item]" class="form-control" required></td>
            <td><input type="number" name="items[${index}][jumlah]" class="form-control" min="1" required></td>
            <td><button type="button" class="btn btn-danger removeItem" onclick="removeItem(this)">Hapus</button></td>
        `;
        document.getElementById(`simpanButton${bookingId}`).disabled = false;
    }

    function addItemFromButton(button) {
        const id = button.dataset.id;
        addItem(id);
    }

    function removeItem(button) {
        let itemId = button.getAttribute('data-id');
        let rowId = button.closest('tr').id;
        let bookingId = rowId ? rowId.split('-').pop() : button.closest('table').id.replace('borrowedList', '');
        
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                if (itemId) {
                    fetch(`/items/${itemId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById(rowId).remove();
                            Swal.fire({
                                title: "Deleted!",
                                text: data.message,
                                icon: "success",
                                timer: 2000,
                                showConfirmButton: false
                            });
                            updateBorrowedTableNumbers(bookingId);
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: "Failed to delete the item: " + data.message,
                                icon: "error",
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: "Error!",
                            text: "An error occurred. Please try again.",
                            icon: "error",
                        });
                    });
                } else {
                    const row = button.closest('tr');
                    const namaItemInput = row.querySelector('input[name^="items"][name$="[nama_item]"]');
                    const jumlahInput = row.querySelector('input[name^="items"][name$="[jumlah]"]');
                    if (namaItemInput && jumlahInput && !namaItemInput.value.trim() && !jumlahInput.value.trim()) {
                        row.remove();
                        updateBorrowedTableNumbers(bookingId);
                    } else {
                        row.remove();
                        updateBorrowedTableNumbers(bookingId);
                    }
                }
                const borrowedItems = document.querySelectorAll(`#borrowedItems${bookingId} tr`);
                const hasNewInput = Array.from(borrowedItems).some(row => 
                    row.querySelector('input[name^="items"][name$="[nama_item]"]') && !row.querySelector('[data-id]')
                );
                document.getElementById(`simpanButton${bookingId}`).disabled = !hasNewInput;
            }
        });
    }

    function updateBorrowedTableNumbers(bookingId) {
        const borrowedItems = document.querySelectorAll(`#borrowedItems${bookingId} tr`);
        borrowedItems.forEach((row, index) => {
            row.children[0].innerText = index + 1;
            const inputs = row.querySelectorAll('input');
            if (inputs.length) {
                inputs[0].name = `items[${index + 1}][nama_item]`;
                inputs[1].name = `items[${index + 1}][jumlah]`;
            }
        });
        borrowedCounts[bookingId] = borrowedItems.length + 1;
        const hasNewInput = Array.from(borrowedItems).some(row => 
            row.querySelector('input[name^="items"][name$="[nama_item]"]') && !row.querySelector('[data-id]')
        );
        document.getElementById(`simpanButton${bookingId}`).disabled = !hasNewInput;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const forms = document.querySelectorAll('[id^="pinjamForm"]');
        forms.forEach(form => {
            const bookingId = form.id.replace('pinjamForm', '');
            const simpanButton = document.getElementById(`simpanButton${bookingId}`);
            simpanButton.disabled = true;

            simpanButton.addEventListener('click', (e) => {
                e.preventDefault();
                const inputs = form.querySelectorAll('input[required]');
                let valid = true;
                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        valid = false;
                        input.classList.add('is-invalid');
                        let errorDiv = input.nextElementSibling;
                        if (!errorDiv || !errorDiv.classList.contains('error-message')) {
                            errorDiv = document.createElement('div');
                            errorDiv.classList.add('error-message');
                            errorDiv.innerText = `Kolom ${input.name.includes('nama_item') ? 'Nama Barang' : 'Jumlah'} wajib diisi.`;
                            input.parentNode.appendChild(errorDiv);
                        }
                    } else {
                        input.classList.remove('is-invalid');
                        const errorDiv = input.nextElementSibling;
                        if (errorDiv && errorDiv.classList.contains('error-message')) {
                            errorDiv.remove();
                        }
                    }
                });

                if (!valid) {
                    Swal.fire({
                        title: "Error!",
                        text: "Harap isi semua kolom yang diperlukan.",
                        icon: "error",
                    });
                    return;
                }

                const formData = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`HTTP error! status: ${response.status}, body: ${text.substring(0, 100)}...`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: "Success!",
                            text: data.message || 'Barang berhasil disimpan!',
                            icon: "success",
                            confirmButtonText: "OK",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#editModal' + bookingId).modal('hide'); 
                                location.reload(); 
                            }
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: data.message || 'Gagal menyimpan barang.',
                            icon: "error",
                        });
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    Swal.fire({
                        title: "Error!",
                        text: `Terjadi kesalahan saat menyimpan: ${error.message}`,
                        icon: "error",
                    });
                });
            });
        });
    });
</script>
</body>

</html>