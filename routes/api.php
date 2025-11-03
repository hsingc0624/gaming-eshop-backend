<?php
use App\Http\Controllers\{AuthController, ProductController, CartController, CheckoutController, OrderController, WishlistController, CampaignController};

Route::get('/products', [ProductController::class,'index']);
Route::get('/products/{slug}', [ProductController::class,'show']);
Route::get('/categories', [ProductController::class,'categories']);


Route::post('/auth/register',[AuthController::class,'register']);
Route::post('/auth/login',[AuthController::class,'login']);
Route::post('/auth/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');


Route::middleware('throttle:60,1')->group(function(){
Route::post('/cart', [CartController::class,'attach']);
Route::post('/cart/items', [CartController::class,'add']);
Route::put('/cart/items/{id}', [CartController::class,'update']);
Route::delete('/cart/items/{id}', [CartController::class,'remove']);
Route::get('/cart', [CartController::class,'show']);
});


Route::middleware('auth:sanctum')->group(function(){
Route::get('/orders', [OrderController::class,'index']);
Route::get('/orders/{number}', [OrderController::class,'show']);
Route::post('/checkout', [CheckoutController::class,'checkout']);


Route::get('/wishlist', [WishlistController::class,'index']);
Route::post('/wishlist/{product}', [WishlistController::class,'toggle']);
});


Route::middleware(['auth:sanctum','permission:manage campaigns'])->group(function(){
Route::get('/campaigns', [CampaignController::class,'index']);
Route::post('/campaigns', [CampaignController::class,'store']);
Route::post('/campaigns/{id}/schedule', [CampaignController::class,'schedule']);
Route::post('/campaigns/{id}/send-test', [CampaignController::class,'sendTest']);
});