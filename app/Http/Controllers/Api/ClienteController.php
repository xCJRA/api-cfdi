<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ClienteController extends Controller
{
    #[OA\Get(
        path: '/clientes',
        tags: ['Clientes'],
        summary: 'Listar clientes',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada de clientes'),
        ]
    )]
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 5);
        $clientes = Cliente::orderBy('nombre')->paginate($perPage);
        return response()->json($clientes);
    }

    #[OA\Post(
        path: '/clientes',
        tags: ['Clientes'],
        summary: 'Crear cliente',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre', 'rfc', 'email'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', example: 'Empresa Ejemplo SA de CV'),
                    new OA\Property(property: 'rfc', type: 'string', example: 'EEJ210401AB1'),
                    new OA\Property(property: 'email', type: 'string', example: 'contacto@empresa.mx'),
                    new OA\Property(property: 'telefono', type: 'string', example: '5512345678'),
                    new OA\Property(property: 'direccion', type: 'string', example: 'Av. Reforma 123, CDMX'),
                    new OA\Property(property: 'regimen_fiscal', type: 'string', example: '601'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Cliente creado exitosamente'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(StoreClienteRequest $request)
    {
        $cliente = Cliente::create($request->validated());

        Cliente::registrarAuditoria(
            accion: 'cliente.creado',
            entidad: 'Cliente',
            entidad_id: $cliente->id,
            datos_nuevos: $cliente->toArray()
        );

        return response()->json($cliente, 201);
    }

    #[OA\Get(
        path: '/clientes/{id}',
        tags: ['Clientes'],
        summary: 'Ver detalle de un cliente',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Detalle del cliente con sus facturas'),
            new OA\Response(response: 404, description: 'Cliente no encontrado'),
        ]
    )]
    public function show(Cliente $cliente)
    {
        return response()->json($cliente->load('facturas'));
    }

    #[OA\Put(
        path: '/clientes/{id}',
        tags: ['Clientes'],
        summary: 'Actualizar cliente',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', example: 'Nuevo Nombre SA de CV'),
                    new OA\Property(property: 'telefono', type: 'string', example: '5598765432'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Cliente actualizado'),
            new OA\Response(response: 404, description: 'Cliente no encontrado'),
        ]
    )]
    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        $datosAnteriores = $cliente->toArray();
        $cliente->update($request->validated());

        Cliente::registrarAuditoria(
            accion: 'cliente.actualizado',
            entidad: 'Cliente',
            entidad_id: $cliente->id,
            datos_anteriores: $datosAnteriores,
            datos_nuevos: $cliente->fresh()->toArray()
        );

        return response()->json($cliente);
    }

    #[OA\Delete(
        path: '/clientes/{id}',
        tags: ['Clientes'],
        summary: 'Eliminar cliente (soft delete)',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Cliente eliminado correctamente'),
            new OA\Response(response: 404, description: 'Cliente no encontrado'),
        ]
    )]
    public function destroy(Cliente $cliente)
    {
        $datosAnteriores = $cliente->toArray();
        $cliente->delete();

        Cliente::registrarAuditoria(
            accion: 'cliente.eliminado',
            entidad: 'Cliente',
            entidad_id: $cliente->id,
            datos_anteriores: $datosAnteriores,
        );

        return response()->json(['message' => 'Cliente eliminado correctamente.']);
    }
}
