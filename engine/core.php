<?php

class Core
{

	private $_request;
	private $_response;
	private static $config = array();
	private static $_instance = null;

	private function __construct()
	{
		$this->loadCore();
		$this->init();
	}

	public function __clone()
	{
		System::error('Cloning the core is not permitted');
	}

	public static function singleton()
	{
		if (!isset(self::$_instance))
		{
			self::$_instance = new Core;
		}

		return self::$_instance;
	}

	private function loadCore()
	{
		include_once(ENGINE . DS . 'request.php');
		include_once(ENGINE . DS . 'response.php');

		include_once(ENGINE . DS . 'system.php');

		include_once(ENGINE . DS . 'abstractApplicationController.php');
		include_once(ENGINE . DS . 'abstractPlugin.php');

		$this->loadConfigs();
		$this->setInitValue();
		$this->loadPlugins();
	}

	private function loadConfigs()
	{
		$configs = glob(CONFIG . DS . '*.ini');

		foreach ($configs as $file)
		{
			$path = pathinfo($file);
			self::$config[$path['filename']] = parse_ini_file($file, true);
		}				

		if (isset($_SERVER['DEBUG']) && $_SERVER['DEBUG'] == 'true')
		{
			self::$config['core']['general']['path'] = self::$config['core']['path']['local'];
			self::$config['core']['general']['devel'] = true;
		}
		else
		{
			self::$config['core']['general']['path'] = self::$config['core']['path']['web'];
			self::$config['core']['general']['devel'] = false; 
		}
	}

	private function setInitValue()
	{
		if (isset(self::$config['core']['php_ini']))
		{
			foreach (self::$config['core']['php_ini'] as $key => $value)
				ini_set($key, $value);
		}
		else
		{
			System::debug('php_ini category in core.ini file is missing');
			return false;
		}
	}

	private function init()
	{
		$this->_request = new Request();
		$this->_response = new Response();
	}

	private function loadPlugins()
	{
		if (isset(self::$config['core']['plugins']))
		{
			foreach (self::$config['core']['plugins'] as $plugin => $value)
			{
				if ($value == true)
				{
					$file = PLUGINS . DS . $plugin . DS . $plugin . '.class.php';

					if (file_exists($file))
						include_once($file);
					else
						System::error('Plugin ' . $plugin . ' does not exist at ' . $file);
				}
			}
		}
		else
		{
			System::debug('plugins category in core.ini file is missing');
			return false;
		}
	}

	public function dispatch()
	{
		$result = $this->_request->route();

		if ($result !== false)
		{
			if (isset($_SESSION['current_route']))
				$_SESSION['last_route'] = $_SESSION['current_route'];

			$_SESSION['current_route'] = $result;

			$this->forward($result['module'], $result['action'], $result['params']);
		}
		else
		{
			$this->forward('main', '404', array());			
		}
		
		$this->_response->printOut();
	}

	public function forward($module, $action, $params = array())
	{
		$this->module = $module;
		$this->action = $action;
		$this->params = $params;

		$command = $this->getCommand($module, $action, $params);
		$command->beforeLaunch();
		$command->launch();
		$command->afterLaunch();
	}

	private function getCommand($module, $action, $params)
	{
		$file = CLASSES . DS . 'controllers' . DS . ucfirst($module) . 'Controller.php';

		if (file_exists($file))
		{
			include_once($file);
			$class = ucfirst($module) . 'Controller';
			return new $class($this, $this->_response, $action, $module, $params);
		}
		else
		{
			System::error('Controller file does not exist : ' . $file);
		}
	}

	public static function config($domain, $dim1 = null, $dim2 = null)
	{
		$return_false = false;

		isset(self::$config[$domain]) ? $return = self::$config[$domain] : $return_false = true;

		if ($dim1 != null)
		{
			isset($return[$dim1]) ? $return = $return[$dim1] : $return_false = true;

			if ($dim2 != null)
				isset($return[$dim2]) ? $return = $return[$dim2] : $return_false = true;
		}

		if ($return_false)
			$return = false;

		return $return;
	}

	public static function errorToException($code, $message, $file, $line)
	{
		if (0 == error_reporting())
		{
			return;
		}

		throw new ErrorException($message, 0, $code, $file, $line);
	}

	public static function handleException($e)
	{
		if (Core::config('core', 'general', 'devel'))
		{
			echo $e->getMessage();
			exit;
		}
		else
		{
			// go error page avec un forward de preference
		}
	}

}

?>
