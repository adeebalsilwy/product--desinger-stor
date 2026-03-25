<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DesignController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SettingsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Design management - no middleware (authentication handled in controller)
Route::prefix('designs')->group(function () {
    Route::get('/', [DesignController::class, 'index']);
    Route::post('/', [DesignController::class, 'store']);
    Route::get('/{design}', [DesignController::class, 'show']);
    Route::put('/{design}', [DesignController::class, 'update']);
    Route::delete('/{design}', [DesignController::class, 'destroy']);
    Route::post('/{design}/duplicate', [DesignController::class, 'duplicate']);
    Route::post('/{design}/export', [DesignController::class, 'export']);
    Route::post('/{design}/preview', [DesignController::class, 'generatePreview']);
    Route::post('/{design}/save-as-template', [DesignController::class, 'saveAsTemplate']);
});

// Asset management - no middleware (authentication handled in controller)
Route::prefix('assets')->group(function () {
    Route::get('/', [AssetController::class, 'index']);
    Route::post('/upload', [AssetController::class, 'upload']);
    Route::get('/{asset}', [AssetController::class, 'show']);
    Route::delete('/{asset}', [AssetController::class, 'destroy']);
    Route::post('/bulk-delete', [AssetController::class, 'bulkDelete']);
});

// Orders - no middleware (authentication handled in controller)
Route::apiResource('orders', OrderController::class);
Route::get('orders/{order}/invoice', [OrderController::class, 'invoice']);

// User profile - requires auth
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Public API routes
Route::get('/settings', [SettingsController::class, 'index']);

// Designer Templates API - Make sure this is registered
Route::prefix('designer')->group(function () {
    Route::get('/templates', [\App\Http\Controllers\Api\TemplateController::class, 'index'])->name('api.designer.templates');
    Route::get('/templates/{template}', [\App\Http\Controllers\Api\TemplateController::class, 'show'])->name('api.designer.templates.show');
});

Route::prefix('v1')->group(function () {
    
    // Products (public)
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    Route::get('/product-categories', [ProductController::class, 'productTypes']);
    Route::get('/design-templates', [ProductController::class, 'designTemplates']);
    
    // Templates (public)
    Route::get('/templates', [\App\Http\Controllers\Api\TemplateController::class, 'index']);
    Route::get('/templates/{template}', [\App\Http\Controllers\Api\TemplateController::class, 'show']);
    Route::post('/templates/{template}/use', [DesignController::class, 'useTemplate']);
    
    // Product types (public)
    Route::get('/product-types', [\App\Http\Controllers\Api\ProductTypeController::class, 'index']);
    Route::get('/product-types/{slug}', [\App\Http\Controllers\Api\ProductTypeController::class, 'show']);
    Route::get('/product-types/{slug}/print-areas', [\App\Http\Controllers\Api\PrintAreaController::class, 'byProductType']);
    
    // Fonts & Cliparts (public)
    Route::get('/fonts', [\App\Http\Controllers\Api\FontController::class, 'index']);
    Route::get('/cliparts', [\App\Http\Controllers\Api\ClipartController::class, 'index']);
    Route::get('/clipart-categories', [\App\Http\Controllers\Api\ClipartCategoryController::class, 'index']);
    
    // Public designs
    Route::get('/public-designs', [DesignController::class, 'publicDesigns']);
});

// Web-authenticated API routes for customer users
Route::middleware(['web'])->group(function () {
    Route::prefix('customer')->group(function () {
        Route::prefix('designs')->group(function () {
            Route::get('/', [DesignController::class, 'index']);
            Route::post('/', [DesignController::class, 'store']);
            Route::get('/{design}', [DesignController::class, 'show']);
            Route::put('/{design}', [DesignController::class, 'update']);
            Route::delete('/{design}', [DesignController::class, 'destroy']);
            Route::post('/{design}/duplicate', [DesignController::class, 'duplicate']);
            Route::post('/{design}/export', [DesignController::class, 'export']);
            Route::post('/{design}/preview', [DesignController::class, 'generatePreview']);
            Route::post('/{design}/save-as-template', [DesignController::class, 'saveAsTemplate']);
        });
    });
});