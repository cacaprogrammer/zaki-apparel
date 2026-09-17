<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('landing');
})->name('home');

Route::get('/test-livewire', function () {
    return view('test-livewire');
});

Route::get('/layout-test', function () {
    return view('layout-test');
});

Route::get('/profile', function () {
    return view('profile');
})->name('profile')->middleware('auth');

Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('lang.switch');

Route::get('/login', function () {
    return view('login');
})->name('login')->middleware('guest');

Route::get('/register', function () {
    return view('register');
})->name('register')->middleware('guest');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout')->middleware('auth');

Route::get('/product/{slug}', function (string $slug) {
    return view('product-detail', ['slug' => $slug]);
})->name('product.detail');

Route::get('/wishlist', function () {
    return view('wishlist');
})->name('wishlist');

Route::get('/category/{slug?}', function (?string $slug = null) {
    return view('category', ['slug' => $slug]);
})->name('category');

Route::get('/cart', function () {
    return view('cart');
})->name('cart');

Route::get('/checkout', function () {
    return view('checkout');
})->name('checkout');

Route::get('/order/{orderId?}', function (?string $orderId = null) {
    return view('order-detail', ['orderId' => $orderId ?? 'ZA-84719']);
})->name('order.detail');

Route::get('/order/{orderId}/refund', function (string $orderId) {
    return view('refund-request', ['orderId' => $orderId]);
})->name('refund.request')->middleware('auth');

Route::get('/order/{orderId}/refund/status', function (string $orderId) {
    return view('refund-status', ['orderId' => $orderId]);
})->name('refund.status')->middleware('auth');

Route::get('/order/{orderId}/refund/status', function (string $orderId) {
    return view('refund-status', ['orderId' => $orderId]);
})->name('refund.status')->middleware('auth');

Route::get('/my-orders', function () {
    return view('my-orders');
})->name('my-orders')->middleware('auth');

Route::get('/address', function () {
    return view('address');
})->name('address')->middleware('auth');

Route::get('/settings', function () {
    return view('settings');
})->name('settings')->middleware('auth');

Route::get('/chat', function () {
    return view('chat');
})->name('chat')->middleware('auth');

Route::get('/notifications', function () {
    return view('notifications');
})->name('notifications')->middleware('auth');

// ===== ADMIN AUTH =====
Route::get('/admin/login', function () {
    return view('admin-login');
})->name('admin.login')->middleware('guest');

Route::get('/admin/register', function () {
    return view('admin-register');
})->name('admin.register')->middleware('guest');

Route::post('/admin/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('admin.login');
})->name('admin.logout')->middleware('auth');

Route::get('/admin', function () {
    if (!auth()->user()->isAdmin()) {
        abort(403);
    }
    return 'Admin Dashboard — akan dibuat di batch berikutnya.';
})->name('admin.dashboard')->middleware('auth');