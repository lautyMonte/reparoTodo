@extends('layouts.app', ['title' => $titulo])

@section('contenido')
            <h1 class="titulo">¡Bienvenido!</h1>
            <p class="subtitulo">Ingresá a tu cuenta para encontrar profesionales y gestionar tus solicitudes</p>

        <form class="login-form" method="POST" action="{{route('usuario.verifica')}}">
            @csrf

            <div class="campo">
                <label for="usuario">Nombre de usuario</label>
                <input
                    type="text"
                    id="usuario"
                    name="usuario"
                    placeholder="Ingresá tu usuario"
                    required
                >
            </div>

            <div class="campo">
                <label for="password">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Ingresá tu contraseña"
                    required
                >
            </div>

            <button type="submit" class="btn-login">Iniciar sesion</button>

            <p class="subtitulo">¿Todavia no tenés cuenta?<a href="{{ route('usuario.create') }}"> Registrate</a></p>
        </form>
@endsection