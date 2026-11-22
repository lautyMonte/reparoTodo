<footer class="footer-rt">
    <div class="container py-5">
        <div class="row g-4">

            {{-- Columna 1: marca y redes --}}
            <div class="col-lg-5">
                <a class="footer-marca d-inline-flex align-items-center gap-2 mb-3" href="{{ route('inicio') }}">
                    <img src="{{ asset('img/logo.svg') }}" alt="" height="48">
                    <span>Reparo<span class="brand-accent">Todo</span></span>
                </a>
                <p class="footer-texto">Conectamos tu hogar con profesionales de oficios, cerca de vos.</p>

                <h3 class="footer-titulo">Seguinos en redes</h3>
                {{-- Reemplazar los href="#" por los links reales --}}
                <div class="d-flex gap-2">
                    <a href="#" class="footer-red" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="footer-red" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="footer-red" aria-label="X"><i class="bi bi-twitter-x"></i></a>
                </div>
            </div>

            {{-- Columna 2: navegacion --}}
            <div class="col-6 col-lg-3 offset-lg-1">
                <h3 class="footer-titulo">Navegación</h3>
                <ul class="list-unstyled footer-links mb-0">
                    <li><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li><a href="{{ route('profesional.index') }}">Buscar profesional</a></li>
                    <li><a href="{{ route('inicio') }}#como-funciona">Cómo funciona</a></li>
                </ul>
            </div>

            {{-- Columna 3: ayuda (los "#" se completan cuando existan las paginas) --}}
            <div class="col-6 col-lg-3">
                <h3 class="footer-titulo">Ayuda</h3>
                <ul class="list-unstyled footer-links mb-0">
                    <li><a href="#">Preguntas frecuentes</a></li>
                    <li><a href="#">Términos y condiciones</a></li>
                    <li><a href="{{ route('contacto') }}">Contacto</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="footer-copy text-center py-3">© 2026 ReparoTodo.com.ar</div>
</footer>