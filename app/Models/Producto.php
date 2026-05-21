<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Producto extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'nombre',
        'descripcion',
        'clave_sat',
        'unidad_medida',
        'precio',
        'iva_aplicable',
    ];

    protected $casts = [
        'iva_aplicable' => 'boolean',
        'precio'        => 'decimal:2',
    ];

    // Un producto puede aparecer en muchos conceptos de factura
    public function conceptos()
    {
        return $this->hasMany(ConceptoFactura::class);
    }
}
