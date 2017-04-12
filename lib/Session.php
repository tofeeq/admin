<?php
namespace Tecnotch;

class Session {

	protected $_data;
	
	public function getStorage() {
		if (!isset($_SESSION['app_data'])) {
			$_SESSION['app_data'] = $this->_data;
		}

		$this->_data = $_SESSION['app_data'];
		return $this->_data; 		 
	}

	public function get($key) {
		$data = $this->getStorage();
		if (isset($data[$key])) {
			return $data[$key];
		}
	}

	public function store($key, $val) {
		$this->_data[$key] = $val;
		$_SESSION['app_data'] = $this->_data;
	}

	public function clear() {
		unset($_SESSION['app_data']);
		session_destroy();
	}
}