<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;

// Routes d'authentification
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes protégées
Route::middleware(['auth'])->group(function () {
    Route::get('/home', function () {
        $courses = \App\Models\Course::all();
        return view('home', compact('courses'));
    })->name('home');
    
    // Routes des cours
    Route::resource('courses', CourseController::class);
});
