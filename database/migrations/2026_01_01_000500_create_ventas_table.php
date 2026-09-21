<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->restrictOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('usuario_id')->constrained('users')->restrictOnDelete();

            // Permuta recibida como parte de pago.
            $table->foreignId('tasacion_permuta_id')->nullable()
                  ->constrained('tasaciones')->nullOnDelete();

            $table->date('fecha');
            $table->decimal('precio_venta', 15, 2);
            $table->decimal('monto_permuta', 15, 2)->default(0);

            // Moneda y cotizacion congeladas al momento de la venta.
            $table->string('moneda', 3)->default('GS');
            $table->decimal('tipo_cambio', 15, 4)->nullable();

            $table->string('tipo_venta', 15);
            $table->string('forma_pago_anticipo', 15)->nullable();
            $table->string('estado', 15)->default('VIGENTE');
            $table->text('observaciones')->nullable();
            $table->text('motivo_anulacion')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('fecha');
            $table->index(['estado', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
