<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommunityController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\LearningController;
use App\Http\Controllers\Api\LoanController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/home', [DashboardController::class, 'home']);

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/dashboard/user', [DashboardController::class, 'userDashboard']);
    Route::get('/loans', [LoanController::class, 'index']);
    Route::post('/loans', [LoanController::class, 'store']);
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::post('/payments', [PaymentController::class, 'store']);

    Route::get('/learning-modules', [LearningController::class, 'index']);
    Route::post('/learning-modules/{module}/complete', [LearningController::class, 'complete']);

    Route::get('/community-posts', [CommunityController::class, 'index']);
    Route::post('/community-posts', [CommunityController::class, 'store']);
    Route::post('/community-posts/{post}/comments', [CommunityController::class, 'comment']);

    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard/admin', [DashboardController::class, 'adminDashboard']);
        Route::patch('/loans/{loan}/status', [LoanController::class, 'updateStatus']);
        Route::patch('/payments/{payment}/status', [PaymentController::class, 'updateStatus']);
        Route::delete('/community-posts/{post}', [CommunityController::class, 'destroy']);
        Route::get('/admin/users', [AdminController::class, 'users']);
        Route::post('/admin/notifications', [AdminController::class, 'sendNotification']);
        Route::post('/admin/ojk-report', [AdminController::class, 'ojkReport']);
    });
});
