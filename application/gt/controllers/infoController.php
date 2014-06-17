<?php
/*
 * 消息处理共通
 */
class gt_infoController extends gt_controllers_baseController  {
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
		Common_Logger::logException($errors->exception);
			

	    switch ( $errors->type ) { 
	 	case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER: 
	 	case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:                // 404 错误 -- 控制器或方法没有发现 
	 		 $this->getResponse()->setHttpResponseCode( 404 );
	 		 //$this->view->message = 'Page not found'; 
	 		 break;
	    default:                // application错误 
	    	 $this->getResponse()->setHttpResponseCode( 500 ); 
	    	// $this->view->message = 'Application error';
	    	 break;
	    }
		
	
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