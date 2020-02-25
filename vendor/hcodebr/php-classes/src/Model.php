<?php

	namespace Hcode;

	class Model {

		private $values = [];

		public function __call($name, $args)
		{
			$method = substr($name, 0, 3);
			$fieldName = substr($name, 3, strlen($name));

			switch ($method) {

				case "get":
					return (isset($this->values[$fieldName])) ? $this->values[$fieldName] : NULL; // se ao executar o get+NomeCampo não foi encontrado, retorna NULL
					break;
				
				case "set":

					$this->values[$fieldName] = $args[0];
					break;
			}
			
		}

		public function setData($data = array())
		{
			foreach ($data as $key => $value) {
				// o uso de chaves abaixo possibilita fazer a concatenação e utilizar como comando
				$this->{"set" . $key}($value); // é o mesmo que usar setidusuario(1)
			}
		}

		public function getValues()
		{
			return $this->values;
		}


	}



?>