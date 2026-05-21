<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'auditoria';

    protected $fillable = [
        'user_id',
        'accion',
        'entidad',
        'entidad_id',
        'datos_anteriores',
        'datos_nuevos',
        'ip',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos'     => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
