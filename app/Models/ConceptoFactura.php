<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConceptoFactura extends Model
{
    protected $table = 'conceptos_factura';

    protected $fillable = [
        'factura_id',
        'producto_id',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'iva',
        'total',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'subtotal'        => 'decimal:2',
        'iva'             => 'decimal:2',
        'total'           => 'decimal:2',
    ];

    // Un concepto pertenece a una factura
    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    // Un concepto pertenece a un producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
