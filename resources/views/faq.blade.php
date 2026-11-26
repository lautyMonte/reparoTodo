@extends('layouts.app', ['titulo' => 'Preguntas frecuentes'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/faq.css') }}">
@endpush

@section('contenido')

    {{-- Enlace "Crear Cuenta": si esta logeado muestra modal de error --}}
    @php
        $usuarioActual = auth()->user();
        $estaLogeado = $usuarioActual ?? null;

        if ($usuarioActual == null) {
            $urlOfrecer = route('usuario.registro');
        } else {
            $urlOfrecer = '#';
        }
    @endphp

    <div class="contenedor-tit">
        <h1>Preguntas Frecuentes</h1>
        <p>Encontrá respuestas a las dudas más comunes sobre ReparoTodo.</p>
    </div>
    <div class="accordion acordeon contenedor-faq" id="accordionFAQ">
        <!-- Pregunta 1 -->
        <div class="accordion-item acordeon-item">
            <h2 class="accordion-header">
                <button class="accordion-button acordeon-btn collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#pregunta1">
                    ¿Qué es ReparoTodo y cómo funciona?
                </button>
            </h2>

            <div id="pregunta1" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                <div class="accordion-body acordeon-body">
                    ReparoTodo es una plataforma digital que conecta personas que necesitan realizar reparaciones,
                    mantenimiento u otros trabajos con profesionales de diferentes oficios.
                </div>
            </div>
        </div>

        <!-- Pregunta 2 -->
        <div class="accordion-item acordeon-item">
            <h2 class="accordion-header">
                <button class="accordion-button acordeon-btn collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#pregunta2">
                    ¿Necesito registrarme para utilizar la plataforma?
                </button>
            </h2>

            <div id="pregunta2" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                <div class="accordion-body acordeon-body">
                    No necesitás registrarte para explorar la plataforma, buscar profesionales o consultar sus
                    perfiles.<br>
                    Solo necesitás crear una cuenta cuando quieras contactar a un profesional.<br>
                    Si sos profesional y querés ofrecer tus servicios en ReparoTodo, sí es necesario registrarte y crear tu
                    perfil profesional.
                </div>
            </div>
        </div>

        <!-- Pregunta 3 -->
        <div class="accordion-item acordeon-item">
            <h2 class="accordion-header">
                <button class="accordion-button acordeon-btn collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#pregunta3">
                    ¿Utilizar ReparoTodo tiene algún costo?
                </button>
            </h2>

            <div id="pregunta3" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                <div class="accordion-body acordeon-body">
                    No. ReparoTodo es totalmente gratuito, tanto para quienes buscan profesionales como para quienes desean
                    ofrecer sus servicios.<br>
                    Los profesionales también pueden optar por contratar publicidad para aumentar la visibilidad de sus
                    servicios dentro de la plataforma.
                </div>
            </div>
        </div>

        <!-- Pregunta 4 -->
        <div class="accordion-item acordeon-item">
            <h2 class="accordion-header">
                <button class="accordion-button acordeon-btn collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#pregunta4">
                    ¿ReparoTodo realiza las reparaciones?
                </button>
            </h2>

            <div id="pregunta4" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                <div class="accordion-body acordeon-body">
                    ReparoTodo no realiza reparaciones ni presta servicios directamente.<br>
                    Nuestra función es conectar a personas que necesitan solucionar un problema con profesionales que
                    ofrecen sus servicios.<br>
                    Los presupuestos, las condiciones del trabajo y los pagos se acuerdan directamente entre cliente y
                    profesional.
                </div>
            </div>
        </div>

        <!-- Pregunta 5 -->
        <div class="accordion-item acordeon-item">
            <h2 class="accordion-header">
                <button class="accordion-button acordeon-btn collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#pregunta5">
                    ¿Cómo puedo registrarme como profesional en ReparoTodo?
                </button>
            </h2>

            <div id="pregunta5" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                <div class="accordion-body acordeon-body">
                    Ingresá en <a href="{{ $urlOfrecer }}" class="enlace" @if ($estaLogeado) data-bs-toggle="modal" data-bs-target="#modalCrearCuenta" @endif class="btn btn-accent btn-lg fw-bold px-5">Crear Cuenta</a> y completa los campos
                    con la información solicitada.<br>
                    Luego en Agregar Servicios podes optar por añadir servicios adicionales que realices.<br>
                    Una vez registrado ya podes comenzar a ofrecer tus servicios.
                </div>
            </div>
        </div>

        <!-- Pregunta 6 -->
        <div class="accordion-item acordeon-item">
            <h2 class="accordion-header">
                <button class="accordion-button acordeon-btn collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#pregunta6">
                    ¿Qué información necesito para crear mi perfil profesional?
                </button>
            </h2>

            <div id="pregunta6" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                <div class="accordion-body acordeon-body">
                    Solicitamos información que permita identificar al profesional y conocer los servicios que ofrece.
                    Esta información incluye:<br>
                    <ul>
                        <li>Nombre y apellido</li>
                        <li>Datos de contacto</li>
                        <li>Localidad y zona de trabajo</li>
                        <li>Oficios y especialidades</li>
                        <li>Experiencia laboral y certificaciones</li>
                        <li>Información adicional para verificar la identidad</li>
                    </ul>
                    Algunos datos son pura y exclusivamente para verificar al usuario, por lo que no serán visibles para
                    otros usuarios.
                </div>
            </div>
        </div>

        <!-- Pregunta 7 -->
        <div class="accordion-item acordeon-item">
            <h2 class="accordion-header">
                <button class="accordion-button acordeon-btn collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#pregunta7">
                    ¿Cómo puedo comunicarme con el soporte de ReparoTodo?
                </button>
            </h2>

            <div id="pregunta7" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                <div class="accordion-body acordeon-body">
                    Si tenés alguna consulta, necesitás ayuda con tu cuenta o querés informar un inconveniente, podés
                    comunicarte con nuestro equipo a través de la sección <a href="{{ route('contacto') }}"
                        class="enlace">Contacto</a> de la plataforma.<br>
                    Completá el formulario con tus datos y una descripción de tu consulta para que podamos ayudarte.
                </div>
            </div>
        </div>
    </div>
    <div class="contenedor-norta">
        <h3>
            ¿No encontraste tu respuesta?
        </h3>
        <p>Escribinos y te responderemos a la brevedad</p>
        <a href="{{ route('contacto') }}" class="btn btn-faq">Contacto</a>
    </div>
    @if ($estaLogeado != null)
        <div class="modal fade" id="modalCrearCuenta" tabindex="-1" aria-labelledby="modalCrearCuentaLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center p-4">
                    <div class="modal-body">
                        <i data-lucide="triangle-exclamation-point" class="icn-aviso mb-3"></i>
                        <h4 id="modalCrearCuentaLabel">No podes crear una cuenta si estas logeado</h4>
                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <button type="button" class="btn btn-cancelar" data-bs-dismiss="modal">Volver</button>
                            <a href="{{ route('usuario.salir') }}" class="btn btn-accent"><i data-lucide="log-out"></i>
                                Cerrar sesión</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
