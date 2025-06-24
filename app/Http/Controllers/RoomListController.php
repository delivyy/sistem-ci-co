<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class RoomListController extends Controller
{
    private $apiKey = 'JUrrUHAAdBepnJjpfVL2nY6mx9x4Cful4AhYxgs3Qj6HEgryn77KOoDr6BQZgHU1';
    private function fetchApiData($apiType, $date = null)
    {
        $allData = collect();
        $page = 1;
        
        // Use provided date or default to today
        $targetDate = $date ?? Carbon::now()->toDateString();
    
        do {
            if ($apiType === 'rooms') {
                $url = "https://event.mcc.or.id/api/ruangan?page={$page}";
            } elseif ($apiType === 'bookings') {
                $url = "https://event.mcc.or.id/api/event?status=booked&date={$targetDate}&page={$page}";
            } else {
                break;
            }
    
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
            ])->withoutVerifying()->get($url);
    
            if ($response->successful()) {
                $data = collect($response->json('data') ?? []);
                $allData = $allData->merge($data);
                $page++;
            } else {
                report("Error accessing {$apiType} API: " . $response->status());
                break;
            }
        } while ($data->isNotEmpty());
    
        return $allData;
    }
    
    public function index(Request $request)
    {
        // Get selected date or default to today
        $selectedDate = $request->filled('date') ? $request->date : Carbon::now()->toDateString();
        
        $allRooms = $this->fetchApiData('rooms');
        $timeRanges = $this->getBookingTimes($selectedDate);
    
        // Database statuses, keyed by room
        $dbStatuses = Room::query()
            ->select('ruangan', 'lantai', 'status')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->unique(function ($room) {
                return strtolower(trim("{$room->ruangan}|{$room->lantai}"));
            })
            ->mapWithKeys(function ($room) {
                return [strtolower(trim("{$room->ruangan}|{$room->lantai}")) => $room->status];
            });
    
        // Get booking statuses from database (for event status logic)
        $bookingStatuses = $this->getBookingStatusesFromDatabase($selectedDate);
    
        // Apply filters
        $apiRooms = $allRooms;
    
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $apiRooms = $apiRooms->filter(function ($room) use ($search) {
                return stripos(strtolower($room['name']), $search) !== false || 
                       stripos(strtolower($room['floor']), $search) !== false;
            });
        }
    
        if ($request->filled('lantai')) {
            $lantai = strtolower($request->lantai);
            $apiRooms = $apiRooms->filter(function ($room) use ($lantai) {
                return strtolower($room['floor']) === $lantai;
            });
        }
    
        // UPDATED: Process rooms - Simplified to use only 2 statuses
        $rooms = $apiRooms->map(function ($room) use ($timeRanges, $dbStatuses, $selectedDate, $bookingStatuses) {
            $roomId = $room['id'];
            $key = strtolower(trim("{$room['name']}|{$room['floor']}"));
            
            // Simplified fix - replace the status determination logic
            if ($selectedDate === Carbon::now()->toDateString()) {
                $status = $dbStatuses->get($key, 'Check-out');
            } else {
                $status = 'Check-out';
                }
    
            // Get all bookings for this room, sorted by time
            $allBookings = $timeRanges
                ->where('ruangan_id', $roomId)
                ->sortBy('start_time')
                ->values();
    
            // Default return structure
            $roomData = [
                'name' => $room['name'],
                'floor' => $room['floor'],
                'status' => $status, // UPDATED: Will only be 'Check-in' or 'Check-out'
                'events' => [],
                'has_bookings' => $allBookings->isNotEmpty(),
            ];
    
            if ($allBookings->isNotEmpty()) {
                // Room has bookings - determine event status based on database status
                $roomData['events'] = $allBookings->map(function ($booking) use ($bookingStatuses) {
                    $bookingCode = $booking['booking_code'];
                    
                    // Get booking status from database
                    $dbBookingStatus = $bookingStatuses->get($bookingCode, 'upcoming');
                    
                    // Map database status to event status
                    $eventStatus = 'upcoming'; // default
                    
                    switch (strtolower($dbBookingStatus)) {
                        case 'check-in':
                            $eventStatus = 'active';
                            break;
                        case 'check-out':
                            $eventStatus = 'completed';
                            break;
                        default:
                            $eventStatus = 'upcoming';
                            break;
                    }
    
                    return [
                        'start' => $booking['start_time'],
                        'end' => $booking['end_time'],
                        'booking_code' => $bookingCode,
                        'status' => $eventStatus
                    ];
                })->toArray();
            }
    
            return $roomData;
        });
    
        // Apply status filter after processing
        if ($request->filled('status')) {
            $statusFilter = strtolower($request->status);
            $rooms = $rooms->filter(function ($room) use ($statusFilter) {
                return strtolower($room['status']) === $statusFilter;
            });
        }
    
        // UPDATED: Sort rooms for consistent display - Updated for 2 statuses only
        $rooms = $rooms->sortBy(function ($room) {
            $statusOrder = [
                'check-in' => 1,     // Sedang Digunakan (prioritas tinggi)
                'check-out' => 2,    // Tersedia
            ];
    
            $statusRank = $statusOrder[strtolower($room['status'])] ?? 3;
            $hasEvents = !empty($room['events']) ? 0 : 1;
    
            // Get earliest start time for sorting
            $earliestStart = 'zz:zz';
            if (!empty($room['events'])) {
                $earliestStart = collect($room['events'])->min('start') ?? 'zz:zz';
            }
    
            return [
                $statusRank,
                $hasEvents,
                $earliestStart,
            ];
        })->values();
    
        // Pagination
        $perPage = $request->get('per_page', 9);
        $currentPage = $request->get('page', 1);
        $paginatedRooms = $rooms->forPage($currentPage, $perPage);
    
        return view('front_office.roomList', [
            'rooms' => $paginatedRooms,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'totalPages' => ceil($rooms->count() / $perPage),
            'lantai' => $request->lantai,
            'status' => $request->status,
            'selectedDate' => $selectedDate,
        ]);
    }
    
    public function filter(Request $request)
    {
        // Use the same logic as index method for consistency
        return $this->index($request);
    }
    
    // UPDATED: Method to get booking statuses from database
    private function getBookingStatusesFromDatabase($date)
    {
        // Get booking statuses from 'absen' table using id_booking
        return \App\Models\Absen::where('tanggal', $date)
            ->select('id_booking', 'status')
            ->get()
            ->mapWithKeys(function ($absen) {
                return [$absen->id_booking => $absen->status];
            });
    }
    
    private function getBookingTimes($date = null)
    {
        // Use provided date or default to today
        $targetDate = $date ?? Carbon::now()->toDateString();
        
        $allBookings = $this->fetchApiData('bookings', $targetDate);
    
        return $allBookings->flatMap(function ($item) use ($targetDate) {
            $bookingItems = collect($item['booking_items'] ?? [])
                ->filter(fn($bookingItem) => $bookingItem['booking_date'] === $targetDate)
                ->sortBy('booking_hour');
    
            $ruangans = collect($item['ruangans'] ?? []);
    
            return $ruangans->flatMap(function ($ruangan) use ($bookingItems, $item) {
                $ruanganId = $ruangan['id'];
    
                // Get booking times for this room - group by consecutive hours
                $roomBookings = $bookingItems->where('ruangan_id', $ruanganId);
                
                if ($roomBookings->isEmpty()) {
                    return collect();
                }
    
                // Group consecutive booking hours into separate events
                $groupedBookings = collect();
                $currentGroup = [];
                $previousHour = null;
    
                foreach ($roomBookings->sortBy('booking_hour') as $booking) {
                    $currentHour = $booking['booking_hour'];
                    
                    if ($previousHour === null || $currentHour === $previousHour + 1) {
                        // Continue current group or start new one
                        $currentGroup[] = $booking;
                    } else {
                        // Gap found, finish current group and start new one
                        if (!empty($currentGroup)) {
                            $groupedBookings->push($currentGroup);
                        }
                        $currentGroup = [$booking];
                    }
                    
                    $previousHour = $currentHour;
                }
                
                // Don't forget the last group
                if (!empty($currentGroup)) {
                    $groupedBookings->push($currentGroup);
                }
    
                // Convert each group to a booking entry
                return $groupedBookings->map(function ($group) use ($item) {
                    $startTime = collect($group)->min('booking_hour');
                    $endTime = collect($group)->max('booking_hour');
                    
                    if ($startTime !== null && $endTime !== null) {
                        // Add 1 hour to end time to show actual end time
                        $endTimeFormatted = Carbon::createFromTime($endTime)->addHour()->format('H:i');
                        
                        return [
                            'ruangan_id' => $group[0]['ruangan_id'],
                            'start_time' => Carbon::createFromTime($startTime)->format('H:i'),
                            'end_time' => $endTimeFormatted,
                            'booking_code' => $item['booking_code'] ?? null,
                        ];
                    }
                    
                    return null;
                })->filter(); // Remove null entries
            });
        });
    }
}