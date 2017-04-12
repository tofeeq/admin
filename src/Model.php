<?php
namespace Tecnotch;
class Model
{
	public $fields = array();
	
	
	function select(array $cols, $where=1)
	{
		$res = $this->query("SELECT $cols FROM " . TABLE . " WHERE $where ");
		if ($res && mysql_num_rows($res))
			return mysql_fetch_assoc($res);
		return false;
	}
	
	function getCols()
	{

		$res = $this->query("SHOW COLUMNS FROM " . TABLE);
		if ($res && mysql_num_rows($res)) return $res;
		return false;
	}
	
	function formatName($field)
	{
		while (($pos = strpos($field, "_")) !== false)
		{
			$field = str_replace(("_" . $field{$pos+1}), strtoupper($field{$pos+1}), $field);
		}
		return $field; 
	}
	
	function createModel()
	{
		$data = $this->getCols();
		$str = ('<?php') . " \nclass ". MODULE . "_Model_" . MODEL;
		$str .= "\n{\n";
		
		$res =$this->getCols();
		
		$fields = array();
		
		while ($data =  mysql_fetch_assoc($res))
		{
			$fld['realname'] = $data['Field'];
			$fld['Type'] = $data['Type'];
			$fld['Null'] = $data['Null'];
			$fld['Key'] = $data['Key'];
			$fld['Default'] = $data['Default'];
			$fld['Extra'] = $data['Extra'];
			$fld['Field'] = $field = $this->formatName($data['Field']);
			$fields[] = $fld;
			$str .= "\tprotected " . '$_' . $field . "; \n";
		}
		
		$str .= $this->getBasicFunctions();
		
		$this->fields = $fields;
		
		foreach ($fields as $field)
		{
			$str .= $this->createFunction($field);
		}

		$str .= $this->getArray();
		
		$str .= '}';
		
		
		$file = fopen("models/" . MODEL . ".php", 'w');
		fwrite($file, $str);
		fclose($file);
		echo "Model created as models/" . MODEL . ".php";
	}
	
	
	
	function getBasicFunctions()
	{
		$str = "\n\t" . 'public function __construct(array $options = null)' . "\n" ;
		$str .=  "\t" . '{' . "\n" ;
		$str .= "\t\t" . 'if (is_array($options))' . "\n";
		$str .= "\t\t" . '{' . "\n" ;
		$str .= "\t\t\t" . '$this->setOptions($options);' . "\n" ;
		$str .= "\t\t" . '}' . "\n";
		$str .= "\t" . '}' . "\n " ;
		 	
		$str .= "\n\t" . 'public function __set($name, $value)' . "\n" ;
		$str .= "\t" . '{' . "\n" ;
		$str .= "\t\t" . '$method = \'set\' . $name;' . "\n" ;
		$str .= "\t\t" . 'if ((\'mapper\' == $name) || !method_exists($this, $method))' . "\n";
		$str .= "\t\t" . '{' . "\n" ;
		$str .= "\t\t\t" . 'throw new Exception(\'Invalid ' . MODEL . ' property\');' . "\n" ;
		$str .= "\t\t" . '}' . "\n" ;
		$str .= "\t\t" . '$this->$method($value);' . "\n" ;
		$str .= "\t" . '}' . "\n" ;
		 
		$str .= "\n\t" . 'public function __get($name)' . "\n" ;
		$str .= "\t" . '{' . "\n" ;
		$str .= "\t\t" . '$method = \'get\' . $name;' . "\n" ;
		$str .= "\t\t" . 'if ((\'mapper\' == $name) || !method_exists($this, $method))' . "\n";
		$str .= "\t\t" . '{' . "\n" ;
		$str .= "\t\t\t" . 'throw new Exception(\'Invalid ' . MODEL . ' property\');' . "\n" ;
		$str .= "\t\t" . '}' . "\n" ;
		$str .= "\t\t" . 'return $this->$method();' . "\n" ;
		$str .= "\t" . '}' . "\n" ;
		 
		$str .= "\n\t" . 'public function setOptions(array $options)' . "\n" ;
		$str .= "\t" . '{' . "\n" ;
		$str .= "\t\t" . '$methods = get_class_methods($this);' . "\n" ;
		$str .= "\t\t" . 'foreach ($options as $key => $value) {' . "\n" ;
		$str .= "\t\t\t" .'$method = \'set\' . ucfirst($key);' . "\n" ;
		$str .= "\t\t\t" . 'if (in_array($method, $methods)) {' . "\n" ;
		$str .= "\t\t\t\t" . '$this->$method($value);' . "\n" ;
		$str .= "\t\t\t" . '}' . "\n" ;
		$str .= "\t\t" . '}' . "\n" ;
		$str .= "\t\t" . 'return $this;' . "\n" ;
		$str .= "\t" . '}' . "\n" ;
		
		
		$str .= "\n\t" . 'public function getOptions($camelCase = false)' . "\n";
		$str .= "\t" . '{' . "\n";
		$str .= "\t" . '	$methods = get_class_methods($this);' . "\n";
		$str .= "\t" . '	$array = array();' . "\n";
		$str .= "\t" . '	foreach ($methods as $method) ' . "\n";
		$str .= "\t" . '	{' . "\n";
		$str .= "\t" . '		if ($method != "getOptions" and substr($method, 0, 3) == "get")' . "\n";
		$str .= "\t" . '		{' . "\n";
		$str .= "\t" . '			if ($this->$method() or $this->$method() === \'0\')' . "\n";
		$str .= "\t" . '			{' . "\n";
		$str .= "\t" . '				$variable = str_replace("get", "", $method);' . "\n";
		$str .= "\t" . '				if ($camelCase) {' . "\n";
		$str .= "\t" . '					$variable{0} = strtolower($variable{0});' . "\n";
		$str .= "\t" . '					$str = $variable;' . "\n";
		$str .= "\t" . '				} else {' . "\n";
		$str .= "\t" . '					$str = strtolower($variable{0});' . "\n";
		$str .= "\t" . '					for ($i = 1; $i < strlen($variable); $i++)' . "\n";
		$str .= "\t" . '					{' . "\n";
		$str .= "\t" . '						if ($variable[$i] === strtoupper($variable[$i]))' . "\n";
		$str .= "\t" . '						{' . "\n";
		$str .= "\t" . '							$str .= "_" . strtolower($variable[$i]);' . "\n";
		$str .= "\t" . '						}' . "\n";
		$str .= "\t" . '						else ' . "\n";
		$str .= "\t" . '						{' . "\n";
		$str .= "\t" . '							$str .= $variable[$i];' . "\n";
		$str .= "\t" . '						}' . "\n";
		$str .= "\t" . '					}' . "\n";
		$str .= "\t" . '				}' . "\n";
		$str .= "\t" . '				$array[$str] = $this->$method();' . "\n";
		$str .= "\t" . '			}' . "\n";
		$str .= "\t" . '		}' . "\n";
		$str .= "\t" . '	}' . "\n";
		$str .= "\t" . '	return count($array) ? $array : false;' . "\n";
		$str .= "\t" . '}' . "\n";
		
		return $str;
	}
	
	function createFunction($field)
	{
		$functionName = ucfirst($field['Field']);
		$str = "\n\t" . 'public function set' . $functionName . '($value)';
	    $str .= "\n\t{";
	    $str .= "\n\t\t" . '$this->_' . $field['Field'] . ' = ' . $this->getType($field['Type']) . '$value;';
	    $str .= "\n\t\t" . 'return $this;';
	    $str .= "\n\t}\n";
	    
	    $str .= "\n\t" . 'public function get' . $functionName . '()';
	    $str .= "\n\t{";
	    $str .= "\n\t\t" . 'return $this->_' . $field['Field'] . ';';
	    $str .= "\n\t}\n";

	    return $str;
	}
	
	public function createMapper()
	{
		$str = ('<?php') . " \nclass ". MODULE . "_Model_" . MODEL . "Mapper";
		$str .= "\n{\n";
		
		$str .= "\t" . '//Db table object like ' . MODULE . '_Model_DbTable_' . MODEL;
		$str .= "\n\t" . 'protected $_dbTable;' . "\n";
		
		$str .= "\n\t" . '//creating a db table object'; 
		$str .= "\n\t" . 'public function setDbTable($dbTable)';
		$str .= "\n\t" . '{';
			$str .= "\n\t\t" . '//check if it is the name of object in dbtable variable';
			$str .= "\n\t\t" . 'if (is_string($dbTable))';
			$str .= "\n\t\t" . '{';
				$str .= "\n\t\t\t" . '//if name then it is actually a class name like "' . MODULE . '_Model_DbTable_' . MODEL . '" then create an object of that classname';
	            $str .= "\n\t\t\t" . '$dbTable = new $dbTable();';
	        $str .= "\n\t\t" . '}' . "\n";
	        $str .= "\n\t\t" . '//object either already exists or created (by checking dbTable above)';
	        $str .= "\n\t\t" . '//check the object instance if it is created from Zend_Db_Table_Abstract class';
	        $str .= "\n\t\t" . 'if (!$dbTable instanceof Zend_Db_Table_Abstract) {';
	            $str .= "\n\t\t\t" . 'throw new Exception(\'Invalid table data gateway provided\');';
	        $str .= "\n\t\t" . '}';
	        $str .= "\n\t\t" . '//assign dbTable object to protected variable $_dbTable';
	        $str .= "\n\t\t" . '$this->_dbTable = $dbTable;';
	        $str .= "\n\t\t" . 'return $this;';
		$str .= "\n\t" . '}' . "\n";
	
		$str .= "\n\t" . '//function to access dbTable object';
		$str .= "\n\t" . 'public function getDbTable()';
	    $str .= "\n\t" . '{';
	    	$str .= "\n\t\t" . '//If the dbTable object isnot already created create one';
	        $str .= "\n\t\t" . 'if (null === $this->_dbTable) {';
	            $str .= "\n\t\t\t" . '$this->setDbTable(\'' . MODULE . '_Model_DbTable_' . MODEL . '\');';
	        $str .= "\n\t\t" . '}';
	        $str .= "\n\t\t" . 'return $this->_dbTable;';
	    $str .= "\n\t" . '}' . "\n\n";
	    
		$str .= "\n\t" . '//function to validate records';
		$str .= "\n\t" . 'public function validateNoRecordExists($field, $value, $exclude = false)';
	    $str .= "\n\t" . '{';
	        $str .= "\n\t\t" . '$data = array(';
	            $str .= "\n\t\t\t" . '\'table\' => $this->getDbTable()->getTableName(),';
	            $str .= "\n\t\t\t" . '\'field\' => $field';
	        $str .= "\n\t\t" . ');';
	        
	        $str .= "\n\n\t\t" . 'if ($exclude) {';
	            $str .= "\n\t\t\t" . '$data[\'exclude\'] = $exclude;';
	        $str .= "\n\t\t" . '}';
	        
	        $str .= "\n\n\t\t" . '$validator = new Zend_Validate_Db_NoRecordExists($data);';
	        $str .= "\n\t\t" . 'return $validator->isValid($value);';
	    $str .= "\n\t" . '}' . "\n\n";
		
	    //Field
	    $model = strtolower(MODEL);
	    
	    $str .= "\n\t" . 'public function find(' . MODULE . '_Model_' . MODEL . ' $' . $model . ', $id=null)';
		$str .= "\n\t" . '{';
		$str .= "\n\t\t" . 'if (!$id) $id = $' . $model . '->getId();';
			    	
		$str .= "\n\t\t" . '$result = $this->getDbTable()->find($id);';
		$str .= 	        "\n\t\t" . 'if (0 == count($result)) {';
		$str .= 	        "\n\t\t\t" . 'return;';
		$str .= 	        "\n\t\t" . '}';
		$str .= 	        "\n\t\t" . '$row = $result->current();';
		$str .= 	        "\n\t\t" . '$' . $model;
			        foreach ($this->fields as $field) {
				        $functionName = ucfirst($field['Field']);
			        	$str .= "\n\t\t\t" .  '->set' . $functionName . '($row->' . $field['realname'] . ')';
			        }
		$str .= "\n\t\t\t" .';';
		$str .=     "\n\t" . '}' . "\n";
		
		$str .=   "\n\t" .  'public function fetchAll($condition = null, $return = \'object\')';
		$str .=    "\n\t" . '{';
		$str .=        "\n\t\t" . '$resultSet = $this->getDbTable()->fetchAll($condition);';
		$str .=        "\n\t\t" . '$entries   = array();';
		$str .=        "\n\t\t" . 'if ($return == \'object\') {';
		$str .=        "\n\t\t\t" . 'foreach ($resultSet as $row) {';
		$str .=             "\n\t\t\t\t" . '$entry = new ' . MODULE . '_Model_' . MODEL . '();';
		$str .=            "\n\t\t\t\t" . '$entry';
		
		foreach ($this->fields as $field) {
			$str .=  "\n\t\t\t\t\t" .  '->set' . ucfirst($field['Field']) . '($row->' . $field['realname'] . ')';
		}
		
		$str .=         "\n\t\t\t\t\t" .  ';';
		$str .=            "\n\t\t\t\t" . '$entries[] = $entry;';
		$str .=        "\n\t\t\t" . '}';
		$str .=        "\n\t\t" . '} else {';
		
		$str .=        "\n\t\t\t" . 'foreach ($resultSet as $row) {';
		
		$field = $this->fields[1];

		$str .=            "\n\t\t\t\t" . '$entries[] = $row->' . $field['realname'] . ';';
		$str .=        "\n\t\t\t" . '}';
		
		$str .=        "\n\t\t" . '}';
		
		$str .=        "\n\t\t" . 'return $entries;';
		$str .=    "\n\t" . '}' . "\n" ;

		
		$str .= "\n\t" . 'public function insert(' . MODULE . '_Model_' . MODEL . ' $' . $model . ')' . "\n";
		$str .= "\t" . '{' . "\n";
		$str .= "\t" . '	return $this->getDbTable()->insert($' . $model . '->toArray());' . "\n";
		$str .= "\t" . '}' . "\n";
		$str .= "\n\t" . 'public function update($' . $model . ', $value) {' . "\n";
		$str .= "\t" . '	if (!trim($value)) return ;' . "\n\n";
		$str .= "\t" . '	if ($' . $model . ' instanceof ' . MODULE . '_Model_' . MODEL . ') {' . "\n";
		$str .= "\t" . '		$' . $model . ' = $' . $model .'->getOptions();' . "\n";
		$str .= "\t" . '	}' . "\n\n";
		$str .= "\t" . '	if (count($' . $model . ')) {' . "\n";
		$str .= "\t" . '		return $this->getDbTable()->update($' . $model . ',' . "\n";
		$str .= "\t" . '			is_numeric($value) ? ("id = \'$value\'") : $value ' . "\n";
		$str .= "\t" . '		);' . "\n";
		$str .= "\t" . '	};' . "\n";
		$str .= "\t" . '}' . "\n";
		$str .= "\n\t" . 'public function delete($condition)' . "\n";
		$str .= "\t" . '{' . "\n";
		$str .= "\t" . '	return $this->getDbTable()->delete($condition);	' . "\n";
		$str .= "\t" . '}' . "\n";
		  
		$str .= "\n\n" . '}';
		//$str .= "\n";
		
		
		
		$file = fopen("models/" . MODEL . "Mapper.php", 'w');
		fwrite($file, $str);
		fclose($file);
		echo "Model created as models/" . MODEL . ".php";
	}
	
	public function getType($type)
	{
		if (preg_match("/^int/i", $type))
		return '(int) ';
		
		if (preg_match("/(char|text)/i", $type))
		return '(string) ';
		
		return false;
	}
	
	
	public function getArray()
	{
		$str = "\n\tpublic function toArray()";
		$str .= "\n\t{";
		$str .= "\n\t\treturn array(";
		
		foreach ($this->fields as $field) {
			$str .=  "\n\t\t\t'{$field['realname']}' => " . '$this->get' . ucfirst($field['Field']) . '(),';
		}
		
		$str .= "\n\t\t);";
		$str .= "\n\t}\n";
		
		return $str;
	}
	
	function createForm() {
		$str = ('<?php') . " \nclass ". MODULE . "_Form_" . MODEL;
		$str .= " extends Zend_Form \n";
		$str .= "{\n";
		$str .= "\tpublic function init() \n";
		$str .= "\t{\n";
		$str .= "\t\t" . '$this->setMethod(\'post\')->setAction(\'\');' . "\n";

		foreach ($this->fields as $field) {
			
			if ($field['realname'] == 'id') continue;
			$str .=  "\n\t\t" . 
		'$this->addElement(\'text\', \'' . lcfirst($field['Field']) . '\', array(
    	\'required\' 		=> true,
    	\'label\'    		=> \'' . ucfirst(str_replace("_", " ", $field['realname'])) . ($field['Null'] == 'NO' ? ' <span class="required">*</span>' : '') . '\',
    	\'class\'			=> \'long\',
    	\'filters\'  		=> array(\'stringTrim\'),
    	\'validators\'	=> array(
    		array(\'NotEmpty\', true, array(\'Messages\' => \'\')),
    		array(\'validator\' => \'stringLength\', \'options\' => array(1, 150), \'messages\' => \'\')
    	)
    ));' . "\n"
			;
		}
			
		$str .= "\n\n\t\t" . 
		'// Add the submit button
    $this->addElement(\'submit\', \'submit\', array(
    	\'value\' => \'SUBMIT\',
    	\'ignore\' => true,
    	\'class\' => \'button\',
    	//\'onclick\' => \'submitCustomForm(this);\'
     ));
      // Add the submit button
    $this->addElement(\'reset\', \'reset\', array(
    	\'value\' => \'reset\',
    	\'ignore\' => true,
    	\'class\' => \'button\'
     ));
     
     
    // And finally add some CSRF protection
    $this->addElement(\'hash\', \'csrf\', array(
        \'ignore\' => true,
    ));
    
	$this->addElement(\'hidden\', \'id\', array(
        \'ignore\' => true,
    ));
    
    $this->setElementDecorators(array(
    	\'ViewHelper\',
    	\'Errors\'
    ));';
		
		$str .= "\n\t}\n";
		$str .= "}\n";
		
		$file = fopen("models/forms/" . MODEL . ".php", 'w');
		fwrite($file, $str);
		fclose($file);
		echo "Form created as models/forms/" . MODEL . ".php";
		
	}
	
	
	function createformHtml() {
		$str = '<h1><?php echo $this->action; ?>Create ' . MODEL . '</h1>
<div id="' . strtolower(MODEL) . '" >
<?php if ($this->msg) : 
		echo $this->msg; ?>
	 <ul	class="nav-h align-right">
        <li class="last">
        <a href="<?php echo $this->storeUrl(\'/' . MODULE . '/' . MODEL . '/index\'); ?>">List ' . MODEL . '</a></li>
    </ul>

<?php else: ?>
	<!-- begin main content area -->
<?php echo $this->decorateErrors(); ?>

<form method="<?php echo $this->form->getMethod(); ?>" action="<?php echo $this->form->getAction(); ?>" enctype="<?php echo $this->form->getAttrib(\'enctype\') ?>" name="add-' . strtolower(MODEL) . '" id="add-' . strtolower(MODEL) . '">
  
  <!-- begin centercol -->
	<table cellspacing="5" cellpadding="0" border="0">';
 foreach ($this->fields as $field) {
 	if ($field['realname'] == 'id') continue;
 $str .= "\n\t\t\t" . '<tr>
          <td valign="top"><?php echo $this->form->' . lcfirst($field['Field']) . '->getLabel()?></td>
          <td>&nbsp;</td>
          <td><?php echo $this->form->' . lcfirst($field['Field']) . '?></td>
        </tr>';
	}
  $str .= "\n" . '</table>
  <div class="right-foot-r"> <span class="required">* Required Fields</span> <br />
	<?php echo $this->form->csrf; ?> 
    <?php echo $this->form->id; ?> 
     <ul id="controls" style="float:right;"><li> <?php echo $this->form->reset; ?> </li><li> <?php echo $this->form->submit; ?> </li></ul>
  </div>
</form>
<?php endif; ?>
</div>';

		$file = fopen("models/forms/" . strtolower(MODEL) . ".phtml", 'w');
		fwrite($file, $str);
		fclose($file);
		echo "Form created as models/forms/" . MODEL . ".php";      	 
	}

}
	
	
	
	
	
	if (isset($_GET['table']) && isset($_GET['module']) && isset($_GET['model']))
	{
		define('TABLE',  $_GET['table']);
		define('MODULE',  ucfirst($_GET['module']));
		define('MODEL',  ucfirst($_GET['model']));
		
		$model = new model();
		$model->createModel();
		$model->createMapper();
		$model->createForm();
		$model->createformHtml();
	}
?>