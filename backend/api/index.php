<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$requestUri = $_SERVER['REQUEST_URI'];
$basePath = '/api';

if (strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}

$parts = explode('/', trim(explode('?', $requestUri)[0], '/'));
$resource = '/' . ($parts[0] ?? '');
$id = $parts[1] ?? null;

// middleware

$publicRoutes = ['/', '/status', '/auth/login'];
if (!in_array($resource, $publicRoutes)) {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    $secretKey = getenv('JWT_SECRET');

    if (!$authHeader || strpos($authHeader, 'Bearer ') !== 0) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        exit;
    }

    $token = substr($authHeader, 7);
    $tokenParts = explode('.', $token);
    
    if (count($tokenParts) !== 3) {
        http_response_code(401);
        echo json_encode(['error' => 'Malformed token']);
        exit;
    }

    list($header, $payload, $signature) = $tokenParts;
    $validSignature = base64_encode(hash_hmac('sha256', "$header.$payload", $secretKey, true));

    if (!hash_equals($validSignature, $signature)) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid signature']);
        exit;
    }

    $data = json_decode(base64_decode($payload), true);
    if (!$data || ($data['exp'] < time())) {
        http_response_code(401);
        echo json_encode(['error' => 'Token expired']);
        exit;
    }
}

require_once __DIR__ . '/../src/conexiondb.php';

switch ($resource) {
    case '/':
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
                $stmt = $pdo->prepare('INSERT INTO Estatus (comentarios, estado_actual_unidad, pulse_score) VALUES (:com, 1, 100)');
                $stmt->execute([':com' => $data['comentarios'] ?? 'Initial registration']);
                $idEstatus = $pdo->lastInsertId();
                $stmt = $pdo->prepare('INSERT INTO Hardware (tipo_hardware, marca_hardware, modelo_hardware, bienes_hardware, usuario_hardware, fecha_ingreso, id_estatus, id_sede, qr_code) VALUES (:tipo, :marca, :modelo, :bienes, :usuario, :fIngreso, :idEstatus, :idSede, :qrCode)');
                $stmt->execute([':tipo' => $data['tipo'] ?? '', ':marca' => $data['marca'] ?? null, ':modelo' => $data['modelo'] ?? null, ':bienes' => $data['bienes'] ?? '', ':usuario' => $data['usuario'] ?? 'OTIC', ':fIngreso' => $data['fecha_ingreso'] ?? date('Y-m-d'), ':idEstatus' => $idEstatus, ':idSede' => $data['id_sede'] ?? null, ':qrCode' => $data['qr_code'] ?? null]);
                $idHard = $pdo->lastInsertId();
                $pdo->commit();
                echo json_encode(['success' => true, 'id' => $idHard]);
            } catch (Exception $e) { $pdo->rollBack(); http_response_code(500); echo json_encode(['error' => 'Registration failed', 'details' => $e->getMessage()]); }
        } else {
            // Calculate dynamic pulse and join with last entry/sede info
            $stmt = $pdo->query('SELECT h.*, e.estado_actual_unidad, e.fallas, e.comentarios, s.nombre_sede, 
                ent.numero_orden, ent.fecha_entrada,
                COALESCE(e.pulse_score, (100 - (TIMESTAMPDIFF(YEAR, h.fecha_ingreso, CURDATE()) * 5) - (SELECT COUNT(*) FROM Entradas repairs WHERE repairs.id_hardware = h.id_hardware) * 10)) as pulse_score
                FROM Hardware h 
                LEFT JOIN Estatus e ON h.id_estatus = e.id_estatus 
                LEFT JOIN Sedes s ON h.id_sede = s.id_sede 
                LEFT JOIN (SELECT id_hardware, MAX(fecha_entrada) as fecha_entrada, numero_orden FROM Entradas GROUP BY id_hardware) ent ON h.id_hardware = ent.id_hardware
                ORDER BY h.id_hardware DESC');
            echo json_encode($stmt->fetchAll());
        }
        break;

    case '/telefonos':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare('INSERT INTO Estatus (comentarios, estado_actual_unidad, pulse_score) VALUES (:com, 1, 100)');
                $stmt->execute([':com' => $data['comentarios'] ?? 'Initial registration']);
                $idEstatus = $pdo->lastInsertId();
                $stmt = $pdo->prepare('INSERT INTO Telefonos (tipo_telefono, marca_telefono, modelo_telefono, nro_telefono, imei_telefono, imeisim_telefono, puk_telefono, usuario_asignado, fecha_ingreso, id_estatus, id_sede, qr_code) VALUES (:tipo, :marca, :modelo, :nro, :imei, :imeisim, :puk, :usuario, :fIngreso, :idEstatus, :idSede, :qrCode)');
                $stmt->execute([':tipo' => $data['tipo'] ?? '', ':marca' => $data['marca'] ?? null, ':modelo' => $data['modelo'] ?? null, ':nro' => $data['nro'] ?? '', ':imei' => $data['imei'] ?? '', ':imeisim' => $data['imeisim'] ?? '', ':puk' => $data['puk'] ?? '', ':usuario' => $data['usuario'] ?? 'OTIC', ':fIngreso' => $data['fecha_ingreso'] ?? date('Y-m-d'), ':idEstatus' => $idEstatus, ':idSede' => $data['id_sede'] ?? null, ':qrCode' => $data['qr_code'] ?? null]);
                $idTel = $pdo->lastInsertId();
                $pdo->commit();
                echo json_encode(['success' => true, 'id' => $idTel]);
            } catch (Exception $e) { $pdo->rollBack(); http_response_code(500); echo json_encode(['error' => 'Registration failed', 'details' => $e->getMessage()]); }
        } else {
            $stmt = $pdo->query('SELECT t.*, e.estado_actual_unidad, e.fallas, e.comentarios, s.nombre_sede,
                COALESCE(e.pulse_score, (100 - (TIMESTAMPDIFF(YEAR, t.fecha_ingreso, CURDATE()) * 5) - (SELECT COUNT(*) FROM Entradas ent WHERE ent.id_unit_hardware = t.id_telefono) * 10)) as pulse_score
                FROM Telefonos t 
                LEFT JOIN Estatus e ON t.id_estatus = e.id_estatus 
                LEFT JOIN Sedes s ON t.id_sede = s.id_sede 
                ORDER BY t.id_telefono DESC');
            echo json_encode($stmt->fetchAll());
        }
        break;

    case '/entradas':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $assetId = $data['asset_id'] ?? null;
            $assetType = $data['asset_type'] ?? 'hardware';
            try {
                $pdo->beginTransaction();
                $idHard = ($assetType === 'hardware') ? $assetId : null;
                $idUnit = ($assetType === 'telefonos') ? $assetId : null;
                $stmt = $pdo->prepare('INSERT INTO Entradas (fecha_entrada, id_hardware, id_unit_hardware, numero_orden, nom_responsable, id_encargado) VALUES (:fecha, :idHard, :idUnit, :nOrden, :nomResp, :idEnc)');
                $stmt->execute([':fecha' => date('Y-m-d'), ':idHard' => $idHard, ':idUnit' => $idUnit, ':nOrden' => $data['numero_orden'] ?? null, ':nomResp' => $data['nom_responsable'] ?? 'OTIC', ':idEnc' => $data['id_encargado'] ?? null]);
                $idEntrada = $pdo->lastInsertId();
                $table = ($assetType === 'hardware') ? 'Hardware' : 'Telefonos';
                $idCol = ($assetType === 'hardware') ? 'id_hardware' : 'id_telefono';
                $stmt = $pdo->prepare("UPDATE Estatus SET estado_actual_unidad = 2 WHERE id_estatus = (SELECT id_estatus FROM $table WHERE $idCol = :id)");
                $stmt->execute([':id' => $assetId]);
                $pdo->commit();
                echo json_encode(['success' => true, 'id_entrada' => $idEntrada]);
            } catch (Exception $e) { $pdo->rollBack(); http_response_code(500); echo json_encode(['error' => 'Support entry failed', 'details' => $e->getMessage()]); }
        } else {
            $stmt = $pdo->query('SELECT e.*, h.tipo_hardware, h.marca_hardware, h.modelo_hardware, h.bienes_hardware, u.nombre_usuario as encargado_nombre FROM Entradas e LEFT JOIN Hardware h ON e.id_hardware = h.id_hardware LEFT JOIN Usuarios u ON e.id_encargado = u.id_usuario ORDER BY e.fecha_entrada DESC');
            echo json_encode($stmt->fetchAll());
        }
        break;

    case '/salidas':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $idEntrada = $data['id_entrada'] ?? null;
            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare('INSERT INTO Salidas (id_entrada, fecha_salida, nom_responsable, reporte, receptor) VALUES (:idEnt, :fecha, :nomResp, :reporte, :receptor)');
                $stmt->execute([':idEnt' => $idEntrada, ':fecha' => date('Y-m-d'), ':nomResp' => $data['nom_responsable'] ?? 'OTIC', ':reporte' => $data['reporte'] ?? '', ':receptor' => $data['receptor'] ?? '']);
                $idSalida = $pdo->lastInsertId();
                $stmt = $pdo->prepare('SELECT id_hardware, id_unit_hardware FROM Entradas WHERE id_entrada = :id');
                $stmt->execute([':id' => $idEntrada]);
                $entry = $stmt->fetch();
                if ($entry) {
                    $table = $entry['id_hardware'] ? 'Hardware' : 'Telefonos';
                    $idCol = $entry['id_hardware'] ? 'id_hardware' : 'id_telefono';
                    $assetId = $entry['id_hardware'] ?: $entry['id_unit_hardware'];
                    $stmt = $pdo->prepare("UPDATE Estatus SET estado_actual_unidad = 1 WHERE id_estatus = (SELECT id_estatus FROM $table WHERE $idCol = :id)");
                    $stmt->execute([':id' => $assetId]);
                }
                $pdo->commit();
                echo json_encode(['success' => true, 'id_salida' => $idSalida]);
            } catch (Exception $e) { $pdo->rollBack(); http_response_code(500); echo json_encode(['error' => 'Support exit failed', 'details' => $e->getMessage()]); }
        } else {
            $stmt = $pdo->query('SELECT s.*, e.fecha_entrada, e.numero_orden, h.tipo_hardware, h.marca_hardware, h.modelo_hardware, h.bienes_hardware FROM Salidas s LEFT JOIN Entradas e ON s.id_entrada = e.id_entrada LEFT JOIN Hardware h ON e.id_hardware = h.id_hardware ORDER BY s.fecha_salida DESC');
            echo json_encode($stmt->fetchAll());
        }
        break;

    case '/estatus':
        if ($_SERVER['REQUEST_METHOD'] === 'PUT' && $id) {
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare('UPDATE Estatus SET comments = :com, estado_actual_unidad = :state, fallas = :fail WHERE id_estatus = :id');
            $stmt->execute([
                ':com' => $data['comentarios'] ?? null,
                ':state' => $data['estado'] ?? 1,
                ':fail' => $data['fallas'] ?? null,
                ':id' => $id
            ]);
            echo json_encode(['success' => true]);
        }
        break;

    case '/usuarios':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $assetId = $data['asset_id'] ?? null;
            $assetType = $data['asset_type'] ?? 'hardware';
            $ci = $data['ci'] ?? '';
            
            try {
                $pdo->beginTransaction();
                
                $stmt = $pdo->prepare('SELECT id_usuario FROM Usuarios WHERE ci_usuario = :ci');
                $stmt->execute([':ci' => $ci]);
                $idUser = $stmt->fetchColumn();
                
                if (!$idUser) {
                    $stmt = $pdo->prepare('INSERT INTO Usuarios (nombre_usuario, apellido_usuario, ci_usuario) VALUES (:nom, :ape, :ci)');
                    $stmt->execute([':nom' => $data['nombre'] ?? '', ':ape' => $data['apellido'] ?? '', ':ci' => $ci]);
                    $idUser = $pdo->lastInsertId();
                }
                
                $userName = ($data['nombre'] ?? '') . ' ' . ($data['apellido'] ?? '');
                
                if ($assetType === 'hardware') {
                    $stmt = $pdo->prepare('UPDATE Hardware SET usuario_hardware = :user WHERE id_hardware = :id');
                    $stmt->execute([':user' => $userName, ':id' => $assetId]);
                } else {
                    $stmt = $pdo->prepare('UPDATE Telefonos SET usuario_asignado = :user WHERE id_telefono = :id');
                    $stmt->execute([':user' => $userName, ':id' => $assetId]);
                }
                
                $pdo->commit();
                echo json_encode(['success' => true, 'id_usuario' => $idUser]);
            } catch (Exception $e) { $pdo->rollBack(); http_response_code(500); echo json_encode(['error' => 'Assignment failed', 'details' => $e->getMessage()]); }
        }
        break;

    case '/auth/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';
            $secretKey = getenv('JWT_SECRET');

            if (!$secretKey) {
                http_response_code(500);
                echo json_encode(['error' => 'Server configuration error: Missing JWT_SECRET']);
                exit;
            }
            $stmt = $pdo->prepare('SELECT id_login, password_hash, role FROM Usuarios_Login WHERE username = :user');
            $stmt->execute([':user' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
                $payload = base64_encode(json_encode([
                    'id' => $user['id_login'],
                    'role' => $user['role'],
                    'exp' => time() + (86400 * 7)
                ]));
                $signature = base64_encode(hash_hmac('sha256', "$header.$payload", $secretKey, true));
                $token = "$header.$payload.$signature";

                echo json_encode([
                    'success' => true,
                    'token' => $token,
                    'user' => [
                        'username' => $username,
                        'role' => $user['role']
                    ]
                ]);
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid credentials']);
            }
        }
        break;

    case '/timeline':
        $type = $_GET['type'] ?? 'hardware';
        $id = $_GET['id'] ?? 0;
        
        if ($type === 'hardware') {
            $stmt = $pdo->prepare("
                SELECT 'Birth' as type, fecha_ingreso as date, 'Initial asset registration' as details, usuario_hardware as actor, 'bg-emerald-500' as color FROM Hardware WHERE id_hardware = :id
                UNION ALL
                SELECT 'Support Entry' as type, fecha_entrada as date, CONCAT('Support order: ', numero_orden) as details, nom_responsable as actor, 'bg-amber-500' as color FROM Entradas WHERE id_hardware = :id
                UNION ALL
                SELECT 'Support Exit' as type, s.fecha_salida as date, s.reporte as details, s.nom_responsable as actor, 'bg-blue-500' as color FROM Salidas s JOIN Entradas e ON s.id_entrada = e.id_entrada WHERE e.id_hardware = :id
                ORDER BY date ASC
            ");
        } else {
            $stmt = $pdo->prepare("
                SELECT 'Birth' as type, fecha_ingreso as date, 'Initial mobile registration' as details, usuario_asignado as actor, 'bg-emerald-500' as color FROM Telefonos WHERE id_telefono = :id
                UNION ALL
                SELECT 'Support Entry' as type, fecha_entrada as date, CONCAT('Support order: ', numero_orden) as details, nom_responsable as actor, 'bg-amber-500' as color FROM Entradas WHERE id_unit_hardware = :id
                UNION ALL
                SELECT 'Support Exit' as type, s.fecha_salida as date, s.reporte as details, s.nom_responsable as actor, 'bg-blue-500' as color FROM Salidas s JOIN Entradas e ON s.id_entrada = e.id_entrada WHERE e.id_unit_hardware = :id
                ORDER BY date ASC
            ");
        }
        $stmt->execute([':id' => $id]);
        echo json_encode($stmt->fetchAll());
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found', 'path' => $resource]);
        break;
}
