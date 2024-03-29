<?php
class Common_Client {
	
	/*
	 * 获取客户端IP地址
	 */
	public static function GetIP() {
		$realip = NULL;
		if (isset ( $_SERVER )) {
			if (isset ( $_SERVER ["HTTP_X_FORWARDED_FOR"] )) {
				$realip = $_SERVER ["HTTP_X_FORWARDED_FOR"];
			} elseif (isset ( $_SERVER ["HTTP_CLIENT_IP"] )) {
				$realip = $_SERVER ["HTTP_CLIENT_IP"];
			} else {
				$realip = $_SERVER ["REMOTE_ADDR"];
			}
		} else {
			if (getenv ( 'HTTP_X_FORWARDED_FOR' )) {
				$realip = getenv ( 'HTTP_X_FORWARDED_FOR' );
			} elseif (getenv ( 'HTTP_CLIENT_IP' )) {
				$realip = getenv ( 'HTTP_CLIENT_IP' );
			} else {
				$realip = getenv ( 'REMOTE_ADDR' );
			}
		}
		return $realip;
	
	}

}