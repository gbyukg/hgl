<?php

/**
 * 架构级系统基类*
 * 处理request,response，视图模板，路径等信息
 *
 */
abstract class Common_Controller_Base extends Zend_Controller_Action {
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
	
	function __construct(Zend_Controller_Request_Abstract $Request, Zend_Controller_Response_Abstract $Response, array $invokeArgs = array()) {
		parent::__construct ( $Request, $Response, $invokeArgs );
		$_auth = new Common_Auth ( );
			$_auth->qybh = "001";
			$_auth->bmbh = "000001";
			$_auth->bmmch = "销售部";
			$_auth->ckbh = "110000";
			$_auth->ckmch = "一仓库";
			$_auth->userId = "00000001";
			$_auth->userName = "系统测试员";
			$_auth->mdbh = "000001";
			$_auth->mdmch = "棠梨沟门店";
			$_auth->logonTime = "2011-02-10 12:50:23";
			$_SESSION ["auth"] = $_auth;
			/*
		if($Request->getModuleName () =="sys" && $Request->getControllerName()=="login" ){
			
		}else{
			if(!isset($_SESSION ["auth"])){
				//Common_Logger::logMessage($_SESSION ["auth"]);
				$this->_redirect('/sys/login/index');
			}
		}*/
		
			
		$htmlEntities = new Zend_Filter_HtmlEntities ( array ("quotestyle" => ENT_NOQUOTES, "charset" => "UTF-8" ) );
		foreach ( $_REQUEST as $key => $requestEntity ) {
			if (! is_array ( $requestEntity )) {
				$_REQUEST [$key] = $htmlEntities->filter ( $requestEntity );
			}
		}
		
		foreach ( $_POST as $key => $postEntity ) {
			if (! is_array ( $postEntity )) {
				$_POST [$key] = $htmlEntities->filter ( $postEntity );
			}
			
			//表格数据自动转换
			if (substr ( $key, 0, 6 ) == "#grid_" || substr ( $key, 0, 6 ) == "grid_") {
				$_POST [$key] = Common_Tool::unSerializeToGrid ( $_POST [$key] );
			}
		}
		foreach ( $_GET as $key => $getEntity ) {
			if (! is_array ( $getEntity )) {
				$_GET [$key] = $htmlEntities->filter ( $getEntity );
			}
		}
				
		
		if ($Request == "" && $Response == "") {
			$this->_controller = Zend_Registry::get ( "controller" );
			$Request = $this->_controller->getRequest ();
			$Response = $this->_controller->getResponse ();
		}
		
	
		
		$this->_basePath = ($Request->getBasePath () == "\\") ? "" : $Request->getBasePath ();
		//根据当前模块定义视图模板的路径信息
		$config = Zend_Registry::get ( "configuration" );
		@define ( "MODULE_NAME", $Request->getModuleName () );//模块名
		@define ( "THEMESPATH", TEMPLATE_PATH . '/modules/' . MODULE_NAME);//画面模板目录
		@define ( "ROOTURL", $this->_basePath );
		@define ( "THEMESURL", ROOTURL . '/' . TEMPLATEURL . '/public');
		@define ( "MODULEURL", ROOTURL . '/' . TEMPLATEURL . '/modules/' . MODULE_NAME);
		
		$this->_view = Zend_Registry::get ( "view" );
		$this->_view->setCompileDir ( TEMPLATE_C_PATH . '/' . MODULE_NAME );
		//目录不存在则自动建立
		if (! file_exists ( TEMPLATE_C_PATH . '/' . MODULE_NAME )) {
			mkdir ( TEMPLATE_C_PATH . '/' . MODULE_NAME, 0777 );
			@chmod ( TEMPLATE_C_PATH . '/' . MODULE_NAME, 0777 );
		}
		$this->_view->setTemplateDir ( THEMESPATH);

		//引入当前模块配置文件
		$config = CONF_PATH . '/config_' . MODULE_NAME . '.php';
		if (file_exists ( $config )) {
			@include_once ($config);
		}
		$this->_view->assign ( "WEB_NAME", WEB_NAME ); //网站名称
		$this->_view->assign ( "MODULE_NAME", MODULE_NAME ); //模板式样目录
		$this->_view->assign ( "THEMESURL", THEMESURL ); //模板式样目录
		$this->_view->assign ( "MODULEURL", MODULEURL ); //模板目录
		$this->_view->assign ( "ROOTURL", ROOTURL ); //根目录
		//$this->_view->assign("BASEURL", BASEURL); //当前模块根目录
		$this->_view->assign ( "REQUEST", $_REQUEST );
		$this->_view->assign ( "POST", $_POST );
		$this->_view->assign ( "GET", $_GET );
		//保存当前正在访问路径内容
	}
	
	/**
	 * 系统提示信息
	 *
	 * @access      public
	 * @param       string      msg_detail      消息内容
	 * @param       int         msg_type        消息类型， 0消息，1错误，2询问
	 * @param       array       links           可选的链接
	 * @param       boolen      $auto_redirect  是否需要自动跳转
	 * @return      void
	 */
	public function sys_msg($msg_detail, $msg_type = 0, $links = array(), $auto_redirect = false, $showback = true) {
		//消息数组
		$infoArray = array ('msg_detail' => $msg_detail, 'msg_type' => $msg_type, 'links' => $links, 'auto_redirect' => $auto_redirect, 'showback' => $showback );
		$_SESSION ['infoArray'] = $infoArray;
		//消息显示共通页面
		$this->_redirect ( '/common/info/showmsg' );
	}

}


