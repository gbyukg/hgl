<?php

class Common_Logger {
	/**
	 * @var Zend_Log
	 */
	private static $accesslogger = null;
	private static $errorlogger = null;
	
	/**
	 * Save a string in the log file
	 * @param string|$msg
	 * @param integer $type - one of Zend_Log constants
	 * @return boolean
	 */
	public static function logMessage($msg = "", $msgaddition = "") {
		try {
			
			self::$accesslogger->setEventItem ( 'uid', $_SESSION ["auth"]->userId ); //用户id
			self::$accesslogger->log ( $msg . ' ' . $msgaddition, Zend_Log::INFO );
			return true;
		} catch ( Zend_Log_Exception $e ) {
			Zend_Debug::dump ( $e );
		}
		
		return false;
	}
	
	/**
	 * Save an exception as an error in the log file
	 * @param Exception $e
	 * @return boolean
	 */
	public static function logException(Exception $e) {
		
		try {
			Zend_Debug::setSapi ( 'cli' );
			self::$errorlogger->setEventItem ( 'uid', $_SESSION ["auth"]->userId );
			self::$errorlogger->log ( Zend_Debug::dump ( $e, NULL, FALSE ), Zend_Log::ERR );
			return true;
		} catch ( Zend_Log_Exception $e ) {
			Zend_Debug::dump ( $e );
		
		}
		return false;
	}
	
	/**
	 * @param Zend_Log_Writer_Abstract $stream
	 */
	public static function setLoggerWriter() {
		
		$log = LOG_PATH . '/' . date ( 'Y-m-d' ) . '.log';
		$errlog = LOG_PATH . '/' . date ( 'Y-m-d' ) . '_error.log';
		
		self::$accesslogger = new Zend_Log ( );
		$stream = @fopen ( $log, 'a', false );
		$writer = new Zend_Log_Writer_Stream ( $stream );
		$format = '%datetime% %ip% %uid% ' . ' %message%' . PHP_EOL;
		$formatter = new Zend_Log_Formatter_Simple ( $format );
		$writer->setFormatter ( $formatter );
		self::$accesslogger->addWriter ( $writer );
		
		self::$errorlogger = new Zend_Log ( );
		$stream = @fopen ( $errlog, 'a', false );
		$writer = new Zend_Log_Writer_Stream ( $stream );
		$format = '%datetime% %ip% %uid% %url%' . ' %message%' . PHP_EOL;
		$formatter = new Zend_Log_Formatter_Simple ( $format );
		$writer->setFormatter ( $formatter );
		self::$errorlogger->addWriter ( $writer );
		
		self::$accesslogger->setEventItem ( 'datetime', date ( 'Y-m-d H:i:s' ) );
		
		self::$accesslogger->setEventItem ( 'ip', Common_Client::GetIP () ); //登陆ip
		//self::$accesslogger->setEventItem ( 'url', $_SERVER ["REQUEST_URI"] );            //当前路径
		

		self::$errorlogger->setEventItem ( 'datetime', date ( 'Y-m-d H:i:s' ) );
		
		self::$errorlogger->setEventItem ( 'ip', Common_Client::GetIP () );
		self::$errorlogger->setEventItem ( 'url', $_SERVER ["REQUEST_URI"] );
	
	}
	
	private function __construct() {
	}
	
	public static function logToDb($message) {
		$db = Zend_Registry::get ( "db" );
		
		$columnMapping = array ('QYBH'=>'qybh',
		                        'CHLSHJ'=>'datetime',
		                        'YHID'=>'userid',
		                        'MODULE'=>'module',
		                        'CONTROLLER'=>'controller',
		                        'ACTION'=>'action',
		                        'CONTENT' => 'message',
		                        'IP'=>'ip' );
		$writer = new Zend_Log_Writer_Db ( $db, 'H01DB012002', $columnMapping );
		$logger = new Zend_Log ( $writer );
		$logger->setEventItem ( 'qybh', $_SESSION['auth']->qybh );
		$logger->setEventItem ( 'datetime', new Zend_Db_Expr("SYSDATE") );
		$logger->setEventItem ( 'userid', $_SESSION ['auth']->userId );
        $request = Zend_Registry::get ( "controller" )->getRequest();
       	$logger->setEventItem ( 'module',$request->getModuleName());
		$logger->setEventItem ( 'controller',$request->getControllerName());
		$logger->setEventItem ( 'action',$request->getActionName());
		$logger->setEventItem ( 'ip', Common_Client::GetIP () );
		$logger->info ( $message );
	
	}

}