<?php
class cc_models_shzcqxx extends Common_Model_Base {
	
	
	/**
	 * 获取仓库名称
	 *
	 * 
	 * @return unknown
	 */
	public function getCangkuList() {
		$sql = "SELECT CKBH,CKMCH FROM H01DB012401 " . "WHERE  QYBH =:QYBH " . " ORDER BY CKBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh );
		$cangkuList = $this->_db->fetchPairs ( $sql, $bind );
		$cangkuList ['0'] = '- - 请 选 择 - -';
		ksort ( $cangkuList );
		return $cangkuList;
	}
	
	/**
	 * 获取传送带出口数据
	 *
	 * 
	 * @return unknown
	 */
	public function getCsdList($ckbh) {
		$sql = "SELECT CKBH, CHSDCHK FROM H01VIEW012443" . " WHERE QYBH =:QYBH AND CKBH = :CKBH AND ZHUANGTAI='1'". " ORDER BY CHSDCHK";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh );
		$bind['CKBH'] = $ckbh;
		
		return $this->_db->fetchAll ( $sql, $bind );
	
	}
	
/**
	 * 得到库区信息列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ("","ZHUANGTAI","CKBH", "CHSDCHK", "FJZCQBH" ); //状态，散货暂存区编号，传送带出口
		

		//检索SQL
		$sql = "SELECT DECODE(ZHUANGTAI,'1','启用','X','禁用') AS ZHUANGTAI,CKMCH,CHSDCHK,FJZCQBH,FJZCQMCH,CKBH,SHYZHT
		      FROM H01VIEW012444 WHERE QYBH= :QYBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件  编号或名称
		if($filter['searchParams']['CKBH']!=""){
			$sql .= " AND( CKBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(CKMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['CKBH']);
		}
		
		if($filter['searchParams']['CHSDCHK']!=""){
			$sql .= " AND( CHSDCHK LIKE '%' || :SEARCHKEYCHSDCHK || '%')";
			$bind ['SEARCHKEYCHSDCHK'] = strtolower($filter ["searchParams"]['CHSDCHK']);
		}
		//查找条件  编号或名称
		if($filter['searchParams']['FJZCQBH']!=""){
			$sql .= " AND( FJZCQBH LIKE '%' || :SEARCHKEYFJZCQBH || '%'".
			        "      OR  lower(FJZCQMCH) LIKE '%' || :SEARCHKEYFJZCQBH || '%')";
			$bind ['SEARCHKEYFJZCQBH'] = strtolower($filter ["searchParams"]['FJZCQBH']);
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_SHZCQXX",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 必须添加所有主键
		$sql .= " ,CKBH,FJZCQBH,CHSDCHK";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true,$totalCount, $filter ["posStart"] );
	}
	
	
	
	/**
	 * 取得暂存区信息
	 * @param string $shzcqbh	  散货暂存区编号
	 * @param string $ckbh       仓库编号
	 * @param string $chsdchk    传送带出口
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	
	function getShzcqxx($shzcqbh,$ckbh, $chsdchk,$filter, $flg = 'current') {
		
		//检索SQL
		$fields = array ("", "ZHUANGTAI","CKBH","CHSDCHK","FJZCQBH" ); //状态，散货暂存区编号，传送带出口，
		$sql_list = "SELECT  ZCQROWID,LEAD(ZCQROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",CKBH,FJZCQBH,CHSDCHK) AS NEXTROWID," . 
		" 						   LAG(ZCQROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",CKBH,FJZCQBH,CHSDCHK) AS PREVROWID " . 
		"  ,CKBH,FJZCQBH,CHSDCHK" .
		 " FROM H01VIEW012444 " .
		" WHERE QYBH = :QYBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件 仓库编号或名称
		if($filter['searchParams']['CKBH']!=""){
			$sql_list .= " AND( CKBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(CKMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['CKBH']);
		}
		 //查找条件  传送带出口
		if($filter['searchParams']['CHSDCHK']!=""){
			$sql_list .= " AND( CHSDCHK LIKE '%' || :SEARCHKEYCHSDCHK || '%')";
			$bind ['SEARCHKEYCHSDCHK'] = strtolower($filter ["searchParams"]['CHSDCHK']);
		}
	    //查找条件 散货暂存区 编号或名称
		if($filter['searchParams']['FJZCQBH']!=""){
			$sql_list .= " AND( FJZCQBH LIKE '%' || :SEARCHKEYFJZCQBH || '%'".
			        "      OR  lower(FJZCQMCH) LIKE '%' || :SEARCHKEYFJZCQBH || '%')";
			$bind ['SEARCHKEYFJZCQBH'] = strtolower($filter ["searchParams"]['FJZCQBH']);
		}
		
		$sql_list .= Common_Tool::createFilterSql("CC_SHZCQXX",$filter['filterParams'],$bind);
		
		$sql_single = "SELECT DECODE(ZHUANGTAI,'1','启用','X','禁用') AS ZHUANGTAI,CKMCH,CHSDCHK,FJZCQBH,FJZCQMCH,SHYZHT,CKBH,to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') AS BGRQ,BGZH ".
		 " FROM H01VIEW012444 " ;
		
		//当前				
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH = :QYBH AND CKBH = :CKBH AND FJZCQBH = :FJZCQBH AND CHSDCHK=:CHSDCHK";
		
		unset ( $bind ['SEARCHKEY'] );
		unset ( $bind ['SEARCHKEYCHSDCHK'] );
		unset ( $bind ['SEARCHKEYFJZCQBH'] );
		} else if ($flg == 'next') { //下一条		
			$sql_single .= "WHERE ZCQROWID = (SELECT NEXTROWID FROM  (SELECT NEXTROWID ,CKBH,FJZCQBH,CHSDCHK FROM ( $sql_list ) WHERE CKBH = :CKBH AND FJZCQBH = :FJZCQBH AND CHSDCHK=:CHSDCHK))";
			                
		} else if ($flg == 'prev') { //前一条
			$sql_single .= "WHERE ZCQROWID = (SELECT PREVROWID FROM  (SELECT PREVROWID ,CKBH,FJZCQBH,CHSDCHK FROM ( $sql_list ) WHERE CKBH = :CKBH AND FJZCQBH = :FJZCQBH AND CHSDCHK=:CHSDCHK))";
		}
		//绑定 区域编号 & 仓库编号 &  散货暂存区编号 & 传送带出口
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CKBH'] = $ckbh;
		$bind ['FJZCQBH'] = $shzcqbh;
		$bind ['CHSDCHK'] = $chsdchk;

		
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	
	
	
	/**
	 * 生成散货暂存区信息
	 *
	 * @return bool
	 */
	function insertShzcqxx() {
		
		//判断散货暂存区编号是否存在
		

		if ($this->getShzcqxx ( $_POST ['FJZCQBH'] ) != FALSE) {
			return false;
		} else {
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['FJZCQBH'] = $_POST ['FJZCQBH']; //散货暂存区编号
			$data ['FJZCQMCH'] = $_POST ['FJZCQMCH']; //散货暂存区名称
			$data ['CKBH'] = $_POST ['CKBH']; //仓库编号
			
			$data ['CHSDCHK'] = $_POST ['CHSDCHK']; //传送带出口
		    $data ['SHYZHT'] = '0'; //使用状态 
		    $data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		    $data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		    $data ['BGZH'] = $_SESSION ['auth']->userId; //操作用户	
			$data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //变更日期
			$data ['ZHUANGTAI'] = '1'; //状态
							
			
			//散货暂存区信息表
			$this->_db->insert ( "H01DB012444", $data );
			return true;
		}
	}
	

	/**
	 * 更新散货暂存区信息
	 *
	 * @return bool
	 */
	function updateShzcqxx() {
	
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss'),BGZH FROM H01DB012444 WHERE QYBH = :QYBH AND CKBH = :CKBH AND FJZCQBH = :FJZCQBH AND CHSDCHK=:CHSDCHK FOR UPDATE WAIT 10";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'FJZCQBH' => $_POST ['FJZCQBH'],'CKBH' => $_POST['CKBH'],'CHSDCHK' => $_POST['CHSDCHK'] );
		$timestamp = $this->_db->fetchOne ( $sql, $bind );
		
		//时间戳已经变更
		

		if ($timestamp != $_POST ['BGRQ']) {
				return false;
		
		} else {
			$sql = "UPDATE  H01DB012444 SET " .  "FJZCQMCH = :FJZCQMCH," .  " BGRQ = SYSDATE," . " BGZH = :BGZH" . " WHERE QYBH = :QYBH AND CKBH=:CKBH AND FJZCQBH =:FJZCQBH AND CHSDCHK=:CHSDCHK";
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['FJZCQMCH'] = $_POST ['FJZCQMCH']; //散货暂存区名称
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			$bind ['CHSDCHK'] = $_POST ['CHSDCHK'];//传送带出口

			$this->_db->query ( $sql, $bind );
			
			return true;
		}
		
	}


	/**
	 * 获取仓库信息状态
	 *
	 * @param string $ckbh  仓库编号
	 * @return unknown
	 */
	function getCkzht($ckbh) {
		
		$sql = "SELECT CKZHT FROM H01DB012401 WHERE QYBH = :QYBH AND CKBH = :CKBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'CKBH' => $ckbh );
		$ckzht = $this->_db->fetchOne ( $sql, $bind );
		return $ckzht;
	}
	
	/**
	 * 散货暂存区信息锁定和解锁
	 * @param string $ckbh     仓库编号
	 * @param string $shzcqbh  散货暂存区编号
	 * @param string $chkzht   状态
	 * @param string $chsdchk  传送带出口
	 * @return unknown
	 */
	function updateStatus($shzcqbh, $chkzht ,$ckbh,$chsdchk) {
		
		$sql = "UPDATE H01DB012444 " . " SET ZHUANGTAI = :ZHUANGTAI" . " WHERE QYBH =:QYBH AND CKBH = :CKBH AND FJZCQBH =:FJZCQBH AND CHSDCHK=:CHSDCHK";
		$bind = array ('ZHUANGTAI' => $chkzht, 'QYBH' => $_SESSION ['auth']->qybh, 'FJZCQBH' => $shzcqbh,'CKBH'=>$ckbh,'CHSDCHK'=>$chsdchk);
		return $this->_db->query ( $sql, $bind );
	
	}

	
	/**
	 * 查找对应传送带出口的状态信息
	 * @param string $ckbh   仓库编号
	 * @param string $chsdchk  传送带出口的状态 1：可用；X：删除
	 * @return bool
	 */
	function getChkstatus($ckbh,$chsdchk){
		
			$sql = "SELECT ZHUANGTAI FROM H01DB012443 WHERE QYBH=:QYBH AND CKBH=:CKBH AND CHSDCHK=:CHSDCHK";
			$bind = array('QYBH' => $_SESSION ['auth']->qybh,'CKBH'=>$ckbh,'CHSDCHK'=>$chsdchk);
			$temp = $this->_db->fetchOne( $sql, $bind );
			if($temp == 1){
				return true; //传送带出口状态为可用
			}else{
				return false;//传送带出口状态为禁用
			}
	
	}
	
	
	
	
	
	
	
	

}




?>