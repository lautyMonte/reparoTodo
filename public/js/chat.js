// Chat flotante de ReparoTodo: lista de conversaciones, conversacion abierta y envio de mensajes
document.addEventListener('DOMContentLoaded', () => {

    const widget = document.getElementById('chat-widget');

    // Si la persona no tiene conversaciones, el widget ni se dibuja en la pagina
    if (!widget) return;

    // Direccion de las conversaciones (la escribe Blade en el atributo data-url-conversaciones)
    const direccion = widget.dataset.urlConversaciones;
    const token = document.querySelector('meta[name="csrf-token"]').content;

    // Las piezas del widget
    const botonMensajes = document.getElementById('chat-boton');
    const ventana = document.getElementById('chat-ventana');
    const vistaLista = document.getElementById('chat-vista-lista');
    const vistaConversacion = document.getElementById('chat-vista-conversacion');
    const lista = document.getElementById('chat-lista');
    const volver = document.getElementById('chat-volver');
    const avatar = document.getElementById('chat-avatar');
    const nombre = document.getElementById('chat-nombre');
    const mensajes = document.getElementById('chat-mensajes');
    const formulario = document.getElementById('chat-formulario');
    const texto = document.getElementById('chat-texto');

    let personaActual = null;    // id de la persona con la que se esta hablando (null = ninguna)
    let cantidadMostrada = -1;   // cuantos mensajes hay dibujados (-1 = todavia ninguno)
    let temporizador = null;     // el setInterval que actualiza los mensajes

    // ---------- Abrir y cerrar ----------

    function abrirVentana() {
        ventana.hidden = false;
        botonMensajes.hidden = true;
        mostrarLista();
    }

    function cerrarVentana() {
        ventana.hidden = true;
        botonMensajes.hidden = false;
        detenerActualizacion();
        personaActual = null;
    }

    function mostrarLista() {
        detenerActualizacion();
        personaActual = null;
        vistaConversacion.hidden = true;
        vistaLista.hidden = false;
        cargarLista();
    }

    function abrirConversacion(id, nombrePersona, fotoPersona) {
        personaActual = id;
        cantidadMostrada = -1;
        nombre.textContent = nombrePersona;
        avatar.src = fotoPersona;
        mensajes.innerHTML = '';
        vistaLista.hidden = true;
        vistaConversacion.hidden = false;

        cargarMensajes();

        // Mientras la conversacion esta abierta, se buscan mensajes nuevos cada 5 segundos
        detenerActualizacion();
        temporizador = setInterval(cargarMensajes, 5000);

        texto.focus();
    }

    function detenerActualizacion() {
        clearInterval(temporizador);
    }

    // ---------- Formato de fechas ----------

    // Hora de un mensaje, por ejemplo 19:58
    function formatearHora(fecha) {
        return new Date(fecha).toLocaleTimeString('es-AR', { hour: '2-digit', minute: '2-digit' });
    }

    // Para la lista: la hora si es de hoy y el dia y mes si es de otro dia
    function formatearFecha(fecha) {
        if (!fecha) return '';

        const mensaje = new Date(fecha);

        if (mensaje.toDateString() === new Date().toDateString()) {
            return formatearHora(fecha);
        }

        return mensaje.toLocaleDateString('es-AR', { day: '2-digit', month: '2-digit' });
    }

    // ---------- Lista de conversaciones ----------

    async function cargarLista() {
        try {
            const respuesta = await fetch(direccion, {
                headers: { 'Accept': 'application/json' }
            });

            if (!respuesta.ok) {
                throw new Error('Error HTTP');
            }

            const datos = await respuesta.json();

            lista.innerHTML = '';

            if (datos.conversaciones.length === 0) {
                lista.innerHTML = '<p class="chat-vacio">Todavía no tenés conversaciones.</p>';
                return;
            }

            datos.conversaciones.forEach(conversacion => {
                lista.appendChild(crearFila(conversacion));
            });

        } catch (error) {
            lista.innerHTML = '<p class="chat-vacio">No se pudieron cargar las conversaciones.</p>';
        }
    }

    // Arma la fila de una conversacion en la lista
    function crearFila(conversacion) {
        const fila = document.createElement('button');
        fila.type = 'button';
        fila.className = 'chat-contacto';

        const foto = document.createElement('img');
        foto.src = conversacion.avatar;
        foto.alt = '';

        const cuerpo = document.createElement('div');
        cuerpo.className = 'chat-contacto-cuerpo';

        const arriba = document.createElement('div');
        arriba.className = 'chat-contacto-nombre';

        const quien = document.createElement('span');
        quien.textContent = conversacion.nombre;

        const cuando = document.createElement('small');
        cuando.textContent = formatearFecha(conversacion.fecha);

        arriba.appendChild(quien);
        arriba.appendChild(cuando);

        const ultimo = document.createElement('div');
        ultimo.className = 'chat-contacto-ultimo';
        ultimo.textContent = conversacion.ultimo_mensaje || 'Todavía no hay mensajes';

        cuerpo.appendChild(arriba);
        cuerpo.appendChild(ultimo);

        fila.appendChild(foto);
        fila.appendChild(cuerpo);

        fila.addEventListener('click', () => {
            abrirConversacion(conversacion.usuario_id, conversacion.nombre, conversacion.avatar);
        });

        return fila;
    }

    // ---------- Conversacion abierta ----------

    async function cargarMensajes() {
        if (personaActual === null) return;

        const id = personaActual;

        try {
            const respuesta = await fetch(`${direccion}/${id}`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!respuesta.ok) {
                throw new Error('Error HTTP');
            }

            const datos = await respuesta.json();

            // Si mientras cargaba la persona volvio a la lista o eligio otra, no se dibuja nada
            if (id !== personaActual) return;

            dibujarMensajes(datos.mensajes);

        } catch (error) {
            if (mensajes.children.length === 0) {
                mensajes.innerHTML = '<p class="chat-vacio">No se pudieron cargar los mensajes.</p>';
            }
        }
    }

    function dibujarMensajes(listaDeMensajes) {
        // Si no hay mensajes nuevos, no se vuelve a dibujar
        if (listaDeMensajes.length === cantidadMostrada) return;

        cantidadMostrada = listaDeMensajes.length;
        mensajes.innerHTML = '';

        if (listaDeMensajes.length === 0) {
            mensajes.innerHTML = '<p class="chat-vacio">Todavía no hay mensajes. ¡Escribí el primero!</p>';
            return;
        }

        listaDeMensajes.forEach(mensaje => {
            const burbuja = document.createElement('div');
            burbuja.className = mensaje.mio ? 'chat-burbuja mio' : 'chat-burbuja otro';
            burbuja.textContent = mensaje.texto;

            const hora = document.createElement('small');
            hora.textContent = formatearHora(mensaje.creado);
            burbuja.appendChild(hora);

            mensajes.appendChild(burbuja);
        });

        // Deja visible el ultimo mensaje
        mensajes.scrollTop = mensajes.scrollHeight;
    }

    // ---------- Enviar un mensaje ----------

    formulario.addEventListener('submit', async (e) => {
        e.preventDefault();

        const mensaje = texto.value.trim();

        if (mensaje === '' || personaActual === null) return;

        try {
            const respuesta = await fetch(`${direccion}/${personaActual}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ mensaje: mensaje })
            });

            if (!respuesta.ok) {
                throw new Error('Error HTTP');
            }

            texto.value = '';
            cargarMensajes();

        } catch (error) {
            const aviso = document.createElement('p');
            aviso.className = 'chat-vacio';
            aviso.textContent = 'No se pudo enviar el mensaje. Probá de nuevo.';
            mensajes.appendChild(aviso);
        }
    });

    // ---------- Botones ----------

    botonMensajes.addEventListener('click', abrirVentana);

    document.querySelectorAll('.chat-cerrar').forEach(boton => {
        boton.addEventListener('click', cerrarVentana);
    });

    volver.addEventListener('click', mostrarLista);

    // Boton "Abrir chat" de la bandeja: abre la ventana directo en la conversacion con esa persona
    document.querySelectorAll('.btn-abrir-chat').forEach(boton => {
        boton.addEventListener('click', () => {
            ventana.hidden = false;
            botonMensajes.hidden = true;
            abrirConversacion(Number(boton.dataset.usuarioId), boton.dataset.nombre, boton.dataset.avatar);
        });
    });
});