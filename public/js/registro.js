// Guarda los IDs y ambos nombres de las especialidades antes de inicializar Tom Select
const nombresEspecialidades = Array.from(
    document.querySelectorAll('#especialidad option[data-nombre-m]'),
    opcion => ({
        id: opcion.value,
        nombreM: opcion.dataset.nombreM,
        nombreF: opcion.dataset.nombreF,
    })
);

document.addEventListener("DOMContentLoaded", () => {

    const botones = document.querySelectorAll('.btn-tipo');
    const tipo = document.getElementById('tipo');
    const camposProfesional = document.getElementById('campos-profesional');
    const provincia = document.getElementById('provincia');
    const ciudad = document.getElementById('ciudad');
    const genero = document.getElementById('genero');
    const especialidad = document.getElementById('especialidad');

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

            // Habilita el selector de especialidades solo si la cuenta es de un profesional, estas ultimas requieren un genero seleccionado si o si
            if (campo.id === 'especialidad') {
                habilitado = esProfesional && genero.value !== '';
            }

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

    // Al cambiar el género, borrar las especialidades elegidas y actualiza sus nombres
    genero.addEventListener('change', () => {
        const selector = especialidad.tomselect;
        const atributo = genero.value === 'f' ? 'nombreF' : 'nombreM';

        selector.clear();
        selector.clearOptions();

        selector.addOptions(nombresEspecialidades.map(oficio => ({
            value: oficio.id,
            text: oficio[atributo],
        })));

        selector.refreshOptions(false);
        actualizarFormulario(tipo.value);
    });

});
