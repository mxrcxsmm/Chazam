<!-- filepath: c:\wamp64\www\DAW2\MP12\Chazam\Chazam\resources\views\stripe\success.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Exitoso</title>
</head>
<body>
    <h1>¡Pago Exitoso!</h1>
    <p>Has comprado el producto: {{ $producto->titulo }}</p>
    <a href="{{ route('tienda.index') }}">Volver a la tienda</a>
</body>
</html>