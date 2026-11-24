
document.addEventListener("DOMContentLoaded", () => {

    const provincia = document.getElementById('provincia');
    const ciudad = document.getElementById('ciudad');
    const tipo = document.getElementById('tipo');

    const selectorProvincia = provincia.tomselect;
    const selectorCiudad = ciudad.tomselect;

    function actualizarCiudades(restaurar = false) {

        // Obtener los IDs de las provincias seleccionadas
        const provinciasSeleccionadas = selectorProvincia.getValue();

        // Recuperar las ciudades seleccionadas anteriormente
        const ciudadesSeleccionadas = restaurar
            ? ciudadesAnteriores.map(String)
            : selectorCiudad.getValue();

        // Obtener las ciudades disponibles
        const ciudadesDisponibles = [];

        provinciasDB.forEach(provincia => {

            if (provinciasSeleccionadas.includes(String(provincia.id))) {

                ciudadesDisponibles.push(...provincia.ciudades);

            }

        });

        // Limpiar el selector de ciudades
        selectorCiudad.clear();
        selectorCiudad.clearOptions();

        // Agregar las ciudades disponibles
        ciudadesDisponibles.forEach(ciudad => {

            selectorCiudad.addOption({
                value: String(ciudad.id),
                text: ciudad.nombre
            });

        });

        // Obtener los IDs de las ciudades disponibles
        const idsDisponibles = ciudadesDisponibles.map(ciudad =>
            String(ciudad.id)
        );

        // Conservar las ciudades que siguen estando disponibles
        const ciudadesValidas = ciudadesSeleccionadas.filter(ciudad =>
            idsDisponibles.includes(ciudad)
        );

        selectorCiudad.setValue(ciudadesValidas);

        // Habilitar o deshabilitar el selector
        if (provinciasSeleccionadas.length > 0 && tipo.value === 'profesional') {

            selectorCiudad.enable();

        } else {

            selectorCiudad.disable();

        }

        selectorCiudad.refreshOptions(false);
    }

    // Actualizar las ciudades cuando cambian las provincias
    provincia.addEventListener('change', () => {
        actualizarCiudades();
    });

    // Estado inicial y recuperación de datos anteriores
    actualizarCiudades(true);

});
