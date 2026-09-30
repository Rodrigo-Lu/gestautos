<?php

use App\Console\Commands\GenerarAlertasCuotas;
use Illuminate\Support\Facades\Schedule;

// Todos los dias a las 08:00 hora de Asuncion: marca cuotas vencidas y
// genera avisos para cuotas que vencen hoy o mañana.
Schedule::command(GenerarAlertasCuotas::class)
    ->dailyAt('08:00')
    ->timezone('America/Asuncion')
    ->withoutOverlapping();
