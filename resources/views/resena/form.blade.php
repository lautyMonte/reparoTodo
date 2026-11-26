@php
    $prefijo = $prefijo ?? 'resena';
    $usarOld = $usarOld ?? true;

    $puntajeActual = $usarOld
        ? old('puntaje', $resena->puntaje ?? '')
        : ($resena->puntaje ?? '');

    $comentarioActual = $usarOld
        ? old('comentario', $resena->comentario ?? '')
        : ($resena->comentario ?? '');
@endphp

<fieldset class="mb-3">

    <legend class="form-label fw-semibold">
        Puntuación
    </legend>

    <div class="rating-estrellas">

        @foreach ([5, 4, 3, 2, 1] as $puntaje)

            <input
                type="radio"
                name="puntaje"
                id="{{ $prefijo }}-estrella-{{ $puntaje }}"
                value="{{ $puntaje }}"
                @checked($puntajeActual == $puntaje)
                required
            >

            <label
                for="{{ $prefijo }}-estrella-{{ $puntaje }}"
                title="{{ $puntaje }} de 5 estrellas"
            >
                <span class="visually-hidden">
                    {{ $puntaje }} de 5 estrellas
                </span>

                <i data-lucide="star" aria-hidden="true"></i>
            </label>

        @endforeach

    </div>

    @if ($usarOld)
        @error('puntaje')
            <div class="text-danger small mt-2">
                {{ $message }}
            </div>
        @enderror
    @endif

</fieldset>

<div class="mb-3">

    <label
        for="{{ $prefijo }}-comentario"
        class="form-label fw-semibold"
    >
        Comentario (opcional)
    </label>

    <textarea
        name="comentario"
        id="{{ $prefijo }}-comentario"
        class="form-control {{ $usarOld && $errors->has('comentario') ? 'is-invalid' : '' }}"
        rows="3"
        maxlength="1000"
        placeholder="Contá cómo fue tu experiencia..."
    >{{ $comentarioActual }}</textarea>

    @if ($usarOld)
        @error('comentario')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    @endif

</div>