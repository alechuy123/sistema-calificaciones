<h1>Gestión de Grupos</h1>

<a href="{{ route('grupos.create') }}">Crear Nuevo Grupo</a>
<br><br>

@if ($message = Session::get('success'))
    <div style="color: green;">
        <p>{{ $message }}</p>
    </div>
@endif

@if($grupos->isEmpty())
    <p>No hay grupos registrados. <a href="{{ route('grupos.create') }}">¡Crea el primero!</a></p>
@else
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del Grupo</th>
                <th>Carrera</th>
                <th>Materia</th>
                <th>Cuatrimestre</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($grupos as $grupo)
                <tr>
                    <td>{{ $grupo->id }}</td>
                    <td>{{ $grupo->nombre }}</td>
                    <td>{{ $grupo->carrera->nombre }}</td>
                    <td>{{ $grupo->materia->nombre }}</td>
                    <td>{{ $grupo->cuatrimestre->nombre }}</td>

                    {{-- Muestra el estado (del proyecto final) --}}
                    <td>
                        @if ($grupo->esta_activo)
                            <span style="color: green;">✅ Activo</span>
                        @else
                            <span style="color: red;">❌ Desactivado</span>
                        @endif
                    </td>

                    {{-- ACCIONES FUSIONADAS --}}
                    <td>
                        {{-- ========================================================== --}}
                        {{-- === CÓDIGO AÑADIDO (del proyecto antiguo) === --}}
                        {{-- ========================================================== --}}
                        <a href="{{ route('calificaciones.create', ['grupo' => $grupo->id, 'materia' => $grupo->materia->id]) }}">
                            <strong>Calificar</strong>
                        </a> |
                        {{-- ========================================================== --}}


                        <a href="{{ route('grupos.show', $grupo->id) }}">Matricular</a>
                        |
                        <a href="{{ route('grupos.edit', $grupo->id) }}">Editar</a>
                        |

                        {{-- Lógica de Activar/Desactivar (del proyecto final) --}}
                        @if ($grupo->esta_activo)
                            {{-- Opción para DESACTIVAR --}}
                            <form action="{{ route('grupos.destroy', $grupo->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('¿Quieres DESACTIVAR este grupo?')" style="background:none; border:none; color:red; cursor:pointer;">Desactivar</button>
                            </form>
                        @else
                            {{-- Opción para REACTIVAR --}}
                            <form action="{{ route('grupos.update', $grupo->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PUT')
                                {{-- Campos ocultos para pasar la validación y reactivar --}}
                                <input type="hidden" name="esta_activo" value="1">
                                <input type="hidden" name="nombre" value="{{ $grupo->nombre }}">
                                <input type="hidden" name="materia_id" value="{{ $grupo->materia_id }}">
                                <input type="hidden" name="cuatrimestre_id" value="{{ $grupo->cuatrimestre_id }}">
                                <input type="hidden" name="carrera_id" value="{{ $grupo->carrera_id }}">

                                <button type="submit" onclick="return confirm('¿Quieres REACTIVAR este grupo?')" style="background:none; border:none; color:blue; cursor:pointer;">Reactivar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif