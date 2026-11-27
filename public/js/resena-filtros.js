document.addEventListener('DOMContentLoaded', function () {

    const panel = document.getElementById('panel-resenas');

    if (!panel) {
        return;
    }

    const botones = panel.querySelectorAll('[data-filtro-puntaje]');
    const resenas = panel.querySelectorAll('[data-resena-puntaje]');
    const sinResultados = panel.querySelector('#resenas-sin-resultados');

    botones.forEach(function (boton) {

        boton.addEventListener('click', function () {

            const puntajeSeleccionado = boton.dataset.filtroPuntaje;

            let cantidadVisible = 0;

            // Actualizar el estado de los botones.
            botones.forEach(function (otroBoton) {

                const activo = otroBoton === boton;

                otroBoton.classList.toggle('activo', activo);
                otroBoton.setAttribute('aria-pressed', String(activo));

            });

            // Mostrar únicamente las reseñas correspondientes.
            resenas.forEach(function (resena) {

                const coincide =
                    puntajeSeleccionado === 'todas'
                    || resena.dataset.resenaPuntaje === puntajeSeleccionado;

                resena.classList.toggle('d-none', !coincide);

                if (coincide) {
                    cantidadVisible++;
                }

            });

            // Informar cuando no existen coincidencias.
            if (sinResultados) {

                sinResultados.classList.toggle(
                    'd-none',
                    cantidadVisible > 0
                );

            }

        });

    });

});