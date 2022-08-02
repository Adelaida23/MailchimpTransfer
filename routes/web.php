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



Route::get('/mailchimp/transfer/index', [MailchimptransferController::class, 'index'])->name('transfer');
Route::post('/mailchimp/transfer', [MailchimptransferController::class, 'storeMailchimpToMailchimp'])->name('mailchimp.transfer');

Route::get('/mailchimp/subscribe/index', [MailchimptransferController::class, 'indexSubscribe'])->name('index.subscribe');
Route::post('mailchimp-subscribe', [MailchimptransferController::class, 'storeSubscribe'])->name('mailchimp.subscribe');

// transfer mailchimp active trail
Route::get('transfer/index', [MailchimptransferController::class, 'indexMailToActive'])->name('transfer.mailchimp_activetrail');
Route::post('transfer/mailchimp/to/activetrail', [MailchimptransferController::class, 'storeTransfer'])->name('transfer.mailchimpToactivetrail');
 