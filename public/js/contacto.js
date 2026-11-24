// Muestra el input asunto cuanto el motivo es "Otro motivo"
const motivo = document.getElementById('motivo');
const contenedorAsunto = document.getElementById('contenedorAsunto');
const asunto = document.getElementById('asunto');

function actualizarAsunto() {
    if (motivo.value === 'otro') {
        contenedorAsunto.classList.remove('d-none');
        asunto.required = true;
    } else {
        contenedorAsunto.classList.add('d-none');
        asunto.required = false;
        asunto.value = '';
    }
}

motivo.addEventListener('change', actualizarAsunto);
actualizarAsunto();

// Muestra el modal de mensaje exitoso
document.addEventListener('DOMContentLoaded', function () {

    const modalExito = document.getElementById('modalExito');

    if (modalExito) {
        const modal = new bootstrap.Modal(modalExito);
        modal.show();
    }

});

// Muestra la cantidad de caracteres del mensaje
const mensaje = document.getElementById("mensaje");
const contador = document.getElementById("contador");

mensaje.addEventListener("input", () => {
    contador.textContent = `${mensaje.value.length} / 240 caracteres`;
});