<?php 

	// carga la vista
	$tpl = new poroto("panel");

	// // vector con variables para la vista
	$vars = ["PROYECT_SECTION" => "Panel"];

	// reemplaza las variables de la vista con los valores del vector
	$tpl->setVars($vars);
	
	// imprime la vista en la página
	$tpl->print();

 ?>