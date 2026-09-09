@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center mb-4">Ingresar Empleado</h2>

    <!-- Formulario de búsqueda -->
<form id="search-form" method="GET" action="{{ route('employee.index') }}">
        <div class="mb-3">
            <label for="numero_documento" class="form-label">Número de Cédula</label>
            <input type="text" name="numero_documento" id="numero_documento" class="form-control" value="{{ old('numero_documento', request('numero_documento')) }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Buscar</button>
        {{-- Biometría desactivada temporalmente.
        <button type="button" class="btn btn-success me-2" onclick="startFingerprintVerification('employee')">
            <i class="fas fa-fingerprint"></i> Validar Huella
        </button> --}}
    </form>

    <hr>

    <!-- Mostrar resultado de la búsqueda -->
    @if(isset($user))
        <!-- Modal para mostrar la información del empleado -->
        <div class="modal fade" id="employeeModal{{ $user->id }}" tabindex="-1" aria-labelledby="employeeModalLabel{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content text-center">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="employeeModalLabel{{ $user->id }}">Credencial de {{ $user->nombres }} {{ $user->apellidos }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            @if($user->foto_webcam)
                                <img src="{{ asset('storage/' . $user->foto_webcam) }}" alt="Foto de {{ $user->nombres }}" class="img-fluid rounded-circle mb-3" style="max-width: 150px;">
                            @else
                                <div class="text-muted">Sin Foto</div>
                            @endif
                        </div>
                        <p><strong>Cédula:</strong> {{ $user->numero_documento }}</p>
                        <p><strong>Nombre:</strong> {{ $user->nombres }} {{ $user->apellidos }}</p>
                        <p><strong>Área:</strong> {{ $user->area }}</p>
                        <p><strong>RH:</strong> {{ $user->rh }}</p>
                        <p><strong>Género:</strong> {{ $user->genero }}</p>
                        <p><strong>Fecha de Nacimiento:</strong> {{ $user->fecha_nacimiento }}</p>

                        <!-- Botones para registrar entrada o salida -->
                        <div class="mt-3">
                            @if(!$activeLog)
                                <form action="{{ route('employee.entry', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success">Registrar Entrada</button>
                                </form>
                            @else
                                <form action="{{ route('employee.exit', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-danger">Registrar Salida</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auto abrir la modal cuando el empleado es encontrado -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('employeeModal{{ $user->id }}'), {
                    keyboard: false
                });
                myModal.show();
            });
        </script>

    @elseif(request()->has('numero_documento'))
        <!-- Modal si el empleado no está registrado -->
        <div class="modal fade" id="notFoundEmployeeModal" tabindex="-1" aria-labelledby="notFoundEmployeeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title" id="notFoundEmployeeModalLabel">Empleado No Encontrado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <p>El empleado no está registrado.</p>
<a href="{{ route('employee.create') }}" class="btn btn-warning">Registrar Nuevo Empleado</a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModalNotFound = new bootstrap.Modal(document.getElementById('notFoundEmployeeModal'), {
                    keyboard: false
                });
                myModalNotFound.show();
            });
        </script>
    @endif

    <hr>

    <!-- Tabla de empleados actualmente dentro -->
    <h4 class="mt-4">Empleados actualmente dentro</h4>
    @if($employeesInside->count())
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Cédula</th>
                <th>Nombre</th>
                <th>Área</th>
                <th>Hora de Entrada</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employeesInside as $log)
            <tr>
                <td>{{ $log->employee->numero_documento }}</td>
                <td>{{ $log->employee->nombres }} {{ $log->employee->apellidos }}</td>
                <td>{{ $log->employee->area }}</td>
                <td>{{ $log->entry_time ? $log->entry_time->format('d/m/Y H:i:s') : '---' }}</td>
                <td>
                    <form action="{{ route('employee.exit', $log->employee->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">Registrar Salida</button>
                    </form>

                    <button class="btn btn-info mt-2" data-bs-toggle="modal" data-bs-target="#credencialModal{{ $log->employee->id }}">Mostrar Credencial</button>

                    <!-- Modal de credencial -->
                    <div class="modal fade" id="credencialModal{{ $log->employee->id }}" tabindex="-1" aria-labelledby="credencialModalLabel{{ $log->employee->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-sm">
                            <div class="modal-content text-center">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="credencialModalLabel{{ $log->employee->id }}">Credencial de {{ $log->employee->nombres }} {{ $log->employee->apellidos }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        @if($log->employee->foto_webcam)
                                            <img src="{{ asset('storage/' . $log->employee->foto_webcam) }}" alt="Foto de {{ $log->employee->nombres }}" class="img-fluid rounded-circle mb-3" style="max-width: 150px;">
                                        @else
                                            <div class="text-muted">Sin Foto</div>
                                        @endif
                                    </div>
                                    <p>
                                        <strong>Estado:</strong>
                                        @if($log->employee->estado)
                                            <button class="btn btn-success btn-sm" disabled>Activo</button>
                                        @else
                                            <button class="btn btn-danger btn-sm" disabled>Inactivo</button>
                                        @endif
                                    </p>
                                    <p><strong>Cédula:</strong> {{ $log->employee->numero_documento }}</p>
                                    <p><strong>Nombre:</strong> {{ $log->employee->nombres }} {{ $log->employee->apellidos }}</p>
                                    <p><strong>Área:</strong> {{ $log->employee->area }}</p>
                                    <p><strong>RH:</strong> {{ $log->employee->rh }}</p>
                                    <p><strong>Género:</strong> {{ $log->employee->genero }}</p>
                                    <p><strong>Fecha de Nacimiento:</strong> {{ $log->employee->fecha_nacimiento }}</p>

                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <p>No hay empleados dentro en este momento.</p>
    @endif
</div>

<!-- Modal de Validación -->
<div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="validationModalLabel">Resultado de Validación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="validationResultBody">
        <!-- El resultado se insertará aquí -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
@endsection

<div id="employeeModalContainer"></div>

@section('scripts')
<script>
/* function startFingerprintVerification(source = "employee") {
    const validationResultBody = document.getElementById('validationResultBody');
    const validationModal = new bootstrap.Modal(document.getElementById('validationModal'));
    const csrfToken = '{{ csrf_token() }}';

    // Mostrar modal con spinner
    validationResultBody.innerHTML = '<div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="visually-hidden">Cargando...</span></div></div>';
    validationModal.show();

    // Llamada al backend para identificar huella
    fetch(`/api/identify`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ source })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.matched_user) {
            showEmployeeModal(data.matched_user, csrfToken);
            validationResultBody.innerHTML = `<p class="text-success">✅ Huella validada correctamente</p>`;
        } else {
            validationResultBody.innerHTML = `<p class="text-danger">❌ ${data.message || 'No se encontró coincidencia'}</p>`;
        }
    })
    .catch(error => {
        validationResultBody.innerHTML = `<p class="text-danger">Error: ${error.message}</p>`;
    });
}

// Función para mostrar modal del empleado
function showEmployeeModal(user, csrfToken) {
    const modalContainer = document.getElementById('employeeModalContainer');
    modalContainer.innerHTML = `
        <div class="modal fade" id="employeeModalDynamic" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content text-center">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Credencial de ${user.nombres} ${user.apellidos}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            ${user.foto_webcam ? `<img src="/storage/${user.foto_webcam}" class="img-fluid rounded-circle mb-3" style="max-width: 150px;">` : '<div class="text-muted">Sin Foto</div>'}
                        </div>
                        <p><strong>Cédula:</strong> ${user.numero_documento}</p>
                        <p><strong>Nombre:</strong> ${user.nombres} ${user.apellidos}</p>
                        <p><strong>Área:</strong> ${user.area}</p>
                        <p><strong>RH:</strong> ${user.rh}</p>
                        <p><strong>Género:</strong> ${user.genero}</p>
                        <p><strong>Fecha de Nacimiento:</strong> ${user.fecha_nacimiento}</p>
                        <div class="mt-3">
                            ${user.active_log
                                ? `<form action="/employee/exit/${user.id}" method="POST" class="d-inline">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <button class="btn btn-danger">Registrar Salida</button>
                                  </form>`
                                : `<form action="/employee/entry/${user.id}" method="POST" class="d-inline">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <button class="btn btn-success">Registrar Entrada</button>
                                  </form>`
                            }
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    const modal = new bootstrap.Modal(document.getElementById('employeeModalDynamic'));
    modal.show();
} */
</script>
</script>
@endsection
