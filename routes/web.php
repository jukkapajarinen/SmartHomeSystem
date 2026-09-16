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

Route::get('/login', [SessionController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login', [SessionController::class, 'store'])->middleware('guest');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

Route::get('/sensors', [SensorController::class, 'list'])->middleware('auth')->name('sensors.list');
Route::post('/sensors', [SensorController::class, 'store'])->middleware('auth')->name('sensors.store');
Route::patch('/sensors/{sensor}', [SensorController::class, 'update'])->middleware('auth')->name('sensors.update');
Route::patch('/sensors/{sensor}/move-up', [SensorController::class, 'moveUp'])->middleware('auth')->name('sensors.move-up');
Route::patch('/sensors/{sensor}/move-down', [SensorController::class, 'moveDown'])->middleware('auth')->name('sensors.move-down');
Route::delete('/sensors/{sensor}', [SensorController::class, 'destroy'])->middleware('auth')->name('sensors.destroy');

Route::get('/cameras', [CameraController::class, 'show'])->middleware('auth')->name('cameras.show');
Route::get('/cameras/feed', [CameraController::class, 'feed'])->middleware('auth')->name('cameras.feed');

Route::view('/profile', 'profile')->middleware('auth')->name('profile.edit');
Route::put('/password', [PasswordController::class, 'update'])->middleware('auth')->name('password.update');
Route::post('/logout', [SessionController::class, 'destroy'])->middleware('auth')->name('logout');
