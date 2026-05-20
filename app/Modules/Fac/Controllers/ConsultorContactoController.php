<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Fac\Models\ConsultorEmail;
use App\Modules\Fac\Models\ConsultorTelefono;
use App\Modules\Fac\Models\ConsultorRedSocial;
use App\Modules\Fac\Models\ConsultorEmergencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultorContactoController extends Controller
{
    public function edit(Consultor $consultor)
    {
        $consultor->load([
            'emails',
            'telefonos',
            'redesSociales',
            'emergencias',
        ]);

        $catalogos = [
            'tiposTelefono' => DB::table('tbl_tipo_telefono')
                ->where('activo', true)
                ->orderBy('nombre')
                ->get(),

            'tiposRedSocial' => DB::table('tbl_tipo_red_social')
                ->where('activo', true)
                ->orderBy('nombre')
                ->get(),
        ];

        return view('fac.consultores.contacto', compact('consultor', 'catalogos'));
    }

    public function update(Request $request, Consultor $consultor)
    {
        $request->validate([
            'emails' => ['nullable', 'array'],
            'emails.*.email' => ['nullable', 'email', 'max:150'],
            'emails.*.principal' => ['nullable', 'boolean'],

            'telefonos' => ['nullable', 'array'],
            'telefonos.*.id_tipo_telefono' => ['nullable', 'integer', 'exists:tbl_tipo_telefono,id_tipo_telefono'],
            'telefonos.*.numero_telefono' => ['nullable', 'string', 'max:20'],
            'telefonos.*.extension' => ['nullable', 'string', 'max:10'],

            'redes' => ['nullable', 'array'],
            'redes.*.id_tipo_red_social' => ['nullable', 'integer', 'exists:tbl_tipo_red_social,id_tipo_red_social'],
            'redes.*.enlace' => ['nullable', 'string', 'max:500'],

            'emergencias' => ['nullable', 'array'],
            'emergencias.*.nombre' => ['nullable', 'string', 'max:150'],
            'emergencias.*.telefono' => ['nullable', 'string', 'max:20'],
            'emergencias.*.correo' => ['nullable', 'email', 'max:120'],
        ]);

        DB::transaction(function () use ($request, $consultor) {
            $userId = auth()->id();

            $consultor->emails()->delete();
            $consultor->telefonos()->delete();
            $consultor->redesSociales()->delete();
            $consultor->emergencias()->delete();

            foreach ($request->input('emails', []) as $email) {
                if (!empty($email['email'])) {
                    ConsultorEmail::create([
                        'id_consultor' => $consultor->id_consultor,
                        'email' => $email['email'],
                        'principal' => !empty($email['principal']),
                        'activo' => true,
                        'usuario_crea' => $userId,
                    ]);
                }
            }

            foreach ($request->input('telefonos', []) as $telefono) {
                if (!empty($telefono['id_tipo_telefono']) && !empty($telefono['numero_telefono'])) {
                    ConsultorTelefono::create([
                        'id_consultor' => $consultor->id_consultor,
                        'id_tipo_telefono' => $telefono['id_tipo_telefono'],
                        'numero_telefono' => $telefono['numero_telefono'],
                        'extension' => $telefono['extension'] ?? null,
                        'activo' => true,
                        'usuario_crea' => $userId,
                    ]);
                }
            }

            foreach ($request->input('redes', []) as $red) {
                if (!empty($red['id_tipo_red_social']) && !empty($red['enlace'])) {
                    ConsultorRedSocial::create([
                        'id_consultor' => $consultor->id_consultor,
                        'id_tipo_red_social' => $red['id_tipo_red_social'],
                        'enlace' => $red['enlace'],
                        'activo' => true,
                        'usuario_crea' => $userId,
                    ]);
                }
            }

            foreach ($request->input('emergencias', []) as $emergencia) {
                if (!empty($emergencia['nombre'])) {
                    ConsultorEmergencia::create([
                        'id_consultor' => $consultor->id_consultor,
                        'nombre' => $emergencia['nombre'],
                        'telefono' => $emergencia['telefono'] ?? null,
                        'correo' => $emergencia['correo'] ?? null,
                        'activo' => true,
                        'usuario_crea' => $userId,
                    ]);
                }
            }
        });

        return redirect()
            ->route('fac.consultores.formacion.edit', $consultor)
            ->with('success', 'Información de contacto guardada correctamente. Continúa con formación académica.');
    }
}