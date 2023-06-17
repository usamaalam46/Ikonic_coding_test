<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConnectionController;

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
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/requests', [ConnectionController::class, 'index'])->name('requests');
Route::post('/requests', [ConnectionController::class, 'store']);
Route::delete('/requests/{id}', [ConnectionController::class, 'destroy']);
Route::patch('/requests/{id}', [ConnectionController::class, 'update']);
