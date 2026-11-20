<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ProfesionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //primero busca al usuario que le corresponde ese correo y despues crea al profesional, para que coincida con el id_user (1aM)
        $esteban = User::where('email', 'esteban@mail.com')->first()
            ->profesional()->updateOrCreate([], 
            [
                'provincia' => 'Neuquen',
                'ciudad' => 'Neuquen',
            ]);

        $armando = User::where('email', 'armando@mail.com')->first()
            ->profesional()->updateOrCreate([], 
            [
                'provincia' => 'Rio Negro',
                'ciudad' => 'General Roca',
            ]);

        $lujan = User::where('email', 'lujan@mail.com')->first()
            ->profesional()->updateOrCreate([], [
            'provincia' => 'Rio Negro',
            'ciudad' => 'Cipolletti',
        ]);

        $matias = User::where('email', 'matias@mail.com')->first()
            ->profesional()->updateOrCreate([], [
            'provincia' => 'Rio Negro',
            'ciudad' => 'Cipolletti',
        ]);

        $salgado = User::where('email', 'salgado@mail.com')->first()
            ->profesional()->updateOrCreate([], [
            'provincia' => 'Neuquen',
            'ciudad' => 'Plottier',
        ]);

        // Para que los profesionales que cargamos, tengan las profesiones que queremos especificamente por el id del oficio (MaN)
        $esteban->oficios()->sync([2,3]);// Electricista y gasista
        $armando->oficios()->sync([1,2]);// Plomero y electricista
        $lujan->oficios()->sync([1]);// Plomero
        $matias->oficios()->sync([2,1]);// Electricista y plomero
        $salgado->oficios()->sync([3]);// Gasista
       
    }
}
