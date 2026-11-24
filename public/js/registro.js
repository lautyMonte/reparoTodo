const botones = document.querySelectorAll('.btn-tipo');
const tipo = document.getElementById('tipo');
const camposProfesional = document.getElementById('campos-profesional');

function actualizarFormulario(tipoSeleccionado) {

    // Actualizar el tipo de cuenta
    tipo.value = tipoSeleccionado;

    const esProfesional = tipoSeleccionado === 'profesional';

    // Mostrar u ocultar campos del profesional
    camposProfesional.hidden = !esProfesional;

    // Controlar campos exclusivos del profesional
    camposProfesional.querySelectorAll('input, select, textarea').forEach(input => {
        input.required = esProfesional;
        input.disabled = !esProfesional;
    });

    // Controlar el selector de ciudades
    const provincia = document.getElementById('provincia');
    const ciudad = document.getElementById('ciudad');

    ciudad.disabled = !esProfesional || provincia.value === '';

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
