<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absen;
use App\Models\PeminjamanBarang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AbsenController extends Controller
{
    private $apiKey = 'JUrrUHAAdBepnJjpfVL2nY6akan4x4Cful4AhYxgs3Qj6HEgryn77KOoDr6BQZgHU1';

    public function __construct()
    {
        $this->middleware('trigger.sync.booking');
    }

    // Proses input kode booking
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_booking' => 'required|string',
        ]);

        $id_booking = strtolower(trim($request->id_booking));
        Log::info('Kode Booking Diterima: ' . $id_booking);
        Log::info('Query Parameters: ', request()->query());

        $cacheKey = 'booking_data_' . $id_booking . '_' . Carbon::now()->toDateString();
        $bookingData = Cache::get($cacheKey);

        if (!$bookingData) {
            $today = Carbon::now()->toDateString(); 
            $alternativeFormats = [
                Carbon::now()->format('d-m-Y'), 
                Carbon::now()->format('Y-m-d'), 
                Carbon::now()->format('Y/m/d'), 
            ];
            Carbon::setLocale('id');
            $allBookings = collect();
            $page = 1;

            $dateFormats = array_merge([$today], $alternativeFormats);
            $formatIndex = 0;
            $foundData = false;

            while ($formatIndex < count($dateFormats) && !$foundData) {
                $currentDateFormat = $dateFormats[$formatIndex];
                Log::info("Mencoba format tanggal: {$currentDateFormat}");

                $allBookings = collect();
                $page = 1;

                // Iterasi API untuk mengambil semua data
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
                            Log::info("Data dari halaman {$page}: ", $data->toArray());
                            $allBookings = $allBookings->merge($data);

                            // Periksa apakah kode booking ditemukan
                            $bookingData = $data->first(function ($item) use ($id_booking) {
                                return strtolower($item['booking_code']) === $id_booking;
                            });

                            if ($bookingData) {
                                Log::info('Data Booking ditemukan pada halaman: ' . $page . ' dengan tanggal: ' . $currentDateFormat);
                                // Simpan ke cache selama 1 jam
                                Cache::put($cacheKey, $bookingData, now()->addHour());
                                $foundData = true;
                                break; 
                            }

                            // Jika data kosong, hentikan iterasi untuk format ini
                            if ($data->isEmpty()) {
                                Log::info("Tidak ada data pada halaman {$page} untuk tanggal {$currentDateFormat}. Menghentikan iterasi.");
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

                                // Coba format tanggal berikutnya
                                if ($formatIndex < count($dateFormats) - 1) {
                                    Log::info("Gagal dengan format tanggal {$currentDateFormat}. Mencoba format berikutnya.");
                                    break;
                                }

                                Log::warning("Semua format tanggal gagal untuk kode: {$id_booking}");
                                return redirect()->route('inputkode.show')->with('gagal', 'Gagal mengambil data dari API: ' . $errorMessage);
                            }

                            Log::error("Error API dengan status: {$response->status()}");
                            return redirect()->route('inputkode.show')->with('gagal', 'Gagal mengambil data dari API (Status: ' . $response->status() . ').');
                        }
                    } catch (\Exception $e) {
                        Log::error("Exception saat mengakses API: {$e->getMessage()}");
                        Log::error("URL: {$url}");
                        // Lanjutkan ke format tanggal berikutnya jika masih ada
                        if ($formatIndex < count($dateFormats) - 1) {
                            Log::info("Gagal dengan format tanggal {$currentDateFormat}. Mencoba format berikutnya.");
                            break;
                        }
                        // Jika semua format gagal, kembalikan error
                        return redirect()->route('inputkode.show')->with('gagal', 'Terjadi kesalahan saat mengakses API.');
                    }
                } while (true); // Lanjutkan hingga data kosong atau ditemukan

                $formatIndex++;
            }

            // Cari booking berdasarkan kode_booking
            $bookingData = $allBookings->first(function ($item) use ($id_booking) {
                return strtolower($item['booking_code']) === $id_booking;
            });

            if (!$bookingData) {
                Log::warning('Booking tidak ditemukan untuk kode: ' . $id_booking);
                return redirect()->route('inputkode.show')->with('gagal', 'Kode booking tidak ditemukan atau belum disinkronkan.');
            }
        }

        Log::info('Data Booking ditemukan: ', (array) $bookingData);

        // Cek apakah booking sudah check-in untuk hari ini
        $checkin = Absen::where('id_booking', $id_booking)
                        ->where('tanggal', $today)
                        ->first();

        if ($checkin) {
            // Jika sudah check-in, tampilkan pesan dan berhenti
            return redirect()->route('inputkode.show')->with('gagal', 'Anda sudah melakukan check-in dengan kode booking ini.');
        }

        // Format tanggal dan hari dari API
        $apiBookingDate = Carbon::parse($bookingData['date'] ?? '');
        $formattedDate = $apiBookingDate->toDateString();  // Format tanggal YYYY-MM-DD
        $dayOfWeek = $apiBookingDate->isoFormat('dddd');  // Nama hari dalam minggu (misalnya: Senin, Selasa, dll)

        // Ambil data booking_items yang terkait dengan booking_id dan tanggal yang sesuai
        $bookingItems = collect($bookingData['booking_items'] ?? [])
            ->filter(function ($item) use ($formattedDate) {
                return isset($item['booking_date']) && $item['booking_date'] === $formattedDate;
            });

        if ($bookingItems->isEmpty()) {
            Log::warning('Tidak ada booking_items untuk kode: ' . $id_booking);
            return redirect()->route('inputkode.show')->with('gagal', 'Tidak ada item booking untuk tanggal ini.');
        }

        Log::info('Data Booking Items ditemukan: ', $bookingItems->toArray());

        // Pemrosesan detail booking
        try {
            $ruangan = collect($bookingData['ruangans'] ?? [])->first();

            if (!$ruangan) {
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

            // Tampilkan halaman booking.details
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
            return redirect()->route('inputkode.show')->with('gagal', 'Terjadi kesalahan saat memproses data booking.');
        }
    }

    // Fungsi check-in dengan form
    public function checkinstore(Request $request)
    {
        // Validasi input form check-in
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'signatureData' => 'required',
        ]);

        // Ambil kode_booking dari sesi
        $kode_booking = $request->session()->get('kode_booking');
        $today = Carbon::now()->toDateString();
        $alternativeFormats = [
            Carbon::now()->format('d-m-Y'), 
            Carbon::now()->format('Y-m-d'), 
            Carbon::now()->format('Y/m/d'), 
        ];
        $allBookings = collect();
        $page = 1;

        $cacheKey = 'booking_data_' . $kode_booking . '_' . $today;
        $bookingData = Cache::get($cacheKey);

        if (!$bookingData) {
            // Coba semua format tanggal
            $dateFormats = array_merge([$today], $alternativeFormats);
            $formatIndex = 0;
            $foundData = false;

            while ($formatIndex < count($dateFormats) && !$foundData) {
                $currentDateFormat = $dateFormats[$formatIndex];
                Log::info("Mencoba format tanggal: {$currentDateFormat}");

                // Reset untuk format tanggal baru
                $allBookings = collect();
                $page = 1;

                // Iterasi API untuk mengambil semua data
                do {
                    Log::info("Mengakses halaman API: {$page} untuk kode: {$kode_booking} dengan tanggal: {$currentDateFormat}");
                    $url = "https://event.mcc.or.id/api/event?status=booked&date={$currentDateFormat}&page={$page}";
                    Log::info("URL API: {$url}");

                    try {
                        $response = Http::withHeaders([
                            'X-API-KEY' => $this->apiKey,
                        ])->withoutVerifying()->get($url);

                        if ($response->successful()) {
                            $data = collect($response->json()['data'] ?? []);
                            Log::info("Data dari halaman {$page}: ", $data->toArray());
                            $allBookings = $allBookings->merge($data);

                            // Periksa apakah kode booking ditemukan
                            $bookingData = $data->first(function ($item) use ($kode_booking) {
                                return strtolower($item['booking_code']) === $kode_booking;
                            });

                            if ($bookingData) {
                                // Simpan ke cache selama 1 jam
                                Cache::put($cacheKey, $bookingData, now()->addHour());
                                $foundData = true;
                                break;
                            }

                            // Jika data kosong, hentikan iterasi
                            if ($data->isEmpty()) {
                                Log::info("Tidak ada data pada halaman {$page} untuk tanggal {$currentDateFormat}. Menghentikan iterasi.");
                                break;
                            }

                            $page++;
                        } else {
                            Log::error("Gagal mengambil data dari halaman: {$page} dengan status: {$response->status()} untuk tanggal: {$currentDateFormat}");
                            Log::error('Respons API: ' . $response->body());

                            // Tangani semua respons 400
                            if ($response->status() === 400) {
                                $errorMessage = $response->json()['message'] ?? 'Unknown error';
                                Log::error("Detail error API: {$errorMessage}");

                                // Jika pesan adalah "data not found", hentikan iterasi
                                if (str_contains(strtolower($errorMessage), 'data not found')) {
                                    Log::info("API mengembalikan 'data not found' pada halaman {$page} untuk tanggal {$currentDateFormat}.");
                                    break;
                                }

                                // Coba format tanggal berikutnya
                                if ($formatIndex < count($dateFormats) - 1) {
                                    Log::info("Gagal dengan format tanggal {$currentDateFormat}. Mencoba format berikutnya.");
                                    break;
                                }

                                // Jika semua format gagal
                                Log::warning("Semua format tanggal gagal untuk kode: {$kode_booking}");
                                return redirect()->route('inputkode.show')->with('gagal', 'Gagal mengambil data dari API: ' . $errorMessage);
                            }

                            Log::error("Error API dengan status: {$response->status()}");
                            return redirect()->route('inputkode.show')->with('gagal', 'Gagal mengambil data dari API (Status: ' . $response->status() . ').');
                        }
                    } catch (\Exception $e) {
                        Log::error("Exception saat mengakses API: {$e->getMessage()}");
                        Log::error("URL: {$url}");
                        // Coba format tanggal berikutnya
                        if ($formatIndex < count($dateFormats) - 1) {
                            Log::info("Gagal dengan format tanggal {$currentDateFormat}. Mencoba format berikutnya.");
                            break;
                        }
                        return redirect()->route('inputkode.show')->with('gagal', 'Terjadi kesalahan saat mengakses API.');
                    }
                } while (true);
            }

            $bookingData = $allBookings->first(function ($item) use ($kode_booking) {
                return strtolower($item['booking_code']) === $kode_booking;
            });

            if (!$bookingData) {
                Log::error("Booking tidak ditemukan untuk kode: {$kode_booking}");
                return redirect()->route('inputkode.show')->with('gagal', 'Kode booking tidak ditemukan atau belum disinkronkan.');
            }
        }

        // Simpan atau update data check-in
        $absen = Absen::where('id_booking', $kode_booking)
                      ->where('tanggal', $today)
                      ->first();

        if (!$absen) {
            Absen::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'signature' => $request->signatureData,
                'id_booking' => $kode_booking,
                'tanggal' => $today,
                'duty_officer_id' => $request->duty_officer_id,
                'status' => $request->status,
                'ruangan' => $bookingData['ruangans'][0]['name'],
                'lantai' => $bookingData['ruangans'][0]['floor'],
            ]);
        } else {
            $absen->update([
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'signature' => $request->input('signatureData'),
                'status' => 'Check-in',
            ]);
        }

        // Cek apakah ada peminjaman barang
        $peminjaman = PeminjamanBarang::where('kode_booking', $kode_booking)->first();

        if ($peminjaman) {
            return redirect()->route('peminjaman.show', $peminjaman->kode_booking);
        } else {
            return redirect()->route('dashboard')->with('success', 'Check-in berhasil tanpa peminjaman barang.');
        }
    }
}