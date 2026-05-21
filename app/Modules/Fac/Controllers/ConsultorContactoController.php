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
use Illuminate\Validation\ValidationException;

class ConsultorContactoController extends Controller
{
    public function edit(Consultor $consultor)
    {
        $consultor->load([
            'emails' => fn ($query) => $query->where('activo', true)->orderByDesc('principal'),
            'telefonos' => fn ($query) => $query->where('activo', true),
            'redesSociales' => fn ($query) => $query->where('activo', true),
            'emergencias' => fn ($query) => $query->where('activo', true),
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
            'emails.*.email' => [
                'nullable',
                'string',
                'max:150',
                'regex:/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/',
            ],
            'emails.*.principal' => ['nullable', 'boolean'],

            'telefonos' => ['nullable', 'array'],
            'telefonos.*.id_tipo_telefono' => ['nullable', 'integer', 'exists:tbl_tipo_telefono,id_tipo_telefono'],
            'telefonos.*.numero_telefono' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9+\-\s]{7,20}$/',
            ],
            'telefonos.*.extension' => ['nullable', 'string', 'max:10'],

            'redes' => ['nullable', 'array'],
            'redes.*.id_tipo_red_social' => ['nullable', 'integer', 'exists:tbl_tipo_red_social,id_tipo_red_social'],
            'redes.*.enlace' => ['nullable', 'url', 'max:500'],

            'emergencias' => ['nullable', 'array'],
            'emergencias.*.nombre' => ['nullable', 'string', 'max:150'],
            'emergencias.*.telefono' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9+\-\s]{7,20}$/',
            ],
            'emergencias.*.correo' => [
                'nullable',
                'string',
                'max:120',
                'regex:/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/',
            ],
        ], [
            'emails.*.email.regex' => 'El correo debe tener un dominio completo. Ejemplo: nombre@dominio.com',
            'telefonos.*.numero_telefono.regex' => 'El teléfono solo puede contener números, espacios, guiones o el signo +.',
            'redes.*.enlace.url' => 'El enlace debe ser una URL válida. Ejemplo: https://www.linkedin.com/in/perfil',
            'emergencias.*.telefono.regex' => 'El teléfono de emergencia solo puede contener números, espacios, guiones o el signo +.',
            'emergencias.*.correo.regex' => 'El correo del contacto de emergencia debe tener un dominio completo. Ejemplo: nombre@dominio.com',
        ]);

        $emails = collect($request->input('emails', []))
            ->filter(fn ($item) => !empty($item['email']))
            ->values();

        $principales = $emails->filter(fn ($item) => !empty($item['principal']))->count();

        if ($principales > 1) {
            throw ValidationException::withMessages([
                'emails' => 'Solo puede existir un correo principal por consultor.',
            ]);
        }

        DB::transaction(function () use ($request, $consultor) {
            $userId = auth()->id();

            $this->eliminarRelacionActual($consultor->emails(), $userId);
            $this->eliminarRelacionActual($consultor->telefonos(), $userId);
            $this->eliminarRelacionActual($consultor->redesSociales(), $userId);
            $this->eliminarRelacionActual($consultor->emergencias(), $userId);

            foreach ($request->input('emails', []) as $email) {
                if (!empty($email['email'])) {
                    ConsultorEmail::create([
                        'id_consultor' => $consultor->id_consultor,
                        'email' => strtolower(trim($email['email'])),
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
                        'numero_telefono' => trim($telefono['numero_telefono']),
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
                        'enlace' => trim($red['enlace']),
                        'activo' => true,
                        'usuario_crea' => $userId,
                    ]);
                }
            }

            foreach ($request->input('emergencias', []) as $emergencia) {
                if (!empty($emergencia['nombre'])) {
                    ConsultorEmergencia::create([
                        'id_consultor' => $consultor->id_consultor,
                        'nombre' => trim($emergencia['nombre']),
                        'telefono' => $emergencia['telefono'] ?? null,
                        'correo' => !empty($emergencia['correo']) ? strtolower(trim($emergencia['correo'])) : null,
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

    private function eliminarRelacionActual($relation, ?int $userId): void
    {
        $items = $relation->whereNull('deleted_at')->get();

        foreach ($items as $item) {
            $item->update([
                'activo' => false,
                'usuario_elim' => $userId,
            ]);

            $item->delete();
        }
    }
}