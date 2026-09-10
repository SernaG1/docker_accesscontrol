@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar Ingreso de Empleado</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('employee.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Datos personales --}}
        <div class="mb-3">
            <label for="numero_documento" class="form-label">Número de Documento</label>
            <input type="text" name="numero_documento" value="{{ old('numero_documento', $employee->numero_documento) }}" class="form-control" required readonly>
        </div>

        <div class="mb-3">
            <label for="nombres" class="form-label">Nombres</label>
            <input type="text" name="nombres" value="{{ old('nombres', $employee->nombres) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="apellidos" class="form-label">Apellidos</label>
            <input type="text" name="apellidos" value="{{ old('apellidos', $employee->apellidos) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
            <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $employee->fecha_nacimiento) }}" class="form-control">
        </div>

        <div class="mb-3">
            <label for="genero" class="form-label">Género</label>
            <select name="genero" class="form-select">
                <option value="">Selecciona</option>
                <option value="M" {{ old('genero', $employee->genero) == 'M' ? 'selected' : '' }}>Masculino</option>
                <option value="F" {{ old('genero', $employee->genero) == 'F' ? 'selected' : '' }}>Femenino</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="rh" class="form-label">Tipo de Sangre (RH)</label>
            <select name="rh" class="form-select">
                <option value="">Selecciona</option>
                <option value="A+" {{ old('rh', $employee->rh) == 'A+' ? 'selected' : '' }}>A+</option>
                <option value="A-" {{ old('rh', $employee->rh) == 'A-' ? 'selected' : '' }}>A-</option>
                <option value="B+" {{ old('rh', $employee->rh) == 'B+' ? 'selected' : '' }}>B+</option>
                <option value="B-" {{ old('rh', $employee->rh) == 'B-' ? 'selected' : '' }}>B-</option>
                <option value="AB+" {{ old('rh', $employee->rh) == 'AB+' ? 'selected' : '' }}>AB+</option>
                <option value="AB-" {{ old('rh', $employee->rh) == 'AB-' ? 'selected' : '' }}>AB-</option>
                <option value="O+" {{ old('rh', $employee->rh) == 'O+' ? 'selected' : '' }}>O+</option>
                <option value="O-" {{ old('rh', $employee->rh) == 'O-' ? 'selected' : '' }}>O-</option>
            </select>
        </div>

        {{-- Nuevos campos personales --}}
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" name="telefono" value="{{ old('telefono', $employee->telefono) }}" class="form-control">
        </div>

        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" name="direccion" value="{{ old('direccion', $employee->direccion) }}" class="form-control">
        </div>

        {{-- Datos de contacto de emergencia --}}
        <div class="mb-3">
            <label for="nombre_contacto_emergencia" class="form-label">Nombre del Contacto de Emergencia</label>
            <input type="text" name="nombre_contacto_emergencia" value="{{ old('nombre_contacto_emergencia', $employee->nombre_contacto_emergencia) }}" class="form-control">
        </div>

        <div class="mb-3">
            <label for="telefono_contacto_emergencia" class="form-label">Teléfono de Contacto de Emergencia</label>
            <input type="text" name="telefono_contacto_emergencia" value="{{ old('telefono_contacto_emergencia', $employee->telefono_contacto_emergencia) }}" class="form-control">
        </div>

        {{-- Datos de visita --}}
        <div class="mb-3">
            <label for="area" class="form-label">Área a Visitar</label>
            <select name="area" class="form-select">
                <option value="">Selecciona</option>
                <option value="Contabilidad" {{ old('area', $employee->area) == 'Contabilidad' ? 'selected' : '' }}>Contabilidad</option>
                <option value="Operaciones" {{ old('area', $employee->area) == 'Operaciones' ? 'selected' : '' }}>Operaciones</option>
                <option value="Gerencia" {{ old('area', $employee->area) == 'Gerencia' ? 'selected' : '' }}>Gerencia</option>
                <option value="Auxiliar administrativo" {{ old('area', $employee->area) == 'Auxiliar administrativo' ? 'selected' : '' }}>Auxiliar administrativo</option>
                <option value="Gestión humana" {{ old('area', $employee->area) == 'Gestión humana' ? 'selected' : '' }}>Gestión humana</option>
                <option value="HSEQ" {{ old('area', $employee->area) == 'HSEQ' ? 'selected' : '' }}>HSEQ</option>
                <option value="Almacén" {{ old('area', $employee->area) == 'Almacén' ? 'selected' : '' }}>Almacén</option>
                <option value="Selección" {{ old('area', $employee->area) == 'Selección' ? 'selected' : '' }}>Selección</option>
            </select>
        </div>
                {{-- Estado --}}
        <div class="mb-3 form-check">
            <input type="checkbox" name="estado" id="estado" class="form-check-input" {{ old('estado', $employee->estado) ? 'checked' : '' }}>
            <label for="estado" class="form-check-label">Activo</label>
        </div>

        {{-- Captura de imagen --}}
        <div class="mb-3">
            <label class="form-label">Capturar Foto (Webcam)</label>
            <div id="my_camera" class="mb-2" style="width:320px; height:240px; border:1px solid #ccc;"></div>
            <button type="button" class="btn btn-info mt-2" onclick="take_snapshot()">Capturar Foto</button>
        </div>

        {{-- Vista previa --}}
        <div class="mb-3">
            <label class="form-label">Vista previa de la foto</label>
            <div id="my_result">
                @if($employee->foto_webcam && !old('foto_webcam'))
                    {{-- Mostrar imagen guardada solo si no hay imagen nueva tomada --}}
                    <img src="{{ asset('storage/' . $employee->foto_webcam) }}" class="img-thumbnail" alt="Foto del empleado">
                @elseif(old('foto_webcam'))
                    {{-- Mostrar imagen capturada nueva (base64) --}}
                    <img src="{{ old('foto_webcam') }}" class="img-thumbnail" alt="Foto capturada">
                @endif
            </div>
        </div>

        {{-- Campo oculto para guardar la imagen --}}
        <input type="hidden" name="foto_webcam" id="foto_webcam" value="{{ old('foto_webcam', $employee->foto_webcam) }}">

        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

<script>
    // Configurar la webcam
    Webcam.set({
        width: 320,
        height: 240,
        image_format: 'jpeg',
        jpeg_quality: 90
    });

    Webcam.attach('#my_camera');

    function take_snapshot() {
        Webcam.snap(function(data_uri) {
            // Mostrar imagen capturada
            document.getElementById('my_result').innerHTML = '<img src="'+data_uri+'" class="img-thumbnail"/>';

            // Guardar base64 en el input oculto
            document.getElementById('foto_webcam').value = data_uri;
        });
    }

</script>
@endsection
