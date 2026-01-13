<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\JobMatcherController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\XPathController;
use App\Http\Controllers\StatisticalController;
use App\Http\Controllers\AdminTicketController;

Route::get('/', fn() => view('welcome'))->name('welcome');

Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/auth/google', [SocialAuthController::class, 'redirectGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'callbackGoogle']);

Route::get('/auth/github', [SocialAuthController::class, 'redirectGithub'])->name('auth.github');
Route::get('/auth/github/callback', [SocialAuthController::class, 'callbackGithub']);

Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');
Route::post('/reset-password/{token}', [ResetPasswordController::class, 'reset'])->name('password.update');


Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [JobMatcherController::class, 'dashboard'])->name('dashboard');

    Route::prefix('cv')->group(function () {
        Route::get('/', [CvController::class, 'index'])->name('cv.form');
        Route::post('/', [CvController::class, 'store'])->name('cv.store');
        Route::delete('/{cv}', [CvController::class, 'destroy'])->name('cv.destroy');
    });

    Route::prefix('match-cv')->group(function () {
        Route::get('/', [JobMatcherController::class, 'showMatchForm'])->name('match.cv.form');
        Route::post('/', [JobMatcherController::class, 'processMatch'])->name('match.cv');
    });

    Route::prefix('crawl')->group(function () {
        Route::get('/', [JobMatcherController::class, 'showCrawlForm'])->name('crawl.form');
        Route::post('/jobs', [JobMatcherController::class, 'crawlJobs'])->name('crawl.jobs');
        Route::get('/history', [JobMatcherController::class, 'crawlHistory'])->name('crawl.history');
        
        Route::middleware('web')->group(function () {
            Route::post('/match-with-run/{runId}', [JobMatcherController::class, 'matchWithRun'])
                ->name('match.with.run');
        });

        Route::get('/runs/{runId}/export-training', [JobMatcherController::class, 'exportTrainingData'])
            ->name('crawl-runs.export-training');

        Route::delete('/runs/{crawlRun}', [JobMatcherController::class, 'destroy'])
            ->name('crawl-runs.destroy');
    });

    Route::prefix('support')->group(function () {
        Route::get('/', [SupportController::class, 'index'])->name('support.index');
        Route::get('/create', [SupportController::class, 'create'])->name('support.create');
        Route::post('/', [SupportController::class, 'store'])->name('support.store');
        Route::get('/{ticket}', [SupportController::class, 'show'])->name('support.show');
        Route::post('/{ticket}/reply', [SupportController::class, 'reply'])->name('support.reply');
    });

    Route::middleware('role:user')->group(function () {
        Route::get('/user', [UserController::class, 'index'])->name('user.dashboard');
    });


    Route::middleware('restrict.admin')->prefix('admin')->group(function () {

        Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

        Route::get('/users', [AdminController::class, 'manageUsers'])->name('admin.users');
        Route::put('/users/{user}', [AdminController::class, 'updateUserRole'])->name('admin.users.update');

        Route::resource('logs', LogController::class)->names([
            'index'  => 'admin.logs.index',
            'show'   => 'admin.logs.show',
            'destroy' => 'admin.logs.destroy',
        ]);
        Route::get('/logs/pending', [LogController::class, 'pendingExpiration'])->name('admin.logs.pending');
        Route::post('/logs/{log}/handle', [LogController::class, 'handleExpiration'])->name('admin.logs.handle');

        Route::get('/deleted-crawls', [AdminController::class, 'deletedCrawls'])->name('admin.deleted.crawls');
        Route::get('/deleted-crawls/{deletedCrawl}', [AdminController::class, 'showDeletedCrawl'])->name('admin.deleted.crawls.show');
        Route::delete('/deleted-crawls/{id}', [AdminController::class, 'destroyDeletedCrawl'])->name('admin.deleted.crawls.destroy');

        Route::get('/site-selectors', [XPathController::class, 'index'])->name('admin.site-selectors.index');
        Route::put('/site-selectors/{siteSelector}', [XPathController::class, 'update'])->name('admin.site-selectors.update');

        Route::get('/statistics', [StatisticalController::class, 'index'])->name('admin.statistics');

        Route::prefix('tickets')->group(function () {
            Route::get('/', [AdminTicketController::class, 'index'])->name('admin.tickets.index');
            Route::get('/{ticket}', [AdminTicketController::class, 'show'])->name('admin.tickets.show');
            Route::post('/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('admin.tickets.reply');
            Route::post('/{ticket}/status', [AdminTicketController::class, 'changeStatus'])->name('admin.tickets.status');
        });
    });
});