@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Registro de Ingreso de Empleado</h2>

    <form action="{{ route('employee.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Scanner input for autocompletion --}}
        {{-- Biometría desactivada temporalmente.
        <div class="mb-3">
            <label for="scanner_string" class="form-label">Entrada del Scanner (cadena)</label>
            <input type="text" id="scanner_string" name="scanner_string" class="form-control" placeholder="Pega la cadena del scanner aquí">
            <button type="button" class="btn btn-secondary mt-2" id="parseScannerBtn">Autocompletar campos</button>
            <div id="scannerError" class="text-danger mt-2" style="display:none;"></div>
        </div> --}}

        {{-- Datos personales --}}
        <div class="mb-3">
            <label for="numero_documento" class="form-label">Número de Documento</label>
            <input type="text" name="numero_documento" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="nombres" class="form-label">Nombres</label>
            <input type="text" name="nombres" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="apellidos" class="form-label">Apellidos</label>
            <input type="text" name="apellidos" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
            <input type="date" name="fecha_nacimiento" class="form-control">
        </div>

        <div class="mb-3">
            <label for="genero" class="form-label">Género</label>
            <select name="genero" class="form-select">
                <option value="" disabled selected>Selecciona</option>
                <option value="M">Masculino</option>
                <option value="F">Femenino</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="rh" class="form-label">Tipo de Sangre (RH)</label>
            <select name="rh" class="form-select">
                <option value="" disabled selected>Selecciona</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
            </select>
        </div>

        {{-- Nuevos campos personales --}}
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" name="telefono" class="form-control">
        </div>

        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-control">
        </div>

        {{-- Datos de contacto de emergencia --}}
        <div class="mb-3">
            <label for="nombre_contacto_emergencia" class="form-label">Nombre del Contacto de Emergencia</label>
            <input type="text" name="nombre_contacto_emergencia" class="form-control">
        </div>

        <div class="mb-3">
            <label for="telefono_contacto_emergencia" class="form-label">Teléfono de Contacto de Emergencia</label>
            <input type="text" name="telefono_contacto_emergencia" class="form-control">
        </div>

        {{-- Datos de visita --}}
        <div class="mb-3">
            <label for="area" class="form-label">Área de pertenencia</label>
            <select name="area" class="form-select">
                <option value="" disabled selected>Selecciona </option>
                <option value="Contabilidad">Contabilidad</option>
                <option value="Operaciones">Operaciones</option>
                <option value="Gerencia">Gerencia</option>
                <option value="Auxiliar administrativo">Auxiliar administrativo</option>
                <option value="Gestión humana">Gestión humana</option>
                <option value="HSEQ">HSEQ</option>
                <option value="Almacén">Almacén</option>
                <option value="Selección">Selección</option>
            </select>
        </div>

                {{-- Estado --}}
        <div class="mb-3 form-check">
            <input type="checkbox" name="estado" id="estado" class="form-check-input" checked>
            <label for="estado" class="form-check-label">Activo</label>
        </div>

        {{-- Captura de imagen --}}
        <div class="mb-3">
            <label class="form-label">Capturar Foto (Webcam)</label>
            <div id="my_camera" class="mb-2" style="width:320px; height:240px; border:1px solid #ccc;"></div>
            <button type="button" class="btn btn-info mt-2" onclick="take_snapshot()">Capturar Foto</button>
            <button type="button" class="btn btn-warning mt-2 ms-2" onclick="requestCameraPermission()">Solicitar Permiso Cámara</button>
        </div>

        {{-- Vista previa --}}
        <div class="mb-3">
            <label class="form-label">Vista previa de la foto</label>
            <div id="my_result"></div>
        </div>

        {{-- Campo oculto para guardar la imagen --}}
        <input type="hidden" name="foto_webcam" id="foto_webcam">

        {{-- Botón de envío --}}
        <button type="submit" class="btn btn-primary">Registrar Ingreso</button>
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

<script>
document.getElementById('parseScannerBtn').addEventListener('click', function() {
    const scannerString = document.getElementById('scanner_string').value.trim();
    const errorDiv = document.getElementById('scannerError');
    errorDiv.style.display = 'none';
    errorDiv.textContent = '';

    if (!scannerString) {
        errorDiv.textContent = 'Por favor ingresa la cadena del scanner.';
        errorDiv.style.display = 'block';
        return;
    }

    fetch('/api/usersincome/parse-scanner', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ scanner_string: scannerString })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => { throw new Error(data.error || 'Error al procesar la cadena'); });
        }
        return response.json();
    })
    .then(data => {
        document.querySelector('input[name="numero_documento"]').value = data.numero_documento || '';
        document.querySelector('input[name="nombres"]').value = data.nombres || '';
        document.querySelector('input[name="apellidos"]').value = data.apellidos || '';
        document.querySelector('select[name="genero"]').value = data.genero || '';
        if (data.fecha_nacimiento) {
            document.querySelector('input[name="fecha_nacimiento"]').value = data.fecha_nacimiento;
        }
    })
    .catch(error => {
        errorDiv.textContent = error.message;
        errorDiv.style.display = 'block';
    });
});
</script>
@endsection
