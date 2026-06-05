<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\FacturaController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Clientes
    Route::apiResource('clientes', ClienteController::class);
    // Productos
    Route::apiResource('productos', ProductoController::class);

    // Facturas — solo index, store y show
    Route::apiResource('facturas', FacturaController::class)->only(['index', 'store', 'show']);

    // Factura Cancelar
    Route::post('facturas/{factura}/cancelar', [FacturaController::class, 'cancelar']);


});
