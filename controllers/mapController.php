<?php
if (empty($_SESSION['is_admin'])) {
    header('Location: panel');
    exit();
}

$tpl = new poroto('map');
$tpl->setVars([
    'PROYECT_SECTION' => 'Mapa de clientes'
]);
$tpl->print();
