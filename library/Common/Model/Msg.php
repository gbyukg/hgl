 <?php
	class Common_Model_Msg {
		
		/*
     * 取得信息内容
     */
		public static function getMsg($msgcode) {
			
			$msg = "消息无法取得,请检查消息表"."msgcode:".$msgcode;
			$db = Zend_Registry::get ( 'db' );
			$sql = 'SELECT MESSAGE FROM M_MESSAGE WHERE MSGCODE = :MSGCODE';
			@$result = $db->fetchOne ( $sql, $msgcode );
			
			//存在则返回，不存在则返回默认信息
			return $result == TRUE ? $result : $msg;
		
		}
	}
    
    
    
    