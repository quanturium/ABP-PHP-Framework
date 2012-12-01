<?php

class DB extends AbstractPlugin
{

	private static $_instance = null;
	private $PDOInstance = null;
	private $_preparedQuery = array();
	private $_nbQueryExecuted = 0;

	function __construct()
	{
		$this->PDOInstance = new PDO('mysql:dbname=' . Core::config('db', 'connexion', 'dbname') . ';host=' . Core::config('db', 'connexion', 'hostname') . ';port=' . Core::config('db', 'connexion', 'port'), Core::config('db', 'connexion', 'username'), Core::config('db', 'connexion', 'password'));
		$this->PDOInstance->exec('SET NAMES utf8');
	}

	public function __clone()
	{
		System::error('Cloning the DB is not permitted');
	}

	public static function singleton()
	{
		if (!isset(self::$_instance))
		{
			self::$_instance = new DB;
		}

		return self::$_instance;
	}

	private function getQuery($queryName)
	{
		if (Core::config('db', 'queries', $queryName))
		{
			return Core::config('db', 'queries', $queryName);
		}
		else
		{
			System::error('query' . $queryName . ' does not exist');
		}
	}

	public function exec($queryName, $data = array())
	{
		// Avoid same query to be prepared twice
		if (!isset($this->_preparedQuery[$queryName]))
		{
			$query = $this->getQuery($queryName);
			$this->_preparedQuery[$queryName] = $this->PDOInstance->prepare($query);
		}

		foreach ($data as $k => $v)
		{
			if (is_int($v))
			{
				$this->_preparedQuery[$queryName]->bindValue($k, (int) $v, PDO::PARAM_INT);
			}
			else
			{
				$this->_preparedQuery[$queryName]->bindValue($k, $v);
			}
		}

		$this->_preparedQuery[$queryName]->execute();
		$this->_nbQueryExecuted++;
	}

	public function query($query)
	{
		$result = $this->PDOInstance->query($query);
		return $result->fetchAll();
	}

	// return SELECT result 
	public function fetch($queryName)
	{
		$return = $this->_preparedQuery[$queryName]->fetchAll(PDO::FETCH_ASSOC);
		$this->resetQuery($queryName);

		return $return;
	}

	// return nb rows affected by UPDATE, INSERT, DELETE
	public function rowCount($queryName)
	{
		$return = $this->_preparedQuery[$queryName]->rowCount();
		$this->resetQuery($queryName);

		return $return;
	}

	public function lastInsertId()
	{
		return $this->PDOInstance->lastInsertId();
	}

	private function resetQuery($queryName)
	{
		$this->_preparedQuery[$queryName]->closeCursor();
	}

	public function nbQueryExecuted()
	{
		return $this->_nbQueryExecuted;
	}

}

?>