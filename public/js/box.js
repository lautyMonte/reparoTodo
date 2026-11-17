
const API = '/api/chatify/v1';

const listaChats = document.getElementById('lista-chats');
const contenedor = document.getElementById('chat-mensajes');

const usuarioActualId = Number(
    document.querySelector('meta[name="usuario-id"]').content
);

let conversacionActual = null;

// Ordenar mensajes del más antiguo al más reciente.
function ordenarMensajes(mensajes) {
    return [...mensajes].sort((a, b) => {
        const fechaA = Date.parse(a.attributes?.created_at);
        const fechaB = Date.parse(b.attributes?.created_at);

        return fechaA - fechaB;
    });
}

// Cargar conversaciones.
async function cargarConversaciones() {

    const respuesta = await fetch(`${API}/conversations`, {
        headers: {
            Accept: 'application/json'
        },
        credentials: 'same-origin'
    });

    if (!respuesta.ok) {
        console.error(await respuesta.text());
        return;
    }

    const resultado = await respuesta.json();

    listaChats.innerHTML = '';

    const conversaciones = resultado.data ?? [];

    conversaciones.forEach(chat => {

        if (chat.attributes?.conversation_type !== 'direct') {
            return;
        }

        const participantes =
            chat.relationships?.participants ?? [];

        const otroUsuario = participantes.find(
            participante =>
                Number(participante.id) !== usuarioActualId
        );

        if (!otroUsuario) return;

        const nombre =
            otroUsuario.attributes?.name ??
            `Usuario ${otroUsuario.id}`;

        const ultimoMensaje =
            chat.relationships?.last_message?.attributes?.body ??
            'Sin mensajes';

        const noLeidos =
            chat.attributes?.unread_count ?? 0;

        const elemento = document.createElement('button');

        elemento.type = 'button';
        elemento.className = 'chat-item';

        const titulo = document.createElement('strong');
        titulo.textContent = nombre;

        const mensaje = document.createElement('p');
        mensaje.textContent = ultimoMensaje;

        const contador = document.createElement('span');
        contador.textContent = noLeidos > 0
            ? `${noLeidos} mensajes sin leer`
            : '';

        elemento.append(titulo, mensaje, contador);

        elemento.addEventListener('click', () => {
            abrirConversacion(chat.id, nombre);
        });

        listaChats.appendChild(elemento);
    });
}

// Abrir conversación.
async function abrirConversacion(id, nombre) {

    conversacionActual = id;

    document.getElementById('chat-titulo').textContent = nombre;

    document.getElementById('form-chat').style.display = 'flex';

    await cargarMensajes();

    await fetch(`${API}/conversations/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector(
                'meta[name="csrf-token"]'
            ).content,
            Accept: 'application/json'
        },
        credentials: 'same-origin'
    });

    await cargarConversaciones();
}

// Cargar mensajes de la conversación.
async function cargarMensajes() {

    if (!conversacionActual) return;

    const respuesta = await fetch(
        `${API}/conversations/${conversacionActual}/messages`,
        {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        }
    );

    if (!respuesta.ok) {
        throw new Error(`Error al cargar mensajes: ${respuesta.status}`);
    }

    const resultado = await respuesta.json();

    contenedor.innerHTML = '';

    ordenarMensajes(resultado.data ?? []).forEach(mensaje => {

        const elemento = document.createElement('div');

        const emisor = mensaje.relationships?.sender?.data?.id;

        elemento.className =
            String(emisor) === String(usuarioActualId)
                ? 'mensaje propio'
                : 'mensaje recibido';

        elemento.textContent = mensaje.attributes?.body ?? '';

        contenedor.appendChild(elemento);
        
    });

    contenedor.scrollTop = contenedor.scrollHeight;
}

// ÚNICO evento para enviar mensajes.
const formulario = document.getElementById('form-chat');

formulario.addEventListener('submit', async function(e) {

    e.preventDefault();

    if (!conversacionActual) return;

    const input = document.getElementById('mensaje');

    const texto = input.value.trim();

    if (!texto) return;

    try {

        const respuesta = await fetch(
            `${API}/conversations/${conversacionActual}/messages`,
            {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content
                },

                credentials: 'same-origin',

                body: JSON.stringify({
                    body: texto
                })
            }
        );

        if (!respuesta.ok) {
            console.error(await respuesta.text());
            return;
        }

        input.value = '';

        await cargarMensajes();

        await cargarConversaciones();

    } catch (error) {
        console.error('Error al enviar mensaje:', error);
    }
});

cargarConversaciones();

// Actualizar conversaciones y mensajes cada 5 segundos.
setInterval(async () => {

    await cargarConversaciones();

    if (conversacionActual) {
        await cargarMensajes();
    }

}, 5000);
