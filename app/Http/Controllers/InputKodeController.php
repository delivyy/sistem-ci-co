<?php
namespace App\Http\Controllers;

use App\Models\Absen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\PeminjamanBarang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class InputKodeController extends Controller
{
    private $apiKey = 'JUrrUHAAdBepnJjpfVL2nY6mx9x4Cful4AhYxgs3Qj6HEgryn77KOoDr6BQZgHU1';

    public function show()
    {
        return view('front_office.inputkode');
    }

    public function validateRole()
    {
        $user = Auth::user();
        if ($user && $user->role === 'frontoffice') {
            session()->regenerate();
            return redirect()->route('front_office.dashboard');
        }
        return redirect()->route('dashboard');
    }
    
    public function match(Request $request)
    {
        $request->validate([
            'id_booking' => 'required|string',
        ]);

        $id_booking = strtolower(trim($request->id_booking));
        Log::info('Kode Booking Diterima: ' . $id_booking);

        $today = Carbon::today()->toDateString();
        $checkIn = Absen::where('id_booking', $id_booking)
                        ->where('tanggal', $today)
                        ->first();

        if ($checkIn) {
            Log::info('Booking dengan kode ' . $id_booking . ' sudah check-in pada tanggal ' . $today);
            session()->forget('kode_booking');
            return redirect()->route('inputkode.show')->with('gagal', 'Anda sudah melakukan check-in dengan kode booking ini untuk hari ini.');
        }

        $cacheKey = 'booking_data_' . $id_booking . '_' . $today;
        $bookingData = Cache::get($cacheKey);

        if (!$bookingData) {
            Carbon::setLocale('id');
            $allBookings = collect();
            $page = 1;
            $alternativeFormats = [
                Carbon::now()->format('d-m-Y'),
                Carbon::now()->format('Y-m-d'),
                Carbon::now()->format('Y/m/d'),
            ];
            $dateFormats = array_merge([$today], $alternativeFormats);
            $formatIndex = 0;
            $foundData = false;

            while ($formatIndex < count($dateFormats) && !$foundData) {
                $currentDateFormat = $dateFormats[$formatIndex];
                Log::info("Mencoba format tanggal: {$currentDateFormat}");

                $allBookings = collect();
                $page = 1;

                do {
                    Log::info("Mengakses halaman API: {$page} untuk kode: {$id_booking} dengan tanggal: {$currentDateFormat}");
                    $url = "https://event.mcc.or.id/api/event?status=booked&date={$currentDateFormat}&page={$page}";
                    Log::info("URL API: {$url}");

                    try {
                        $response = Http::withHeaders([
                            'X-API-KEY' => $this->apiKey,
                        ])->withoutVerifying()->get($url);

                        if ($response->successful()) {
                            $data = collect($response->json()['data'] ?? []);
                            $allBookings = $allBookings->merge($data);

                            $bookingData = $data->first(function ($item) use ($id_booking) {
                                return strtolower($item['booking_code']) === $id_booking;
                            });

                            if ($bookingData) {
                                Log::info('Data Booking ditemukan pada halaman: ' . $page . ' dengan tanggal: ' . $currentDateFormat);
                                Cache::put($cacheKey, $bookingData, now()->addHour());
                                $foundData = true;
                                break;
                            }

                            if ($data->isEmpty()) {
                                Log::info("Tidak ada data pada halaman {$page} untuk tanggal {$currentDateFormat}.");
                                break;
                            }

                            $page++;
                        } else {
                            Log::error("Gagal mengambil data dari halaman: {$page} dengan status: {$response->status()} untuk tanggal: {$currentDateFormat}");
                            Log::error('Respons API: ' . $response->body());

                            if ($response->status() === 400) {
                                $errorMessage = $response->json()['message'] ?? 'Unknown error';
                                Log::error("Detail error API: {$errorMessage}");
                                if (str_contains(strtolower($errorMessage), 'data not found')) {
                                    Log::info("API mengembalikan 'data not found' pada halaman {$page} untuk tanggal {$currentDateFormat}.");
                                    break;
                                }
                                if ($formatIndex < count($dateFormats) - 1) {
                                    Log::info("Gagal dengan format tanggal {$currentDateFormat}. Mencoba format berikutnya.");
                                    break;
                                }
                                session()->forget('kode_booking');
                                return redirect()->route('inputkode.show')->with('gagal', 'Gagal mengambil data dari API: ' . $errorMessage);
                            }

                            session()->forget('kode_booking');
                            return redirect()->route('inputkode.show')->with('gagal', 'Gagal mengambil data dari API (Status: ' . $response->status() . ').');
                        }
                    } catch (\Exception $e) {
                        Log::error("Exception saat mengakses API: {$e->getMessage()}", ['url' => $url]);
                        if ($formatIndex < count($dateFormats) - 1) {
                            Log::info("Gagal dengan format tanggal {$currentDateFormat}. Mencoba format berikutnya.");
                            break;
                        }
                        session()->forget('kode_booking');
                        return redirect()->route('inputkode.show')->with('gagal', 'Terjadi kesalahan saat mengakses API.');
                    }
                } while (true);

                $formatIndex++;
            }

            $bookingData = $allBookings->first(function ($item) use ($id_booking) {
                return strtolower($item['booking_code']) === $id_booking;
            });

            if (!$bookingData) {
                Log::warning('Booking tidak ditemukan untuk kode: ' . $id_booking);
                session()->forget('kode_booking');
                return redirect()->route('inputkode.show')->with('gagal', 'Kode booking tidak ditemukan atau belum disinkronkan.');
            }
        }

        Log::info('Data Booking ditemukan: ', (array) $bookingData);

        $apiBookingDate = Carbon::parse($bookingData['date'] ?? '');
        $formattedDate = $apiBookingDate->toDateString();
        $dayOfWeek = $apiBookingDate->isoFormat('dddd');

        $bookingItems = collect($bookingData['booking_items'] ?? [])
            ->filter(function ($item) use ($formattedDate) {
                return isset($item['booking_date']) && $item['booking_date'] === $formattedDate;
            });

        if ($bookingItems->isEmpty()) {
            Log::warning('Tidak ada booking_items untuk kode: ' . $id_booking);
            session()->forget('kode_booking');
            return redirect()->route('inputkode.show')->with('gagal', 'Tidak ada item booking untuk tanggal ini.');
        }

        Log::info('Data Booking Items ditemukan: ', $bookingItems->toArray());

        try {
            $ruangan = collect($bookingData['ruangans'] ?? [])->first();

            if (!$ruangan) {
                session()->forget('kode_booking');
                return redirect()->route('inputkode.show')->with('gagal', 'Data ruangan tidak ditemukan.');
            }

            $startTime = $bookingItems->min('booking_hour');
            $endTime = $bookingItems->max('booking_hour');

            $roomDetails = [
                'room_name' => $ruangan['name'] ?? 'Tidak Diketahui',
                'room_floor' => $ruangan['floor'] ?? 'Tidak Diketahui',
                'room_description' => $ruangan['description'] ?? '',
                'room_facility' => $ruangan['facility'] ?? '',
            ];

            $request->session()->put('kode_booking', $id_booking);

            return view('booking.details', [
                'booking' => $bookingData,
                'roomDetails' => $roomDetails,
                'bookingItems' => $bookingItems,
                'formattedDate' => $formattedDate,
                'dayOfWeek' => $dayOfWeek,
                'startTime' => $startTime ? Carbon::createFromTime($startTime)->format('H:i') : null,
                'endTime' => $endTime ? Carbon::createFromTime($endTime)->format('H:i') : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Kesalahan saat memproses data booking: ' . $e->getMessage());
            session()->forget('kode_booking');
            return redirect()->route('inputkode.show')->with('gagal', 'Terjadi kesalahan saat memproses data booking.');
        }
    }


    // Proses simpan data check-in setelah melengkapi form
public function showPeminjaman(Request $request, $kode_booking)
{
    // 1. Ambil data dari form sebelumnya
    $checkInData = $request->only(['name', 'phone', 'signatureData']);

    // 2. Ambil data detail booking dari API
    $today = Carbon::now()->toDateString();
    $apiUrl = "https://event.mcc.or.id/api/event?status=booked&booking_code={$kode_booking}";
    
    $response = Http::withHeaders([
        'X-API-KEY' => $this->apiKey,
    ])->withoutVerifying()->get($apiUrl);

    if (!$response->successful()) {
        Log::error("Gagal mengakses API untuk kode: {$kode_booking}");
        return back()->with('gagal', 'Gagal mengambil data booking dari API.');
    }

    $bookingData = $response->json();
    $booking = collect($bookingData['data'] ?? [])->firstWhere('booking_code', $kode_booking);

    if (!$booking) {
        Log::error("Booking tidak ditemukan untuk kode: {$kode_booking}");
        return redirect()->route('inputkode.show')->with('gagal', 'Data booking tidak ditemukan.');
    }

    $bookingItemsToday = collect($booking['booking_items'] ?? [])
        ->where('booking_date', $today);

    $minHour = $bookingItemsToday->min('booking_hour');
    $maxHour = $bookingItemsToday->max('booking_hour');

    $startTime = $minHour ? Carbon::createFromTime($minHour)->format('H:i') : 'N/A';
    $endTime = $maxHour ? Carbon::createFromTime($maxHour)->format('H:i') : 'N/A';
    // ==============================================================================

    // 3. Siapkan data lain yang dibutuhkan
    $ruangan = collect($booking['ruangans'] ?? [])
        ->first(); // Ambil data ruangan pertama

    $database_items = \App\Models\PeminjamanBarang::where('kode_booking', $kode_booking)->get();
    $tools = collect($booking['tools'] ?? []);
    
    // 4. Kirim semua data yang diperlukan ke view
    return view('peminjaman.showPinjam', [
        'booking' => $booking,
        'ruangan' => $ruangan,
        'database_items' => $database_items,
        'tools' => $tools,
        'absen' => null,
        'checkInData' => $checkInData,
        'startTime' => $startTime, // <-- Kirim variabel baru ke view
        'endTime' => $endTime,     // <-- Kirim variabel baru ke view
    ]);
}


    public function completeCheckIn(Request $request, $kode_booking)
    {
        // Validasi input dari form kedua (termasuk data tersembunyi)
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'signatureData' => 'required|string',
            'agree' => 'required', // Memastikan checkbox persetujuan dicentang
        ]);

        $signatureData = $request->input('signatureData');
        if (empty($signatureData)) {
            session()->forget('kode_booking');
            return back()->with('error', 'Tanda tangan tidak ditemukan.');
        }

        $today = Carbon::now()->toDateString();

        // Periksa apakah sudah check-in
        $cek = Absen::where([
            'id_booking' => $kode_booking,
            'tanggal' => $today,
        ])->first();

        if ($cek) {
            Log::warning("Check-in ganda untuk kode: {$kode_booking}");
            return redirect()->route('inputkode.show')->with('gagal', 'Anda sudah melakukan check-in dengan kode booking ini hari ini.');
        }

        // Ambil data dari API untuk detail ruangan
        $apiUrl = "https://event.mcc.or.id/api/event?status=booked&booking_code={$kode_booking}";
        $response = Http::withHeaders(['X-API-KEY' => $this->apiKey])->withoutVerifying()->get($apiUrl);

        if (!$response->successful()) {
            Log::error("Gagal mengakses API untuk kode: {$kode_booking}");
            return back()->with('error', 'Gagal mengakses data booking. Coba lagi.');
        }

        $bookingData = $response->json();
        $booking = collect($bookingData['data'] ?? [])->firstWhere('booking_code', $kode_booking);

        if (!$booking) {
            return back()->with('error', 'Booking tidak ditemukan.');
        }

        $validRuanganIds = collect($booking['booking_items'] ?? [])
            ->filter(fn ($item) => isset($item['booking_date'], $item['ruangan_id']) && $item['booking_date'] === $today)
            ->pluck('ruangan_id')
            ->unique();

        // Simpan data check-in ke tabel 'absen'
        foreach ($booking['ruangans'] as $ruangan) {
            if ($validRuanganIds->contains($ruangan['id'])) {
                Absen::create([
                    'id_booking' => $kode_booking,
                    'tanggal' => $today,
                    'name' => $request->input('name'),
                    'phone' => $request->input('phone'),
                    'signature' => $signatureData,
                    'status' => 'Check-in', // Status diubah di sini
                    'ruangan' => $ruangan['name'],
                    'lantai' => $ruangan['floor'],
                ]);
            }
        }

        Log::info("Check-in berhasil untuk kode: {$kode_booking}");

        // **PERUBAHAN UTAMA:**
        // Arahkan ke halaman dashboard yang sesuai setelah check-in berhasil.
        // Tidak lagi mengarah ke form peminjaman.
        return redirect()->route('inputkode.show')
            ->with('success', 'Check-in berhasil. Terima kasih!');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'kode_booking' => 'required|string',
        ]);

        $kode_booking = strtolower(trim($request->input('kode_booking')));

        $absenTerbaru = Absen::where('id_booking', $kode_booking)
            ->orderBy('tanggal', 'desc')
            ->first();

        if (!$absenTerbaru) {
            session()->forget('kode_booking');
            return redirect()->back()->with('gagal', 'Data booking tidak ditemukan.');
        }

        try {
            $absenTerbaru->update(['status' => 'Check-out']);
            return redirect()->back()->with('sukses', 'Check-out berhasil dilakukan.');
        } catch (\Exception $e) {
            Log::error("Error saat check-out: " . $e->getMessage());
            session()->forget('kode_booking');
            return redirect()->back()->with('gagal', 'Terjadi kesalahan saat mengubah status.');
        }
    }
}