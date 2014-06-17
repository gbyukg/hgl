<?php
class Common_Plugin_Acl extends Zend_Controller_Plugin_Abstract {
	/**
	 * @var Zend_Acl
	 */
	private $_acl = null;
	
	public function preDispatch(Zend_Controller_Request_Abstract $Request) {
		
		try {
			
			$authInfo = new Zend_Session_Namespace ( 'authInfo' );
			
			//得到登录用户
			if ($authInfo->login == TRUE) {
				$userid = $authInfo->user->USERID;
			} else {
				//游客
				$userid = 'guest';
			}
			
			//ACL信息不存在则从数据库中取得
			if (! isset ( $authInfo->acl )) {
				//权限列表取得
				$authInfo->acl = new Common_Acl ( $userid );
			}
			
			//欲访问url取得
			$url = $Request->getModuleName () . "/" . $Request->getControllerName () . "/" . $Request->getActionName ();
			
			//验证是否有访问该url的权限
			if ($authInfo->acl->has ( $url )) {
				if (! $authInfo->acl->can_access ( $url )) {
					throw new Common_Exception ( '系统检测到您没有访问此功能的权限,请联系系统管理员' );
				}
			}
			
			//记录访问日志
			//Common_Logger::logMessage();
			
			
		} catch ( Exception $e ) {
			throw $e;
		
		}
	
	}

}