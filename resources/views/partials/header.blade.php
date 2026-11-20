<header>
    <nav class="navbar navbar-expand-lg navbar-rt sticky-top" data-bs-theme="dark">
        <div class="container">

            {{-- Logo + nombre: siempre lleva al home --}}
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('inicio') }}">
                <img src="{{ asset('img/logo.svg') }}" alt="" height="40">
                <span>Reparo<span class="brand-accent">Todo</span></span>
            </a>

            {{-- Botón de hamburguesa (solo se ve en pantallas chicas) --}}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu-principal" aria-controls="menu-principal" aria-expanded="false" aria-label="Abrir menú">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menu-principal">

                {{-- Links de navegación --}}
                <ul class="navbar-nav mx-lg-auto gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('inicio') ? 'active' : '' }}" href="{{ route('inicio') }}">
                            <i data-lucide="house"></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('profesional.index') ? 'active' : '' }}" href="{{ route('profesional.index') }}">
                            <i data-lucide="search"></i> Buscar profesional
                        </a>
                    </li>
                    {{-- Pendientes (se agregan cuando existan las páginas):
                         Rubros, Ofrecer servicios, Preguntas frecuentes, Contacto --}}
                </ul>

                {{-- Zona derecha: cambia según haya sesión o no --}}
                <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">

                    {{-- SIN LOGIN --}}
                    @guest
                        <a class="btn btn-accent fw-semibold" href="{{ route('usuario.create') }}">
                            <i data-lucide="user-plus"></i> Crear cuenta
                        </a>
                        <a class="btn btn-blanco fw-semibold" href="{{ route('usuario.registro') }}">
                            <i data-lucide="log-in"></i> Iniciar sesión
                        </a>
                    @endguest

                    {{-- CON LOGIN --}}
                    @auth
                        {{-- Campanita de notificaciones (por ahora siempre vacía) --}}
                        <div class="dropdown">
                            <button class="btn-campana" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notificaciones">
                                <i data-lucide="bell"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end menu-notificaciones p-3 text-center">
                                <h6 class="text-start mb-3">Notificaciones</h6>
                                <i data-lucide="bell-off"></i>
                                <p class="fw-bold mb-1 mt-2">No tenés notificaciones</p>
                                <p class="small text-muted mb-0">Cuando haya novedades sobre tus consultas y presupuestos, las vas a ver acá.</p>
                            </div>
                        </div>

                        {{-- "Mi perfil" solo para profesionales --}}
                        @if (auth()->user()->tipo === 'profesional' && auth()->user()->profesional)
                            <a class="btn btn-outline-light" href="{{ route('profesional.show', auth()->user()->profesional->id) }}">Mi perfil</a>
                        @endif

                        <a class="btn btn-accent" href="{{ route('usuario.salir') }}">
                            <i data-lucide="log-out"></i> Cerrar sesión
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>