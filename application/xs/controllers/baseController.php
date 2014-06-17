<?php
class xs_controllers_baseController extends Common_Controller_Base {
	
	protected $_auth = null;   //登陆用户信息
	/**
	 * acl对象
	 *
	 * @var Common_Acl
	 */
	protected $_acl = null;    //权限信息
	
	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		parent::__construct ( $request, $response, $invokeArgs );
		

		
//		$authInfo = new Zend_Session_Namespace ( 'authInfo' );
//		
//		//判断是否已经登陆
//		if ($authInfo->login == TRUE) {
//			//得到当前登录用户信息和所属权限
//			$authInfo = new Zend_Session_Namespace ( 'authInfo' );
//			$this->_user = $authInfo->user;
//			$this->_acl = $authInfo->acl;
//		} else {
//			//跳转到登陆页面
//			$this->_redirect ( '/main/login/' );
//		}
	
	}
}