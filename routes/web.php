<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Blog\IO\Http\Controllers\PostController;
use App\Blog\IO\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});

Route::get('/', function () {
    return response()->json([
        'message' => 'Laravel Blog Application is running!',
        'status' => 'success',
        'timestamp' => now(),
        'environment' => config('app.env'),
        'debug' => config('app.debug')
    ]);
})->name('home');

// Simple blog routes without complex controllers for now
Route::get('/blog', function () {
    return response()->json([
        'message' => 'Blog section - Coming soon!',
        'status' => 'success',
        'timestamp' => now()
    ]);
})->name('blog.index');

Auth::routes();

// Admin routes (protected by auth middleware)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return response()->json([
            'message' => 'Admin Dashboard',
            'user' => auth()->user()->name ?? 'Admin',
            'timestamp' => now()
        ]);
    })->name('dashboard');

    // Post management
    Route::resource('posts', PostController::class);

    // Category management
    Route::resource('categories', CategoryController::class)->except(['show']);
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home.controller');
