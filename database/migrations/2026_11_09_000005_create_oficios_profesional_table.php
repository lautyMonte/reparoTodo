<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * tabla intermedia entre los profesionales y los oficios
     */
    public function up(): void
    {
        Schema::create('oficio_profesional', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profesional_id')->constrained('profesionales')->cascadeOnDelete();
            $table->foreignId('oficio_id')->constrained('oficios')->cascadeOnDelete();
            $table->unique(['profesional_id', 'oficio_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oficio_profesional');
    }
};
