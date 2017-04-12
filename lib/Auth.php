<?php
namespace Tecnotch;
class Auth extends Session {
	public function data() {
		if (empty($this->getStorage())) {
			return false;
		}
		return $this->getStorage();
	}

	
}