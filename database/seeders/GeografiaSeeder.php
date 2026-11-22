<?php

namespace Database\Seeders;

use App\Models\Jurisdiccion;
use App\Models\Localidad;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeografiaSeeder extends Seeder
{
    public function run(): void
    {
        $baseUrl = 'https://apis.datos.gob.ar/georef/api/v2.0';

        $respuesta = Http::timeout(30)
            ->retry(3, 500)
            ->get("$baseUrl/provincias", ['max' => 100])
            ->throw()
            ->json();

        $provincias = $respuesta['provincias'] ?? [];

        if (empty($provincias)) {
            throw new RuntimeException('Georef no devolvió jurisdicciones.');
        }

        foreach ($provincias as $provincia) {
            $jurisdiccion = Jurisdiccion::updateOrCreate(
                ['georef_id' => (string) $provincia['id']],
                ['nombre' => $provincia['nombre']]
            );

            $inicio = 0;

            do {
                $datos = Http::timeout(30)
                    ->retry(3, 500)
                    ->get("$baseUrl/localidades", [
                        'provincia' => $provincia['id'],
                        'max' => 5000,
                        'inicio' => $inicio,
                    ])
                    ->throw()
                    ->json();

                if (!isset($datos['localidades'], $datos['total'])) {
                    throw new RuntimeException('Respuesta incompleta de Georef.');
                }

                $localidades = $datos['localidades'];

                if (empty($localidades) && $inicio < $datos['total']) {
                    throw new RuntimeException('Georef devolvió una página vacía.');
                }

                foreach ($localidades as $localidad) {
                    Localidad::updateOrCreate(
                        ['georef_id' => (string) $localidad['id']],
                        [
                            'jurisdiccion_id' => $jurisdiccion->id,
                            'nombre' => $localidad['nombre'],
                        ]
                    );
                }

                $inicio += count($localidades);

            } while ($inicio < $datos['total']);
        }
    }
}
