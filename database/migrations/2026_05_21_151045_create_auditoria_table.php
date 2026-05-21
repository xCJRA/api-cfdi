<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('accion');           // ej: "factura.emitida", "cliente.creado"
            $table->string('entidad');          // ej: "Factura", "Cliente"
            $table->unsignedBigInteger('entidad_id')->nullable(); // el ID del registro afectado
            $table->json('datos_anteriores')->nullable();  // cómo estaba antes
            $table->json('datos_nuevos')->nullable();      // cómo quedó
            $table->string('ip')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditoria');
    }
};
