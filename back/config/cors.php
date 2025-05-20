<?php
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'], // Permitir todos los métodos (puedes restringirlo si es necesario)
    'allowed_origins' => ['http://localhost:5173'], // Cambia esto al dominio de tu frontend
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Necesario para withCredentials
];
