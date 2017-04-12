<?php
namespace Tecnotch;
class Router extends Request {

	public function __construct() {
		$this->parseUri();
	}
	
	public function getUri()
	{
		$uri = Request::info('uri'); 
		if (strpos($uri, BASE_NAME) === 0) {
			$uri = str_replace(BASE_NAME, "", $uri);
		}
		return $uri;
	}

	public function parseUri() {
		$uri = $this->getUri();
		if ($uri == "/") {
			$this->module = "default";
			$this->controller = "Index";
			$this->action = "index";
		} else {
			$parts = explode('/', ltrim($uri, "/"));
			$this->module = isset($parts[0]) ? $parts[0] : 'default';
			$this->controller = isset($parts[1]) ? ucfirst($parts[1]) : 'Index';
			$this->action = isset($parts[2]) ? $parts[2] : 'index';

			$i = 3;
			while ($i < count($parts)) {
				Request::addParam($parts[$i]);
				$i ++;
			}
		}
	}

	public function route($route = null) {
		if ($route) {
			header("Location: " . BASE_URL . $route);
			exit();
		}

		$className = ucfirst($this->module) . '_' . $this->controller . 'Controller';

		$controller = new $className();
		call_user_func(array($controller, $this->action));
		//echo $request;
	}

	public function shutdown($data = null) {
		//echo "shutdonw";	
		//print_r($data);
	}
}