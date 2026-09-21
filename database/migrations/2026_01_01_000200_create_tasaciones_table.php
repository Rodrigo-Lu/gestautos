<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_contacto');
            $table->string('telefono_contacto', 30);
            $table->string('marca', 60);
            $table->string('modelo', 60);
            $table->smallInteger('anio');
            $table->unsignedInteger('kilometraje')->default(0);
            $table->decimal('monto_ofrecido', 15, 2)->nullable();
            $table->string('moneda', 3)->default('GS');
            $table->date('fecha');
            $table->string('estado', 15)->default('PENDIENTE');
            $table->text('observaciones')->nullable();

            // Usuario que evaluo la tasacion. Nulo mientras esta PENDIENTE.
            $table->foreignId('usuario_id')->nullable()
                  ->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['estado', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasaciones');
    }
};
