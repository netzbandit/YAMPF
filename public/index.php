<?php
// the root directory (server file system)
define('ROOT', dirname(dirname(__FILE__)));
// the base url
define('BASEURL', str_replace('public/', '', str_replace("index.php", "", $_SERVER['PHP_SELF'])));

if(isset($_GET['url'])) {
	$url = $_GET['url'];
} else {
	$url = '';
}

require_once (ROOT . '/library/bootstrap.php');
