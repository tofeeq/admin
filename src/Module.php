<?php
namespace Tecnotch;

class Module
{
	protected $_modules = [];

	function __construct($modules = null)
	{
		//parse db and get all tables from there
		if ($modules) {
			$this->setModules($modules);
		}

		$this->_init();

	}
	
	public function getSystemTables() {
		return ['persistences', 'reminders', 'activations', 'role_users'];
	}

	protected function _init() {
		$db = Factory::Db();
		$tables = $db->getTables();

		foreach ($tables as $row) {
			$table = current($row);
			if (!in_array($table, $this->getSystemTables())) {
				$this->addModule($table);
			}
		}

	}

	public function setModules(array $modules) {
		$this->_modules = $modules;
		return $this;
	}

	public function getModules() {
		return $this->_modules;
	}

	public function addModule($module) {
		$this->_modules[] = $module;
		return $this;
	}


	public function generate() {
		if (!is_dir(MODULE_DIR)) {
			mkdir(MODULE_DIR, 0755, true);	
		}

		foreach ($this->getModules() as $key => $module) {
			$modulePath = MODULE_DIR . "/{$module}";
			if (!is_dir($modulePath)) {
				mkdir($modulePath, 0755, true);
			}

			//gen controllers
			if (!is_dir($modulePath . '/controllers')) {
				mkdir($modulePath . '/controllers', 0755, true);
			}
			
			Factory::Controller($module)->generate();

			//gen models
			if (!is_dir($modulePath . '/models')) {
				mkdir($modulePath . '/models', 0755, true);
			}
			
			//Factory::Model($module)->generate();

			//gen views
			//if (!is_dir($modulePath . '/views')) {
			//	mkdir($modulePath . '/views', 0755, true);
			//}
			
			//Factory::View($module)->generate();
		}
	}
}