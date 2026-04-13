<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$request = $_SERVER['REQUEST_URI'];
$basePath = '/api';

if (strpos($request, $basePath) === 0) {
    $request = substr($request, strlen($basePath));
}

$request = explode('?', $request)[0];
$request = rtrim($request, '/');

require_once __DIR__ . '/../src/conexiondb.php';

switch ($request) {
    case '':
    case '/status':
        echo json_encode([
            'status' => 'online',
            'version' => '2.0.0',
            'system' => 'SIOTIC'
        ]);
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found', 'path' => $request]);
        break;
}
