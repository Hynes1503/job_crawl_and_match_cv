<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\SupportTicket;
use App\Models\Log;
use App\Models\DeletedCrawl;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        view()->composer('layouts.admin', function ($view) {

            $view->with([
                'newTicketsCount'        => SupportTicket::countForAdminNavbar(),
                'newLogsCount'           => Log::countForAdminNavbar(),
                'newDeletedCrawlsCount'  => DeletedCrawl::countForAdminNavbar(),
            ]);
        });
    }
}
