<?php
class Common_Exception extends Zend_Exception {
	
	const EXCEPTION_DB = 'EXCEPTION_DB'; //数据库例外
	const EXCEPTION_NET = 'EXCEPTION_NET'; //网络例外
	const EXCEPTION_ACL = 'EXCEPTION_ACL'; //权限例外
	const EXCEPTION_OTHER = 'EXCEPTION_OTHER'; //一般性例外
	const EXCEPTION_BUSINESS = 'EXCEPTION_LOGIC'; //业务例外
	const EXCEPTION_INFO = 'EXCEPTION_INFO'; //提示信息
	

	private $_msg; //例外信息
	private $_msgcode; //消息code
	private $_exceptiontype; //例外类型
	private $_addition; //例外详情
	
/*
	public function setMessage($value) {
		$this->msg = $value;
	}
	
	public function getMessage() {
		return $this->_msg;
	}
*/
	
	public function setAddition($value) {
		$this->_addition = $value;
	}
	
	public function getAddition() {
		return $this->_addition;
	}
	
	public function setExceptionType($value) {
		$this->_exceptiontype = $value;
	}
	
	public function getExceptionType() {
		return $this->_exceptiontype;
	}
	
	/**
	 * 例外信息
	 *
	 * @param       string      msg      消息内容
	 * @param       string      msgcode  消息code
	 * @param       string      $errtype        
	 * @param       array       links           可选的链接
	 * @param       boolen      $auto_redirect  是否需要自动跳转
	 * @return      void
	 */
	function __construct($msg, $addition) {
		parent::__construct($msg);
		$this->_addition = $addition;

	
	}

}