<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\ClassLoader;
use Doctrine\DBAL\Logging\SQLLogger;

require_once PLUGINS . DS . 'doctrine/Doctrine/Common/ClassLoader.php';
require_once PLUGINS . DS . 'doctrine/Doctrine/ORM/Tools/Setup.php';
require_once PLUGINS . DS . 'doctrine/Doctrine/Common/Persistence/ObjectRepository.php';
require_once PLUGINS . DS . 'doctrine/Doctrine/ORM/EntityRepository.php';
require_once PLUGINS . DS . 'doctrine/Doctrine/DBAL/Logging/SQLLogger.php';

/**
 * @property EntityManager $_em
 */
class Doctrine extends AbstractPlugin
{

	private static $_instance = null;
	private $_em;

	private function __construct()
	{
		Setup::registerAutoloadDirectory(__DIR__);

		$dbParams = array(
			'driver' => 'pdo_mysql',
			'hostname' => Core::config('doctrine', 'connexion', 'hostname'),
			'user' => Core::config('doctrine', 'connexion', 'username'),
			'password' => Core::config('doctrine', 'connexion', 'password'),
			'dbname' => Core::config('doctrine', 'connexion', 'dbname')
		);
		$path = array(self::getEntitiesPath());
		$config = Setup::createAnnotationMetadataConfiguration($path, Core::config('core', 'general', 'devel'));
		$config->setSQLLogger(new Profiler());
		
		$this->_em = EntityManager::create($dbParams, $config);
	}

	public static function getInstance()
	{
		return is_null(self::$_instance) ? self::$_instance = new Doctrine() : self::$_instance;
	}
	
	public static function getEntitiesPath()
	{
		return CLASSES . DS . 'entities';
	}
	
	public static function includeEntities()
	{		
		$path = self::getEntitiesPath();
		
		foreach (glob($path . '/*') as $filename)
		{
			include_once $filename;
		}
	}

	public function getEntityManager()
	{
		return $this->_em;
	}
	
	public function getInfos()
	{
		$profiler = $this->_em->getConfiguration()->getSQLLogger();
		
		return array("count" => $profiler->getNbQueries(), "time" => $profiler->getTotalTime());
	}

}

class Profiler implements SQLLogger
{

	private $nbQueries = 0;
	private $totalTime = 0;	
	private $start;
	
	public function startQuery($sql, array $params = null, array $types = null)
	{
		$this->nbQueries++;
		$this->start = microtime(true);
	}

	public function stopQuery()
	{
		$this->totalTime += microtime(true) - $this->start;
	}
	
	public function getNbQueries()
	{
		return $this->nbQueries;
	}

	public function getTotalTime()
	{
		return $this->totalTime;
	}
}

?>