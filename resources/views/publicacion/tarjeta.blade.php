<article class="border rounded-3 p-3 mb-3 bg-white">

    {{-- Título y fecha de publicación --}}
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">

        <h3 class="h5 mb-0">
            {{ $publicacion->titulo }}
        </h3>

        <small class="text-secondary">
            {{ $publicacion->created_at->format('d/m/Y') }}
        </small>

    </div>

    {{-- Trabajo finalizado y verificado --}}
    <small class="text-success">
        <i data-lucide="badge-check"></i>
        Trabajo verificado
    </small>

    {{-- Descripción del trabajo --}}
    <p class="mt-3 mb-3 text-break"
        style="white-space: pre-line;">{{ $publicacion->descripcion }}</p>

    {{-- Galería de fotografías --}}
    @if ($publicacion->fotos->isNotEmpty())

        <div class="row g-2 mb-3">

            @foreach ($publicacion->fotos as $foto)

                <div class="col-6 col-md-4">

                    <a
                        href="{{ asset('storage/'.$foto->ruta) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="d-block"
                    >
                        <div class="ratio ratio-4x3">

                            <img
                                src="{{ asset('storage/'.$foto->ruta) }}"
                                alt="Fotografía {{ $loop->iteration }} de {{ $publicacion->titulo }}"
                                class="rounded object-fit-cover"
                                loading="lazy"
                            >

                        </div>
                    </a>

                </div>

            @endforeach

        </div>

    @endif

    {{-- Acciones exclusivas del propietario --}}
    @if ($esDuenio)

        <div class="d-flex flex-wrap justify-content-end gap-2">

            @can('update', $publicacion)

                <button
                    type="button"
                    class="btn btn-eliminar-resena btn-editar-resena"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEditarPublicacion{{ $publicacion->id }}"
                >
                    <i data-lucide="pencil"></i>
                    Editar publicación
                </button>

            @endcan

            @can('delete', $publicacion)

                <button
                    type="button"
                    class="btn btn-eliminar-resena"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEliminarPublicacion{{ $publicacion->id }}"
                >
                    <i data-lucide="trash-2"></i>
                    Eliminar publicación
                </button>

            @endcan

        </div>

    @endif

</article>