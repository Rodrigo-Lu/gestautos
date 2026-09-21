<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financiamientos', function (Blueprint $table) {
            $table->id();

            // Unique: una venta tiene a lo sumo un financiamiento.
            $table->foreignId('venta_id')->unique()
                  ->constrained('ventas')->cascadeOnDelete();

            $table->decimal('monto_anticipo', 15, 2)->default(0);
            $table->decimal('monto_financiado', 15, 2);
            $table->decimal('monto_total_con_interes', 15, 2);
            $table->unsignedSmallInteger('cantidad_cuotas');
            $table->decimal('tasa_interes_mensual', 5, 2)->default(0);
            $table->decimal('tasa_mora_diaria', 5, 2)->default(0);
            $table->date('fecha_primer_vencimiento');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financiamientos');
    }
};
