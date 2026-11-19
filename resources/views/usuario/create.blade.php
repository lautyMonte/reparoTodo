@extends('layouts.login')
@section('session-content')
    <form action="{{ route('usuario.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="usuario">Usuario:</label>
            <input type="text" name="usuario" id="usuario" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" id="telefono" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="imagen">Imagen de perfil:</label>
            <input type="file" name="imagen" id="imagen" class="form-control-file">
        </div>
        <div class="form-group">
            <label for="tipo">Tipo de usuario:</label>
            <select name="tipo" id="tipo" class="form-control">
                <option value="comun">Común</option>
                <option value="profesional">Profesional</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Registrar</button>
    </form>
@endsection