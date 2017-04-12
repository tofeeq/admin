<?php
namespace Tecnotch;

class Factory {

	protected static $_db;
	protected static $_config;
	
	public static function module() {
		return new Module();
	}

	public static function db() {
		if (!self::$_db) {
			self::$_db = new Db\Pdo();
		}
		return self::$_db;
	}

	public static function config() {
		if (!self::$_config) {
			self::$_config = json_decode(file_get_contents(BASE_DIR . '/config.json'), 1);
		}
		return self::$_config;
	}

	public static function Controller($module) {
		if (file_exists(BASE_DIR . '/src/modules/' . $module . '/Controller.php')) {
			$class = "Tecnotch\modules\\" . $module . '\\Controller';
			return new $class($module, BASE_DIR . '/src/modules/' . $module);
		} else {
			return new Controller($module);
		}
	}

	public static function Model() {
		
	}

	public static function View() {
		
	}
}