<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SDGController extends Controller
{
    public function __construct()
    {
        $this->middleware('trigger.sync.booking'); 
    }

    public function decryptbooking(Request $request){
        $encryptedCode = $request->query('code');
    
        if (!$encryptedCode) {
            return response()->json(['error' => 'No code provided'], 400);
        }
        
        try {
            $bookingId = \App\Helpers\BookingEncryption::decrypt($encryptedCode);
            return response()->json(['booking_id' => $bookingId]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid code'], 400);
        }
    }

    public function sdgsQuiz()
    {
        return view('Sdgs.quiz');
    }

    public function dashboard(Request $request)
    {
        $sdgs = DB::table('sdgs')->get();
        return view('Sdgs.dashboard', [
            'sdgs' => $sdgs
        ]);
    }

    public function detailDashboard(Request $request)
    {
        $sdgId = $request->route('sdg_id') ?? $request->query('sdg_id');
        if (!$sdgId) {
            abort(404, 'SDG ID tidak ditemukan');
        }

        $sdg = DB::table('sdgs')->where('id_sdgs', $sdgId)->first();
        if (!$sdg) {
            abort(404, 'SDG tidak ditemukan');
        }

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = Booking::query()->where('status', 'booked');
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
            Log::info('Filter tanggal diterapkan', ['start_date' => $startDate, 'end_date' => $endDate]);
        } else {
            Log::info('Tidak ada filter tanggal, menampilkan semua data');
        }

        $allBookings = $query->get();

        $bookings = $allBookings->filter(function ($booking) use ($sdgId) {
            $sdgData = $booking->sdgs_data;
            if (is_string($sdgData)) {
                $sdgData = json_decode($sdgData, true);
            }
            if (is_array($sdgData)) {
                foreach ($sdgData as $sdgEntry) {
                    if (is_array($sdgEntry) && isset($sdgEntry[0]) && $sdgEntry[0] === $sdgId) {
                        return true;
                    }
                }
            }
            return false;
        })->values();

        return view('Sdgs.detail', compact('sdg', 'bookings'));
    }

    public function result()
    {
        $sdgs = DB::table('sdgs')->get();
        return view('Sdgs.result', ['sdgs' => $sdgs]);
    }

    public function saveSDGResult(Request $request)
    {
        try {
            $bookingId = $request->input('booking_id');
            $sdgResults = $request->input('sdg_results', []);
    
            Log::info('ambil booking_id:', ['booking_id' => $bookingId]);
            Log::info('ambil sdg_results:', ['sdg_results' => $sdgResults]);
    
            $today = now()->toDateString();
            $booking = Booking::where('kode_booking', $bookingId)
                  ->where('tanggal', $today)
                  ->first();
    
            if (!$booking) {
                Log::warning('Booking ID not found:', ['booking_id' => $bookingId]);
                return response()->json([
                    'success' => false,
                    'message' => 'Booking ID tidak ditemukan'
                ], 404);
            }
    
            if (!empty($booking->sdgs_data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah pernah mengisi kusioner untuk kode booking ini'
                ], 400);
            }
    
            if (count($sdgResults) === 1 && $sdgResults[0]['sdg'] === 'SDG None') {
                $booking->sdgs_data = json_encode([['SDG None']]);
                $booking->save();
                
                Log::info('SDG None saved:', ['booking_id' => $bookingId]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Data SDG None berhasil disimpan'
                ]);
            }
    
            if (count($sdgResults) > 0) {
                $sdgIds = array_column($sdgResults, 'sdg');
                $sdgData = DB::table('sdgs')
                    ->whereIn('id_sdgs', $sdgIds)
                    ->get();
    
                $mergedResults = [];
                foreach ($sdgResults as $result) {
                    if (!isset($result['sdg'])) {
                        Log::warning('Invalid sdg result format:', ['result' => $result]);
                        continue;
                    }
                    $sdgInfo = $sdgData->firstWhere('id_sdgs', $result['sdg']);
                    if ($sdgInfo) {
                        $mergedResults[] = [
                            $sdgInfo->id_sdgs,
                            $sdgInfo->deskripsi,
                        ];
                    } else {
                        Log::warning('SDG not found in sdgs table:', ['sdg_id' => $result['sdg']]);
                    }
                }
    
                $booking->sdgs_data = !empty($mergedResults) ? json_encode($mergedResults) : json_encode([]);
                $saveResult = $booking->save();
    
                if ($saveResult) {
                    Log::info('SDG data successfully saved:', ['booking_id' => $bookingId, 'sdgs_data' => $booking->sdgs_data]);
                } else {
                    Log::warning('Failed to save booking:', ['booking_id' => $bookingId]);
                }
            } else {
                Log::info('No sdg_results to save:', ['booking_id' => $bookingId]);
                $booking->sdgs_data = json_encode([['SDG None']]);
                $booking->save();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Data SDG berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving SDG result: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function storeBookingData(Request $request)
    {
        try {
            $data = $request->all();
            $booking = Booking::create([
                'nama_event' => $data['nama_event'],
                'kode_booking' => $data['kode_booking'],
                'nama_organisasi' => $data['nama_organisasi'],
                'tanggal' => $data['tanggal'],
                'waktu_mulai' => $data['waktu_mulai'],
                'waktu_selesai' => $data['waktu_selesai'],
                'nama_pic' => $data['nama_pic'],
                'status' => $data['status'],
                'bidang' => $data['bidang'],
                'kegiatan' => $data['kegiatan'],
                'komersial/non' => $data['komersial/non'],
                'lantai' => $data['lantai'],
                'ruangan' => $data['ruangan'],
                'jumlah_peserta' => $data['jumlah_peserta'],
            ]);

            return response()->json(['message' => 'Booking data saved successfully']);
        } catch (\Exception $e) {
            Log::error('Error storing booking data: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to save booking data'], 500);
        }
    }

    public function getSDGData(Request $request)
    {
        try {
            $sdgIds = $request->input('sdgs');

            if (empty($sdgIds) || !is_array($sdgIds)) {
                return response()->json(['error' => 'Invalid SDG IDs'], 400);
            }

            $sdgs = DB::table('sdgs')
                    ->whereIn('id_sdgs', $sdgIds)
                    ->get();

            if ($sdgs->isEmpty()) {
                return response()->json(['error' => 'No SDG data found'], 404);
            }

            return response()->json($sdgs);
        } catch (\Exception $e) {
            Log::error('Error fetching SDG data: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch SDG data'], 500);
        }
    }

    public function getAkumulasiPengunjung(Request $request)
    {
        try {
            // Struktur master dengan kunci teks lengkap dan nilai default 0
            $masterStrukturRuangan = [
                // --- TAMBAHKAN LANTAI 1 DI SINI ---
                'Lantai 1' => ["Stage Outdoor" => 0,],
                'Lantai 2' => ["Main Hall" => 0,"Open Public Space Utara" => 0, "Ruangan Broadcast" => 0, "Ruang Podcast" => 0, "Teras Utara" => 0, "Teras Selatan" => 0, "Teras Tengah" => 0, "Creative City Planing Galery" => 0, ],
                'Lantai 3' => ["Food Lab" => 0, "Ruang Kelas" => 0, "Ruang Meeting" => 0, "Open Public Space Utara 1" => 0, "Multifunction Room" => 0, "Open Public Space Utara 2" => 0, "Open Public Space Barat" => 0, "Multi Purpose Area" => 0],
                'Lantai 4' => ["Multi Purpose Area" => 0, "Open Public Space Utara" => 0, "Lab Komputer" => 0, "Coworking Space 1" => 0, "Studio Musik & Recording" => 0 ],
                'Lantai 5' => ["Amphitheater 1" => 0, "Amphitheater 2" => 0, "Studio Foto" => 0, "Coworking Space 2" => 0, "Backstage amphitheater 2" => 0, "Outdoor Lounge" => 0, "Multi Purpose Area" => 0, "Open Public Space Utara" => 0],
                'Lantai 6' => ["Perpustakaan 1" => 0, "Open Public Space Utara" => 0, "Ruang Live Market" => 0],
                'Lantai 7' => ["Auditorium" => 0, "Ruang VIP" => 0, "Ruang Transit" => 0, "Backstage" => 0, "Open Public Space Utara" => 0, "Free Function Lounge" => 0],
                'Lantai 8' => ["Rooftop" => 0],
            ];
    
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            
            if ($startDate || $endDate) {
                $request->validate([
                    'start_date' => 'nullable|date',
                    'end_date' => 'nullable|date|after_or_equal:start_date'
                ]);
            }
            
            $query = Booking::select('lantai', 'ruangan', 'jumlah_peserta')
                ->where('status', 'Booked');
            
            if ($startDate) {
                $query->whereDate('tanggal', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('tanggal', '<=', $endDate);
            }
            
            $bookings = $query->get();
            
            $dataFromDb = [];
            foreach ($bookings as $booking) {
                $lantai = $booking->lantai;
                $ruangan = $booking->ruangan;
                
                if (empty(trim($lantai)) || empty(trim($ruangan))) {
                    continue;
                }
                
                $jumlahPeserta = (int) ($booking->jumlah_peserta ?? 0);
                
                if (!isset($dataFromDb[$lantai])) { $dataFromDb[$lantai] = []; }
                if (!isset($dataFromDb[$lantai][$ruangan])) { $dataFromDb[$lantai][$ruangan] = 0; }
                $dataFromDb[$lantai][$ruangan] += $jumlahPeserta;
            }
    
            $finalData = array_replace_recursive($masterStrukturRuangan, $dataFromDb);
            
            // Urutkan lantai secara numerik
            uksort($finalData, function($a, $b) {
                $numA = (int) preg_replace('/\D/', '', $a);
                $numB = (int) preg_replace('/\D/', '', $b);
                return $numA - $numB;
            });
            
            // Urutkan ruangan di tiap lantai berdasarkan jumlah pengunjung
            foreach ($finalData as &$ruanganData) {
                arsort($ruanganData);
            }
            
            return response()->json([
                'success' => true,
                'data' => $finalData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data pengunjung: ' . $e->getMessage());
            return response()->json([ 'success' => false, 'message' => 'Terjadi kesalahan saat mengambil data.' ], 500);
        }
    }
        
        /**
         * Mengambil daftar lantai yang tersedia berdasarkan filter periode
         */
        public function getAvailableFloors(Request $request)
    {
        try {
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            
            $query = Booking::select('lantai')->distinct()->whereNotNull('lantai')->where('lantai', '!=', '');
            
            if ($startDate) { $query->whereDate('tanggal', '>=', $startDate); }
            if ($endDate) { $query->whereDate('tanggal', '<=', $endDate); }
            
            $floors = $query->orderByRaw('CAST(REGEXP_REPLACE(lantai, "[^0-9]", "") AS UNSIGNED)')
                           ->pluck('lantai')
                           ->filter(function($lantai) {
                               return !empty(trim($lantai));
                           })
                           ->values();
            
            return response()->json([ 'success' => true, 'data' => $floors ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting available floors: ' . $e->getMessage());
            return response()->json([ 'success' => false, 'message' => 'Failed to get floors data' ], 500);
        }
    }        
        /**
         * Mengambil daftar ruangan berdasarkan lantai dan filter periode
         */
        public function getRoomsByFloor(Request $request)
        {
            try {
                $lantai = $request->query('lantai');
                $startDate = $request->query('start_date');
                $endDate = $request->query('end_date');
                
                $query = Booking::select('ruangan')
                    ->distinct()
                    ->whereNotNull('ruangan')
                    ->where('ruangan', '!=', '');
                
                if ($lantai) {
                    $query->where('lantai', $lantai);
                }
                
                // Filter berdasarkan tanggal jika ada
                if ($startDate) {
                    $query->whereDate('tanggal', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('tanggal', '<=', $endDate);
                }
                
                $rooms = $query->orderBy('ruangan', 'asc')
                            ->pluck('ruangan')
                            ->filter(function($ruangan) {
                                return !empty(trim($ruangan));
                            })
                            ->values();
                
                return response()->json([
                    'success' => true,
                    'data' => $rooms
                ]);
                
            } catch (\Exception $e) {
                Log::error('Error getting rooms by floor: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get rooms data'
                ], 500);
            }
        }
        
        /**
         * Detail lantai dengan filter periode
         */
        public function detailLantai(Request $request, $lantai) 
        {
            try {
                $startDate = $request->query('start_date');
                $endDate = $request->query('end_date');
                
                $query = Booking::where('lantai', $lantai)
                            ->where('status', 'Booked');
                
                // Filter berdasarkan tanggal jika ada
                if ($startDate) {
                    $query->whereDate('tanggal', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('tanggal', '<=', $endDate);
                }
                
                $bookings = $query->orderBy('tanggal', 'desc')
                                ->paginate(10);
                
                // Hitung statistik untuk lantai ini
                $stats = [
                    'total_events' => $bookings->total(),
                    'total_pengunjung' => $query->sum('jumlah_peserta'),
                    'periode' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate
                    ]
                ];
                
                return view('Sdgs.lantai_detail', [
                    'lantai' => $lantai,
                    'bookings' => $bookings,
                    'stats' => $stats,
                    'filter' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate
                    ]
                ]);
                
            } catch (\Exception $e) {
                Log::error('Error in detailLantai: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data lantai.');
            }
        }
        
        /**
         * Detail ruangan dengan filter periode
         */
        public function roomDetail(Request $request, $lantai, $ruangan)
        {
            try {
                $startDate = $request->query('start_date');
                $endDate = $request->query('end_date');
                
                $query = Booking::where('lantai', $lantai)
                            ->where('ruangan', $ruangan)
                            ->where('status', 'Booked');
                
                // Filter berdasarkan tanggal jika ada
                if ($startDate) {
                    $query->whereDate('tanggal', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('tanggal', '<=', $endDate);
                }
                
                $events = $query->orderBy('tanggal', 'desc')
                            ->paginate(10);
                
                // Hitung statistik untuk ruangan ini
                $stats = [
                    'total_events' => $events->total(),
                    'total_pengunjung' => $query->sum('jumlah_peserta'),
                    'average_pengunjung' => $events->total() > 0 ? round($query->sum('jumlah_peserta') / $events->total(), 2) : 0,
                    'periode' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate
                    ]
                ];
                
                return view('Sdgs.Ruangan_detail', [
                    'lantai' => $lantai,
                    'ruangan' => $ruangan,
                    'events' => $events,
                    'stats' => $stats,
                    'filter' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate
                    ]
                ]);
                
            } catch (\Exception $e) {
                Log::error('Error in roomDetail: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data ruangan.');
            }
        }

    public function fetchAndStoreBookingData(Request $request)
    {
        try {
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            Log::info('Fetching booking data from database', [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            $query = Booking::query()->where('status', 'booked');

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            } else {
                Log::info('No date range provided, fetching all booking data');
            }

            $totalAcaraKeseluruhan = $query->count();
            $totalPengunjungKeseluruhan = $query->sum('jumlah_peserta') ?? 0;

            Log::info('Booking data retrieved from database', [
                'total_acara_keseluruhan' => $totalAcaraKeseluruhan,
                'total_pengunjung_keseluruhan' => $totalPengunjungKeseluruhan,
            ]);

            return response()->json([
                'message' => 'Data booking berhasil disimpan.',
                'total_acara_baru' => 0,
                'total_pengunjung_baru' => 0,
                'total_acara_keseluruhan' => $totalAcaraKeseluruhan,
                'total_pengunjung_keseluruhan' => $totalPengunjungKeseluruhan,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching booking data from database: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_params' => $request->query(),
            ]);
            return response()->json([
                'message' => 'Gagal mengambil data booking dari database',
                'total_acara_baru' => 0,
                'total_pengunjung_baru' => 0,
                'total_acara_keseluruhan' => 0,
                'total_pengunjung_keseluruhan' => 0,
            ], 500);
        }
    }

    public function getKomersialData(Request $request)
    {
        try {
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            Log::info('Fetching komersial data', [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            $query = DB::table('booking')->where('status', 'booked');
            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }
            
            $uniqueValues = $query->select('komersial/non')
                ->distinct()
                ->pluck('komersial/non')
                ->toArray();
            Log::info('Unique values in komersial/non column', [
                'values' => $uniqueValues
            ]);

            $total = $query->count();

            if ($total === 0) {
                Log::info('No booking data found for komersial data query');
                return response()->json([
                    'jumlah' => [
                        'komersial' => 0,
                        'Empowerment' => 0, 
                    ],
                    'persentase' => [
                        'komersial' => 0,
                        'Empowerment' => 0, 
                    ]
                ]);
            }

            $dataQuery = DB::table('booking')
                ->select('komersial/non', DB::raw('COUNT(*) as total'))
                ->where('status', 'booked');

            if ($startDate && $endDate) {
                $dataQuery->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $data = $dataQuery->groupBy('komersial/non')->pluck('total', 'komersial/non');

            $totalKomersial = $data['komersial'] ?? 0;
            $totalEmpowerment = $data['non-komersial'] ?? 0;

            Log::info('Komersial data results', [
                'total' => $total,
                'komersial' => $totalKomersial,
                'Empowerment' => $totalEmpowerment 
            ]);

            $unaccounted = $total - ($totalKomersial + $totalEmpowerment);
            if ($unaccounted > 0) {
                Log::warning('Some booking records have unexpected komersial/non values', [
                    'unaccounted_count' => $unaccounted
                ]);
            }

            $percentKomersial = $total > 0 ? round(($totalKomersial / $total) * 100, 2) : 0;
            $percentEmpowerment = $total > 0 ? round(($totalEmpowerment / $total) * 100, 2) : 0;

            return response()->json([
                'jumlah' => [
                    'komersial' => $totalKomersial,
                    'Empowerment' => $totalEmpowerment, 
                ],
                'persentase' => [
                    'komersial' => $percentKomersial,
                    'Empowerment' => $percentEmpowerment, 
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getKomersialData: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data komersial',
                'jumlah' => [
                    'komersial' => 0,
                    'Empowerment' => 0, 
                ],
                'persentase' => [
                    'komersial' => 0,
                    'Empowerment' => 0, 
                ]
            ], 500);
        }
    }

    public function getBidangData(Request $request)
    {
        try {
            $query = DB::table('booking')->where('status', 'booked');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $bidangData = $query
                ->select('bidang', DB::raw('count(*) as jumlah'))
                ->groupBy('bidang')
                ->get();

            if ($bidangData->isEmpty()) {
                return response()->json([
                    'labels' => [],
                    'jumlah' => [],
                    'persentase' => [],
                ]);
            }

            $total = $bidangData->sum('jumlah');
            $labels = $bidangData->pluck('bidang')->toArray();
            $jumlah = $bidangData->pluck('jumlah')->toArray();
            $persentase = $total > 0
                ? $bidangData->map(fn($item) => round(($item->jumlah / $total) * 100, 2))->toArray()
                : array_fill(0, count($labels), 0);

            return response()->json([
                'labels' => $labels,
                'jumlah' => $jumlah,
                'persentase' => $persentase,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching bidang data: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data bidang',
                'labels' => [],
                'jumlah' => [],
                'persentase' => [],
            ], 500);
        }
    }

    public function getSubsektorData(Request $request)
    {
        try {
            $subsektorList = [
                'Arsitektur',
                'Film',
                'Fotografi',
                'Kriya',
                'Kuliner',
                'Seni Rupa',
                'Produk',
                'Aplikasi',
                'Game',
                'TV & Radio',
                'Fashion',
                'Pertunjukan',
                'Desain Interior',
                'Periklanan',
                'Penerbitan',
                'DKV',
                'Musik'
            ];

            $query = DB::table('booking')->where('status', 'booked');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $subsektorData = $query
                ->select('kegiatan', DB::raw('count(*) as jumlah'))
                ->whereIn('kegiatan', $subsektorList)
                ->groupBy('kegiatan')
                ->get();

            $data = array_fill_keys($subsektorList, 0);

            foreach ($subsektorData as $item) {
                if (in_array($item->kegiatan, $subsektorList)) {
                    $data[$item->kegiatan] = $item->jumlah;
                }
            }

            $labels = $subsektorList;
            $values = array_values($data);

            return response()->json([
                'labels' => $labels,
                'data' => $values,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching subsektor data: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data subsektor',
                'labels' => [],
                'data' => [],
            ], 500);
        }
    }

    public function getSDGsData(Request $request)
    {
        try {
            Log::info('Starting getSDGsData', [
                'start_date' => $request->query('start_date'),
                'end_date' => $request->query('end_date'),
                'timestamp' => now()->toDateTimeString()
            ]);

            $allSdgs = DB::table('sdgs')->get()->keyBy('id_sdgs');
            Log::info('Fetched sdgs table', [
                'count' => $allSdgs->count(),
                'ids' => $allSdgs->keys()->toArray()
            ]);

            if ($allSdgs->isEmpty()) {
                Log::warning('Tabel sdgs kosong atau tidak ditemukan data');
                $responseData = [];
                foreach (array_keys($allSdgs->toArray()) as $id) {
                    $imagePath = file_exists(public_path("logo_sdg/{$id}.png"))
                        ? asset("logo_sdg/{$id}.png")
                        : asset("logo_sdg/default.png");
                    $responseData[] = [
                        'id' => $id,
                        'name' => 'Tidak Ada Data',
                        'count' => 0,
                        'percentage' => 0,
                        'image' => $imagePath
                    ];
                }
                return response()->json([
                    'success' => true,
                    'data' => $responseData
                ]);
            }

            $query = Booking::whereNotNull('sdgs_data')->where('status', 'booked');
            if ($request->query('start_date') && $request->query('end_date')) {
                $query->whereBetween('tanggal', [$request->query('start_date'), $request->query('end_date')]);
            } else {
                Log::info('No date range provided, fetching all SDGs data');
            }

            $bookings = $query->get();
            Log::info('Bookings fetched', [
                'count' => $bookings->count(),
                'sample' => $bookings->take(2)->map(function ($booking) {
                    return ['kode_booking' => $booking->kode_booking, 'sdgs_data' => $booking->sdgs_data];
                })->toArray()
            ]);

            $sdgCounts = array_fill_keys($allSdgs->keys()->toArray(), 0);
            $totalSDGs = 0;

            foreach ($bookings as $booking) {
                if (empty($booking->sdgs_data)) {
                    Log::warning('sdgs_data is empty for booking', [
                        'kode_booking' => $booking->kode_booking,
                        'sdgs_data' => $booking->sdgs_data
                    ]);
                    continue;
                }

                $sdgs = $booking->sdgs_data;
                if (is_string($sdgs)) {
                    $decodedSdgs = json_decode($sdgs, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Log::warning('Invalid JSON in sdgs_data', [
                            'kode_booking' => $booking->kode_booking,
                            'sdgs_data' => $sdgs,
                            'json_error' => json_last_error_msg()
                        ]);
                        continue;
                    }
                    $sdgs = $decodedSdgs ?: [];
                } elseif (!is_array($sdgs)) {
                    Log::warning('sdgs_data is neither string nor array', [
                        'kode_booking' => $booking->kode_booking,
                        'sdgs_data' => $sdgs
                    ]);
                    continue;
                }

                if (empty($sdgs)) {
                    Log::warning('Empty or invalid sdgs_data after processing', [
                        'kode_booking' => $booking->kode_booking,
                        'sdgs_data' => $booking->sdgs_data
                    ]);
                    continue;
                }

                foreach ($sdgs as $sdg) {
                    if (is_array($sdg) && isset($sdg[0])) {
                        $sdgId = $sdg[0]; 
                        if (isset($sdgCounts[$sdgId])) {
                            $sdgCounts[$sdgId]++;
                            $totalSDGs++;
                        } else {
                            Log::warning('SDG ID not found in sdgs table', [
                                'sdg_id' => $sdgId,
                                'kode_booking' => $booking->kode_booking
                            ]);
                        }
                    } else {
                        Log::warning('Invalid SDG format', [
                            'kode_booking' => $booking->kode_booking,
                            'sdg' => $sdg
                        ]);
                    }
                }
            }

            Log::info('SDG counts calculated', [
                'sdg_counts' => $sdgCounts,
                'total_sdgs' => $totalSDGs
            ]);

            $responseData = [];
            foreach ($allSdgs as $sdgId => $sdgInfo) {
                $count = $sdgCounts[$sdgId] ?? 0;
                $percentage = $totalSDGs > 0 ? round(($count / $totalSDGs) * 100, 2) : 0;

                $imagePath = file_exists(public_path("logo_sdg/{$sdgId}.png"))
                    ? asset("logo_sdg/{$sdgId}.png")
                    : asset("logo_sdg/default.png");

                $responseData[] = [
                    'id' => $sdgId,
                    'name' => $sdgInfo->deskripsi ?? $sdgId,
                    'count' => $count,
                    'percentage' => $percentage,
                    'image' => $imagePath
                ];
            }

            Log::info('Returning SDGs data', [
                'response_data' => $responseData
            ]);

            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching SDGs data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_params' => $request->query()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan saat mengambil data SDGs: ' . $e->getMessage()
            ], 500);
        }
    }

    private function convertSdgIdToNumeric($sdgId)
    {
        if (preg_match('/^SDG(\d{2})$/', $sdgId, $matches)) {
            return (int)$matches[1]; 
        }
        return null; 
    }

    public function getTop3SDGs(Request $request)
    {
        try {
            Log::info('Starting getTop3SDGs', [
                'start_date' => $request->query('start_date'),
                'end_date' => $request->query('end_date'),
                'timestamp' => now()->toDateTimeString()
            ]);

            $allSdgs = DB::table('sdgs')->get()->keyBy('id_sdgs');
            if ($allSdgs->isEmpty()) {
                Log::warning('No SDGs found in sdgs table');
                return response()->json([
                    'success' => false,
                    'error' => 'Tabel SDGs kosong',
                    'data' => [
                        'top3' => [
                            ['id' => '1', 'name' => 'Tidak Ada Data', 'count' => 0, 'percentage' => 0],
                            ['id' => '2', 'name' => 'Tidak Ada Data', 'count' => 0, 'percentage' => 0],
                            ['id' => '3', 'name' => 'Tidak Ada Data', 'count' => 0, 'percentage' => 0]
                        ],
                        'others' => []
                    ]
                ], 200);
            }
            Log::info('All SDGs fetched', ['count' => $allSdgs->count(), 'keys' => $allSdgs->keys()->toArray()]);

            $sdgCounts = array_fill_keys($allSdgs->keys()->toArray(), 0);
            $totalBookings = 0;

            $query = Booking::whereNotNull('sdgs_data')->where('status', 'booked');

            if ($request->query('all') == 'true') {
                Log::info('All data requested, no date filter applied');
            } else if ($request->query('start_date') && $request->query('end_date')) {
                $query->whereBetween('tanggal', [$request->query('start_date'), $request->query('end_date')]);
                Log::info('Date filter applied', ['start_date' => $request->query('start_date'), 'end_date' => $request->query('end_date')]);
            } else {
                $query->whereDate('tanggal', now()->toDateString());
                Log::info('Default today filter applied', ['today' => now()->toDateString()]);
            }

            $bookings = $query->get();
            Log::info('Bookings fetched', ['count' => $bookings->count()]);

            foreach ($bookings as $booking) {
                $sdgs = $booking->sdgs_data;

                if (is_string($sdgs)) {
                    $decodedSdgs = json_decode($sdgs, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Log::warning('Failed to decode sdgs_data', [
                            'kode_booking' => $booking->kode_booking,
                            'sdgs_data' => $sdgs,
                            'json_error' => json_last_error_msg()
                        ]);
                        continue;
                    }
                    $sdgs = $decodedSdgs ?: [];
                } elseif (!is_array($sdgs)) {
                    Log::warning('sdgs_data is neither string nor array', [
                        'kode_booking' => $booking->kode_booking,
                        'sdgs_data' => $sdgs
                    ]);
                    continue;
                }

                if (!is_array($sdgs) || empty($sdgs)) {
                    Log::warning('Invalid or empty sdgs_data', [
                        'kode_booking' => $booking->kode_booking,
                        'sdgs_data' => $sdgs
                    ]);
                    continue;
                }

                $processedSdgs = [];
                foreach ($sdgs as $sdg) {
                    if (is_array($sdg) && !empty($sdg)) {
                        if (is_array($sdg[0])) {
                            $processedSdgs[] = $sdg[0];
                        } else {
                            $processedSdgs[] = $sdg;
                        }
                    }
                }

                if (empty($processedSdgs)) {
                    Log::warning('No valid SDG data after processing', [
                        'kode_booking' => $booking->kode_booking,
                        'sdgs_data' => $booking->sdgs_data
                    ]);
                    continue;
                }

                foreach ($processedSdgs as $sdg) {
                    if (!isset($sdg[0])) {
                        Log::warning('Invalid SDG format in sdgs_data (missing id)', [
                            'kode_booking' => $booking->kode_booking,
                            'sdg' => $sdg
                        ]);
                        continue;
                    }

                    $sdgId = $sdg[0];
                    if (isset($sdgCounts[$sdgId])) {
                        $sdgCounts[$sdgId]++;
                        $totalBookings++;
                    } else {
                        Log::warning('SDG ID not found in sdgs table', [
                            'sdg_id' => $sdgId,
                            'kode_booking' => $booking->kode_booking
                        ]);
                    }
                }
            }

            Log::info('SDG counts', ['counts' => $sdgCounts, 'total' => $totalBookings]);
            $sdgData = [];
            foreach ($allSdgs as $id => $sdgInfo) {
                $count = $sdgCounts[$id] ?? 0;
                $sdgData[] = [
                    'id' => $id,
                    'name' => $sdgInfo->nama_sdg ?? $id,
                    'count' => $count,
                    'percentage' => $totalBookings > 0 ? round(($count / $totalBookings) * 100, 2) : 0.0
                ];
            }

            usort($sdgData, function ($a, $b) {
                return $b['count'] <=> $a['count'];
            });

            $top3 = array_slice($sdgData, 0, 3);
            $others = array_slice($sdgData, 3);

            if (count($top3) < 3) {
                $dummyData = [
                    ['id' => 'dummy1', 'name' => 'Tidak Ada Data', 'count' => 0, 'percentage' => 0],
                    ['id' => 'dummy2', 'name' => 'Tidak Ada Data', 'count' => 0, 'percentage' => 0],
                    ['id' => 'dummy3', 'name' => 'Tidak Ada Data', 'count' => 0, 'percentage' => 0]
                ];
                $top3 = array_merge($top3, array_slice($dummyData, 0, 3 - count($top3)));
            }

            Log::info('Top 3 SDGs and Others', ['top3' => $top3, 'others' => $others]);

            return response()->json([
                'success' => true,
                'data' => [
                    'top3' => $top3,
                    'others' => $others
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching Top 3 SDGs data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_params' => $request->query()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan saat mengambil data Top 3 SDGs: ' . $e->getMessage(),
                'data' => [
                    'top3' => [
                        ['id' => '1', 'name' => 'Tidak Ada Data', 'count' => 0, 'percentage' => 0],
                        ['id' => '2', 'name' => 'Tidak Ada Data', 'count' => 0, 'percentage' => 0],
                        ['id' => '3', 'name' => 'Tidak Ada Data', 'count' => 0, 'percentage' => 0]
                    ],
                    'others' => []
                ]
            ], 500);
        }
    }

    public function getTotalEventPengunjung(Request $request)
    {
        try {
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            $query = DB::table('booking')->where('status', 'booked');
            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $data = $query
                ->select(
                    DB::raw("DATE_FORMAT(tanggal, '%b') as bulan"),
                    DB::raw('COUNT(*) as total_acara'),
                    DB::raw('SUM(jumlah_peserta) as total_pengunjung')
                )
                ->groupBy(DB::raw("DATE_FORMAT(tanggal, '%b')"))
                ->orderBy(DB::raw("MIN(tanggal)"))
                ->get();

            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $acaraData = array_fill(0, 12, 0);
            $pengunjungData = array_fill(0, 12, 0);

            foreach ($data as $item) {
                $index = array_search($item->bulan, $months);
                if ($index !== false) {
                    $acaraData[$index] = $item->total_acara;
                    $pengunjungData[$index] = $item->total_pengunjung;
                }
            }

            return response()->json([
                'labels' => $months,
                'acara' => $acaraData,
                'pengunjung' => $pengunjungData
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching total event/pengunjung data: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data',
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'acara' => array_fill(0, 12, 0),
                'pengunjung' => array_fill(0, 12, 0)
            ], 500);
        }
    }
}