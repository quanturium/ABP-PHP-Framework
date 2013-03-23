<?php

class Request
{

	private $_defaults = array('module' => 'home', 'action' => 'index', 'params' => array());

	public function route()
	{
		if (isset($_GET['url']))
			$url = $_GET['url'];
		else
		{
			$url = $this->_defaults['module'] . '/' . $this->_defaults['action'];
		}

		if ($url[strlen($url) - 1] == '/')
			$url = substr($url, 0, -1);

		$url_params = explode('/', $url);

		if (count($url_params) >= 2)
		{
			// Module exists
			if (Core::config('core', 'request', $url_params[0]))
			{
				// Action exists for this module
				if (in_array($url_params[1], Core::config('core', 'request', $url_params[0])))
				{
					$this->_defaults['module'] = $url_params[0];
					$this->_defaults['action'] = $url_params[1];

					unset($url_params[0]);
					unset($url_params[1]);

					$this->_defaults['params'] = array_values($url_params);

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