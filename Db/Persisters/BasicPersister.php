<?php
namespace Adm\Db\Persisters;

class BasicPersister
{
	protected $class = null;

	protected $entityName = null;

	protected $orm = null;

	protected $conn = null;

	public function __construct($orm,$entityName)
	{
		$this->orm = $orm;
		$this->conn = $orm->getConnection();
		$this->entityName = $entityName;
		$this->class = $this->orm->getClassAnnotation($entityName);
		var_dump($this->class);
	}

	public function getClassMetadata()
	{
		return $this->class;
	}

	public function load($criteria)
	{
		$tableAlias = $this->class->tableAlias;
		$selectClause = $this->generateSelectClause(array($this->entityName => $tableAlias));

		$whereR = [];
		$valueParams = [];
		foreach ($criteria as $key => $eq) {
			if (is_array($eq)) {
				$where[] = $key . " " . $eq['condition'] . " :" . $key;
				$valueParams[$key] = $eq['match'];
			} else {
				$where[] = $key . " = " . ":" . $key;
				$valueParams[$key] = $eq;
			}
		}
		$queryWhere = implode(' AND ', $where);

		$sth = $this->conn->execQuery("SELECT " . $selectClause . " FROM " . $this->class->table->name . " AS " . $tableAlias . " WHERE " . $queryWhere,$valueParams);
		return $sth;
	}

	public function generateSelectClause($tableAliases)
	{
		$propertyAliases = [];
		foreach ($tableAliases as $entityName => $tableAlias) {
			if ($entityName != $this->entityName) {
				if (isset($this->class->entities[$entityName])) {
					var_dump($entityName . 'isset');
				}
			}
			foreach ($properties as $propertyName => $propMetadata) {
				$propertyAliases[] = $tableAlias . "." . $propMetadata->Column->name . " AS `" . $tableAlias . "." . $propertyName . "`";
			}
		}

		return implode(",",$propertyAliases);
	}
}