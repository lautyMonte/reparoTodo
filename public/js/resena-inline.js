document.addEventListener('DOMContentLoaded', function () {

    const componentes = document.querySelectorAll('[data-resena-inline]');

    componentes.forEach(function (componente) {

        const lectura = componente.querySelector('[data-resena-lectura]');
        const formulario = componente.querySelector('[data-resena-edicion]');
        const btnEditar = componente.querySelector('[data-resena-editar]');
        const btnCancelar = componente.querySelector('[data-resena-cancelar]');

        if (!lectura || !formulario || !btnEditar || !btnCancelar) {
            return;
        }

        function activarEdicion() {

            lectura.classList.add('d-none');
            btnEditar.classList.add('d-none');

            formulario.classList.remove('d-none');
        }

        function cancelarEdicion() {

            const puntajeOriginal = formulario.dataset.puntaje;

            formulario.querySelectorAll('input[name="puntaje"]')
                .forEach(function (estrella) {
                    estrella.checked = estrella.value === puntajeOriginal;
                });

            formulario.querySelector('textarea[name="comentario"]').value =
                formulario.dataset.comentario;

            formulario.classList.add('d-none');

            lectura.classList.remove('d-none');
            btnEditar.classList.remove('d-none');

            btnEditar.focus();
        }

        btnEditar.addEventListener('click', activarEdicion);
        btnCancelar.addEventListener('click', cancelarEdicion);

    });

});