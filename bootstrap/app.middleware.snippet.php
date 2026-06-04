<?php

// En bootstrap/app.php, dentro de ->withMiddleware(function (Middleware $middleware) { ... }) agrega:

$middleware->alias([
    'permission' => \App\Modules\Seg\Middleware\EnsurePermission::class,
    'consultor.owner' => \App\Modules\Seg\Middleware\EnsureConsultorOwnerOrPermission::class,
]);
