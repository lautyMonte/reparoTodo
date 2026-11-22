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
            {{--Aca va el buscador de los profesionales--}}
            <form action="{{ route('profesional.filtrarOficio') }}" method="GET" class="buscador-oficio" id="form-oficio">

                <div class="d-flex">
                    <div class="campo-buscar flex-grow-1">
                        <select name="oficio" id="oficio" class="form-select">
                            <option value="">Seleccionar oficio</option>

                                @foreach ($oficios as $oficio)
                                    <option value="{{ $oficio->id }}">
                                        {{ $oficio->tipo }}
                                    </option>
                                @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-accent btn-barra fw-semibold">Filtrar</button>
                </div>
            </form>

            <form action="{{ route('profesional.busca') }}" method="GET" class="buscador-lista" id="form-buscar">

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

    <div class="row g-3" id="resultados-profesionales">
        @include('partials.tarjetas')
    </div>
     {{--Para el buscador de profesionales--}}
    <script src="{{ asset('js/buscador.js') }}" defer></script>
@endsection