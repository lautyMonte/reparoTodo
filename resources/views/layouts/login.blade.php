<!doctype html>
<html lang="es">
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="usuario-id" content="{{ auth()->id() }}">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

    <header class="header">

        <p class="navbar-brand fw-bold">Reparo<span class="brand-accent">Todo</span></p>

    </header>

<main class="container">
    @include('partials.errores-validacion')
    @yield('session-content')
</main>

<footer class="footer">
        <p>© 2026 Mi Aplicación. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
