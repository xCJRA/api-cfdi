<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\Auditable;

class Factura extends Model
{
    use Auditable;

    protected $fillable = [
        'folio',
        'cliente_id',
        'subtotal',
        'iva',
        'total',
        'estado',
        'motivo_cancelacion',
        'fecha_emision',
    ];

    protected $casts = [
        'subtotal'      => 'decimal:2',
        'iva'           => 'decimal:2',
        'total'         => 'decimal:2',
        'fecha_emision' => 'datetime',
    ];

    // Genera el UUID automáticamente al crear la factura
    protected static function booted(): void
    {
        static::creating(function ($factura) {
            $factura->folio = (string) Str::uuid();
        });
    }

    // Una factura pertenece a un cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // Una factura tiene muchos conceptos
    public function conceptos()
    {
        return $this->hasMany(ConceptoFactura::class);
    }
}
