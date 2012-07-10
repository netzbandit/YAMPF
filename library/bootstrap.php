<?php

session_start();

/**
 * @var Db 
 */
$database = null;

require_once (ROOT . '/config/config.php');
require_once (ROOT . '/config/constants.php');
require_once (ROOT . '/library/common.functions.php');
require_once (ROOT . '/library/bootstrap.functions.php');

setReporting();
removeMagicQuotes();
unregisterGlobals();
runApp();
