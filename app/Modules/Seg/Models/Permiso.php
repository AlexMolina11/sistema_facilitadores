<?php

namespace App\Modules\Seg\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permiso extends Model
{
    use SoftDeletes;

    protected $table = 'seg_permisos';
    protected $primaryKey = 'id_permiso';

    protected $fillable = ['codigo', 'nombre', 'descripcion', 'modulo', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'seg_rol_permiso', 'id_permiso', 'id_rol')->withTimestamps();
    }
}
