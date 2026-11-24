
document.addEventListener("DOMContentLoaded", () => {

    const selectores = document.querySelectorAll(".tom-select");

    selectores.forEach(selector => {

        new TomSelect(selector, {

            create: false,

            maxItems: selector.multiple ? null : 1,

            plugins: selector.multiple ? ['remove_button'] : [],

            allowEmptyOption: false,

            placeholder: selector.options[0]?.textContent || "Seleccioná una opción...",

            render: {
                no_results: function () {
                    return '<div class="no-results">No se encontraron resultados</div>';
                }
            }

        });

    });

});
