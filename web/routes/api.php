<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/password', [AuthController::class, 'changePassword']);

    Route::controller(StudentController::class)->group(function () {
        Route::get('/me', 'me');
        Route::put('/me', 'updateProfile');
        Route::get('/courses', 'courses');
        Route::get('/courses/{course}/materials', 'materials');
        Route::post('/materials/{material}/complete', 'completeMaterial');
        Route::get('/materials/{material}/quiz', 'quiz');
        Route::post('/materials/{material}/quiz', 'submitQuiz');
        Route::get('/assignments', 'assignments');
        Route::post('/assignments/{assignment}/submit', 'submitAssignment');
        Route::get('/grades', 'grades');
        Route::get('/schedules', 'schedules');
        Route::get('/announcements', 'announcements');
        Route::get('/notifications', 'notifications');
        Route::post('/notifications/read', 'readNotifications');
    });
});
