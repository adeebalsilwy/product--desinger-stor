<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Setting;
use App\Services\FileService;
use App\Services\TemplateService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind services to the container
        $this->app->singleton(FileService::class, function ($app) {
            return new FileService();
        });
        
        $this->app->singleton(TemplateService::class, function ($app) {
            return new TemplateService($app->make(FileService::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        Model::shouldBeStrict();
        Gate::define('super_admin', function (User $user) {
            return $user->role === 'super_admin';
        });

        // Share settings with all views
        View::composer('*', function ($view) {
            $settings = Setting::getSettings();
            $view->with('site_settings', $settings);
        });
    }
}