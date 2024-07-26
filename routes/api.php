<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\ChatController;

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

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

Route::group([
    'middleware' => 'authenticate',
    'prefix' => 'codes',
    'controller' => CodeController::class
], function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    //Route::get('/download/{id}', 'download');
});
Route::group([
    'middleware' => 'authenticate',
    'prefix' => 'chats',
    'controller' => ChatController::class
], function () {
    Route::get('/', 'listAll'); 
    Route::get('/{id}', 'show'); 
    Route::post('/', 'store'); 
});

Route::group([
    'middleware' => 'auth:user',
    'prefix' => 'admin',
    'controller' => AuthController::class
], function () {
    Route::post('import-users', 'import');
});