<?php
/*******************************************************************************
* Tecnotch Libraries                                                 		   *
*                                                                              *
* Version: 1.0                                                                 *
* Date:    2016-03-15                                                          *
* Author:  Tofeeq ur Rehman - Tecnotch Ltd.                                    *
*******************************************************************************/

use Tecnotch as Tec;

error_reporting(-1);
session_start();

ini_set('display_errors', 'On');
ini_set('memory_limit', '256M');

define('BASE_DIR', __DIR__);
define('THEME_DIR', __DIR__ . '/themes');
define('MODULE_DIR', BASE_DIR . '/modules');



//set_error_handler("var_dump");

//ini_set("mail.log", __DIR__ . "/logs/php_mail.log");
//ini_set("mail.add_x_header", TRUE);
define('BASE_NAME',  "/" . basename(__DIR__));
//define('BASE_NAME',  "/");
define('BASE_URL', "http://" . $_SERVER['SERVER_NAME'] . BASE_NAME);
define('ENV', 'dev');

spl_autoload_register(function ($className) {
	//convert \ to /

	if (preg_match('#Tecnotch\\\#', $className)) {
		//LOAD CORE LIBRARIES
		$className = str_replace(
			array('Tecnotch', "\\"), 
			array('lib', DIRECTORY_SEPARATOR)
			, $className
		);

		$classPath = BASE_DIR  . '/'. $className . '.php';
	} else if (strpos($className, "_") !== false) {
		//module related libraries
		list($module, $class) = explode("_", $className); 
		//list($module, $class) = ;
		$classPath = MODULE_DIR . "/" . strtolower($module);

		if (preg_match('/Controller$/', $className)) {
			$classPath .= "/controllers/" 
				. preg_replace("/Controller$/", "", $class) 
				. '.php';
		} else if (preg_match('/Model$/', $className)) {
			$classPath .= "/models/" 
				. preg_replace("/Model$/", "", $class) 
				. '.php';
		} else if (preg_match('/Helper$/', $className)) {
			$classPath .= "/helpers/" 
				. preg_replace("/Helper$/", "", $class) 
				. '.php';
		}

	} else {
		echo "unkown $className"; 
	}

	
	if (file_exists($classPath)) {
		require_once($classPath);
	} else {
		echo "inclusion failed for class: $classPath";
	}
});

register_shutdown_function(array(Tec\Factory::Router(), 'shutdown'));
/////////////////////////////
//Routing
Tec\Factory::Router()->route();