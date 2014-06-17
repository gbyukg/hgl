<?php
class Common_Model_Application {
	/**
	 * 载入系统配置文件
	 * registry
	 * @throws Exception
	 */
	public static function loadConfiguration() {
		try {
			$configuration = new Zend_Config_Ini ( CONFIGURATION_FILE );
			Zend_Registry::set ( 'configuration', $configuration );
		
		} catch ( Exception $e ) {
			throw new $e ( );
		}
	}
	
	/**
	 * 设置常量
	 * registry
	 * @throws Exception
	 */
	public static function loadConst() {
		try {
			
			$configuration = Zend_Registry::get ( 'configuration' );
			define ( 'SQL_PREFIX', $configuration->db->sqlprefix );
			define ( 'WEB_NAME', $configuration->default->web_name );
			date_default_timezone_set ( $configuration->default->web_timezone );
		
		} catch ( Exception $e ) {
			throw new $e ( );
		}
	}
	
	/**
	 * 载入数据库连接
	 * registry
	 * @throws Exception
	 */
	public static function loadDB() {
		$configuration = Zend_Registry::get ( 'configuration' );
		$db = Zend_Db::factory ( $configuration->db );
		
		if ($configuration->db->tryconnect == '1') {
			//是否测试数据库可以连通,会影响网站的显示速度
			try {
				$TablesArr = $db->listTables ();
			
			} catch ( Exception $e ) {
				throw $e;
			}
		}
		
		
		Zend_Registry::set ( 'db', $db );
		Zend_Db_Table::setDefaultAdapter ( $db );
	}
	
	/**
	 * Controller初始化
	 * registry
	 * @throws Exception
	 */
	public static function loadController() {
		$router = new Zend_Controller_Router_Rewrite ( );
		$controller = Zend_Controller_Front::getInstance ();
		//指定模块目录
		
		$controller->setModuleControllerDirectoryName ( 'controllers' );
		$controller->setDefaultModule('sys');
		$controller->setDefaultControllerName('main');
		$controller->setDefaultAction('index');
		$controller->addModuleDirectory ( APP_PATH );
		$controller->setRouter ( $router )->returnResponse ( true );
				
        //权限验证插件
		//$controller->registerPlugin(new Common_Plugin_Acl());
		
		//错误控制插件
		$controller->registerPlugin ( new Zend_Controller_Plugin_ErrorHandler ( array ('module' => 'gt', 'controller' => 'info', 'action' => 'error' ) ) );
		//$controller->setParam ( 'noErrorHandler', true );
		
		//禁止自带模板
		$controller->setParam ( 'noViewRenderer', true );
		
		Zend_Registry::set ( 'controller', $controller );
	
	}
	
	public static function loadSmarty() {
		
		$view = new Common_View_Smarty ( );
		Zend_Registry::set ( 'view', $view );
	
	}
	
	/**
	 * Get the writer stream for the logger
	 * @return Zend_Log_Writer_Abstract
	 */
	public static function getLoggerStream() {
		
		//日志文件目录不存在则自动建立
		if (! file_exists ( LOG_PATH )) {
			mkdir ( LOG_PATH, 0777 );
			@chmod ( LOG_PATH, 0777 );
		}
		
		$date = new Zend_Date ( );
		$accesslog = $date->toString ( "'access_'YYYYMMdd'.log'" );
		$errorlog = $date->toString ( "'error_'YYYYMMdd'.log'" );
		
		try {
			return array ('accesslog' => new Zend_Log_Writer_Stream ( self::create ( LOG_PATH, $accesslog ) ), 'errorlog' => new Zend_Log_Writer_Stream ( self::create ( LOG_PATH, $errorlog ) ) );
		} catch ( Exception $e ) {
			throw $e;
		}
	
	}
	
	
}
