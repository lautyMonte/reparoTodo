@extends('layouts.login')
@section('session-content')
    <h1 class="registro-titulo">Crear Cuenta</h1>
    <p class="registro-subtitulo">¿Como vas a usar Reparo Todo?</p>
    <form action="#" method="POST" id="opcion-form">
        <div class="form-group" id="tipo-cuenta">
            <button type="submit" class="btn btn-primary" class="tipo-cuenta button">Busco Profesional</button>
            <button type="submit" class="btn btn-secondary" class="tipo-cuenta button.activo">Ofrecer Servicios</button>
        </div>
    </form>

    <form action="{{ route('usuario.store') }}" method="POST" enctype="multipart/form-data" class="form-registro">
        @csrf
        <div class="form-group">
            <label for="usuario">Nombre de usuario</label>
            <input type="text" name="usuario" id="usuario" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Correo electrónico</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirmar su contraseña</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="telefono">Número de teléfono</label>
            <input type="text" name="telefono" id="telefono" class="form-control">
        </div>
        <div class="form-group">
            <input type="checkbox" name="terminos" id="terminos" class="form-control-file">
            <label for="terminos">Acepto los términos y condiciones</label>
        </div>
        <button type="submit" class="btn btn-primary">Registrar</button>
    </form>
@endsection