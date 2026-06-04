<?php

namespace App\Modules\Seg\Support;

trait HasPermissions
{
    public function canAccess(string $permission): bool
    {
        return method_exists($this, 'tienePermiso') && $this->tienePermiso($permission);
    }
}
