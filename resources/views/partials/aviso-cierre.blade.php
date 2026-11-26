{{-- Aviso del cierre del trabajo. Recibe $clase (el color), $icono, $titulo y $texto --}}
<div class="cierre-aviso {{ $clase }}">
    <i data-lucide="{{ $icono }}"></i>
    <div>
        <strong>{{ $titulo }}</strong>
        <span>{{ $texto }}</span>
    </div>
</div>