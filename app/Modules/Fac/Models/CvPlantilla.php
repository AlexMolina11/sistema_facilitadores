<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CvPlantilla extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_cv_plantilla';
    protected $primaryKey = 'id_cv_plantilla';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'vista_blade',
        'vista_verificada',
        'fecha_verificacion',
        'tamanio_papel',
        'orientacion',
        'orden',
        'activa',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'activo' => 'boolean',
        'vista_verificada' => 'boolean',
        'fecha_verificacion' => 'datetime',
        'orden' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (CvPlantilla $plantilla) {
            $plantilla->codigo = Str::slug($plantilla->codigo, '_');
            $plantilla->vista_blade = 'fac.cv.pdf.' . $plantilla->codigo;
        });
    }

    public function getRouteKeyName(): string
    {
        return 'id_cv_plantilla';
    }
}