@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/contacto.css') }}">
@endpush

@section('contenido')
    <h1 class="titulo">Contacto</h1>

    <form action="{{ route('contacto') }}" method="POST" class="contact-form">
        @csrf
        <div class="campo">
            <label for="nombre">Nombre y Apellido</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}"
                placeholder="Ingresá tu nombre completo" required>
        </div>
        <div class="campo">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                placeholder="tucorreo@ejemplo.com" required>
        </div>

        <div class="campo">
            <label for="telefono">Teléfono (opcional)</label>
            <input type="tel" name="telefono" id="telefono" value="{{ old('telefono') }}"
                placeholder="+54 9 11 1234-5678" pattern="^\+?[0-9 ]{8,15}$">
        </div>
        <div class="campo">
            <label for="motivo">Motivo de la consulta</label>
            <select id="motivo" name="motivo" required>
                <option value="">Elegí un motivo</option>
                <option value="problema" @selected(old('motivo') === 'problema')>Problema con mi cuenta</option>
                <option value="reportar" @selected(old('motivo') === 'reportar')>Reportar a un profesional</option>
                <option value="sugerencia" @selected(old('motivo') === 'sugerencia')>Sugerencia</option>
                <option value="otro" @selected(old('motivo') === 'otro')>Otro motivo</option>
            </select>
        </div>
        <div class="campo d-none" id="contenedorAsunto">
            <label for="asunto">Asunto</label>
            <input type="text" name="asunto" id="asunto" value="{{ old('asunto') }}" placeholder="Motivo consulta"
                required>
        </div>
        <textarea class="mensaje" id="mensaje" name="mensaje" placeholder="Contanos tu consulta" rows="10" cols="47" required>{{ old('mensaje') }}</textarea>
        <button type="submit" class="btn-enviar">Enviar mensaje</button>
    </form>
    @if (session('exito'))
        <div class="modal fade" id="modalExito" tabindex="-1" aria-labelledby="modalExitoLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center p-4">
                    <div class="modal-body">
                        <i data-lucide="circle-check" class="text-success mb-3 icn-exito"></i>
                        <h4 id="modalExitoLabel">
                            ¡Mensaje enviado!
                        </h4>
                        <p class="txt-exito">Tu consulta fue enviada correctamente. Nos pondremos en contacto con vos lo antes posible.</p>
                        <button type="button" class="btn-exito mt-3" data-bs-dismiss="modal"> {{-- class="btn btn-primary mt-3" --}}
                            Aceptar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
@push('scripts')
    <script src="{{ asset('js/contacto.js') }}" defer></script>
@endpush
