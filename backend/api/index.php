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

    case '/dashboard':
        $metrics = [
            'total_hardware' => $pdo->query('SELECT COUNT(*) FROM Hardware')->fetchColumn(),
            'total_telefonos' => $pdo->query('SELECT COUNT(*) FROM Telefonos')->fetchColumn(),
            'in_support' => $pdo->query('SELECT COUNT(*) FROM Estatus WHERE estado_actual_unidad = 2')->fetchColumn(),
            'available_mobile' => $pdo->query('SELECT COUNT(*) FROM Telefonos t JOIN Estatus e ON t.id_estatus = e.id_estatus WHERE e.estado_actual_unidad = 1')->fetchColumn()
        ];
        echo json_encode($metrics);
        break;

    case '/hardware':
        $stmt = $pdo->query('SELECT e.*, h.tipo_hardware, h.marca_hardware, h.modelo_hardware, h.bienes_hardware, h.usuario_hardware 
                            FROM Entradas e 
                            LEFT JOIN Hardware h ON e.id_hardware = h.id_hardware 
                            ORDER BY e.fecha_entrada DESC');
        echo json_encode($stmt->fetchAll());
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found', 'path' => $request]);
        break;
}
