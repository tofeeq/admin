<?php
use Tecnotch\BaseController as Base;
class Default_IndexController extends Base {
	
	protected $_layout = 'default';

	public function index() {
		$this->view->variable = "hello234";
		$this->render();
	}
}