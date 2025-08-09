<?php

use App\Http\Controllers\Admin\Web\Auth\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\Web\DashboardController as AdminDashboardController;
use Illuminate\Support\Facades\Auth;
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

// Auth::routes();
//Route::get('/', [App\Http\Controllers\HomeController::class, 'root'])->name('root');


Route::post('/update-profile/{id}', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('updateProfile');
Route::post('/update-password/{id}', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('updatePassword');


Route::prefix('admin')->middleware(['check_admin_access'])->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.loginForm');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login');
});

Route::prefix('admin')->middleware(['auth:admin', 'check_admin_access'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});
