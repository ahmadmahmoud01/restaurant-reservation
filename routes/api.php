<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MealController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ReservationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//check availability
Route::get('/tables/{id}/availability', [TableController::class, 'checkAvailability'])->name('availability.check');

//reserve table
Route::post('/reserve_table', [ReservationController::class, 'reserveTable'])->name('table.reserve');

//list all items in the menu
Route::get('/meals', [MealController::class, 'index'])->name('meals.index');

//place an order
Route::post('/orders/place', [OrderController::class, 'placeOrder'])->name('orders.place');

//checkout and print invoice API
Route::get('/orders/{id}/invoice', [OrderController::class, 'getInvoice'])->name('print.invoice');
