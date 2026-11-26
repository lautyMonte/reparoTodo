@extends('layouts.app', ['titulo' => $titulo])

@section('contenido')
    @php
        $usuario = $profesional->user;
        $promedio = $profesional->resenas_avg_puntaje;
        $total = $profesional->resenas_count;

        $tabActiva = request()->query('tab', 'info');

        if (old('origen') === 'perfil_profesional') {

            if ($errors->getBag('publicacion')->any()) {
                $tabActiva = 'trabajos';
            } elseif ($errors->has('puntaje') || $errors->has('comentario')) {
                $tabActiva = 'resenas';
            }

        }

        if (!in_array($tabActiva, ['info', 'trabajos', 'resenas'], true)) {
            $tabActiva = 'info';
        }

        $mostrarInfo = $tabActiva === 'info';
        $mostrarTrabajos = $tabActiva === 'trabajos';
        $mostrarResenas = $tabActiva === 'resenas';
    @endphp

    <div class="detalle-pagina">

        {{-- Volver a la pagina anterior (igual que en el buscador) --}}
        <a href="{{ url()->previous() }}" class="btn btn-accent btn-barra fw-semibold mb-4">
            <i data-lucide="arrow-left"></i> Volver
        </a>

        <div class="row g-4">

            {{-- ===== Columna izquierda: quien es el profesional ===== --}}
            <aside class="col-lg-4">
                <div class="detalle-tarjeta detalle-lateral">
                    <div class="detalle-tapa"></div>
                    <div class="detalle-lateral-cuerpo">
                        <img class="detalle-foto" src="{{ $usuario->avatar() }}" alt="Foto de {{ $usuario->nombreCompleto() }}">
                        <h1 class="h3 mb-2">{{ $usuario->nombreCompleto() }}</h1>

                        <span class="pill-valoracion puntaje">
                            <i data-lucide="star"></i>
                            @if ($total > 0)
                                <strong>{{ number_format($promedio, 1, ',', '.') }}</strong>
                                <span class="text-secondary">({{ $total }}
                                    {{ $total === 1 ? 'reseña' : 'reseñas' }})</span>
                            @else
                                <span class="text-secondary">Sin reseñas</span>
                            @endif
                        </span>

                        <div class="detalle-separador"></div>

                        <div>
                            @if ($esDuenio)             
                            @elseif (auth()->check() && auth()->user()->tipo === 'cliente')
                                <a href="{{ route('solicitud.create', $profesional->id) }}"
                                    class="btn btn-accent btn-detalle w-100">
                                    <i data-lucide="briefcase"></i> Solicitar trabajo
                                </a>
                            @elseif (!auth()->check())
                                <a href="{{ route('usuario.registro') }}" class="btn btn-accent btn-detalle w-100">
                                    <i data-lucide="briefcase"></i> Solicitar trabajo
                                </a>
                            @endif
                            @can('update', $profesional)
                                <a href="{{ route('profesional.edit') }}" class="btn btn-accent btn-detalle w-100">
                                    <i data-lucide="pencil"></i>Editar mi perfil</a>

                            @endcan
                        </div>
                    </div>
                </div>
            </aside>

            {{-- ===== Columna derecha: pestanas ===== --}}
            <section class="col-lg-8">

                <div class="detalle-tabs" role="tablist">

                    <button
                        type="button"
                        class="detalle-tab {{ $mostrarInfo ? 'active' : '' }}"
                        id="tab-info"
                        data-bs-toggle="tab"
                        data-bs-target="#panel-info"
                        role="tab"
                        aria-controls="panel-info"
                        aria-selected="{{ $mostrarInfo ? 'true' : 'false' }}"
                    >
                        <i data-lucide="info"></i>
                        <span>Información</span>
                    </button>

                    <button
                        type="button"
                        class="detalle-tab {{ $mostrarTrabajos ? 'active' : '' }}"
                        id="tab-trabajos"
                        data-bs-toggle="tab"
                        data-bs-target="#panel-trabajos"
                        role="tab"
                        aria-controls="panel-trabajos"
                        aria-selected="{{ $mostrarTrabajos ? 'true' : 'false' }}"
                    >
                        <i data-lucide="hammer"></i>
                        <span>Trabajos</span>
                    </button>

                    <button
                        type="button"
                        class="detalle-tab {{ $mostrarResenas ? 'active' : '' }}"
                        id="tab-resenas"
                        data-bs-toggle="tab"
                        data-bs-target="#panel-resenas"
                        role="tab"
                        aria-controls="panel-resenas"
                        aria-selected="{{ $mostrarResenas ? 'true' : 'false' }}"
                    >
                        <i data-lucide="message-square"></i>
                        <span>Reseñas</span>
                    </button>

                </div>

                <div class="tab-content detalle-tarjeta">

                    {{-- INFORMACION --}}
                    <div class="tab-pane fade detalle-pane {{ $mostrarInfo ? 'show active' : '' }}"
                        id="panel-info"
                        role="tabpanel"
                        aria-labelledby="tab-info">
                        <div class="dato-info">
                            <i data-lucide="map-pin"></i>
                            <span>
                                @foreach ($profesional->ciudades as $ciudad)
                                    {{ $ciudad->nombre }}, {{ $ciudad->provincia->nombre }}

                                    @if (!$loop->last)
                                        •
                                    @endif
                                @endforeach
                            </span>
                        </div>

                        <div class="dato-info">
                            <i data-lucide="calendar-days"></i>
                            <span>Miembro desde
                                {{ $profesional->created_at->locale('es')->translatedFormat('F \d\e Y') }}</span>
                        </div>

                        {{-- Estos 2 datos aparecen cuando existan las columnas (pantalla ofrecer servicios) --}}

                        @if ($profesional->seguro)
                            <div class="dato-info">
                                <i data-lucide="shield-check"></i>
                                <span>Seguro de responsabilidad</span>
                            </div>
                        @endif

                        @if ($profesional->sobre_mi)
                            <h2 class="detalle-titulo"><i data-lucide="user"></i> Sobre mí</h2>
                            <p class="text-secondary mb-0">{{ $profesional->sobre_mi }}</p>
                        @endif

                        <h2 class="detalle-titulo"><i data-lucide="wrench"></i> Servicios</h2>
                        @if ($profesional->oficios->isNotEmpty())
                            <div class="lista-servicios">
                                @foreach ($profesional->oficios as $oficio)
                                    <span class="etiqueta-servicio">{{ $oficio->tipo }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-secondary mb-0">Este profesional todavía no cargó sus servicios.</p>
                        @endif
                    </div>

                    {{-- TRABAJOS --}}
                    <div
                        class="tab-pane fade detalle-pane {{ $mostrarTrabajos ? 'show active' : '' }}"
                        id="panel-trabajos"
                        role="tabpanel"
                        aria-labelledby="tab-trabajos"
                    >

                        @forelse ($profesional->publicaciones as $publicacion)

                            @include('publicacion.tarjeta', [
                                'publicacion' => $publicacion,
                                'esDuenio' => $esDuenio,
                            ])

                        @empty

                            <div class="text-center py-4">

                                <i data-lucide="hammer" class="text-secondary mb-2"></i>

                                <p class="text-secondary mb-0">
                                    Todavía no hay trabajos publicados.
                                </p>

                            </div>

                        @endforelse

                    </div>

                    {{-- RESENAS --}}
                    <div class="tab-pane fade detalle-pane {{ $mostrarResenas ? 'show active' : '' }}"
                        id="panel-resenas"
                        role="tabpanel"
                        aria-labelledby="tab-resenas">

                        @if ($total > 0)
                            <div class="resumen-resenas">
                                <div class="resumen-promedio puntaje">
                                    <div class="resumen-numero">{{ number_format($promedio, 1, ',', '.') }}</div>
                                    <div class="my-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i data-lucide="star"
                                                class="{{ $i <= round($promedio) ? 'estrella-llena' : 'estrella-vacia' }}"></i>
                                        @endfor
                                    </div>
                                    <small class="text-secondary">{{ $total }}
                                        {{ $total === 1 ? 'reseña' : 'reseñas' }}</small>
                                </div>

                                <div>
                                    @foreach ([5, 4, 3, 2, 1] as $estrellas)
                                        @php $cantidad = $conteo[$estrellas] ?? 0; @endphp
                                        <div class="fila-barra">
                                            <span>{{ $estrellas }} ★</span>
                                            <div class="barra-fondo"><span
                                                    style="width: {{ round(($cantidad * 100) / $total) }}%"></span></div>
                                            <span>{{ $cantidad }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Filtros por estrellas --}}
                        <div class="filtro-estrellas">

                            <button
                                type="button"
                                class="btn-filtro-estrella activo"
                                data-filtro-puntaje="todas"
                                aria-pressed="true"
                            >
                                Todas
                            </button>

                            @foreach ([5, 4, 3, 2, 1] as $estrellas)

                                <button
                                    type="button"
                                    class="btn-filtro-estrella"
                                    data-filtro-puntaje="{{ $estrellas }}"
                                    aria-pressed="false"
                                >
                                    {{ $estrellas }}
                                    <i data-lucide="star"></i>
                                </button>

                            @endforeach

                        </div>

                        @if ($total > 0)
                            <p
                                id="resenas-sin-resultados"
                                class="text-secondary text-center py-3 d-none"
                                role="status"
                            >
                                No hay reseñas con esta puntuación.
                            </p>
                        @endif

                        @forelse ($profesional->resenas as $resena)
                            @php
                                $puedeEditar = auth()->user()?->can('update', $resena);

                                $mostrarFormulario = $puedeEditar
                                    && old('origen') === 'perfil_profesional'
                                    && old('resena_editada') == $resena->id
                                    && ($errors->has('puntaje') || $errors->has('comentario'));
                            @endphp

                            <div
                                class="tarjeta-resena"
                                data-resena-inline
                                data-resena-puntaje="{{ $resena->puntaje }}"
                            >

                                {{-- Inicial del cliente --}}
                                <div class="inicial-resena">
                                    {{ mb_strtoupper(mb_substr($resena->solicitud->cliente->nombreCompleto(), 0, 1)) }}
                                </div>

                                <div class="flex-grow-1">

                                    {{-- Encabezado de la reseña --}}
                                    <div class="d-flex justify-content-between align-items-start">

                                        <strong>
                                            {{ $resena->solicitud->cliente->nombreCompleto() }}
                                        </strong>

                                        <small class="text-secondary">
                                            {{ $resena->created_at->format('d/m/Y') }}
                                        </small>

                                    </div>

                                    <small class="text-success">
                                        <i data-lucide="badge-check"></i>
                                        Trabajo verificado
                                    </small>

                                    {{-- Estado de lectura --}}
                                    <div
                                        data-resena-lectura
                                        class="{{ $mostrarFormulario ? 'd-none' : '' }}"
                                    >

                                        <div class="my-1">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i
                                                    data-lucide="star"
                                                    class="{{ $i <= $resena->puntaje
                                                        ? 'estrella-llena'
                                                        : 'estrella-vacia' }}"
                                                ></i>
                                            @endfor
                                        </div>

                                        @if ($resena->comentario)
                                            <p class="mb-0">
                                                {{ $resena->comentario }}
                                            </p>
                                        @endif

                                        <div class="resena-acciones gap-2">

                                            @can('update', $resena)
                                                <button
                                                    type="button"
                                                    class="btn btn-eliminar-resena"
                                                    data-resena-editar
                                                >
                                                    <i data-lucide="pencil"></i>
                                                    Editar reseña
                                                </button>
                                            @endcan

                                            @can('delete', $resena)
                                                <button
                                                    type="button"
                                                    class="btn btn-eliminar-resena"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEliminarResena{{ $resena->id }}"
                                                >
                                                    <i data-lucide="trash-2"></i>
                                                    Eliminar reseña
                                                </button>
                                            @endcan

                                        </div>

                                    </div>

                                    {{-- Estado de edición --}}
                                    @can('update', $resena)

                                        <form
                                            data-resena-edicion
                                            class="mt-3 {{ $mostrarFormulario ? '' : 'd-none' }}"
                                            action="{{ route('resenas.update', $resena) }}"
                                            method="POST"
                                            data-puntaje="{{ $resena->puntaje }}"
                                            data-comentario="{{ $resena->comentario ?? '' }}"
                                        >

                                            @csrf
                                            @method('PUT')

                                            <input
                                                type="hidden"
                                                name="origen"
                                                value="perfil_profesional"
                                            >

                                            <input
                                                type="hidden"
                                                name="resena_editada"
                                                value="{{ $resena->id }}"
                                            >

                                            @include('resena.form', [
                                                'resena' => $resena,
                                                'prefijo' => 'resena-'.$resena->id,
                                                'usarOld' => $mostrarFormulario
                                            ])

                                            <div class="d-flex justify-content-end gap-2">

                                                <button
                                                    type="button"
                                                    class="btn btn-cancelar"
                                                    data-resena-cancelar
                                                >
                                                    Cancelar
                                                </button>

                                                <button
                                                    type="submit"
                                                    class="btn btn-accent"
                                                >
                                                    Guardar cambios
                                                </button>

                                            </div>

                                        </form>

                                    @endcan

                                </div>

                            </div>

                        @empty

                            <p class="text-secondary text-center mb-0">
                                Este profesional todavía no tiene reseñas.
                            </p>

                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>
    {{-- Modales de confirmación para eliminar reseñas --}}
    @foreach ($profesional->resenas as $resena)
        @can('delete', $resena)

            <div
                class="modal fade modal-eliminar-resena"
                id="modalEliminarResena{{ $resena->id }}"
                tabindex="-1"
                aria-labelledby="modalEliminarResenaLabel{{ $resena->id }}"
                aria-hidden="true"
            >
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center p-4">

                        <div class="modal-body">

                            <i data-lucide="circle-alert"
                                class="modal-resena-icono mb-3">
                            </i>

                            <h4 id="modalEliminarResenaLabel{{ $resena->id }}">
                                ¿Eliminar tu reseña?
                            </h4>

                            <p class="text-secondary mt-3">
                                Tu comentario y puntuación se eliminarán
                                definitivamente del perfil del profesional.
                            </p>

                            <p class="fw-semibold">
                                Esta acción no se puede deshacer.
                            </p>

                            <form
                                action="{{ route('resenas.destroy', $resena) }}"
                                method="POST"
                                class="d-flex justify-content-center flex-wrap gap-2 mt-4"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="button"
                                    class="btn btn-cancelar"
                                    data-bs-dismiss="modal"
                                >
                                    Cancelar
                                </button>

                                <button
                                    type="submit"
                                    class="btn btn-rechazar-confirmar"
                                >
                                    Sí, eliminar reseña
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

        @endcan
    @endforeach
    {{-- Modales de gestión del portfolio --}}
    @if ($esDuenio)

        @foreach ($profesional->publicaciones as $publicacion)

            @can('update', $publicacion)

                @include('publicacion.modal-editar', [
                    'publicacion' => $publicacion,
                    'origen' => 'perfil_profesional',
                ])

            @endcan

            @can('delete', $publicacion)

                @include('publicacion.modal-eliminar', [
                    'publicacion' => $publicacion,
                    'origen' => 'perfil_profesional',
                ])

            @endcan

        @endforeach

    @endif

<script src="{{ asset('js/resena-inline.js') }}" defer></script>
<script src="{{ asset('js/resena-filtros.js') }}" defer></script>
@endsection
