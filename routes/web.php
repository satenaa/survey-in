<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use APp\Models\User;

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
    return view('login');
});

Route::get('/profile/{User:id}', [AdminController::class, 'profile']);

Route::get('/surveyor', [AdminController::class, 'surveyor']);
Route::get('/surveyor/{id}', [AdminController::class, 'surveyorProfile']);
Route::get('/profile/{id}', [AdminController::class, 'profile']);
Route::post('/tambah-user', [AdminController::class, 'store']);
Route::get('/tambah-user', function () {
    return view('tambah');
});
// Halaman Pengaturan Admin
Route::get('/pengaturan', [AdminController::class, 'pengaturan']);
Route::get('/pengaturan/edit-data-survey', [AdminController::class, 'editDataSurvey']);
Route::get('/pengaturan/ubah-password', [AdminController::class, 'ubahPassword']);
