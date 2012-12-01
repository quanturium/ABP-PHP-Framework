<?php

class Request
{

	private $_defaults = array('module' => 'home', 'action' => 'index', 'params' => array());

	public function route()
	{
		if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] != "")
			$route = $_SERVER['PATH_INFO'];
		else
		{
			$route = $this->_defaults['module'] . '/' . $this->_defaults['action'];
		}

		if ($route[0] == '/')
			$route = substr($route, 1);

		if ($route[strlen($route) - 1] == '/')
			$route = substr($route, 0, -1);

		$route_array = explode('/', $route);

		if (count($route_array) == 1) // Si on a qu'un parametre (le module) on ajoute l'action ""
			$route_array[] = "";

		if (count($route_array) >= 2)
		{
			// Module exists
			if (Core::config('core', 'request', $route_array[0]))
			{
				// Action exists for this module
				if (in_array($route_array[1], Core::config('core', 'request', $route_array[0])))
				{
					$this->_defaults['module'] = $route_array[0];
					$this->_defaults['action'] = $route_array[1];

					unset($route_array[0]);
					unset($route_array[1]);

					$this->_defaults['params'] = array_values($route_array);

					return $this->_defaults;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

}

?>