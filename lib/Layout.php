<?php
namespace Tecnotch;

class Layout {

	public $view;
	protected $_layout;
	protected $_view;

	public function __construct() {
		$this->view = new View();
		$this->view->error = false;
		$this->view->msg = false;
	}

	public function setLayout() {

	}

	public function getLayout() {
		$config = Factory::config();

		return (THEME_DIR . '/' . $config['theme'] . '/' 
			. '/layouts/' . ($this->_layout ? $this->_layout : $config['layout']) . '.html');
	}

	public function setView() {

	}
	

	public function getView($viewFile = null) {
		$router = Factory::Router();
		$view = MODULE_DIR . '/' . $router->module . '/views/' .
		 	strtolower($router->controller) . '/' .
		 	($viewFile ? $viewFile : $router->action) . '.html';
		return $view;
		 
	}


	public function render($view = null) {
		$this->renderLayout($view);
	}

	public function renderLayout($view = null) {
		$contents = file_get_contents($this->getLayout());
		echo str_replace('{view}', $this->renderView($view), $contents);
	}

	public function renderView($viewFile = null) {
		$viewPath = $this->getView($viewFile);
		$view = $this->view;
		ob_start();
		include_once $viewPath;
		$view = ob_get_clean();
		return $view;
	}

}