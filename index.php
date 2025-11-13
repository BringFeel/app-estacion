<?php

	// incluye las variables de entorno
	include_once 'env.php';

	// incluimos el motor de plantillas
	include_once 'lib/poroto/poroto.php';

	session_start();

	// Router con auto carga de controladores 

	// por defecto seccion es landing
	$seccion = "landing";

	// Parámetro adicional para el token (ej: validate/TOKEN)
	$token_param = null;

	// si existe slug por GET
	if(!empty($_GET['slug'])){
		$parts = explode('/', $_GET['slug']);
        $seccion = $parts[0]; // La primera parte es el controlador
        if (count($parts) > 1) {
            $token_param = $parts[1]; // La segunda parte es el token/token_action
        }
	}



	// si no existe el archivo del controlador
	if(!file_exists('controllers/'.$seccion.'Controller.php')){
		// seccion se carga con el controlador de error 404
		$seccion = "error404";
	}

	// carga del controlador
	include 'controllers/'.$seccion.'Controller.php';

 ?>