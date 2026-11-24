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


        <input type="hidden" name="tipo" id="tipo" value="cliente">

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

        <!--Campo exclusivo del profesional!-->
        <div class="campo" id="campos-profesional" hidden>
            <label for="especialidad">Tus especialidades</label>
            {{-- <input type="text" name="especialidad" id="especialidad" value="{{ old('especialidad') }}"
                placeholder="Electricista"> --}}
            <select id="especialidad" name="especialidad" required>
                <option value="">Seleccionar especialidad</option>

                @foreach ($oficios as $oficio)
                    <option value="{{ $oficio->id }}" @selected(old('especialidad') == $oficio->id)>
                        {{ $oficio->tipo }}
                    </option>
                @endforeach
            </select>


            <label for="provincia">Provincia en la que realizas servicios</label>
            {{-- <input type="text" name="provincia" id="provincia" value="{{old ('provincia')}}" placeholder="Rio Negro"> --}}
            <select id="provincia" name="provincia" required>
                <option value="">Seleccionar provincia</option>
                <option value="nqn">Neuquén</option>
                <option value="rn">Río Negro</option>
            </select>

            <label for="ciudad">Ciudad en la que realizas servicios</label>
            {{-- <input type="text" name="ciudad" id="ciudad" value="{{old ('ciudad')}}" placeholder="Cipolletti"> --}}
            <select id="ciudad" name="ciudad" data-ciudad-anterior="{{ old('ciudad') }}" disabled required>
                <option value="">Seleccionar ciudad</option>
            </select>
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
    <script src="{{ asset('js/registro.js') }}"></script>
    <script src="{{ asset('js/ciudades.js') }}" defer></script>
@endpush
