<?php

class Response
{

	public static $code = array(
		"404" => array(404, "404 Not Found")
	);
	private $_vars = array();
	private $_headers = array();
	private $_bodyParts = array();
	private $_returnCode = null;

	public function addVar($key, $value)
	{
		$this->_vars[$key] = $value;
	}

	public function getVar($key)
	{
		return $this->_vars[$key];
	}

	public function render($view)
	{
		$this->_bodyParts[] = $view;
	}

	function redirect($url, $permanent = false)
	{
		if ($permanent)
			$this->_headers['Status'] = '301 Moved Permanently';
		else
			$this->_headers['Status'] = '302 Found';

		$this->_headers['location'] = $url;

		$this->printOut();
	}

	function addHeader($key, $value)
	{
		$this->_headers[$key] = $value;
	}

	public function printOut()
	{
		if (!is_null($this->_returnCode))
		{
			header('Status' . ':' . $this->_returnCode[1], true, $this->_returnCode[0]);
		}

		foreach ($this->_headers as $key => $value)
		{
			header($key . ':' . $value);
		}

		extract($this->_vars);

		foreach ($this->_bodyParts as $view)
		{
			$file = CLASSES . DS . 'views' . DS . $view . '.php';

			if (file_exists($file))
			{
				include($file);
			}
			else
			{
				System::error("View " . $view . ".php does not exist");
				exit;
			}
		}		
	}

	function setReturnCode($code)
	{
		$this->_returnCode = $code;
	}

}

?>