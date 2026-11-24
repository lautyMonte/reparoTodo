<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Chatify\Traits\InteractsWithChatify;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
/**para chatify*/
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**para chatify*/
    use InteractsWithChatify;

    protected $fillable = [
        'usuario', 'nombre', 'apellido', 'telefono', 'email', 'tipo', 'imagen', 'email_verified_at', 'password',
    ];

    // Obtener el nombre completo del usuario
    public function nombreCompleto(): string
    {
        return trim($this->nombre.' '.$this->apellido);
    }

    // Porque el profesional solo puede tener un usuario
    public function profesional(): HasOne
    {
        return $this->hasOne(Profesional::class);
    }

    // Un cliente puede hacer muchas solicitudes. La columna se llama cliente_id, no user_id
    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'cliente_id');
    }

    // Un usuario recibe muchas notificaciones. Se llama notificaciones (no notifications) para no chocar con el sistema propio de Laravel
    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class);
    }

    public function urlImagen(?string $ruta): ?string
    {
        if ($ruta === null || $ruta === '') {
            return null;
        }

        return Storage::disk('public')->url($ruta);
    }

    // Foto de perfil: la propia si la tiene y, si no, una imagen por defecto
    // (cuando esten los avatares de los clientes, se cambia solo la ruta de abajo)
    public function avatar(): string
    {
        return $this->urlImagen($this->imagen)
            ?? asset('img/avatares/avatar-'.(($this->id - 1) % 5 + 1).'.svg');
    }

    #[Scope]
    protected function sinImagen(Builder $query): void
    {
        $query->whereNull('imagen');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**Funciones para utilizar chatify */

    /**
     * Iniciar o recuperar una conversación directa.
     */
    public function iniciarConversacion(User $destinatario)
    {
        return $this->conversationWith($destinatario);
    }

    /**
     * Enviar un mensaje a otro usuario.
     */
    public function enviarMensaje(User $destinatario, string $mensaje)
    {
        return $this->sendMessageTo($destinatario, $mensaje);
    }

    /**
     * Para que Chatify utilice el nombre completo del usuario.
     */
    public function getNameAttribute(): ?string
    {
        return $this->nombreCompleto();
    }
}
