<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ContactController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful as Stateful;
use App\Http\Controllers\{
    AuthController,
    ProductController,
    CartController,
    CheckoutController,
    OrderController,
    WishlistController,
    CampaignController,
    UserAdminController
};

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::get('/categories', [ProductController::class, 'categories']);

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $r) => $r->user());

    Route::post('/products', [ProductController::class, 'store']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])
        ->middleware('permission:manage products');

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{number}', [OrderController::class, 'show']);
    Route::patch('/orders/{number}', [OrderController::class, 'update'])
        ->middleware('permission:manage orders');
    Route::post('/orders/{number}/refund', [OrderController::class, 'refund'])
        ->middleware('permission:manage orders');
    Route::get('/orders/{number}/invoice', [OrderController::class, 'invoice']);
    Route::post('/checkout', [CheckoutController::class, 'checkout']);

    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle']);
});

Route::middleware(['auth:sanctum', 'permission:manage campaigns'])->group(function () {
    Route::get('/campaigns', [CampaignController::class, 'index']);
    Route::get('/campaigns/metrics', [CampaignController::class, 'metrics']);
    Route::post('/campaigns', [CampaignController::class, 'store']);
    Route::post('/campaigns/{id}/schedule', [CampaignController::class, 'schedule']);
    Route::post('/campaigns/{id}/send-test', [CampaignController::class, 'sendTest']);
});

Route::middleware(['auth:sanctum','permission:manage users'])->prefix('admin')->group(function () {
    Route::get('/users', [UserAdminController::class, 'index']);
    Route::patch('/users/{id}', [UserAdminController::class, 'update']);
    Route::get('/roles', [UserAdminController::class, 'roles']);
    Route::post('/users', [UserAdminController::class, 'store']);
});

Route::post('/contact', [ContactController::class, 'store'])
    ->middleware(['throttle:10,1'])
    ->withoutMiddleware([Stateful::class]);

Route::middleware('throttle:60,1')
    ->withoutMiddleware([Stateful::class])
    ->group(function () {
        Route::post('/cart', [CartController::class, 'attach']);
        Route::post('/cart/items', [CartController::class, 'add']);
        Route::put('/cart/items/{id}', [CartController::class, 'update']);
        Route::delete('/cart/items/{id}', [CartController::class, 'remove']);
        Route::get('/cart', [CartController::class, 'show']);
    });
