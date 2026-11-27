<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'genero')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('genero', ['m', 'f'])->nullable();
            });
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('genero', ['m', 'f'])->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'genero')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('genero');
            });
        }
    }
};