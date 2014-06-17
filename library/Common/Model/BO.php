<?php
class Common_Model_BO
{
	/**
	 * database connection
	 *
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected  $_db;
	
	function __construct(){
		$this->_db = Zend_Registry::get('db');
	}

	/**
	 * 全部记录列表*
	 */
	public  function  listAll(){
		return $this->_dao->getAllinfo();	
	}
    
		/**
		 * 增加记录*
		 */
	    public  function  add(array $entity){
			try {   
				unset($entity["SUBMIT"]);
				$this->_dao->insert($entity);
			}catch (Exception $e) {
				throw $e;
			}
		}
		
		/**
		 * 修改记录*
		 */
	    public  function  modi(array $entity){
			try {  
				if ($entity["ID"]==""){
					throw new Exception("id值不能为空");
				}
				unset($entity["SUBMIT"]);
				$this->_dao->update($entity,"ID='".$entity["ID"]."'");
			}catch (Exception $e) {
				throw $e;
			}
		}
		
		/**
		 * 删除记录*
		 */
	    public  function  del($entity){
			try {   
				if ($entity["ID"]==""){
					throw new Exception("id值不能为空");
				}
				$this->_dao->delete("ID='".$entity["ID"]."'");
			}catch (Exception $e) {
				throw $e;
			}
		}
		
		
		/**
		 * 得到一条记录记录*
		 */
	    public  function  getOne($entity){
	    	if ($entity["ID"]=="")  return false;
			$result = $this->_dao->getinfo("ID",$entity["ID"]);
			if ($result["ID"]=="") {
				return false;
			}else{
				return $result;
			}
		}


		/**
		 * 分页显示记录列表*
		 */
	    public  function  listPage(){
		    $filter = array();
		    $filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
		    $filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
		    
		    $filter['record_count'] = $this->_dao->getRecordCount();
		     
		    /* 获得总记录数据 */
		    $MyCore_Pager = new Common_Pager();
		    $filter = $MyCore_Pager->page_and_size($filter);
		    $list = $this->_dao->getPage($filter);
		    return array('list' => $list, 'filter' => $filter, 'page_count' =>  $filter['page_count'], 'record_count' => $filter['record_count']);
		}
}
