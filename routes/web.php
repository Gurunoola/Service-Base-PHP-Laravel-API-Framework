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
    return "Laravel Base Restfull Api v1";
});

Route::get('/run-migrations', function () {
    Artisan::call('migrate', ["--force" => true]);
    Artisan::call('db:seed', ["--force" => true]);
    return 'Migrations and Seeders Run Successfully';
});

Route::get('/clear-cache', function () {
    Artisan::call('config:cache');
    Artisan::call('route:cache');
    Artisan::call('view:cache');
    return 'Cache Cleared and Configurations Cached';
});

Route::get('/optimize', function () {
    Artisan::call('config:cache');
    Artisan::call('route:cache');
    Artisan::call('view:cache');
    return 'Optimized';
});

Route::get('/create-storage-link', function () {
    Artisan::call('storage:link');
    return 'Storage link created successfully';
});