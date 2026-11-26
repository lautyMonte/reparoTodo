<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EntornoTestingTest extends TestCase
{
    public function test_entorno_de_testing_y_migrations(): void
    {
        // Verificar que estamos en un entorno aislado.
        $this->assertSame('testing', app()->environment());

        $this->assertSame('sqlite', config('database.default'));

        $this->assertSame(':memory:', DB::connection()->getDatabaseName());

        // Ejecutar las migrations en la base de datos temporal.
        $resultado = Artisan::call('migrate', [
            '--force' => true,
        ]);

        $this->assertSame(0, $resultado, Artisan::output());

        // Comprobar las tablas necesarias.
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('profesionales'));
        $this->assertTrue(Schema::hasTable('solicitudes'));
        $this->assertTrue(Schema::hasTable('resenas'));

        // Comprobar la nueva estructura de reseñas.
        $this->assertTrue(
            Schema::hasColumn('resenas', 'solicitud_id')
        );

        $this->assertFalse(
            Schema::hasColumn('resenas', 'profesional_id')
        );

        $this->assertFalse(
            Schema::hasColumn('resenas', 'user_id')
        );
    }
}