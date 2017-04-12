<?php
namespace Tecnotch;

class BaseController extends Layout {
	public $request;

	public function __construct() {
		$auth = Factory::Auth();

		if (!$auth->data() && !in_array(Factory::Router()->action, [
				'login', 'logout'
			])) {
			 
			Factory::Router()->route("/users/index/login");
		}
		parent::__construct();
	}

	
}