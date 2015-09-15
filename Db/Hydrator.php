<?php

namespace Adm\Db;

class Hydrator
{
	
	protected $orm;

	public function __construct($orm)
	{
		$this->orm = $orm;
	}

	public function hydrate($data,$className)
	{		
		$class = $this->orm->getClassAnnotation($className);
		$reflection = new \ReflectionClass($className);
		$instance = $reflection->newInstanceArgs();

		foreach ($class->properties as $propertyName => $propertyArgs)
		{
			if (isset($propertyArgs->Column)) {				
				$reflProperty = $class->reflectionFields[$propertyName];
				$reflProperty->setAccessible(true);
				$reflProperty->setValue($instance,$this->convertToPHPValue($propertyArgs->Column->type,$data[$class->tableAlias.".".$reflProperty->name]));
			}
		}
		return $instance;
	}

	public function extract($instance)
	{
		$data = [];

		$class = $this->orm->getClassAnnotation($instance);

		foreach ($class->properties as $propertyName => $propertyAnnt)
		{			
			if (isset($propertyAnnt->Column)) {
				$reflProperty = $class->reflectionFields[$propertyName];
				$reflProperty->setAccessible(true);
				$value = $this->convertToPersisterValue($propertyAnnt->Column->type,$reflProperty->getValue($instance));
				$data[$propertyAnnt->Column->name] = $value;
			}
		}

		return $data;
	}

	protected function convertToPHPValue($type,$value)
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
			case 'date':
				$result = new \DateTime($value);
				break;
			case 'datetime':
				$result = new \DateTime($value);
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

	protected function convertToPersisterValue($type,$value)
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
			case 'date':
				$result = $value->format('Y-m-d');
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