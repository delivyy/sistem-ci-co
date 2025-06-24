<!DOCTYPE html>
<html lang="en">
@php
use Carbon\Carbon;
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Room List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <script src="{{ asset('js/app.js') }}"></script>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1a202c;
        }

        .container {
            max-width: 1200px;
        }

        .header {
            text-align: center;
            margin: 2rem 0;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
        }

        .filters {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .filter-row {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-item {
            flex: 1;
            min-width: 200px;
        }

        .search-item {
            flex: 1.5;
            min-width: 250px;
        }

        .form-control, .form-select {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 0.9rem;
            transition: border-color 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: #3182ce;
            box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
        }

        .search-container {
            position: relative;
        }

        .search-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #718096;
            padding: 0.5rem;
        }

        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .room-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
            border: 1px solid #e2e8f0;
        }

        .room-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }

        .room-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .room-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
            flex: 1;
            padding-right: 1rem;
        }

        .room-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: capitalize;
            white-space: nowrap;
        }

        /* UPDATED: Hanya 2 status */
        .status-tersedia {
            background-color: #fef5e7; /* kuning muda */
            color: #d69e2e; 
        }

        .status-sedang-digunakan {
            background-color: #c6f6d5;
            color: #2f855a; 
        }

        .room-info {
            color: #718096;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .room-floor {
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .events-container {
            max-height: 300px;
            overflow-y: auto;
        }

        .event-item {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 0.75rem;
            border-left: 3px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .event-item:last-child {
            margin-bottom: 0;
        }

        .event-item.active {
            background-color: #c6f6d5;
            border-left-color: #38a169;
        }

        .event-item.upcoming {
            background-color: #feebc8;
            border-left-color: #d69e2e;
        }

        .event-item.completed {
            background-color: #edf2f7;
            border-left-color: #a0aec0;
            opacity: 0.8;
        }

        .event-time {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }

        .event-booking {
            font-size: 0.8rem;
            color: #718096;
        }

        .event-status-badge {
            display: inline-block;
            padding: 0.2rem 0.5rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 500;
            text-transform: uppercase;
            margin-top: 0.25rem;
        }

        .event-status-active {
            background-color: #c6f6d5;
            color: #2f855a;
        }

        .event-status-upcoming {
            background-color: #feebc8;
            color: #b7791f;
        }

        .event-status-completed {
            background-color: #edf2f7;
            color: #718096;
        }

        .no-schedule {
            color: #a0aec0;
            font-style: italic;
            text-align: center;
            padding: 1rem;
            background-color: #f7fafc;
            border-radius: 8px;
            margin-top: 0.5rem;
        }

        .events-count {
            font-size: 0.8rem;
            color: #718096;
            margin-bottom: 0.5rem;
        }

        .pagination {
            justify-content: center;
        }

        .pagination .page-link {
            border: 1px solid #e2e8f0;
            color: #4a5568;
            padding: 0.5rem 0.75rem;
            margin: 0 2px;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .pagination .page-item.active .page-link {
            background-color: #3182ce;
            border-color: #3182ce;
            color: white;
        }

        .pagination .page-link:hover {
            background-color: #edf2f7;
            border-color: #cbd5e0;
        }

        .alert-success {
            background-color: #c6f6d5;
            border: 1px solid #9ae6b4;
            color: #2f855a;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .filter-row {
                flex-direction: column;
            }
            
            .filter-item, .search-item {
                width: 100%;
                min-width: auto;
            }
            
            .rooms-grid {
                grid-template-columns: 1fr;
            }
            
            .room-header {
                flex-direction: column;
                gap: 0.75rem;
                align-items: flex-start;
            }
            
            .room-name {
                padding-right: 0;
            }
        }
    </style>
</head>

<body>
    @include('layouts.app');

    <div class="container py-4">
        <div class="header">
            <h1>Room List</h1>
        </div>

        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <!-- Filters -->
        <div class="filters">
            <div class="filter-row">
                <!-- Date Filter -->
                <div class="filter-item">
                    <form method="GET" action="{{ route('front_office.roomList') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="lantai" value="{{ request('lantai') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <input type="date" name="date" class="form-control" 
                               value="{{ $selectedDate ?? Carbon::now()->toDateString() }}" 
                               onchange="this.form.submit()">
                    </form>
                </div>

                <!-- Floor Filter -->
                <div class="filter-item">
                    <form method="GET" action="{{ route('front_office.roomList') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="date" value="{{ request('date') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <select name="lantai" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Lantai</option>
                            <option value="lantai 1" {{ request('lantai') == 'lantai 1' ? 'selected' : '' }}>Lantai 1</option>
                            <option value="lantai 2" {{ request('lantai') == 'lantai 2' ? 'selected' : '' }}>Lantai 2</option>
                            <option value="lantai 3" {{ request('lantai') == 'lantai 3' ? 'selected' : '' }}>Lantai 3</option>
                            <option value="lantai 4" {{ request('lantai') == 'lantai 4' ? 'selected' : '' }}>Lantai 4</option>
                            <option value="lantai 5" {{ request('lantai') == 'lantai 5' ? 'selected' : '' }}>Lantai 5</option>
                            <option value="lantai 6" {{ request('lantai') == 'lantai 6' ? 'selected' : '' }}>Lantai 6</option>
                            <option value="lantai 7" {{ request('lantai') == 'lantai 7' ? 'selected' : '' }}>Lantai 7</option>
                            <option value="lantai 8" {{ request('lantai') == 'lantai 8' ? 'selected' : '' }}>Lantai 8</option>
                        </select>
                    </form>
                </div>

                <!-- Status Filter - UPDATED: Hanya 2 opsi -->
                <div class="filter-item">
                    <form method="GET" action="{{ route('front_office.roomList') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="date" value="{{ request('date') }}">
                        <input type="hidden" name="lantai" value="{{ request('lantai') }}">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="Check-out" {{ request('status') == 'Check-out' ? 'selected' : '' }}>Tersedia</option>
                            <option value="Check-in" {{ request('status') == 'Check-in' ? 'selected' : '' }}>Sedang Digunakan</option>
                        </select>
                    </form>
                </div>

                <!-- Search -->
                <div class="search-item">
                    <form method="GET" action="{{ route('front_office.roomList') }}">
                        <input type="hidden" name="date" value="{{ request('date') }}">
                        <input type="hidden" name="lantai" value="{{ request('lantai') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <div class="search-container">
                            <input type="text" class="form-control" placeholder="Cari ruangan..." 
                                   name="search" value="{{ request('search') }}">
                            <button type="submit" class="search-btn">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Room Grid -->
        <div class="rooms-grid">
            @foreach($rooms as $room)
                <div class="room-card">
                    <div class="room-header">
                        <h3 class="room-name">{{ $room['name'] }}</h3>
                        @php
                            // UPDATED: Hanya 2 status
                            $status = $room['status'];
                            $statusClass = '';
                            $statusText = '';
                            if($status == 'Check-out') {
                                $statusClass = 'status-tersedia';
                                $statusText = 'Tersedia';
                            } else {
                                // Semua status selain Check-out dianggap sebagai Sedang Digunakan
                                $statusClass = 'status-sedang-digunakan';
                                $statusText = 'Sedang Digunakan';
                            }
                        @endphp
                        <span class="room-status {{ $statusClass }}">{{ $statusText }}</span>
                    </div>
                    
                    <div class="room-info">
                        <div class="room-floor">{{ $room['floor'] }}</div>
                        
                        @if(!empty($room['events']) && $room['has_bookings'])
                            <div class="events-count">
                                {{ count($room['events']) }} Event{{ count($room['events']) > 1 ? 's' : '' }} pada tanggal ini
                            </div>
                            
                            <div class="events-container">
                                @foreach($room['events'] as $event)
                                    <div class="event-item {{ $event['status'] }}">
                                        <div class="event-time">{{ $event['start'] }} - {{ $event['end'] }}</div>
                                        @if(!empty($event['booking_code']))
                                            <div class="event-booking">Booking: {{ $event['booking_code'] }}</div>
                                        @endif
                                        <span class="event-status-badge event-status-{{ $event['status'] }}">
                                            @if($event['status'] == 'active')
                                                Sedang Berlangsung
                                            @elseif($event['status'] == 'upcoming')
                                                Akan Datang
                                            @else
                                                Selesai
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="no-schedule">Tidak ada jadwal pada tanggal ini</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination">
                @for ($page = 1; $page <= $totalPages; $page++)
                    <li class="page-item {{ $currentPage == $page ? 'active' : '' }}">
                        <a class="page-link"
                            href="{{ url()->current() }}?page={{ $page }}&search={{ request('search') }}&lantai={{ request('lantai') }}&status={{ request('status') }}&date={{ request('date') }}&per_page={{ $perPage }}">
                            {{ $page }}
                        </a>
                    </li>
                @endfor
            </ul>
        </nav>
    </div>

    @csrf
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[placeholder="Cari ruangan..."]');
            let timeout = null;
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        const url = new URL(window.location);
                        url.searchParams.set('search', this.value);
                        window.location = url;
                    }, 500);
                });
            }
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>