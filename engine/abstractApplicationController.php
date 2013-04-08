<?php
/**
 * @property Response $_response 
 */
abstract class AbstractApplicationController
{

	protected $_core = null;
	protected $_response = null;
	protected $_module = null;
	protected $_action = null;
	protected $_params = null;
	protected $_mainController = null;

	function __construct($core, $response, $action = null, $module = null, $params = null)
	{
		$this->_core = $core;
		$this->_response = $response;

		$this->_module = $module;
		$this->_action = $action;
		$this->_params = $params;

		$this->loadClasses();
	}
	
	abstract function launch();

	public function beforeLaunch()
	{
		$this->response()->addVar('module', $this->_module);
		$this->response()->addVar('action', $this->_action);
		$this->response()->addVar('params', $this->_params);
	}	

	public function afterLaunch()
	{
		
	}

	protected function forward($module, $action, $params = array(), $useHttpRedirection = false)
	{
		if ($useHttpRedirection)
		{
			if (count($params) > 0)
				$params = '/' . implode('/', $params);
			else
				$params = '';

			$this->_response->redirect(Core::config('core', 'general', 'path') . '/' . $module . '/' . $action . $params);
		}
		else
		{
			$this->_core->forward($module, $action, $params);
		}
	}

	protected function redirect($url, $permanent = false)
	{
		$this->_response->redirect($url, $permanent);
	}

	protected function loadModel($model)
	{
		$file = CLASSES . DS . 'models' . DS . ucfirst($model) . 'Model.php';

		if (file_exists($file))
		{
			include_once($file);
			$class = ucfirst($model . 'Model');
			return new $class();
		}
		else
		{
			System::error('Model does not exist : ' . $file);
		}
	}

	protected function response()
	{
		return $this->_response;
	}

	private function loadClasses()
	{
		if (isset($this->_classes))
		{
			foreach ($this->_classes as $class)
			{
				$file = LIBRARY . DS . $class . '.class.php';

				if (file_exists($file))
					include_once($file);
				else
					System::error('Can not load class ' . $class . ' at ' . $file);
			}
		}
		else
		{
			System::error('Classes to load were not defined in controller file. Take a look at sampleController if you dont know how to define these');
		}
	}

}

?>