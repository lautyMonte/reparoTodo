@extends('layouts.app', ['titulo' => $titulo])

@section('contenido')
    @php
        $usuario  = $profesional->user;
        $promedio = $profesional->resenas_avg_puntaje;
        $total    = $profesional->resenas_count;
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
                        <img class="detalle-foto" src="{{ $usuario->avatar() }}" alt="Foto de {{ $usuario->usuario }}">
                        <h1 class="h3 mb-2">{{ $usuario->usuario }}</h1>

                        <span class="pill-valoracion puntaje">
                            <i data-lucide="star"></i>
                            @if ($total > 0)
                                <strong>{{ number_format($promedio, 1, ',', '.') }}</strong>
                                <span class="text-secondary">({{ $total }} {{ $total === 1 ? 'reseña' : 'reseñas' }})</span>
                            @else
                                <span class="text-secondary">Sin reseñas</span>
                            @endif
                        </span>

                        <div class="detalle-separador"></div>

                        <div>
                            @if ($esDuenio)
                                <p class="text-secondary small mb-0">Así ven tu perfil los clientes.</p>
                            @elseif (auth()->check() && auth()->user()->tipo === 'cliente')
                                <a href="{{ route('solicitud.create', $profesional->id) }}" class="btn btn-accent btn-detalle w-100">
                                    <i data-lucide="briefcase"></i> Solicitar trabajo
                                </a>
                            @elseif (! auth()->check())
                                <a href="{{ route('usuario.registro') }}" class="btn btn-accent btn-detalle w-100">
                                    <i data-lucide="briefcase"></i> Solicitar trabajo
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </aside>

            {{-- ===== Columna derecha: pestanas ===== --}}
            <section class="col-lg-8">

                <div class="detalle-tabs" role="tablist">
                    <button type="button" class="detalle-tab active" id="tab-info" data-bs-toggle="tab" data-bs-target="#panel-info" role="tab" aria-controls="panel-info" aria-selected="true">
                        <i data-lucide="info"></i> <span>Información</span>
                    </button>
                    <button type="button" class="detalle-tab" id="tab-trabajos" data-bs-toggle="tab" data-bs-target="#panel-trabajos" role="tab" aria-controls="panel-trabajos" aria-selected="false">
                        <i data-lucide="hammer"></i> <span>Trabajos</span>
                    </button>
                    <button type="button" class="detalle-tab" id="tab-resenas" data-bs-toggle="tab" data-bs-target="#panel-resenas" role="tab" aria-controls="panel-resenas" aria-selected="false">
                        <i data-lucide="message-square"></i> <span>Reseñas</span>
                    </button>
                </div>

                <div class="tab-content detalle-tarjeta">

                    {{-- INFORMACION --}}
                    <div class="tab-pane fade show active detalle-pane" id="panel-info" role="tabpanel" aria-labelledby="tab-info">
                        <div class="dato-info">
                            <i data-lucide="map-pin"></i>
                            <span>{{ $profesional->ciudad }} • {{ $profesional->provincia }}</span>
                        </div>

                        <div class="dato-info">
                            <i data-lucide="calendar-days"></i>
                            <span>Miembro desde {{ $profesional->created_at->locale('es')->translatedFormat('F \d\e Y') }}</span>
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
                    <div class="tab-pane fade detalle-pane text-center" id="panel-trabajos" role="tabpanel" aria-labelledby="tab-trabajos">
                        <i data-lucide="hammer" class="text-secondary mb-2"></i>
                        <p class="text-secondary mb-0">Todavía no hay trabajos publicados.</p>
                    </div>

                    {{-- RESENAS --}}
                    <div class="tab-pane fade detalle-pane" id="panel-resenas" role="tabpanel" aria-labelledby="tab-resenas">

                        @if ($total > 0)
                            <div class="resumen-resenas">
                                <div class="resumen-promedio puntaje">
                                    <div class="resumen-numero">{{ number_format($promedio, 1, ',', '.') }}</div>
                                    <div class="my-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i data-lucide="star" class="{{ $i <= round($promedio) ? 'estrella-llena' : 'estrella-vacia' }}"></i>
                                        @endfor
                                    </div>
                                    <small class="text-secondary">{{ $total }} {{ $total === 1 ? 'reseña' : 'reseñas' }}</small>
                                </div>

                                <div>
                                    @foreach ([5, 4, 3, 2, 1] as $estrellas)
                                        @php $cantidad = $conteo[$estrellas] ?? 0; @endphp
                                        <div class="fila-barra">
                                            <span>{{ $estrellas }} ★</span>
                                            <div class="barra-fondo"><span style="width: {{ round($cantidad * 100 / $total) }}%"></span></div>
                                            <span>{{ $cantidad }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Filtros por estrellas (todavia sin funcionalidad) --}}
                        <div class="filtro-estrellas">
                            <button type="button" class="btn-filtro-estrella activo">Todas</button>
                            @foreach ([5, 4, 3, 2, 1] as $estrellas)
                                <button type="button" class="btn-filtro-estrella">{{ $estrellas }} <i data-lucide="star"></i></button>
                            @endforeach
                        </div>

                        @forelse ($profesional->resenas as $resena)
                            <div class="tarjeta-resena">
                                <div class="inicial-resena">{{ mb_strtoupper(mb_substr($resena->user->usuario, 0, 1)) }}</div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <strong>{{ $resena->user->usuario }}</strong>
                                        <small class="text-secondary">{{ $resena->created_at->format('d/m/Y') }}</small>
                                    </div>
                                    <div class="my-1">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i data-lucide="star" class="{{ $i <= $resena->puntaje ? 'estrella-llena' : 'estrella-vacia' }}"></i>
                                        @endfor
                                    </div>
                                    @if ($resena->comentario)
                                        <p class="mb-0">{{ $resena->comentario }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-secondary text-center mb-0">Este profesional todavía no tiene reseñas.</p>
                        @endforelse

                        {{-- Dejar reseña: solo clientes logueados (todavia sin funcionalidad) --}}
                        @if (auth()->check() && auth()->user()->tipo === 'cliente')
                            <div class="d-flex justify-content-center mt-3">
                                <button type="button" class="btn btn-accent btn-detalle px-5" disabled>
                                    <i data-lucide="pencil"></i> Dejar una reseña
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>

    {{-- El duenio ve su bandeja de mensajes. El chat con un cliente se va a abrir recien al aceptar el presupuesto --}}
    @auth
        @if ($esDuenio)
            @include('chatify.box')
        @endif
    @endauth
@endsection