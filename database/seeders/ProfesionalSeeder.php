<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ciudad;
use Illuminate\Database\Seeder;

class ProfesionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // CREACIÓN DE LOS PROFESIONALES

        $esteban = User::where('email', 'esteban@mail.com')->firstOrFail()
            ->profesional()->updateOrCreate([], []);

        $armando = User::where('email', 'armando@mail.com')->firstOrFail()
            ->profesional()->updateOrCreate([], []);

        $lujan = User::where('email', 'lujan@mail.com')->firstOrFail()
            ->profesional()->updateOrCreate([], []);

        $matias = User::where('email', 'matias@mail.com')->firstOrFail()
            ->profesional()->updateOrCreate([], []);

        $salgado = User::where('email', 'salgado@mail.com')->firstOrFail()
            ->profesional()->updateOrCreate([], []);


        // ASIGNACIÓN DE OFICIOS (N:M)

        $esteban->oficios()->sync([2, 3]);
        $armando->oficios()->sync([1, 2]);
        $lujan->oficios()->sync([1]);
        $matias->oficios()->sync([2, 1]);
        $salgado->oficios()->sync([3]);


        // OBTENER LOS IDS DE LAS CIUDADES

        $neuquen = Ciudad::where('nombre', 'Neuquén')->firstOrFail()->id;

        $generalRoca = Ciudad::where('nombre', 'General Roca')->firstOrFail()->id;

        $cipolletti = Ciudad::where('nombre', 'Cipolletti')->firstOrFail()->id;

        $plottier = Ciudad::where('nombre', 'Plottier')->firstOrFail()->id;


        // ASIGNACIÓN DE CIUDADES (N:M)

        $esteban->ciudades()->sync([$neuquen]);
        $armando->ciudades()->sync([$generalRoca]);
        $lujan->ciudades()->sync([$cipolletti]);
        $matias->ciudades()->sync([$cipolletti]);
        $salgado->ciudades()->sync([$plottier]);
    }
}
