<?php

	namespace Atiksoftware\Schema;

	class Merger
	{

		public $schema = [];


		private function isTypeNameInteger($type){
			return $type == "integer" || $type == "int" ? true : false;
		}
		private function isTypeNameString($type){
			return $type == "string" || $type == "str" ? true : false;
		}
		private function isTypeNameBoolean($type){
			return $type == "boolean" || $type == "bool" ? true : false;
		}
		private function isTypeNameFloat($type){
			return $type == "float" || $type == "double" ? true : false;
		}
		private function isTypeNameArray($type){
			return $type == "array" || $type == "arr" ? true : false;
		}
		private function isTypeNameObject($type){
			return $type == "object" || $type == "obj" ? true : false;
		}

		private function toType($data, $type = "string"){
			if( $this->isTypeNameInteger($type) ){
				return (int)$data;
			}
			if( $this->isTypeNameString($type) ){
				return (string)$data;
			}
			if( $this->isTypeNameBoolean($type) ){
				return (boolean)$data;
			}
			if( $this->isTypeNameFloat($type) ){
				return (float)$data;
			}
			if( $this->isTypeNameArray($type) ){
				return (array)$data;
			}
			if( $this->isTypeNameObject($type) ){
				return (object)$data;
			}
			return $data;
		}

		private function checkLimits($data,$schema){
			if($this->isTypeNameInteger($schema["_type"]) && is_integer($data)){
				if(isset($schema["_min"]) && $data < $schema["_min"]){
					$data = $schema["_min"];
				}
				if(isset($schema["_max"]) && $data > $schema["_max"]){
					$data = $schema["_max"];
				}
			}
			if($this->isTypeNameString($schema["_type"]) && is_string($data)){
				if(isset($schema["_maxlen"]) && strlen($data) > $schema["_maxlen"]){
					$data = substr($data,0,$schema["_maxlen"]);
				}
			}
			return $data;
		}

		private function format($data,$schema){
			if(isset($schema["_format"])){
				if($schema["_format"] == "upper" || $schema["_format"] == "uppercase"){
					$data = \Atiksoftware\Cover\Text::toUpper($data);
				}
				if($schema["_format"] == "lower" || $schema["_format"] == "lowercase"){
					$data = \Atiksoftware\Cover\Text::toLower($data);
				}
				if($schema["_format"] == "fname" || $schema["_format"] == "firstname"){
					$data = \Atiksoftware\Cover\Text::formatFirstName($data);
				}
				if($schema["_format"] == "lname" || $schema["_format"] == "lastname"){
					$data = \Atiksoftware\Cover\Text::formatLastName($data);
				}
				if($schema["_format"] == "fullname"){
					$data = \Atiksoftware\Cover\Text::formatFullName($data);
				}
			}
			return $data;
		}

		public function copySchemaItem($item){
			unset($item["_type"]);
			unset($item["_default"]);
			unset($item["_maxlen"]);
			unset($item["_min"]);
			unset($item["_max"]);
			unset($item["_format"]);
			return $item;
		}
		public function setSchema($schema){
			$this->schema = $schema;
		}

		public function Migrate($data,$schema = false, $forceClear = false, $deep = 0){
			if($deep > 20){ return false; }

			if(!$schema){
				$schema = $this->schema;
			}
			foreach($schema as $sKey => $sItem){
				if(!isset($data[$sKey])){
					if(isset($sItem["_default"])){
						$data[$sKey] = $sItem["_default"];
					}
					if($sItem["_type"] == "array"){
						$_sItem = $sItem;
						unset($_sItem["_type"]);
						unset($_sItem["_default"]);
						if(count($_sItem)){
							$data[$sKey] = $this->Migrate( [], $_sItem, $forceClear, ($deep+1) );
						}
					}
				}
				else{
					if($sItem["_type"] == "array"){
						$_sItem = $this->copySchemaItem($sItem);
						$data[$sKey] = $this->Migrate( $data[$sKey], $_sItem, $forceClear, ($deep+1) );
					}
					$data[$sKey] = $this->toType($data[$sKey],$sItem["_type"]);
					$data[$sKey] = $this->checkLimits($data[$sKey],$sItem);
					$data[$sKey] = $this->format($data[$sKey],$sItem);
				}


			}
			return $data;
		}



	}
