<?php
use \App\Http\Controllers;
use App\Http\Controllers\AppointmentsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Public routes
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::get('/appointments/statistics', [AppointmentsController::class, 'getAppointmentStatistics']);


// Protected routes
Route::group(['middleware' => ['auth:sanctum', 'role:admin']], function () {
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    Route::get('/test', [\App\Http\Controllers\PatientsController::class, 'test']);
    Route::resource('/appointments', \App\Http\Controllers\AppointmentsController::class);
    Route::resource('/patients', \App\Http\Controllers\PatientsController::class);
    Route::resource('/secretary', \App\Http\Controllers\SecretaryController::class);
    Route::get('/patient/{id}/report-pdf', [Controllers\PatientsController::class, 'generateReportPDF']);
    Route::patch('/appointments/{appointment}/approve', [Controllers\AppointmentsController::class, 'approve']);




});
