<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SDGController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/save-sdg-result', [SDGController::class, 'saveSDGResult']);
Route::post('/store-booking-data', [SDGController::class, 'storeBookingData']);
Route::get('/sdg-data', [SDGController::class, 'getSDGData']);
Route::get('/fetch-and-store-booking-data', [SDGController::class, 'fetchAndStoreBookingData']);
Route::get('/komersial-data', [SDGController::class, 'getKomersialData']);
Route::get('/bidang-data', [SDGController::class, 'getBidangData']);
Route::get('/subsektor-data', [SDGController::class, 'getSubsektorData']);
Route::get('/sdgs-data', [SDGController::class, 'getSDGsData']);
Route::get('/total-event-pengunjung', [SDGController::class, 'getTotalEventPengunjung']);
Route::group(['prefix' => 'dashboard'], function() {
    Route::get('akumulasi-pengunjung', [SDGController::class, 'getAkumulasiPengunjung']);
    Route::get('available-floors', [SDGController::class, 'getAvailableFloors']);
    Route::get('rooms-by-floor', [SDGController::class, 'getRoomsByFloor']);
});

Route::get('/top3-sdgs', [SDGController::class, 'getTop3SDGs']);

Route::get('/decrypt-booking',[SDGController::class, 'decryptbooking']);