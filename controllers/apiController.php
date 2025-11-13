<?php
require_once 'lib/DB.php';

header('Content-Type: application/json; charset=utf-8');

if (isset($_GET['list-clients-location'])) {
    try {
        $db = DB::getInstance();
        $sql = "SELECT ip, MAX(latitud) AS latitud, MAX(longitud) AS longitud, COUNT(*) AS accesses FROM tracker GROUP BY ip";
        $stmt = $db->execute($sql);
        $rows = $stmt->fetchAll();
        echo json_encode($rows ?: []);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "DB error", "message" => $e->getMessage()]);
    }
    exit;
}

echo json_encode(["error" => "Invalid parameter"]);
exit;
