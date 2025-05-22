<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;

// Routes d'authentification
Route::post('/login', [AuthController::class, 'login']);

// Routes publiques
Route::get('/courses', [CourseController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Routes pour les utilisateurs
    Route::apiResource('users', UserController::class);

    // Routes protégées pour les cours
    Route::post('/courses', [CourseController::class, 'store']);
    Route::put('/courses/{course}', [CourseController::class, 'update']);
    Route::delete('/courses/{course}', [CourseController::class, 'destroy']);
    Route::post('courses/{course}/generate-qr', [CourseController::class, 'generateNewQrCode']);

    // Routes pour les présences
    Route::apiResource('attendances', AttendanceController::class);
    Route::post('courses/{course}/sign', [AttendanceController::class, 'signAttendance']);
});

Route::get('/test', function () {
    return response()->json([
        'message' => 'Connexion API réussie!',
        'status' => 'success'
    ]);
}); 