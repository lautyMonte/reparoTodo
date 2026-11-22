document.addEventListener("DOMContentLoaded", () => {

    const secciones = document.querySelectorAll(".terminos-contenido section");
    const enlaces = document.querySelectorAll(".terminos-menu a");

    function actualizarMenu() {
        let seccionActual = secciones[0]?.id;

        secciones.forEach(seccion => {
            const posicion = seccion.getBoundingClientRect().top;

            if (posicion <= 160) {
                seccionActual = seccion.id;
            }
        });

        enlaces.forEach(enlace => {
            const activo = enlace.getAttribute("href") === `#${seccionActual}`;

            enlace.classList.toggle("activo", activo);

            if (activo) {
                enlace.setAttribute("aria-current", "location");
            } else {
                enlace.removeAttribute("aria-current");
            }
        });
    }

    window.addEventListener("scroll", actualizarMenu, { passive: true });

    actualizarMenu();
});
