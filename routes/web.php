<?php

use App\Http\Controllers;
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

Route::group(['middleware' => ['auth:sanctum', 'verified']],function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
    Route::get('/user/application', Controllers\UserApplicationController::class)->name('application.show');
    Route::get('/billing', Controllers\BillingController::class)->name('billing.show');
});

require __DIR__.'/fortify.php';
require __DIR__.'/jetstream.php';
