<!-- filepath: c:\wamp64\www\DAW2\MP12\Chazam\Chazam\resources\views\stripe\success.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Exitoso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <script>
        @if (isset($producto))
            // Mostrar la notificación de SweetAlert 2 para productos
            Swal.fire({
                icon: 'success',
                title: '¡Pago Exitoso!',
                text: 'El pago ha sido registrado correctamente. Has comprado el producto: {{ $producto->titulo }}',
                confirmButtonText: 'Volver a la tienda',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirigir a la tienda
                    window.location.href = "{{ route('tienda.index') }}";
                }
            });
        @elseif (isset($mensaje))
            // Mostrar la notificación de SweetAlert 2 para donaciones
            Swal.fire({
                icon: 'success',
                title: '¡Pago Exitoso!',
                text: '{{ $mensaje ?? 'El pago ha sido registrado correctamente.' }}',
                confirmButtonText: 'Volver a la tienda',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirigir a la tienda
                    window.location.href = "{{ route('tienda.index') }}";
                }
            });
        @else
            // Mensaje genérico en caso de que no haya información específica
            Swal.fire({
                icon: 'info',
                title: 'Operación completada',
                text: 'Tu transacción ha sido procesada correctamente.',
                confirmButtonText: 'Volver a la tienda',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirigir a la tienda
                    window.location.href = "{{ route('tienda.index') }}";
                }
            });
        @endif
    </script>
</body>

</html>
