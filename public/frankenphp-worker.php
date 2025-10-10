<?php

// FRANKENPHP WORKER MODE
// This file keeps your Laravel application in memory for maximum performance

// Prevent worker script from being accessed directly
if (!isset($_SERVER['FRANKENPHP_WORKER'])) {
    die('This script can only be run as a FrankenPHP worker');
}

// Bootstrap Laravel application
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Boot the application once (not per-request)
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Clear resolved instances that should not be shared between requests
$clearOnRequest = [
    'request',
    'request.input',
    'request.route',
    'Illuminate\Http\Request',
    'Illuminate\Routing\Route',
];

// Worker loop - handle requests
while ($request = \frankenphp_handle_request(function () use ($app, $clearOnRequest) {
    // Clear request-specific instances
    foreach ($clearOnRequest as $abstract) {
        if ($app->resolved($abstract)) {
            $app->forgetInstance($abstract);
        }
    }
    
    // Handle the request
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    
    $request = \Illuminate\Http\Request::capture();
    
    try {
        $response = $kernel->handle($request);
        $response->send();
        $kernel->terminate($request, $response);
    } catch (\Throwable $e) {
        // Log error
        if ($app->bound('log')) {
            $app['log']->error('Worker error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        // Send 500 response
        http_response_code(500);
        echo "Internal Server Error";
    }
    
    // Reset the application state for next request
    $app->flush();
    
    // Force garbage collection periodically
    if (random_int(1, 100) === 1) {
        gc_collect_cycles();
    }
})) {}

