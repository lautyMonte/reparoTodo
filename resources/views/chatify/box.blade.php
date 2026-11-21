
<div class="bandeja-chat">

    {{-- Lista de conversaciones --}}
    <div id="lista-chats"></div>

    {{-- Conversación seleccionada --}}
    <div class="panel-conversacion">

        <h3 id="chat-titulo">
            Seleccioná una conversación
        </h3>

        <div id="chat-mensajes"></div>

        <form id="form-chat" style="display: none;">

            <input
                type="text"
                id="mensaje"
                placeholder="Escribí un mensaje..."
                autocomplete="off"
            >

            <button type="submit">
                Enviar
            </button>

        </form>

    </div>

</div>

@push('scripts')
    <script src="{{ asset('js/box.js') }}" defer></script>
@endpush