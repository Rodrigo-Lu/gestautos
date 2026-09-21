<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comprobantes', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 30)->unique();
            $table->string('tipo', 25);
            $table->date('fecha_emision');
            $table->decimal('monto', 15, 2);
            $table->string('moneda', 3)->default('GS');
            $table->string('ruta_archivo')->nullable();

            $table->foreignId('venta_id')->nullable()->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('cuota_id')->nullable()->constrained('cuotas')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('users')->restrictOnDelete();

            $table->timestamps();

            $table->index(['tipo', 'fecha_emision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprobantes');
    }
};
