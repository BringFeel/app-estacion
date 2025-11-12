<?php
	// carga la vista
	$tpl = new poroto("detalle");

	// variables a reemplazar en la vista
	$vars = ["PROYECT_SECTION" => "Detalle"];
	
	// reemplaza las variables de la vista con los valores del vector 
	$tpl->setVars($vars);

	// imprime la vista en la página
	$tpl->print();


 ?>