<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\FirebaseAuthController;
use App\Http\Controllers\AIChatController;
use App\Http\Controllers\ExportReportController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\CardController;

Route::get('/login', [FirebaseAuthController::class, 'showLogin'])
    ->name('login')
    ->middleware('guest');
Route::post('/auth/login', [FirebaseAuthController::class, 'login'])
    ->middleware('guest');
Route::get('/auth/logout', [FirebaseAuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [FirebaseAuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [FirebaseAuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [FirebaseAuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [FirebaseAuthController::class, 'resetPassword'])->name('password.update');

Route::middleware(['auth'])->group(function () {
    Route::get('/welcome', [WelcomeController::class, 'index'])->name('welcome');
    
    Route::get('/ai-chat', [AIChatController::class, 'index'])->name('ai.chat');
    Route::post('/ai-chat/send', [AIChatController::class, 'send'])->name('ai.chat.send');
});

Route::get('/', function () {
    if (session()->has('firebase_user_id')) {
        return redirect()->route('welcome');
    }
    return view('auth.login');
});

Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
Route::get('/reports', [ReportController::class, 'index'])->name('reports');
Route::get('/export-reports', [ExportReportController::class, 'exportPdf']);

// RFID Card Management Routesview.cards
Route::get('/cards', [CardController::class, 'index'])->name('view.cards');
Route::post('/cards/add', [CardController::class, 'addCard'])->name('cards.add');
Route::get('/cards/delete/{cardId}', [CardController::class, 'deleteCard'])->name('cards.delete');
Route::get('/cards/delete-all', [CardController::class, 'deleteAllCards'])->name('cards.delete.all');

// Optional API route for status updates
Route::get('/api/device-status', function() {
    $firebase = app('firebase.database');
    return response()->json([
        'system' => $firebase->getReference('logs/systemWemos')->getValue(),
        'rfid' => $firebase->getReference('logs/RFID/status')->getValue(),
        'servo' => $firebase->getReference('logs/servo/status')->getValue(),
    ]);
});

// API route for card-status
Route::get('/api/card-status', function() {
    $firebase = app('firebase.database');
    return response()->json([
        'cardId' => $firebase->getReference('smartcab/status_device')->getValue(),
        'access' => $firebase->getReference('smartcab/last_access')->getValue(),
    ]);
});