<?php

class Template {

	private $variables = array();
	private $controller;
	private $action;
	private $view;
	private $is_ajax;

	function __construct($controller, $action) {
		$this->controller = $controller;
		$this->action = $action;
		$this->view = $action;
		$this->is_ajax = false;
	}

	function setView($view) {
		$this->view = $view;
	}

	function setAjax($is_ajax = true) {
		$this->is_ajax = $is_ajax;
	}
	
	/** Set Variables * */
	function set($name, $value) {
		$this->variables[$name] = $value;
	}

	function getVariables() {
		return $this->variables;
	}

	function addVariables($vars) {
		$this->variables = array_merge($this->variables, $vars);
	}

	/** Display Template * */
	function render() {
		global $DEBUGMESSAGES, $the_user;

		extract($this->variables);

		if (false === $this->is_ajax) {
			if (file_exists(ROOT . '/application/views/' . $this->controller . '/header.php')) {
				include (ROOT . '/application/views/' . $this->controller . '/header.php');
			} else {
				include (ROOT . '/application/views/_general/header.php');
			}
		}

		include (ROOT . '/application/views/' . $this->controller . '/' . $this->view . '.php');

		if (false === $this->is_ajax) {
			if (file_exists(ROOT . '/application/views/' . $this->controller . '/footer.php')) {
				include (ROOT  . '/application/views/' . $this->controller . '/footer.php');
			} else {
				include (ROOT . '/application/views/_general/footer.php');
			}
		}
	}
}
