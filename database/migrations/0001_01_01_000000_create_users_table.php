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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('usuario',20)->unique();
            $table->string('nombre',50);
            $table->string('apellido',50);
            $table->string('telefono',16);
            $table->string('imagen')->nullable();
            $table->string('email')->unique();
            $table->enum('tipo', ['cliente', 'profesional'])->default('cliente');
            $table->timestamp('email_verified_at')->nullable(); // nulllable() significa que no es obligatorio, puede ser nulo
            $table->string('password');
            $table->rememberToken(); // Es el token que se genera para recordar la sesion del usuario
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
