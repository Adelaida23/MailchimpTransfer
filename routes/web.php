<?php

use App\Http\Controllers\MailchimptransferController;
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



Route::get('/mailchimp/transfer/index', [MailchimptransferController::class, 'index']);
Route::post('/mailchimp/transfer', [MailchimptransferController::class, 'store'])->name('mailchimp.transfer');
