<?php

use App\Http\Controllers\FetchArticlesController;
use Illuminate\Support\Facades\Route;

Route::get('articles', FetchArticlesController::class)->name('articles');
