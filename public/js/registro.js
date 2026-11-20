
const botones = document.querySelectorAll('.btn-tipo');

const tipo = document.getElementById('tipo');

const camposCliente = document.getElementById('campo-cliente');

const camposProfesional = document.getElementById('campos-profesional');


function actualizarFormulario(tipoSeleccionado) {

    // Actualizar el tipo de cuenta
    tipo.value = tipoSeleccionado;

    const esCliente = tipoSeleccionado === 'cliente';
    const esProfesional = tipoSeleccionado === 'profesional';

    // Mostrar u ocultar campos
    camposCliente.hidden = !esCliente;
    camposProfesional.hidden = !esProfesional;

    // Controlar campos obligatorios del cliente
    camposCliente.querySelectorAll('input').forEach(input => {
        input.required = esCliente;
    });

    // Controlar campos obligatorios del profesional
    camposProfesional.querySelectorAll('input, textarea').forEach(input => {
        input.required = esProfesional;
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
actualizarFormulario('cliente');

