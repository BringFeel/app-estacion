<?php
require_once 'lib/DB.php';

if (empty($_SESSION['is_admin'])) {
    header('Location: login');
    exit();
}

$db = DB::getInstance();

$usersCount = 0;
try {
    $stmt = $db->execute('SELECT COUNT(*) AS c FROM usuarios');
    $row = $stmt->fetch();
    $usersCount = $row ? (int)$row['c'] : 0;
} catch (Exception $e) { }

$clientsCount = 0;
try {
    $stmt = $db->execute('SELECT COUNT(DISTINCT ip) AS c FROM tracker');
    $row = $stmt->fetch();
    $clientsCount = $row ? (int)$row['c'] : 0;
} catch (Exception $e) { }

$tpl = new poroto('administrator');
$tpl->setVars([
    'PROYECT_SECTION' => 'Administrator',
    'USERS_COUNT' => $usersCount,
    'CLIENTS_COUNT' => $clientsCount
]);
$tpl->print();
