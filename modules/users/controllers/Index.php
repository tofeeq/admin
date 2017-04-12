<?php
use Tecnotch\BaseController as Base;
use Tecnotch\Request as Request;
use Tecnotch\Factory as Factory;

class Users_IndexController extends Base {

	public function login() {
		if (Factory::Auth()->data()) {
			if (Factory::Auth()->get('user')) {
				Factory::Router()->route('/default');
			}
		} else {
			if (Request::isPost()) {
				//check login
				$post = Request::post();
				if (!empty($post['username']) && !empty($post['password'])) {
					$model = Factory::Model("users_user");
					$user = $model
						->cols("*")
						->where("email", $post['username'])
						->where("password", md5($post['password']))
						->fetch();

					if ($user) {
						$modelActivation = Factory::Model("users_activation");
						$activation = $modelActivation->cols("completed")
							->where("user_id", $user['id'])
							->fetch();
						if ($activation['completed']) {
							$res = $model
								->cols(["last_login" => date("Y-m-d H:i:s")])
								->where("id", $user['id'])
								->update();

							
							Factory::Auth()->store('user', $user);
							Factory::Router()->route('/default');
						} else {
							$this->view->error = "Your account not activated yet";
						}
					} else {
						$this->view->error = "No data found";
					}
				}
			}
		}

		$this->render();
		
	}

	public function logout() {
		Factory::Auth()->clear();
		Factory::Router()->route('/users/index/login');
	}

}