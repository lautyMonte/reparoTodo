
const botones = document.querySelectorAll('.btn-tipo');
const tipoInput = document.getElementById('tipo');
const campoProfesional = document.getElementById('campo-profesional');

botones.forEach(boton => {

    boton.addEventListener('click', () => {

        // Obtener el tipo seleccionado
        const tipoSeleccionado = boton.dataset.tipo;

        // Actualizar el input hidden
        tipoInput.value = tipoSeleccionado;

        // Mostrar u ocultar campos
        const esProfesional = tipoSeleccionado === 'profesional';

        camposProfesional.hidden = !esProfesional;

        // Actualizar obligatoriedad
        campoProfesional.querySelectorAll('input, textarea').forEach(input => {
            input.required = esProfesional;
        });

        // Cambiar botón activo
        botones.forEach(btn => btn.classList.remove('activo'));

        boton.classList.add('activo');

    });

});
