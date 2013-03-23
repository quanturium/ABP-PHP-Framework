<?php

class System
{

	static public $activated = true;
	static private $logsFile = 'logs.txt';
	static private $_createLogsFile = false;

	static function debug($msg)
	{
		if (self::$activated && Core::config('core', 'general', 'devel'))
		{
			$msg .= PHP_EOL;
			
			if (self::$_createLogsFile)
			{
				$file = TMP . DS . 'logs' . DS . self::$logsFile;

				if (file_exists($file))
					unlink($file);

				file_put_contents($file, $msg, FILE_APPEND);

				self::$_createLogsFile = false;
			}
			else
			{
				$file = TMP . DS . 'logs' . DS . self::$logsFile;

				file_put_contents($file, $msg, FILE_APPEND);
			}
		}
	}

	static function error($msg)
	{
		throw new Exception("/!\ ERROR: \t" . $msg);
	}
}

?>