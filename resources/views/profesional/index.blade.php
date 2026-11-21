@extends('layouts.app', ['titulo' => $titulo])

@section('contenido')

    {{-- Titulo de la pagina, centrado --}}
    <div class="text-center mb-4">
        <h1 class="h2 mb-1">Buscar profesional</h1>
        <p class="text-muted mb-0">Encontrá al profesional ideal para lo que necesitás reparar.</p>
    </div>

    {{-- Barra superior: volver a la izquierda | filtros + buscador a la derecha (todavia sin funcionalidad) --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <a href="{{ url()->previous() }}" class="btn btn-accent btn-barra fw-semibold">
            <i data-lucide="arrow-left"></i> Volver
        </a>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn btn-accent btn-barra fw-semibold">
                <i data-lucide="sliders-horizontal"></i> Filtros
            </button>

            <form action="{{ route('profesional.busca') }}" method="get" class="buscador-lista">
                <div class="d-flex">
                    <div class="campo-buscar flex-grow-1">
                        <i data-lucide="search"></i>
                        <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre" aria-label="Buscar profesional"  value="{{ request('buscar') }}">
                    </div>
                    <button type="submit" class="btn btn-accent btn-barra fw-semibold">Buscar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3">
        @forelse ($profesionales as $profesional)

            {{-- Foto: la que subio el usuario; si no tiene, un avatar de ejemplo segun su id --}}
            @php
                $foto = $profesional->user->urlImagen($profesional->user->imagen)
                    ?? asset('img/avatares/avatar-' . (($profesional->id - 1) % 5 + 1) . '.svg');
            @endphp

            <div class="col-md-6 col-xl-4">
                <article class="card tarjeta-profesional h-100">
                    <div class="card-body d-flex flex-column p-3">

                        {{-- Fila de arriba: foto + nombre + oficios --}}
                        <div class="d-flex gap-3">
                            <img class="avatar-lista" src="{{ $foto }}" alt="Foto de {{ $profesional->user->usuario }}">

                            <div class="flex-grow-1">
                                <h2 class="nombre-profesional">
                                    <a href="{{ route('profesional.show', $profesional->id) }}" class="stretched-link text-reset text-decoration-none">{{ $profesional->user->usuario }}</a>
                                </h2>

                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($profesional->oficios->take(3) as $oficio)
                                        <span class="chip-lista">{{ $oficio->tipo }}</span>
                                    @endforeach

                                    @if ($profesional->oficios->count() > 3)
                                        <span class="chip-lista">+{{ $profesional->oficios->count() - 3 }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Fila del medio: zona y puntaje --}}
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="small"><i data-lucide="map-pin"></i> {{ $profesional->ciudad }} • {{ $profesional->provincia }}</span>

                            @if ($profesional->resenas_count > 0)
                                <span class="puntaje fw-bold"><i data-lucide="star"></i> {{ number_format($profesional->resenas_avg_puntaje, 1, ',', '.') }}</span>
                            @else
                                <span class="small text-muted">Sin reseñas</span>
                            @endif
                        </div>
                    </div>
                </article>
            </div>
        @empty
    <p class="text-center text-muted">
        @if(request('buscar'))
            No se encontraron profesionales con el nombre "{{ request('buscar') }}".
        @else
            Todavía no hay profesionales para mostrar.
        @endif
    </p>
    @endforelse
    </div>

    {{-- El chat solo existe con sesion iniciada --}}
    @auth
        @include('chatify.index')
    @endauth
@endsection