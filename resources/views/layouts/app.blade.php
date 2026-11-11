<!doctype html>
<html lang="es">
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

    <header class="header">
        <h2>Mi Aplicación</h2>
        <nav>
            <a href="{{route('usuario.index')}}">Inicio</a>
        </nav>
    </header>

<main class="container">
    @include('partials.errores-validacion')
    @yield('content')
</main>

<footer class="footer">
        <p>© 2026 Mi Aplicación. Todos los derechos reservados.</p>
    </footer>

</body>
</html>
