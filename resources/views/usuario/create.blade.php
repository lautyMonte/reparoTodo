@extends('layouts.app')
@section('contenido')
    
        <h1 class="titulo">Crear Cuenta</h1>
        <p class="subtitulo">¿Como vas a usar Reparo Todo?</p>
    
    <form action="#" method="POST" class="form-registro">
        @csrf

        <div class="tipo-cuenta">
            <button type="button" class="btn-tipo activo" data-tipo="cliente">Busco Profesional</button>
            <button type="button" class="btn-tipo" data-tipo="profesional">Ofrecer Servicios</button>
        </div>

        <input type="hidden" name="tipo" id="tipo" value="cliente">

        <div class="campo">
            <label for="usuario">Nombre de usuario</label>
            <input type="text" name="usuario" id="usuario" required value="{{ old('usuario') }}" placeholder="EJ:juanperez">
        
        
            <label for="email">Correo electrónico</label>
            <input type="email" name="email" id="email" required value="{{ old('email') }}" placeholder="EJ:juanperez@example.com">
        
        
            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" required placeholder="Minimo 8 caracteres">
        
        
            <label for="password_confirmation">Confirmar su contraseña</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Repetir contraseña">
        </div>

        <div class="campo" id="campo-cliente">
            <label for="telefono">Número de teléfono</label>
            <input type="number" name="telefono" id="telefono" required value="{{ old('telefono') }}" placeholder="EJ:1123456789">
        </div>
        
        <!--Campo exclusivo del profesional!-->
        <div class="campo" id="campo-profesional" hidden>
            <label for="especialidad">Selecciona tu especialidad</label>
            <input type="text" name="especialidad" id="especialidad" required>
        </div>
        <div class="terminos">
            <input type="checkbox" name="terminos" id="terminos" required>
            <label for="terminos">Acepto los términos y condiciones</label>
        </div>
        <button type="submit" class="btn-crear">Crear Cuenta</button>
    </form>
    <br><p class="subtitulo">Ya tienes una cuenta? <a href="{{ route('usuario.registro') }}">Inicia sesión</a></p>
@endsection
