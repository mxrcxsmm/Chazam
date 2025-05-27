<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/reportes.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Lista de Reportes - Admin</title>
</head>

<body>
    <!-- Navbar -->
    @include('admin.partials.navbar')

    <div class="container mt-4">
        <h1>Lista de Reportes</h1>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th class="th-id">ID</th>
                    <th class="th-reportador">Reportador</th>
                    <th class="th-reportado">Reportado</th>
                    <th class="th-titulo">Título</th>
                    <th class="th-descripcion">Descripción</th>
                    <th class="th-acciones">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reportes as $reporte)
                    <tr>
                        <td class="td-id">{{ $reporte->id_reporte }}</td>
                        <td class="td-reportador">{{ $reporte->reportador->username ?? 'N/A' }}</td>
                        <td class="td-reportado">{{ $reporte->reportado->username ?? 'N/A' }}</td>
                        <td class="td-titulo">{{ $reporte->titulo }}</td>
                        <td class="td-descripcion">{{ $reporte->descripcion }}</td>
                        <td class="td-acciones">
                            <!-- Botón para eliminar -->
                            <form action="{{ route('admin.reportes.destroy', $reporte->id_reporte) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="border: none; background: none; cursor: pointer;"
                                    title="Eliminar">
                                    <i class="fas fa-trash text-danger"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    <script src="{{ asset('js/reportes.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
