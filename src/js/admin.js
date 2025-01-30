document.addEventListener('DOMContentLoaded', function() {
    iniciarApp();
});

function iniciarApp() {
    searchDate();
}

function searchDate() {
    const fechaInput = document.querySelector('#fecha');
    fechaInput.addEventListener('input', function(e) {
        window.location = `?fecha=${e.target.value}`
    });
}

function confirmEliminar(event) {
    event.preventDefault();

    Swal.fire({
        title: "Confirmación",
        text: "Seguro de eliminar este registro?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.submit();
        }
    });
}