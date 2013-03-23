<?php

class Secure extends AbstractPlugin
{

	public static function string($string, $secured = false)
	{
		$string = ($secured ? $string : htmlspecialchars($string, ENT_COMPAT, "UTF-8"));
		return $string;
	}

	public static function integer($string)
	{
		$integer = intval($string);
		return $integer;
	}

	public static function display($string)
	{
		return nl2br($string);
	}

}

?>