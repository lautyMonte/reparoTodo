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
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('profesional_id')->constrained('profesionales')->cascadeOnDelete();
            $table->string('titulo', 80);
            $table->text('descripcion');
            $table->string('zona', 100)->nullable();
            $table->date('fecha_deseada')->nullable();
            $table->enum('estado', [
                'pendiente',
                'aceptada',
                'presupuestada',
                'en_chat',
                'finalizada',
                'cancelada',
                'rechazada',
            ])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};