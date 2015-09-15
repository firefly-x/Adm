<?php

namespace Adm\Db;

class AnnotationReader
{	


	public function __construct()
	{
	}

	public function getClassAnnotation($class)
	{
		$reflectionClass = new \reflectionClass($class);

		$entityAnnotation = $this->readBlock($reflectionClass->getDocComment());
		$entityAnnotation->entities[$class] = array('type' => 'root');
		foreach ($reflectionClass->getProperties() as $property) {
			$entityAnnotation->reflectionFields[$property->getName()] = $property;
			$mappedAnnotation = $this->readBlock($property->getDocComment());
			if (isset($mappedAnnotation->ManyToOne) && $mappedAnnotation->ManyToOne->fetch == 'EAGER') {
				$entityAnnotation->entities[$mappedAnnotation->ManyToOne->targetEntity] = array('type' => 'join','joinmeta' => $mappedAnnotation);
			}
			if (isset($mappedAnnotation->Column)) {
				$entityAnnotation->properties[$property->getName()] = $mappedAnnotation;
			}
			if (isset($mappedAnnotation->identifier)) {
				$entityAnnotation->identifierFields[] = $property->getName();
			}
		}
		$entityAnnotation->tableAlias = substr($entityAnnotation->table->name,0,1);		
		return $entityAnnotation;
	}

	public function readBlock($unparsedBlock)
	{		
		$commentParams = array_filter(explode('@', trim(substr($unparsedBlock,3,-2))));
		$params = new \StdClass();
		foreach ($commentParams as $param) {
			$commandBegin = strpos($param, '(');
			$commandEnd = strrpos($param, ')');

			if ($commandBegin) {
				$command = substr($param,0,$commandBegin);

				$paramBlock = substr($param,$commandBegin+1,($commandEnd-$commandBegin)-1);

				$args = $this->parseParamsToObject($paramBlock);
				$params->$command = $args;				
			} else {
				$command = trim($param);
				$params->$command = true;
			}
			if ($command == 'Id') {				
				$params->identifier = true;
			}
		}
		return $params;
	}

	/**
	 *  parses string into object
	 *	ex. name="adem",lastname="saglam"
	 *	returns {name: "adem","lastname"}
	 * @return object
	 * 
	 **/	
	private function parseParamsToObject($unparsedParams)
	{
		$params = explode(',', $unparsedParams);
		$result = new \StdClass();

		foreach ($params as $param)
		{
			$propertyName = substr($param,0,strpos($param,'='));
			$value = substr($param,strpos($param,'=')+1);
			$result->$propertyName = trim($value,'\"');
		}
		return $result;
	}

}