<?php
/*
 * 消息处理共通
 */
class co_infoController extends co_controllers_baseController  {
	/**
	 * View对象
	 *
	 * @var Common_View_Smarty
	 */
	protected $_view;
	
	/**
	 * controller对象
	 *
	 * @var controller
	 */
	protected $_controller;
	
	/**
	 * session名称
	 *
	 * @var 
	 */
	protected $_sessionName;
	
	/**
	 * index的文件夹路径*
	 *
	 * @var unknown_type
	 */
	protected $_basePath;
	
	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		parent::__construct ( $request, $response, $invokeArgs );
	
	}
	
	/*
	 * 错误处理
	 */
	public function errorAction() {
		$errors = $this->_getParam ( 'error_handler' );
     	$this->getResponse ()->clearBody ();
		//echo $errors;
		//echo "I am sorry ,a server side error has happend,please contact to system administrator.";
		Common_Logger::logException($errors->exception);
		//Zend_Debug::dump($errors);
		
		echo "server error!";
		
	
	}
	
	/*
	 * 消息显示
	 */
	public function showmsgAction($exception) {
		
		$msg = $exception->msg;
		
		$infoArray = $_SESSION ['infoArray'];
		
		$this->_view->assign ( 'ur_here', "系统信息" );
		$this->_view->assign ( 'msg_detail', $msg ); //消息内容
		$this->_view->assign ( 'msg_type', $infoArray ['msg_type'] );
		$this->_view->assign ( 'links', $infoArray ['links'] );
		$this->_view->assign ( 'defaultlink', $infoArray ['links'] ['default'] );
		$this->_view->assign ( 'default_url', $infoArray ['links'] [0] ['href'] );
		$this->_view->assign ( 'auto_redirect', $infoArray ['auto_redirect'] );
		$this->_view->assign ( 'showback', $infoArray ['showback'] );
		$this->_view->display ( 'message.php' );
	
	}

}