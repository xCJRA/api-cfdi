<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rutas donde aplica CORS
    |--------------------------------------------------------------------------
    | Aplica a todas las rutas de la API
    */
    'paths' => ['api/*'],

    /*
    |--------------------------------------------------------------------------
    | Métodos HTTP permitidos
    |--------------------------------------------------------------------------
    */
    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Orígenes permitidos
    |--------------------------------------------------------------------------
    | En desarrollo puedes usar '*' para permitir cualquier origen.
    | En producción debes especificar los dominios reales.
    |
    | Ejemplo producción:
    | 'allowed_origins' => ['https://tu-frontend.com', 'https://app.tu-dominio.mx'],
    */
    'allowed_origins' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Patrones de orígenes permitidos (alternativa a allowed_origins)
    |--------------------------------------------------------------------------
    */
    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Headers permitidos en las peticiones
    |--------------------------------------------------------------------------
    */
    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Headers expuestos al navegador
    |--------------------------------------------------------------------------
    */
    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Tiempo en segundos que el navegador cachea la respuesta del preflight
    |--------------------------------------------------------------------------
    */
    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Soporte para cookies / credenciales
    |--------------------------------------------------------------------------
    | Actívalo si tu frontend envía cookies o usa sesiones.
    | Si lo activas, allowed_origins NO puede ser '*' — debe ser un dominio específico.
    */
    'supports_credentials' => false,

];
