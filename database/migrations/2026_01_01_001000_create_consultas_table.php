<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('telefono', 30);
            $table->string('email')->nullable();
            $table->text('mensaje');
            $table->date('fecha');
            $table->date('fecha_contacto')->nullable();
            $table->string('estado', 15)->default('NUEVA');

            $table->foreignId('vehiculo_id')->nullable()->constrained('vehiculos')->nullOnDelete();
            // Nullable: la mayoria de las consultas llegan de visitantes sin cuenta.
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            // Usuario que atendio la consulta.
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['estado', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
