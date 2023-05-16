<?php

use Illuminate\Support\Facades\App;
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


Route::view('', 'welcome');

/**
 * @hideFromAPIDocumentation
 */
Route::get('/testing', function () {
    if (App::environment() != 'local') return;
    $hello = new \App\Models\EntrepreneurProfile();
    return $hello::$transferValues;
});

/**
 * @hideFromAPIDocumentation
 */
Route::get('/mail-testing', function () {
    if (App::environment() != 'local') return;
    $application = \App\Models\Application::first();
    $emailTemplate = \App\Models\EmailTemplate::name(\App\Models\EmailTemplate::USER_APPLIED_NOTIFY_STAFF)->first();
    return (new \App\Notifications\NotificationMail($emailTemplate, 'subject', 'body'))->toMail(\App\Models\User::first());
});
