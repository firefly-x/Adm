<?php

namespace Adm\Utils;

use Adm\Db\AnnotationReader;
use Adm\Common\Converters;
class ObjectBinder
{
	
	public static function bind($array,$object,$params = null)
	{
		$refl = new \ReflectionClass($object);
		$reader = new AnnotationReader();
		$classMetadata = $reader->getClassAnnotation($object);

		$sourceType = isset($params['sourcetype']) ? $params['sourcetype'] : null;
		$converter = Converters\ConverterFactory::getConverter($sourceType);
		
		foreach ($array as $key => $value) {
			try {
				$prop = $refl->getProperty($key);								
				$prop->setAccessible(true);
				$prop->setValue($object,$converter->convert($classMetadata->properties[$prop->getName()]->Column->type,$value));
			} catch (\ReflectionException $e) {
				
			}
		}
	}	

}