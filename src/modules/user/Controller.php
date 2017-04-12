<?php
namespace Tecnotch\modules\user;
class Controller extends \Tecnotch\Controller {

	public function generate() {
		\Tecnotch\Factory::Db()->importSql($this->getModule(), $this->getModulePath());

		parent::generate();
	}
}