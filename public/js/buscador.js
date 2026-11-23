document.addEventListener('DOMContentLoaded', () => {

    const resultados = document.getElementById('resultados-profesionales');
    const formularioNombre = document.getElementById('form-buscar');
    const formularioFiltros = document.getElementById('form-filtros');
    const campoBuscar = document.getElementById('buscar');
    const botonLimpiar = document.getElementById('limpiar-filtros');
    const contadorFiltros = document.getElementById('contador-filtros');

    let temporizador = null;
    let controladorActual = null;

    // Búsqueda conjunta
    async function realizarBusqueda(cerrarModal = false) {

        // Cancelar la petición anterior, si existe
        if (controladorActual) {
            controladorActual.abort();
        }

        // Crear un nuevo controlador
        controladorActual = new AbortController();

        // Tomamos los filtros seleccionados
        const parametros = new URLSearchParams(
            new FormData(formularioFiltros)
        );

        // Agregamos el nombre escrito
        parametros.set('buscar', campoBuscar.value.trim());

        try {

            resultados.innerHTML =
                '<p class="text-center">Buscando profesionales...</p>';

            // Siempre enviamos los datos al método filtrar
            const respuesta = await fetch(
                `${formularioFiltros.action}?${parametros}`,
                {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },

                    // Asociar la petición al controlador
                    signal: controladorActual.signal
                }
            );

            if (!respuesta.ok) {
                throw new Error(`Error HTTP: ${respuesta.status}`);
            }

            const datos = await respuesta.json();

            resultados.innerHTML = datos.html;

            if (window.lucide) {
                lucide.createIcons();
            }

            // Cerrar el modal solamente al aplicar los filtros
            if (cerrarModal) {
                const modal = document.getElementById('modalFiltros');

                if (modal && window.bootstrap) {
                    bootstrap.Modal.getOrCreateInstance(modal).hide();
                }
            }

        } catch (error) {

            // No mostrar errores cuando cancelamos una petición
            if (error.name === 'AbortError') {
                return;
            }

            console.error('Error en la búsqueda:', error);

            resultados.innerHTML = `
                <p class="text-center text-danger">
                    Ocurrió un error al realizar la búsqueda.
                </p>
            `;
        }
    }

    // Al presionar el botón Buscar
    formularioNombre.addEventListener('submit', (e) => {
        e.preventDefault();
        clearTimeout(temporizador);
        realizarBusqueda();
    });

    // Buscar automáticamente mientras se escribe
    campoBuscar.addEventListener('input', () => {

        clearTimeout(temporizador);

        temporizador = setTimeout(() => {
            realizarBusqueda();
        }, 350);

    });

    // Al aplicar los filtros del modal
    formularioFiltros.addEventListener('submit', (e) => {
        e.preventDefault();

        clearTimeout(temporizador);
        realizarBusqueda(true);
    });

    // Limpiar filtros
    botonLimpiar.addEventListener('click', () => {

        // Desmarcar todos los checkboxes
        formularioFiltros.querySelectorAll('input[type="checkbox"]')
            .forEach(checkbox => {
                checkbox.checked = false;
            });

        // Eliminar los parámetros de oficios de la URL
        const url = new URL(window.location.href);

        for (const parametro of [...url.searchParams.keys()]) {
            if (parametro === 'oficios' || parametro.startsWith('oficios[')) {
                url.searchParams.delete(parametro);
            }
        }

        // Actualizar la URL sin recargar la página
        window.history.replaceState(
            window.history.state,
            '',
            url.pathname + url.search + url.hash
        );

        // Actualizar contador y resultados
        actualizarContadorFiltros();

        clearTimeout(temporizador);
        realizarBusqueda(true);
    });



    // Actualizar cantidad de filtros seleccionados
    function actualizarContadorFiltros() {

        const filtrosSeleccionados = formularioFiltros.querySelectorAll(
            'input[type="checkbox"]:checked'
        );

        const cantidad = filtrosSeleccionados.length;

        contadorFiltros.textContent = cantidad;

        contadorFiltros.style.display = cantidad > 0 ? 'inline-block': 'none';
    }

    formularioFiltros.addEventListener('change', actualizarContadorFiltros);
    // Inicializar contador
    actualizarContadorFiltros();
});