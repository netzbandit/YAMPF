<?php

$DEBUGMESSAGES = array();

/**
 * add a line to debug messages 
 */
function addDebug($debugline) {
    global $DEBUGMESSAGES;

    if (DEVELOPMENT_ENVIRONMENT === true) {
        $DEBUGMESSAGES[] = $debugline;
    }
}

/** 
 * Autoload any classes that are required
 */
function __autoload($className) {
    if (file_exists(ROOT . '/library/' . strtolower($className) . '.class.php')) {
        require_once(ROOT . '/library/' . strtolower($className) . '.class.php');
    } else if (file_exists(ROOT . '/application/controllers/' . strtolower($className) . '.php')) {
        require_once(ROOT . '/application/controllers/' . strtolower($className) . '.php');
    } else if (file_exists(ROOT . '/application/models/' . strtolower($className) . '.php')) {
        require_once(ROOT . '/application/models/' . strtolower($className) . '.php');
    } else {
        /* Error Generation Code Here */
    }
}