// Cada 30 segundos le pregunta al servidor cuantas notificaciones y solicitudes pendientes hay,
// y actualiza los numeros del menu sin recargar la pagina.
const INTERVALO = 30000;

const numeroCampana = document.getElementById('campana-numero');
const numeroBandeja = document.getElementById('bandeja-numero');
const aviso = document.getElementById('aviso-nuevas');
const avisoCampana = document.getElementById('aviso-notificaciones');

function mostrarNumero(elemento, cantidad) {
    if (!elemento) {
        return;
    }
    elemento.textContent = cantidad;
    elemento.classList.toggle('d-none', cantidad === 0);
}

async function actualizar() {
    try {
        const respuesta = await fetch('/notificaciones/resumen', {
            headers: { 'Accept': 'application/json' },
        });
        if (!respuesta.ok) throw new Error('Error HTTP');
        const datos = await respuesta.json();

        mostrarNumero(numeroCampana, datos.sin_leer);
        mostrarNumero(numeroBandeja, datos.pendientes);

        // En la bandeja: si hay mas pendientes que cuando se cargo la pagina, muestra el aviso
        if (aviso && datos.pendientes > Number(aviso.dataset.pendientes)) {
            aviso.classList.remove('d-none');
        }
        // En la campana: si hay mas sin leer que cuando se cargo la pagina, muestra el aviso
        if (avisoCampana && datos.sin_leer > Number(avisoCampana.dataset.sinLeer)) {
            avisoCampana.classList.remove('d-none');
        }
    } catch (error) {
        // Si falla la conexion no hace nada: lo vuelve a intentar en el proximo ciclo
    }
}

setInterval(actualizar, INTERVALO);