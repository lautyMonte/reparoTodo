
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profesionales', function (Blueprint $table) {
            $table->text('descripcion')->nullable();
            $table->string('nombre_comercial', 150)->nullable();
            $table->string('telefono_comercial', 20)->nullable();
            $table->string('foto')->nullable();

            $table->foreignId('localidad_id')
                ->nullable()
                ->constrained('localidades')
                ->restrictOnDelete();

            $table->boolean('publicado')->default(false);

            // Columnas del diseño anterior:
            // ya no deben impedir crear borradores.
            $table->string('provincia')->nullable()->change();
            $table->string('ciudad')->nullable()->change();
        });
    }

    public function down(): void
    {
        // No restaurar NOT NULL si existen perfiles
        // que todavía no tienen provincia o ciudad.
        if (
            DB::table('profesionales')
                ->whereNull('provincia')
                ->orWhereNull('ciudad')
                ->exists()
        ) {
            throw new RuntimeException(
                'No se puede revertir: existen perfiles con ubicación incompleta.'
            );
        }

        Schema::table('profesionales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('localidad_id');

            $table->dropColumn([
                'descripcion',
                'nombre_comercial',
                'telefono_comercial',
                'foto',
                'publicado',
            ]);

            $table->string('provincia')->nullable(false)->change();
            $table->string('ciudad')->nullable(false)->change();
        });
    }
};
