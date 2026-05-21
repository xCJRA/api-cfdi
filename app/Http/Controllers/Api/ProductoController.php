<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Producto;
use OpenApi\Attributes as OA;

class ProductoController extends Controller
{
    #[OA\Get(
        path: '/productos',
        tags: ['Productos'],
        summary: 'Listar productos',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada de productos'),
        ]
    )]
    public function index()
    {
        $productos = Producto::orderBy('nombre')->paginate(15);
        return response()->json($productos);
    }

    #[OA\Post(
        path: '/productos',
        tags: ['Productos'],
        summary: 'Crear producto',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre', 'clave_sat', 'unidad_medida', 'precio', 'iva_aplicable'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', example: 'Consultoría en sistemas'),
                    new OA\Property(property: 'descripcion', type: 'string', example: 'Asesoría técnica'),
                    new OA\Property(property: 'clave_sat', type: 'string', example: '81111507'),
                    new OA\Property(property: 'unidad_medida', type: 'string', example: 'HUR'),
                    new OA\Property(property: 'precio', type: 'number', example: 1200.00),
                    new OA\Property(property: 'iva_aplicable', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Producto creado exitosamente'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(StoreProductoRequest $request)
    {
        $producto = Producto::create($request->validated());

        Producto::registrarAuditoria(
            accion: 'producto.creado',
            entidad: 'Producto',
            entidad_id: $producto->id,
            datos_nuevos: $producto->toArray()
        );

        return response()->json($producto, 201);
    }

    #[OA\Get(
        path: '/productos/{id}',
        tags: ['Productos'],
        summary: 'Ver detalle de un producto',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Detalle del producto'),
            new OA\Response(response: 404, description: 'Producto no encontrado'),
        ]
    )]
    public function show(Producto $producto)
    {
        return response()->json($producto);
    }

    #[OA\Put(
        path: '/productos/{id}',
        tags: ['Productos'],
        summary: 'Actualizar producto',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'precio', type: 'number', example: 1500.00),
                    new OA\Property(property: 'iva_aplicable', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Producto actualizado'),
            new OA\Response(response: 404, description: 'Producto no encontrado'),
        ]
    )]
    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        $datosAnteriores = $producto->toArray();
        $producto->update($request->validated());

        Producto::registrarAuditoria(
            accion: 'producto.actualizado',
            entidad: 'Producto',
            entidad_id: $producto->id,
            datos_anteriores: $datosAnteriores,
            datos_nuevos: $producto->fresh()->toArray()
        );

        return response()->json($producto);
    }

    #[OA\Delete(
        path: '/productos/{id}',
        tags: ['Productos'],
        summary: 'Eliminar producto (soft delete)',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Producto eliminado correctamente'),
            new OA\Response(response: 404, description: 'Producto no encontrado'),
        ]
    )]
    public function destroy(Producto $producto)
    {
        $datosAnteriores = $producto->toArray();
        $producto->delete();

        Producto::registrarAuditoria(
            accion: 'producto.eliminado',
            entidad: 'Producto',
            entidad_id: $producto->id,
            datos_anteriores: $datosAnteriores,
        );

        return response()->json(['message' => 'Producto eliminado correctamente.']);
    }
}
