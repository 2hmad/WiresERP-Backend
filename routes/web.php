<?php

use Carbon\Carbon;
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
    // Parse 2022-07-21T12:25:05.709419Z with Carbon and format it to a human readable date and disable the timezone
    $date = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', '2022-07-21T12:25:05.709419Z')->format('Y-m-d');
    return $date;
});
