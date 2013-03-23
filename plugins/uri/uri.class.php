<?php

class URI extends AbstractPlugin
{

	static public function createLink($link, $type = "")
	{
		if ($type == "")
		{
			return Core::config('core', 'general', 'path') . '/' . $link;
		}
		elseif (Core::config('uri', $type))
		{
			// If it is an absolute link, we don't concatenate the main address
			if (strtolower(substr($type, 0, 4)) == 'http')
			{
				return Core::config('uri', $type) . $link;
			}
			else
			{
				return Core::config('core', 'general', 'path') . '/' . Core::config('uri', $type) . $link;
			}
		}
		else
		{
			System::error('Context $type of url does not match anything. Please correct it, or add it in url.ini ');
			return -1;
		}
	}

	static public function createLink2($module, $action, $params = array())
	{
		if (count($params) > 0)
			$params = '/' . implode('/', $params);
		else
			$params = '';

		return Core::config('core', 'general', 'path') . '/' . $module . '/' . $action . $params;
	}

}

?>