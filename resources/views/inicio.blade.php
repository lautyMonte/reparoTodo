@extends('layouts.app', ['titulo' => $titulo])

@section('contenido')

    {{-- ========== 1. CARRUSEL (2 diapositivas: clientes y profesionales) ========== --}}
    <div id="carrusel-inicio" class="carousel slide carrusel-rt mb-5" data-bs-ride="carousel">

        {{-- Puntitos de abajo --}}
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carrusel-inicio" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Para clientes"></button>
            <button type="button" data-bs-target="#carrusel-inicio" data-bs-slide-to="1" aria-label="Para profesionales"></button>
        </div>

        <div class="carousel-inner">

            {{-- Diapositiva 1: cliente --}}
            <div class="carousel-item active">
                <div class="slide-rt" style="background-image: url('{{ asset('img/home/slide-cliente.jpg') }}')">
                    <div class="slide-contenido">
                        <h1>¿Qué necesitás reparar hoy?</h1>
                        <a href="{{ route('profesional.listado') }}" class="btn btn-accent btn-lg fw-bold px-5">Busco profesional</a>
                    </div>
                </div>
            </div>

            {{-- Diapositiva 2: profesional --}}
            <div class="carousel-item">
                <div class="slide-rt" style="background-image: url('{{ asset('img/home/slide-profesional.jpg') }}')">
                    <div class="slide-contenido">
                        <h1>Conseguí clientes cerca tuyo</h1>

                        @guest
                            {{-- Sin sesión: lo mandamos a iniciar sesión --}}
                            <a href="{{ route('usuario.registro') }}" class="btn btn-accent btn-lg fw-bold px-5">Ofrecer servicios</a>
                        @else
                            {{-- Con sesión: pendiente hasta que exista la pantalla "Ofrecer servicios" --}}
                            <a href="#" class="btn btn-accent btn-lg fw-bold px-5">Ofrecer servicios</a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>

        {{-- Flechas anterior / siguiente --}}
        <button class="carousel-control-prev" type="button" data-bs-target="#carrusel-inicio" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carrusel-inicio" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>


    {{-- ========== 2. PARA CLIENTES ========== --}}
    <section class="bloque-home">
        <h2 class="titulo-seccion">¿Por qué elegirnos?</h2>

        <div class="row g-3 mb-5">
            <div class="col-md-4">
                <div class="card tarjeta-beneficio h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="icono-circulo"><i data-lucide="hand-coins"></i></span>
                        <div>
                            <h3 class="h6 mb-1">Gratis para vos</h3>
                            <p class="small text-muted mb-0">Consultá y pedí presupuestos sin costo.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card tarjeta-beneficio h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="icono-circulo"><i data-lucide="badge-percent"></i></span>
                        <div>
                            <h3 class="h6 mb-1">Sin comisiones</h3>
                            <p class="small text-muted mb-0">Acordás el pago directo con el profesional.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card tarjeta-beneficio h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="icono-circulo"><i data-lucide="shield-check"></i></span>
                        <h3 class="h6 mb-0">Profesionales verificados</h3>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="titulo-seccion" id="como-funciona">¿Cómo funciona?</h2>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="card tarjeta-paso h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="numero-paso">1</span>
                        <span class="fw-bold">Elegí el oficio</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card tarjeta-paso h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="numero-paso">2</span>
                        <span class="fw-bold">Elegí al profesional</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card tarjeta-paso h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="numero-paso">3</span>
                        <span class="fw-bold">Contactalo y pedí tu presupuesto</span>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ========== 3. PARA PROFESIONALES ========== --}}
    <section class="bloque-home">
        <h2 class="titulo-seccion">¿Por qué sumarte?</h2>

        <div class="row g-3 mb-5">
            <div class="col-md-4">
                <div class="card tarjeta-beneficio h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="icono-circulo"><i data-lucide="user-plus"></i></span>
                        <div>
                            <h3 class="h6 mb-1">Registro gratuito</h3>
                            <p class="small text-muted mb-0">Creá tu perfil sin costo.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card tarjeta-beneficio h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="icono-circulo"><i data-lucide="badge-percent"></i></span>
                        <div>
                            <h3 class="h6 mb-1">Sin comisiones</h3>
                            <p class="small text-muted mb-0">Te quedás con todo lo que cobrás.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card tarjeta-beneficio h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="icono-circulo"><i data-lucide="map-pin"></i></span>
                        <h3 class="h6 mb-0">Clientes de tu zona</h3>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="titulo-seccion">Empezá en 3 pasos</h2>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="card tarjeta-paso h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="numero-paso">1</span>
                        <span class="fw-bold">Creá tu cuenta</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card tarjeta-paso h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="numero-paso">2</span>
                        <span class="fw-bold">Completá tu perfil</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card tarjeta-paso h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="numero-paso">3</span>
                        <span class="fw-bold">Recibí pedidos de clientes</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection