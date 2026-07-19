<?php

namespace App\Providers;

use App\Models\Product;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer('layouts.admin', function ($view) {
            $storeSettings = \App\Models\StoreSetting::first();

            if ($storeSettings && isset($storeSettings->notify_low_stock) && ! $storeSettings->notify_low_stock) {
                $lowStockCount = 0;
            } else {
                $lowStockCount = Product::where('is_active', true)
                    ->whereColumn('stock', '<=', 'stock_min')
                    ->count();
            }

            $view->with('lowStockCount', $lowStockCount)
                 ->with('storeSettings', $storeSettings);
        });
    }
}
