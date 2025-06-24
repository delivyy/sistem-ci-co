<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BookingSyncService
{
    public function syncBookingsForDateRange($startDate, $endDate)
    {
        $page = 1;
        $limit = 100;
        $apiKey = config('services.mcc.api_key', 'JUrrUHAAdBepnJjpfVL2nY6mx9x4Cful4AhYxgs3Qj6HEgryn77KOoDr6BQZgHU1');
        $totalAcara = 0;
        $totalPengunjung = 0;
        $statuses = ['booked', 'booking'];

        $period = CarbonPeriod::create($startDate, $endDate);
        $alternativeFormats = [
            'd-m-Y', 
            'Y-m-d', 
            'Y/m/d', 
        ];

        foreach ($period as $date) {
            $dateFormats = array_merge([$date->format('Y-m-d')], array_map(fn($fmt) => $date->format($fmt), $alternativeFormats));
            $formatIndex = 0;
            $dataFound = false;

            while ($formatIndex < count($dateFormats) && !$dataFound) {
                $currentDate = $dateFormats[$formatIndex];
                Log::info("Mencoba sinkronisasi booking untuk tanggal: {$currentDate}");

                foreach ($statuses as $status) {
                    $page = 1;

                    do {
                        $url = "https://event.mcc.or.id/api/event?status={$status}&date={$currentDate}&limit={$limit}&page={$page}";
                        Log::info("Mengakses API untuk sinkronisasi, URL: {$url}");

                        try {
                            $response = Http::withHeaders(['X-API-KEY' => $apiKey])->get($url);

                            if ($response->successful()) {
                                $data = $response->json()['data'] ?? [];
                                if (empty($data)) {
                                    Log::info("Tidak ada data pada halaman {$page} untuk tanggal {$currentDate}, status {$status}");
                                    break;
                                }
                                $dataFound = true;

                                foreach ($data as $item) {
                                    $bookingDates = array_filter(explode(',', $item['booking_dates'] ?? ''), function ($d) {
                                        return preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($d));
                                    });

                                    foreach ($bookingDates as $bookingDate) {
                                        if ($bookingDate !== $date->format('Y-m-d')) continue;

                                        $bookingItems = collect($item['booking_items'] ?? [])->filter(
                                            fn($bi) => $bi['booking_date'] === $bookingDate
                                        );

                                        if ($bookingItems->isEmpty()) continue;

                                        $bookingHours = $bookingItems->pluck('booking_hour')->sort()->values();
                                        $waktuMulai = sprintf('%02d:00', $bookingHours->first());
                                        $waktuSelesai = sprintf('%02d:00', $bookingHours->last() + 1);

                                        try {
                                            Booking::updateOrCreate(
                                                [
                                                    'kode_booking' => $item['booking_code'],
                                                    'tanggal' => $bookingDate,
                                                ],
                                                [
                                                    'nama_event' => $item['name'] ?? '',
                                                    'nama_organisasi' => $item['user_name'] ?? '',
                                                    'waktu_mulai' => $waktuMulai,
                                                    'waktu_selesai' => $waktuSelesai,
                                                    'nama_pic' => $item['pic_name'] ?? '',
                                                    'status' => $item['status'] ?? 'Unknown',
                                                    'bidang' => $item['kategori_event'] ?? '',
                                                    'kegiatan' => $item['kategori_ekraf'] ?? '',
                                                    'komersial/non' => $item['komersial'] ?? 'non-komersial',
                                                    'lantai' => $item['ruangans'][0]['floor'] ?? 'Tidak Diketahui',
                                                    'ruangan' => $item['ruangans'][0]['name'] ?? 'Tidak Diketahui',
                                                    'jumlah_peserta' => $item['participant'] ?? 0,
                                                ]
                                            );

                                            $totalAcara++;
                                            $totalPengunjung += $item['participant'] ?? 0;
                                        } catch (\Exception $e) {
                                            Log::error('Gagal menyimpan booking', [
                                                'kode_booking' => $item['booking_code'],
                                                'tanggal' => $bookingDate,
                                                'error' => $e->getMessage(),
                                            ]);
                                        }
                                    }
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
                                        Log::info("API mengembalikan 'data not found' untuk tanggal {$currentDate}, status {$status}");
                                        break;
                                    }
                                    if ($formatIndex < count($dateFormats) - 1) {
                                        Log::info("Mencoba format tanggal berikutnya.");
                                        break;
                                    }
                                    break;
                                }
                                break;
                            }
                        } catch (\Exception $e) {
                            Log::error("Exception saat mengakses API: {$e->getMessage()}", ['url' => $url]);
                            if ($formatIndex < count($dateFormats) - 1) {
                                Log::info("Mencoba format tanggal berikutnya.");
                                break;
                            }
                            break;
                        }
                    } while (true);
                }

                $formatIndex++;
            }
        }

        $query = Booking::query();
        $query->whereBetween('tanggal', [$startDate, $endDate]);
        $query->whereIn('status', $statuses);
        $totalAcaraKeseluruhan = $query->count();
        $totalPengunjungKeseluruhan = $query->sum('jumlah_peserta');

        Log::info("Sinkronisasi booking selesai", [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'statuses' => $statuses,
            'total_acara_baru' => $totalAcara,
            'total_pengunjung_baru' => $totalPengunjung,
            'total_acara_keseluruhan' => $totalAcaraKeseluruhan,
            'total_pengunjung_keseluruhan' => $totalPengunjungKeseluruhan
        ]);

        return [
            'message' => 'Data booking berhasil disimpan.',
            'total_acara_baru' => $totalAcara,
            'total_pengunjung_baru' => $totalPengunjung,
            'total_acara_keseluruhan' => $totalAcaraKeseluruhan,
            'total_pengunjung_keseluruhan' => $totalPengunjungKeseluruhan,
        ];
    }
}