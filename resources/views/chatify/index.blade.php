
 <div
    id="chat-widget"
    data-api="/api/chatify/v1"
    data-user="{{ auth()->id() }}"
>

    {{-- Botón flotante --}}
    <button type="button" id="chat-toggle" class="chat-toggle">
        <span>✉</span>
        Mensajes
    </button>

    {{-- Ventana pequeña --}}
    <section id="chat-panel" class="chat-panel" hidden>

        <header class="chat-header">
            <div class="chat-user-info">
                <div class="chat-avatar" id="chat-avatar">?</div>

                <div>
                    <strong id="chat-name">Mensajes</strong>
                    <small id="chat-status">Conectate con profesionales</small>
                </div>
            </div>

            <button type="button" id="chat-close" aria-label="Cerrar chat">
                ×
            </button>
        </header>

        <div id="chat-messages" class="chat-messages">
            <p class="chat-empty">
                Elegí un profesional para comenzar a conversar.
            </p>
        </div>

        <form id="chat-form" class="chat-form">
            @csrf

            <input
                type="text"
                id="chat-input"
                placeholder="Escribí un mensaje..."
                autocomplete="off"
                required
            >

            <button type="submit" id="chat-send">
                ➤
            </button>
        </form>

    </section>
</div>
