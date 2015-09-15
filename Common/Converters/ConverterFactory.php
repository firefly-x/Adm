<?php

namespace Adm\Common\Converters;

class ConverterFactory
{
	
	public static function getConverter($type = null)
	{
		switch ($type) {
			case 'javascript':
				return new JavascriptConverter();
				break;
			case 'mysql':
				return new MysqlConverter();
				break;
			default:
				return new StandartConverter();
				break;
		}
		return false;
	}

}