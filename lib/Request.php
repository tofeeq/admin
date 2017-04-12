<?php
namespace Tecnotch;

class Request {

	protected static $_params;
	
	public static function info($option = null)
	{
		if (!$option)
			return $_SERVER;

		
		switch ($option) {
			case 'uri':
				return $_SERVER['REQUEST_URI'];
			break;
			
			default:
				if (isset($_SERVER[$option])) {
					return $_SERVER[$option];
				}
			break;
		}
	}

	public static function post($option = null)
	{
		if (!$option)
			return $_POST;

		if (isset($_POST[$option])) {
			return $_POST[$option];
		}
	}

	public static function get($option = null)
	{
		if (!$option)
			return $_GET;

		if (isset($_GET[$option])) {
			return $_GET[$option];
		}
	}

	public static function isPost() {
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			return true;
		}
		return false;
	}

	public static function addParam($param) {
		self::$_params[] = $param;
	}

	public static function param($opt) {
		if (is_numeric($opt)) {
			$opt = $opt - 1;
			if (isset(self::$_params[$opt])) {
				return self::$_params[$opt];
			}
		} else if (isset($_GET[$opt])) {
			return $_GET[$opt];
		}
	}

	public static function id() {
		return self::param(1);
	}
}