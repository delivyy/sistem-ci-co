<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <script src="{{ asset('js/app.js') }}"></script>

    <title>Booking List</title>
</head>

<body>

    <!-- Navbar -->
    @include('layouts.app')

    <!-- Main Content -->
    <div class="container my-4">
        @if (session('sukses'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('sukses') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('gagal'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('gagal') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show text-center mx-3 mt-3">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="display-4 flex-column flex-md-row text-center mb-4">
            <h1 class="display-4 mb-4 text-center">Booking List</h1>
        </div>
        <!-- Filter and Search in a Single Row -->
        <div class="row align-items-center mb-3" style="margin-top: -10px;">
            <!-- Filter -->
            <div class="col-md-3">
                <form method="GET" action="{{ route('front_office.dashboard') }}">
                    <select name="status" class="form-select" style="width: 100%;" aria-label="Status Filter"
                        onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Check-in" {{ request('status') == 'Check-in' ? 'selected' : '' }}>Check-in</option>
                        <option value="Booked" {{ request('status') == 'Booked' ? 'selected' : '' }}>Booked</option>
                        <option value="Check-out" {{ request('status') == 'Check-out' ? 'selected' : '' }}>Check-out
                        </option>
                    </select>
                </form>
            </div>

            <!-- Search (Tengah) -->
            <div class="col-md-6 text-center">
                <form method="GET" action="{{ route('front_office.dashboard') }}" class="d-inline-block"
                    style="width: 100%;">
                    <input type="text" name="search" class="form-control"
                        placeholder="Cari berdasarkan Nama Event atau Kode Booking" style="font-style: italic;"
                        value="{{ old('search', request('search')) }}" onchange="this.form.submit()">
                </form>
            </div>

            <!-- Export -->
            <div class="col-md-3 text-end">
                 <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#exportDateModal">
                        <i class="fas fa-download"></i> Export
                    </button>
        </div>

        <!-- Booking Table -->
        <div class="responsive-container">
            <table class="table custom-table">
                <thead class="table-header">
                    <tr>
                        <th style="width: 15%;">Kode Booking</th>
                        <th style="width: 15%;">Nama Event</th>
                        <th style="width: 15%;">Nama Organisasi</th>
                        <th style="width: 18%;">Ruangan dan Waktu</th>
                        <th style="width: 12%;">Nama PIC</th>
                        <th style="width: 12%;">Duty Officer</th>
                        <th style="width: 15%;">User Check-in</th>
                        <th style="width: 13%;">Status</th>
                        <th style="width: 20%;">Hasil SDG</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sortedBookings = $bookings->sortByDesc(function ($booking) {
                            return isset($booking['absen']['status']) && $booking['absen']['status'] === 'Check-in' ? 1 : 0;
                        });
                    @endphp
                    @foreach ($sortedBookings as $booking)
                        @php
                            // Ambil tanggal booking dari kolom yang tersedia
                            $booking_date = $booking['tanggal'] ?? ($booking['start_date'] ?? ($booking['booking_date'] ?? now()->format('Y-m-d')));
                            $sdgs_data = \App\Models\Booking::where('kode_booking', $booking['booking_code'])
                                ->where('tanggal', $booking_date)
                                ->value('sdgs_data');
                            $sdgs = json_decode($sdgs_data ?? '[]', true);
                            // Logika $sdgsFilled agar SDG None dianggap valid
                            $sdgsFilled = !empty($sdgs);
                        @endphp
                        <tr class="table-row">
                            <td>{{ $booking['booking_code'] }}</td>
                            <td>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#eventModal{{ $booking['id'] }}"
                                    class="fw-bold" style="color: #091F5B;">
                                    {{ Str::limit($booking['name'], 20) }} 
                                </a>
                            </td>
                            <td class="fw-semibold">{{ $booking['user_name'] }}</td>

                            <td>
                                @if (!empty($booking['ruangans']) && is_array($booking['ruangans']))
                                    @foreach ($booking['ruangans'] as $ruangan)
                                        <p>{{ $ruangan['name'] }}<br>
                                            <span>{{ $ruangan['floor'] }}</span><br>
                                            <span>{{ $booking['start_time'] ?? 'N/A' }} - {{ $booking['end_time'] ?? 'N/A' }}</span>
                                        </p>
                                    @endforeach
                                @else
                                    <p>No rooms available</p>
                                @endif
                            </td>
                            <td>{{ $booking['pic_name'] }}</td>

                            <td>
                                @if (!empty($booking['absen']['name']))
                                    @if (!empty($booking['absen']['duty_officer']))
                                        {{ $booking['absen']['duty_officer'] }}
                                    @else
                                        <button type="button" class="btn btn-sm"
                                            style="background-color: #5897ee; color:#ffff; font-weight: 600;"
                                            data-bs-toggle="modal" data-bs-target="#dutyOfficerModal"
                                            data-booking-id="{{ $booking['booking_code'] }}">
                                            Pilih Duty Officer
                                        </button>
                                    @endif
                                @else
                                    <button type="button" class="btn btn-sm"
                                        style="background-color: #5897ee; color:#ffff; font-weight: 600;"
                                        data-bs-toggle="modal" data-bs-target="#checkinWarningModal">
                                        Pilih Duty Officer
                                    </button>
                                @endif
                            </td>
                            <!-- Modal Peringatan Check-in -->
                            <div class="modal fade" id="checkinWarningModal" tabindex="-1"
                                aria-labelledby="checkinWarningModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="checkinWarningModalLabel">Peringatan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Silahkan check-in dulu sebelum memilih Duty Officer.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary"
                                                data-bs-dismiss="modal">OK</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if (empty($booking['absen']['duty_officer']))
                                <div class="modal fade" id="dutyOfficerModal" tabindex="-1"
                                    aria-labelledby="dutyOfficerModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content rounded-4 shadow-lg">
                                            <form id="dutyOfficerForm" method="POST" action="{{ route('dutyofficer.store') }}">
                                                @csrf
                                                <div class="modal-header"
                                                    style="background-color: #3182ce; color: #fff;">
                                                    <h5 class="modal-title" id="dutyOfficerModalLabel">Pilih Duty Officer</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" id="bookingId" name="id_booking"
                                                        value="{{ $booking['booking_code'] }}">
                                                    <div class="mb-3">
                                                        <label for="dutyOfficerSelect" class="form-label"
                                                            style="font-weight: 600; color: #4C74E1;">Duty Officer</label>
                                                        <select class="form-select" id="dutyOfficerSelect"
                                                            name="duty_officer_id" required
                                                            style="border-radius: 10px; border: 1px solid #4C74E1;">
                                                            @foreach ($dutyOfficers as $officer)
                                                                <option value="{{ $officer->id }}">{{ $officer->nama_do }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                                        style="background-color: #D1D1D1; border: none;">Close</button>
                                                    <button type="submit" class="btn"
                                                        style="background-color: #4C74E1; color: white;">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <td>
                                @if (!empty($booking['absen']))
                                    {{ $booking['absen']['name'] }}<br>
                                    @php
                                        $phone = preg_replace('/\D/', '', $booking['absen']['phone']);
                                    @endphp
                                    <a href="https://wa.me/{{ $phone }}" target="_blank" style="color: #25D366;">
                                        {{ $phone }}
                                    </a>
                                @else
                                    <em class="text-secondary ">Belum Check-in</em>
                                    <em class="text-secondary">Belum Check-in</em>
                                @endif
                            </td>

                            <td>
                                @if (!empty($booking['absen']))
                                    @if ($booking['absen']['status'] === 'Check-in')
                                        <button
                                            class="btn btn-sm w-100 d-flex align-items-center justify-content-center custom-shadow fw-bold checkout-btn"
                                            style="background-color: #c6f6d5; color:#38a169;"
                                            data-booking-id="{{ $booking['id'] }}"
                                            data-duty-officer="{{ $booking['absen']['duty_officer'] ?? '' }}"
                                            data-sdgs-filled="{{ $sdgsFilled ? 'true' : 'false' }}">
                                            Check-In
                                        </button>
                                    @elseif ($booking['absen']['status'] === 'Check-out')
                                        <span class="btn btn-sm w-100 custom-shadow fw-bold"
                                            style="background-color: #edf2f7; color:#a0aec0; pointer-events: none; border: 2px solid white;">
                                            Check-Out
                                        </span>
                                    @endif
                                @else
                                    <a href="{{ route('inputkode.match', ['id_booking' => $booking['booking_code']]) }}"
                                        class="btn w-100 custom-shadow"
                                        style="background-color: #feebc8; font-weight: 600; color: #d69e2e;">
                                        Booked
                                    </a>
                                @endif
                            </td>

                            <!-- Tabel Hasil SDG -->
                            <td>
                                @if (!empty($sdgs))
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($sdgs as $sdg)
                                            <li>
                                                @if ($sdg[0] === 'SDG None')
                                                    SDG belum tersedia di MCC
                                                @else
                                                    @php
                                                        // Format ID SDG sebagai SDGXX
                                                        $sdgId = is_numeric($sdg[0]) ? 'SDG' . str_pad($sdg[0], 2, '0', STR_PAD_LEFT) : $sdg[0];
                                                    @endphp
                                                    {{ $sdgId }}
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    @if (!empty($booking['absen']))
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#sdgModal{{ $booking['id'] }}" class="text-decoration-none">
                                            <strong><u class="text-muted">Belum Mengisi SDG</u></strong>
                                        </a>
                                    @else
                                        <span class="text-muted">
                                            <em>Check-in terlebih dahulu untuk mengisi SDG</em>
                                        </span>
                                    @endif
                                @endif
                            </td>

                            <!-- Update bagian modal SDG di dashboard front office -->
                            @if (!empty($booking['absen']))
                                <div class="modal fade" id="sdgModal{{ $booking['id'] }}" tabindex="-1" aria-labelledby="sdgModalLabel{{ $booking['id'] }}" aria-hidden="true">
                                    <div class="modal-dialog" style="max-width: 500px;">
                                        <div class="modal-content p-0 rounded-3">
                                            <div class="modal-header" style="border: none; padding-bottom: 0px; display: flex; justify-content: space-between; align-items: center;">
                                                <h3 class="modal-title w-100 text-center" id="sdgModalLabel{{ $booking['id'] }}" style="color: #091F5B; font-weight: 400;">
                                                    Kuesioner SDG
                                                </h3>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            
                                            <div class="modal-body" style="padding-top: 0px;">
                                                <div class="text-center mb-2" style="border-bottom: 3px solid #091F5B; padding-bottom: 5px; justify-content: center;">
                                                    <div style="font-size: 1.5rem;">
                                                        {{ $booking['name'] }}
                                                    </div>
                                                </div>
                                                
                                                <div class="row mt-3">
                                                    <!-- Tampilkan kontak user yang check-in -->
                                                    <div class="col-12 mb-3">
                                                        <p><strong>Kontak User Check-in:</strong></p>
                                                        <p>
                                                            @php
                                                                $phone = preg_replace('/\D/', '', $booking['absen']['phone']);
                                                                $encryptedBookingId = \App\Helpers\BookingEncryption::encrypt($booking['booking_code']);
                                                                $kuesionerUrl = "http://127.0.0.1:8000/sdgs-quiz?code=" . $encryptedBookingId . "&date=" . urlencode($booking_date);
                                                                $waMessage = "Halo, berikut link pengisian kuesioner acara: " . $booking['name'] . " (Tanggal: " . $booking_date . "):\n\n" . $kuesionerUrl . "\n\nTerima kasih!";
                                                                $encodedMessage = urlencode($waMessage);
                                                            @endphp
                                                            <a href="https://wa.me/{{ $phone }}?text={{ $encodedMessage }}" target="_blank" class="btn btn-success">
                                                                <i class="fab fa-whatsapp"></i> Kirim Link Kuesioner via WA
                                                            </a>                                
                                                        </p>
                                                    </div>
                                                    
                                                    <div class="col-12">
                                                        <p><strong>Link Kuesioner:</strong></p>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <a href="{{ $kuesionerUrl }}" 
                                                               class="btn btn-primary" target="_blank">
                                                                <i class="fas fa-external-link-alt"></i> Buka Kuesioner SDG
                                                            </a>
                                                            <button type="button" 
                                                                    class="btn btn-outline-secondary" 
                                                                    onclick="copyToClipboard('{{ $kuesionerUrl }}', this)"
                                                                    title="Copy Link"
                                                                    data-bs-toggle="tooltip">
                                                                <i class="fas fa-copy"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
                            
        @foreach ($sortedBookings as $booking)
            @if (!empty($booking['absen']) && $booking['absen']['status'] === 'Check-in')
                @php
                    // Ambil tanggal booking dari kolom yang tersedia untuk modal check-out
                    $booking_date = $booking['tanggal'] ?? ($booking['start_date'] ?? ($booking['booking_date'] ?? now()->format('Y-m-d')));
                    $sdgs_data = \App\Models\Booking::where('kode_booking', $booking['booking_code'])
                        ->where('tanggal', $booking_date)
                        ->value('sdgs_data');
                    $sdgs = json_decode($sdgs_data ?? '[]', true);
                    // Logika $sdgsFilled agar SDG None dianggap valid
                    $sdgsFilled = !empty($sdgs);
                @endphp

                <!-- Modal Peringatan SDG Belum Diisi -->
                <div class="modal fade" id="sdgWarningModal{{ $booking['id'] }}" tabindex="-1"
                    aria-labelledby="sdgWarningModalLabel{{ $booking['id'] }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content rounded-4 shadow-lg">
                            <div class="modal-header" style="background-color: rgba(91, 76, 225, 0.5); color: #fff;">
                                <h5 class="modal-title" id="sdgWarningModalLabel{{ $booking['id'] }}">Peringatan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <i class="fas fa-exclamation-circle" style="font-size: 2em; color: #FFC107; margin-bottom: 1rem; display: block;"></i>
                                <p style="font-weight: 500; color: #333;">
                                    SDG belum diisi. Silakan isi data SDG terlebih dahulu sebelum melakukan check-out.
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn" style="background-color: #4C74E1; color: white;" data-bs-dismiss="modal">OK</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Konfirmasi Check-out -->
                <div class="modal fade" id="checkoutModal{{ $booking['id'] }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Konfirmasi Check-out</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @if (empty($booking['absen']['duty_officer']))
                                    <div class="alert alert-warning" role="alert">
                                        <strong>Peringatan!</strong> Anda harus memilih Duty Officer terlebih dahulu sebelum melakukan Check-out.
                                    </div>
                                @elseif (!$sdgsFilled)
                                    <div class="alert alert-warning" role="alert">
                                        <strong>Peringatan!</strong> SDG belum diisi. Silakan isi data SDG terlebih dahulu.
                                    </div>
                                @else
                                    Apakah Anda yakin ingin melakukan check-out untuk booking ini?
                                @endif
                            </div>
                            <div class="modal-footer">
                                @if (!empty($booking['absen']['duty_officer']) && $sdgsFilled)
                                    <form method="POST" action="{{ route('inputkode.checkout') }}">
                                        @csrf
                                        <input type="hidden" name="kode_booking" value="{{ $booking['booking_code'] }}">
                                        <input type="hidden" name="tanggal" value="{{ $booking_date }}">
                                        <button type="submit" class="btn btn-danger">Check-out</button>
                                    </form>
                                @endif
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
                            
        <!-- Pagination and Dropdown for per page selection -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="mb-3">
                <label for="per-page" class="form-label"></label>
                <select id="per-page" class="form-select" onchange="updatePerPage()">
                    <option value="6" {{ request('per_page') == 6 ? 'selected' : '' }}>6</option>
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                </select>
            </div>
                            
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    @for ($page = 1; $page <= $totalPages; $page++)
                        <li class="page-item {{ $currentPage == $page ? 'active' : '' }}">
                            <a class="page-link" href="{{ url()->current() }}?page={{ $page }}&per_page={{ $perPage }}">
                                {{ $page }}
                            </a>
                        </li>
                    @endfor
                </ul>
            </nav>
        </div>
                            
        <!-- Modal for Event Details -->
        @foreach ($sortedBookings as $booking)
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
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                            
                        <div class="modal-body" style="padding-top: 0px;">
                            <div class="text-center mb-2" style="border-bottom: 3px solid #091F5B; padding-bottom: 5px; justify-content: center;">
                                <div style="font-size: 1.5rem;">
                                    {{ $booking['name'] }}
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Nama PIC:</strong></p>
                                    <p><strong style="font-weight: bold; font-size: 18px;">{{ $booking['pic_name'] }}</strong></p>
                            
                                    <p><strong>Kategori Ekraf:</strong></p>
                                    <p>{{ $booking['kategori_ekraf'] }}</p>
                            
                                    <p><strong>Jumlah Peserta:</strong></p>
                                    <p>{{ $booking['participant'] }} Orang</p>
                                </div>
                            
                                <div class="col-md-6">
                                    <p><strong>No Telp:</strong></p>
                                    <p><a href="https://wa.me/{{ $booking['pic_phone_number'] }}" target="_blank" style="color: #25D366;">
                                        <span>{{ $booking['pic_phone_number'] }}</span>
                                    </a></p>
                            
                                    <p><strong>Kategori Event:</strong></p>
                                    <p>{{ $booking['kategori_event'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <script>
            $(document).ready(function () {
                $('.checkout-btn').on('click', function (e) {
                    e.preventDefault();
                    
                    const bookingId = $(this).data('booking-id');
                    const dutyOfficer = $(this).data('duty-officer');
                    const sdgsFilled = $(this).data('sdgs-filled') === true;

                    const modalElement = $('#checkoutModal' + bookingId);
                    const sdgWarningModal = $('#sdgWarningModal' + bookingId);

                    if (!modalElement.length) {
                        console.error('Checkout modal not found for bookingId:', bookingId);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Modal checkout tidak ditemukan.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#4C74E1'
                        });
                        return;
                    }

                    console.log('Checkout button clicked:', { bookingId, dutyOfficer, sdgsFilled });

                    if (!dutyOfficer) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Silakan pilih Duty Officer terlebih dahulu.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#4C74E1'
                        });
                        return;
                    }

                    if (!sdgsFilled) {
                        const modal = new bootstrap.Modal(sdgWarningModal[0], { backdrop: 'static' });
                        modal.show();
                        $(document.activeElement).blur();
                        return;
                    }

                    const modal = new bootstrap.Modal(modalElement[0], { backdrop: 'static' });
                    modal.show();
                    $(document.activeElement).blur();
                });

            // =================================================================
            // === KODE BARU UNTUK FUNGSI EKSPOR DITARUH DI SINI ===
            // =================================================================
            $('.export-trigger-btn').on('click', function () {
                const startDate = $('#export_start_date').val();
                const endDate = $('#export_end_date').val();
                const format = $(this).data('format');

                if (!startDate || !endDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Input Tidak Lengkap',
                        text: 'Silakan pilih Tanggal Mulai dan Tanggal Akhir terlebih dahulu.',
                    });
                    return;
                }

                const currentUrlParams = new URLSearchParams(window.location.search);
                const exportUrl = new URL("{{ route('bookings.export') }}");
                exportUrl.searchParams.set('start_date', startDate);
                exportUrl.searchParams.set('end_date', endDate);
                exportUrl.searchParams.set('format', format);

                if (currentUrlParams.has('status')) {
                    exportUrl.searchParams.set('status', currentUrlParams.get('status'));
                }
                if (currentUrlParams.has('search')) {
                    exportUrl.searchParams.set('search', currentUrlParams.get('search'));
                }

                window.location.href = exportUrl.href;
                $('#exportDateModal').modal('hide');
            });


            // --- BLOK 2: LOGIKA UNTUK TOOLTIPS (BIARKAN SEPERTI ADANYA) ---
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
            });

            function copyToClipboard(text, button) {
                navigator.clipboard.writeText(text).then(function() {
                    const icon = button.querySelector('i');
                    const originalClass = icon.className;
                    icon.className = 'fas fa-check';
                    button.classList.add('btn-success');
                    button.classList.remove('btn-outline-secondary');
                    
                    setTimeout(function() {
                        icon.className = originalClass;
                        button.classList.remove('btn-success');
                        button.classList.add('btn-outline-secondary');
                    }, 2000);
                });
            }

            function updatePerPage() {
                const perPage = document.getElementById('per-page').value;
                const url = new URL(window.location);
                url.searchParams.set('per_page', perPage);
                url.searchParams.set('page', 1); 
                window.location.href = url.toString();
            }
        </script>
    </div>
    <div class="modal fade" id="exportDateModal" tabindex="-1" aria-labelledby="exportDateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportDateModalLabel">Pilih Rentang Tanggal Ekspor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="export_start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="export_start_date" required>
                </div>
                <div class="mb-3">
                    <label for="export_end_date" class="form-label">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="export_end_date" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger export-trigger-btn" data-format="pdf">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
                <button type="button" class="btn btn-success export-trigger-btn" data-format="csv">
                    <i class="fas fa-file-csv"></i> Export CSV
                </button>
            </div>
        </div>
    </div>
    </div>
</body>                            
</html>