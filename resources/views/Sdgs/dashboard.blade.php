<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SDGs Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{ asset('css/data.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datepicker.css') }}">
    <script src="{{ asset('js/app.js') }}"></script>
    <style>
        .card-lg, .container, canvas {
            display: block !important;
            visibility: visible !important;
            width: 100% !important;
        }
        #top3Chart,
        #chartKomersial,
        #bidangChart,
        #chartTotal,
        #chartSektor {
            width: 100 !important;
            height: auto !important;
            max-height: 500px;
        }  
        #chartNonKomersialContainer canvas {
            margin-left: 115px;
            margin-top: 40px;
            width: 400px !important;
            height: auto !important;
            max-height: 600px;
        } 
        .flatpickr-calendar {
            font-family: 'Barlow', sans-serif;
            background: #ffffff;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 240px;
            padding: 0.5rem;
            z-index: 10000;
        }
        .other-sdgs-container {
            margin-top: 20px;
        }
        .other-sdgs-title {
            font-family: 'Barlow', sans-serif;
            font-size: 16px;
            color: #2d3748;
            margin-bottom: 10px;
        }
        .other-sdgs-list {
            list-style: none;
            padding: 0;
        }
        .other-sdgs-list li {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-family: 'Barlow', sans-serif;
            font-size: 12px;
            color: #2d3748;
        }
        .other-sdgs-list li .sdg-color {
            width: 12px;
            height: 12px;
            margin-right: 8px;
            border-radius: 50%;
        }
        .sdgs-card, .sdgs-value {
            text-decoration: none !important;
        }

        @media (max-width: 1300px) {
            #chartNonKomersialContainer canvas {
                margin-left: 40px;
                margin-top: 40px;
                width: 400px !important;
                height: auto !important;
                max-height: 600px;
                } 
            }

        @media (max-width: 1024px) {
            #chartNonKomersialContainer canvas {
                margin: 0px auto !important;
                width: 100% !important;
                max-width: 260px;
                max-height: 220px;
            } 
        }
        
        @media (max-width: 480px) {
            .flatpickr-calendar {
                width: 220px;
                padding: 0.4rem;
            }

            #chartBidang,
            #chartKomersial,
            #chartNonKomersial {
                width: 100% !important;
                max-width: 260px;
                height: auto !important;
                margin: 0 auto;
                display: block;
            }

            #chartTotal,
            #chartSektor {
                width: 100% !important;
                max-height: 300px !important;
                margin: 0 auto;
                display: block;
            }

            #top3Chart {
                width: 100% !important;
                max-height: 400px !important;
                margin: 0 auto;
                display: block;
            }

            #chartNonKomersialContainer canvas {
                margin: 0 auto !important;
                width: 100% !important;
                max-width: 260px;
                max-height: 220px;
            }

            .chart-container {
                text-align: center;
            }

            .chart-legend {
                text-align: center;
                font-size: 12px;
            }
                }
            </style>
</head>
<body>
    @include('layouts.header')
    <main class="container">
        <div class="subheader">
            <div class="subheader-content">
                <h3 class="subheader-title">Dashboard</h3>
                <p class="subheader-subtitle">Hi, {{ Auth::user()->name }}. Welcome back!</p>
            </div>
            <div class="subheader-addon">
                <div class="filter">
                    <div class="filter-icon">
                        <i class="fa-regular fa-calendar"></i>
                    </div>
                    <div class="filter-info">
                        <span class="filter-title">Filter Periode</span>
                        <span class="filter-date" data-dates="">Tanggal Hari Ini</span>
                    </div>
                </div>
                <div class="confirm-btn" id="confirmDate">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="confirm-btn reset-btn" id="resetDate">
                    <i class="fa-solid fa-database"></i>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-half">
                <div class="card-row">
                    <div class="card card-bg">
                        <h5 class="display-subtitle">Total Acara</h5>
                        <h2 class="display-title total-acara" id="totalAcara"></h2>
                    </div>
                    <div class="card card-bg">
                        <h5 class="display-subtitle">Total Pengunjung</h5>
                        <h2 class="display-title total-pengunjung" id="totalPengunjung"></h2>
                    </div>
                </div>
                <div class="card card-lg">
                    <h3 class="chart-title">Top 3 SDGs</h3>
                    <canvas id="top3Chart" height="200"></canvas>
                    <div id="otherSdgsContainer" class="other-sdgs-container">
                        <h4 class="other-sdgs-title">SDG Lainnya</h4>
                        <ul id="otherSdgsList" class="other-sdgs-list"></ul>
                    </div>
                </div>
            </div>
            <div class="col-half">
                <div class="card card-lg card-komersial">
                    <div class="chart-header">
                        <h3 class="chart-title">Fungsional MCC</h3>
                        <div class="toggle">
                            <div class="toggle-input">
                                <input type="radio" name="chartKomersialToggle" id="komersialCount" value="count" />
                                <label for="komersialCount">Jumlah</label>
                            </div>
                            <div class="toggle-input">
                                <input type="radio" name="chartKomersialToggle" id="komersialPercent" value="percent" checked />
                                <label for="komersialPercent">Persen</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-half">
                        <div id="chartNonKomersialContainer">
                            <canvas id="chartNonKomersial" style="max-width: 30rem;"></canvas>
                        </div>
                    </div>
                    <div class="col-half">
                        <div id="legendNonKomersial" class="legend-container"></div>
                    </div>
                </div>
                <div class="card card-lg card-bidang">
                    <div class="chart-header">
                        <h3 class="chart-title">Bidang</h3>
                        <div class="toggle">
                            <div class="toggle-input">
                                <input type="radio" name="chartBidangToggle" value="count" />
                                <label>Jumlah</label>
                            </div>
                            <div class="toggle-input">
                                <input type="radio" name="chartBidangToggle" value="percent" checked />
                                <label>Persen</label>
                            </div>
                        </div>
                    </div>
                    <div class="chart-hstack" style="margin-top: -50px;">
                        <canvas id="bidangChart"></canvas>
                        <div class="legend-container" id="legend-container">
                            <ul id="bidangLabels"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div><br>
        <div class="card card-lg">
            <h3 class="chart-title">Total Event & Pengunjung</h3>
            <canvas id="chartTotal" height="100"></canvas>
        </div><br>
        <div class="card card-lg">
            <h3 class="chart-title">Sub Sektor Ekonomi Kreatif</h3>
            <canvas id="chartSektor" height="300"></canvas>
        </div> <br>
        <div class="card card-lg" style="position: relative;">
            <div class="chart-header">
                <h3 class="chart-title">Akumulasi Pengunjung</h3> <br>      
                <div class="dropdown-wrapper">
                    <button class="dropdown-toggle" onclick="toggleDropdown()">Pilih Lantai <span class="arrow">&#9662;</span></button>
                    <ul class="dropdown-menu" id="dropdownMenu">
                        <li onclick="filterLantai('all')">General</li>
                        <li onclick="filterLantai('2')">Lantai 2</li>
                        <li onclick="filterLantai('3')">Lantai 3</li>
                        <li onclick="filterLantai('4')">Lantai 4</li>
                        <li onclick="filterLantai('5')">Lantai 5</li>
                        <li onclick="filterLantai('6')">Lantai 6</li>
                        <li onclick="filterLantai('7')">Lantai 7</li>
                        <li onclick="filterLantai('8')">Lantai 8</li>
                    </ul>
                </div>
                <canvas id="chartAkumulasi" style="max-height: 380px; height: 300px;"></canvas>
            </div>
        </div>
        <h4 class="sdgs-title">SDGs Event</h4>
        <div class="toggle">
            <div class="toggle-input">
                <input type="radio" name="sdgsToggle" value="count" checked />
                <label>Jumlah</label>
            </div>
            <div class="toggle-input">
                <input type="radio" name="sdgsToggle" value="percent" />
                <label>Persen</label>
            </div>
        </div>
        <div id="sdgsGridContainer">
            <div class="sdgs-grid">
                @forelse ($sdgs as $sdg)
                    <a href="{{ route('all.detail.Dashboard', ['sdg_id' => $sdg->id_sdgs]) . (request()->query('start_date') && request()->query('end_date') ? '?start_date=' . request()->query('start_date') . '&end_date=' . request()->query('end_date') : '') }}" style="text-decoration: none;">
                        <div class="sdgs-card">
                            <img class="sdgs-img" src="{{ asset('logo_sdg/' . $sdg->id_sdgs . '.png') }}" alt="{{ $sdg->id_sdgs }};">
                            <span class="sdgs-value">{{ $sdg->id_sdgs }}</span>
                        </div>
                    </a>
                @empty
                    <p>Tidak ada data SDGs untuk ditampilkan.</p>
                @endforelse
            </div>
        </div>
    </main>
    <footer class="footer"></footer>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@3.0.1/dist/chartjs-plugin-annotation.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>