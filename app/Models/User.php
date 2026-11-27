<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Chatify\Traits\InteractsWithChatify;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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
        'usuario', 'nombre', 'apellido', 'telefono', 'email', 'tipo', 'imagen', 'portada', 'email_verified_at', 'password', 'genero',
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

    // Reseñas escritas por el cliente en sus solicitudes.
    public function resenas(): HasManyThrough
    {
        return $this->hasManyThrough(
            Resena::class,
            Solicitud::class,
            'cliente_id',
            'solicitud_id'
        );
    }

    // Un usuario recibe muchas notificaciones. Se llama notificaciones (no notifications) para no chocar con el sistema propio de Laravel
    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class);
    }

    // Cuantas cosas le estan esperando a este usuario segun su rol:
    // profesional = solicitudes por aceptar o rechazar, aceptadas a las que les falta el presupuesto,
    // y propuestas de cierre que le hizo un cliente
    // cliente = presupuestos que tiene que aceptar o rechazar, y propuestas de cierre que le hizo un profesional
    public function pendientesDeRespuesta(): int
    {
        $cantidad = 0;

        if ($this->tipo === 'profesional' && $this->profesional) {
            $cantidad = $this->profesional->solicitudes()->whereIn('estado', ['pendiente', 'aceptada'])->count();
            $cantidad += $this->profesional->solicitudes()->where('estado', 'en_chat')
                ->whereNotNull('cierre_propuesto_por')->where('cierre_propuesto_por', '!=', $this->id)->count();
        }

        if ($this->tipo === 'cliente') {
            $cantidad = $this->solicitudes()->where('estado', 'presupuestada')->count();
            $cantidad += $this->solicitudes()->where('estado', 'en_chat')
                ->whereNotNull('cierre_propuesto_por')->where('cierre_propuesto_por', '!=', $this->id)->count();
        }

        return $cantidad;
    }

    // Cuantas notificaciones todavia no leyo
    public function notificacionesSinLeer(): int
    {
        return $this->notificaciones()->where('leida', false)->count();
    }

    // Sus notificaciones mas nuevas (por defecto 5)
    public function ultimasNotificaciones(int $cantidad = 5): Collection
    {
        return $this->notificaciones()->latest()->take($cantidad)->get();
    }

    // Las personas con las que puede chatear ahora: las del otro lado de sus solicitudes en estado en_chat
    public function contactosDeChat(): Collection
    {
        $contactos = new Collection;

        if ($this->tipo === 'cliente') {
            $contactos = User::whereHas('profesional.solicitudes', function ($consulta) {
                $consulta->where('cliente_id', $this->id)->where('estado', 'en_chat');
            })->get();
        }

        if ($this->tipo === 'profesional' && $this->profesional) {
            $contactos = User::whereHas('solicitudes', function ($consulta) {
                $consulta->where('profesional_id', $this->profesional->id)->where('estado', 'en_chat');
            })->get();
        }

        return $contactos;
    }

    // Si puede chatear con esa persona (solo si tienen una solicitud en chat entre los dos)
    public function puedeChatearCon(User $otro): bool
    {
        return $this->contactosDeChat()->contains('id', $otro->id);
    }

    // Si tiene al menos una conversacion en curso (para mostrar o no el boton de mensajes)
    public function tieneChats(): bool
    {
        return $this->contactosDeChat()->isNotEmpty();
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
        $imagen = $this->urlImagen($this->imagen);

        if ($imagen !== null) {
            return $imagen;
        }

        if (!in_array($this->genero, ['m', 'f'], true)) {
            return asset('img/avatares/avatar-generico.svg');
        }

        $numeroAvatar = ($this->id - 1) % 10 + 1;

        return asset(
            'img/avatares/'.$this->genero.'/avatar-'.$this->genero.'-'.$numeroAvatar.'.svg'
        );
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

    /**
     * Para que Chatify utilice el nombre completo del usuario.
     */
    public function getNameAttribute(): ?string
    {
        return $this->nombreCompleto();
    }
}
