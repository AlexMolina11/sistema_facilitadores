<?php

namespace App\Modules\Seg\Models;

use App\Modules\Fac\Models\Consultor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Modules\Seg\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'seg_usuarios';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'id_consultor', 'nombres', 'apellidos', 'email', 'password', 'activo',
        'ultimo_acceso', 'usuario_crea', 'usuario_mod', 'usuario_elim',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo' => 'boolean',
            'ultimo_acceso' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function getAuthPassword(): string
    {
        return $this->password;
    }

    public function getNameAttribute(): string
    {
        return trim("{$this->nombres} {$this->apellidos}");
    }

    public function consultor(): BelongsTo
    {
        return $this->belongsTo(Consultor::class, 'id_consultor', 'id_consultor');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'seg_usuario_rol', 'id_usuario', 'id_rol')
            ->withTimestamps();
    }

    public function permisosDirectos(): BelongsToMany
    {
        return $this->belongsToMany(Permiso::class, 'seg_usuario_permiso', 'id_usuario', 'id_permiso')
            ->withPivot('permitido')
            ->withTimestamps();
    }

    public function tieneRol(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        return $this->roles()
            ->where('seg_roles.activo', true)
            ->whereIn('nombre', $roles)
            ->exists();
    }

    public function tienePermiso(string $codigo): bool
    {
        $directo = $this->permisosDirectos()
            ->where('seg_permisos.codigo', $codigo)
            ->where('seg_permisos.activo', true)
            ->first();

        if ($directo) {
            return (bool) $directo->pivot->permitido;
        }

        return $this->roles()
            ->where('seg_roles.activo', true)
            ->whereHas('permisos', fn ($q) => $q
                ->where('seg_permisos.codigo', $codigo)
                ->where('seg_permisos.activo', true))
            ->exists();
    }

    public function permisosEfectivos(): array
    {
        $porRol = Permiso::query()
            ->where('seg_permisos.activo', true)
            ->whereHas('roles', fn ($q) => $q
                ->where('seg_roles.activo', true)
                ->whereIn('seg_roles.id_rol', $this->roles()->pluck('seg_roles.id_rol')))
            ->pluck('codigo')
            ->all();

        $directos = $this->permisosDirectos()
            ->where('seg_permisos.activo', true)
            ->get(['seg_permisos.codigo']);

        $permitidos = $directos->where('pivot.permitido', true)->pluck('codigo')->all();
        $denegados = $directos->where('pivot.permitido', false)->pluck('codigo')->all();

        return collect($porRol)
            ->merge($permitidos)
            ->reject(fn ($codigo) => in_array($codigo, $denegados, true))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public function esConsultorPropietario(int $idConsultor): bool
    {
        return (int) $this->id_consultor === (int) $idConsultor;
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function invitacionesCreadas(): HasMany
    {
        return $this->hasMany(
            Invitacion::class,
            'usuario_crea',
            'id_usuario'
        );
    }

    public function invitacionesModificadas(): HasMany
    {
        return $this->hasMany(
            Invitacion::class,
            'usuario_mod',
            'id_usuario'
        );
    }

    public function invitacionesEliminadas(): HasMany
    {
        return $this->hasMany(
            Invitacion::class,
            'usuario_elim',
            'id_usuario'
        );
    }
}
