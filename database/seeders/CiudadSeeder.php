<?php

namespace Database\Seeders;

use App\Models\Provincia;
use Illuminate\Database\Seeder;

class CiudadSeeder extends Seeder
{
    public function run(): void
    {
        $neuquen = Provincia::where('nombre', 'Neuquén')->firstOrFail();

        $rioNegro = Provincia::where('nombre', 'Río Negro')->firstOrFail();

        $neuquen->ciudades()->createMany([
            ['nombre' => 'Centenario'],
            ['nombre' => 'Neuquén'],
            ['nombre' => 'Plottier'],
            ['nombre' => 'San Martín de los Andes'],
        ]);

        $rioNegro->ciudades()->createMany([
            ['nombre' => 'Bariloche'],
            ['nombre' => 'Cinco Saltos'],
            ['nombre' => 'Cipolletti'],
            ['nombre' => 'General Roca'],
        ]);
    }
}