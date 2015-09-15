<?php

namespace Adm\Db;


class UnitOfWork {

	const STATE_NEW = 1;

	const STATE_MANAGED = 2;

	const STATE_NEEDUPDATE = 3;
	
	
	protected $orm = null;

	protected $persistQueue = [];

	protected $insertQueue = [];

	protected $updateQueue = [];

	protected $removeQueue = [];

	public function __construct($orm)
	{
		$this->orm = $orm;
	}

	public function getPersister($entityName)
	{
		return new Persisters\BasicPersister($this->orm,$entityName);
	}

	public function queryBy($criteria,$entityName)
	{
		$persister = $this->getPersister($entityName);
		return $persister->load($criteria);
	}

	public function findAll($class)
	{
		$classAnnotation = $this->orm->getClassAnnotation($class);

		$sth = $this->orm->getConnection()->execQuery("SELECT " . $classAnnotation->table->name . ".* FROM " . $classAnnotation->table->name);
		return $sth;
	}

	public function persist($entity)
	{
		$class = $this->orm->getAnnotationReader()->getClassAnnotation($entity);

		if ($this->computeState($class,$entity) == self::STATE_NEW) {
			$this->insertQueue[] = $entity;
		}
		if ($this->computeState($class,$entity) == self::STATE_NEEDUPDATE) {
			$this->updateQueue[] = $entity;
		}
	}

	public function remove($entity)
	{
		$classMetadata = $this->orm->getAnnotationReader()->getClassAnnotation($entity);

		$this->removeQueue[] = $entity;
	}

	public function commit()
	{
		foreach ($this->insertQueue as $entity) {
			$class = $this->orm->getAnnotationReader()->getClassAnnotation($entity);
			$this->insertEntity($class,$entity);
		}

		foreach ($this->updateQueue as $entity) {
			$class = $this->orm->getAnnotationReader()->getClassAnnotation($entity);
			$this->updateEntity($class,$entity);
		}

		foreach ($this->removeQueue as $entity) {
			$classMetadata = $this->orm->getAnnotationReader()->getClassAnnotation($entity);
			$this->removeEntity($classMetadata,$entity);
		}

		$this->insertQueue = [];
		$this->updateQueue = [];
		$this->removeQueue = [];
	}

	public function computeState($class,$entity)
	{
		foreach ($class->identifierFields as $idField) {
			$refl = new \ReflectionClass($entity);
			$idProperty = $refl->getProperty($idField);
			$idProperty->setAccessible(true);
			if ($idProperty->getValue($entity) != null) {
				return self::STATE_NEEDUPDATE;
			} else {
				return self::STATE_NEW;
			}
		}
	}

	public function getIdentifier($class,$entity)
	{
		$idReflProperty = $class->reflectionFields[$class->identifierFields[0]];
		$idReflProperty->setAccessible(true);
		$result[$class->identifierFields[0]] = $idReflProperty->getValue($entity);
		return $result;
	}

	public function insertEntity($class,$entity)
	{
		$hydrator = $this->orm->newHydrator();

		$data = $hydrator->extract($entity);

		$insertedId = $this->orm->getConnection()->insert($class->table->name,$data);
		/** add inserted id to entity's identifier field */
		$class->reflectionFields[$class->identifierFields[0]]->setAccessible(true);
		$class->reflectionFields[$class->identifierFields[0]]->setValue($entity,$insertedId);
	}	

	public function updateEntity($class,$entity)
	{
		$hydrator = $this->orm->newHydrator();

		$data = $hydrator->extract($entity);
		$idField = $this->getIdentifier($class,$entity);

		$persister = $this->orm->getConnection();
		$persister->update($idField,$class->table->name,$data);
	}

	public function removeEntity($class,$entity)
	{
		$idField = $this->getIdentifier($class,$entity);

		$persister = $this->orm->getConnection();
		$persister->delete($idField,$class->table->name);
	}



}