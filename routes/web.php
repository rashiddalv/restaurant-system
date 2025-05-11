<?php

use App\Http\Controllers\Api\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::get('/auth/reset-password/{token}', function ($token) {
    return redirect('http://localhost:5173/auth/reset-password/' . $token);
})->name('password.reset');

Route::post('/api/auth/reset-password', [ResetPasswordController::class, 'resetPassword'])->name('password.update');
