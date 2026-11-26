@extends('layouts.app', ['titulo' => $titulo])

@section('contenido')

    {{-- Titulo de la pagina, centrado --}}
    <div class="text-center mb-4">
        <h1 class="h2 mb-1">Buscar profesional</h1>
        <p class="text-muted mb-0">Encontrá al profesional ideal para lo que necesitás reparar.</p>
    </div>

    {{-- Barra superior: volver a la izquierda | filtros + buscador a la derecha --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <a href="{{ url()->previous() }}" class="btn btn-accent btn-barra fw-semibold">
            <i data-lucide="arrow-left"></i> Volver
        </a>

        <div class="d-flex flex-wrap align-items-center gap-2">
            {{--Aca va el boton de filtros--}}
           <button type="button" class="btn btn-accent btn-barra fw-semibold" data-bs-toggle="modal" data-bs-target="#modalFiltros">
                <i data-lucide="sliders-horizontal"></i>
                    Filtros
                    <span id="contador-filtros" class="badge rounded-pill bg-danger ms-1" style="display: none;">
                        0
                    </span>
            </button>
            
            {{--Aca va el buscador de los profesionales--}}
            <form action="{{ route('profesional.filtrar') }}" method="GET" class="buscador-lista" id="form-buscar">

                <div class="d-flex">
                    <div class="campo-buscar flex-grow-1">
                        <i data-lucide="search"></i>
                        <input type="text" name="buscar" id="buscar" class="form-control" placeholder="Buscar por nombre">
                    </div>

                    <button type="submit" class="btn btn-accent btn-barra fw-semibold">Buscar</button>
                </div>
            </form>
        </div>
    </div>
    {{--Aca va la parte de filtros--}}
    <div class="modal fade" id="modalFiltros" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                {{-- Encabezado --}}
                <div class="modal-header">
                    <h5 class="modal-title">Filtrar profesionales</h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                {{-- Contenido --}}
                <div class="modal-body">
                    <form id="form-filtros" action="{{ route('profesional.filtrar') }}" method="GET">

                    {{-- FILTRO POR OFICIO --}}
                    <div class="mb-4">
                        <h6 class="fw-bold">Oficios</h6>
                        
                        @foreach ($oficios as $oficio)
                            <div class="form-check">

                                <input
                                class="form-check-input"
                                type="checkbox"
                                name="oficios[]"
                                value="{{ $oficio->id }}"
                                id="oficio-{{ $oficio->id }}"
                                @checked(in_array($oficio->id, $oficiosSeleccionados))
                                >

                                <label class="form-check-label" for="oficio-{{ $oficio->id }}">
                                    {{ $oficio->tipo }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <hr>
                    {{-- FILTRO POR LOCALIDAD --}}
                    <div class="mb-4">
                        <h6 class="fw-bold">Localidad</h6>
                        @foreach ($localidades as $localidad)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="localidades[]" value="{{ $localidad }}" id="localidad-{{ $loop->index }}">

                                <label class="form-check-label" for="localidad-{{ $loop->index }}">
                                    {{ $localidad }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <hr>
                    {{-- FILTRO POR PUNTUACIÓN MÍNIMA --}}
                    <div class="mb-3">

                        <h6 class="fw-bold">Puntuación mínima</h6>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="puntaje" value="" id="puntaje-todos" checked>
                                <label class="form-check-label" for="puntaje-todos">
                                    Todos los puntajes
                                </label>
                        </div>

                        @foreach ([1, 2, 3, 4, 5] as $puntaje)

                        <div class="form-check">

                            <input class="form-check-input" type="radio" name="puntaje" value="{{ $puntaje }}" id="puntaje-{{ $puntaje }}">

                                <label class="form-check-label" for="puntaje-{{ $puntaje }}">
                                    {{ $puntaje }} estrellas
                                </label>

                        </div>

                        @endforeach

                    </div>

                    </div>
                        {{-- Botones --}}
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" id="limpiar-filtros">
                                Limpiar
                            </button>

                            <button type="submit" form="form-filtros" class="btn btn-accent">
                                Aplicar filtros
                            </button>
                        </div>
                    </div>
        </div>
    </div>

    <div class="row g-3" id="resultados-profesionales">
        @include('partials.tarjetas')
    </div>
     {{--Para el buscador de profesionales--}}
    @push('scripts')
    <script src="{{ asset('js/buscador.js') }}" defer></script>
    @endpush
@endsection