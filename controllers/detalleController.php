<?php 
    // controllers/detalleController.php

    // Requerimiento: ahora solo disponible si el usuario está logueado, en caso de no estarlo redirecciona a "login".
    if (!isset($_SESSION['user_id'])) {
        header("Location: login");
        exit();
    }

    // carga la vista
    $tpl = new poroto("detalle");

    // vector con variables para la vista
    $vars = ["PROYECT_SECTION" => "Detalle de Estación"];

    // reemplaza las variables de la vista con los valores del vector
    $tpl->setVars($vars);
    
    // imprime la vista en la página
    $tpl->print();

?>