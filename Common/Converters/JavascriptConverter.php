<?php

namespace Adm\Common\Converters;

class JavascriptConverter
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
				$result = $value ? true : false;
				break;
			case 'datetime':								
				$dTime = new \DateTime($value->date,new \DateTimeZone($value->timezone));
				$result = $dTime;
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