<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class userSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
    ['email' => 'esteban@mail.com'],
    [
        'usuario' => 'Esteban Quito',
        'telefono' => '542994237856',
        'tipo' => 'profesional',
        'imagen' => null,
        'email_verified_at' => now(),//para que ya este verificado el mail
        'password' => Hash::make('password123'),
    ]
);

User::updateOrCreate(
    ['email' => 'armando@mail.com'],
    [
        'usuario' => 'Armando Paredes',
        'telefono' => '542994237857',
        'tipo' => 'profesional',
        'imagen' => null,
        'email_verified_at' => now(),//para que ya este verificado el mail
        'password' => Hash::make('password123'),
    ]
);

User::updateOrCreate(
    ['email' => 'lujan@mail.com'],
    [
        'usuario' => 'Lujan Perez',
        'telefono' => '542994237858',
        'tipo' => 'profesional',
        'imagen' => null,
        'email_verified_at' => now(),//para que ya este verificado el mail
        'password' => Hash::make('password123'),
    ]);

    User::updateOrCreate(
    ['email' => 'lautaro@mail.com'],
    [
        'usuario' => 'lautaro Rodriguez',
        'telefono' => '542994237859',
        'tipo' => 'comun',
        'imagen' => null,
        'email_verified_at' => now(),
        'password' => Hash::make('password123'),
    ]
);
    }
}
