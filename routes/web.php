<?php

use App\Http\Controllers\AlertaController;
use App\Http\Controllers\Cliente\MisCuotasController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ComprobanteController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\CuotaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FotoVehiculoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Publico\CatalogoController;
use App\Http\Controllers\Publico\ConsultaPublicaController;
use App\Http\Controllers\Publico\SimuladorController;
use App\Http\Controllers\Publico\TasacionPublicaController;
use App\Http\Controllers\Publico\WhatsAppController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\TasacionController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Publico: el visitante nunca necesita cuenta
|--------------------------------------------------------------------------
*/
Route::get('/', [CatalogoController::class, 'index'])->name('catalogo.index');
Route::get('/vehiculos/{vehiculo}', [CatalogoController::class, 'show'])->name('catalogo.show');
Route::get('/vehiculos/{vehiculo}/whatsapp', [WhatsAppController::class, 'redirect'])
    ->middleware('throttle:30,1')
    ->name('whatsapp.redirect');
Route::match(['get', 'post'], '/simulador', SimuladorController::class)->name('simulador');
Route::post('/consultas', [ConsultaPublicaController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('consultas.publicas.store');
Route::get('/permuta', [TasacionPublicaController::class, 'create'])->name('permuta.create');
Route::post('/permuta', [TasacionPublicaController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('permuta.store');

/*
|--------------------------------------------------------------------------
| Autenticacion
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'mostrar'])->name('login');
    Route::post('/login', [LoginController::class, 'autenticar'])->middleware('throttle:5,1');
});

Route::post('/logout', [LoginController::class, 'salir'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Portal del cliente
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'rol:CLIENTE'])->prefix('mi-cuenta')->group(function () {
    Route::get('/cuotas', MisCuotasController::class)->name('cliente.cuotas');
});

/*
|--------------------------------------------------------------------------
| Panel interno
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'rol:PROPIETARIO,ADMINISTRADOR,EMPLEADO'])
    ->prefix('panel')
    ->group(function () {

        Route::get('/', DashboardController::class)->name('dashboard');

        // Vehiculos
        Route::resource('vehiculos', VehiculoController::class);
        Route::post('vehiculos/{vehiculo}/fotos/{foto}/principal', [FotoVehiculoController::class, 'principal'])
            ->name('vehiculos.fotos.principal');
        Route::delete('vehiculos/{vehiculo}/fotos/{foto}', [FotoVehiculoController::class, 'destroy'])
            ->name('vehiculos.fotos.destroy');

        // Clientes
        Route::resource('clientes', ClienteController::class);
        Route::post('clientes/{cliente}/acceso', [ClienteController::class, 'darAcceso'])
            ->name('clientes.acceso.crear');
        Route::delete('clientes/{cliente}/acceso', [ClienteController::class, 'revocarAcceso'])
            ->name('clientes.acceso.revocar');

        // Ventas
        Route::resource('ventas', VentaController::class)->only(['index', 'create', 'store', 'show']);
        Route::post('ventas/{venta}/anular', [VentaController::class, 'anular'])->name('ventas.anular');

        // Cobranzas
        Route::get('cuotas', [CuotaController::class, 'index'])->name('cuotas.index');
        Route::get('cuotas/{cuota}/cobrar', [CuotaController::class, 'cobrar'])->name('cuotas.cobrar');
        Route::post('cuotas/{cuota}/cobrar', [CuotaController::class, 'registrarPago'])->name('cuotas.pagar');
        Route::get('cuotas/{cuota}/whatsapp', [CuotaController::class, 'avisarWhatsApp'])
            ->middleware('throttle:30,1')
            ->name('cuotas.whatsapp');

        // Alertas
        Route::get('alertas', [AlertaController::class, 'index'])->name('alertas.index');
        Route::post('alertas/{alerta}/leida', [AlertaController::class, 'marcarLeida'])->name('alertas.leida');
        Route::post('alertas/leidas', [AlertaController::class, 'marcarTodas'])->name('alertas.todas');

        // Consultas y tasaciones
        Route::get('consultas', [ConsultaController::class, 'index'])->name('consultas.index');
        Route::patch('consultas/{consulta}', [ConsultaController::class, 'actualizar'])->name('consultas.actualizar');
        Route::get('tasaciones', [TasacionController::class, 'index'])->name('tasaciones.index');
        Route::patch('tasaciones/{tasacion}', [TasacionController::class, 'evaluar'])->name('tasaciones.evaluar');

        // Comprobantes
        Route::get('comprobantes/{comprobante}', [ComprobanteController::class, 'descargar'])
            ->name('comprobantes.descargar');

        // Reportes
        Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('reportes/{tipo}', [ReporteController::class, 'generar'])->name('reportes.generar');
    });

/*
|--------------------------------------------------------------------------
| Solo direccion
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'rol:PROPIETARIO,ADMINISTRADOR'])
    ->prefix('panel')
    ->group(function () {
        Route::resource('usuarios', UsuarioController::class)->except(['show', 'destroy']);
    });
