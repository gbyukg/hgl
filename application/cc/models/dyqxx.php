<?php
class cc_models_dyqxx extends Common_Model_Base {
	
	
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
	 * 获取库区类型名称
	 *
	 * 
	 * @return unknown
	 */
	public function getKqlxList() {
		$sql = "SELECT ZIHAOMA,NEIRONG FROM H01DB012001" . " WHERE QYBH =:QYBH AND CHLID = 'KQLX'";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh );
		$kqlxList = $this->_db->fetchPairs ( $sql, $bind );
		$kqlxList [''] = '- - 请 选 择 - -';
		ksort ( $kqlxList );
		return $kqlxList;
	
	}
	
/**
	 * 得到库区信息列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ("", "ZHUANGTAI","CKBH","DYQBH", "DYQMCH","KQLXMCH","BGRQ","BGZHXM" ); //状态，待验区编号，
		

		//检索SQL
		$sql = "SELECT DECODE(ZHUANGTAI,'0','冻结','1','正常','X','禁用') AS ZHUANGTAI,CKMCH,DYQBH,DYQMCH,KQLXMCH,TO_CHAR(BGRQ,'YYYY-MM-DD'),BGZHXM ,CKBH
		      FROM H01VIEW012435 WHERE QYBH= :QYBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件  编号或名称
		if($filter['searchParams']['DYQBH']!=""){
			$sql .= " AND( CKBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(CKMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['DYQBH']);
		}
		
		if($filter['searchParams']['KQLX']!=""){
			$sql .= " AND( DYQBH LIKE '%' || :SEARCHKEYKQLX || '%'".
			        "      OR  lower(DYQMCH) LIKE '%' || :SEARCHKEYKQLX || '%')";
			$bind ['SEARCHKEYKQLX'] = strtolower($filter ["searchParams"]['KQLX']);
		}
		
				//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_DYQXX",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 必须添加所有主键
		$sql .= " ,CKBH,DYQBH";
		
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
	 * 取得库区信息
	 * @param string $ckbh	  仓库编号
	 * @param string &$kqbh  库区编号
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	
	function getDyqxx($dyqbh,$ckbh, $filter, $flg = 'current') {
		
		//检索SQL
		$fields = array ("", "ZHUANGTAI","CKBH","DYQBH", "DYQMCH","KQLXMCH","BGRQ","BGZHXM","CKBH" ); //状态，待验区编号，
		$sql_list = "SELECT  DYQROWID,LEAD(DYQROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",CKBH,DYQBH) AS NEXTROWID," . 
		" 						   LAG(DYQROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",CKBH,DYQBH) AS PREVROWID " . 
		"  ,CKBH,DYQBH" .
		 " FROM H01VIEW012435 " .
		" WHERE QYBH = :QYBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件 仓库
			//查找条件  编号或名称
		if($filter['searchParams']['DYQBH']!=""){
			$sql_list .= " AND( CKBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(CKMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['DYQBH']);
		}
		
		if($filter['searchParams']['KQLX']!=""){
			$sql_list .= " AND( DYQBH LIKE '%' || :SEARCHKEYKQLX || '%'".
			        "      OR  lower(DYQMCH) LIKE '%' || :SEARCHKEYKQLX || '%')";
			$bind ['SEARCHKEYKQLX'] = strtolower($filter ["searchParams"]['KQLX']);
		}
		
		$sql_list .= Common_Tool::createFilterSql("CC_DYQXX",$filter['filterParams'],$bind);
		
		$sql_single = "SELECT DECODE(ZHUANGTAI,'0','冻结','1','正常','X','禁用') AS ZHUANGTAI,CKMCH,DYQBH,DYQMCH,KQLX,KQLXMCH,ZCHZHXM,TO_CHAR(ZCHRQ,'YYYY-MM-DD'),BGZHXM,TO_CHAR(BGRQ,'YYYY-MM-DD hh24:mi:ss') AS BGRQ,CKBH ".
		 " FROM H01VIEW012435 " ;
		
		//当前				
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH = :QYBH AND CKBH = :CKBH AND DYQBH = :DYQBH";
		
		unset ( $bind ['SEARCHKEY'] );
		unset ( $bind ['SEARCHKEYKQLX'] );
		
		} else if ($flg == 'next') { //下一条		
			$sql_single .= "WHERE DYQROWID = (SELECT NEXTROWID FROM  (SELECT NEXTROWID ,CKBH,DYQBH FROM ( $sql_list ) WHERE CKBH = :CKBH AND DYQBH = :DYQBH))";
			                
		} else if ($flg == 'prev') { //前一条
			$sql_single .= "WHERE DYQROWID = (SELECT PREVROWID FROM  (SELECT PREVROWID ,CKBH,DYQBH FROM ( $sql_list ) WHERE CKBH = :CKBH AND DYQBH = :DYQBH))";
		}
		//绑定 区域编号 & 待验区编号 & 仓库编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DYQBH'] = $dyqbh;
		$bind ['CKBH'] = $ckbh;

		
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	
	
	
	/**
	 * 生成库区信息
	 *
	 * @return bool
	 */
	function insertDyqxx() {
		
		//判断是否待验区编号是否存在
		

		if ($this->getDyqxx ( $_POST ['DYQBH'] ) != FALSE) {
			return false;
		} else {
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['DYQBH'] = $_POST ['DYQBH']; //待验区编号
			$data ['DYQMCH'] = $_POST ['DYQMCH']; //待验区名称
			$data ['CKBH'] = $_POST ['CKBH']; //仓库编号
			
			$data ['KQLX'] = $_POST ['KQLX']; //库区类型
		    
		    $data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		    $data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		    $data ['BGZH'] = $_SESSION ['auth']->userId; //操作用户	
			$data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //变更日期
			$data ['ZHUANGTAI'] = '1'; //状态
							
			
			//待验区信息表
			$this->_db->insert ( "H01DB012435", $data );
			return true;
		}
	}
	

	/**
	 * 更新库区信息
	 *
	 * @return bool
	 */
	function updateDyqxx() {
	
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss'),BGZH FROM H01DB012435 WHERE QYBH = :QYBH AND CKBH = :CKBH AND DYQBH = :DYQBH  FOR UPDATE WAIT 10";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'DYQBH' => $_POST ['DYQBH'],'CKBH' => $_POST['CKBH'] );
		$timestamp = $this->_db->fetchOne ( $sql, $bind );
		
		//时间戳已经变更
		

		if ($timestamp != $_POST ['BGRQ']) {
				return false;
		
		} else {
			$sql = "UPDATE  H01DB012435 SET " . " CKBH = :CKBH," ." DYQBH = :DYQBH," . "DYQMCH = :DYQMCH," . "KQLX = :KQLX," . " BGRQ = SYSDATE," . " BGZH = :BGZH" . " WHERE QYBH = :QYBH AND CKBH=:CKBH AND DYQBH =:DYQBH";
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['CKBH'] = $_POST ['CKBH']; //仓库编号
			
			$bind ['DYQBH'] = $_POST ['DYQBH']; //库区编号
			$bind ['DYQMCH'] = $_POST ['DYQMCH']; //库区编号
			$bind ['KQLX'] = $_POST ['KQLX']; //库位名称
           // $data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //变更日期
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			

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
	 * 库区信息锁定和解锁
	 * @param string $ckbh  仓库编号
	 * @param string $kqbh  库区编号
	 * @param string $kqzht 状态
	 * @return unknown
	 */
	function updateStatus($dyqbh,$dyqzht,$ckbh) {
		
		$sql = "UPDATE H01DB012435 " . " SET ZHUANGTAI = :ZHUANGTAI" . " WHERE QYBH =:QYBH AND CKBH = :CKBH AND DYQBH =:DYQBH";
		$bind = array ('ZHUANGTAI' => $dyqzht, 'QYBH' => $_SESSION ['auth']->qybh, 'DYQBH' => $dyqbh,'CKBH'=>$ckbh);
		return $this->_db->query ( $sql, $bind );
	
	}

	
	/**
	 * 查找对应库区的状态信息
	 * @param string $ckbh   仓库编号
	 * @param string $kqzht  查找库区的状态 0：冻结；1：可用；X：删除
	 * @return bool
	 */
	function getkwstatus( $dyqbh, $kwzht ,$ckbh){
		if($kwzht == '0'){
			$sql = "SELECT COUNT(*) FROM H01DB012439 WHERE DYQBH =:DYQBH AND ZHUANGTAI =:KWZHT AND QYBH=:QYBH AND CKBH=:CKBH";
			$bind = array('DYQBH' => $dyqbh, 'KWZHT' => '1','QYBH' => $_SESSION ['auth']->qybh,'CKBH'=>$ckbh);
			$temp = $this->_db->fetchOne( $sql, $bind );
			if($temp == 0){
				return true;
			}else{
				return false;
			}
		} else {
			$sql = "SELECT COUNT(*) FROM H01DB012439 WHERE DYQBH =:DYQBH AND ZHUANGTAI !=:KWZHT AND QYBH=:QYBH AND CKBH=:CKBH";
			$bind = array('DYQBH' => $dyqbh, 'KWZHT' => 'X','QYBH' => $_SESSION ['auth']->qybh,'CKBH'=>$ckbh);
			$temp = $this->_db->fetchOne( $sql, $bind );
			if($temp == 0){
				return true;
			}else{
				return false;
			}
		}
	}
	
	
	
	
	
	
	
	

}




?>