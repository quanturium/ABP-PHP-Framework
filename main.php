<?php

session_start();

include_once(ENGINE . DS . 'core.php');

set_exception_handler(array('Core', 'handleException'));

Core::singleton()->dispatch();

?>