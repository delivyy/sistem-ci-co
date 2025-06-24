<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Lantai - {{ $lantai ?? 'Detail' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    {{-- Font Awesome untuk ikon sorting --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('css/data.css') }}">
    <link rel="stylesheet" href="{{asset('css/header.css')}}">
    <style>
        /* [SALIN SEMUA CSS ANDA YANG SEBELUMNYA DI SINI] */
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

        .lantai-header {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: nowrap;
        }

        .lantai-icon {
            width: 100px;
            height: 100px;
            object-fit: contain;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0f4f8;
            border-radius: 8px;
        }

        .lantai-icon i {
            font-size: 3rem;
            color: #2d3748;
        }

        .nama_lantai {
            flex: 1;
            min-width: 0;
        }

        .lantai-title {
            font-size: 1.8rem;
            color: #2d3748;
            word-break: break-word;
            margin: 0 0 8px 0;
            line-height: 1.3;
        }

        .nama_lantai p {
            margin: 0;
            color: #718096;
            font-size: 0.9rem;
            line-height: 1.4;
        }

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
            min-width: 600px;
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

        /* -- PERUBAHAN CSS DIMULAI DI SINI -- */
        .table th.sortable {
            cursor: pointer;
            position: relative;
        }

        .table th.sortable:hover {
            background-color: #00259c;
        }

        .table th.sortable .sort-icon {
            margin-left: 8px;
            color: #a0aec0;
        }
        
        .table th.sortable.asc .sort-icon,
        .table th.sortable.desc .sort-icon {
            color: #ffffff;
        }
        /* -- PERUBAHAN CSS SELESAI -- */


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

        .table tr {
            transition: background-color 0.3s ease;
        }
        
        .table tr:hover td {
            background-color: #f9fafb;
        }

        .card-lg {
            border: none;
            box-shadow: none;
            background-color: transparent;
            margin-bottom: 30px;
        }

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

        @media screen and (max-width: 768px) {
            main.container {
                margin-top: 20px;
            }

            .lantai-header {
                gap: 15px;
                align-items: center;
            }

            .lantai-icon {
                width: 80px;
                height: 80px;
            }

            .lantai-icon i {
                font-size: 2.5rem;
            }

            .lantai-title {
                font-size: 1.5rem;
            }

            .nama_lantai p {
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

            .lantai-header {
                gap: 12px;
                margin-bottom: 15px;
                align-items: flex-start;
            }

            .lantai-icon {
                width: 70px;
                height: 70px;
            }

            .lantai-icon i {
                font-size: 2rem;
            }

            .lantai-title {
                font-size: 1.3rem;
                line-height: 1.2;
            }

            .nama_lantai p {
                font-size: 0.8rem;
            }

            .pagination .page-link {
                padding: 6px 10px;
                font-size: 0.9rem;
            }
        }

        @media screen and (max-width: 360px) {
            main.container {
                margin-top: 10px;
            }

            .lantai-header {
                gap: 10px;
                margin-bottom: 10px;
            }

            .lantai-icon {
                width: 60px;
                height: 60px;
            }

            .lantai-icon i {
                font-size: 1.8rem;
            }

            .lantai-title {
                font-size: 1.1rem;
                line-height: 1.2;
            }

            .nama_lantai p {
                font-size: 0.75rem;
            }
        }

        @media screen and (max-width: 320px) {
            .lantai-header {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 8px;
            }

            .nama_lantai {
                text-align: center;
            }

            .lantai-title {
                text-align: center;
            }

            .nama_lantai p {
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
            <div class="lantai-header">
                <div class="lantai-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="nama_lantai">
                    <div class="lantai-title">{{ $lantai }}</div>
                    <p>Daftar kegiatan dan peminjaman ruangan di lantai ini</p>
                </div>
            </div>
            <div class="table-responsive">
                {{-- ID ditambahkan ke tabel --}}
                <table class="table" id="bookingTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Ruangan</th>
                            <th>Nama Acara</th>
                            <th>Nama Organisasi</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            
                            {{-- -- PERUBAHAN HTML DIMULAI DI SINI -- --}}
                            <th class="sortable" onclick="sortTable(6)">
                                Jumlah Peserta
                                <i class="fas fa-sort sort-icon"></i>
                            </th>
                            {{-- -- PERUBAHAN HTML SELESAI -- --}}
                        </tr>
                    </thead>
                    {{-- ID ditambahkan ke tbody --}}
                    <tbody id="bookingTableBody">
                        @forelse ($bookings as $booking)
                             <tr class="data-row">
                                <td>{{ $bookings->firstItem() + $loop->index }}</td>
                                <td>{{ $booking->ruangan ?? 'Tidak ada ruangan' }}</td>
                                <td>{{ $booking->nama_event ?? 'Tidak ada nama acara' }}</td>
                                <td>{{ $booking->nama_organisasi ?? 'Tidak ada organisasi' }}</td>
                                <td>{{ $booking->tanggal ? \Carbon\Carbon::parse($booking->tanggal)->format('d/m/Y') : 'Tidak ada tanggal' }}</td>
                                <td>
                                    @if($booking->waktu_mulai && $booking->waktu_selesai)
                                        {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}
                                    @else
                                        Waktu tidak tersedia
                                    @endif
                                </td>
                                <td>{{ $booking->jumlah_peserta ?? 0 }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="no-data">Tidak ada data untuk lantai ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($bookings->hasPages())
            <div class="pagination-container">
                 <ul class="pagination">
                   {{-- Previous Page Link --}}
                   @if($bookings->onFirstPage())
                   <li class="page-item disabled">
                       <span class="page-link">&laquo;</span>
                   </li>
               @else
                   <li class="page-item">
                       <a class="page-link" href="{{ $bookings->previousPageUrl() }}" rel="prev">&laquo;</a>
                   </li>
               @endif

               {{-- First Page Link --}}
               @if($bookings->currentPage() > 3)
                   <li class="page-item">
                       <a class="page-link" href="{{ $bookings->url(1) }}">1</a>
                   </li>
                   @if($bookings->currentPage() > 4)
                       <li class="page-item disabled">
                           <span class="page-link">...</span>
                       </li>
                   @endif
               @endif

               {{-- Page Number Links --}}
               @foreach(range(1, $bookings->lastPage()) as $i)
                   @if($i >= $bookings->currentPage() - 1 && $i <= $bookings->currentPage() + 1)
                       @if($i == $bookings->currentPage())
                           <li class="page-item active">
                               <span class="page-link">{{ $i }}</span>
                           </li>
                       @else
                           <li class="page-item">
                               <a class="page-link" href="{{ $bookings->url($i) }}">{{ $i }}</a>
                           </li>
                       @endif
                   @endif
               @endforeach

               {{-- Last Page Links --}}
               @if($bookings->currentPage() < $bookings->lastPage() - 2)
                   @if($bookings->currentPage() < $bookings->lastPage() - 3)
                       <li class="page-item disabled">
                           <span class="page-link">...</span>
                       </li>
                   @endif
                   <li class="page-item">
                       <a class="page-link" href="{{ $bookings->url($bookings->lastPage()) }}">{{ $bookings->lastPage() }}</a>
                   </li>
               @endif

               {{-- Next Page Link --}}
               @if($bookings->hasMorePages())
                   <li class="page-item">
                       <a class="page-link" href="{{ $bookings->nextPageUrl() }}" rel="next">&raquo;</a>
                   </li>
               @else
                   <li class="page-item disabled">
                       <span class="page-link">&raquo;</span>
                   </li>
               @endif
           </ul>
       </div>
       @endif
        </div>
    </main>

{{-- -- PENAMBAHAN JAVASCRIPT DIMULAI DI SINI -- --}}
<script>
    function sortTable(columnIndex) {
        // Menargetkan ID tabel yang baru
        const table = document.getElementById("bookingTable");
        const tbody = document.getElementById("bookingTableBody");
        const header = table.tHead.rows[0].cells[columnIndex];
        const rows = Array.from(tbody.rows);

        if (rows.length <= 1 && rows[0].cells.length <= 1) {
            return;
        }

        const currentDir = header.classList.contains("asc") ? "desc" : "asc";

        table.querySelectorAll("th.sortable").forEach(th => {
            th.classList.remove("asc", "desc");
            th.querySelector('.sort-icon').className = 'fas fa-sort sort-icon';
        });

        header.classList.add(currentDir);
        if (currentDir === 'asc') {
            header.querySelector('.sort-icon').className = 'fas fa-sort-up sort-icon';
        } else {
            header.querySelector('.sort-icon').className = 'fas fa-sort-down sort-icon';
        }

        const sortedRows = rows.sort((a, b) => {
            const aText = a.cells[columnIndex].textContent.trim();
            const bText = b.cells[columnIndex].textContent.trim();
            
            // Konversi ke angka untuk perbandingan numerik
            const aValue = parseFloat(aText);
            const bValue = parseFloat(bText);

            if (isNaN(aValue) || isNaN(bValue)) {
                return 0;
            }

            if (currentDir === "asc") {
                return aValue - bValue;
            } else {
                return bValue - aValue;
            }
        });

        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }

        tbody.append(...sortedRows);
    }
</script>
{{-- -- PENAMBAHAN JAVASCRIPT SELESAI -- --}}

</body>
</html>