<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('cedula', 25)->unique();
            $table->string('telefono', 30);
            $table->string('direccion')->nullable();
            $table->string('email')->nullable();

            // Nullable: el cliente que compra en el local no necesita cuenta.
            // Unique: un usuario del portal corresponde a un solo cliente.
            $table->foreignId('usuario_id')->nullable()->unique()
                  ->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
