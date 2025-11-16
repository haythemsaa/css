<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\ReductionCodeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| Routes are loaded by the RouteServiceProvider and assigned the "api" middleware group.
|
*/

// Public routes
Route::prefix('v1')->group(function () {

    // Authentication
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });

    // Partners (Public - anyone can browse)
    Route::prefix('partners')->group(function () {
        Route::get('categories', [PartnerController::class, 'categories']);
        Route::get('featured', [PartnerController::class, 'featured']);
        Route::get('/', [PartnerController::class, 'index']);
        Route::get('{slug}', [PartnerController::class, 'show']);
        Route::get('{slug}/offers', [PartnerController::class, 'partnerOffers']);
    });

    // Offers (Public - anyone can browse)
    Route::prefix('offers')->group(function () {
        Route::get('/', [PartnerController::class, 'offers']);
        Route::get('{slug}', [PartnerController::class, 'showOffer']);
    });

    // Reduction codes validation (for partners)
    Route::prefix('codes')->group(function () {
        Route::post('validate', [ReductionCodeController::class, 'validate']);
        Route::post('{code}/use', [ReductionCodeController::class, 'use']);
    });

    // Content (Public - browsing, premium content requires auth)
    Route::prefix('content')->group(function () {
        Route::get('/', [ContentController::class, 'index']);
        Route::get('featured', [ContentController::class, 'featured']);
        Route::get('{slug}', [ContentController::class, 'show']);
    });

    // Players (Public)
    Route::prefix('players')->group(function () {
        Route::get('/', [PlayerController::class, 'index']);
        Route::get('position/{position}', [PlayerController::class, 'byPosition']);
        Route::get('{slug}', [PlayerController::class, 'show']);
    });

    // Matches (Public)
    Route::prefix('matches')->group(function () {
        Route::get('/', [MatchController::class, 'index']);
        Route::get('upcoming', [MatchController::class, 'upcoming']);
        Route::get('results', [MatchController::class, 'results']);
        Route::get('{id}', [MatchController::class, 'show']);
    });
});

// Protected routes (require authentication)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // Authentication
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'profile']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        Route::post('request-socios-verification', [AuthController::class, 'requestSociosVerification']);
    });

    // Reduction codes (require authentication)
    Route::prefix('codes')->group(function () {
        Route::post('generate/{offerSlug}', [ReductionCodeController::class, 'generate']);
        Route::get('my-codes', [ReductionCodeController::class, 'myCodes']);
        Route::get('{code}', [ReductionCodeController::class, 'show']);
    });

    // Content interactions (require authentication)
    Route::prefix('content')->group(function () {
        Route::post('{slug}/like', [ContentController::class, 'toggleLike']);
    });
});
