<?php
use Tecnotch\BaseController as Base;
use Tecnotch\Request as Request;
use Tecnotch\Factory as Factory;

class Menu_IndexController extends Base {

	public function index() {
		$model = Factory::Model("menu_menu");
		$rows = $model
			->order("id", "asc")
			->fetchAll();

		$this->view->data = $rows;
		$this->render();
	}

	public function create() {
		if (Request::isPost()) {
			$post = Request::post();
			$model = Factory::Model("menu_menu");
			$model
				->cols(["title" => $post['name'], "parent_id" => $post['parent_id']])
				->insert();

			$this->view->title = $post['name'];
			$this->view->parent_id = $post['parent_id'];
	
			$this->view->msg = "Successfully added.";
		}

		$this->render('form');
	}

	public function edit() {

		$model = Factory::Model("menu_menu");
		
		if (Request::isPost()) {
			$post = Request::post();
			$model
				->cols(["title" => $post['name'], "parent_id" => $post['parent_id']])
				->where("id", Request::id())
				->update();

			$this->view->msg = "Successfully updated.";
		}

		$data = $model->fetch(Request::id());
		$this->view->title = $data['title'];
		$this->view->parent_id = $data['parent_id'];
		$this->render('form');
	}

	public function delete() {
		$model = Factory::Model("menu_menu");
		$model	->where("id", Request::id())
				->delete();
		$this->view->msg = "Successfully deleted.";	
		$this->render("index");
	}
}