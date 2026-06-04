<?php

namespace App\Modules\Seg\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Seg\Models\Rol;
use App\Modules\Seg\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(Request $request): View
    {
        $usuarios = Usuario::with(['roles', 'consultor'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('nombres', 'like', "%{$request->q}%")
                        ->orWhere('apellidos', 'like', "%{$request->q}%")
                        ->orWhere('email', 'like', "%{$request->q}%");
                });
            })
            ->latest('id_usuario')
            ->paginate(15)
            ->withQueryString();

        return view('seg.usuarios.index', compact('usuarios'));
    }

    public function create(): View
    {
        return view('seg.usuarios.create', [
            'roles' => Rol::where('activo', true)->orderBy('nombre')->get(),
            'consultores' => Consultor::orderBy('nombres')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:seg_usuarios,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'id_consultor' => ['nullable', 'integer', 'exists:tbl_consultor,id_consultor', 'unique:seg_usuarios,id_consultor'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:seg_roles,id_rol'],
        ]);

        $usuario = Usuario::create($data + ['usuario_crea' => auth()->id(), 'activo' => true]);
        $usuario->roles()->sync($request->input('roles', []));

        return redirect()->route('seg.usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(Usuario $usuario): View
    {
        return view('seg.usuarios.edit', [
            'usuario' => $usuario->load('roles'),
            'roles' => Rol::where('activo', true)->orderBy('nombre')->get(),
            'consultores' => Consultor::orderBy('nombres')->get(),
        ]);
    }

    public function update(Request $request, Usuario $usuario): RedirectResponse
    {
        $data = $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('seg_usuarios', 'email')->ignore($usuario->id_usuario, 'id_usuario')],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'id_consultor' => ['nullable', 'integer', 'exists:tbl_consultor,id_consultor', Rule::unique('seg_usuarios', 'id_consultor')->ignore($usuario->id_usuario, 'id_usuario')],
            'activo' => ['nullable', 'boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:seg_roles,id_rol'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $data['activo'] = $request->boolean('activo');
        $data['usuario_mod'] = auth()->id();

        $usuario->update($data);
        $usuario->roles()->sync($request->input('roles', []));

        return redirect()->route('seg.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Usuario $usuario): RedirectResponse
    {
        $usuario->update(['usuario_elim' => auth()->id(), 'activo' => false]);
        $usuario->delete();

        return back()->with('success', 'Usuario eliminado correctamente.');
    }
}
