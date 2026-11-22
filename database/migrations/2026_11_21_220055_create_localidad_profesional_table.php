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
        Schema::create('localidad_profesional', function (Blueprint $table) {
            $table->id();

            $table->foreignId('profesional_id')
                ->constrained('profesionales')
                ->cascadeOnDelete();

            $table->foreignId('localidad_id')
                ->constrained('localidades')
                ->restrictOnDelete();

            $table->timestamps();

            $table->unique([
                'profesional_id',
                'localidad_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('localidad_profesional');
    }
};
