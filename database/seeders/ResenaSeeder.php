<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Solicitud;
use Illuminate\Database\Seeder;

class ResenaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // email del profesional => lista de resenas (cliente, puntaje, comentario)
        $datos = [
            'matias@mail.com' => [   // promedio 4,8
                ['cliente' => 'lautaro@mail.com', 'puntaje' => 5, 'comentario' => 'Muy prolijo y puntual.'],
                ['cliente' => 'carla@mail.com',   'puntaje' => 5, 'comentario' => 'Resolvió el problema en el día.'],
                ['cliente' => 'lucia@mail.com',   'puntaje' => 4, 'comentario' => 'Buen trabajo, lo recomiendo.'],
                ['cliente' => 'manuel@mail.com',  'puntaje' => 5, 'comentario' => 'Excelente atención.'],
                ['cliente' => 'pablo@mail.com',   'puntaje' => 5, 'comentario' => 'Todo funcionó perfecto.'],
            ],
            'esteban@mail.com' => [  // promedio 4,75
                ['cliente' => 'lautaro@mail.com', 'puntaje' => 5, 'comentario' => 'Rápido y atento.'],
                ['cliente' => 'carla@mail.com',   'puntaje' => 4, 'comentario' => 'Buen precio y buen trabajo.'],
                ['cliente' => 'lucia@mail.com',   'puntaje' => 5, 'comentario' => 'Muy profesional.'],
                ['cliente' => 'manuel@mail.com',  'puntaje' => 5, 'comentario' => 'Lo volvería a contratar.'],
            ],
            'armando@mail.com' => [  // promedio 4,33
                ['cliente' => 'lautaro@mail.com', 'puntaje' => 4, 'comentario' => 'Cumplió con lo acordado.'],
                ['cliente' => 'carla@mail.com',   'puntaje' => 4, 'comentario' => 'Buen servicio.'],
                ['cliente' => 'pablo@mail.com',   'puntaje' => 5, 'comentario' => 'Muy recomendable.'],
            ],
            'lujan@mail.com' => [    // promedio 3,5
                ['cliente' => 'lucia@mail.com',   'puntaje' => 3, 'comentario' => 'Llegó tarde pero lo arregló.'],
                ['cliente' => 'manuel@mail.com',  'puntaje' => 4, 'comentario' => 'Correcto.'],
            ],
            'salgado@mail.com' => [  // promedio 3,0
                ['cliente' => 'carla@mail.com',   'puntaje' => 3, 'comentario' => 'Podría ser más prolijo.'],
                ['cliente' => 'pablo@mail.com',   'puntaje' => 2, 'comentario' => 'Tardó más de lo previsto.'],
                ['cliente' => 'manuel@mail.com',  'puntaje' => 4, 'comentario' => 'Buen resultado final.'],
            ],
        ];

        foreach ($datos as $emailProfesional => $resenas) {

            $profesional = User::where('email', $emailProfesional)
                ->firstOrFail()
                ->profesional;

            foreach ($resenas as $resena) {

                $cliente = User::where('email', $resena['cliente'])
                    ->firstOrFail();

                // Crear una solicitud finalizada de prueba.
                $solicitud = Solicitud::firstOrCreate(
                    [
                        'cliente_id' => $cliente->id,
                        'profesional_id' => $profesional->id,
                        'titulo' => 'Trabajo de prueba finalizado',
                        'estado' => 'finalizada',
                    ],
                    [
                        'descripcion' => 'Trabajo generado para probar las reseñas verificadas.',
                    ]
                );

                // Crear o actualizar la reseña de la solicitud.
                $solicitud->resena()->updateOrCreate(
                    [],
                    [
                        'puntaje' => $resena['puntaje'],
                        'comentario' => $resena['comentario'],
                    ]
                );
            }
        }
    }
}