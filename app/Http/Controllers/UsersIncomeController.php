<?php

namespace App\Http\Controllers;

use App\Models\UsersIncome;
use App\Models\AccessLog;
use Illuminate\Http\Request;

use App\Models\Employee;
use App\Models\EmployeeAccessLog;

class UsersIncomeController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'numero_documento' => 'required|numeric|digits_between:6,12',
        ]);

        $user = UsersIncome::where('numero_documento', $request->numero_documento)->first();
        $visitorsInside = AccessLog::whereNull('exit_time')->get();

        if ($user) {
            $activeLog = AccessLog::where('visitor_id', $user->id)
                ->whereNull('exit_time')
                ->first();

            return view('visitor.index', compact('user', 'activeLog', 'visitorsInside'));
        } else {
            return view('visitor.index', compact('user', 'visitorsInside'));
        }
    }

    public function getAllUsers()
    {
        $visitors = UsersIncome::orderBy('nombres')->paginate(5);
        return view('incomes.visitor.search', compact('visitors'));
    }

    public function search(Request $request)
    {
     $search = $request->input('nombre');

    $query = UsersIncome::query();

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

    $visitors = $query->orderBy('nombres')->paginate(5)->withQueryString();

    return view('incomes.visitor.search', compact('visitors'));
    }

    public function create()
    {
        return view('incomes.visitor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero_documento' => 'required|string|max:20|unique:users_incomes,numero_documento',
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

        $income = new UsersIncome();
        $income->fill($request->only([
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
            $imageName = 'visitor_' . $request->input('numero_documento') . '.jpg';
            \Storage::disk('public')->put('photos/' . $imageName, $imageData);
            $income->foto_webcam = 'photos/' . $imageName;
        }

        $income->save();

        return redirect()->route('visitor.index')->with('success', 'Visitante registrado exitosamente.');
    }

    public function edit(UsersIncome $usersIncome)
    {
        return view('incomes.visitor.edit', compact('usersIncome'));
    }

    public function update(Request $request, UsersIncome $usersIncome)
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

        $usersIncome->fill($request->only([
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

        $fotoWebcam = $request->input('foto_webcam');

        if ($fotoWebcam && str_starts_with($fotoWebcam, 'data:image')) {
            // Se tomó una nueva foto (base64)
            $imageData = str_replace('data:image/jpeg;base64,', '', $fotoWebcam);
            $imageData = base64_decode($imageData);
            $imageName = 'visitor_' . $request->input('numero_documento') . '.jpg';
            \Storage::disk('public')->put('photos/' . $imageName, $imageData);
            $usersIncome->foto_webcam = 'photos/' . $imageName;
        }
        $usersIncome->save();

        return redirect()->route('incomes.searchUser')->with('success', 'Datos del visitante actualizados correctamente.');
    }

    public function destroy(UsersIncome $usersIncome)
    {
        $usersIncome->delete();

        return redirect()->route('incomes.searchUser')->with('success', 'Visitante eliminado exitosamente.');
    }

    public function show($id)
    {
        $usersIncome = UsersIncome::findOrFail($id);
        return view('incomes.edit', compact('usersIncome'));
    }

}