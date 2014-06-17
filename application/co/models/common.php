<?php 
class co_models_common extends Common_Model_Base {
	
	public  function __construct() {
		$this->_db = Zend_Registry::get ( "db" );
	}
	
	
	public static function getDanwei($dwbh){
		try{
	    //检索SQL
	    //$this->_db = Zend_Registry::get ( "db" );
		$sql = "SELECT * FROM H01DB012106 WHERE QYBH='".$_SESSION['auth']->qybh."' AND DWBH ='".$dwbh."'";
		return Zend_Registry::get ( "db" );
		}catch (Exception  $e){
			return $e;
		}

	}
	
	
	
	
	
	
	
	
	
	
}