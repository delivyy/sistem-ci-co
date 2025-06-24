<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class BookingSyncService
{
    protected $apiKey;

    public function __construct()
    {
        // Mengambil API Key dari file .env
        $this->apiKey = env('API_KEY');
    }

    public function sync()
    {
        Log::info('Mulai sinkronisasi dari API...');
        $today = Carbon::now()->toDateString();
        $page = 1;
        $maxPages = 10;

        do {
            $url = "https://event.mcc.or.id/api/event?status=booked&date={$today}&page={$page}";
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
            ])->withoutVerifying()->get($url);

            if ($response->successful()) {
                $data = collect($response->json()['data'] ?? []);

                foreach ($data as $item) {
                    // Menggunakan updateOrCreate untuk menghindari duplikasi data
                    Booking::updateOrCreate(
                        ['kode_booking' => $item['booking_code']],
                        [
                            'nama_event' => $item['event_name'] ?? '',
                            'nama_organisasi' => $item['organisasi'] ?? '',
                            'tanggal' => $today,
                            'waktu_mulai' => '08:00', 
                            'waktu_selesai' => '10:00',
                            'nama_pic' => $item['pic_name'] ?? '',
                            'status' => 'Booked',
                            'bidang' => 'kategori_event',
                            'kegiatan' => 'kategori_ekraf',
                            'komersial/non' => 'komersial',
                            'lantai' => $item['ruangans'][0]['floor'] ?? 'Tidak Diketahui',
                            'ruangan' => $item['ruangans'][0]['name'] ?? '',
                            'jumlah_peserta' => $item['participant'] ?? 0,
                        ]
                    );
                }

                $page++;
            } else {
                // Log error jika API gagal
                Log::error('API Error: ' . $response->status());
                break;
            }
        } while ($data->isNotEmpty() && $page <= $maxPages);

        return "Sinkronisasi selesai!";
    }
}
