<?php

namespace Adm\Common\Converters;

class MysqlConverter
{
	
	public function convert($type,$value)	
	{		
		switch ($type) {
			case 'string':
				$result = $value;
				break;
			case 'int':
				$result = intval($value);
				break;
			case 'boolean':
				$result = $value ? 1 : 0;
				break;
			case 'datetime':
				$result = $value->format('Y-m-d H:i:s');
				break;
			case 'decimal':
				$result = $value;
				break;
			default:
				throw new \Exception("Alanın tipi okunamıyor alanin degeri: " . $value , 1);
				break;
		}
		return $result;
	}
}