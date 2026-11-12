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

        //para que los profesionales que cargo, tengan las profesiones que quiero especificamente por el id del oficio (MaN)
        $esteban->oficios()->sync([2,3]);//electricista y gasista
        $armando->oficios()->sync([1,2]);//plomero y electricista
        $lujan->oficios()->sync([1]);//plomero

       
    }
}
