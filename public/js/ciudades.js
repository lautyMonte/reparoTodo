const provincia = document.getElementById('provincia');
const ciudad = document.getElementById('ciudad');

const ciudades = {
    nqn: ['Centenario', 'Neuquén', 'Plottier', 'San Martín de los Andes'],
    rn: ['Bariloche', 'Cinco Saltos', 'Cipolletti', 'General Roca', ]
};

provincia.addEventListener('change', function () {

    ciudad.innerHTML = '<option value="">Seleccionar ciudad</option>';

    const provinciaSeleccionada = provincia.value;

    if (provinciaSeleccionada !== '') {

        ciudad.disabled = false;

        ciudades[provinciaSeleccionada].forEach(function (nombreCiudad) {

            const opcion = document.createElement('option');

            opcion.value = nombreCiudad;
            opcion.textContent = nombreCiudad;

            ciudad.appendChild(opcion);
        });

    } else {
        ciudad.disabled = true;
    }
});