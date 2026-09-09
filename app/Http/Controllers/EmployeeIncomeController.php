<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAccessLog;
use Illuminate\Http\Request;

class EmployeeIncomeController extends Controller
{
    public function dashboard()
    {
        $user = null;
        $activeLog = null;

        if (request()->has('numero_documento')) {
            $user = Employee::where('numero_documento', request('numero_documento'))->first();

            if ($user) {
                $activeLog = $user->accessLogs()->whereNull('exit_time')->first();
            }
        }
        $employeesInside = EmployeeAccessLog::whereNull('exit_time')->with('employee')->get();
        return view('incomes.employee.index', compact('employeesInside', 'user', 'activeLog'));
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'numero_documento' => 'required|numeric|digits_between:6,12',
        ]);

        $user = Employee::where('numero_documento', $request->numero_documento)->first();
        $employeesInside = EmployeeAccessLog::whereNull('exit_time')->get();

        if ($user) {
            $activeLog = EmployeeAccessLog::where('employee_id', $user->id)
                ->whereNull('exit_time')
                ->first();

            return view('incomes.employee.index', compact('user', 'activeLog', 'employeesInside'));
        } else {
            return view('incomes.employee.index', compact('user', 'employeesInside'));
        }
    }

    public function getAllEmployees()
    {
        $employees = Employee::orderBy('nombres')->paginate(5);
        return view('incomes.employee.search', compact('employees'));
    }

    public function search(Request $request)
    {
    $search = $request->input('nombre');

    $query = Employee::query();

    if ($search) {
        if (is_numeric($search)) {
            $query->where('numero_documento', $search);
        } else {
            $query->where(function ($q) use ($search) {
                $q->where('nombres', 'like', $search . '%')
                  ->orWhere('apellidos', 'like', $search . '%');
            });
        }
    }

    // Paginación con 10 por página
    $employees = $query->orderBy('nombres')->paginate(5)->withQueryString();

    return view('incomes.employee.search', compact('employees'));
    }

    public function create()
    {
        return view('incomes.employee.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero_documento' => 'required|string|max:20|unique:employees,numero_documento',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'genero' => 'nullable|in:M,F',
            'rh' => 'nullable|string|max:5',
            'telefono' => 'nullable|string|max:20',
            'nombre_contacto_emergencia' => 'nullable|string|max:100',
            'telefono_contacto_emergencia' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'area' => 'nullable|string|max:100',
            'foto_webcam' => 'nullable|string',
        ]);

        $employee = new Employee();
        $employee->fill($request->only([
            'numero_documento',
            'nombres',
            'apellidos',
            'fecha_nacimiento',
            'genero',
            'rh',
            'telefono',
            'nombre_contacto_emergencia',
            'telefono_contacto_emergencia',
            'direccion',
            'area',
        ]));

        if ($request->filled('foto_webcam')) {
            $imageData = str_replace('data:image/jpeg;base64,', '', $request->input('foto_webcam'));
            $imageData = base64_decode($imageData);
            $imageName = 'employee_' . $request->input('numero_documento') . '.jpg';
            \Storage::disk('public')->put('photos/' . $imageName, $imageData);
            $employee->foto_webcam = 'photos/' . $imageName;
        }

        $employee->save();

        return redirect()->route('employee.index')->with('success', 'Empleado registrado exitosamente.');
    }

    public function edit(Employee $employee)
    {
        return view('incomes.employee.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'numero_documento' => 'required|string|max:20',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'genero' => 'nullable|in:M,F',
            'rh' => 'nullable|string|max:5',
            'telefono' => 'nullable|string|max:20',
            'nombre_contacto_emergencia' => 'nullable|string|max:100',
            'telefono_contacto_emergencia' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'area' => 'nullable|string|max:100',
            'foto_webcam' => 'nullable|string',
        ]);

        $employee->fill($request->only([
            'numero_documento',
            'nombres',
            'apellidos',
            'fecha_nacimiento',
            'genero',
            'rh',
            'telefono',
            'nombre_contacto_emergencia',
            'telefono_contacto_emergencia',
            'direccion',
            'area',
        ]));

        if ($request->filled('foto_webcam')) {
            $imageData = str_replace('data:image/jpeg;base64,', '', $request->input('foto_webcam'));
            $imageData = base64_decode($imageData);
            $imageName = 'employee_' . $request->input('numero_documento') . '.jpg';
            \Storage::disk('public')->put('photos/' . $imageName, $imageData);
            $employee->foto_webcam = 'photos/' . $imageName;
        }

        $employee->save();

        return redirect()->route('employee.searchUser')->with('success', 'Datos del empleado actualizados correctamente.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employee.searchUser')->with('success', 'Empleado eliminado exitosamente.');
    }

    public function show(Employee $employee)
    {
        return view('incomes.employee.edit', compact('employee'));
    }
}
