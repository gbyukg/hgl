<?php
/***
**
**具体的父类查看Zend_Db_Adapter_Abstract
**也就是/library/Zend/Db/Adapter/Abstract.php
**
**
**/
abstract class Common_Model_Table extends Zend_Db_Table
{
	/**
	 * table name
	 *
	 * 
	 */
	protected  $_name;	
	
	/**
	 * database connection
	 *
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected  $_db;
	
	function __construct(){
		$this->_db = Zend_Registry::get('db');
		$this->_name = SQL_PREFIX.$this->_name;
		parent::__construct();
		
	}
	
		
}
?>