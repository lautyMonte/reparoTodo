
@extends('layouts.app', ['titulo' => $titulo])

@section('contenido')

    <div class="mb-4">
        <h1>Mi perfil profesional</h1>

        <p class="text-muted">
            Administrá tus datos y los servicios que ofrecés
            en ReparoTodo.
        </p>
    </div>

    @if ($profesional)

        {{-- Estado del perfil --}}
        <div class="alert alert-secondary">
            Estado del perfil:
            <strong>
                {{ $profesional->publicado ? 'Publicado' : 'Borrador' }}
            </strong>
        </div>

        {{-- Formulario para editar el perfil --}}
        <form
            action="{{ route('profesional.actualizar') }}"
            method="POST"
            class="card"
        >
            @csrf
            @method('PATCH')

            <div class="card-body">

                <h2 class="h5 mb-4">
                    Datos profesionales
                </h2>

                {{-- Descripción --}}
                <div class="mb-3">
                    <label for="descripcion" class="form-label">
                        Descripción profesional
                    </label>

                    <textarea
                        name="descripcion"
                        id="descripcion"
                        class="form-control"
                        rows="4"
                        maxlength="3000"
                        placeholder="Contá qué servicios ofrecés y cuál es tu experiencia."
                    >{{ old('descripcion', $profesional->descripcion) }}</textarea>
                </div>

                {{-- Nombre comercial --}}
                <div class="mb-3">
                    <label for="nombre_comercial" class="form-label">
                        Nombre comercial (opcional)
                    </label>

                    <input
                        type="text"
                        name="nombre_comercial"
                        id="nombre_comercial"
                        class="form-control"
                        maxlength="150"
                        value="{{ old('nombre_comercial', $profesional->nombre_comercial) }}"
                    >
                </div>

                {{-- Teléfono comercial --}}
                <div class="mb-4">
                    <label for="telefono_comercial" class="form-label">
                        Teléfono comercial (opcional)
                    </label>

                    <input
                        type="tel"
                        name="telefono_comercial"
                        id="telefono_comercial"
                        class="form-control"
                        maxlength="20"
                        placeholder="2991234567"
                        value="{{ old('telefono_comercial', $profesional->telefono_comercial) }}"
                    >
                </div>

                <hr>

                {{-- Oficios --}}
                <h2 class="h5 mb-3">
                    Oficios que ofrecés
                </h2>

                <p class="text-muted small">
                    Seleccioná uno o varios oficios.
                </p>

                <div class="row mb-4">

                    @foreach ($oficiosDisponibles as $oficio)

                        <div class="col-md-6 mb-2">
                            <label class="form-check">

                                <input
                                    type="checkbox"
                                    name="oficios[]"
                                    value="{{ $oficio->id }}"
                                    class="form-check-input"

                                    @checked(
                                        in_array(
                                            $oficio->id,
                                            (array) old(
                                                'oficios',
                                                $profesional->oficios->modelKeys()
                                            )
                                        )
                                    )
                                >

                                <span class="form-check-label">
                                    {{ $oficio->tipo }}
                                </span>

                            </label>
                        </div>

                    @endforeach

                </div>


                <hr>

                <h2 class="h5 mb-3">Localidad principal</h2>

                <p class="text-muted small">
                    Seleccioná la provincia y la localidad donde trabajás principalmente.
                </p>

                <div class="row g-3 mb-4">

                    {{-- Provincia --}}
                    <div class="col-md-6">
                        <label for="jurisdiccion-principal" class="form-label">
                            Provincia
                        </label>

                        <select
                            id="jurisdiccion-principal"
                            name="jurisdiccion_principal"
                            class="form-select"
                        >
                            <option value="">Seleccionar provincia</option>

                            @foreach ($jurisdicciones as $jurisdiccion)
                                <option
                                    value="{{ $jurisdiccion->id }}"
                                    @selected(
                                        old(
                                            'jurisdiccion_principal',
                                            $profesional->localidadPrincipal?->jurisdiccion_id
                                        ) == $jurisdiccion->id
                                    )
                                >
                                    {{ $jurisdiccion->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Localidad --}}
                    <div class="col-md-6">
                        <label for="localidad-principal" class="form-label">
                            Localidad
                        </label>

                        <select
                            id="localidad-principal"
                            name="localidad_id"
                            class="form-select"
                            data-seleccion="{{ old('localidad_id', $profesional->localidad_id) }}"
                            disabled
                        >
                            <option value="">Seleccionar localidad</option>
                        </select>
                    </div>

                </div>

                <hr>

                <h2 class="h5 mb-3">Localidades de cobertura</h2>

                <p class="text-muted small">
                    Seleccioná las localidades donde ofrecés tus servicios.
                    Podés agregar varias, incluso de diferentes provincias.
                </p>

                <div class="row g-3 mb-3">

                    <div class="col-md-5">
                        <label for="jurisdiccion-cobertura" class="form-label">
                            Provincia
                        </label>

                        <select id="jurisdiccion-cobertura" class="form-select">
                            <option value="">Seleccionar provincia</option>

                            @foreach ($jurisdicciones as $jurisdiccion)
                                <option value="{{ $jurisdiccion->id }}">
                                    {{ $jurisdiccion->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label for="localidad-cobertura" class="form-label">
                            Localidad
                        </label>

                        <select id="localidad-cobertura"
                                class="form-select"
                                disabled>
                            <option value="">Seleccionar localidad</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button"
                                id="agregar-cobertura"
                                class="btn btn-outline-primary w-100">
                            Agregar
                        </button>
                    </div>

                </div>

                <h3 class="h6">Zonas seleccionadas</h3>

                <ul id="lista-cobertura" class="list-group mb-4">

                    @foreach ($localidadesSeleccionadas as $zona)

                        <li class="list-group-item d-flex justify-content-between align-items-center gap-2"
                            data-id="{{ $zona->id }}">

                            <span>
                                {{ $zona->nombre }}
                                ({{ $zona->jurisdiccion->nombre }})
                            </span>

                            <input type="hidden"
                                name="localidades_cobertura[]"
                                value="{{ $zona->id }}">

                            <button type="button"
                                    class="btn btn-sm btn-outline-danger quitar-cobertura">
                                Quitar
                            </button>

                        </li>

                    @endforeach

                </ul>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Guardar cambios
                </button>

            </div>
        </form>

    @else

        {{-- Usuario que todavía no tiene perfil profesional --}}
        <div class="card">
            <div class="card-body">

                <h2 class="h5">
                    Comenzá a ofrecer tus servicios
                </h2>

                <p>
                    Creá tu perfil profesional. Inicialmente
                    será privado y podrás completar sus datos
                    antes de publicarlo.
                </p>

                <form
                    action="{{ route('profesional.crear-borrador') }}"
                    method="POST"
                >
                    @csrf

                    <button type="submit" class="btn btn-primary">
                        Crear mi perfil profesional
                    </button>
                </form>

            </div>
        </div>

    @endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const url = @json(route('geografia.localidades'));

    // Localidad principal
    const provinciaPrincipal =
        document.getElementById('jurisdiccion-principal');

    const localidadPrincipal =
        document.getElementById('localidad-principal');

    // Localidades de cobertura
    const provinciaCobertura =
        document.getElementById('jurisdiccion-cobertura');

    const localidadCobertura =
        document.getElementById('localidad-cobertura');

    const listaCobertura =
        document.getElementById('lista-cobertura');

    const botonAgregar =
        document.getElementById('agregar-cobertura');

    // El formulario solo existe si hay perfil profesional.
    if (!provinciaPrincipal) return;

    // Función reutilizable para ambos selectores.
    async function cargarLocalidades(
        provinciaId,
        selector,
        localidadSeleccionada = ''
    ) {
        selector.replaceChildren(
            new Option('Seleccionar localidad', '')
        );

        selector.disabled = true;

        if (!provinciaId) return;

        try {
            const respuesta = await fetch(
                `${url}?jurisdiccion_id=${encodeURIComponent(provinciaId)}`,
                {
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );

            if (!respuesta.ok) {
                throw new Error('Error al obtener localidades');
            }

            const localidades = await respuesta.json();

            localidades.forEach(item => {
                selector.add(
                    new Option(item.nombre, item.id)
                );
            });

            selector.value = String(localidadSeleccionada);
            selector.disabled = false;

        } catch (error) {
            console.error(error);
            alert('No se pudieron cargar las localidades.');
        }
    }

    // Selección de localidad principal.
    provinciaPrincipal.addEventListener('change', () => {
        cargarLocalidades(
            provinciaPrincipal.value,
            localidadPrincipal
        );
    });

    // Recuperar localidad principal al abrir el formulario.
    if (provinciaPrincipal.value) {
        cargarLocalidades(
            provinciaPrincipal.value,
            localidadPrincipal,
            localidadPrincipal.dataset.seleccion
        );
    }

    // Selección de localidades de cobertura.
    provinciaCobertura.addEventListener('change', () => {
        cargarLocalidades(
            provinciaCobertura.value,
            localidadCobertura
        );
    });

    // Agregar localidad de cobertura.
    botonAgregar.addEventListener('click', () => {
        const id = localidadCobertura.value;

        if (!id) return;

        // Impedir agregar dos veces la misma localidad.
        const existe = [...listaCobertura.querySelectorAll('li')]
            .some(elemento => elemento.dataset.id === id);

        if (existe) {
            alert('Esta localidad ya está agregada.');
            return;
        }

        const elemento = document.createElement('li');

        elemento.className =
            'list-group-item d-flex justify-content-between align-items-center gap-2';

        elemento.dataset.id = id;

        const nombre = document.createElement('span');

        nombre.textContent =
            `${localidadCobertura.selectedOptions[0].text} ` +
            `(${provinciaCobertura.selectedOptions[0].text})`;

        const input = document.createElement('input');

        input.type = 'hidden';
        input.name = 'localidades_cobertura[]';
        input.value = id;

        const botonQuitar = document.createElement('button');

        botonQuitar.type = 'button';
        botonQuitar.className =
            'btn btn-sm btn-outline-danger quitar-cobertura';

        botonQuitar.textContent = 'Quitar';

        elemento.append(nombre, input, botonQuitar);

        listaCobertura.appendChild(elemento);
    });

    // Quitar localidad de cobertura.
    listaCobertura.addEventListener('click', evento => {
        const boton = evento.target.closest('.quitar-cobertura');

        if (boton) {
            boton.closest('li').remove();
        }
    });

});
</script>
@endpush
