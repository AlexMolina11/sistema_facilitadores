<?php

namespace App\Modules\Seg\Models;

use App\Modules\Fac\Models\Consultor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AceptacionTerminos extends Model
{
    protected $table = 'seg_aceptaciones_terminos';

    protected $primaryKey = 'id_aceptacion';

    protected $fillable = [
        'id_usuario',
        'id_consultor',
        'id_invitacion',
        'version_terminos',
        'hash_terminos',
        'ip',
        'user_agent',
        'fecha_aceptacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_aceptacion' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(
            Usuario::class,
            'id_usuario',
            'id_usuario'
        );
    }

    public function consultor(): BelongsTo
    {
        return $this->belongsTo(
            Consultor::class,
            'id_consultor',
            'id_consultor'
        );
    }

    public function invitacion(): BelongsTo
    {
        return $this->belongsTo(
            Invitacion::class,
            'id_invitacion',
            'id_invitacion'
        );
    }
}