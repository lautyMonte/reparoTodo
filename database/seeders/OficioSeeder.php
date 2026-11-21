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
            'tipo' => 'Plomería',
        ]);

        Oficio::updateOrCreate([
            'tipo' => 'Electridad',
        ]);

        Oficio::updateOrCreate([
            'tipo' => 'Gas',
        ]);

        Oficio::updateOrCreate([
            'tipo' => 'Cerrajería',
        ]);

        Oficio::updateOrCreate([
            'tipo' => 'Albañilería',
        ]);

        Oficio::updateOrCreate([
            'tipo' => 'Carpintería',
        ]);

        Oficio::updateOrCreate([
            'tipo' => 'Fletes',
        ]);

        Oficio::updateOrCreate([
            'tipo' => 'Pintura',
        ]);
    }
}
