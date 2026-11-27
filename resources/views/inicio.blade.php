@extends('layouts.app', ['titulo' => $titulo])

@section('contenido')

    {{-- Boton "Ofrecer servicios": sin sesion va al login; el profesional va a su perfil; el cliente ve un aviso --}}
    @php
        $usuarioActual = auth()->user();
        $esCliente = $usuarioActual && $usuarioActual->tipo === 'cliente';

        if (! $usuarioActual) {
            $urlOfrecer = route('usuario.registro');
        } elseif ($usuarioActual->profesional) {
            $urlOfrecer = route('profesional.show', $usuarioActual->profesional->id);
        } else {
            $urlOfrecer = '#';
        }
    @endphp

    {{-- ========== 1. CARRUSEL PRINCIPAL ========== --}}
    <div id="carrusel-inicio" class="carousel slide carrusel-rt mb-5" data-bs-ride="carousel">

        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carrusel-inicio" data-bs-slide-to="0" class="active" aria-current="true"
                aria-label="Para clientes"></button>
            <button type="button" data-bs-target="#carrusel-inicio" data-bs-slide-to="1"
                aria-label="Para profesionales"></button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="slide-rt" style="background-image: url('{{ asset('img/home/slide-cliente.avif') }}')">
                    <div class="slide-contenido">
                        <h1>¿Qué necesitás reparar hoy?</h1>
                        <a href="{{ route('oficio.index') }}" class="btn btn-accent btn-lg fw-bold px-5">Busco
                            profesional</a>
                    </div>
                </div>
            </div>

            <div class="carousel-item">
                <div class="slide-rt" style="background-image: url('{{ asset('img/home/slide-profesional.avif') }}')">
                    <div class="slide-contenido">
                        <h1>Conseguí clientes cerca tuyo</h1>
                        <a href="{{ $urlOfrecer }}" @if ($esCliente) data-bs-toggle="modal" data-bs-target="#modalOfrecer" @endif class="btn btn-accent btn-lg fw-bold px-5">Ofrecer servicios</a>
                    </div>
                </div>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carrusel-inicio" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carrusel-inicio" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>


    {{-- ========== 2. POR QUE ELEGIRNOS (clientes y profesionales juntos) ========== --}}
    <section class="bloque-home">
        <h2 class="titulo-seccion">¿Por qué elegirnos?</h2>

        <div class="row g-3">
            <div class="col-md-6 col-lg-3">
                <div class="card tarjeta-beneficio h-100">
                    <div class="card-body">
                        <span class="icono-circulo mb-3"><i data-lucide="hand-coins"></i></span>
                        <h3 class="h6">Gratis para todos</h3>
                        <p class="small text-muted mb-0">Registrate, consultá y recibí presupuestos sin costo.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card tarjeta-beneficio h-100">
                    <div class="card-body">
                        <span class="icono-circulo mb-3"><i data-lucide="badge-percent"></i></span>
                        <h3 class="h6">Sin comisiones</h3>
                        <p class="small text-muted mb-0">El pago se acuerda directo entre cliente y profesional, sin
                            intermediarios.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card tarjeta-beneficio h-100">
                    <div class="card-body">
                        <span class="icono-circulo mb-3"><i data-lucide="map-pin"></i></span>
                        <h3 class="h6">Cerca de vos</h3>
                        <p class="small text-muted mb-0">Conectamos clientes con profesionales de su misma zona.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card tarjeta-beneficio h-100">
                    <div class="card-body">
                        <span class="icono-circulo mb-3"><i data-lucide="shield-check"></i></span>
                        <h3 class="h6">Profesionales verificados</h3>
                        <p class="small text-muted mb-0">Perfiles claros, con oficios, zona y reseñas de otros clientes.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ========== 3. COMO FUNCIONA (los 3 pasos sirven para los dos) ========== --}}
    <section class="bloque-home">
        <h2 class="titulo-seccion" id="como-funciona">¿Cómo funciona?</h2>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="card tarjeta-paso h-100">
                    <div class="card-body d-flex gap-3">
                        <span class="numero-paso">1</span>
                        <div>
                            <h3 class="h6 mb-1">Creá tu cuenta</h3>
                            <p class="small text-muted mb-0">Registrate gratis, como cliente o como profesional.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card tarjeta-paso h-100">
                    <div class="card-body d-flex gap-3">
                        <span class="numero-paso">2</span>
                        <div>
                            <h3 class="h6 mb-1">Elegí o completá</h3>
                            <p class="small mb-1"><strong>Cliente:</strong> elegí el oficio y al profesional.</p>
                            <p class="small mb-0"><strong>Profesional:</strong> completá tu perfil con tus oficios y tu
                                zona.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card tarjeta-paso h-100">
                    <div class="card-body d-flex gap-3">
                        <span class="numero-paso">3</span>
                        <div>
                            <h3 class="h6 mb-1">Conectate</h3>
                            <p class="small mb-1"><strong>Cliente:</strong> contactá al profesional y recibí su presupuesto.
                            </p>
                            <p class="small mb-0"><strong>Profesional:</strong> recibí pedidos y enviale tu presupuesto al
                                cliente.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ========== 4. PROFESIONALES DESTACADOS ========== --}}
    <section class="bloque-home">
        <h2 class="titulo-seccion">Profesionales destacados</h2>

        <div class="row g-3">
            @forelse ($destacados as $destacado)
                <div class="col-md-4">
                    <div class="card tarjeta-profesional h-100">
                        <div class="card-body text-center p-4">
                            <span class="etiqueta-destacado mb-3"><i data-lucide="star"></i> Destacado</span>

                                <img
                                    class="avatar-rt mx-auto mb-2"
                                    src="{{ $destacado->user->avatar() }}"
                                    alt="Foto de {{ $destacado->user->nombreCompleto() }}"
                                >

                            <h3 class="h5 mb-1">
                                <a href="{{ route('profesional.show', $destacado->id) }}"
                                    class="stretched-link text-reset text-decoration-none">{{ $destacado->user->nombreCompleto() }}</a>
                            </h3>

                            <p class="estrellas small mb-2">
                                <i data-lucide="star"></i>
                                <strong>{{ number_format($destacado->resenas_avg_puntaje, 1, ',', '.') }}</strong>
                                <span class="text-muted">({{ $destacado->resenas_count }} valoraciones)</span>
                            </p>

                            <div class="mb-3">
                                @foreach ($destacado->oficios as $oficio)
                                    <span class="chip-oficio">{{ $oficio->nombreParaGenero($destacado->user->genero) }}</span>
                                @endforeach
                            </div>

                            <p class="small text-muted mb-0">
                                <i data-lucide="map-pin"></i>
                                {{ $destacado->ciudades->pluck('nombre')->join(', ') }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted">Todavía no hay profesionales con reseñas.</p>
            @endforelse
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('profesional.index') }}" class="btn btn-primary fw-semibold px-4">Ver más profesionales</a>
        </div>
    </section>


    {{-- ========== 5. ASI TE VEN LOS CLIENTES ========== --}}
    <section class="bloque-home bloque-perfil">
        <div class="row align-items-center g-4">

            <div class="col-lg-6">
                <h2>Así te ven los clientes</h2>
                <p class="mb-4">Tu perfil es tu vidriera: mostrá lo que sabés hacer y dónde trabajás para que te
                    encuentren y te contacten.</p>

                <ul class="list-unstyled lista-check mb-4">
                    <li><span class="check-rt"><i data-lucide="check"></i></span> Tu nombre, tu foto y tus oficios</li>
                    <li><span class="check-rt"><i data-lucide="check"></i></span> Tu zona de trabajo</li>
                    <li><span class="check-rt"><i data-lucide="check"></i></span> Las reseñas de tus clientes</li>
                    <li><span class="check-rt"><i data-lucide="check"></i></span> Un botón para que te contacten directo
                    </li>
                </ul>

                <a href="{{ $urlOfrecer }}" @if ($esCliente) data-bs-toggle="modal" data-bs-target="#modalOfrecer" @endif class="btn btn-accent btn-lg fw-bold px-5">Ofrecer servicios</a>
            </div>

            <div class="col-lg-5 offset-lg-1">
                <div class="card tarjeta-profesional">
                    <div class="card-body text-center p-4">
                        <small class="d-block text-secondary mb-3">
    Ejemplo de perfil profesional
</small>
                        <img
    class="avatar-rt mx-auto mb-2"
    src="{{ asset('img/avatares/m/avatar-m-5.svg') }}"
    alt="Avatar de ejemplo"
>
                        <h3 class="h5 mb-1">Matias Jañez</h3>

                        <p class="estrellas small mb-2">
                            <i data-lucide="star"></i> <strong>4,5</strong>
                            <span class="text-muted">(24 valoraciones)</span>
                        </p>

                        <div class="mb-3">
                            <span class="chip-oficio">Electricista</span>
                            <span class="chip-oficio">Plomero</span>
                            <span class="chip-oficio">Cerrajero</span>
                        </div>

                        <p class="small text-muted mb-1"><i data-lucide="map-pin"></i> Cipolletti</p>
                        <p class="small text-body mb-3">Instalaciones eléctricas, reparaciones de plomería y cambio de
                            cerraduras en toda la zona.</p>

                        <div class="btn btn-accent w-100 fw-bold pe-none">Contactar</div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ========== 6. CARRUSEL FINAL (mas chico) ========== --}}
    <div id="carrusel-cta" class="carousel slide carrusel-cta" data-bs-ride="carousel" data-bs-interval="6000">

        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carrusel-cta" data-bs-slide-to="0" class="active"
                aria-current="true" aria-label="Buscar profesional"></button>
            <button type="button" data-bs-target="#carrusel-cta" data-bs-slide-to="1"
                aria-label="Ofrecer servicios"></button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="slide-cta">
                    <h2>¿Listo para empezar?</h2>
                    <a href="{{ route('oficio.index') }}" class="btn btn-accent fw-bold px-5">Buscar profesional</a>
                </div>
            </div>
            <div class="carousel-item">
                <div class="slide-cta">
                    <h2>Sumate a ReparoTodo</h2>
                    <a href="{{ $urlOfrecer }}" @if ($esCliente) data-bs-toggle="modal" data-bs-target="#modalOfrecer" @endif class="btn btn-accent fw-bold px-5">Ofrecer servicios</a>
                </div>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carrusel-cta" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carrusel-cta" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>

    {{-- Aviso para el cliente que toca "Ofrecer servicios" --}}
    @if ($esCliente)
        <div class="modal fade" id="modalOfrecer" tabindex="-1" aria-labelledby="modalOfrecerLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center p-4">
                    <div class="modal-body">
                        <i data-lucide="briefcase" class="icn-aviso mb-3"></i>
                        <h4 id="modalOfrecerLabel">Necesitás una cuenta de profesional</h4>
                        <p>Ahora estás usando ReparoTodo como cliente. Para ofrecer tus servicios, cerrá sesión y luego ingresá o registrate como profesional.</p>
                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <button type="button" class="btn btn-cancelar" data-bs-dismiss="modal">Volver</button>
                            <a href="{{ route('usuario.salir') }}" class="btn btn-accent"><i data-lucide="log-out"></i> Cerrar sesión</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection