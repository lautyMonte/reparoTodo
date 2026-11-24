@extends('layouts.app', ['titulo' => $titulo])

@section('contenido')
    @php
        // La base guarda las horas en UTC; las mostramos en hora de Argentina
        $zona = 'America/Argentina/Buenos_Aires';

        // Nombre que se ve para cada estado
        $etiquetas = [
            'pendiente' => 'Pendiente',
            'aceptada' => 'Aceptada',
            'presupuestada' => 'Presupuestada',
            'en_chat' => 'En chat',
            'finalizada' => 'Finalizada',
            'cancelada' => 'Cancelada',
        ];
    @endphp

    <div class="bandeja-pagina">
        <div class="bandeja-encabezado">
            <h1 class="h3 mb-1">Solicitudes recibidas</h1>
            <p class="text-secondary mb-0">Revisá lo que te piden los clientes y respondé desde acá.</p>
        </div>

        <div id="aviso-nuevas" class="alert alert-warning text-center d-none" data-pendientes="{{ $conteo->get('pendiente', 0) + $conteo->get('aceptada', 0) }}">
            Llegaron solicitudes nuevas. <a href="{{ request()->fullUrl() }}" class="alert-link">Actualizar</a>
        </div>

        <div class="bandeja-tarjeta">

            {{-- Lista de solicitudes (izquierda) --}}
            <div class="bandeja-lista">
                <div class="bandeja-filtros">
                    <a href="{{ route('solicitud.index') }}" class="bandeja-filtro {{ $estado === null ? 'activo' : '' }}">Todas {{ $total }}</a>
                    <a href="{{ route('solicitud.index', ['estado' => 'pendiente']) }}" class="bandeja-filtro {{ $estado === 'pendiente' ? 'activo' : '' }}">Pendientes {{ $conteo->get('pendiente', 0) }}</a>
                    <a href="{{ route('solicitud.index', ['estado' => 'aceptada']) }}" class="bandeja-filtro {{ $estado === 'aceptada' ? 'activo' : '' }}">Aceptadas {{ $conteo->get('aceptada', 0) }}</a>
                    <a href="{{ route('solicitud.index', ['estado' => 'presupuestada']) }}" class="bandeja-filtro {{ $estado === 'presupuestada' ? 'activo' : '' }}">Presupuestadas {{ $conteo->get('presupuestada', 0) }}</a>
                    <a href="{{ route('solicitud.index', ['estado' => 'en_chat']) }}" class="bandeja-filtro {{ $estado === 'en_chat' ? 'activo' : '' }}">En chat {{ $conteo->get('en_chat', 0) }}</a>
                </div>

                @forelse ($lista as $solicitud)
                    <a href="{{ route('solicitud.show', ['id' => $solicitud->id, 'estado' => $estado]) }}"
                       class="bandeja-item borde-{{ $solicitud->estado }} {{ $seleccionada && $seleccionada->id === $solicitud->id ? 'activa' : '' }} {{ $solicitud->leida_profesional ? '' : 'no-leida' }}">
                        <img src="{{ $solicitud->cliente->avatar() }}" alt="" class="bandeja-avatar">
                        <div class="bandeja-cuerpo">
                            <div class="bandeja-fila">
                                <span class="bandeja-nombre">
                                    @unless ($solicitud->leida_profesional)
                                        <span class="bandeja-punto"></span>
                                    @endunless
                                    {{ $solicitud->cliente->usuario }}
                                </span>
                                <span class="bandeja-fecha">{{ $solicitud->created_at->timezone($zona)->format('d/m H:i') }}</span>
                            </div>
                            <div class="bandeja-titulo-item">{{ $solicitud->titulo }}</div>
                            <div class="bandeja-resumen">{{ $solicitud->descripcion }}</div>
                            <span class="estado estado-{{ $solicitud->estado }}">{{ $etiquetas[$solicitud->estado] }}</span>
                        </div>
                    </a>
                @empty
                    <p class="bandeja-vacia">No hay solicitudes para mostrar.</p>
                @endforelse
            </div>

            {{-- Detalle de la solicitud elegida (derecha) --}}
            <div class="bandeja-detalle">
                @if ($seleccionada)
                    <div class="bandeja-detalle-cuerpo">
                        <div class="bandeja-de">
                            <img src="{{ $seleccionada->cliente->avatar() }}" alt="" class="bandeja-avatar">
                            <div>
                                <div class="fw-bold bandeja-nombre">{{ $seleccionada->cliente->usuario }}</div>
                                <div class="small text-secondary">
                                    Te envió esta solicitud el {{ $seleccionada->created_at->timezone($zona)->format('d/m/Y') }}
                                    a las {{ $seleccionada->created_at->timezone($zona)->format('H:i') }}
                                </div>
                            </div>
                            <span class="estado estado-{{ $seleccionada->estado }} ms-auto">{{ $etiquetas[$seleccionada->estado] }}</span>
                        </div>

                        <h2 class="h4 mb-0">{{ $seleccionada->titulo }}</h2>

                        @if ($seleccionada->zona || $seleccionada->fecha_deseada)
                            <div class="bandeja-datos">
                                @if ($seleccionada->zona)
                                    <span class="bandeja-dato"><i data-lucide="map-pin"></i> {{ $seleccionada->zona }}</span>
                                @endif
                                @if ($seleccionada->fecha_deseada)
                                    <span class="bandeja-dato"><i data-lucide="calendar"></i> Para el {{ $seleccionada->fecha_deseada->format('d/m/Y') }}</span>
                                @endif
                            </div>
                        @endif

                        <p class="bandeja-descripcion">{{ $seleccionada->descripcion }}</p>

                        @if ($seleccionada->presupuesto)
                            @include('partials.presupuesto', [
                                'presupuesto' => $seleccionada->presupuesto,
                                'encabezado' => $seleccionada->estado === 'en_chat' ? 'Presupuesto aceptado' : 'Presupuesto enviado',
                            ])
                        @endif

                        @if ($seleccionada->fotos->isNotEmpty())
                            <div class="bandeja-subtitulo">Fotos adjuntas ({{ $seleccionada->fotos->count() }})</div>
                            <div class="bandeja-fotos">
                                @foreach ($seleccionada->fotos as $foto)
                                    <a href="{{ asset('storage/'.$foto->ruta) }}" target="_blank">
                                        <img src="{{ asset('storage/'.$foto->ruta) }}" alt="Foto {{ $loop->iteration }} de la solicitud" class="bandeja-foto">
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="bandeja-acciones">
                        <div class="bandeja-botones">
                            <span></span>
                            <div class="bandeja-botones-centro">
                                @if ($seleccionada->estado === 'pendiente')
                                    <form action="{{ route('solicitud.aceptar', $seleccionada->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-aceptar fw-semibold"><i data-lucide="check"></i> Aceptar</button>
                                    </form>
                                    <button type="button" class="btn btn-rechazar fw-semibold" data-bs-toggle="modal" data-bs-target="#modalRechazar"><i data-lucide="x"></i> Rechazar</button>
                                @endif
                                @if ($seleccionada->estado === 'aceptada')
                                    <a href="{{ route('presupuesto.create', $seleccionada->id) }}" class="btn btn-accent fw-semibold"><i data-lucide="file-text"></i> Armar presupuesto</a>
                                @endif
                                @if ($seleccionada->estado === 'en_chat')
                                    <button type="button" class="btn btn-accent fw-semibold btn-abrir-chat"
                                            data-usuario-id="{{ $seleccionada->cliente->id }}"
                                            data-nombre="{{ $seleccionada->cliente->usuario }}"
                                            data-avatar="{{ $seleccionada->cliente->avatar() }}">
                                        <i data-lucide="message-circle"></i> Abrir chat
                                    </button>
                                @endif
                            </div>
                            <a href="{{ route('contacto') }}" class="btn btn-soporte"><i data-lucide="life-buoy"></i> Soporte</a>
                        </div>
                        @if ($seleccionada->estado === 'pendiente')
                            <p class="bandeja-ayuda">Si aceptás, vas a armar el presupuesto para el cliente. Si rechazás, la solicitud se elimina para ambos y {{ $seleccionada->cliente->usuario }} recibe un aviso.</p>
                        @endif
                        @if ($seleccionada->estado === 'aceptada')
                            <p class="bandeja-ayuda">Aceptaste esta solicitud. Armá el presupuesto para que {{ $seleccionada->cliente->usuario }} decida si lo acepta.</p>
                        @endif
                        @if ($seleccionada->estado === 'en_chat')
                            <p class="bandeja-ayuda">{{ $seleccionada->cliente->usuario }} aceptó tu presupuesto. Ya pueden hablar por el chat para coordinar el trabajo.</p>
                        @endif
                    </div>
                @else
                    <div class="bandeja-vacio">
                        <i data-lucide="inbox"></i>
                        <p class="fw-bold mb-1">Todavía no recibiste solicitudes</p>
                        <p class="small mb-0">Cuando un cliente te pida un trabajo, lo vas a ver acá.</p>
                    </div>
                @endif
            </div>
        </div>
        @if ($seleccionada && $seleccionada->estado === 'pendiente')
            <div class="modal fade" id="modalRechazar" tabindex="-1" aria-labelledby="modalRechazarLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center p-4">
                        <div class="modal-body">
                            <i data-lucide="circle-alert" class="icn-rechazo mb-3"></i>
                            <h4 id="modalRechazarLabel">¿Rechazar esta solicitud?</h4>
                            <p>Se elimina para ambos y {{ $seleccionada->cliente->usuario }} recibe un aviso. Esta acción no se puede deshacer.</p>
                            <form action="{{ route('solicitud.rechazar', $seleccionada->id) }}" method="POST" class="d-flex justify-content-center gap-2 mt-3">
                                @csrf
                                <button type="button" class="btn btn-detalle btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-rechazar-confirmar fw-semibold">Sí, rechazar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection