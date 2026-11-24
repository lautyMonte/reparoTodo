<?php

namespace Database\Seeders;

use App\Models\Provincia;
use Illuminate\Database\Seeder;

class ProvinciaSeeder extends Seeder
{
    public function run(): void
    {
        Provincia::create([
            'nombre' => 'Neuquén',
        ]);

        Provincia::create([
            'nombre' => 'Río Negro',
        ]);
    }
}