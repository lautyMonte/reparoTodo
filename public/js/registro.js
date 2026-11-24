
document.addEventListener("DOMContentLoaded", () => {

    const botones = document.querySelectorAll('.btn-tipo');
    const tipo = document.getElementById('tipo');
    const camposProfesional = document.getElementById('campos-profesional');
    const provincia = document.getElementById('provincia');
    const ciudad = document.getElementById('ciudad');

    function actualizarFormulario(tipoSeleccionado) {

        // Actualizar el tipo de cuenta
        tipo.value = tipoSeleccionado;

        const esProfesional = tipoSeleccionado === 'profesional';

        // Mostrar u ocultar campos del profesional
        camposProfesional.hidden = !esProfesional;

        // Controlar campos exclusivos del profesional
        camposProfesional.querySelectorAll('select').forEach(campo => {

            campo.required = esProfesional;

            let habilitado = esProfesional;

            // La ciudad requiere una provincia seleccionada
            if (campo.id === 'ciudad') {
                habilitado = esProfesional && provincia.selectedOptions.length > 0;
            }

            campo.disabled = !habilitado;

            // Controlar los selectores que utilizan Tom Select
            if (campo.tomselect) {

                if (habilitado) {
                    campo.tomselect.enable();
                } else {
                    campo.tomselect.disable();
                }

                campo.tomselect.refreshState();
            }
        });

        // Cambiar el botón seleccionado
        botones.forEach(boton => {
            boton.classList.toggle(
                'activo',
                boton.dataset.tipo === tipoSeleccionado
            );
        });
    }

    // Detectar los clics en los botones
    botones.forEach(boton => {
        boton.addEventListener('click', () => {
            actualizarFormulario(boton.dataset.tipo);
        });
    });

    // Estado inicial
    actualizarFormulario(tipo.value);

});
