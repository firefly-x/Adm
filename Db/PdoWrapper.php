<?php

namespace Adm\Db;

class PdoWrapper {
	
	public $handler = null;
	
	public function __construct($dsn,$user,$pass)
	{
		$this->handler = new \PDO($dsn,$user,$pass);
	}

	public function getConnection()
	{
		return $this->handler;
	}

	public function execQuery($sql,$params = array())
	{
		$sth = $this->getConnection()->prepare($sql);
		foreach ($params as $key => $value) {			
			$sth->bindParam($key,$params[$key]);
		}
		if (!$sth->execute()) {
			var_dump($sth->errorInfo());
		}
		return $sth;
	}	

	public function findAll($sql,$params = array(),$className)
	{
		$results = array();
		$sth = $this->execQuery($sql,$params);
		while ($obj = $sth->fetchObject($className)) {
			$results[] = $obj;
		}
		return $results;
	}
	public function findOne($sql,$params = array(),$className)
	{
		$stm = $this->execQuery($sql,$params);
		return $stm->fetchObject($className);
	}

	/**buraya ait degil */
	public function findOneById($model,$id)
	{
		return $this->findOne('SELECT * FROM MusteriFiles WHERE id = :id',array('id' => $id),$model);
	}

	/** buraya ait degil */
	public function findBy($model,$criteria)
	{
		$results = array();

		$stm = $this->execQuery('SELECT * FROM MusteriFiles WHERE '.key($criteria).' = :value',array(':value' => $criteria[key($criteria)]));
		while ($obj = $stm->fetchObject($model)) {
			$results[] = $obj;
		}
		return $results;
	}

	public function insert($table,$fields)
	{
		$fieldNames = implode('`,`', array_keys($fields));
		$params = array();
		foreach ($fields as $key => $value) {
			$params[':' . $key] = $value;
		}
		$this->execQuery('INSERT INTO '.$table.' (`'.$fieldNames.'`) VALUES('.implode(',', array_keys($params)).')',$params);
		return $this->getConnection()->lastInsertId();
	}

	public function update($criteria,$table,$fields)
	{
		if (isset($criteria)) {
			$setWhere = 'WHERE ' . key($criteria) . '=\'' . $criteria[key($criteria)] . '\'';
		} else {
			throw new Exception("Kayıt güncellenemiyor, kaydın geçerli bir kriteri yok.", 1);			
		}
		$fieldsAndValues = [];
		foreach ($fields as $key => $value) {
			$fieldsAndValues[] = $key . '=\'' . $value . '\'';
		}
		$setLine = implode(',', $fieldsAndValues);

		$query = 'UPDATE ' . $table . ' SET ' . $setLine . ' ' . $setWhere;
		$this->execQuery($query);
	}

	public function delete($criteria,$table)
	{
		if (isset($criteria)) {
			$setWhere = 'WHERE ' . key($criteria) . '=\'' . $criteria[key($criteria)] . '\'';
		} else {
			throw new Exception("Kayıt silinemiyor, kaydın silmek için kriterler uymuyor.", 1);			
		}

		$query = 'DELETE FROM ' . $table . ' ' . $setWhere;
		$this->execQuery($query);
	}

	public function saveModel($model)
	{
		$reflect = new ReflectionClass($model);
		$properties = $reflect->getProperties();
		$fields  = array();
		foreach ($properties as $propObj) {
			$propname = $propObj->name;
			$fields[$propname] = $model->$propname;
		}
		$annotation = $this->annotationReader($reflect);
	 	
		return $this->insert($annotation->table,$fields);
	}
	public function annotationReader(ReflectionClass $reflect)
	{
		$doc = trim(substr($reflect->getDocComment(),3,-2));
		$blockParam = explode(',', $doc);
		$params = new StdClass();
		foreach ($blockParam as $param) {
			$seperatorPos = strpos($param, '=');
			$property = substr($param,0,$seperatorPos);
			$value = trim(substr($param,$seperatorPos+1),'\"');
			$params->$property = $value;
		}
		return $params;
	}

}