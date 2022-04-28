<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ModerationController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\UserController;
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

Route::any('/telegram/handler', [TelegramController::class, "handler"]);

Route::get('/openDeviceAdding', [ModerationController::class, "openDeviceAdding"])->name("openDeviceAdding");

Route::post('/addDevice', [ModerationController::class, "addDevice"]);

Route::get('/addDeviceType', [ModerationController::class, "openDeviceTypeAdding"])->name("typeAdding");

Route::post('/addDeviceType', [ModerationController::class, "addDeviceType"]);
