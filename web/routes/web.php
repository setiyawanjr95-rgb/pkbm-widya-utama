<?php

use App\Http\Controllers\SubmissionDownloadController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

Route::get('/admin/submissions/{submission}/download', SubmissionDownloadController::class)
    ->middleware('auth')
    ->name('submissions.download');
