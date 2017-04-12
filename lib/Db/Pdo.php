<?php
namespace Tecnotch\Db;
class Pdo extends \PDO {

	protected $_table;
	protected $_where = [];
	protected $_orwhere = [];
	protected $_order = [];
	protected $_limit = 10;
	protected $_cols = [];
	protected $_debug = 0;

	public function __construct() {

		$config = \Tecnotch\Factory::config();
		$constring = "mysql:host={$config['host']};dbname={$config['db']}";
		parent::__construct($constring, $config['uname'], $config['pass']);	
	}

	public function cols($key) {
		
		if (is_array($key)) {
			$this->_cols = $key;
		} else {
			$this->_cols[] = $key;
		}
		return $this;
	}

	public function where($key, $val, $operator = '=') {
		$this->_where[] =  [$key, $operator, $val]; 
		return $this;
	}

	public function order($key, $val) {
		$this->_order[$key] = $val; 
		return $this;
	}
	
	public function limit($start= null, $end = null) {
		$this->_limit =  [$start, $end]; 
		return $this;
	}

	public function orWhere($key, $val, $operator = '=') {
		$this->_orwhere[] = [$key, $operator, $val]; 
		return $this;
	}

	public function addSelect() {
		$query = "SELECT ";
		if (empty($this->_cols) or (current($this->_cols) == '*')) {
			$query .= "*";
		} else {
			$query .= "`" . implode("`,`", $this->_cols) . "`";
		}
		return $query . " FROM `" . $this->_table . "` ";
	}

	public function addOrder(&$query) {
		if (empty($this->_order))
			return ;

		$query .= "order by ";
		$q = [];
		foreach ($this->_order as $key => $val) {
			$q[] = "`$key` $val";
		}
		$query .= implode(",", $q);
	}

	public function addLimit(&$query) {
		if (empty($this->_limit[0])) {
			return ;
		}
		$query .= " LIMIT " . $this->_limit[0];
		if (isset($this->_limit[1])) {
			$query .= ", " . $this->_limit[1];
		}
	}
	
	public function addCols() {
		if (empty($this->_cols)) {
			return;
		}

		$q = [];
		foreach ($this->_cols as $key => $val) {
			$q[] = "`$key` = :$key";
		}

		return implode(",", $q);
	}

	public function addColKeys() {
		if (empty($this->_cols)) {
			return;
		}

		$q = [];
		foreach ($this->_cols as $key => $val) {
			$q[] = ":$key";
		}

		return implode(",", $q);
	}

	public function addWhere(&$query) {
		if (empty($this->_where)) {
			return ;
		}

		$q = [];
		$i = 0;
		foreach ($this->_where as $col => $data) {
			$q[] = "`{$data[0]}` " . $data[1] . " :{$data[0]}" . $i++;
		}

		$query .= '	WHERE ' . implode(" AND ", $q);

		if (empty($this->_orwhere)) {
			return ;
		}

		$q = [];
		 
		foreach ($this->_orwhere as $col => $data) {
			$q[] = "`{$data[0]}` " . $data[1] . " :{$data[0]}" . $i++;
		}

		$query .= '	OR ' . implode(" OR ", $q);
	}
	
	 

	public function getPlacehoders() {
		//array(':calories' => 150, ':colour' => 'red')
		$q = [];
		$i = 0;

		foreach ($this->_where as $key => $data) {
			$q[":{$data[0]}" . $i ++] = $data[2]; 
		}

		 
		foreach ($this->_orwhere as $key => $data) {
			$q[":{$data[0]}" . $i ++] = $data[2]; 
		}
		
		
		return $q;
	}

	public function getColsPlacehoders() {
		$q = [];
		foreach ($this->_cols as $key => $data) {
			$q[":$key"] = $data; 
		}

		return $q;
	}

	public function prepareInsertQuery($option = "select") {
		$query = "INSERT INTO `" . $this->_table . "` (`" . 
			implode("`,`", array_keys($this->_cols)) . "`) VALUES (" . 
				$this->addColKeys() .
			")"; 
		
		return parent::prepare($query);

	}

	public function prepareUpdateQuery() {
		$query = "UPDATE `" . $this->_table . "` SET " 
			. $this->addCols();
		$this->addWhere($query);
	
		return parent::prepare($query);
	}

	public function prepareDeleteQuery() {
		$query = "DELETE FROM `" . $this->_table . "`";
		$this->addWhere($query);
		
		return parent::prepare($query);
	}

	public function prepareFetchQuery() {
		$query = $this->addSelect();
		$this->addWhere($query);
		$this->addOrder($query);
		$this->addLimit($query);
		return parent::prepare($query);
	}

	public function fetch($id = null) {
		if ($id) {
			$this->where("id", $id);
		}
		$stat = $this->prepareFetchQuery();
		$stat->execute($this->getPlacehoders());

		if ($this->_debug) {
			echo '<pre>'; echo $stat->queryString . "\n"; 
			print_r($this->getPlacehoders());
			echo '</pre>';
		}

		$this->_reset();

		return $stat->fetch(\PDO::FETCH_ASSOC);
	}

	public function fetchAll() {
		$stat = $this->prepareFetchQuery();
		$stat->execute($this->getPlacehoders());

		if ($this->_debug) {
			echo '<pre>'; echo $stat->queryString . "\n"; 
			print_r($this->getPlacehoders());
			echo '</pre>';
		}

		$this->_reset();

		return $stat->fetchAll(\PDO::FETCH_ASSOC);
	}


	public function update() {
		$stat = $this->prepareUpdateQuery();
		$placeholders = array_merge($this->getPlacehoders(), $this->getColsPlacehoders());
		$stat->execute($placeholders);

		if ($this->_debug) {
			echo '<pre>'; echo $stat->queryString . "\n"; 
			print_r($placeholders);
			echo '</pre>';
		}

		$this->_reset();

		return $stat->rowCount();
	}

	public function insert() {
		$stat = $this->prepareInsertQuery();
		$placeholders = $this->getColsPlacehoders();
		$stat->execute($placeholders);
		
		if ($this->_debug) {
			echo '<pre>'; echo $stat->queryString . "\n"; 
			print_r($placeholders);
			echo '</pre>';
		}

		$this->_reset();
		return $this->lastInsertId();
	}

	public function delete() {
		$stat = $this->prepareDeleteQuery();
		$placeholders = $this->getPlacehoders();
		$stat->execute($placeholders);

		if ($this->_debug) {
			echo '<pre>'; echo $stat->queryString . "\n"; 
			print_r($placeholders);
			echo '</pre>';
		}

		$this->_reset();
		return $stat->rowCount();
	}

	public function query($query) {
		$stat = $this->prepare($query);
		$stat->execute();
		if ($this->_debug) {
			echo '<pre>'; echo $stat->queryString . "\n"; 
			echo '</pre>';
		}

		$this->_reset();
		if ($stat->rowCount())
			return $stat->fetchAll(\PDO::FETCH_ASSOC);
	}

	private function _reset() {
		$this->_cols = [];
		$this->_where = [];
		$this->_orwhere = [];
	}
}