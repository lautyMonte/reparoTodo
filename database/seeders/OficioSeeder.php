<?php

namespace Database\Seeders;

use App\Models\Oficio;
use Illuminate\Database\Seeder;

class OficioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Oficio::updateOrCreate([
            'tipo' => 'Plomero',
        ]);

        Oficio::updateOrCreate([
            'tipo' => 'Electricista',
        ]);

        Oficio::updateOrCreate([
            'tipo' => 'Gasista',
        ]);
    }
}
