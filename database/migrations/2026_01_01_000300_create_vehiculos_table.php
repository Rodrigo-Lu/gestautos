<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_publicacion', 30)->unique();
            $table->string('marca', 60);
            $table->string('modelo', 60);
            $table->string('version', 60)->nullable();
            $table->smallInteger('anio');

            $table->decimal('precio', 15, 2);
            $table->decimal('precio_compra', 15, 2)->nullable();
            $table->string('moneda', 3)->default('GS');

            $table->unsignedInteger('kilometraje')->default(0);
            $table->string('color', 40)->nullable();
            $table->string('numero_chasis', 40)->nullable()->unique();
            $table->string('transmision', 15)->default('MANUAL');
            $table->string('combustible', 15)->default('NAFTA');
            $table->boolean('acepta_permuta')->default(false);
            $table->date('fecha_ingreso');
            $table->boolean('activo')->default(true);
            $table->string('estado', 15)->default('DISPONIBLE');
            $table->text('descripcion')->nullable();

            // Si el vehiculo entro por una permuta, de que tasacion vino.
            $table->foreignId('tasacion_origen_id')->nullable()
                  ->constrained('tasaciones')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['estado', 'activo']);
            $table->index(['marca', 'modelo']);
            $table->index('precio');
            $table->index('anio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
