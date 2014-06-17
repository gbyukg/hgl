<?php
class Common_Acl extends Zend_Acl {
	
	public function __construct($userid = "") {
		
		$db = Zend_Registry::get ( "db" );
		
		//得到所有资源
		$sql = "SELECT RESOURCECONTENT,RESOURCEID,RESOURCECONTENT,RESOURCETYPEID,RESOURCEFLG FROM ACL_RESOURCE";
		$resoures = $db->fetchAssoc ( $sql );
		
		//得到该用户的权限
		$sql = "SELECT DISTINCT 
		        RESOURCEID,
		        RESOURCECONTENT,
		        OPERATIONID 
		        FROM VIEW_ACL_USER_PRIVILEGE WHERE USERID = :USERID
                ORDER BY RESOURCEID,OPERATIONID";
		
		$privileges = $db->fetchAll ( $sql, array ('USERID' => $userid ) );
		
		//添加Role
		//$this->addRole ( new Zend_Acl_Role ( $userid ) );
		
		
		//添加Resource
		foreach ( $resoures as $resoure ) {
			$this->addResource ( new Zend_Acl_Resource ( $resoure ["RESOURCECONTENT"] ) );
			//无需权限任意访问的资源
			if($resoure ["RESOURCEFLG"] == '1')
			{
			  //$this->allow($userid,$resoure ["RESOURCECONTENT"],NULL);
			}
		}
		
		//添加权限
		foreach ( $privileges as $privilege ) {
			$this->allow ( null, $privilege ["RESOURCECONTENT"], $privilege ["OPERATIONID"] );
		}
		
	
	}
	
	
	/*
	 * 
	 */
	function can_access($resource) {

       return $this->isAllowed(null, $resource, '001')? TRUE : FALSE;

    }
	

	function can_show($resource) {

       return $this->isAllowed(null, $resource, '002')? TRUE : FALSE;

    }

    function can_click($resource) {

       return $this->isAllowed(null, $resource, '003')? TRUE : FALSE;

    }
    
    function can_read($resource) {

       return $this->isAllowed(null, $resource, '004')? TRUE : FALSE;

    }
    
    
    function can_modify($resource) {

       return $this->isAllowed($role, $resource, '005')? TRUE : FALSE;

    }

    function can_delete($resource) {

       return $this->isAllowed($role, $resource, '006')? TRUE : FALSE;

    }

    function can_publish($resource) {

    	return $this->isAllowed($role, $resource, '007')? TRUE : FALSE;

    }	
	
}