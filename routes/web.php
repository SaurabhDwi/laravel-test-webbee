<?php

use App\Http\Controllers\EventsController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\SchedulableEventController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return view('welcome');
});

Route::get('/events', [EventsController::class, 'getEventsWithWorkshops']);
Route::get('/futureevents', [EventsController::class, 'getFutureEventsWithWorkshops']);
Route::get('/menu', [MenuController::class, 'getMenuItems']);

Route::get('schedulabl/events/{eventId}/slots.json', [SchedulableEventController::class, 'returnAvilableBookingSlot'])->name('schedulableEvents')->where('eventId', '[0-9]+');
Route::post('book/events/{eventId}/slot', [SchedulableEventController::class, 'saveUserBooking'])->name('schedulableEvents')->where('eventId', '[0-9]+');
