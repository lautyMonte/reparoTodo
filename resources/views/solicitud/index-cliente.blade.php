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
            <h1 class="h3 mb-1">Mis solicitudes</h1>
            <p class="text-secondary mb-0">Seguí tus pedidos y respondé a los presupuestos que te llegan.</p>
        </div>

        <div class="bandeja-tarjeta">

            {{-- Lista de solicitudes (izquierda) --}}
            <div class="bandeja-lista">
                <div class="bandeja-filtros">
                    <a href="{{ route('solicitud.cliente.index') }}" class="bandeja-filtro {{ $estado === null ? 'activo' : '' }}">Todas {{ $total }}</a>
                    <a href="{{ route('solicitud.cliente.index', ['estado' => 'pendiente']) }}" class="bandeja-filtro {{ $estado === 'pendiente' ? 'activo' : '' }}">Pendientes {{ $conteo->get('pendiente', 0) }}</a>
                    <a href="{{ route('solicitud.cliente.index', ['estado' => 'aceptada']) }}" class="bandeja-filtro {{ $estado === 'aceptada' ? 'activo' : '' }}">Aceptadas {{ $conteo->get('aceptada', 0) }}</a>
                    <a href="{{ route('solicitud.cliente.index', ['estado' => 'presupuestada']) }}" class="bandeja-filtro {{ $estado === 'presupuestada' ? 'activo' : '' }}">Presupuestadas {{ $conteo->get('presupuestada', 0) }}</a>
                </div>

                @forelse ($lista as $solicitud)
                    <a href="{{ route('solicitud.cliente.show', ['id' => $solicitud->id, 'estado' => $estado]) }}"
                       class="bandeja-item borde-{{ $solicitud->estado }} {{ $seleccionada && $seleccionada->id === $solicitud->id ? 'activa' : '' }} {{ $conNovedades->contains($solicitud->id) ? 'no-leida' : '' }}">
                        <img src="{{ $solicitud->profesional->user->avatar() }}" alt="" class="bandeja-avatar">
                        <div class="bandeja-cuerpo">
                            <div class="bandeja-fila">
                                <span class="bandeja-nombre">
                                    @if ($conNovedades->contains($solicitud->id))
                                        <span class="bandeja-punto"></span>
                                    @endif
                                    {{ $solicitud->profesional->user->usuario }}
                                </span>
                                <span class="bandeja-fecha">{{ $solicitud->created_at->timezone($zona)->format('d/m H:i') }}</span>
                            </div>
                            <div class="bandeja-titulo-item">{{ $solicitud->titulo }}</div>
                            <div class="bandeja-resumen">
                                @if ($solicitud->presupuesto)
                                    Te envió un presupuesto: ${{ number_format($solicitud->presupuesto->monto, 0, ',', '.') }} en {{ $solicitud->presupuesto->plazo_dias }} {{ $solicitud->presupuesto->plazo_dias === 1 ? 'día' : 'días' }}.
                                @else
                                    {{ $solicitud->descripcion }}
                                @endif
                            </div>
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
                            <img src="{{ $seleccionada->profesional->user->avatar() }}" alt="" class="bandeja-avatar">
                            <div>
                                <div class="fw-bold bandeja-nombre">{{ $seleccionada->profesional->user->usuario }}</div>
                                <div class="small text-secondary">
                                    Le enviaste esta solicitud el {{ $seleccionada->created_at->timezone($zona)->format('d/m/Y') }}
                                    a las {{ $seleccionada->created_at->timezone($zona)->format('H:i') }}
                                </div>
                            </div>
                            <span class="estado estado-{{ $seleccionada->estado }} ms-auto">{{ $etiquetas[$seleccionada->estado] }}</span>
                        </div>

                        <h2 class="h4 mb-0">{{ $seleccionada->titulo }}</h2>

                        @if ($seleccionada->presupuesto)
                            <div class="presu-tarjeta">
                                <div class="presu-titulo"><i data-lucide="file-text"></i> Presupuesto recibido</div>
                                <div class="presu-datos">
                                    <div>
                                        <div class="presu-etiqueta">Monto total</div>
                                        <div class="presu-valor">${{ number_format($seleccionada->presupuesto->monto, 0, ',', '.') }}</div>
                                    </div>
                                    <div>
                                        <div class="presu-etiqueta">Plazo estimado</div>
                                        <div class="presu-valor">{{ $seleccionada->presupuesto->plazo_dias }} {{ $seleccionada->presupuesto->plazo_dias === 1 ? 'día' : 'días' }}</div>
                                    </div>
                                </div>
                                <div class="presu-etiqueta mt-3">Detalle</div>
                                <p class="mb-0">{{ $seleccionada->presupuesto->detalle }}</p>
                            </div>
                        @endif

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
                                @if ($seleccionada->estado === 'presupuestada')
                                    <form action="{{ route('presupuesto.aceptar', $seleccionada->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-aceptar fw-semibold"><i data-lucide="check"></i> Aceptar</button>
                                    </form>
                                    <button type="button" class="btn btn-rechazar fw-semibold" data-bs-toggle="modal" data-bs-target="#modalRechazar"><i data-lucide="x"></i> Rechazar</button>
                                @endif
                            </div>
                            <a href="{{ route('contacto') }}" class="btn btn-soporte"><i data-lucide="life-buoy"></i> Soporte</a>
                        </div>
                        @if ($seleccionada->estado === 'pendiente')
                            <p class="bandeja-ayuda">Esperando que {{ $seleccionada->profesional->user->usuario }} responda tu solicitud.</p>
                        @elseif ($seleccionada->estado === 'aceptada')
                            <p class="bandeja-ayuda">{{ $seleccionada->profesional->user->usuario }} aceptó tu solicitud y te va a enviar un presupuesto.</p>
                        @elseif ($seleccionada->estado === 'presupuestada')
                            <p class="bandeja-ayuda">Si aceptás, se abre el chat con {{ $seleccionada->profesional->user->usuario }} para coordinar el trabajo. Si rechazás, el presupuesto se elimina para ambos y {{ $seleccionada->profesional->user->usuario }} recibe un aviso.</p>
                        @endif
                    </div>
                @else
                    <div class="bandeja-vacio">
                        <i data-lucide="inbox"></i>
                        <p class="fw-bold mb-1">Todavía no hiciste solicitudes</p>
                        <p class="small mb-0">Cuando le pidas un trabajo a un profesional, lo vas a ver acá.</p>
                    </div>
                @endif
            </div>
        </div>
        
        @if ($seleccionada && $seleccionada->estado === 'presupuestada')
            <div class="modal fade" id="modalRechazar" tabindex="-1" aria-labelledby="modalRechazarLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center p-4">
                        <div class="modal-body">
                            <i data-lucide="circle-alert" class="icn-rechazo mb-3"></i>
                            <h4 id="modalRechazarLabel">¿Rechazar este presupuesto?</h4>
                            <p>Se elimina para ambos y {{ $seleccionada->profesional->user->usuario }} recibe un aviso. Esta acción no se puede deshacer.</p>
                            <form action="{{ route('presupuesto.rechazar', $seleccionada->id) }}" method="POST" class="d-flex justify-content-center gap-2 mt-3">
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