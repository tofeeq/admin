<?php
/*******************************************************************************
* Tecnotch Libraries                                                 		   *
*                                                                              *
* Version: 1.0                                                                 *
* Date:    2016-03-15                                                          *
* Author:  Tofeeq ur Rehman - Tecnotch Ltd.                                    *
*******************************************************************************/

namespace Tecnotch;

error_reporting(-1);
ini_set('display_errors', 'On');
ini_set('memory_limit', '256M');
//set_error_handler("var_dump");

//ini_set("mail.log", __DIR__ . "/logs/php_mail.log");
//ini_set("mail.add_x_header", TRUE);


define('ENV', 'dev');


spl_autoload_register(function ($className) {
	//convert \ to /
	$className = str_replace(
		array('Tecnotch', "\\", "_"), 
		array('src', DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR)
		, $className
	);

	$classPath = BASE_DIR  . '/'. $className . '.php';

	
	if (file_exists($classPath)) {
		require_once($classPath);
	} else {
		echo "inclusion failed for class: $classPath";
	}
});
