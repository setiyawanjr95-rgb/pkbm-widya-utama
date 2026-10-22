<?php

use App\Http\Controllers\SiteController;
use App\Http\Controllers\SubmissionDownloadController;
use Illuminate\Support\Facades\Route;

Route::controller(SiteController::class)->name('site.')->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/profil', 'profile')->name('profile');
    Route::get('/program', 'programs')->name('programs');
    Route::get('/program/{program}', 'program')->name('program');
    Route::get('/berita', 'posts')->name('posts');
    Route::get('/berita/{post}', 'post')->name('post');
    Route::get('/galeri', 'gallery')->name('gallery');
    Route::get('/kontak', 'contact')->name('contact');
    Route::get('/pendaftaran', 'registerForm')->name('register');
    Route::post('/pendaftaran', 'register')->middleware('throttle:5,1')->name('register.store');
    Route::get('/pendaftaran/berhasil', 'registerDone')->name('register.done');
});

Route::get('/admin/submissions/{submission}/download', SubmissionDownloadController::class)
    ->middleware('auth')
    ->name('submissions.download');
