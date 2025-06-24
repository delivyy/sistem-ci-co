<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\DutyOfficer;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Absen;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingsExport;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class BookingsController extends Controller
{
    private $apiKey = 'JUrrUHAAdBepnJjpfVL2nY6mx9x4Cful4AhYxgs3Qj6HEgryn77KOoDr6BQZgHU1';
    
    public function index(Request $request)
    {
        $today = Carbon::now()->toDateString();
        $allBookings = collect();
        $page = 1;
        $alternativeFormats = [
            Carbon::now()->format('d-m-Y'), 
            Carbon::now()->format('Y-m-d'), 
            Carbon::now()->format('Y/m/d'), 
        ];
        $dateFormats = array_merge([$today], $alternativeFormats);
        $formatIndex = 0;
        $dataFound = false;

        // Ambil nilai status filter dari request
        $statusFilter = $request->get('status', '');
        $searchTerm = strtolower($request->get('search', ''));
        $dutyOfficers = DutyOfficer::all();

        while ($formatIndex < count($dateFormats) && !$dataFound) {
            $currentDateFormat = $dateFormats[$formatIndex];
            Log::info("Mencoba mengambil data booking dengan format tanggal: {$currentDateFormat}");

            $allBookings = collect();
            $page = 1;

            do {
                $url = "https://event.mcc.or.id/api/event?status=booked&date={$currentDateFormat}&page={$page}";
                Log::info("Mengakses API untuk dashboard, URL: {$url}");

                try {
                    $response = Http::withHeaders([
                        'X-API-KEY' => $this->apiKey,
                    ])->withoutVerifying()->get($url);

                    if ($response->successful()) {
                        $data = collect($response->json()['data'] ?? []);
                        Log::info("Data dari halaman {$page}: ", $data->toArray());
                        $allBookings = $allBookings->merge($data);
                        if ($data->isNotEmpty()) {
                            $dataFound = true; // Tandai bahwa data ditemukan
                        }
                        $page++;
                    } else {
                        Log::error("Gagal mengambil data API, Status: {$response->status()}", [
                            'url' => $url,
                            'body' => $response->body()
                        ]);

                        if ($response->status() === 400) {
                            $errorMessage = $response->json()['message'] ?? 'Unknown error';
                            Log::error("Detail error API: {$errorMessage}");
                            if (str_contains(strtolower($errorMessage), 'data not found')) {
                                Log::info("API mengembalikan 'data not found' untuk tanggal {$currentDateFormat}");
                                break;
                            }
                            if ($formatIndex < count($dateFormats) - 1) {
                                Log::info("Mencoba format tanggal berikutnya.");
                                break;
                            }
                            // Jika semua format gagal, tampilkan dashboard kosong
                            return view('front_office.dashboard', [
                                'bookings' => collect(),
                                'totalPages' => 1,
                                'currentPage' => 1,
                                'perPage' => 6,
                                'dutyOfficers' => $dutyOfficers,
                            ]);
                        }
                        break; // Hentikan iterasi untuk error lain
                    }
                } catch (\Exception $e) {
                    Log::error("Exception saat mengakses API: {$e->getMessage()}", ['url' => $url]);
                    if ($formatIndex < count($dateFormats) - 1) {
                        Log::info("Mencoba format tanggal berikutnya.");
                        break;
                    }
                    return view('front_office.dashboard', [
                        'bookings' => collect(),
                        'totalPages' => 1,
                        'currentPage' => 1,
                        'perPage' => 6,
                        'dutyOfficers' => $dutyOfficers,
                    ]);
                }
            } while (!empty($data));

            $formatIndex++;
        }

        $filteredBookings = $allBookings->map(function ($item) use ($today) {
            $bookingItems = collect($item['booking_items'] ?? [])
                ->filter(fn($bookingItem) => $bookingItem['booking_date'] === $today)
                ->values();

            $startTime = $bookingItems->min('booking_hour');
            $endTime = $bookingItems->max('booking_hour');

            $item['start_time'] = $startTime ? Carbon::createFromTime($startTime, 0)->format('H:i') : null;
            $item['end_time'] = $endTime ? Carbon::createFromTime($endTime, 0)->format('H:i') : null;

            $ruanganIds = $bookingItems->pluck('ruangan_id')->unique();
            $item['ruangans'] = collect($item['ruangans'] ?? [])
                ->filter(fn($ruangan) => $ruanganIds->contains($ruangan['id']))
                ->values()
                ->toArray();

            $item['booking_items'] = $bookingItems->toArray();

            $absenData = Absen::where('id_booking', $item['booking_code'])
                ->whereDate('tanggal', $today)
                ->latest('updated_at')
                ->first();

            if ($absenData) {
                $item['absen'] = [
                    'name' => $absenData->name,
                    'status' => $absenData->status,
                    'duty_officer' => $absenData->duty_officer,
                    'phone' => $absenData->phone,
                ];
            }

            return $item;
        })->filter(function ($item) use ($today, $searchTerm, $statusFilter) {
            if (empty($item['booking_items'])) {
                return false;
            }

            if ($searchTerm) {
                $itemText = strtolower(json_encode($item));
                return strpos($itemText, $searchTerm) !== false;
            }

            if ($statusFilter) {
                $currentStatus = $item['absen']['status'] ?? 'Booked';
                return $currentStatus === $statusFilter;
            }

            return true;
        });

        $filteredBookings = $filteredBookings->sortBy(function ($item) {
            $floor = $item['ruangans'][0]['floor'] ?? '0';
            $startTime = $item['start_time'] ?? '00:00';
            return [$floor, $startTime];
        });

        $currentPage = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 6);
        $paginatedBookings = $filteredBookings->forPage($currentPage, $perPage);

        return view('front_office.dashboard', [
            'bookings' => $paginatedBookings,
            'totalPages' => ceil($filteredBookings->count() / $perPage),
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'dutyOfficers' => $dutyOfficers,
        ]);
    }

    public function showBookingList()
    {
        $bookings = Booking::with('dutyOfficer')->get();
        
        $bookings->transform(function($booking) {
            $booking->dutyOfficerIsFilled = !is_null($booking->duty_officer);
            return $booking;
        });

        return view('dashboard', ['bookings' => $bookings]);
    }

    public function exportBookings(Request $request)
    {
        $filters = $request->all();
        $format = $request->input('format', 'csv');
        $filename = "bookings.$format";
        Log::info('Format: ' . $format);

        switch ($format) {
            case 'pdf':
                return Excel::download(new BookingsExport($filters), $filename, \Maatwebsite\Excel\Excel::DOMPDF);
            case 'csv':
                return Excel::download(new BookingsExport($filters), $filename, \Maatwebsite\Excel\Excel::CSV);
            case 'xlsx':
                return Excel::download(new BookingsExport($filters), $filename, \Maatwebsite\Excel\Excel::XLSX);
            default:
                abort(400, 'Format tidak didukung.');
        }
    }

    public function updateDutyOfficer(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|string',
            'duty_officer_id' => 'required|exists:duty_officers,id',
        ]);

        $booking = Absen::where('id_booking', $validated['booking_id'])->first();

        if ($booking) {
            $dutyOfficer = DutyOfficer::find($validated['duty_officer_id']);
            $booking->duty_officer_id = $dutyOfficer->id;
            $booking->save();

            return response()->json([
                'success' => true,
                'officer_name' => $dutyOfficer->nama_do,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Booking tidak ditemukan.',
        ], 404);
    }

    public function showDetails($kode_booking)
    {
        if (!$kode_booking) {
            return redirect()->route('front_office.dashboard')->with('error', 'Kode booking tidak valid.');
        }

        $apiUrl = "https://event.mcc.or.id/api/event?status=booked&booking_code={$kode_booking}";
        Log::info("Mengakses API untuk detail booking, URL: {$apiUrl}");

        try {
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
            ])->withoutVerifying()->get($apiUrl);

            if ($response->successful()) {
                $data = collect($response->json()['data']);
                $booking = $data->firstWhere('booking_code', $kode_booking);

                if ($booking) {
                    $bookingItems = collect($booking['booking_items'] ?? []);
                    $room = collect($booking['ruangans'] ?? [])->first();

                    if (!$room) {
                        Log::error("Data ruangan tidak ditemukan untuk kode: {$kode_booking}");
                        return redirect()->route('front_office.dashboard')->with('error', 'Data ruangan tidak ditemukan.');
                    }

                    $roomDetails = [
                        'room_name' => $room['name'] ?? 'Tidak Diketahui',
                        'room_floor' => $room['floor'] ?? 'Tidak Diketahui',
                        'room_description' => $room['description'] ?? '',
                        'room_facility' => $room['facility'] ?? '',
                    ];

                    return view('booking.details', compact('booking', 'bookingItems', 'roomDetails'));
                }

                Log::warning("Data booking tidak ditemukan untuk kode: {$kode_booking}");
                return redirect()->route('front_office.dashboard')->with('error', 'Data booking tidak ditemukan.');
            }

            Log::error("Gagal mengakses API untuk detail booking, Status: {$response->status()}", [
                'url' => $apiUrl,
                'body' => $response->body()
            ]);
            return view('errors.generic', [
                'error' => 'Tidak dapat mengambil data dari API. Silakan coba lagi nanti.',
            ]);
        } catch (\Exception $e) {
            Log::error("Exception saat mengakses API untuk detail booking: {$e->getMessage()}", ['url' => $apiUrl]);
            return view('errors.generic', [
                'error' => 'Terjadi kesalahan saat mengambil data. Silakan coba lagi nanti.',
            ]);
        }
    }
}