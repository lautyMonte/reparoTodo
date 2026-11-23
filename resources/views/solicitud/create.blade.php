@extends('layouts.app', ['titulo' => $titulo])

@section('contenido')
    @php
        $usuario = $profesional->user;
    @endphp

    <div class="solicitud-contenedor">

        {{-- Volver al detalle del profesional (a la izquierda, como en el detalle) --}}
        <a href="{{ route('profesional.show', $profesional->id) }}" class="btn btn-accent btn-barra fw-semibold solicitud-volver">
            <i data-lucide="arrow-left"></i> Volver
        </a>

        <div class="solicitud-pagina">
            <div class="solicitud-tarjeta">

                {{-- A quien le pedis el trabajo --}}
                <div class="solicitud-destinatario">
                    <img src="{{ $usuario->avatar() }}" alt="Foto de {{ $usuario->usuario }}">
                    <div>
                        <div class="text-secondary small">Vas a solicitar un trabajo a</div>
                        <h1 class="h5 mb-1">{{ $usuario->usuario }}</h1>
                        <span class="puntaje small">
                            <i data-lucide="star"></i>
                            @if ($profesional->resenas_count > 0)
                                <strong>{{ number_format($profesional->resenas_avg_puntaje, 1, ',', '.') }}</strong>
                                <span class="text-secondary">({{ $profesional->resenas_count }} {{ $profesional->resenas_count === 1 ? 'reseña' : 'reseñas' }})</span>
                            @else
                                <span class="text-secondary">Sin reseñas</span>
                            @endif
                        </span>
                    </div>
                </div>

                <form action="{{ route('solicitud.store', $profesional->id) }}" method="POST" enctype="multipart/form-data" class="solicitud-cuerpo">
                    @csrf

                    <div class="solicitud-encabezado">
                        <h2 class="h4 mb-1">Solicitar trabajo</h2>
                        <p class="text-secondary mb-0">Contale qué necesitás. Si le interesa, te va a enviar un presupuesto.</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="titulo">Nombre del trabajo</label>
                        <input type="text" name="titulo" id="titulo" maxlength="80" required
                               class="form-control @error('titulo') is-invalid @enderror"
                               value="{{ old('titulo') }}" placeholder="Ej: Cambio de tomacorrientes">
                        @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="descripcion">Descripción</label>
                        <textarea name="descripcion" id="descripcion" rows="5" maxlength="1000" required
                                  class="form-control @error('descripcion') is-invalid @enderror"
                                  placeholder="Explicá qué hay que hacer y cualquier detalle que ayude a calcular el presupuesto">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="zona">Zona o barrio <span class="solicitud-opcional">(opcional)</span></label>
                            <input type="text" name="zona" id="zona" maxlength="100"
                                   class="form-control @error('zona') is-invalid @enderror"
                                   value="{{ old('zona') }}" placeholder="Ej: Alta Barda, Neuquén">
                            @error('zona')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="fecha_deseada">Fecha deseada <span class="solicitud-opcional">(opcional)</span></label>
                            <input type="date" name="fecha_deseada" id="fecha_deseada" min="{{ date('Y-m-d') }}"
                                   class="form-control @error('fecha_deseada') is-invalid @enderror"
                                   value="{{ old('fecha_deseada') }}">
                            @error('fecha_deseada')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label" for="fotos">Fotos <span class="solicitud-opcional">(opcional)</span></label>
                        <input type="file" name="fotos[]" id="fotos" multiple accept=".jpg,.jpeg,.png,.webp"
                               class="form-control @if ($errors->has('fotos') || $errors->has('fotos.*')) is-invalid @endif">
                        @if ($errors->has('fotos') || $errors->has('fotos.*'))
                            <div class="invalid-feedback">{{ $errors->first('fotos') ?: $errors->first('fotos.*') }}</div>
                        @endif
                        <div class="form-text">Hasta 3 fotos en formato JPG, PNG o WEBP, de 2 MB cada una.</div>
                    </div>

                    <div class="solicitud-acciones">
                        <button type="submit" class="btn btn-accent btn-detalle">
                            <i data-lucide="send"></i> Enviar solicitud
                        </button>
                        <a href="{{ route('profesional.show', $profesional->id) }}" class="btn btn-detalle btn-cancelar">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection