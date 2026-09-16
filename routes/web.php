<?php

use App\Http\Controllers\SessionController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\CameraController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
  return redirect('/dashboard'); // Redirect to your desired route
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store']);
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
  Route::get('/sensors', [SensorController::class, 'list'])->name('sensors.list');
  Route::post('/sensors', [SensorController::class, 'store'])->name('sensors.store');
  Route::get('/sensors/{sensor}/edit', [SensorController::class, 'edit'])->name('sensors.edit');
  Route::patch('/sensors/{sensor}', [SensorController::class, 'update'])->name('sensors.update');

  Route::get('/cameras', [CameraController::class, 'show'])->name('cameras.show');
  Route::get('/cameras/feed', [CameraController::class, 'feed'])->name('cameras.feed');
});

Route::middleware('auth')->group(function () {
    Route::view('/profile', 'profile')->name('profile.edit');
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');
    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
});
