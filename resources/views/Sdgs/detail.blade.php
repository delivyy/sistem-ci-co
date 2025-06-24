<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SDGs Detail - {{ $sdg->deskripsi ?? 'Detail' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('css/data.css') }}">
    <link rel="stylesheet" href="{{asset('css/header.css')}}">
    <style>
        /* Base styles */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            font-family: 'Inter', sans-serif;
        }

        main.container {
            margin-top: 50px;
            flex: 1 0 auto;
            padding: 0 20px;
            max-width: 1200px;
            width: 100%;
            box-sizing: border-box;
            margin-left: auto;
            margin-right: auto;
        }

        /* SDG Header */
        .sdg-header {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: nowrap;
        }

        .sdg-img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .nama_sdg {
            flex: 1;
            min-width: 0;
        }

        .sdg-title {
            font-size: 1.8rem;
            color: #2d3748;
            word-break: break-word;
            margin: 0 0 8px 0;
            line-height: 1.3;
        }

        .nama_sdg p {
            margin: 0;
            color: #718096;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        /* Table styles */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #ffffff;
            min-width: 600px; /* Ensure table doesn't get too small */
        }

        .table th, .table td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .table th {
            background-color: #001A72;
            font-weight: 600;
            color: #ffffff;
            text-align: center;
            white-space: nowrap;
        }

        .table td {
            color: #4a5568;
            text-align: left;
            background-color: #ffffff;
            word-wrap: break-word;
        }

        .no-data {
            text-align: center;
            color: #718096;
            padding: 20px;
        }

        /* Table rows hover effect */
        .table tr {
            transition: background-color 0.3s ease;
        }
        
        .table tr:hover td {
            background-color: #f9fafb;
        }

        /* Card styles */
        .card-lg {
            border: none;
            box-shadow: none;
            background-color: transparent;
            margin-bottom: 30px;
        }

        /* Pagination styles */
        .pagination-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
            width: 100%;
            overflow-x: auto;
        }

        .pagination {
            display: flex;
            gap: 5px;
            margin: 0;
            padding: 0;
            list-style: none;
            flex-wrap: wrap;
        }

        .page-numbers-container {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .pagination .page-item {
            list-style: none;
            margin: 0;
        }

        .pagination .page-link {
            padding: 8px 12px;
            border-radius: 4px;
            color: #3182ce;
            background-color: #fff;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            display: block;
        }

        .pagination .page-link:hover {
            background-color: #f7fafc;
        }

        .pagination .active .page-link {
            background-color: #3182ce;
            color: white;
            border-color: #3182ce;
        }

        .pagination .disabled .page-link {
            color: #a0aec0;
            pointer-events: none;
            cursor: not-allowed;
        }

        .pagination .ellipsis .page-link {
            background: transparent;
            border: none;
            pointer-events: none;
        }

        /* Responsive styles */
        @media screen and (max-width: 768px) {
            main.container {
                margin-top: 20px;
            }

            .sdg-header {
                gap: 15px;
                align-items: center;
            }

            .sdg-img {
                width: 80px;
                height: 80px;
            }

            .sdg-title {
                font-size: 1.5rem;
            }

            .nama_sdg p {
                font-size: 0.85rem;
            }

            .pagination-container {
                justify-content: center;
            }

            .table th, .table td {
                padding: 8px;
                font-size: 0.9rem;
            }
        }

        @media screen and (max-width: 480px) {
            .left-logo, .right-logo {
                gap: 10px;
            }

            .logo-malang, .logo-mcc, .logo-sdg {
                height: 40px;
            }

            main.container {
                margin-top: 15px;
                padding: 0 10px;
            }

            .sdg-header {
                gap: 12px;
                margin-bottom: 15px;
                align-items: flex-start;
            }

            .sdg-img {
                width: 70px;
                height: 70px;
            }

            .sdg-title {
                font-size: 1.3rem;
                line-height: 1.2;
            }

            .nama_sdg p {
                font-size: 0.8rem;
            }

            .pagination .page-link {
                padding: 6px 10px;
                font-size: 0.9rem;
            }
        }

        /* Extra small screens - phones in portrait */
        @media screen and (max-width: 360px) {
            main.container {
                margin-top: 10px;
            }

            .sdg-header {
                gap: 10px;
                margin-bottom: 10px;
            }

            .sdg-img {
                width: 60px;
                height: 60px;
            }

            .sdg-title {
                font-size: 1.1rem;
                line-height: 1.2;
            }

            .nama_sdg p {
                font-size: 0.75rem;
            }
        }

        /* Very small screens - adjust layout if needed */
        @media screen and (max-width: 320px) {
            .sdg-header {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 8px;
            }

            .nama_sdg {
                text-align: center;
            }

            .sdg-title {
                text-align: center;
            }

            .nama_sdg p {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="left-logo">
            <img src="{{ asset('img/malang.png') }}" alt="Kota Malang Logo" class="logo-malang" />
            <img src="{{ asset('img/mcc.png') }}" alt="MCC Logo" class="logo-mcc" />
        </div>
        <div class="right-logo">
            <img src="{{ asset('img/sdgs.png') }}" alt="SDGs Logo" class="logo-sdg" />
        </div>
    </div>
    <main class="container">
        <div class="card card-lg"> 
            <div class="sdg-header">
                <img class="sdg-img" 
                src="{{ asset('logo_sdg/' . Str::lower($sdg->id_sdgs) . '.png') }}" 
                alt="{{ $sdg->deskripsi }} Logo" />
                <div class="nama_sdg">
                    <div class="sdg-title">{{ $sdg->nama_sdg }}</div>
                    <p>{{ $sdg->deskripsi }}</p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Acara</th>
                            <th>Nama Organisasi</th>
                            <th>Jenis Kegiatan</th>
                            <th>Sub Sektor</th>
                            <th>Komersial</th>
                            <th>Jumlah Peserta</th>
                        </tr>
                    </thead>
                    <tbody id="tableContent">
                        @forelse ($bookings as $index => $booking)
                            <tr class="data-row">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $booking->nama_event ?? 'Tidak ada nama acara' }}</td>
                                <td>{{ $booking->nama_organisasi ?? 'Tidak ada organisasi' }}</td>
                                <td>{{ $booking->bidang ?? 'Tidak ada bidang' }}</td>
                                <td>{{ $booking->kegiatan ?? 'Tidak ada kegiatan' }}</td>
                                <td>{{ $booking->{'komersial/non'} ?? 'Tidak diketahui' }}</td>
                                <td>{{ $booking->jumlah_peserta ?? 0 }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="no-data">Tidak ada data untuk SDG ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination controls -->
            <div class="pagination-container">
                <ul class="pagination" id="pagination"></ul>
            </div>
        </div>
    </main>
    <footer class="footer"></footer>
    <script src="{{ asset('js/detail.js') }}"></script>
</body>
</html>