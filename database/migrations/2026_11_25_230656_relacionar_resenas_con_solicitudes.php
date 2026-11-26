<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Evitar la pérdida accidental de reseñas existentes.
        if (DB::table('resenas')->exists()) {
            throw new \RuntimeException(
                'La tabla resenas debe estar vacía antes de aplicar esta migration.'
            );
        }

        // Eliminar las restricciones anteriores.
        Schema::table('resenas', function (Blueprint $table) {
            $table->dropForeign(['profesional_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('resenas', function (Blueprint $table) {
            $table->dropUnique(['profesional_id', 'user_id']);
        });

        // Reemplazar las columnas anteriores.
        Schema::table('resenas', function (Blueprint $table) {
            $table->dropColumn(['profesional_id', 'user_id']);

            $table->foreignId('solicitud_id')
                ->unique()
                ->constrained('solicitudes')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // Evitar la pérdida de reseñas durante la reversión.
        if (DB::table('resenas')->exists()) {
            throw new \RuntimeException(
                'La tabla resenas debe estar vacía antes de revertir esta migration.'
            );
        }

        Schema::table('resenas', function (Blueprint $table) {
            $table->dropForeign(['solicitud_id']);
        });

        Schema::table('resenas', function (Blueprint $table) {
            $table->dropUnique(['solicitud_id']);
            $table->dropColumn('solicitud_id');

            $table->foreignId('profesional_id')
                ->constrained('profesionales')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->unique(['profesional_id', 'user_id']);
        });
    }
};