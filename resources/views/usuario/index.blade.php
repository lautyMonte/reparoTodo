@extends('layouts.app', ['title' => $titulo])

@section('content')
        <form class="login-form" method="POST" action="{{route('usuario.verifica')}}">
            @csrf

            <h1>!Bienvenido¡</h1>
            <p>Ingresá a tu cuenta para encontrar profesionales y gestionar tus solicitudes</p>

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

            <button type="submit">Ingresar</button>

            <a href="#">¿No tenés una cuenta? Registrate</a>
        </form>
@endsection