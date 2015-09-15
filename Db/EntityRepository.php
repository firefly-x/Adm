<?php

namespace Adm\Db;

class EntityRepository
{
	
	protected $annotationReader = null;

	protected $dbDriver = null;

	protected $orm = null;

	protected $className = null;

	public function __construct($orm,$className,$args = null)
	{
		$this->orm = $orm;
		$this->dbDriver = $orm->getConnection();
		$this->annotationReader = $orm->getAnnotationReader();
		$this->className = $className;
	}

	protected function queryBy($criteria)
	{
		return $this->orm->getUnitOfWork()->queryBy($criteria,$this->className);
	}

	protected function hydrateAll($sth,$className = null)
	{		
		while ($obj = $this->hydrateEntity($sth,$className)) {
			$results[] = $obj;
		}
		return isset($results) ? $results : [];
	}

	protected function hydrateEntity($sth,$className = null)
	{
		$className = ($className) ? : $this->className;
		$row = $sth->fetch(\PDO::FETCH_ASSOC);
		return $row ? $this->orm->newHydrator()->hydrate($row,$className) : false;
	}

	public function findAll()
	{
		$sth = $this->orm->getUnitOfWork()->findAll($this->className);
		return $this->hydrateAll($sth);
	}

	public function findBy($criteria)
	{
		$sth = $this->queryBy($criteria);
		return $this->hydrateAll($sth);
	}

	public function findOneBy($criteria)
	{
		$sth = $this->queryBy($criteria);
		return $this->hydrateEntity($sth);
	}



}