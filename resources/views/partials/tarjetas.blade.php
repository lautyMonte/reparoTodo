@forelse ($profesionales as $profesional)
    <div class="col-md-6 col-xl-4">
        <article class="card tarjeta-profesional h-100">
            <div class="card-body d-flex flex-column p-3">

                {{-- Fila de arriba: foto + nombre + oficios --}}
                <div class="d-flex gap-3">
                    <img class="avatar-lista" src="{{ $profesional->user->avatar() }}"
                        alt="Foto de {{ $profesional->user->nombreCompleto() }}">

                    <div class="flex-grow-1">
                        <h2 class="nombre-profesional">
                            <a href="{{ route('profesional.show', $profesional->id) }}"
                                class="stretched-link text-reset text-decoration-none">{{ $profesional->user->nombreCompleto() }}</a>
                        </h2>

                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($profesional->oficios->take(3) as $oficio)
                                <span class="chip-lista">{{ $oficio->nombreParaGenero($profesional->user->genero) }}</span>
                            @endforeach

                            @if ($profesional->oficios->count() > 3)
                                <span class="chip-lista">+{{ $profesional->oficios->count() - 3 }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Fila del medio: zona y puntaje --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="small">
                        <i data-lucide="map-pin"></i>
                        {{ $profesional->ciudades->pluck('nombre')->join(' • ') }}
                    </span>

                    @if ($profesional->resenas_count > 0)
                        <span class="puntaje fw-bold"><i data-lucide="star"></i>
                            {{ number_format($profesional->resenas_avg_puntaje, 1, ',', '.') }}</span>
                    @else
                        <span class="small text-muted">Sin reseñas</span>
                    @endif
                </div>
            </div>
        </article>
    </div>
@empty
    <p class="text-center text-muted">
        @if (request('buscar'))
            No se encontraron profesionales con esas especificaciones.
        @else
            Todavía no hay profesionales para mostrar.
        @endif
    </p>
@endforelse
