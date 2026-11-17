<!doctype html>
<html lang="es">
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="usuario-id" content="{{ auth()->id() }}">
    <title>Lista de Profesionales</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">
    <link rel="stylesheet" href="{{ asset('css/box.css') }}">
</head>
<body>

    <header class="header">

        <a class="navbar-brand fw-bold" href="{{ route('profesional.listado') }}">Reparo<span class="brand-accent">Todo</span></a>
        <nav>
            <a href="{{route('usuario.registro')}}">Cerrar sesion</a>
        </nav>
    </header>

<main class="container">
    @include('partials.errores-validacion')
    @yield('content')
</main>

<footer class="footer">
        <p>© 2026 Mi Aplicación. Todos los derechos reservados.</p>
    </footer>
<script src="{{ asset('js/chat.js') }}" defer></script>
<script src="{{ asset('js/box.js') }}" defer></script>
@stack('scripts')
</body>
</html>
