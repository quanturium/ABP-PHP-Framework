<?php

class MainController extends AbstractApplicationController
{
	protected $_classes = array(); // classes to load

	public function launch()
	{
		switch($this->_action)
		{
			case '404' : 
				
				$this->response()->addVar('referer', $this->_params);
				
				$this->response()->render('page_main_404');
				
				break;
		}
	}

}

?>
