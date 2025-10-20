<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pantalla de Calificación y Gestión</title>
    <style>
        /* --- Estilos Generales --- */
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f4f7f9; color: #333; padding: 20px; }
        .container { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 6px 25px rgba(0,0,0,0.1); max-width: 95%; margin: auto; }
        h1, h2, h3 { color: #1e88e5; text-align: center; }
        h1 { font-size: 2em; }
        h2 { font-size: 1.5em; color: #555; margin-bottom: 40px;}
        h3 { text-align: left; border-bottom: 2px solid #e3f2fd; padding-bottom: 10px; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #dee2e6; padding: 12px; text-align: center; }
        thead th { background-color: #e3f2fd; color: #1565c0; vertical-align: middle; font-weight: 600; }
        tbody tr:nth-child(even) { background-color: #f8f9fa; }

        /* --- Estilos de Secciones --- */
        .management-section { margin-bottom: 50px; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; }
        
        /* --- Estilos de la Parrilla de Calificación --- */
        .student-name { text-align: left; font-weight: 600; white-space: nowrap;}
        input[type="number"] { width: 60px; padding: 5px; text-align: center; border: 1px solid #ccc; border-radius: 4px; transition: border-color 0.3s, box-shadow 0.3s; }
        input[type="number"]:focus { border-color: #1e88e5; box-shadow: 0 0 5px rgba(30, 136, 229, 0.5); outline: none; }
        .status-graded { color: #28a745; font-size: 0.8em; font-weight: bold; display: block; }
        .status-pending { color: #6c757d; font-size: 0.8em; font-weight: normal; display: block; }
        .final-grade { font-weight: bold; font-size: 1.1em; color: #1e88e5; }
        
        /* --- Estilos de Botones y Enlaces --- */
        button, .button { display: inline-block; padding: 12px 25px; background-color: #28a745; color: white; border: none; border-radius: 8px; font-size: 1.1em; cursor: pointer; text-decoration: none; text-align: center; transition: background-color 0.3s; }
        button:hover, .button:hover { background-color: #218838; }
        .button-add-criteria { background-color: #007bff; display: inline-block; margin-top: 15px; }
        .button-add-criteria:hover { background-color: #0056b3; }
        .action-links a { color: #007bff; text-decoration: none; margin: 0 5px; }
        .action-links a:hover { text-decoration: underline; }
        .back-link { display: block; text-align: center; margin-top: 30px; color: #777; }
        .delete-button { background: none; border: none; color: #007bff; text-decoration: none; cursor: pointer; padding: 0; font-size: inherit; font-family: inherit; display: inline; }
        .delete-button:hover { text-decoration: underline; }

        /* --- Estilos de Resultados --- */
        .success-message { font-weight: bold; color: #155724; text-align: center; margin-bottom: 15px; font-size: 1.2em; }
    </style>
</head>
<body>

<div class="container">
    <h1>Pantalla de Calificación y Gestión</h1>
    <h2>Grupo: {{ $grupo->nombre }} | Materia: {{ $materia->nombre }}</h2>


    {{--ADMINISTRACION DE CRITERIOS--}}
    <div class="management-section">
        <h3>Administración de Criterios</h3>
        @if(session('success'))
            <div class="success-message">{{ session('success') }}</div>
        @endif
        <table>
            <thead>
                <tr>
                    <th>Criterio</th>
                    <th>Porcentaje</th>
                    <th>Subtareas</th>
                    <th style="width: 15%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($criterios as $criterio)
                    <tr>
                        <td>{{ $criterio->nombre }}</td>
                        <td>{{ $criterio->porcentaje_decimal }}%</td>
                        <td>{{ $criterio->subtareas->pluck('nombre')->join(', ') ?: 'N/A' }}</td>
                        <td class="action-links">
                            <a href="{{ route('criterios.edit', ['criterio' => $criterio->id, 'grupo_id' => $grupo->id]) }}">Editar</a> | 
                            <form action="{{ route('criterios.destroy', $criterio->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro de eliminar este criterio? Afectará los cálculos.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-button">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px;">
                            <p><strong>Aún no hay criterios de evaluación definidos para este grupo.</strong></p>
                            <p>¡Añade el primero para empezar a calificar!</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <a href="{{ route('criterios.create', ['materia' => $materia->id, 'grupo_id' => $grupo->id]) }}" class="button button-add-criteria">Añadir Criterio</a>
    </div>

    {{--CALIFICACIONES DE ALUMNOS --}}
    <div class="management-section">
        <h3>Calificación de Alumnos</h3>
        <form action="{{ route('calificaciones.store') }}" method="POST">
            @csrf
            <input type="hidden" name="grupo_id" value="{{ $grupo->id }}">
            <input type="hidden" name="materia_id" value="{{ $materia->id }}">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 25%;">Alumno</th>
                        @foreach($criterios as $criterio)
                            <th colspan="{{ $criterio->subtareas->count() > 0 ? $criterio->subtareas->count() : 1 }}">{{ $criterio->nombre }} ({{ $criterio->porcentaje_decimal }}%)</th>
                        @endforeach
                        <th rowspan="2">Promedio Final</th>
                    </tr>
                    <tr>
                        @foreach($criterios as $criterio)
                            @if($criterio->subtareas->count() > 0)
                                @foreach($criterio->subtareas as $subtarea)
                                    <th>{{ $subtarea->nombre }}</th>
                                @endforeach
                            @else
                                 <th>Calificación</th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($alumnos as $alumno)
                    <tr>
                        <td class="student-name">
                            {{ $alumno->apellido_paterno }} {{ $alumno->apellido_materno }} {{ $alumno->nombre }}
                            @if(isset($calificacionesOrganizadas[$alumno->id]))
                                <span class="status-graded">✔ Calificado</span>
                            @else
                                <span class="status-pending">● Pendiente</span>
                            @endif
                        </td>
                        
                        @foreach($criterios as $criterio)
                            @if($criterio->subtareas->count() > 0)
                                @foreach($criterio->subtareas as $subtarea)
                                <td>
                                    @php $nota = $calificacionesOrganizadas[$alumno->id][$criterio->id][$subtarea->id] ?? null; @endphp
                                    <input type="number" step="0.1" max="10" min="0" value="{{ $nota }}" name="calificaciones[{{ $alumno->id }}][{{ $criterio->id }}][{{ $subtarea->id }}][puntuacion]">
                                </td>
                                @endforeach
                            @else
                                <td>
                                    @php $nota = $calificacionesOrganizadas[$alumno->id][$criterio->id]['main'] ?? null; @endphp
                                    <input type="number" step="0.1" max="10" min="0" value="{{ $nota }}" name="calificaciones[{{ $alumno->id }}][{{ $criterio->id }}][puntuacion]">
                                </td>
                            @endif
                        @endforeach

                        <td class="final-grade">
                            {{ $promediosFinales[$alumno->id] ?? 'N/A' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="text-align: center; margin-top: 30px;">
                <button type="submit">Guardar o Actualizar Calificaciones</button>
            </div>
        </form>
    </div>

    <a class="back-link" href="{{ route('grupos.index') }}">Volver a la lista de grupos</a>
</div>

</body>
</html>