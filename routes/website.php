<?php

use App\Http\Controllers\website\ArticleController;
use App\Http\Controllers\website\HomeController;
use App\Http\Controllers\website\PageController;
use App\Http\Controllers\website\ProductController;
use App\Http\Controllers\website\SearchController;
use App\Http\Controllers\website\VideoController;
use Illuminate\Support\Facades\Route;

Route::name('website.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

    Route::get('/support', [ArticleController::class, 'support'])->name('support.index');
    Route::get('/literature', [ArticleController::class, 'literature'])->name('literature.index');
    Route::get('/news', [ArticleController::class, 'news'])->name('articles.index');
    Route::get('/news/{slug}', [ArticleController::class, 'show'])->name('articles.show');

    Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
    Route::get('/videos/{slug}', [VideoController::class, 'show'])->name('videos.show');

    Route::get('/about-us', [PageController::class, 'about'])->name('about');
    Route::get('/agents', [PageController::class, 'agents'])->name('agents');
    Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');
});
