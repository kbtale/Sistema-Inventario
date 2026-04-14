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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            try {
                $pdo->beginTransaction();
                
                $stmt = $pdo->prepare('INSERT INTO Estatus (comentarios, estado_actual_unidad) VALUES (:com, 1)');
                $stmt->execute([':com' => $data['comentarios'] ?? 'Initial registration']);
                $idEstatus = $pdo->lastInsertId();
                
                $stmt = $pdo->prepare('INSERT INTO Hardware (tipo_hardware, marca_hardware, modelo_hardware, bienes_hardware, usuario_hardware, fecha_ingreso, id_estatus) 
                                      VALUES (:tipo, :marca, :modelo, :bienes, :usuario, :fIngreso, :idEstatus)');
                $stmt->execute([
                    ':tipo' => $data['tipo'] ?? '',
                    ':marca' => $data['marca'] ?? null,
                    ':modelo' => $data['modelo'] ?? null,
                    ':bienes' => $data['bienes'] ?? '',
                    ':usuario' => $data['usuario'] ?? 'OTIC',
                    ':fIngreso' => $data['fecha_ingreso'] ?? date('Y-m-d'),
                    ':idEstatus' => $idEstatus
                ]);
                
                $idHard = $pdo->lastInsertId();
                $pdo->commit();
                
                echo json_encode(['success' => true, 'id' => $idHard]);
            } catch (Exception $e) {
                $pdo->rollBack();
                http_response_code(500);
                echo json_encode(['error' => 'Registration failed', 'details' => $e->getMessage()]);
            }
        } else {
            $stmt = $pdo->query('SELECT e.*, h.tipo_hardware, h.marca_hardware, h.modelo_hardware, h.bienes_hardware, h.usuario_hardware 
                                FROM Entradas e 
                                LEFT JOIN Hardware h ON e.id_hardware = h.id_hardware 
                                ORDER BY e.fecha_entrada DESC');
            echo json_encode($stmt->fetchAll());
        }
        break;

    case '/telefonos':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            try {
                $pdo->beginTransaction();
                
                $stmt = $pdo->prepare('INSERT INTO Estatus (comentarios, estado_actual_unidad) VALUES (:com, 1)');
                $stmt->execute([':com' => $data['comentarios'] ?? 'Initial registration']);
                $idEstatus = $pdo->lastInsertId();
                
                $stmt = $pdo->prepare('INSERT INTO Telefonos (tipo_telefono, marca_telefono, modelo_telefono, nro_telefono, imei_telefono, imeisim_telefono, puk_telefono, usuario_asignado, id_estatus) 
                                      VALUES (:tipo, :marca, :modelo, :nro, :imei, :imeisim, :puk, :usuario, :idEstatus)');
                $stmt->execute([
                    ':tipo' => $data['tipo'] ?? '',
                    ':marca' => $data['marca'] ?? null,
                    ':modelo' => $data['modelo'] ?? null,
                    ':nro' => $data['nro'] ?? '',
                    ':imei' => $data['imei'] ?? '',
                    ':imeisim' => $data['imeisim'] ?? '',
                    ':puk' => $data['puk'] ?? '',
                    ':usuario' => $data['usuario'] ?? 'OTIC',
                    ':idEstatus' => $idEstatus
                ]);
                
                $idTel = $pdo->lastInsertId();
                $pdo->commit();
                
                echo json_encode(['success' => true, 'id' => $idTel]);
            } catch (Exception $e) {
                $pdo->rollBack();
                http_response_code(500);
                echo json_encode(['error' => 'Registration failed', 'details' => $e->getMessage()]);
            }
        } else {
            $stmt = $pdo->query('SELECT t.*, e.estado_actual_unidad, e.fallas, e.comentarios 
                                FROM Telefonos t 
                                LEFT JOIN Estatus e ON t.id_estatus = e.id_estatus 
                                ORDER BY t.id_telefono DESC');
            echo json_encode($stmt->fetchAll());
        }
        break;

    case '/entradas':
        $stmt = $pdo->query('SELECT e.*, h.tipo_hardware, h.marca_hardware, h.modelo_hardware, h.bienes_hardware, u.nombre_usuario as encargado_nombre
                            FROM Entradas e 
                            LEFT JOIN Hardware h ON e.id_hardware = h.id_hardware 
                            LEFT JOIN Usuarios u ON e.id_encargado = u.id_usuario
                            ORDER BY e.fecha_entrada DESC');
        echo json_encode($stmt->fetchAll());
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found', 'path' => $request]);
        break;
}
