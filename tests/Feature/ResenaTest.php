<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Profesional;
use App\Models\Solicitud;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResenaTest extends TestCase
{
    use RefreshDatabase;

    private User $cliente;
    private User $otroCliente;
    private Profesional $profesional;
    private Solicitud $solicitud;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cliente = User::create([
            'usuario' => 'cliente_prueba',
            'nombre' => 'Cliente',
            'apellido' => 'Prueba',
            'telefono' => '2991234567',
            'email' => 'cliente@example.com',
            'password' => 'password123',
            'tipo' => 'cliente',
        ]);

        $this->otroCliente = User::create([
            'usuario' => 'otro_cliente',
            'nombre' => 'Otro',
            'apellido' => 'Cliente',
            'telefono' => '2991234568',
            'email' => 'otro@example.com',
            'password' => 'password123',
            'tipo' => 'cliente',
        ]);

        $usuarioProfesional = User::create([
            'usuario' => 'profesional_prueba',
            'nombre' => 'Profesional',
            'apellido' => 'Prueba',
            'telefono' => '2997654321',
            'email' => 'profesional@example.com',
            'password' => 'password123',
            'tipo' => 'profesional',
        ]);

        $this->profesional = $usuarioProfesional
            ->profesional()
            ->create([]);

        $this->solicitud = Solicitud::create([
            'cliente_id' => $this->cliente->id,
            'profesional_id' => $this->profesional->id,
            'titulo' => 'Reparación eléctrica',
            'descripcion' => 'Reparación de instalación eléctrica.',
            'estado' => 'finalizada',
        ]);
    }

    public function test_cliente_puede_crear_resena(): void
    {
        $respuesta = $this->actingAs($this->cliente)
            ->post(route('resenas.store', $this->solicitud), [
                'puntaje' => 5,
                'comentario' => 'Excelente trabajo.',
            ]);

        $respuesta->assertRedirect(
            route('solicitud.cliente.show', $this->solicitud->id)
        );

        $this->assertDatabaseHas('resenas', [
            'solicitud_id' => $this->solicitud->id,
            'puntaje' => 5,
            'comentario' => 'Excelente trabajo.',
        ]);
    }

    public function test_cliente_no_puede_duplicar_resena(): void
    {
        $this->solicitud->resena()->create([
            'puntaje' => 5,
            'comentario' => 'Reseña original.',
        ]);

        $respuesta = $this->actingAs($this->cliente)
            ->post(route('resenas.store', $this->solicitud), [
                'puntaje' => 2,
                'comentario' => 'Reseña duplicada.',
            ]);

        $respuesta->assertStatus(409);

        $this->assertDatabaseCount('resenas', 1);

        $this->assertDatabaseHas('resenas', [
            'solicitud_id' => $this->solicitud->id,
            'puntaje' => 5,
            'comentario' => 'Reseña original.',
        ]);
    }

    public function test_cliente_no_puede_enviar_puntaje_invalido(): void
    {
        $respuesta = $this->actingAs($this->cliente)
            ->post(route('resenas.store', $this->solicitud), [
                'puntaje' => 6,
                'comentario' => 'Puntuación inválida.',
            ]);

        $respuesta->assertSessionHasErrors('puntaje');

        $this->assertDatabaseCount('resenas', 0);
    }

    public function test_cliente_no_puede_resenar_solicitud_ajena(): void
    {
        $respuesta = $this->actingAs($this->otroCliente)
            ->post(route('resenas.store', $this->solicitud), [
                'puntaje' => 5,
                'comentario' => 'Reseña no autorizada.',
            ]);

        $respuesta->assertForbidden();

        $this->assertDatabaseCount('resenas', 0);
    }
    public function test_cliente_no_puede_resenar_trabajo_pendiente(): void
    {
        $this->solicitud->update([
            'estado' => 'pendiente',
        ]);

        $respuesta = $this->actingAs($this->cliente)
            ->post(route('resenas.store', $this->solicitud), [
                'puntaje' => 5,
                'comentario' => 'Trabajo pendiente.',
            ]);

        $respuesta->assertForbidden();

        $this->assertDatabaseCount('resenas', 0);
    }

    public function test_cliente_no_puede_resenar_trabajo_cancelado(): void
    {
        $this->solicitud->update([
            'estado' => 'cancelada',
        ]);

        $respuesta = $this->actingAs($this->cliente)
            ->post(route('resenas.store', $this->solicitud), [
                'puntaje' => 1,
                'comentario' => 'Trabajo cancelado.',
            ]);

        $respuesta->assertForbidden();

        $this->assertDatabaseCount('resenas', 0);
    }

    public function test_cliente_puede_resenar_sin_comentario(): void
    {
        $respuesta = $this->actingAs($this->cliente)
            ->post(route('resenas.store', $this->solicitud), [
                'puntaje' => 4,
            ]);

        $respuesta->assertRedirect(
            route('solicitud.cliente.show', $this->solicitud->id)
        );

        $this->assertDatabaseHas('resenas', [
            'solicitud_id' => $this->solicitud->id,
            'puntaje' => 4,
            'comentario' => null,
        ]);
    }

    public function test_cliente_puede_resenar_distintos_trabajos_del_mismo_profesional(): void
    {
        $this->solicitud->resena()->create([
            'puntaje' => 5,
            'comentario' => 'Primer trabajo.',
        ]);

        $segundaSolicitud = Solicitud::create([
            'cliente_id' => $this->cliente->id,
            'profesional_id' => $this->profesional->id,
            'titulo' => 'Segunda reparación',
            'descripcion' => 'Otro trabajo realizado.',
            'estado' => 'finalizada',
        ]);

        $respuesta = $this->actingAs($this->cliente)
            ->post(route('resenas.store', $segundaSolicitud), [
                'puntaje' => 4,
                'comentario' => 'Segundo trabajo.',
            ]);

        $respuesta->assertRedirect(
            route('solicitud.cliente.show', $segundaSolicitud->id)
        );

        $this->assertDatabaseCount('resenas', 2);

        $this->assertDatabaseHas('resenas', [
            'solicitud_id' => $segundaSolicitud->id,
            'puntaje' => 4,
        ]);
    }

    public function test_cliente_puede_editar_resena_propia(): void
    {
        $resena = $this->solicitud->resena()->create([
            'puntaje' => 5,
            'comentario' => 'Comentario original.',
        ]);

        $respuesta = $this->actingAs($this->cliente)
            ->put(route('resenas.update', $resena), [
                'puntaje' => 3,
                'comentario' => 'Comentario actualizado.',
                'origen' => 'mis_solicitudes',
            ]);

        $respuesta->assertRedirect(
            route('solicitud.cliente.show', $this->solicitud->id)
        );

        $this->assertDatabaseHas('resenas', [
            'id' => $resena->id,
            'puntaje' => 3,
            'comentario' => 'Comentario actualizado.',
        ]);

        $this->assertDatabaseCount('resenas', 1);
    }

    public function test_cliente_no_puede_editar_resena_ajena(): void
    {
        $resena = $this->solicitud->resena()->create([
            'puntaje' => 5,
            'comentario' => 'Comentario original.',
        ]);

        $respuesta = $this->actingAs($this->otroCliente)
            ->put(route('resenas.update', $resena), [
                'puntaje' => 1,
                'comentario' => 'Modificación no autorizada.',
            ]);

        $respuesta->assertForbidden();

        $this->assertDatabaseHas('resenas', [
            'id' => $resena->id,
            'puntaje' => 5,
            'comentario' => 'Comentario original.',
        ]);
    }

    public function test_cliente_puede_eliminar_resena_propia(): void
    {
        $resena = $this->solicitud->resena()->create([
            'puntaje' => 5,
            'comentario' => 'Reseña original.',
        ]);

        $respuesta = $this->actingAs($this->cliente)
            ->delete(route('resenas.destroy', $resena));

        $respuesta->assertRedirect(
            route('profesional.show', [
                'id' => $this->profesional->id,
                'tab' => 'resenas',
            ])
        );

        $this->assertDatabaseMissing('resenas', [
            'id' => $resena->id,
        ]);

        $this->assertDatabaseHas('solicitudes', [
            'id' => $this->solicitud->id,
            'estado' => 'finalizada',
        ]);
    }

    public function test_cliente_no_puede_eliminar_resena_ajena(): void
    {
        $resena = $this->solicitud->resena()->create([
            'puntaje' => 5,
            'comentario' => 'Reseña original.',
        ]);

        $respuesta = $this->actingAs($this->otroCliente)
            ->delete(route('resenas.destroy', $resena));

        $respuesta->assertForbidden();

        $this->assertDatabaseHas('resenas', [
            'id' => $resena->id,
            'puntaje' => 5,
            'comentario' => 'Reseña original.',
        ]);
    }

    public function test_profesional_no_puede_crear_resena(): void
    {
        $respuesta = $this->actingAs($this->profesional->user)
            ->post(route('resenas.store', $this->solicitud), [
                'puntaje' => 5,
                'comentario' => 'Reseña no autorizada.',
            ]);

        $respuesta->assertRedirect(route('inicio'));

        $respuesta->assertSessionHas('error', 'Acceso denegado');

        $this->assertDatabaseCount('resenas', 0);
    }

    public function test_resena_publica_muestra_trabajo_y_nombre_del_cliente(): void
    {
        $this->solicitud->resena()->create([
            'puntaje' => 5,
            'comentario' => 'Excelente trabajo.',
        ]);

        $respuesta = $this->get(route('profesional.show', [
            'id' => $this->profesional->id,
            'tab' => 'resenas',
        ]));

        $respuesta->assertOk();

        // Información pública.
        $respuesta->assertSee($this->solicitud->titulo);
        $respuesta->assertSee($this->cliente->nombreCompleto());
        $respuesta->assertSee('Excelente trabajo.');
        $respuesta->assertSee('Trabajo verificado');
        $respuesta->assertSee('data-resena-puntaje="5"', false);

        // Información privada.
        $respuesta->assertDontSee($this->cliente->usuario);
        $respuesta->assertDontSee($this->cliente->email);
        $respuesta->assertDontSee($this->cliente->telefono);
        $respuesta->assertDontSee($this->solicitud->descripcion);
    }

    public function test_resenas_publicas_se_ordenan_de_mas_reciente_a_mas_antigua(): void
    {
        $resenaAntigua = $this->solicitud->resena()->create([
            'puntaje' => 5,
            'comentario' => 'Comentario de la reseña antigua.',
        ]);

        $resenaAntigua->forceFill([
            'created_at' => now()->subDays(2),
        ])->save();

        $segundaSolicitud = Solicitud::create([
            'cliente_id' => $this->cliente->id,
            'profesional_id' => $this->profesional->id,
            'titulo' => 'Segunda reparación',
            'descripcion' => 'Otro trabajo realizado.',
            'estado' => 'finalizada',
        ]);

        $segundaSolicitud->resena()->create([
            'puntaje' => 4,
            'comentario' => 'Comentario de la reseña reciente.',
        ]);

        $respuesta = $this->get(route('profesional.show', [
            'id' => $this->profesional->id,
            'tab' => 'resenas',
        ]));

        $respuesta->assertOk();

        $respuesta->assertSeeInOrder([
            'Comentario de la reseña reciente.',
            'Comentario de la reseña antigua.',
        ]);
    }
}