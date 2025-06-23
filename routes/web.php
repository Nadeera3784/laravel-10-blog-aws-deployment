<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Blog\IO\Http\Controllers\PostController;
use App\Blog\IO\Http\Controllers\CategoryController;
use App\Blog\IO\Http\Controllers\BlogController;

Route::get('/', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.home');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Auth::routes();

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return response()->json([
            'message' => 'Admin Dashboard',
            'user' => auth()->user()->name ?? 'Admin',
            'timestamp' => now()
        ]);
    })->name('dashboard');

    Route::resource('posts', PostController::class);
    Route::resource('categories', CategoryController::class)->except(['show']);
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});