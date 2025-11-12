<?php

	// incluye las variables de entorno
	include_once 'env.php';

	// incluimos el motor de plantillas
	include_once 'lib/poroto/poroto.php';

	session_start();

	// Router con auto carga de controladores 

	// por defecto seccion es landing
	$seccion = "landing";

	// si existe slug por GET
	if($_GET['slug']!=""){
		$seccion = $_GET['slug'];
	}



	// si no existe el archivo del controlador
	if(!file_exists('controllers/'.$seccion.'Controller.php')){
		// seccion se carga con el controlador de error 404
		$seccion = "error404";
	}

	// carga del controlador
	include 'controllers/'.$seccion.'Controller.php';

 ?>