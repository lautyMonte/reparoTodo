@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/create.css') }}">
@endpush
@section('contenido')
    <div class="contenedor-titulos">
        <h1 class="titulo">Crear Cuenta</h1>
        <p class="subtitulo">
            Creá tu cuenta para contratar servicios o presentar tu trabajo como profesional.
        </p>
    </div>
    <form action="{{ route('usuario.store') }}" method="POST" class="form-registro">
        @csrf

        <div class="campo">

            <label for="nombre">Nombre</label>
            <input
                type="text"
                name="nombre"
                id="nombre"
                value="{{ old('nombre') }}"
                maxlength="100"
                required
                placeholder = "Juan"
            >

            <label for="apellido">Apellido</label>
            <input
                type="text"
                name="apellido"
                id="apellido"
                value="{{ old('apellido') }}"
                maxlength="100"
                required
                placeholder = "Perez"
            >

            <label for="usuario">Nombre de usuario</label>
            <input
                type="text"
                name="usuario"
                id="usuario"
                required value="{{ old('usuario') }}"
                placeholder="juanperez"
            >

            <label for="email">Correo electrónico</label>
            <input
                type="email"
                name="email"
                id="email"
                required value="{{ old('email') }}"
                placeholder="juanperez@example.com"
            >


            <label for="password">Contraseña:</label>
            <input
                type="password"
                name="password"
                id="password"
                required
                placeholder="Minimo 8 caracteres"
                minlength="8"
                maxlength="20"
            >

            <label for="password_confirmation">Confirmar su contraseña</label>
            <input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                required
                placeholder="Repetir contraseña"
            >
        </div>

        <div class="campo" id="campo-cliente">
            <label for="telefono">Número de teléfono</label>
            <input
                type="tel"
                name="telefono"
                id="telefono" value="{{ old('telefono') }}"
                placeholder="2991234567"
                pattern="^\+?[0-9 ]{8,15}$"
                required
            >
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
