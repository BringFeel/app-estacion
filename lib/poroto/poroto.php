<?php


	/**
	 * 
	 * Motor de plantillas para php
	 * 
	 * */
	class poroto{

		private $buffer; // contenido de la vista
		public $vista; // nombre de la vista

		/**
		 * 
		 * Se ejecuta al instanciar la clase
		 * @param string $vista nombre de la vista
		 * 
		 * */
		function __construct($vista){
			$this->vista = $vista;

			/*< valida que exista el archivo de la vista*/
			if(!file_exists("views/".$vista."View.html")){

				echo "No se encontro la vista <b>$vista</b>";
				exit();
			}

			/*< almacena el contenido del documento en un atributo de la clase*/
			$this->buffer = file_get_contents("views/".$vista."View.html");

			/*< carga los archivos externos donde se solicitan en buffer*/
			$this->loadExtern();

			/*< variables de entorno clasicas para el header */
			$vars=[
				"PROYECT_NAME" => $_ENV['PROYECT_NAME'],
				"PROYECT_DESCRIPTION" => $_ENV['PROYECT_DESCRIPTION'],
				"PROYECT_URL" => $_ENV['PROYECT_URL'],
				"PROYECT_AUTHOR" => $_ENV['PROYECT_AUTHOR'],
				"PROYECT_EMAIL_AUTHOR" => $_ENV['PROYECT_EMAIL_AUTHOR'],
				"PROYECT_MODE" => $_ENV['PROYECT_MODE'],
				"PROYECT_KEYWORDS" => $_ENV['PROYECT_KEYWORDS'],
			];

			/*< carga las variables dentro del buffer */
			$this->setVars($vars);
		}

		/**
		 * 
		 * Reemplaza las variables del buffer
		 * @param array $vars Es un arreglo asociativo la llave es el nombre de la variable
		 * 
		 * */
		function setVars($vars){

			foreach ($vars as $needle => $content) {

				if($this->testVar($needle)){
					$this->buffer =str_replace("{{".$needle."}}", $content, $this->buffer); 
				}else{

					echo "No existe en la vista la variable <b>$needle</b>";
					exit();
				}
			}
		}

		/**
		 * 
		 * Verifica si existe la variable dentro del buffer
		 * @param string $name nombre de la variable
		 * @return bool variable existe | no existe
		 * 
		 * */
		function testVar($name){
			return strpos($this->buffer, $name);
		}


		/**
		 * 
		 * Carga los archivos solicitados en extern
		 * 
		 * */
		private function loadExtern(){
		
			// Patrón de REGEX

			$pattern = "/@extern\(['\"]([a-zA-Z0-9_]+)['\"]\)/";

			/*< busca todas las coincidencias dentro de la plantilla*/
			preg_match_all($pattern, $this->buffer, $list_okey);

			/*< recorre todas las coincidencias*/
			foreach ($list_okey[1] as $key => $file_extern) {

				/*@extern a encontrar para luego ser reemplazado*/
				$needle_extern = $list_okey[0][$key];

				// no se encontro el archivo externo
				if(!file_exists("views/".$file_extern.".html")){
					echo "No se encontro el archivo externo: ". $file_extern;
					exit();
				}

				/*< carga del archivo externo*/
				$buffer_extern = file_get_contents("views/".$file_extern.".html");

				/*< reemplazo del pattern con el contenido del archivo y esto dentro de la plantilla*/
				$this->buffer = str_replace($needle_extern, $buffer_extern, $this->buffer);

			}
		}

		/**
		 * 
		 * Imprime en la pagina el contenido de buffer
		 * 
		 * */
		function print(){
			echo $this->buffer;
		}
		
	}


 ?>