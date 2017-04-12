<?php
namespace Tecnotch;

class Factory {

	protected static $_db;
	protected static $_auth;
	protected static $_router;
	protected static $_config;
	

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

	public static function Router() {
		if (!self::$_router) {
			self::$_router = new Router();
		}
		return self::$_router;
	}

	public static function Auth() {
		if (!self::$_auth) {
			self::$_auth = new Auth();
		}
		return self::$_auth;
	}

	public static function Model($model) {
		list($module, $model) = explode("_", $model);
		$modelPath = MODULE_DIR . "/$module/models/" . ucfirst($model) . '.php';

		require_once($modelPath);
		$className = ucfirst($module) . "_" . ucfirst($model) . 'Model'; 
		return new $className();
	}
}