<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
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
        'tipo' => 'cliente',
        'imagen' => null,
        'email_verified_at' => now(),
        'password' => Hash::make('password123'),
    ]
);

    // Profesionales nuevos y clientes que dejan resenas (todos con password123)
    $nuevos = [
        ['matias@mail.com',  'Matias Jañez',   '542994237860', 'profesional'],
        ['salgado@mail.com', 'Matias Salgado', '542994237861', 'profesional'],
        ['carla@mail.com',   'Carla Gomez',    '542994237862', 'cliente'],
        ['lucia@mail.com',   'Lucia Romero',   '542994237863', 'cliente'],
        ['manuel@mail.com',  'Manuel Ferrari', '542994237864', 'cliente'],
        ['pablo@mail.com',   'Pablo Diaz',     '542994237865', 'cliente'],
    ];

    foreach ($nuevos as $nuevo) {
        User::updateOrCreate(
            ['email' => $nuevo[0]],
            [
                'usuario' => $nuevo[1],
                'telefono' => $nuevo[2],
                'tipo' => $nuevo[3],
                'imagen' => null,
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
            ]
        );
    }
    }
}
