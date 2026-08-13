<?php

namespace App\Modules\Seg\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rol extends Model
{
    use SoftDeletes;

    protected $table = 'seg_roles';
    protected $primaryKey = 'id_rol';

    protected $fillable = ['nombre', 'descripcion', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'seg_usuario_rol', 'id_rol', 'id_usuario')->withTimestamps();
    }

    public function permisos(): BelongsToMany
    {
        return $this->belongsToMany(Permiso::class, 'seg_rol_permiso', 'id_rol', 'id_permiso')->withTimestamps();
    }

    public function invitaciones(): HasMany
    {
        return $this->hasMany(
            Invitacion::class,
            'id_rol',
            'id_rol'
        );
    }
}
