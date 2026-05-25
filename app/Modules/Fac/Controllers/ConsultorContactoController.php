<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Fac\Models\ConsultorEmail;
use App\Modules\Fac\Models\ConsultorEmergencia;
use App\Modules\Fac\Models\ConsultorRedSocial;
use App\Modules\Fac\Models\ConsultorTelefono;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConsultorContactoController extends Controller
{
    public function edit(Consultor $consultor)
    {
        $consultor->load([
            'emails' => fn ($q) => $q->where('activo', true)->orderByDesc('principal'),
            'telefonos' => fn ($q) => $q->where('activo', true),
            'redesSociales' => fn ($q) => $q->where('activo', true),
            'emergencias' => fn ($q) => $q->where('activo', true),
        ]);

        $catalogos = [
            'tiposTelefono' => DB::table('tbl_tipo_telefono')->where('activo', true)->orderBy('nombre')->get(),
            'tiposRedSocial' => DB::table('tbl_tipo_red_social')->where('activo', true)->orderBy('nombre')->get(),
        ];

        return view('fac.consultores.contacto', compact('consultor', 'catalogos'));
    }

    public function storeEmail(Request $request, Consultor $consultor)
    {
        $data = $this->validarEmail($request);
        $this->validarCorreoUnico($consultor, $data['email']);

        if (!empty($data['principal'])) {
            ConsultorEmail::where('id_consultor', $consultor->id_consultor)->where('activo', true)->update(['principal' => false]);
        }

        ConsultorEmail::create([
            'id_consultor' => $consultor->id_consultor,
            'email' => strtolower(trim($data['email'])),
            'principal' => !empty($data['principal']),
            'activo' => true,
            'usuario_crea' => auth()->id(),
        ]);

        return back()->with('success', 'Correo registrado correctamente.');
    }

    public function updateEmail(Request $request, Consultor $consultor, ConsultorEmail $email)
    {
        $this->validarPertenencia($consultor, $email->id_consultor);
        $data = $this->validarEmail($request);
        $this->validarCorreoUnico($consultor, $data['email'], $email->id_email);

        if (!empty($data['principal'])) {
            ConsultorEmail::where('id_consultor', $consultor->id_consultor)->where('id_email', '!=', $email->id_email)->where('activo', true)->update(['principal' => false]);
        }

        $email->update([
            'email' => strtolower(trim($data['email'])),
            'principal' => !empty($data['principal']),
            'activo' => true,
            'usuario_mod' => auth()->id(),
        ]);

        return back()->with('success', 'Correo actualizado correctamente.');
    }

    public function destroyEmail(Consultor $consultor, ConsultorEmail $email)
    {
        $this->validarPertenencia($consultor, $email->id_consultor);
        $email->update(['activo' => false, 'usuario_elim' => auth()->id()]);
        $email->delete();
        return back()->with('success', 'Correo eliminado correctamente.');
    }

    public function storeTelefono(Request $request, Consultor $consultor)
    {
        $data = $this->validarTelefono($request);
        ConsultorTelefono::create($data + ['id_consultor' => $consultor->id_consultor, 'activo' => true, 'usuario_crea' => auth()->id()]);
        return back()->with('success', 'Teléfono registrado correctamente.');
    }

    public function updateTelefono(Request $request, Consultor $consultor, ConsultorTelefono $telefono)
    {
        $this->validarPertenencia($consultor, $telefono->id_consultor);
        $telefono->update($this->validarTelefono($request) + ['activo' => true, 'usuario_mod' => auth()->id()]);
        return back()->with('success', 'Teléfono actualizado correctamente.');
    }

    public function destroyTelefono(Consultor $consultor, ConsultorTelefono $telefono)
    {
        $this->validarPertenencia($consultor, $telefono->id_consultor);
        $telefono->update(['activo' => false, 'usuario_elim' => auth()->id()]);
        $telefono->delete();
        return back()->with('success', 'Teléfono eliminado correctamente.');
    }

    public function storeRed(Request $request, Consultor $consultor)
    {
        $data = $this->validarRed($request);
        ConsultorRedSocial::create($data + ['id_consultor' => $consultor->id_consultor, 'activo' => true, 'usuario_crea' => auth()->id()]);
        return back()->with('success', 'Red social registrada correctamente.');
    }

    public function updateRed(Request $request, Consultor $consultor, ConsultorRedSocial $red)
    {
        $this->validarPertenencia($consultor, $red->id_consultor);
        $red->update($this->validarRed($request) + ['activo' => true, 'usuario_mod' => auth()->id()]);
        return back()->with('success', 'Red social actualizada correctamente.');
    }

    public function destroyRed(Consultor $consultor, ConsultorRedSocial $red)
    {
        $this->validarPertenencia($consultor, $red->id_consultor);
        $red->update(['activo' => false, 'usuario_elim' => auth()->id()]);
        $red->delete();
        return back()->with('success', 'Red social eliminada correctamente.');
    }

    public function storeEmergencia(Request $request, Consultor $consultor)
    {
        $data = $this->validarEmergencia($request);
        ConsultorEmergencia::create($data + ['id_consultor' => $consultor->id_consultor, 'activo' => true, 'usuario_crea' => auth()->id()]);
        return back()->with('success', 'Contacto de emergencia registrado correctamente.');
    }

    public function updateEmergencia(Request $request, Consultor $consultor, ConsultorEmergencia $emergencia)
    {
        $this->validarPertenencia($consultor, $emergencia->id_consultor);
        $emergencia->update($this->validarEmergencia($request) + ['activo' => true, 'usuario_mod' => auth()->id()]);
        return back()->with('success', 'Contacto de emergencia actualizado correctamente.');
    }

    public function destroyEmergencia(Consultor $consultor, ConsultorEmergencia $emergencia)
    {
        $this->validarPertenencia($consultor, $emergencia->id_consultor);
        $emergencia->update(['activo' => false, 'usuario_elim' => auth()->id()]);
        $emergencia->delete();
        return back()->with('success', 'Contacto de emergencia eliminado correctamente.');
    }

    public function continuar(Consultor $consultor)
    {
        return redirect()->route('fac.consultores.experiencia.edit', $consultor)->with('success', 'Continúa con experiencia laboral.');
    }

    private function validarEmail(Request $request): array
    {
        return $request->validate([
            'email' => ['required', 'string', 'max:150', 'regex:/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/'],
            'principal' => ['nullable', 'boolean'],
        ]);
    }

    private function validarTelefono(Request $request): array
    {
        return $request->validate([
            'id_tipo_telefono' => ['required', 'integer', 'exists:tbl_tipo_telefono,id_tipo_telefono'],
            'numero_telefono' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]{7,20}$/'],
            'extension' => ['nullable', 'string', 'max:10'],
        ]);
    }

    private function validarRed(Request $request): array
    {
        return $request->validate([
            'id_tipo_red_social' => ['required', 'integer', 'exists:tbl_tipo_red_social,id_tipo_red_social'],
            'enlace' => ['required', 'url', 'max:500'],
        ]);
    }

    private function validarEmergencia(Request $request): array
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'telefono' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]{7,20}$/'],
            'correo' => ['nullable', 'string', 'max:120', 'regex:/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/'],
        ]);
        $data['correo'] = !empty($data['correo']) ? strtolower(trim($data['correo'])) : null;
        return $data;
    }

    private function validarCorreoUnico(Consultor $consultor, string $email, ?int $ignorarId = null): void
    {
        $query = ConsultorEmail::where('id_consultor', $consultor->id_consultor)->where('email', strtolower(trim($email)))->where('activo', true);
        if ($ignorarId) { $query->where('id_email', '!=', $ignorarId); }
        if ($query->exists()) { throw ValidationException::withMessages(['email' => 'Este correo ya está registrado para el consultor.']); }
    }

    private function validarPertenencia(Consultor $consultor, int $idConsultorDelRegistro): void
    {
        abort_if($consultor->id_consultor !== $idConsultorDelRegistro, 404);
    }
}
