@extends('layouts.app')

@section('content')

{{-- ESTILOS CSS --}}
<style>
    /* Inputs numéricos limpios y centrados */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; margin: 0; 
    }
    input[type=number] {
        -moz-appearance: textfield;
        text-align: center;
        font-weight: 600;
        border: 1px solid #ced4da;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    /* COLORES DE ESTADO */
    .input-sin-cambios { background-color: #fff; }
    
    /* AMARILLO: El usuario escribió algo nuevo pero NO ha guardado */
    .input-modificado { 
        background-color: #fff3cd !important; 
        border-color: #ffc107 !important; 
        box-shadow: 0 0 5px rgba(255, 193, 7, 0.5);
    } 
    
    /* VERDE: Confirmado que se guardó en la Base de Datos */
    .input-guardado { 
        background-color: #d1e7dd !important; 
        border-color: #198754 !important; 
    } 
    
    /* ROJO: Error al intentar guardar */
    .input-error { background-color: #f8d7da !important; border-color: #dc3545 !important; }

    /* Estilos generales */
    .table-hover tbody tr:hover { background-color: #f8f9fa; }
    
    /* Colores del Promedio Final */
    .promedio-reprobado { color: #dc3545; font-weight: 800; } /* Rojo fuerte */
    .promedio-aprobado { color: #198754; font-weight: 800; }  /* Verde fuerte */
    
    .acciones-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 20px; background: #fff; padding: 15px;
        border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
</style>

<div class="container">
    
    {{-- ENCABEZADO Y BOTONES --}}
    <div class="acciones-header">
        <div>
            <h2 class="mb-0">Hoja de Calificación</h2>
            <small class="text-muted">
                {{ $grupo->nombre }} | {{ $materia->nombre }} | Unidad: {{ $unidad->nombre }}
            </small>
        </div>
        <div class="d-flex gap-2">
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            
            {{-- BOTÓN GRANDE DE GUARDAR --}}
            <button type="button" class="btn btn-primary btn-lg px-4" id="btn-guardar-todo">
                <i class="bi bi-save"></i> GUARDAR CAMBIOS
            </button>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 align-middle" id="tabla-calificaciones">
                    <thead class="table-light text-center">
                        <tr>
                            <th class="text-start" style="min-width: 250px;">Alumno</th>
                            
                            {{-- Columnas de Instrumentos (Examen, Tarea, etc.) --}}
                            @foreach ($instrumentos as $instrumento)
                                <th style="min-width: 100px;" data-porcentaje="{{ $instrumento->porcentaje }}">
                                    {{ $instrumento->nombre }}
                                    <div style="font-size: 0.8em; color: #666;">{{ $instrumento->porcentaje }}%</div>
                                </th>
                            @endforeach
                            
                            <th class="table-active" style="width: 120px;">Promedio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($alumnos as $alumno)
                            <tr>
                                <td class="ps-3 fw-medium">
                                    {{ $alumno->apellido_paterno }} {{ $alumno->apellido_materno }} {{ $alumno->nombre }}
                                </td>

                                {{-- Celdas de Calificación --}}
                                @foreach ($instrumentos as $instrumento)
                                    @php
                                        // Obtener calificación si existe
                                        $key = $alumno->id . '-' . $instrumento->id;
                                        $val = $calificaciones->get($key)->calificacion_obtenida ?? '';
                                    @endphp
                                    <td class="text-center p-1">
                                        <input 
                                            type="number" 
                                            class="form-control calificacion-input input-sin-cambios" 
                                            value="{{ $val }}"
                                            step="0.1" min="0" max="10"
                                            data-alumno-id="{{ $alumno->id }}"
                                            data-instrumento-id="{{ $instrumento->id }}"
                                            data-original-value="{{ $val }}" 
                                            placeholder="-"
                                        >
                                    </td>
                                @endforeach
                                
                                {{-- Celda de Promedio --}}
                                <td class="text-center table-active fw-bold fs-5" data-promedio-id="{{ $alumno->id }}">--</td>
                            </tr>
                        @empty
                            <tr><td colspan="100%" class="text-center p-4">No hay alumnos en este grupo.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Token de seguridad obligatorio para Laravel --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

@endsection

@push('scripts')
{{-- Librería de Alertas (SweetAlert2) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // RUTA IMPORTANTE: Verifica que coincida con tu web.php
    const urlGuardar = '{{ route("calificaciones.guardar.unidad") }}';
    
    const btnGuardarTodo = document.getElementById('btn-guardar-todo');
    const inputs = document.querySelectorAll('.calificacion-input');

    // 1. INICIO: Calcular promedios visuales al cargar
    recalcularTodosLosPromedios();

    // 2. CONFIGURACIÓN DE CADA INPUT
    inputs.forEach(input => {
        
        // EVENTO: Al escribir (Feedback visual inmediato)
        input.addEventListener('input', function() {
            let val = parseFloat(this.value);
            
            // Validar límites visualmente
            if (val > 10) this.value = 10;
            if (val < 0) this.value = 0;
            
            // Si cambió el valor original, poner AMARILLO
            const original = this.dataset.originalValue;
            if (this.value != original) {
                this.classList.remove('input-guardado', 'input-sin-cambios');
                this.classList.add('input-modificado');
            } else {
                this.classList.remove('input-modificado');
                this.classList.add('input-sin-cambios');
            }

            // Actualizar promedio en tiempo real
            recalcularPromedioFila(this.closest('tr'));
        });

        // EVENTO: Al salir de la celda (Formateo y Truncado)
        input.addEventListener('blur', function() {
            if (this.value !== "") {
                // AQUÍ USAMOS LA FUNCIÓN DE TRUNCAR (CORTAR)
                // 2 decimales. Cambia el 2 por 1 si prefieres un solo decimal.
                this.value = truncarValor(this.value, 2); 
            }
        });

        // EVENTO: Tecla Enter para saltar
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                // Opcional: Código para enfocar el siguiente input
            }
        });
    });

    // 3. BOTÓN "GUARDAR TODO"
    btnGuardarTodo.addEventListener('click', async function() {
        // Solo buscamos los inputs que están en AMARILLO (modificados)
        const inputsModificados = document.querySelectorAll('.calificacion-input.input-modificado');

        if (inputsModificados.length === 0) {
            Swal.fire({
                icon: 'info', title: 'Sin cambios', 
                text: 'No hay calificaciones nuevas pendientes de guardar.',
                timer: 2000, showConfirmButton: false
            });
            return;
        }

        // Bloquear botón para evitar doble clic
        btnGuardarTodo.disabled = true;
        const textoOriginal = btnGuardarTodo.innerHTML;
        btnGuardarTodo.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

        let errores = 0;

        // Guardar en paralelo
        const promesas = Array.from(inputsModificados).map(async (input) => {
            try {
                await guardarCalificacionIndividual(input);
                
                // Si tuvo éxito:
                input.dataset.originalValue = input.value; // Actualizamos el "original"
                input.classList.remove('input-modificado', 'input-error');
                input.classList.add('input-guardado'); // VERDE
                
            } catch (error) {
                errores++;
                input.classList.add('input-error'); // ROJO
                console.error(error);
            }
        });

        // Esperar a que todos terminen
        await Promise.all(promesas);

        // Restaurar botón
        btnGuardarTodo.disabled = false;
        btnGuardarTodo.innerHTML = textoOriginal;

        // Mensaje final
        if (errores === 0) {
            Swal.fire({
                icon: 'success', title: '¡Guardado!',
                text: 'Todas las calificaciones se actualizaron correctamente.',
                timer: 1500, showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'warning', title: 'Atención',
                text: `Hubo error al guardar ${errores} calificaciones. Revisa las celdas rojas.`
            });
        }
    });

    // 4. FUNCIÓN AUXILIAR PARA GUARDAR/BORRAR
    async function guardarCalificacionIndividual(inputElement) {
        let calificacion = inputElement.value;
        
        // Si está vacío, enviamos null (El controlador debe hacer ->delete())
        if (calificacion === "") {
            calificacion = null;
        }

        const response = await fetch(urlGuardar, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                alumno_id: inputElement.dataset.alumnoId,
                instrumento_id: inputElement.dataset.instrumentoId,
                calificacion: calificacion 
            })
        });

        const data = await response.json();

        if (!response.ok) throw new Error(data.message || 'Error del servidor');

        // Si se borró (null), quitamos el color verde y dejamos blanco
        if (calificacion === null) {
            inputElement.classList.remove('input-guardado');
            inputElement.classList.add('input-sin-cambios');
        }

        return data;
    }

    // 5. FUNCIÓN ESPECIAL PARA TRUNCAR DECIMALES (NO REDONDEAR)
    function truncarValor(valor, decimales) {
        let num = parseFloat(valor);
        if (isNaN(num)) return "";
        
        let str = num.toString();
        // Si tiene punto decimal
        if (str.indexOf('.') !== -1) {
            let partes = str.split('.');
            let decimalCortado = partes[1].substring(0, decimales);
            // Si quedó corta (ej. 5.), opcionalmente rellenar con 0
            if (decimalCortado.length < decimales) {
                 decimalCortado = decimalCortado.padEnd(decimales, '0');
            }
            return parseFloat(partes[0] + '.' + decimalCortado).toFixed(decimales);
        }
        // Si es entero
        return num.toFixed(decimales);
    }

    // 6. CÁLCULO DE PROMEDIOS (Suma Acumulativa)
    function recalcularPromedioFila(fila) {
        const inputsFila = fila.querySelectorAll('.calificacion-input');
        const celdaPromedio = fila.querySelector('[data-promedio-id]');
        const ths = document.querySelectorAll('#tabla-calificaciones thead th[data-porcentaje]');
        
        let sumaAcumulada = 0;
        
        inputsFila.forEach((input, index) => {
            let calif = parseFloat(input.value);
            // Si está vacío, cuenta como 0 para el promedio acumulado
            if (isNaN(calif)) calif = 0;
            
            const porcentaje = parseFloat(ths[index].dataset.porcentaje);
            
            // Fórmula: (Nota * Porcentaje) / 100
            sumaAcumulada += (calif * porcentaje) / 100;
        });

        // Tope lógico 10
        if (sumaAcumulada > 10) sumaAcumulada = 10;

        // TRUNCAR EL PROMEDIO FINAL TAMBIÉN (Para ser consistentes con Excel)
        // Usamos la misma lógica de truncar a 1 o 2 decimales
        const promedioFinal = truncarValor(sumaAcumulada, 2); // 2 decimales
        
        celdaPromedio.textContent = promedioFinal;
        
        // Estilos condicionales
        celdaPromedio.className = 'text-center fw-bold fs-5 align-middle'; 
        if (parseFloat(promedioFinal) < 7) {  // Criterio de reprobado (ej. 6 o 7)
            celdaPromedio.classList.add('promedio-reprobado');
        } else {
            celdaPromedio.classList.add('promedio-aprobado');
        }
    }

    function recalcularTodosLosPromedios() {
        const filas = document.querySelectorAll('#tabla-calificaciones tbody tr');
        filas.forEach(fila => {
            if (fila.querySelector('.calificacion-input')) recalcularPromedioFila(fila);
        });
    }

    // 7. PREVENIR SALIDA ACCIDENTAL
    window.addEventListener('beforeunload', function (e) {
        const modificados = document.querySelectorAll('.input-modificado').length;
        if (modificados > 0) {
            e.preventDefault();
            e.returnValue = ''; // Muestra alerta estándar del navegador
        }
    });
});
</script>
@endpush