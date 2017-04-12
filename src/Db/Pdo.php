<?php
namespace Tecnotch\Db;
class Pdo extends \PDO {

	public static $db;
	
	public function __construct() {

		$config = \Tecnotch\Factory::config();
		self::$db = $config['db'];

		$constring = "mysql:host={$config['host']};dbname={$config['db']}";
		parent::__construct($constring, $config['uname'], $config['pass']);	
	    // set the PDO error mode to exception
	}


	public function getTables() {
		$statement = $this->prepare("show tables ");
		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC); 
	    return $statement->fetchAll();
	}

	public function importSql($module, $path) {
		if ($path) {
			if (is_dir($path . '/sql')) {
				foreach (glob($path . '/sql/*.sql') as $filename) {
					$this->importFile($filename);	    
				}
			}
		}
	}

	public function importFile($file) {
		$handle = fopen($file, 'r');
		while (!feof($handle)) {
			$sql = stream_get_line($handle, null, ";");
			try {
				$statement = $this->prepare($sql);
				$statement->execute();
			} catch (exception $e) {
				echo $e->getMessage();
			}
		}
		fclose($handle);
	}
}