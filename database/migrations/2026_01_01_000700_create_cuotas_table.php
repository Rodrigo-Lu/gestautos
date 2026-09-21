<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financiamiento_id')->constrained('financiamientos')->cascadeOnDelete();

            $table->unsignedSmallInteger('numero_cuota');
            $table->decimal('monto_cuota', 15, 2);
            $table->decimal('monto_capital', 15, 2)->default(0);
            $table->decimal('monto_interes', 15, 2)->default(0);
            $table->decimal('monto_pagado', 15, 2)->default(0);
            $table->decimal('monto_mora', 15, 2)->default(0);

            $table->date('fecha_vencimiento');
            $table->date('fecha_pago')->nullable();
            $table->string('forma_pago', 15)->nullable();
            $table->string('estado', 15)->default('PENDIENTE');

            // Usuario que cobro. Nulo hasta que se registra el pago.
            $table->foreignId('usuario_id')->nullable()
                  ->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['financiamiento_id', 'numero_cuota']);
            $table->index(['estado', 'fecha_vencimiento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuotas');
    }
};
