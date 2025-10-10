<?php


use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/test', function () {
    return view('welcome');
});

Route::get('contact', [ContactController::class,'create'])->name('contact.create');
Route::post('contact', [ContactController::class,'store'])->name('contact.store')->middleware('limit.contact');
