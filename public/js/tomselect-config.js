document.addEventListener("DOMContentLoaded", () => {

    const selectores = document.querySelectorAll(".tom-select");

    selectores.forEach(selector => {

        new TomSelect(selector, {
            create: false,
            maxItems: 1,
            placeholder: "Seleccioná una opción...",
            allowEmptyOption: true
        });

    });

});