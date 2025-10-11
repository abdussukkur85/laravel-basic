<?php


use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/test', function () {
    return config('mail.admin_email', env('ADMIN_EMAIL'));
});

Route::get('contact', [ContactController::class,'create'])->name('contact.create');
Route::post('contact', [ContactController::class,'store'])->name('contact.store')->middleware('limit.contact');
