<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CancelarFacturaRequest;
use App\Http\Requests\StoreFacturaRequest;
use App\Models\Factura;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class FacturaController extends Controller
{
    const IVA_TASA = 0.16;

    #[OA\Get(
        path: '/facturas',
        tags: ['Facturas'],
        summary: 'Listar facturas',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada de facturas con cliente'),
        ]
    )]
    public function index(Request $request)
    {
        $perPage = $request->get('perPage', 5);
        $facturas = Factura::with('cliente')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($facturas);
    }

    #[OA\Post(
        path: '/facturas',
        tags: ['Facturas'],
        summary: 'Emitir factura',
        description: 'Genera una factura calculando subtotal, IVA (16%) y total automáticamente.',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['cliente_id', 'conceptos'],
                properties: [
                    new OA\Property(property: 'cliente_id', type: 'integer', example: 1),
                    new OA\Property(
                        property: 'conceptos',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'producto_id', type: 'integer', example: 1),
                                new OA\Property(property: 'cantidad', type: 'integer', example: 2),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Factura emitida con folio UUID y totales calculados'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(StoreFacturaRequest $request)
    {
        $factura = DB::transaction(function () use ($request) {
            $factura = Factura::create([
                'cliente_id' => $request->cliente_id,
                'subtotal'   => 0,
                'iva'        => 0,
                'total'      => 0,
                'estado'     => 'borrador',
            ]);

            $subtotalGeneral = 0;
            $ivaGeneral      = 0;

            foreach ($request->conceptos as $item) {
                $producto       = Producto::findOrFail($item['producto_id']);
                $cantidad       = $item['cantidad'];
                $precioUnitario = $producto->precio;
                $subtotal       = $cantidad * $precioUnitario;
                $iva            = $producto->iva_aplicable ? $subtotal * self::IVA_TASA : 0;
                $total          = $subtotal + $iva;

                $factura->conceptos()->create([
                    'producto_id'     => $producto->id,
                    'descripcion'     => $producto->nombre,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal'        => $subtotal,
                    'iva'             => $iva,
                    'total'           => $total,
                ]);

                $subtotalGeneral += $subtotal;
                $ivaGeneral      += $iva;
            }

            $factura->update([
                'subtotal'      => $subtotalGeneral,
                'iva'           => $ivaGeneral,
                'total'         => $subtotalGeneral + $ivaGeneral,
                'estado'        => 'emitida',
                'fecha_emision' => now(),
            ]);

            return $factura;
        });

        Factura::registrarAuditoria(
            accion: 'factura.emitida',
            entidad: 'Factura',
            entidad_id: $factura->id,
            datos_nuevos: $factura->load('conceptos')->toArray()
        );

        return response()->json($factura->load(['cliente', 'conceptos']), 201);
    }

    #[OA\Get(
        path: '/facturas/{id}',
        tags: ['Facturas'],
        summary: 'Ver detalle de una factura',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Factura con cliente y conceptos detallados'),
            new OA\Response(response: 404, description: 'Factura no encontrada'),
        ]
    )]
    public function show(Factura $factura)
    {
        return response()->json($factura->load(['cliente', 'conceptos.producto']));
    }

    public function update()
    {
        return response()->json([
            'message' => 'Las facturas emitidas no pueden editarse. Cancela y genera una nueva.'
        ], 405);
    }

    public function destroy()
    {
        return response()->json([
            'message' => 'Las facturas no pueden eliminarse. Usa el endpoint de cancelación.'
        ], 405);
    }

    #[OA\Post(
        path: '/facturas/{id}/cancelar',
        tags: ['Facturas'],
        summary: 'Cancelar factura',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['motivo'],
                properties: [
                    new OA\Property(property: 'motivo', type: 'string', example: 'Error en los conceptos facturados'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Factura cancelada correctamente'),
            new OA\Response(response: 422, description: 'La factura ya estaba cancelada'),
            new OA\Response(response: 404, description: 'Factura no encontrada'),
        ]
    )]
    public function cancelar(CancelarFacturaRequest $request, Factura $factura)
    {
        if ($factura->estado === 'cancelada') {
            return response()->json([
                'message' => 'Esta factura ya fue cancelada anteriormente.'
            ], 422);
        }

        $datosAnteriores = $factura->toArray();

        $factura->update([
            'estado'             => 'cancelada',
            'motivo_cancelacion' => $request->motivo,
        ]);

        Factura::registrarAuditoria(
            accion: 'factura.cancelada',
            entidad: 'Factura',
            entidad_id: $factura->id,
            datos_anteriores: $datosAnteriores,
            datos_nuevos: $factura->fresh()->toArray()
        );

        return response()->json([
            'message' => 'Factura cancelada correctamente.',
            'factura' => $factura->fresh(),
        ]);
    }
}
