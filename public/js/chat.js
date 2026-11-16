
document.addEventListener('DOMContentLoaded', () => {

    const widget = document.getElementById('chat-widget');

    if (!widget) return;

    const API = widget.dataset.api;

    const panel = document.getElementById('chat-panel');
    const toggle = document.getElementById('chat-toggle');
    const close = document.getElementById('chat-close');

    const nombre = document.getElementById('chat-name');
    const avatar = document.getElementById('chat-avatar');

    const mensajes = document.getElementById('chat-messages');
    const formulario = document.getElementById('chat-form');
    const input = document.getElementById('chat-input');
    const enviar = document.getElementById('chat-send');

    let conversacionActual = null;
    let temporizador = null;

    toggle.addEventListener('click', () => {
        panel.hidden = !panel.hidden;
    });

    close.addEventListener('click', () => {
        panel.hidden = true;
    });

    async function peticion(ruta, opciones = {}) {
    const respuesta = await fetch(`${API}${ruta}`, {
        ...opciones,
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            ...opciones.headers
        }
    });

    const texto = await respuesta.text();
    let resultado = {};

    try {
        resultado = texto ? JSON.parse(texto) : {};
    } catch {
        resultado = { mensaje: texto };
    }

    if (!respuesta.ok) {
        throw new Error(
            resultado.message ||
            resultado.mensaje ||
            `Error HTTP ${respuesta.status}`
        );
    }

    return resultado;
}

    // Se ejecuta desde el botón del perfil profesional.
   window.iniciarChat = async function (idUsuario, nombreUsuario) {
    panel.hidden = false;

    nombre.textContent = nombreUsuario;
    avatar.textContent = nombreUsuario.charAt(0).toUpperCase();

    mensajes.replaceChildren();

    const cargando = document.createElement('p');
    cargando.textContent = 'Abriendo conversación...';
    mensajes.appendChild(cargando);

    input.disabled = true;
    enviar.disabled = true;

    try {
        // Crear o recuperar la conversación directa.
        const resultado = await peticion('/conversations/direct', {
            method: 'POST',
            body: JSON.stringify({
                user_id: Number(idUsuario)
            })
        });

        console.log('Respuesta al iniciar conversación:', resultado);

        // Guardar el ID REAL de la conversación.
        conversacionActual = resultado.data?.id;

        if (!conversacionActual) {
            throw new Error('Chatify no devolvió el ID de la conversación.');
        }

        await cargarMensajes();

        input.disabled = false;
        enviar.disabled = false;
        input.focus();

        clearInterval(temporizador);
        temporizador = setInterval(cargarMensajes, 5000);

    } catch (error) {
        mensajes.textContent = 'No se pudo abrir la conversación.';
        console.error('Error al iniciar:', error);

        input.disabled = false;
        enviar.disabled = false;
    }
};


function ordenarMensajes(mensajes) {
    return [...mensajes].sort((a, b) => {
        const fechaA = Date.parse(a.attributes?.created_at);
        const fechaB = Date.parse(b.attributes?.created_at);

        return fechaA - fechaB;
    });
}

    
async function cargarMensajes() {
    const resultado = await peticion(
    `/conversations/${conversacionActual}/messages`
);

mensajes.replaceChildren();

ordenarMensajes(resultado.data ?? []).forEach(mensaje => {

    const burbuja = document.createElement('div');

    const idEmisor =
        mensaje.relationships?.sender?.data?.id;

    const contenido =
        mensaje.attributes?.body ?? '';

    burbuja.className =
        'chat-message ' +
        (String(idEmisor) === String(widget.dataset.user)
            ? 'sent'
            : 'received');

    burbuja.textContent = contenido;

    mensajes.appendChild(burbuja);
});

mensajes.scrollTop = mensajes.scrollHeight;
}


async function enviarMensaje(texto) {

    console.log('Conversación seleccionada:', conversacionActual);
console.log('Usuario que envía:', widget.dataset.user);
console.log('Texto:', texto);
    if (!conversacionActual) {
        throw new Error('No hay una conversación seleccionada.');
    }

    const resultado = await peticion(
        `/conversations/${conversacionActual}/messages`,
        {
            method: 'POST',
            body: JSON.stringify({
                body: texto
            })
        }
    );

    console.log('Mensaje enviado:', resultado);

    await cargarMensajes();
}


formulario.addEventListener('submit', async function (e) {
    e.preventDefault();

    const texto = input.value.trim();

    if (!texto) return;

    try {
        await enviarMensaje(texto);
        input.value = '';
    } catch (error) {
        console.error('Error al enviar el mensaje:', error);
    }
});

    // Detectar cuando se presiona Contactar profesional
document.addEventListener('click', (evento) => {

    const boton = evento.target.closest('.btn-contactar');

    if (!boton) return;

    const idUsuario = boton.dataset.userId;
    const nombreUsuario = boton.dataset.userName;

    console.log('Profesional seleccionado:', idUsuario, nombreUsuario);

    window.iniciarChat(idUsuario, nombreUsuario);

});

});
