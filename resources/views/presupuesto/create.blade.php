@extends('layouts.app', ['titulo' => $titulo])

@section('contenido')
    <div class="solicitud-contenedor">

        {{-- Volver a la bandeja, con esta solicitud abierta --}}
        <a href="{{ route('solicitud.show', $solicitud->id) }}" class="btn btn-accent btn-barra fw-semibold solicitud-volver">
            <i data-lucide="arrow-left"></i> Volver
        </a>

        <div class="solicitud-pagina">
            <div class="solicitud-tarjeta">

                {{-- A quien le mandas el presupuesto y por que trabajo --}}
                <div class="solicitud-destinatario">
                    <img src="{{ $solicitud->cliente->avatar() }}" alt="Foto de {{ $solicitud->cliente->usuario }}">
                    <div>
                        <div class="text-secondary small">Vas a enviarle un presupuesto a</div>
                        <h1 class="h5 mb-1">{{ $solicitud->cliente->usuario }}</h1>
                        <span class="small">Por: «{{ $solicitud->titulo }}»</span>
                    </div>
                </div>

                <form action="{{ route('presupuesto.store', $solicitud->id) }}" method="POST" class="solicitud-cuerpo">
                    @csrf

                    <div class="solicitud-encabezado">
                        <h2 class="h4 mb-1">Armar presupuesto</h2>
                        <p class="text-secondary mb-0">Indicá cuánto cuesta y cuánto tarda. {{ $solicitud->cliente->usuario }} lo va a ver y puede aceptarlo o rechazarlo.</p>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="monto">Monto total</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text">$</span>
                                <input type="number" name="monto" id="monto" min="1" max="99999999" step="1" required
                                       class="form-control @error('monto') is-invalid @enderror"
                                       value="{{ old('monto') }}" placeholder="Ej: 45000">
                                @error('monto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="plazo_dias">Plazo estimado (en días)</label>
                            <input type="number" name="plazo_dias" id="plazo_dias" min="1" max="365" step="1" required
                                   class="form-control @error('plazo_dias') is-invalid @enderror"
                                   value="{{ old('plazo_dias') }}" placeholder="Ej: 3">
                            @error('plazo_dias')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="detalle">Detalle del trabajo</label>
                        <textarea name="detalle" id="detalle" rows="5" maxlength="1000" required
                                  class="form-control @error('detalle') is-invalid @enderror"
                                  placeholder="Explicá qué incluye el presupuesto: tareas, materiales, condiciones">{{ old('detalle') }}</textarea>
                        @error('detalle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="solicitud-acciones">
                        <button type="submit" class="btn btn-accent btn-detalle">
                            <i data-lucide="send"></i> Enviar presupuesto
                        </button>
                        <a href="{{ route('solicitud.show', $solicitud->id) }}" class="btn btn-detalle btn-cancelar">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection