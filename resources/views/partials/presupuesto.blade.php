{{-- Tarjeta con el presupuesto de una solicitud. Recibe $presupuesto y $encabezado (el titulo de la tarjeta) --}}
<div class="presu-tarjeta">
    <div class="presu-titulo"><i data-lucide="file-text"></i> {{ $encabezado }}</div>
    <div class="presu-datos">
        <div>
            <div class="presu-etiqueta">Monto total</div>
            <div class="presu-valor">${{ number_format($presupuesto->monto, 0, ',', '.') }}</div>
        </div>
        <div>
            <div class="presu-etiqueta">Plazo estimado</div>
            <div class="presu-valor">{{ $presupuesto->plazo_dias }} {{ $presupuesto->plazo_dias === 1 ? 'día' : 'días' }}</div>
        </div>
    </div>
    <div class="presu-etiqueta mt-3">Detalle</div>
    <p class="mb-0">{{ $presupuesto->detalle }}</p>
</div>