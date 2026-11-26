<div
    class="modal fade"
    id="modalEliminarPublicacion{{ $publicacion->id }}"
    tabindex="-1"
    aria-labelledby="modalEliminarPublicacion{{ $publicacion->id }}Label"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4 bg-white">

            <div class="modal-body">

                <i data-lucide="circle-alert" class="icn-rechazo mb-3"></i>

                <h4 id="modalEliminarPublicacion{{ $publicacion->id }}Label">
                    ¿Eliminar publicación?
                </h4>

                <p class="fw-semibold">
                    {{ $publicacion->titulo }}
                </p>

                <p>
                    Se eliminará esta publicación de tu portfolio
                    junto con todas sus fotografías.
                </p>

                <p class="small text-secondary">
                    La solicitud original y la reseña del cliente,
                    si existe, se conservarán.
                    Esta acción no se puede deshacer.
                </p>

                <form
                    action="{{ route('publicaciones.destroy', $publicacion) }}"
                    method="POST"
                    class="d-flex justify-content-center gap-2 mt-3"
                >
                    @csrf
                    @method('DELETE')

                    <input type="hidden" name="origen" value="{{ $origen }}">

                    <button
                        type="button"
                        class="btn btn-detalle btn-cancelar"
                        data-bs-dismiss="modal"
                    >
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="btn btn-rechazar-confirmar fw-semibold"
                    >
                        Sí, eliminar
                    </button>

                </form>

            </div>

        </div>
    </div>
</div>