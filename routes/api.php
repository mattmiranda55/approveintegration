<?php

use App\Http\Controllers\ApproveIntegrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::get('/health', fn () => response()->json(['ok' => true]));

Route::prefix('approve')->group(function () {
    // Health check
    Route::get('/', [ApproveIntegrationController::class, 'index']);

    // Full analysis endpoint
    Route::post('/analyze', [ApproveIntegrationController::class, 'analyze']);

    // Individual page analysis endpoints
    Route::post('/analyze/product', [ApproveIntegrationController::class, 'analyzeProduct']);
    Route::post('/analyze/cart', [ApproveIntegrationController::class, 'analyzeCart']);
    Route::post('/analyze/gallery', [ApproveIntegrationController::class, 'analyzeGallery']);

    // List available selectors
    Route::get('/selectors', [ApproveIntegrationController::class, 'listSelectors']);
});
