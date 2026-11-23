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
                    @auth
                        @if (auth()->user()->tipo === 'profesional' && auth()->user()->profesional)
                            @php $pendientes = auth()->user()->profesional->solicitudes()->whereIn('estado', ['pendiente', 'aceptada'])->count(); @endphp
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('solicitud.*') ? 'active' : '' }}" href="{{ route('solicitud.index') }}">
                                    <i data-lucide="inbox"></i> Mi bandeja
                                    <span class="nav-numero {{ $pendientes === 0 ? 'd-none' : '' }}" id="bandeja-numero">{{ $pendientes }}</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->tipo === 'cliente')
                            @php $porResponder = auth()->user()->solicitudes()->where('estado', 'presupuestada')->count(); @endphp
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('solicitud.cliente.*') ? 'active' : '' }}" href="{{ route('solicitud.cliente.index') }}">
                                    <i data-lucide="inbox"></i> Mis solicitudes
                                    <span class="nav-numero {{ $porResponder === 0 ? 'd-none' : '' }}" id="bandeja-numero">{{ $porResponder }}</span>
                                </a>
                            </li>
                        @endif
                    @endauth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contacto') ? 'active' : '' }}" href="{{ route('contacto') }}">
                            <i data-lucide="mail"></i> Contacto
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
                        {{-- Campanita de notificaciones --}}
                        @php
                            $notificaciones = auth()->user()->notificaciones()->latest()->take(5)->get();
                            $sinLeer = auth()->user()->notificaciones()->where('leida', false)->count();
                            $titulosNotif = [
                                'solicitud_nueva' => 'Nueva solicitud de trabajo.',
                                'solicitud_aceptada' => 'Solicitud aceptada.',
                                'solicitud_rechazada' => 'Solicitud rechazada.',
                                'presupuesto_nuevo' => 'Nuevo presupuesto.',
                                'presupuesto_aceptado' => 'Presupuesto aceptado.',
                                'presupuesto_rechazado' => 'Presupuesto rechazado.',
                            ];
                            // A que solicitud lleva cada tipo. Los rechazos no estan: esa solicitud ya no se ve, y van a la lista
                            $destinos = [
                                'solicitud_nueva' => 'solicitud.show',
                                'presupuesto_aceptado' => 'solicitud.show',
                                'solicitud_aceptada' => 'solicitud.cliente.show',
                                'presupuesto_nuevo' => 'solicitud.cliente.show',
                            ];
                            $lista = auth()->user()->tipo === 'profesional' ? 'solicitud.index' : 'solicitud.cliente.index';
                        @endphp
                        <div class="dropdown">
                            <button class="btn-campana" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notificaciones">
                                <i data-lucide="bell"></i>
                                <span class="campana-numero {{ $sinLeer === 0 ? 'd-none' : '' }}" id="campana-numero">{{ $sinLeer }}</span>
                            </button>
                            <div data-bs-theme="light" class="dropdown-menu dropdown-menu-end menu-notificaciones">
                                <div class="notif-cabecera">
                                    <h6>Notificaciones</h6>
                                    @if ($sinLeer > 0)
                                        <form action="{{ route('notificacion.leer') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="notif-leer">Marcar como leídas</button>
                                        </form>
                                    @endif
                                </div>

                                <div id="aviso-notificaciones" class="alert alert-warning text-center small rounded-0 mb-0 d-none" data-sin-leer="{{ $sinLeer }}">
                                    Hay notificaciones nuevas. <a href="{{ request()->fullUrl() }}" class="alert-link">Actualizar</a>
                                </div>

                                @forelse ($notificaciones as $notificacion)
                                    <a href="{{ isset($destinos[$notificacion->tipo]) && $notificacion->solicitud_id ? route($destinos[$notificacion->tipo], $notificacion->solicitud_id) : route($lista) }}" class="notif-item {{ $notificacion->leida ? '' : 'nueva' }}">
                                        <div class="notif-icono"><i data-lucide="inbox"></i></div>
                                        <div>
                                            <div class="notif-texto"><strong>{{ $titulosNotif[$notificacion->tipo] ?? 'Novedad.' }}</strong> {{ $notificacion->mensaje }}</div>
                                            <div class="notif-hora">{{ $notificacion->created_at->locale('es')->diffForHumans() }}</div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="p-3 text-center">
                                        <i data-lucide="bell-off"></i>
                                        <p class="fw-bold mb-1 mt-2">No tenés notificaciones</p>
                                        <p class="small text-muted mb-0">Cuando haya novedades sobre tus consultas y presupuestos, las vas a ver acá.</p>
                                    </div>
                                @endforelse

                                @if (auth()->user()->tipo === 'profesional')
                                    <a href="{{ route('solicitud.index') }}" class="notif-pie">Ver mi bandeja</a>
                                @endif
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