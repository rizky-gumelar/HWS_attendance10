<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;

use App\Models\PengajuanCuti;
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
        View::composer('*', function ($view) {
            $jumlahPending = PengajuanCuti::where('status', ['pending', 'disetujui spv'])->count();
            $view->with('jumlahPending', $jumlahPending);
        });
    }
}
