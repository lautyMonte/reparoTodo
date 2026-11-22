document.addEventListener('DOMContentLoaded', () => {

    const resultados = document.getElementById('resultados-profesionales');
    const formularioNombre = document.getElementById('form-buscar');
    const formularioOficio = document.getElementById('form-oficio');

    // Función AJAX reutilizable
    async function realizarBusqueda(formulario) {

        const parametros = new URLSearchParams(
            new FormData(formulario)
        );

        try {

            resultados.innerHTML = '<p class="text-center">Buscando profesionales...</p>';

            const respuesta = await fetch(
                `${formulario.action}?${parametros}`,
                {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
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

        } catch (error) {

            console.error('Error en la búsqueda:', error);

            resultados.innerHTML = `
                <p class="text-center text-danger">
                    Ocurrió un error al realizar la búsqueda.
                </p>
            `;
        }
    }

    // Formulario de búsqueda por nombre
    formularioNombre.addEventListener('submit', (e) => {
        e.preventDefault();
        document.getElementById('oficio').value = '';
        realizarBusqueda(formularioNombre);
    });

    // Formulario de filtro por oficio
    

    formularioOficio.addEventListener('submit', (e) => {
        e.preventDefault();
        document.getElementById('buscar').value = '';
        realizarBusqueda(formularioOficio);
    });

});