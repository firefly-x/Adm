<?php

namespace Adm\Db;

class Orm
{
	
	private $annotationDriver = null;

	private $dbDriver = null;

	private $unitOfWork;

	public function __construct($dbDriver,$annotationDriver)
	{
		$this->dbDriver = $dbDriver;
		$this->annotationDriver = $annotationDriver;
		$this->unitOfWork = new UnitOfWork($this);
	}

	public function getRepository($className,$args = null)
	{
		$classAnnotation = $this->annotationDriver->getClassAnnotation($className);
		$params = array('orm' => $this,'class' => $className,'extra' => $args);

		if (isset($classAnnotation->entity->repositoryClass)) {
			$reflection = new \ReflectionClass($classAnnotation->entity->repositoryClass);
			return $reflection->newInstanceArgs($params);
		} else {
			$reflection = new \ReflectionClass('Adm\Db\EntityRepository');
			return $reflection->newInstanceArgs($params);
		}
	}

	public function getUnitOfWork()
	{
		return $this->unitOfWork;
	}

	public function newHydrator()
	{
		return new Hydrator($this);
	}

	public function getAnnotationReader()
	{
		return $this->annotationDriver;
	}

	public function getClassAnnotation($className)
	{
		return $this->annotationDriver->getClassAnnotation($className);
	}

	public function getConnection()
	{
		return $this->dbDriver;
	}

	public function persist($entity)
	{
		$this->unitOfWork->persist($entity);
	}

	public function remove($entity)
	{
		$this->unitOfWork->remove($entity);
	}

	public function flush()
	{
		$this->unitOfWork->commit();
	}

	public function nativeQuery($sql,$params = array())
	{
		return $this->getConnection()->execQuery($sql,$params);
	}

	public function generateSelectClause($entityName,$aliases)
	{
		$persister = $this->unitOfWork->getPersister($entityName);
		return $persister->generateSelectClause($entityName,$aliases);
	}
}