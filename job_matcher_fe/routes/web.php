<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\JobMatcherController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\XPathController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\StatisticalController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/auth/google', [SocialAuthController::class, 'redirectGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'callbackGoogle']);

Route::get('/auth/github', [SocialAuthController::class, 'redirectGithub'])->name('auth.github');
Route::get('/auth/github/callback', [SocialAuthController::class, 'callbackGithub']);

Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');
Route::post('/reset-password/{token}', [ResetPasswordController::class, 'reset'])->name('password.update');

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
    Route::get('/crawl-runs/{runId}/export-training', [JobMatcherController::class, 'exportTrainingData'])
        ->name('crawl-runs.export-training');
});
Route::delete('/crawl-runs/{crawlRun}', [JobMatcherController::class, 'destroy'])
    ->name('crawl-runs.destroy')
    ->middleware(['auth']);

Route::middleware('auth')->group(function () {
    Route::get('/cv', [CvController::class, 'index'])->name('cv.form');
    Route::post('/cv', [CvController::class, 'store'])->name('cv.store');
    Route::delete('/cv/{cv}', [CvController::class, 'destroy'])->name('cv.destroy');
});

// Routes cho Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    // Thêm routes quản lý users
    Route::get('/admin/users', [AdminController::class, 'manageUsers'])->name('admin.users');
    Route::put('/admin/users/{user}', [AdminController::class, 'updateUserRole'])->name('admin.users.update');
    // Routes cho logs
    Route::resource('admin/logs', LogController::class)->names([
        'index' => 'admin.logs.index',
        'show' => 'admin.logs.show',
        'destroy' => 'admin.logs.destroy',
    ]);
    Route::get('/logs/pending', [LogController::class, 'pendingExpiration'])->name('admin.logs.pending');
    Route::post('/logs/{log}/handle', [LogController::class, 'handleExpiration'])->name('admin.logs.handle');
    // Routes cho deleted crawls
    Route::get('/admin/deleted-crawls', [AdminController::class, 'deletedCrawls'])->name('admin.deleted.crawls');
    Route::get('/admin/deleted-crawls/{deletedCrawl}', [AdminController::class, 'showDeletedCrawl'])->name('admin.deleted.crawls.show');
    Route::delete('/admin/deleted-crawls/{id}', [AdminController::class, 'destroyDeletedCrawl'])
        ->name('admin.deleted.crawls.destroy');

    Route::get('/site-selectors', [XPathController::class, 'index'])
        ->name('admin.site-selectors.index');

    Route::put('/site-selectors/{siteSelector}', [XPathController::class, 'update'])
        ->name('admin.site-selectors.update');

    Route::get('admin/statistics', [StatisticalController::class, 'index'])
        ->name('admin.statistics');
});

// Routes cho User
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user', [UserController::class, 'index'])->name('user.dashboard');
});
