<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;

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
    Route::get('/{id}', 'readCode');
    Route::post('/', 'store');
    Route::get('/download/{filename}', 'downloadCode');
    
    
    //Route::get('/download/{id}', 'download');
});
//'middleware' => 'auth',

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
    'middleware' => 'authenticate',
    'prefix' => 'users',
    'controller' => AuthController::class
], function () {
    Route::get('search', 'searchByName');
    Route::get('list', 'listAllUsers');
});

Route::group([
    //'middleware' => 'auth:user',
    'middleware' => 'auth.user',
    'prefix' => 'admin',
    'controller' => AuthController::class
], function () {
    Route::post('import-users', 'import');
    Route::put('{id}', 'updateUser');
    Route::delete('{id}', 'deleteUser');
});