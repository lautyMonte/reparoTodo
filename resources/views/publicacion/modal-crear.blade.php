@php
    $erroresPublicacion = $errors->getBag('publicacion');

    $mostrarErrores = $erroresPublicacion->any()
        && old('origen') === 'bandeja_publicacion_crear'
        && (string) old('publicacion_solicitud_id') === (string) $solicitud->id;
@endphp

<div
    class="modal fade"
    id="modalCrearPublicacion"
    tabindex="-1"
    aria-labelledby="modalCrearPublicacionLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">

            <form
            class="modal-content bg-white"
                action="{{ route('publicaciones.store', $solicitud) }}"
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf

                <input type="hidden" name="origen" value="bandeja_publicacion_crear">
                <input type="hidden" name="publicacion_solicitud_id" value="{{ $solicitud->id }}">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalCrearPublicacionLabel">
                        Publicar trabajo realizado
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar"
                    ></button>
                </div>

                <div class="modal-body">
                    <p class="text-secondary">
                        Mostrá tu experiencia agregando este trabajo
                        a tu portfolio profesional.
                    </p>

                    @if ($mostrarErrores)
                        <div data-publicacion-errores>
                            <div class="alert alert-danger" role="alert">
                                {{ $erroresPublicacion->first() }}
                            </div>
                        </div>
                    @endif

                    @include('publicacion.form', [
                        'publicacion' => null,
                        'prefijo' => 'crear-publicacion',
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
                        <i data-lucide="image-plus"></i>
                        Publicar trabajo
                    </button>
                </div>
            </form>
    </div>
</div>

@if ($mostrarErrores)
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const modal = document.getElementById('modalCrearPublicacion');

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