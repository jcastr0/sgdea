<?php
/**
 * Test Database Connection - API Endpoint Directo
 *
 * Este archivo es un endpoint sin depender de Laravel
 * Acceso: POST /test-db-connection.php
 */

header('Content-Type: application/json; charset=utf-8');

try {
    $host = $_POST['db_host'] ?? $_GET['db_host'] ?? '';
    $port = $_POST['db_port'] ?? $_GET['db_port'] ?? '3306';
    $user = $_POST['db_root_user'] ?? $_GET['db_root_user'] ?? '';
    $pass = $_POST['db_root_password'] ?? $_GET['db_root_password'] ?? '';

    $response = [
        'connection' => false,
        'create_database' => false,
        'create_user' => false,
        'grant_privileges' => false,
        'messages' => [
            'connection' => '',
            'create_database' => '',
            'create_user' => '',
            'grant_privileges' => ''
        ],
        'debug' => []
    ];

    if (!$host || !$port || !$user) {
        $response['messages']['connection'] = 'Falta: Host, Puerto o Usuario';
        echo json_encode($response);
        exit;
    }

    // Conectar con PDO
    try {
        $dsn = "mysql:host={$host};port={$port}";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5
        ]);

        $response['connection'] = true;
        $response['messages']['connection'] = 'Conexión exitosa ✓';

        // Obtener usuario actual
        try {
            $stmt = $pdo->query("SELECT USER()");
            $userRow = $stmt->fetch(PDO::FETCH_NUM);
            $response['debug']['current_user'] = $userRow[0] ?? 'desconocido';
        } catch (Exception $e) {
            $response['debug']['current_user'] = 'Error al obtener usuario';
        }

        // Obtener GRANTS
        try {
            $stmt = $pdo->query("SHOW GRANTS FOR CURRENT_USER()");
            $grants = '';

            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $grants .= $row[0] . ' ';
            }
            $response['debug']['grants_found'] = !empty($grants);

            // Verificar permisos
            $has_all = (strpos($grants, 'ALL PRIVILEGES') !== false);
            $has_create = (strpos($grants, 'CREATE') !== false);
            $has_create_user = (strpos($grants, 'CREATE USER') !== false);
            $has_grant_option = (strpos($grants, 'GRANT OPTION') !== false);

            if ($has_all || $has_create) {
                $response['create_database'] = true;
                $response['messages']['create_database'] = 'Permiso CREATE DATABASE ✓';
            } else {
                $response['messages']['create_database'] = 'Sin permiso CREATE';
            }

            if ($has_all || $has_create_user) {
                $response['create_user'] = true;
                $response['messages']['create_user'] = 'Permiso CREATE USER ✓';
            } else {
                $response['messages']['create_user'] = 'Sin permiso CREATE USER';
            }

            if ($has_all || $has_grant_option) {
                $response['grant_privileges'] = true;
                $response['messages']['grant_privileges'] = 'Permiso GRANT OPTION ✓';
            } else {
                $response['messages']['grant_privileges'] = 'Sin permiso GRANT';
            }

        } catch (Exception $e) {
            $response['debug']['grants_error'] = $e->getMessage();
        }

        $pdo = null;

    } catch (PDOException $e) {
        $response['messages']['connection'] = 'Conexión fallida: ' . $e->getMessage();
        $response['debug'] = [
            'host' => $host,
            'port' => $port,
            'user' => $user,
            'error' => $e->getMessage()
        ];
    }

    echo json_encode($response);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'connection' => false,
        'create_database' => false,
        'create_user' => false,
        'grant_privileges' => false,
        'messages' => [
            'error' => $e->getMessage()
        ]
    ]);
    exit;
}
?>

