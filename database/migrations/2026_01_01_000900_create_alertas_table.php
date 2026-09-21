<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 15);
            $table->string('mensaje');
            $table->date('fecha_generacion');
            $table->boolean('leida')->default(false);

            $table->foreignId('cuota_id')->nullable()->constrained('cuotas')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->cascadeOnDelete();

            $table->timestamps();

            // Una alerta de cada tipo por cuota: el comando diario no duplica.
            $table->unique(['cuota_id', 'tipo']);
            $table->index(['leida', 'fecha_generacion']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
