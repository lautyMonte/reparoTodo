@php
    $modalId = 'modalEditarPublicacion'.$publicacion->id;

    $erroresPublicacion = $errors->getBag('publicacion');

    $mostrarErrores = $erroresPublicacion->any()
        && old('origen') === $origen
        && (string) old('publicacion_editada') === (string) $publicacion->id;
@endphp

<div
    class="modal fade"
    id="{{ $modalId }}"
    tabindex="-1"
    aria-labelledby="{{ $modalId }}Label"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">

        <form
            class="modal-content bg-white"
            action="{{ route('publicaciones.update', $publicacion) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

                <input type="hidden" name="origen" value="{{ $origen }}">

                <input
                    type="hidden"
                    name="publicacion_editada"
                    value="{{ $publicacion->id }}"
                >

                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $modalId }}Label">
                        Editar publicación
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar"
                    ></button>
                </div>

                <div class="modal-body">

                @if ($mostrarErrores)
                    <div data-publicacion-errores>
                        <div class="alert alert-danger" role="alert">
                            {{ $erroresPublicacion->first() }}
                        </div>
                    </div>
                @endif

                    @include('publicacion.form', [
                        'publicacion' => $publicacion,
                        'prefijo' => 'editar-publicacion-'.$publicacion->id,
                        'usarOld' => $mostrarErrores,
                    ])

                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal"
                    >
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-accent fw-semibold">
                        <i data-lucide="save"></i>
                        Guardar cambios
                    </button>
            </div>

        </form>

    </div>
</div>

@if ($mostrarErrores)
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const modal = document.getElementById(@json($modalId));

                if (modal) {

                    modal.addEventListener('hidden.bs.modal', function () {

                        const errores = modal.querySelector('[data-publicacion-errores]');

                        if (errores) {
                            errores.remove();
                        }

                    });

                    bootstrap.Modal.getOrCreateInstance(modal).show();
                }

            });
        </script>
    @endpush
@endif