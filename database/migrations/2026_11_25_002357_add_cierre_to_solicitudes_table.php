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
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->foreignId('cierre_propuesto_por')->nullable()->after('estado')->constrained('users')->nullOnDelete();
            $table->enum('cierre_tipo', ['finalizar', 'cancelar'])->nullable()->after('cierre_propuesto_por');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cierre_propuesto_por');
            $table->dropColumn('cierre_tipo');
        });
    }
};
