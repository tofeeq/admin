<?php
namespace Tecnotch;

class Controller {

	protected $_module;
	protected $_modulePath = null;

	public function __construct($module, $modulePath = null) {
		$this->_module = $module;

		if ($modulePath) {
			$this->_modulePath = $modulePath;
		}
	}

	public function getModule() {
		return $this->_module;
	}

	public function getModulePath() {
		return $this->_modulePath;
	}

	public function generate() 
	{
		//get data from db table and generate controller, model, view
		$controllerTpl = file_get_contents(BASE_DIR . '/src/tpl/controller.php');
		file_put_contents(MODULE_DIR . '/' . $this->getModule() . '/controllers/index.php', str_replace(array('{$module}'), array(ucfirst($this->getModule())), $controllerTpl));
	}
}