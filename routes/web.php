<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersIncomeController;
use App\Http\Controllers\AccessLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EmployeeAccessLogController;
use App\Http\Controllers\EmployeeIncomeController;
use App\Http\Controllers\ReportController;
// use App\Http\Controllers\BiometricController;

Route::get('/', function () {
    if (auth('admin')->check()) {
        return redirect()->route('home');
    }
    return redirect()->route('login');
});

// Página de inicio protegida
Route::get('/home', [HomeController::class, 'index'])
    ->middleware('auth:admin')
    ->name('home');

// Rutas públicas
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.post');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Rutas protegidas por login de admin
Route::middleware(['auth:admin'])->group(function () {
    // Rutas del search
    Route::get('/search', [UsersIncomeController::class, 'getAllUsers'])->name('incomes.search');
    Route::get('/search-user', [UsersIncomeController::class, 'search'])->name('incomes.searchUser');
    Route::get('/incomes/{usersIncome}/edit', [UsersIncomeController::class, 'edit'])->name('incomes.edit');
    Route::put('/incomes/{usersIncome}', [UsersIncomeController::class, 'update'])->name('incomes.update');
    Route::delete('/incomes/{usersIncome}', [UsersIncomeController::class, 'destroy'])->name('incomes.destroy');
    
    // Otras rutas
    Route::get('/incomes/{id}', [UsersIncomeController::class, 'show'])->name('incomes.show');
    Route::get('/create', [UsersIncomeController::class, 'create'])->name('incomes.create');
    Route::get('/incomes', [AccessLogController::class, 'dashboard'])->name('visitor.index');
    Route::post('/incomes', [UsersIncomeController::class, 'store'])->name('incomes.store');

    // Rutas de empleados
    Route::prefix('employee')->group(function () {
        Route::get('/', [EmployeeIncomeController::class, 'dashboard'])->name('employee.index');
        Route::get('/create', [EmployeeIncomeController::class, 'create'])->name('employee.create');
        Route::post('/', [EmployeeIncomeController::class, 'store'])->name('employee.store');
        Route::get('/{employee}/edit', [EmployeeIncomeController::class, 'edit'])->name('employee.edit');
        Route::put('/{employee}', [EmployeeIncomeController::class, 'update'])->name('employee.update');
        Route::delete('/{employee}', [EmployeeIncomeController::class, 'destroy'])->name('employee.destroy');
        Route::get('/{employee}', [EmployeeIncomeController::class, 'show'])->name('employee.show');
        
        // Control de acceso
        Route::post('entry/{employeeId}', [EmployeeAccessLogController::class, 'registerEntry'])->name('employee.entry');
        Route::post('exit/{employeeId}', [EmployeeAccessLogController::class, 'registerExit'])->name('employee.exit');
        
        
        // Búsqueda
       
    });
        Route::get('employee-inside', [EmployeeAccessLogController::class, 'getEmployeesInside'])->name('employee.inside');
         Route::get('/employee-search', [EmployeeIncomeController::class, 'getAllEmployees'])->name('employee.search');
        Route::get('/employee-search-user', [EmployeeIncomeController::class, 'search'])->name('employee.searchUser');
    // Rutas de visitantes
    Route::prefix('visitor')->group(function () {
        Route::post('entry/{visitorId}', [AccessLogController::class, 'registerEntry'])->name('visitor.entry');
        Route::post('exit/{visitorId}', [AccessLogController::class, 'registerExit'])->name('visitor.exit');
        Route::get('visitors-inside', [AccessLogController::class, 'getVisitorsInside'])->name('visitors.inside');
    });

    // Rutas de reportes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/search', [ReportController::class, 'search'])->name('reports.search');
    Route::post('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/export-data', [ReportController::class, 'exportData'])->name('reports.exportData');

    // Rutas biométricas desactivadas temporalmente para este ejercicio.
    // Route::post('/biometric/capture', [BiometricController::class, 'capture'])->name('biometric.capture');
    // Route::post('/biometric/validate', [BiometricController::class, 'validateFingerprint'])->name('biometric.validate');
});
