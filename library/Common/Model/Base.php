<?php 
 class Common_Model_Base {
	 /**
     * database connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db = null;
    
    function __construct()
    {
        $this->_db = Zend_Registry::get('db');
     
    }
    
    public function beginTransaction(){
    	$this->_db->beginTransaction();
    }
    
    public function commit(){
    	$this->_db->commit();
    }
    
    public function rollBack(){
    	$this->_db->rollBack();
    }    
    

    
 
	
	
	
	
	
    
	
}