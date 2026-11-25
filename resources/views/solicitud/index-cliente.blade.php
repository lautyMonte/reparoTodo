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

        <div id="aviso-nuevas" class="alert alert-warning text-center d-none" data-pendientes="{{ auth()->user()->pendientesDeRespuesta() }}">
            Hay novedades en tus solicitudes. <a href="{{ request()->fullUrl() }}" class="alert-link">Actualizar</a>
        </div>

        <div class="bandeja-tarjeta">

            {{-- Lista de solicitudes (izquierda) --}}
            <div class="bandeja-lista">
                <div class="bandeja-filtros">
                    <a href="{{ route('solicitud.cliente.index') }}" class="bandeja-filtro {{ $estado === null ? 'activo' : '' }}">Todas {{ $total }}</a>
                    <a href="{{ route('solicitud.cliente.index', ['estado' => 'pendiente']) }}" class="bandeja-filtro {{ $estado === 'pendiente' ? 'activo' : '' }}">Pendientes {{ $conteo->get('pendiente', 0) }}</a>
                    <a href="{{ route('solicitud.cliente.index', ['estado' => 'aceptada']) }}" class="bandeja-filtro {{ $estado === 'aceptada' ? 'activo' : '' }}">Aceptadas {{ $conteo->get('aceptada', 0) }}</a>
                    <a href="{{ route('solicitud.cliente.index', ['estado' => 'presupuestada']) }}" class="bandeja-filtro {{ $estado === 'presupuestada' ? 'activo' : '' }}">Presupuestadas {{ $conteo->get('presupuestada', 0) }}</a>
                    <a href="{{ route('solicitud.cliente.index', ['estado' => 'en_chat']) }}" class="bandeja-filtro {{ $estado === 'en_chat' ? 'activo' : '' }}">En chat {{ $conteo->get('en_chat', 0) }}</a>
                    <a href="{{ route('solicitud.cliente.index', ['estado' => 'finalizada']) }}" class="bandeja-filtro {{ $estado === 'finalizada' ? 'activo' : '' }}">Finalizadas {{ $conteo->get('finalizada', 0) }}</a>
                    <a href="{{ route('solicitud.cliente.index', ['estado' => 'cancelada']) }}" class="bandeja-filtro {{ $estado === 'cancelada' ? 'activo' : '' }}">Canceladas {{ $conteo->get('cancelada', 0) }}</a>
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
                                'encabezado' => $seleccionada->estado === 'en_chat' ? 'Presupuesto aceptado' : 'Presupuesto recibido',
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

                    @php
                        // Cierre del trabajo: ¿hay una propuesta esperando respuesta? ¿la hice yo?
                        $hayPropuesta = $seleccionada->estado === 'en_chat' && $seleccionada->tieneCierrePendiente();
                        $laHiceYo = $hayPropuesta && $seleccionada->cierre_propuesto_por === auth()->id();
                        $esFinalizar = $seleccionada->cierre_tipo === 'finalizar';
                        $profesional = $seleccionada->profesional->user->nombreCompleto();
                    @endphp

                    <div class="bandeja-acciones">
                        @if ($seleccionada->estado === 'finalizada')
                            @include('partials.aviso-cierre', [
                                'clase' => 'cerrado-fin',
                                'icono' => 'circle-check',
                                'titulo' => 'Trabajo finalizado',
                                'texto' => 'Ustedes dos confirmaron que el trabajo está terminado. El chat con '.$profesional.' se cerró.',
                            ])
                        @endif
                        @if ($seleccionada->estado === 'cancelada')
                            @include('partials.aviso-cierre', [
                                'clase' => 'cerrado-can',
                                'icono' => 'ban',
                                'titulo' => 'Trabajo cancelado',
                                'texto' => 'Ustedes dos acordaron cancelar el trabajo. El chat con '.$profesional.' se cerró.',
                            ])
                        @endif
                        @if ($hayPropuesta && ! $laHiceYo)
                            @include('partials.aviso-cierre', [
                                'clase' => $esFinalizar ? '' : 'cancelar',
                                'icono' => $esFinalizar ? 'info' : 'triangle-alert',
                                'titulo' => $profesional.' propone '.($esFinalizar ? 'finalizar' : 'cancelar').' el trabajo',
                                'texto' => $esFinalizar ? 'Dice que el trabajo ya está terminado. ¿Estás de acuerdo?' : 'Dice que no va a poder hacerlo. ¿Estás de acuerdo en cancelarlo?',
                            ])
                        @endif
                        @if ($laHiceYo)
                            @include('partials.aviso-cierre', [
                                'clase' => $esFinalizar ? '' : 'cancelar',
                                'icono' => $esFinalizar ? 'info' : 'triangle-alert',
                                'titulo' => 'Esperando respuesta de '.$profesional,
                                'texto' => 'Propusiste '.($esFinalizar ? 'finalizar' : 'cancelar').' el trabajo. Te avisamos cuando responda.',
                            ])
                        @endif

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
                                @if ($seleccionada->estado === 'en_chat')
                                    <button type="button" class="btn btn-accent fw-semibold btn-abrir-chat"
                                            data-usuario-id="{{ $seleccionada->profesional->user->id }}"
                                            data-nombre="{{ $seleccionada->profesional->user->usuario }}"
                                            data-avatar="{{ $seleccionada->profesional->user->avatar() }}">
                                        <i data-lucide="message-circle"></i> Abrir chat
                                    </button>
                                    @if (! $hayPropuesta)
                                        <form action="{{ route('cierre.proponer', $seleccionada->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="tipo" value="finalizar">
                                            <button type="submit" class="btn btn-aceptar fw-semibold"><i data-lucide="check"></i> Finalizar trabajo</button>
                                        </form>
                                        <form action="{{ route('cierre.proponer', $seleccionada->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="tipo" value="cancelar">
                                            <button type="submit" class="btn btn-rechazar fw-semibold"><i data-lucide="x"></i> Cancelar trabajo</button>
                                        </form>
                                    @elseif ($laHiceYo)
                                        <form action="{{ route('cierre.retirar', $seleccionada->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-cancelar fw-semibold"><i data-lucide="undo-2"></i> Retirar propuesta</button>
                                        </form>
                                    @else
                                        <form action="{{ route('cierre.aceptar', $seleccionada->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-aceptar fw-semibold"><i data-lucide="check"></i> Aceptar</button>
                                        </form>
                                        <form action="{{ route('cierre.rechazar', $seleccionada->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-rechazar fw-semibold"><i data-lucide="x"></i> Rechazar</button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                            <a href="{{ route('contacto') }}" class="btn btn-soporte"><i data-lucide="life-buoy"></i> Soporte</a>
                        </div>
                        @if ($seleccionada->estado === 'pendiente')
                            <p class="bandeja-ayuda">Esperando que {{ $seleccionada->profesional->user->usuario }} responda tu solicitud.</p>
                        @endif
                        @if ($seleccionada->estado === 'aceptada')
                            <p class="bandeja-ayuda">{{ $seleccionada->profesional->user->usuario }} aceptó tu solicitud y te va a enviar un presupuesto.</p>
                        @endif
                        @if ($seleccionada->estado === 'presupuestada')
                            <p class="bandeja-ayuda">Si aceptás, se abre el chat con {{ $seleccionada->profesional->user->usuario }} para coordinar el trabajo. Si rechazás, el presupuesto se elimina para ambos y {{ $seleccionada->profesional->user->usuario }} recibe un aviso.</p>
                        @endif
                        @if ($seleccionada->estado === 'en_chat' && ! $hayPropuesta)
                            <p class="bandeja-ayuda">Aceptaste el presupuesto. Cuando termine el trabajo, confirmalo con “Finalizar trabajo”. Si no se hace, usá “Cancelar trabajo”. En los dos casos la otra persona tiene que estar de acuerdo.</p>
                        @endif
                        @if ($hayPropuesta && ! $laHiceYo)
                            <p class="bandeja-ayuda">Si aceptás, el trabajo queda {{ $esFinalizar ? 'finalizado' : 'cancelado' }} y se cierra el chat con {{ $profesional }}. Si rechazás, el trabajo sigue en chat.</p>
                        @endif
                        @if ($laHiceYo)
                            <p class="bandeja-ayuda">Si {{ $profesional }} acepta, el trabajo queda {{ $esFinalizar ? 'finalizado' : 'cancelado' }}. Mientras tanto, el trabajo sigue en chat.</p>
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