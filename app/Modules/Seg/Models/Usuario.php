<?php

namespace App\Modules\Seg\Models;

use App\Modules\Fac\Models\Consultor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        return $this->roles()->whereIn('nombre', $roles)->exists();
    }

    public function tienePermiso(string $codigo): bool
    {
        $directo = $this->permisosDirectos()
            ->where('codigo', $codigo)
            ->first();

        if ($directo) {
            return (bool) $directo->pivot->permitido;
        }

        return $this->roles()
            ->whereHas('permisos', fn ($q) => $q->where('codigo', $codigo)->where('seg_permisos.activo', true))
            ->exists();
    }

    public function esConsultorPropietario(int $idConsultor): bool
    {
        return (int) $this->id_consultor === (int) $idConsultor;
    }
}
