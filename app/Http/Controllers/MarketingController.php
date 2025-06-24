<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\PeminjamanBarang;
use App\Models\list_barang;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MarketingController extends Controller
{
    private $apiKey = 'JUrrUHAAdBepnJjpfVL2nY6mx9x4Cful4AhYxgs3Qj6HEgryn77KOoDr6BQZgHU1';

    public function index(Request $request)
    {
        // Get the date from the request or default to today
        $filterDate = Carbon::parse($request->get('date', Carbon::now()->toDateString()));
        $alternativeFormats = [
            $filterDate->format('d-m-Y'), 
            $filterDate->format('Y-m-d'), 
            $filterDate->format('Y/m/d'), 
        ];
        $dateFormats = array_merge([$filterDate->toDateString()], $alternativeFormats);
        $formatIndex = 0;
        $dataFound = false;

        // get data from list_barang
        $listBarang = list_barang::all();

        $allBookings = collect();
        $searchTerm = strtolower($request->get('search', ''));

        while ($formatIndex < count($dateFormats) && !$dataFound) {
            $currentDateFormat = $dateFormats[$formatIndex];
            Log::info("Mencoba mengambil data booking dengan format tanggal: {$currentDateFormat}");

            $page = 1;
            $allBookings = collect();

            do {
                $url = "https://event.mcc.or.id/api/event?status=booked&date={$currentDateFormat}&page={$page}";
                Log::info("Mengakses API untuk marketing, URL: {$url}");

                try {
                    $response = Http::withHeaders([
                        'X-API-KEY' => $this->apiKey,
                    ])->withoutVerifying()->get($url);

                    if ($response->successful()) {
                        $data = collect($response->json()['data'] ?? []);
                        $allBookings = $allBookings->merge($data);
                        if ($data->isNotEmpty()) {
                            $dataFound = true;
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
            } while (!empty($data));

            $formatIndex++;
        }

        // Process and filter the data
        $filteredBookings = $allBookings->map(function ($item) use ($filterDate) {
            $bookingItems = collect($item['booking_items'] ?? []);

            // Filter booking items to only include items with a matching booking_date
            $matchingBookingItems = $bookingItems->filter(function ($bookingItem) use ($filterDate) {
                return $bookingItem['booking_date'] == $filterDate->toDateString();
            });

            // If no booking items match the filtered date, return null
            if ($matchingBookingItems->isEmpty()) {
                return null;
            }

            // Calculate start and end times
            $startTime = $matchingBookingItems->min('booking_hour');
            $endTime = $matchingBookingItems->max('booking_hour');

            $item['start_time'] = $startTime ? Carbon::createFromTime($startTime, 0)->format('H:i') : null;
            $item['end_time'] = $endTime ? Carbon::createFromTime($endTime, 0)->format('H:i') : null;

            // Extract ruangan IDs from the filtered booking items
            $ruanganIds = $matchingBookingItems->pluck('ruangan_id')->unique();

            // Filter ruangans to only include those matching the IDs
            $item['ruangans'] = collect($item['ruangans'] ?? [])
                ->filter(fn($ruangan) => $ruanganIds->contains($ruangan['id']))
                ->values()
                ->toArray();

            $item['booking_items'] = $matchingBookingItems->toArray();

            // Fetch related database items based on booking code
            $item['database_items'] = PeminjamanBarang::where('kode_booking', $item['booking_code'])->get();

            return $item;
        })->filter(function ($item) use ($searchTerm) {
            if (empty($item['booking_items'])) {
                return false;
            }

            if ($searchTerm) {
                // Convert the entire $item to a single string to search all fields
                $itemText = strtolower(json_encode($item));
                return strpos($itemText, $searchTerm) !== false;
            }

            return true;
        });

        // Sort by start time
        $filteredBookings = $filteredBookings->sortBy([
            ['start_time', 'asc']
        ]);

        // Pagination
        $currentPage = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 6);
        $paginatedBookings = $filteredBookings->forPage($currentPage, $perPage);

        return view('marketing.peminjaman', [
            'bookings' => $paginatedBookings,
            'listBarang' => $listBarang,
            'totalPages' => ceil($filteredBookings->count() / $perPage),
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'filterDate' => $filterDate->toDateString(),
        ]);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'kode_booking' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.nama_item' => 'required_with:items|string|max:255',
            'items.*.jumlah' => 'required_with:items|numeric|min:1',
        ], [
            'items.*.nama_item.required_with' => 'Nama barang wajib diisi.',
            'items.*.jumlah.required_with' => 'Jumlah wajib diisi.',
            'items.*.jumlah.numeric' => 'Jumlah harus berupa angka.',
            'items.*.jumlah.min' => 'Jumlah minimal adalah 1.',
        ]);

        try {
            $kode_booking = $validated['kode_booking'] ?? null;
            $items = $validated['items'] ?? [];

            if (empty($items)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada barang yang ditambahkan.',
                ], 422); 
            }

            foreach ($items as $item) {
                PeminjamanBarang::create([
                    'nama_item' => $item['nama_item'],
                    'jumlah' => (int) $item['jumlah'],
                    'kode_booking' => $kode_booking,
                    'created_by' => auth()->id(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil ditambahkan!',
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan peminjaman barang', [
                'error' => $e->getMessage(),
                'input' => $request->all(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $item = PeminjamanBarang::findOrFail($id);
            $item->deleted_by = auth()->id();
            $item->save();
            $item->delete();

            return response()->json(['success' => true, 'message' => 'Item berhasil dihapus']);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus peminjaman barang', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Gagal menghapus item: ' . $e->getMessage()], 500);
        }
    }

    public function history(Request $request)
    {
        $data = PeminjamanBarang::withTrashed()
            ->with(['createdBy', 'deletedBy'])
            ->paginate(10);

        return view('marketing.history', [
            'data' => $data,
            'currentPage' => $data->currentPage(),
            'totalPages' => $data->lastPage(),
            'perPage' => $data->perPage(),
        ]);
    }
}