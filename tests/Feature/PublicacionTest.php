<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Solicitud;
use App\Models\Publicacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicacionTest extends TestCase
{
    use RefreshDatabase;

    private User $cliente;
    private User $profesional;
    private User $otroProfesional;
    private Solicitud $solicitud;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->cliente = $this->crearUsuario('cliente', 'cliente');

        $this->profesional = $this->crearUsuario('profesional', 'profesional');

        $this->otroProfesional = $this->crearUsuario('otro_profesional', 'profesional');

        $this->profesional->profesional()->create([]);

        $this->otroProfesional->profesional()->create([]);

        $this->solicitud = Solicitud::create([
            'cliente_id' => $this->cliente->id,
            'profesional_id' => $this->profesional->profesional->id,
            'titulo' => 'Reparación eléctrica',
            'descripcion' => 'Reparación de instalación eléctrica.',
            'estado' => 'finalizada',
        ]);
    }

    private function crearUsuario(string $nombre, string $tipo): User
    {
        return User::create([
            'usuario' => $nombre,
            'nombre' => $nombre,
            'apellido' => 'Prueba',
            'telefono' => '2991234567',
            'email' => $nombre . '@example.com',
            'password' => 'password123',
            'tipo' => $tipo,
        ]);
    }

    private function datosPublicacion(array $extras = []): array
    {
        return array_replace([
            'titulo' => 'Instalación eléctrica',
            'descripcion' => 'Trabajo realizado correctamente.',
        ], $extras);
    }

    private function crearPublicacion(): Publicacion
    {
        return $this->solicitud->publicacion()->create(
            $this->datosPublicacion()
        );
    }

    private function agregarFoto(Publicacion $publicacion)
    {
        $archivo = UploadedFile::fake()->image('trabajo.jpg');

        $ruta = $archivo->store('publicaciones', 'public');

        return $publicacion->fotos()->create([
            'ruta' => $ruta,
        ]);
    }

    // CREACIÓN

    public function test_profesional_puede_publicar_sin_fotos(): void
    {
        $respuesta = $this->actingAs($this->profesional)
            ->post(
                route('publicaciones.store', $this->solicitud),
                $this->datosPublicacion()
            );

        $respuesta->assertRedirect(
            route('solicitud.show', $this->solicitud->id)
        );

        $this->assertDatabaseHas('publicaciones', [
            'solicitud_id' => $this->solicitud->id,
            'titulo' => 'Instalación eléctrica',
        ]);

        $this->assertDatabaseCount('publicacion_fotos', 0);
    }

    public function test_profesional_puede_publicar_con_fotos(): void
    {
        $respuesta = $this->actingAs($this->profesional)
            ->post(
                route('publicaciones.store', $this->solicitud),
                $this->datosPublicacion([
                    'fotos' => [
                        UploadedFile::fake()->image('trabajo1.jpg'),
                        UploadedFile::fake()->image('trabajo2.png'),
                    ],
                ])
            );

        $respuesta->assertRedirect(
            route('solicitud.show', $this->solicitud->id)
        );

        $publicacion = $this->solicitud->publicacion()->firstOrFail();

        $this->assertCount(2, $publicacion->fotos);

        foreach ($publicacion->fotos as $foto) {
            Storage::disk('public')->assertExists($foto->ruta);
        }
    }

    public function test_profesional_no_puede_duplicar_publicacion(): void
    {
        $this->crearPublicacion();

        $respuesta = $this->actingAs($this->profesional)
            ->post(
                route('publicaciones.store', $this->solicitud),
                $this->datosPublicacion([
                    'titulo' => 'Publicación duplicada',
                ])
            );

        $respuesta->assertStatus(409);

        $this->assertDatabaseCount('publicaciones', 1);

        $this->assertDatabaseHas('publicaciones', [
            'solicitud_id' => $this->solicitud->id,
            'titulo' => 'Instalación eléctrica',
        ]);
    }

    // AUTORIZACIÓN

    public function test_profesional_no_puede_publicar_trabajo_ajeno(): void
    {
        $respuesta = $this->actingAs($this->otroProfesional)
            ->post(
                route('publicaciones.store', $this->solicitud),
                $this->datosPublicacion()
            );

        $respuesta->assertForbidden();

        $this->assertDatabaseCount('publicaciones', 0);
    }

    public function test_profesional_no_puede_publicar_trabajo_no_finalizado(): void
    {
        foreach (['pendiente', 'cancelada'] as $estado) {

            $this->solicitud->update([
                'estado' => $estado,
            ]);

            $respuesta = $this->actingAs($this->profesional)
                ->post(
                    route('publicaciones.store', $this->solicitud),
                    $this->datosPublicacion()
                );

            $respuesta->assertForbidden();

            $this->assertDatabaseCount('publicaciones', 0);
        }
    }

    public function test_cliente_no_puede_crear_publicacion(): void
    {
        $respuesta = $this->actingAs($this->cliente)
            ->post(
                route('publicaciones.store', $this->solicitud),
                $this->datosPublicacion()
            );

        $respuesta->assertRedirect(route('inicio'));

        $respuesta->assertSessionHas('error', 'Acceso denegado');

        $this->assertDatabaseCount('publicaciones', 0);
    }

    public function test_profesional_no_puede_editar_publicacion_ajena(): void
    {
        $publicacion = $this->crearPublicacion();

        $respuesta = $this->actingAs($this->otroProfesional)
            ->put(
                route('publicaciones.update', $publicacion),
                $this->datosPublicacion([
                    'titulo' => 'Edición no autorizada',
                ])
            );

        $respuesta->assertForbidden();

        $this->assertDatabaseHas('publicaciones', [
            'id' => $publicacion->id,
            'titulo' => 'Instalación eléctrica',
        ]);
    }

    public function test_profesional_no_puede_eliminar_publicacion_ajena(): void
    {
        $publicacion = $this->crearPublicacion();

        $respuesta = $this->actingAs($this->otroProfesional)
            ->delete(route('publicaciones.destroy', $publicacion));

        $respuesta->assertForbidden();

        $this->assertDatabaseHas('publicaciones', [
            'id' => $publicacion->id,
        ]);
    }

    // VALIDACIÓN

    public function test_publicacion_requiere_titulo_y_descripcion(): void
    {
        $respuesta = $this->actingAs($this->profesional)
            ->post(route('publicaciones.store', $this->solicitud), []);

        $respuesta->assertSessionHasErrorsIn('publicacion',[
            'titulo',
            'descripcion',
        ]);

        $this->assertDatabaseCount('publicaciones', 0);
    }

    public function test_publicacion_respeta_longitud_maxima(): void
    {
        $respuesta = $this->actingAs($this->profesional)
            ->post(
                route('publicaciones.store', $this->solicitud),
                $this->datosPublicacion([
                    'titulo' => str_repeat('a', 101),
                    'descripcion' => str_repeat('a', 2001),
                ])
            );

        $respuesta->assertSessionHasErrorsIn('publicacion',[
            'titulo',
            'descripcion',
        ]);

        $this->assertDatabaseCount('publicaciones', 0);
    }

    public function test_publicacion_no_admite_mas_de_cinco_fotos(): void
    {
        $fotos = [];

        for ($i = 1; $i <= 6; $i++) {
            $fotos[] = UploadedFile::fake()->image("foto{$i}.jpg");
        }

        $respuesta = $this->actingAs($this->profesional)
            ->post(
                route('publicaciones.store', $this->solicitud),
                $this->datosPublicacion(['fotos' => $fotos])
            );

        $respuesta->assertSessionHasErrorsIn('publicacion','fotos');

        $this->assertDatabaseCount('publicaciones', 0);

        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    public function test_publicacion_rechaza_archivos_que_no_son_imagenes(): void
    {
        $respuesta = $this->actingAs($this->profesional)
            ->post(
                route('publicaciones.store', $this->solicitud),
                $this->datosPublicacion([
                    'fotos' => [
                        UploadedFile::fake()->create(
                            'documento.pdf',
                            100,
                            'application/pdf'
                        ),
                    ],
                ])
            );

        $respuesta->assertSessionHasErrorsIn('publicacion','fotos.0');

        $this->assertDatabaseCount('publicaciones', 0);

        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    public function test_publicacion_rechaza_imagen_mayor_a_dos_mb(): void
    {
        $respuesta = $this->actingAs($this->profesional)
            ->post(
                route('publicaciones.store', $this->solicitud),
                $this->datosPublicacion([
                    'fotos' => [
                        UploadedFile::fake()->image('foto.jpg')->size(2049),
                    ],
                ])
            );

        $respuesta->assertSessionHasErrorsIn('publicacion','fotos.0');

        $this->assertDatabaseCount('publicaciones', 0);
    }

    // EDICIÓN

    public function test_profesional_puede_editar_publicacion_propia(): void
    {
        $publicacion = $this->crearPublicacion();

        $respuesta = $this->actingAs($this->profesional)
            ->put(
                route('publicaciones.update', $publicacion),
                $this->datosPublicacion([
                    'titulo' => 'Título actualizado',
                    'descripcion' => 'Descripción actualizada.',
                ])
            );

        $respuesta->assertRedirect(
            route('solicitud.show', $this->solicitud->id)
        );

        $this->assertDatabaseHas('publicaciones', [
            'id' => $publicacion->id,
            'titulo' => 'Título actualizado',
            'descripcion' => 'Descripción actualizada.',
        ]);

        $this->assertDatabaseCount('publicaciones', 1);
    }

    public function test_profesional_puede_agregar_fotos(): void
    {
        $publicacion = $this->crearPublicacion();

        $fotoExistente = $this->agregarFoto($publicacion);

        $respuesta = $this->actingAs($this->profesional)
            ->put(
                route('publicaciones.update', $publicacion),
                $this->datosPublicacion([
                    'fotos' => [
                        UploadedFile::fake()->image('nueva.jpg'),
                    ],
                ])
            );

        $respuesta->assertRedirect(
            route('solicitud.show', $this->solicitud->id)
        );

        $this->assertCount(2, $publicacion->fresh()->fotos);

        $this->assertDatabaseHas('publicacion_fotos', [
            'id' => $fotoExistente->id,
        ]);

        foreach ($publicacion->fresh()->fotos as $foto) {
            Storage::disk('public')->assertExists($foto->ruta);
        }
    }

    public function test_profesional_puede_eliminar_foto(): void
    {
        $publicacion = $this->crearPublicacion();

        $fotoEliminar = $this->agregarFoto($publicacion);
        $fotoConservar = $this->agregarFoto($publicacion);

        $respuesta = $this->actingAs($this->profesional)
            ->put(
                route('publicaciones.update', $publicacion),
                $this->datosPublicacion([
                    'eliminar_fotos' => [$fotoEliminar->id],
                ])
            );

        $respuesta->assertRedirect(
            route('solicitud.show', $this->solicitud->id)
        );

        $this->assertDatabaseMissing('publicacion_fotos', [
            'id' => $fotoEliminar->id,
        ]);

        Storage::disk('public')->assertMissing($fotoEliminar->ruta);

        $this->assertDatabaseHas('publicacion_fotos', [
            'id' => $fotoConservar->id,
        ]);

        Storage::disk('public')->assertExists($fotoConservar->ruta);
    }

    public function test_profesional_puede_reemplazar_foto_al_alcanzar_limite(): void
    {
        $publicacion = $this->crearPublicacion();

        for ($i = 0; $i < 5; $i++) {
            $this->agregarFoto($publicacion);
        }

        $fotoEliminar = $publicacion->fotos()->firstOrFail();

        $respuesta = $this->actingAs($this->profesional)
            ->put(
                route('publicaciones.update', $publicacion),
                $this->datosPublicacion([
                    'eliminar_fotos' => [$fotoEliminar->id],
                    'fotos' => [
                        UploadedFile::fake()->image('reemplazo.jpg'),
                    ],
                ])
            );

        $respuesta->assertRedirect(
            route('solicitud.show', $this->solicitud->id)
        );

        $this->assertCount(5, $publicacion->fresh()->fotos);

        $this->assertDatabaseMissing('publicacion_fotos', [
            'id' => $fotoEliminar->id,
        ]);

        Storage::disk('public')->assertMissing($fotoEliminar->ruta);

        foreach ($publicacion->fresh()->fotos as $foto) {
            Storage::disk('public')->assertExists($foto->ruta);
        }
    }

    public function test_edicion_no_puede_superar_cinco_fotos(): void
    {
        $publicacion = $this->crearPublicacion();

        for ($i = 0; $i < 5; $i++) {
            $this->agregarFoto($publicacion);
        }

        $respuesta = $this->actingAs($this->profesional)
            ->put(
                route('publicaciones.update', $publicacion),
                $this->datosPublicacion([
                    'titulo' => 'Cambio que debe rechazarse',
                    'fotos' => [
                        UploadedFile::fake()->image('sexta.jpg'),
                    ],
                ])
            );

        $respuesta->assertSessionHasErrorsIn('publicacion','fotos');

        $this->assertCount(5, $publicacion->fresh()->fotos);

        $this->assertCount(
            5,
            Storage::disk('public')->allFiles('publicaciones')
        );

        $this->assertDatabaseHas('publicaciones', [
            'id' => $publicacion->id,
            'titulo' => 'Instalación eléctrica',
        ]);
    }

    public function test_profesional_no_puede_eliminar_foto_de_otra_publicacion(): void
    {
        $publicacion = $this->crearPublicacion();

        $otraSolicitud = Solicitud::create([
            'cliente_id' => $this->cliente->id,
            'profesional_id' => $this->otroProfesional->profesional->id,
            'titulo' => 'Otro trabajo',
            'descripcion' => 'Trabajo de otro profesional.',
            'estado' => 'finalizada',
        ]);

        $otraPublicacion = $otraSolicitud->publicacion()->create(
            $this->datosPublicacion()
        );

        $fotoAjena = $this->agregarFoto($otraPublicacion);

        $respuesta = $this->actingAs($this->profesional)
            ->put(
                route('publicaciones.update', $publicacion),
                $this->datosPublicacion([
                    'eliminar_fotos' => [$fotoAjena->id],
                ])
            );

        $respuesta->assertSessionHasErrorsIn('publicacion','eliminar_fotos');

        $this->assertDatabaseHas('publicacion_fotos', [
            'id' => $fotoAjena->id,
            'publicacion_id' => $otraPublicacion->id,
        ]);

        Storage::disk('public')->assertExists($fotoAjena->ruta);
    }

    // ELIMINACIÓN

    public function test_profesional_puede_eliminar_publicacion_con_fotos(): void
    {
        $publicacion = $this->crearPublicacion();

        $primeraFoto = $this->agregarFoto($publicacion);
        $segundaFoto = $this->agregarFoto($publicacion);

        $respuesta = $this->actingAs($this->profesional)
            ->delete(route('publicaciones.destroy', $publicacion));

        $respuesta->assertRedirect(
            route('solicitud.show', $this->solicitud->id)
        );

        $this->assertDatabaseMissing('publicaciones', [
            'id' => $publicacion->id,
        ]);

        $this->assertDatabaseCount('publicacion_fotos', 0);

        Storage::disk('public')->assertMissing([
            $primeraFoto->ruta,
            $segundaFoto->ruta,
        ]);
    }

    public function test_eliminar_publicacion_conserva_solicitud_y_resena(): void
    {
        $publicacion = $this->crearPublicacion();

        $resena = $this->solicitud->resena()->create([
            'puntaje' => 5,
            'comentario' => 'Excelente trabajo.',
        ]);

        $respuesta = $this->actingAs($this->profesional)
            ->delete(route('publicaciones.destroy', $publicacion));

        $respuesta->assertRedirect(
            route('solicitud.show', $this->solicitud->id)
        );

        $this->assertDatabaseMissing('publicaciones', [
            'id' => $publicacion->id,
        ]);

        $this->assertDatabaseHas('solicitudes', [
            'id' => $this->solicitud->id,
            'estado' => 'finalizada',
        ]);

        $this->assertDatabaseHas('resenas', [
            'id' => $resena->id,
            'solicitud_id' => $this->solicitud->id,
            'puntaje' => 5,
        ]);
    }

    // REDIRECCIONES

    public function test_profesional_puede_editar_desde_perfil(): void
    {
        $publicacion = $this->crearPublicacion();

        $respuesta = $this->actingAs($this->profesional)
            ->put(
                route('publicaciones.update', $publicacion),
                $this->datosPublicacion([
                    'titulo' => 'Editado desde el perfil',
                    'origen' => 'perfil_profesional',
                ])
            );

        $respuesta->assertRedirect(
            route('profesional.show', [
                'id' => $this->profesional->profesional->id,
                'tab' => 'trabajos',
            ])
        );

        $this->assertDatabaseHas('publicaciones', [
            'id' => $publicacion->id,
            'titulo' => 'Editado desde el perfil',
        ]);
    }

    public function test_profesional_puede_eliminar_desde_perfil(): void
    {
        $publicacion = $this->crearPublicacion();

        $respuesta = $this->actingAs($this->profesional)
            ->delete(
                route('publicaciones.destroy', $publicacion),
                ['origen' => 'perfil_profesional']
            );

        $respuesta->assertRedirect(
            route('profesional.show', [
                'id' => $this->profesional->profesional->id,
                'tab' => 'trabajos',
            ])
        );

        $this->assertDatabaseMissing('publicaciones', [
            'id' => $publicacion->id,
        ]);
    }
}