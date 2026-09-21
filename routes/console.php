<?php

use App\Console\Commands\GenerarAlertasCuotas;
use Illuminate\Support\Facades\Schedule;

// Todos los dias a las 07:00 hora de Asuncion: marca cuotas vencidas,
// recalcula la mora y genera las alertas del dia.
Schedule::command(GenerarAlertasCuotas::class)
    ->dailyAt('07:00')
    ->timezone('America/Asuncion')
    ->withoutOverlapping();
