<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Exceptions\CustomExceptionHandler;
use App\Services\ScheduleRuleService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            CustomExceptionHandler::class
        );
    }
}
