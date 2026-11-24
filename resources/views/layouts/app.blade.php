<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="usuario-id" content="{{ auth()->id() }}">
    @endauth
    <title>{{ isset($titulo) ? $titulo . ' | ReparoTodo' : 'ReparoTodo' }}</title>

    {{-- Logo en la pestaña del navegador --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">

    {{-- Fuentes del Style Tile --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Nunito+Sans:wght@400;600;700&display=swap"
        rel="stylesheet">

    {{-- Bootstrap por CDN y después nuestros estilos (el orden importa) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons: solo para los logos de redes del footer --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- CSS de Tom Select para Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.7.0/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/reparotodo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">
    <link rel="stylesheet" href="{{ asset('css/box.css') }}">
    <link rel="stylesheet" href="{{ asset('css/edit.css') }}">

    @stack('styles')

    {{--
    <link rel="stylesheet" href="{{ asset('css/create.css') }}"> Estilos específicos para el crear un usuario 
    <link rel="stylesheet" href="{{ asset('css/login.css') }}"> Estilos específicos para el crear un usuario
     --}}

</head>

<body class="d-flex flex-column min-vh-100">

    {{-- Header compartido por todas las páginas --}}
    @include('partials.header')

    <main class="container py-4 flex-grow-1">
        {{-- Mensajes que mandan los controladores con ->with('error', ...) o ->with('exito', ...) --}}
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (session('exito') && !request()->routeIs('contacto'))
            <div class="alert alert-success">{{ session('exito') }}</div>
        @endif

        @include('partials.errores-validacion')

        @yield('contenido')
    </main>

    {{-- Footer compartido por todas las páginas --}}
    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Lucide: librería de íconos del Style Tile --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    {{-- El chat solo existe para usuarios logueados --}}
    @auth
        <script src="{{ asset('js/chat.js') }}" defer></script>
        <script src="{{ asset('js/notificaciones.js') }}" defer></script>
    @endauth

    @stack('scripts')

    {{-- Convierte cada <i data-lucide="..."> en su ícono (al final, cuando ya está todo el HTML) --}}
    <script>
        lucide.createIcons();
    </script>
    {{-- Librería Tom Select --}}
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.7.0/dist/js/tom-select.complete.min.js"></script>

    {{-- Configuración de Tom Select --}}
    <script src="{{ asset('js/tomselect-config.js') }}"></script>
</body>

</html>
