document.addEventListener('DOMContentLoaded', function () {
    const comprarConPuntosBtns = document.querySelectorAll('.comprar-con-puntos');

    comprarConPuntosBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const productoId = this.dataset.productoId;

            fetch(`/comprar-con-puntos/${productoId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.error,
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: data.success,
                        }).then(() => {
                            location.reload(); // Recargar la página después de la compra
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    });
});