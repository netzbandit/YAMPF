<?php

/** 
 * Check if environment is development and display errors.
 */
function setReporting() {
	if (DEVELOPMENT_ENVIRONMENT == true) {
		error_reporting(E_ALL);
		ini_set('display_errors', 'On');
	} else {
		error_reporting(E_ALL);
		ini_set('display_errors', 'Off');
		ini_set('log_errors', 'On');
		ini_set('error_log', ROOT . '/tmp/logs/error.log');
	}
}

/**
 * Create a link to a controller and action.
 */
function _link($controller='start', $action='index', $parameters=array()) {
    $url = BASEURL . $controller . '/' . $action;
    if(is_array($parameters)) {
        foreach ($parameters as $value) {
            $url .= '/' . urlencode($value);
        }
    } else {
        $url .= '/' . $parameters;
    }
    
    return $url;   
}

/**
 * Create an url to a ressource (css, js, img, etc.).
 */
function _res($ressource) {
    return BASEURL . 'assets/' . $ressource;
}

/**
 * Array aware stripslashes.
 */
function stripSlashesDeep($value) {
	$value = is_array($value) ? array_map('stripSlashesDeep', $value) : stripslashes($value);
	return $value;
}

/** 
 * Check for Magic Quotes and remove them.
 */
function removeMagicQuotes() {
	if (get_magic_quotes_gpc()) {
		$_GET = stripSlashesDeep($_GET);
		$_POST = stripSlashesDeep($_POST);
		$_COOKIE = stripSlashesDeep($_COOKIE);
	}
}

/** 
 * Check register globals and remove them.
 */
function unregisterGlobals() {
	if (ini_get('register_globals')) {
		$array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
		foreach ($array as $value) {
			foreach ($GLOBALS[$value] as $key => $var) {
				if ($var === $GLOBALS[$key]) {
					unset($GLOBALS[$key]);
				}
			}
		}
	}
}

/** 
 * Main function 
 * Checks for controller and action in request and
 * runs them.
 */
function runApp() {
    global $url, $database;

    if(USE_DB) {
        // establish db connection
        $database = new Db();
    }

	$urlArray = array();
	if(strlen(trim($url)) > 0) {
		$urlArray = explode("/", $url);	
	}
	
	// default values for controller and action
	// ==> /start/index/
	$controllerName = "start";
	$actionName = "index";
	$queryString = array();

	if(count($urlArray) == 1) {
		// only controller given
		$controllerName = $urlArray[0];
	} elseif (count($urlArray) == 2) {
		// controller and action given
		$controllerName = $urlArray[0];
		array_shift($urlArray);
		$actionName = $urlArray[0];
	} elseif (count($urlArray) > 2) {
		// controller, action and parameters present
		$controllerName = $urlArray[0];
		array_shift($urlArray);
		$actionName = $urlArray[0];
		array_shift($urlArray);
		$queryString = $urlArray;
	}

	$templateVars = array();

	$controlVar = 0;

	// loop for action-call stacking
	// as long as another controller/action is set in the last controller
	// these are run.
	// to prevent infinite loops, only MAX_STACK_LOOP runs are permitted
	while (true) {
		if (++$controlVar > MAX_STACK_LOOP) {
			die('Infinite loop in controller stack!');
		}

		// start ==> StartController
		$controllerClassName = ucwords($controllerName);
		$controllerClassName .= 'Controller';

        /**
         * @var Controller 
         */
		$controller = new $controllerClassName($controllerName, $actionName);
		$controller->addTemplateVars($templateVars);

		if (true === method_exists($controllerClassName, $actionName)) {
            // call controller->action
			call_user_func_array(array($controller, $actionName), $queryString);
            
            // check if stack is set and run stacked controller
			if ($controller->hasStack()) {
				$controllerName = $controller->getNextController();
				$actionName = $controller->getNextAction();
                // pass variables to next controller call
				$templateVars = array_merge($templateVars, $controller->getTemplateVars());
                
            // otherwise render the view of last controller and leave loop
			} else {
				$controller->render();
				break;
			}
		} else {
			die('No such controller or action ('.$controllerClassName.'::'.$actionName.') ');
		}
	}
}


