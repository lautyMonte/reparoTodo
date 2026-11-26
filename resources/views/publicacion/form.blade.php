@php
    $publicacion = $publicacion ?? null;
    $usarOld = $usarOld ?? false;
@endphp

<div class="mb-3">
    <label for="{{ $prefijo }}-titulo" class="form-label fw-semibold">
        Título del trabajo
    </label>

    <input
        type="text"
        name="titulo"
        id="{{ $prefijo }}-titulo"
        class="form-control"
        maxlength="100"
        value="{{ $usarOld ? old('titulo', $publicacion?->titulo) : $publicacion?->titulo }}"
        placeholder="Ej. Instalación eléctrica de una vivienda"
        required
    >
</div>

<div class="mb-3">
    <label for="{{ $prefijo }}-descripcion" class="form-label fw-semibold">
        Descripción
    </label>

    <textarea
        name="descripcion"
        id="{{ $prefijo }}-descripcion"
        class="form-control"
        rows="5"
        maxlength="2000"
        placeholder="Describí el trabajo realizado..."
        required
    >{{ $usarOld ? old('descripcion', $publicacion?->descripcion) : $publicacion?->descripcion }}</textarea>

    <div class="form-text">
        Máximo 2000 caracteres.
    </div>
</div>

@if ($publicacion && $publicacion->fotos->isNotEmpty())

    @php
        $idsEliminar = $usarOld
            ? (array) old('eliminar_fotos', [])
            : [];
    @endphp

    <div class="mb-3">
        <label class="form-label fw-semibold">
            Fotografías actuales
            ({{ $publicacion->fotos->count() }}/5)
        </label>

        <div class="row g-3">
            @foreach ($publicacion->fotos as $foto)

                <div class="col-6 col-md-4">
                    <div class="border rounded p-2">

                        <div class="ratio ratio-4x3 mb-2">
                            <img
                                src="{{ asset('storage/'.$foto->ruta) }}"
                                alt="Fotografía del trabajo {{ $loop->iteration }}"
                                class="rounded object-fit-cover"
                            >
                        </div>

                        <div class="form-check">
                            <input
                                type="checkbox"
                                name="eliminar_fotos[]"
                                value="{{ $foto->id }}"
                                id="{{ $prefijo }}-eliminar-{{ $foto->id }}"
                                class="form-check-input"
                                @checked(in_array($foto->id, $idsEliminar))
                            >

                            <label
                                class="form-check-label small"
                                for="{{ $prefijo }}-eliminar-{{ $foto->id }}"
                            >
                                Eliminar foto
                            </label>
                        </div>

                    </div>
                </div>

            @endforeach
        </div>

        <div class="form-text">
            Las fotografías seleccionadas se eliminarán al guardar los cambios.
        </div>
    </div>

@endif

<div class="mb-3">
    <label for="{{ $prefijo }}-fotos" class="form-label fw-semibold">
        Fotografías (opcional)
    </label>

    <input
        type="file"
        name="fotos[]"
        id="{{ $prefijo }}-fotos"
        class="form-control"
        accept="image/jpeg,image/png,image/webp"
        multiple
    >

    <div class="form-text">
        Hasta 5 fotografías en total. JPG, PNG o WebP.
        Máximo 2 MB por archivo.
    </div>
</div>

<p class="small text-secondary mb-0">
    La publicación será visible en tu perfil público.
    No incluyas datos personales del cliente ni fotografías
    que no tengas autorización para publicar.
</p>