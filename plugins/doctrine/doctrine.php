<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Symfony\Component\Console\Helper\HelperSet;

include ('../../const.php');

require 'Doctrine/ORM/Tools/Setup.php';

$db_config = parse_ini_file(CONFIG . DS . 'doctrine.ini', true);

Setup::registerAutoloadDirectory(__DIR__);

$paths = array(CLASSES . DS . "entities");
$isDevMode = true;

$dbParams = array(
	'driver' => 'pdo_mysql',
	'hostname' => $db_config['connexion']['hostname'],
	'user' => $db_config['connexion']['username'],
	'password' => $db_config['connexion']['password'],
	'dbname' => $db_config['connexion']['dbname']
);

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);


$em = EntityManager::create($dbParams, $config);

$helperSet = new HelperSet(array(
			'db' => new ConnectionHelper($em->getConnection()),
			'em' => new EntityManagerHelper($em)
		));

ConsoleRunner::run($helperSet);
?>