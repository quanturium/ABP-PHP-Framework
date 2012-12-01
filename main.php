<?php

//$start = microtime(true);

session_start();

include_once('const.php');
include_once(ENGINE . DS . 'core.php');

set_exception_handler(array('Core', 'handleException'));

Core::singleton()->dispatch();

//echo "Generation de la page : " . (microtime(true) - $start);
?>