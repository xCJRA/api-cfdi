<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'API de Facturación Electrónica CFDI',
    version: '1.0.0',
    description: 'API REST para la gestión de clientes, productos y facturas electrónicas (CFDI) simuladas. Desarrollada con Laravel 11 y autenticación via Sanctum.',
    contact: new OA\Contact(
        name: 'César José Reyes Alonso',
        email: 'cesarjreyesa1@gmail.com'
    )
)]
#[OA\Server(
    url: '/api',
    description: 'Servidor local'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Ingresa el token obtenido del endpoint /api/login'
)]
#[OA\Tag(name: 'Autenticación', description: 'Endpoints de login')]
#[OA\Tag(name: 'Clientes', description: 'Gestión de clientes')]
#[OA\Tag(name: 'Productos', description: 'Gestión de productos')]
#[OA\Tag(name: 'Facturas', description: 'Gestión de facturas CFDI')]
abstract class Controller
{
}
