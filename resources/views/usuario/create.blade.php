@extends('layouts.app', ['titulo' => 'Crear cuenta'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/create.css') }}">
@endpush

@section('contenido')
    <div class="contenedor-titulos">
        <h1 class="titulo">Crear Cuenta</h1>
        <p class="subtitulo">¿Como vas a usar Reparo Todo?</p>
        <div class="tipo-cuenta">
            <button type="button" class="btn-tipo activo" data-tipo="cliente">Busco Profesional</button>
            <button type="button" class="btn-tipo" data-tipo="profesional">Ofrecer Servicios</button>
        </div>
    </div>
    <form action="{{ route('usuario.store') }}" method="POST" class="form-registro">
        @csrf


        <input type="hidden" name="tipo" id="tipo" value="{{ old('tipo', 'cliente') }}">

        <div class="campo">
            <label for="usuario">Usuario</label>
            <input type="text" name="usuario" id="usuario" required value="{{ old('usuario') }}"
                placeholder="juanperez" maxlength="20">

            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" required value="{{ old('nombre') }}" placeholder="Juan"
                maxlength="50">

            <label for="apellido">Apellido</label>
            <input type="text" name="apellido" id="apellido" required value="{{ old('apellido') }}" placeholder="Perez"
                maxlength="50">

            <label for="email">Correo electrónico</label>
            <input type="email" name="email" id="email" required value="{{ old('email') }}"
                placeholder="juanperez@example.com" maxlength="255">

            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required placeholder="Minimo 8 caracteres" minlength="8"
                maxlength="20">

            <label for="password_confirmation">Confirmar su contraseña</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required
                placeholder="Repetir contraseña" minlength="8" maxlength="20">
        </div>

        <div class="campo">
            <label for="telefono">Número de teléfono</label>
            <input type="tel" name="telefono" id="telefono" value="{{ old('telefono') }}" placeholder="Ej: 2994123456"
                pattern="^\+?[0-9 ]{8,15}$" maxlength="16" required
                title="Solo números y espacios (entre 8 y 15). Podés empezar con +">
        </div>

        <div class="campo">
            <label for="genero">Género</label>
            <select name="genero" id="genero" class="tom-select" required>
                <option value="" style="color:#71809a">Seleccionar género</option>
                <option value="m" @selected(old('genero') === 'm')>Masculino</option>
                <option value="f" @selected(old('genero') === 'f')>Femenino</option>
            </select>
        </div>


        <!-- Campos exclusivos del profesional -->
        <div class="campo" id="campos-profesional" hidden>

            <label for="especialidad">Tus especialidades</label>

            <div>
                <select id="especialidad" name="especialidad[]" class="tom-select" multiple required>

                    <option value="">Seleccionar especialidades</option>

                    @foreach ($oficios as $oficio)
                        <option value="{{ $oficio->id }}" data-nombre-m="{{ $oficio->nombreParaGenero('m') }}"
                            data-nombre-f="{{ $oficio->nombreParaGenero('f') }}" @selected(in_array($oficio->id, (array) old('especialidad', [])))>
                            {{ $oficio->nombreParaGenero(old('genero')) }}
                        </option>
                    @endforeach

                </select>
            </div>


            <label for="provincia">Provincias en las que realizas servicios</label>

            <div>
                <select id="provincia" name="provincia[]" class="tom-select" multiple required>

                    <option value="">Seleccionar provincias</option>

                    @foreach ($provincias as $provincia)
                        <option value="{{ $provincia->id }}" @selected(in_array($provincia->id, (array) old('provincia', [])))>
                            {{ $provincia->nombre }}
                        </option>
                    @endforeach

                </select>
            </div>


            <label for="ciudad">Ciudades en las que realizas servicios</label>

            <div>
                <select id="ciudad" name="ciudad[]" class="tom-select" multiple disabled required>

                    <option value="">Seleccionar ciudades</option>

                </select>
            </div>

        </div>


        <div class="terminos">
            <input type="checkbox" name="terminos" id="terminos" required>
            <label for="terminos">Acepto los términos y condiciones</label>
        </div>
        <button type="submit" class="btn-crear">Crear Cuenta</button>
    </form>
    <br>
    <p class="subtitulo">Ya tienes una cuenta? <a href="{{ route('usuario.registro') }}">Inicia sesión</a></p>
@endsection

@push('scripts')
    <script>
        const provinciasDB = {{ Illuminate\Support\Js::from($provincias) }};
        const ciudadesAnteriores = {{ Illuminate\Support\Js::from((array) old('ciudad', [])) }};
    </script>

    <script src="{{ asset('js/registro.js') }}" defer></script>
    <script src="{{ asset('js/ciudades.js') }}" defer></script>
@endpush
