<footer class="footer-rt">
    <div class="container py-5 text-center">

        <h2 class="h3">Reparo<span class="brand-accent">Todo</span></h2>
        <p class="footer-texto mx-auto">Conectamos tu hogar con profesionales de oficios, cerca de vos.</p>

        {{-- Redes sociales (reemplazar los href="#" por los links reales) --}}
        <p class="fw-bold mb-2">Seguimos en redes</p>
        <div class="d-flex justify-content-center gap-3 mb-4">
            <a href="#" class="footer-red" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" class="footer-red" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" class="footer-red" aria-label="X"><i class="bi bi-twitter-x"></i></a>
        </div>

        {{-- Links (los "#" se completan cuando existan las páginas) --}}
        <ul class="list-inline footer-links mb-4">
            <li class="list-inline-item mx-3"><a href="{{ route('inicio') }}#como-funciona">Cómo funciona</a></li>
            <li class="list-inline-item mx-3"><a href="#">Preguntas frecuentes</a></li>
            <li class="list-inline-item mx-3"><a href="#">Términos y condiciones</a></li>
            <li class="list-inline-item mx-3"><a href="#">Contacto</a></li>
        </ul>

        <p class="footer-copy pt-3 mb-0">© 2026 ReparoTodo.com.ar</p>
    </div>
</footer>