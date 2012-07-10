<?php
/**
 * Base class for all controllers 
 */
class Controller {

    /** controller/action stack */
    private $_nextController;
	private $_nextAction;
	
    /**
     * @var string
     */
	protected $_action;

    /**
     * @var Template
     */
	protected $_template;

    public function __construct($controller, $action) {
		$this->_action = $action;
		$this->_template = new Template($controller, $action);
	}

    public function set($name, $value) {
		$this->_template->set($name, $value);
	}

	public function get($name) {
		$this->_template->get($name);
	}

	public function setView($view) {
		$this->_template->setView($view);
	}
	
	public function setAjax($is_ajax = true) {
		$this->_template->setAjax($is_ajax);
	}
	
	public function render() {
		$this->_template->render();
	}
	
	protected function callStack($controller = "start", $action = "index") {
		$this->_nextController = $controller;
		$this->_nextAction = $action;
	}
	
	public function hasStack() {
		return ($this->_nextController !== null && $this->_action !== null);
	}
	
	public function getNextController() {
		return $this->_nextController;
	}
	
	public function getNextAction() {
		return $this->_nextAction;
	}

	public function getTemplateVars() {
		return $this->_template->getVariables();
	}
	
	public function addTemplateVars($vars) {
		$this->_template->addVariables($vars);
	}
	
}
