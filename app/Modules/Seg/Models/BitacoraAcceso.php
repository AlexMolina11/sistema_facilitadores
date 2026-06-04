<?php

namespace App\Modules\Seg\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BitacoraAcceso extends Model
{
    protected $table = 'seg_bitacora_accesos';
    protected $primaryKey = 'id_bitacora';

    protected $fillable = ['id_usuario', 'evento', 'ip', 'user_agent', 'fecha_evento'];

    protected function casts(): array
    {
        return ['fecha_evento' => 'datetime'];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
