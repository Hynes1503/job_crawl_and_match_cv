<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\JobMatcherController;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/auth/google', [SocialAuthController::class, 'redirectGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'callbackGoogle']);

Route::get('/auth/github', [SocialAuthController::class, 'redirectGithub'])->name('auth.github');
Route::get('/auth/github/callback', [SocialAuthController::class, 'callbackGithub']);

Route::get('/match-cv', [JobMatcherController::class, 'showMatchForm'])->name('match.cv.form');
Route::post('/match-cv', [JobMatcherController::class, 'processMatch'])->name('match.cv');

// Tùy chọn: trang crawl job
Route::get('/crawl-jobs', [JobMatcherController::class, 'showCrawlForm'])->name('crawl.form');
Route::post('/crawl-jobs', [JobMatcherController::class, 'crawlJobs'])->name('crawl.jobs');

Route::get('/dashboard', function () {
    return view('match-cv');
})->middleware('auth')->name('dashboard');
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/match-with-run/{runId}', [JobMatcherController::class, 'matchWithRun'])
        ->name('match.with.run');
});
Route::middleware('auth')->group(function () {
    Route::get('/crawl-history', [JobMatcherController::class, 'crawlHistory'])->name('crawl.history');
});
Route::delete('/crawl-runs/{crawlRun}', [JobMatcherController::class, 'destroy'])
     ->name('crawl-runs.destroy')
     ->middleware(['auth']);