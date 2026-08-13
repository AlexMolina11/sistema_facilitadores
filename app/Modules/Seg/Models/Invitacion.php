<?php

namespace App\Modules\Seg\Models;

use App\Modules\Fac\Models\Consultor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invitacion extends Model
{
    use SoftDeletes;

    protected $table = 'seg_invitaciones';

    protected $primaryKey = 'id_invitacion';

    public $timestamps = true;

    protected $fillable = [
        'id_consultor',
        'id_rol',
        'alias',
        'token',
        'url_invitacion',
        'ruta_qr',
        'duracion_horas',
        'max_usos',
        'usos_actuales',
        'fecha_expiracion',
        'activa',
        'revocada',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected function casts(): array
    {
        return [
            'id_consultor' => 'integer',
            'id_rol' => 'integer',
            'duracion_horas' => 'integer',
            'max_usos' => 'integer',
            'usos_actuales' => 'integer',
            'fecha_expiracion' => 'datetime',
            'activa' => 'boolean',
            'revocada' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    public function consultor(): BelongsTo
    {
        return $this->belongsTo(
            Consultor::class,
            'id_consultor',
            'id_consultor'
        );
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(
            Rol::class,
            'id_rol',
            'id_rol'
        );
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(
            Usuario::class,
            'usuario_crea',
            'id_usuario'
        );
    }

    public function modificador(): BelongsTo
    {
        return $this->belongsTo(
            Usuario::class,
            'usuario_mod',
            'id_usuario'
        );
    }

    public function eliminador(): BelongsTo
    {
        return $this->belongsTo(
            Usuario::class,
            'usuario_elim',
            'id_usuario'
        );
    }
}