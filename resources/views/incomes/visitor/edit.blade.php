@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar Ingreso de Visitante</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('incomes.update', $usersIncome->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Datos personales --}}
        <div class="mb-3">
            <label for="numero_documento" class="form-label">Número de Documento</label>
            <input type="text" name="numero_documento" value="{{ old('numero_documento', $usersIncome->numero_documento) }}" class="form-control" required readonly>
        </div>

        <div class="mb-3">
            <label for="nombres" class="form-label">Nombres</label>
            <input type="text" name="nombres" value="{{ old('nombres', $usersIncome->nombres) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="apellidos" class="form-label">Apellidos</label>
            <input type="text" name="apellidos" value="{{ old('apellidos', $usersIncome->apellidos) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
            <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $usersIncome->fecha_nacimiento) }}" class="form-control">
        </div>

        <div class="mb-3">
            <label for="genero" class="form-label">Género</label>
            <select name="genero" class="form-select">
                <option value="">Selecciona</option>
                <option value="M" {{ old('genero', $usersIncome->genero) == 'M' ? 'selected' : '' }}>Masculino</option>
                <option value="F" {{ old('genero', $usersIncome->genero) == 'F' ? 'selected' : '' }}>Femenino</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="rh" class="form-label">Tipo de Sangre (RH)</label>
            <select name="rh" class="form-select">
                <option value="">Selecciona</option>
                <option value="A+" {{ old('rh', $usersIncome->rh) == 'A+' ? 'selected' : '' }}>A+</option>
                <option value="A-" {{ old('rh', $usersIncome->rh) == 'A-' ? 'selected' : '' }}>A-</option>
                <option value="B+" {{ old('rh', $usersIncome->rh) == 'B+' ? 'selected' : '' }}>B+</option>
                <option value="B-" {{ old('rh', $usersIncome->rh) == 'B-' ? 'selected' : '' }}>B-</option>
                <option value="AB+" {{ old('rh', $usersIncome->rh) == 'AB+' ? 'selected' : '' }}>AB+</option>
                <option value="AB-" {{ old('rh', $usersIncome->rh) == 'AB-' ? 'selected' : '' }}>AB-</option>
                <option value="O+" {{ old('rh', $usersIncome->rh) == 'O+' ? 'selected' : '' }}>O+</option>
                <option value="O-" {{ old('rh', $usersIncome->rh) == 'O-' ? 'selected' : '' }}>O-</option>
            </select>
        </div>

        {{-- Nuevos campos personales --}}
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" name="telefono" value="{{ old('telefono', $usersIncome->telefono) }}" class="form-control">
        </div>

        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" name="direccion" value="{{ old('direccion', $usersIncome->direccion) }}" class="form-control">
        </div>

        {{-- Datos de contacto de emergencia --}}
        <div class="mb-3">
            <label for="nombre_contacto_emergencia" class="form-label">Nombre del Contacto de Emergencia</label>
            <input type="text" name="nombre_contacto_emergencia" value="{{ old('nombre_contacto_emergencia', $usersIncome->nombre_contacto_emergencia) }}" class="form-control">
        </div>

        <div class="mb-3">
            <label for="telefono_contacto_emergencia" class="form-label">Teléfono de Contacto de Emergencia</label>
            <input type="text" name="telefono_contacto_emergencia" value="{{ old('telefono_contacto_emergencia', $usersIncome->telefono_contacto_emergencia) }}" class="form-control">
        </div>

        {{-- Datos de visita --}}
        <div class="mb-3">
            <label for="area" class="form-label">Área a Visitar</label>
            <select name="area" class="form-select">
                <option value="">Selecciona</option>
                <option value="Contabilidad" {{ old('area', $usersIncome->area) == 'Contabilidad' ? 'selected' : '' }}>Contabilidad</option>
                <option value="Operaciones" {{ old('area', $usersIncome->area) == 'Operaciones' ? 'selected' : '' }}>Operaciones</option>
                <option value="Gerencia" {{ old('area', $usersIncome->area) == 'Gerencia' ? 'selected' : '' }}>Gerencia</option>
                <option value="Auxiliar administrativo" {{ old('area', $usersIncome->area) == 'Auxiliar administrativo' ? 'selected' : '' }}>Auxiliar administrativo</option>
                <option value="Gestión humana" {{ old('area', $usersIncome->area) == 'Gestión humana' ? 'selected' : '' }}>Gestión humana</option>
                <option value="HSEQ" {{ old('area', $usersIncome->area) == 'HSEQ' ? 'selected' : '' }}>HSEQ</option>
                <option value="Almacén" {{ old('area', $usersIncome->area) == 'Almacén' ? 'selected' : '' }}>Almacén</option>
                <option value="Selección" {{ old('area', $usersIncome->area) == 'Selección' ? 'selected' : '' }}>Selección</option>
            </select>
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
                @if($usersIncome->foto_webcam && !old('foto_webcam'))
                    {{-- Mostrar imagen guardada solo si no hay imagen nueva tomada --}}
                    <img src="{{ asset('storage/' . $usersIncome->foto_webcam) }}" class="img-thumbnail" alt="Foto del visitante">
                @elseif(old('foto_webcam'))
                    {{-- Mostrar imagen capturada nueva (base64) --}}
                    <img src="{{ old('foto_webcam') }}" class="img-thumbnail" alt="Foto capturada">
                @endif
            </div>
        </div>

        {{-- Campo oculto para guardar la imagen --}}
        <input type="hidden" name="foto_webcam" id="foto_webcam" value="{{ old('foto_webcam', $usersIncome->foto_webcam) }}">

        {{-- Biometría desactivada temporalmente.
        <div class="mb-3">
            <label for="tipo_dedo" class="form-label">Seleccione el Tipo de Dedo</label>
            <select name="tipo_dedo" id="tipo_dedo" class="form-select">
                <option value="" disabled selected>Seleccione</option>
                <option value="pulgar_izquierdo">Pulgar Izquierdo</option>
                <option value="indice_izquierdo">Índice Izquierdo</option>
                <option value="medio_izquierdo">Medio Izquierdo</option>
                <option value="anular_izquierdo">Anular Izquierdo</option>
                <option value="meñique_izquierdo">Meñique Izquierdo</option>
                <option value="pulgar_derecho">Pulgar Derecho</option>
                <option value="indice_derecho">Índice Derecho</option>
                <option value="medio_derecho">Medio Derecho</option>
                <option value="anular_derecho">Anular Derecho</option>
                <option value="meñique_derecho">Meñique Derecho</option>
            </select>
        </div> --}}

        {{-- Biometría desactivada temporalmente.
        <div class="mb-3">
            <label class="form-label">Validación Biométrica</label>
            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-success me-2" onclick="enrollBiometric()">
                    <i class="fas fa-fingerprint"></i> Enrolar Validación Biométrica
                </button>
                <div id="biometricSpinner" class="spinner-border text-primary" style="display:none;" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
            <div id="biometricResult" class="mt-2"></div>
            <input type="hidden" name="biometric_data" id="biometric_data">
            <input type="hidden" name="user_type" value="visitor">
        </div> --}}

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

    /* async function enrollBiometric() {
        const btn = document.querySelector('button[onclick="enrollBiometric()"]');
        const spinner = document.getElementById('biometricSpinner');
        const resultDiv = document.getElementById('biometricResult');

        btn.disabled = true;
        spinner.style.display = 'inline-block';
        resultDiv.textContent = '';

        // Create timeout controller
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout

        try {
            const response = await fetch('/api/capture', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    tipo_dedo: document.querySelector('select[name="tipo_dedo"]').value
                }),
                signal: controller.signal
            });

            clearTimeout(timeoutId);

            const data = await response.json();

            if (response.ok && data.status === 'success') {
                resultDiv.textContent = 'Huella capturada correctamente.';
                document.getElementById('biometric_data').value = data.huella;
            } else {
                resultDiv.textContent = data.message || 'Error en la captura biométrica.';
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                resultDiv.textContent = 'Tiempo de espera agotado. Por favor, intente nuevamente.';
            } else {
                resultDiv.textContent = 'Error de conexión: ' + error.message;
            }
        } finally {
            btn.disabled = false;
            spinner.style.display = 'none';
            clearTimeout(timeoutId);
        }
    } */
</script>
@endsection
