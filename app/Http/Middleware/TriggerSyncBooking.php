<?php

namespace App\Http\Middleware;

use App\Services\BookingSyncService;
use App\Models\SyncSchedule;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TriggerSyncBooking
{
    public function handle($request, Closure $next)
    {
        try {
            $sync = SyncSchedule::firstOrCreate(
                ['task_name' => 'sync-bookings'],
                ['interval_hours' => 1, 'last_run_at' => null]
            );

            if (!$sync->last_run_at || Carbon::parse($sync->last_run_at)->addHour()->isPast()) {
                $lock = Cache::lock('booking-sync', 60);
                if ($lock->get()) {
                    try {
                        $today = Carbon::today();
                        $dateFormats = [
                            $today->format('Y-m-d'), 
                            $today->format('d-m-Y'), 
                            $today->format('Y/m/d'), 
                            $today->format('Y-m-d'),
                        ];
                        $dateFormats = array_unique($dateFormats);
                        $formatIndex = 0;
                        $syncSuccess = false;

                        while ($formatIndex < count($dateFormats) && !$syncSuccess) {
                            $currentDateFormat = $dateFormats[$formatIndex];
                            Log::info("Mencoba sinkronisasi booking dengan format tanggal: {$currentDateFormat}");

                            try {
                                $result = (new BookingSyncService())->syncBookingsForDateRange($currentDateFormat, $currentDateFormat);
                                $sync->update(['last_run_at' => now()]);
                                Log::info('Sinkronisasi booking berhasil dijalankan', [
                                    'date' => $currentDateFormat,
                                    'total_acara_baru' => $result['total_acara_baru'] ?? 0,
                                    'total_pengunjung_baru' => $result['total_pengunjung_baru'] ?? 0
                                ]);
                                $syncSuccess = true;
                            } catch (\Exception $e) {
                                Log::error("Gagal sinkronisasi dengan format tanggal {$currentDateFormat}", [
                                    'error' => $e->getMessage(),
                                    'url' => $request->url(),
                                    'query' => $request->query()
                                ]);
                                if ($formatIndex < count($dateFormats) - 1) {
                                    Log::info("Mencoba format tanggal berikutnya.");
                                    $formatIndex++;
                                    continue;
                                }
                                throw $e; //ulang kalao gagal
                            }
                        }

                        if (!$syncSuccess) {
                            Log::warning('Sinkronisasi booking gagal untuk semua format tanggal', [
                                'url' => $request->url(),
                                'formats_tried' => $dateFormats
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Gagal memicu sinkronisasi booking', [
                            'error' => $e->getMessage(),
                            'url' => $request->url(),
                            'query' => $request->query()
                        ]);
                    } finally {
                        $lock->release();
                        Log::info('Lock booking-sync dilepaskan');
                    }
                } else {
                    Log::info('Sinkronisasi booking dilewati karena lock aktif', [
                        'url' => $request->url()
                    ]);
                }
            } else {
                Log::info('Sinkronisasi booking dilewati karena belum waktunya', [
                    'last_run_at' => $sync->last_run_at,
                    'next_run_at' => Carbon::parse($sync->last_run_at)->addHour(),
                    'url' => $request->url()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception di TriggerSyncBooking middleware', [
                'error' => $e->getMessage(),
                'url' => $request->url(),
                'query' => $request->query()
            ]);
        }

        return $next($request);
    }
}