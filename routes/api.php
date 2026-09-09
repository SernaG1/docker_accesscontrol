<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UsersIncomeApi;
// use App\Http\Controllers\BiometricController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Aquí definimos las rutas que usará Laravel para comunicación con el
| servicio biométrico y el control de ingresos.
|--------------------------------------------------------------------------
*/

// Información del usuario autenticado (si usas Sanctum)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Parseo de cadena de scanner
Route::post('/usersincome/parse-scanner', [UsersIncomeApi::class, 'parseScannerString'])
    ->name('usersincome.parseScanner');

// Biometría desactivada temporalmente para este ejercicio.
// Route::post('/capture', [BiometricController::class, 'capture'])
//     ->name('fingerprint.capture');
// Route::post('/identify', [BiometricController::class, 'identify'])
//     ->name('fingerprint.identify');
