<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Cliente extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'nombre',
        'rfc',
        'email',
        'telefono',
        'direccion',
        'regimen_fiscal',
    ];

    // Un cliente puede tener muchas facturas
    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }
}
