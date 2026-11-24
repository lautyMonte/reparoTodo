{{-- Chat flotante: boton "Mensajes" y ventana con la lista de conversaciones y la conversacion abierta --}}
<div id="chat-widget" data-url-conversaciones="{{ route('chat.index') }}">

    {{-- Boton flotante --}}
    <button type="button" id="chat-boton" class="chat-boton">
        <i data-lucide="message-circle"></i> Mensajes
    </button>

    <section id="chat-ventana" class="chat-ventana" aria-label="Mensajes" hidden>

        {{-- Vista 1: lista de conversaciones --}}
        <div id="chat-vista-lista" class="chat-vista">
            <header class="chat-cab chat-cab-centrado">
                <div>
                    <div class="chat-titulo">Mensajes</div>
                    <div class="chat-subtitulo">Tus conversaciones en curso</div>
                </div>
                <button type="button" class="chat-cerrar" aria-label="Cerrar mensajes">
                    <i data-lucide="x"></i>
                </button>
            </header>

            <div id="chat-lista" class="chat-lista-cuerpo"></div>

            <div class="chat-pie">Elegí una conversación para verla</div>
        </div>

        {{-- Vista 2: conversacion abierta --}}
        <div id="chat-vista-conversacion" class="chat-vista" hidden>
            <header class="chat-cab">
                <button type="button" id="chat-volver" aria-label="Volver a la lista">
                    <i data-lucide="arrow-left"></i>
                </button>
                <img id="chat-avatar" class="chat-cab-avatar" src="" alt="">
                <div id="chat-nombre" class="chat-titulo"></div>
                <button type="button" class="chat-cerrar" aria-label="Cerrar mensajes">
                    <i data-lucide="x"></i>
                </button>
            </header>

            <div id="chat-mensajes" class="chat-mensajes"></div>

            <form id="chat-formulario" class="chat-formulario" autocomplete="off">
                <input type="text" id="chat-texto" placeholder="Escribí un mensaje..." maxlength="2000" required>
                <button type="submit" aria-label="Enviar mensaje">
                    <i data-lucide="send"></i>
                </button>
            </form>
        </div>

    </section>
</div>