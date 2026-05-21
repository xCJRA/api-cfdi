<?php

namespace App\Traits;

use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function registrarAuditoria(
        string $accion,
        string $entidad,
        ?int $entidad_id = null,
        ?array $datos_anteriores = null,
        ?array $datos_nuevos = null
    ): void {
        Auditoria::create([
            'user_id'          => Auth::id(),
            'accion'           => $accion,
            'entidad'          => $entidad,
            'entidad_id'       => $entidad_id,
            'datos_anteriores' => $datos_anteriores,
            'datos_nuevos'     => $datos_nuevos,
            'ip'               => Request::ip(),
        ]);
    }
}
