<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Products routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
});

// API Routes (also need auth)
Route::middleware('auth')->prefix('api')->group(function () {
    // Products API
    Route::get('/products', [ProductController::class, 'getProducts']);
    Route::get('/products/{product}', [ProductController::class, 'show']);

    // Cart API
    Route::get('/cart', [CartController::class, 'getCartItems']);
    Route::post('/cart', [CartController::class, 'addToCart']);
    Route::patch('/cart/{cartItem}', [CartController::class, 'updateQuantity']);
    Route::delete('/cart/{cartItem}', [CartController::class, 'removeFromCart']);
    Route::delete('/cart', [CartController::class, 'clearCart']);

    // Checkout API
    Route::post('/checkout', [CheckoutController::class, 'checkout']);
});

require __DIR__ . '/auth.php';
