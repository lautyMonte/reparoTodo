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
        Oficio::UpdateOrCreate([
            'tipo' => 'Plomero',
        ]);

        Oficio::UpdateOrCreate([
            'tipo' => 'Electricista',
        ]);

        Oficio::UpdateOrCreate([
            'tipo' => 'Gasista',
        ]);
    }
}
