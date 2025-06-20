<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Blog\IO\Http\Controllers\BlogController;
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

Route::get('/', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.home');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Auth::routes();

// Admin routes (protected by auth middleware)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Post management
    Route::resource('posts', PostController::class);

    // Category management
    Route::resource('categories', CategoryController::class)->except(['show']);
});


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
