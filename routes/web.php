<?php

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

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/dashboard', fn () => view('dashboard'));
    Route::get('/user/profile', [App\Http\Controllers\Livewire\UserProfileController::class, 'show'])->name('profile.show');
});

require __DIR__.'/auth.php';
