<?php

	namespace Hcode;

	class Model {

		private $values = [];

		public function __call($name, $args)
		{
			$method = substr($name, 0, 3); // set ou get
			$fieldName = substr($name, 3, strlen($name)); // nome do campo chamado (ex: em 'setusuario' $fieldName = 'usuario'

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
				$this->{"set" . $key}($value);
			}
		}

		public function getValues()
		{
			return $this->values;
		}


	}



?>