<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ArtworkController;
use App\Http\Controllers\Api\AuthController;

// ==========================================
// 1. AUTHENTICATION ROUTES (Publik)
// ==========================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ==========================================
// 2. ARTWORKS ROUTES
// ==========================================

// A. Endpoint Publik (Bisa diakses bebas oleh React ARTURA untuk menampilkan galeri)
Route::get('artworks', [ArtworkController::class, 'index']);
Route::get('artworks/{slug}', [ArtworkController::class, 'show']);

// B. Endpoint Terproteksi (Wajib menggunakan Bearer Token untuk Admin/Store/Update/Delete)
Route::apiResource('artworks', ArtworkController::class)
    ->except(['index', 'show'])
    ->middleware('auth:sanctum');

// ==========================================
// 3. PROTECTED USER PROFILE & LOGOUT
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});