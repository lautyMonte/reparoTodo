<?php

namespace App\Models;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**para chatify*/
use Chatify\Traits\InteractsWithChatify;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable
{
    /**para chatify*/
    use InteractsWithChatify;
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
    
    protected $fillable = [
        'usuario', 'telefono', 'email', 'tipo', 'imagen', 'email_verified_at', 'password'
    ];


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

    public function urlImagen(?string $ruta): ?string
    {
        if ($ruta === null || $ruta === '') {
            return null;
        }

        return Storage::disk('public')->url($ruta);
    }


    #[Scope]
    protected function sinImagen(Builder $query): void
    {
        $query->whereNull("imagen");
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
     * Para que chatify interpretar a usuario como nombre del usuario en vez de name
     */
    public function getNameAttribute(): ?string
    {
        return $this->usuario;
    }
}