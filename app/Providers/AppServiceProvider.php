<?php

namespace App\Providers;

use App\Models\Alerta;
use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\Venta;
use App\Observers\AuditoriaObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Auditoria sobre los modelos que el negocio no puede darse el lujo de perder.
        Vehiculo::observe(AuditoriaObserver::class);
        Cliente::observe(AuditoriaObserver::class);
        Venta::observe(AuditoriaObserver::class);

        // Contador de la campana en el navbar.
        View::composer('partials.navbar', function ($view) {
            $view->with('alertasNoLeidas', Alerta::noLeidas()->count());
        });
    }
}
