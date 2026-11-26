@extends('layouts.app', ['titulo' => 'Términos y condiciones'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/tyc.css') }}">
@endpush

@section('contenido')
    {{-- Titulo --}}
    <h1>Términos y Condiciones</h1>
    <p class="terminos-descripcion">Bienvenido a ReparoTodo. Antes de utilizar nuestra plataforma, te invitamos a conocer las
        condiciones generales de
        funcionamiento y las responsabilidades de quienes forman parte de nuestra comunidad.</p>
    <div class="terminos-contenedor">
        {{-- Menu Izquierda --}}
        <nav class="terminos-menu" aria-label="Secciones de términos">
            <a href="#introduccion">01. Introducción</a>
            <a href="#registro">02. Registro y cuentas</a>
            <a href="#uso">03. Uso de la plataforma</a>
            <a href="#responsabilidades">04. Responsabilidades</a>
            <a href="#pagos">05. Pagos y contrataciones</a>
            <a href="#privacidad">06. Privacidad y datos personales</a>
            <a href="#modificaciones">07. Modificaciones</a>
            <a href="#contacto">08. Contacto</a>
        </nav>
        {{-- Contenido --}}
        <article class="terminos-contenido">
            {{-- Introduccion --}}
            <section id="introduccion">
                <h2>01. Introducción</h2>
                <p>
                    ReparoTodo es una plataforma digital que tiene como objetivo conectar a personas que necesitan realizar
                    reparaciones, mantenimiento u otros trabajos con profesionales de diferentes oficios.
                </p>
                <p>
                    A través de nuestra plataforma, los usuarios pueden buscar profesionales, consultar sus perfiles y
                    encontrar servicios que se adapten a sus necesidades.
                </p>
                <p>
                    ReparoTodo funciona como intermediario entre clientes y profesionales. No realiza reparaciones ni presta
                    directamente los servicios publicados.
                </p>
                <p>
                    Al utilizar la plataforma, los usuarios aceptan las condiciones de funcionamiento descritas en este
                    documento.
                </p>
            </section>
            {{-- Registro & Cuentas --}}
            <section id="registro">
                <h2>02. Registro y cuentas</h2>
                <p>
                    Para explorar los servicios disponibles, buscar profesionales y consultar sus perfiles, no es necesario
                    <a href="{{ route('usuario.create') }}">crear una cuenta</a>.
                <p>
                    Sin embargo, para contactar a un profesional, los usuarios deberán <a
                        href="{{ route('usuario.create') }}">registrarse</a> en la plataforma.
                <p>
                    Los profesionales que deseen ofrecer sus servicios también deberán crear una cuenta y completar su
                    perfil con la información solicitada.
                </p>
                <p>
                    Al registrarse, cada usuario se compromete a:
                </p>
                <ul>
                    <li>Proporcionar información verdadera y actualizada.</li>
                    <li>Mantener la confidencialidad de sus datos de acceso.</li>
                    <li>Utilizar su cuenta de manera responsable.</li>
                    <li>No hacerse pasar por otras personas ni crear perfiles falsos.</li>
                </ul>
                <p>
                    Cada usuario es responsable de las actividades realizadas desde su cuenta.
                </p>
            </section>
            {{-- Uso Plataforma --}}
            <section id="uso">
                <h2>03. Uso de la plataforma</h2>
                <p>
                    ReparoTodo permite a los usuarios encontrar profesionales de distintos rubros, como plomería,
                    electricidad, gas, albañilería, pintura, cerrajería, fletes y otros servicios.
                </p>
                <p>
                    Los clientes pueden explorar los perfiles profesionales, consultar la información disponible y
                    establecer contacto con quienes ofrezcan los servicios que necesitan.
                </p>
                <p>
                    Por su parte, los profesionales pueden publicar información sobre sus actividades, especialidades,
                    experiencia y zona de trabajo.
                </p>
                <p>
                    Para mantener un entorno seguro y confiable, no está permitido:
                </p>
                <ul>
                    <li>Publicar información falsa o engañosa.</li>
                    <li>Utilizar la plataforma para realizar actividades ilegales.</li>
                    <li>Suplantar la identidad de otros usuarios.</li>
                    <li>Publicar contenido ofensivo, discriminatorio o inapropiado.</li>
                    <li>Utilizar los servicios de ReparoTodo para enviar mensajes no deseados o realizar publicidad no
                        autorizada.
                    </li>
                </ul>
                <p>
                    ReparoTodo podrá limitar el acceso a las cuentas que incumplan estas condiciones.
                </p>
            </section>
            {{-- Responsabilidades --}}
            <section id="responsabilidades">
                <h2>04. Responsabilidades</h2>
                <p>
                    ReparoTodo tiene como finalidad facilitar el contacto entre clientes y profesionales, pero no participa
                    directamente en la realización de los trabajos contratados.
                </p>
                <p>
                    Los profesionales son responsables de la información que publican en sus perfiles, de los servicios que
                    ofrecen y del cumplimiento de los acuerdos establecidos con sus clientes.
                </p>
                <p>
                    Los clientes, por su parte, son responsables de evaluar las opciones disponibles y acordar las
                    condiciones del servicio con el profesional seleccionado.
                </p>
                <p>
                    ReparoTodo no garantiza la calidad, los resultados ni los plazos de ejecución de los trabajos realizados
                    por terceros.
                </p>
                <p>
                    Recomendamos que ambas partes acuerden previamente los detalles del trabajo, los materiales necesarios,
                    el presupuesto y los tiempos estimados de realización.
                </p>
            </section>
            {{-- Pagos & Contrataciones --}}
            <section id="pagos">
                <h2>05. Pagos y contrataciones</h2>
                <p>
                    El uso de ReparoTodo es gratuito tanto para quienes buscan profesionales como para quienes desean
                    ofrecer sus servicios.
                </p>
                <p>
                    Los presupuestos, precios, formas de pago y condiciones de contratación se acuerdan directamente entre
                    el cliente y el profesional.
                </p>
                <p>
                    ReparoTodo no interviene en estas transacciones ni establece los precios de los servicios publicados.
                </p>
                <p>
                    Los profesionales también pueden optar por contratar espacios publicitarios dentro de la plataforma para
                    aumentar la visibilidad de sus perfiles y servicios.
                </p>
                <p>
                    Esta publicidad es opcional y no modifica las condiciones generales de utilización de ReparoTodo.
                </p>
            </section>
            {{-- Privacidad --}}
            <section id="privacidad">
                <h2>06. Privacidad y datos personales</h2>
                <p>
                    En ReparoTodo consideramos importante proteger la información personal de nuestros usuarios.
                </p>
                <p>
                    Los datos proporcionados durante el registro se utilizan para administrar las cuentas, facilitar el
                    funcionamiento de la plataforma y permitir la comunicación entre clientes y profesionales.
                </p>
                <p>
                    Parte de la información de los profesionales, como su nombre, especialidad, experiencia y zona de
                    trabajo, podrá mostrarse públicamente en sus perfiles.
                </p>
                <p>
                    Otros datos solicitados para la identificación y verificación de usuarios se mantendrán privados y no
                    serán visibles para el resto de la comunidad.
                </p>
                <p>
                    Los usuarios deberán proporcionar información correcta y evitar compartir datos personales sensibles
                    mediante publicaciones o mensajes públicos.
                </p>
            </section>
            {{-- Modificaciones --}}
            <section id="modificaciones">
                <h2>07. Modificaciones de los términos</h2>
                <p>
                    ReparoTodo podrá actualizar estos Términos y Condiciones cuando sea necesario incorporar nuevas
                    funcionalidades, mejorar el funcionamiento de la plataforma o modificar sus servicios.
                </p>
                <p>
                    Las modificaciones se reflejarán en esta sección para que los usuarios puedan consultar las condiciones
                    vigentes.
                </p>
                <p>
                    Recomendamos revisar periódicamente esta información para mantenerse al tanto de posibles cambios.
                </p>
                <p>
                    El uso continuado de la plataforma después de la publicación de las modificaciones estará sujeto a las
                    condiciones actualizadas.
                </p>
            </section>
            {{-- Contacto --}}
            <section id="contacto">
                <h2>08. Contacto</h2>
                <p>
                    Si tenés preguntas sobre estos Términos y Condiciones, necesitás ayuda con tu cuenta o querés informar
                    algún inconveniente relacionado con el funcionamiento de ReparoTodo, podés comunicarte con nuestro
                    equipo.
                </p>
                <p>
                    Para hacerlo, ingresá a la sección <a href="{{ route('contacto') }}">Contacto</a>, completá el
                    formulario con tus datos y describí tu
                    consulta.
                </p>
                <p>
                    Nuestro equipo revisará tu mensaje y te responderá a la brevedad.
                </p>
                <p>
                    En ReparoTodo buscamos facilitar el encuentro entre personas que necesitan soluciones y profesionales
                    que pueden ofrecerlas, promoviendo una comunidad basada en el respeto, la responsabilidad y la
                    confianza.
                </p>
            </section>
        </article>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/tyc.js') }}" defer></script>
@endpush
