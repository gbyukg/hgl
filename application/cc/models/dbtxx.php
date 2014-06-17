<?php
class cc_models_dbtxx extends Common_Model_Base {
	
	
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
	 * 得到打包台信息列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ("", "ZHUANGTAI",  "CKBH","DBTBH","DBTMCH"); //状态，打包台编号，
		

		//检索SQL
		$sql = "SELECT DECODE(ZHUANGTAI,'1','启用','2','暂停','X','禁用') AS ZHUANGTAI,CKMCH,DBTBH,DBTMCH,CKBH
		      FROM H01VIEW012442 WHERE QYBH= :QYBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件  编号或名称
		if($filter['searchParams']['CKBH']!=""){
			$sql .= " AND( CKBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(CKMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['CKBH']);
		}
		
		if($filter['searchParams']['DBTBH']!=""){
			$sql .= " AND( DBTBH LIKE '%' || :SEARCHKEYKQLX || '%'".
			        "      OR  lower(DBTMCH) LIKE '%' || :SEARCHKEYKQLX || '%')";
			$bind ['SEARCHKEYKQLX'] = strtolower($filter ["searchParams"]['DBTBH']);
		}
		
				//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_DBTXX",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 必须添加所有主键
		$sql .= " ,CKBH,DBTBH";
		
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
	 * 取得打包台信息
	 * @param string $ckbh	  仓库编号
	 * @param string &$dbtbh 打包台编号
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	
	function getDbtxx($dbtbh,$ckbh, $filter, $flg = 'current') {
		
		//检索SQL
		$fields = array ("", "ZHUANGTAI", "CKBH","DBTBH","DBTMCH"); //状态，打包台编号，
		$sql_list = "SELECT  DBTROWID,LEAD(DBTROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] ." ,CKBH,DBTBH) AS NEXTROWID," . 
		" 						   LAG(DBTROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . " ,CKBH,DBTBH) AS PREVROWID " . 
		"  ,CKBH,DBTBH" .
		 " FROM H01VIEW012442 " .
		" WHERE QYBH = :QYBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
	
		//查找条件 仓库 编号或名称
		if($filter['searchParams']['CKBH']!=""){
			$sql_list .= " AND( CKBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(CKMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['CKBH']);
		}
		//查找条件 打包台 编号或名称
		if($filter['searchParams']['DBTBH']!=""){
			$sql_list .= " AND( DBTBH LIKE '%' || :SEARCHKEYKQLX || '%'".
			        "      OR  lower(DBTMCH) LIKE '%' || :SEARCHKEYKQLX || '%')";
			$bind ['SEARCHKEYKQLX'] = strtolower($filter ["searchParams"]['DBTBH']);
		}
		
		$sql_list .= Common_Tool::createFilterSql("CC_DBTXX",$filter['filterParams'],$bind);
		
		$sql_single = "SELECT DECODE(ZHUANGTAI,'1','启用','2','暂停','X','禁用') AS ZHUANGTAI,CKMCH,DBTBH,DBTMCH,CKBH,to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') AS BGRQ
		      FROM H01VIEW012442";
		
		//当前				
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH = :QYBH AND CKBH = :CKBH AND DBTBH = :DBTBH";
		
		unset ( $bind ['SEARCHKEY'] );
		unset ( $bind ['SEARCHKEYKQLX'] );
		
		} else if ($flg == 'next') { //下一条		
			$sql_single .= " WHERE DBTROWID = (SELECT NEXTROWID FROM  (SELECT NEXTROWID ,CKBH,DBTBH FROM ( $sql_list ) WHERE CKBH = :CKBH AND DBTBH = :DBTBH))";
			                
		} else if ($flg == 'prev') { //前一条
			$sql_single .= " WHERE DBTROWID = (SELECT PREVROWID FROM  (SELECT PREVROWID ,CKBH,DBTBH FROM ( $sql_list ) WHERE CKBH = :CKBH AND DBTBH = :DBTBH))";
		}
		//绑定 区域编号 & 打包台编号 & 仓库编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DBTBH'] = $dbtbh;
		$bind ['CKBH'] = $ckbh;

		
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	
	
	
	/**
	 * 生成打包台信息
	 *
	 * @return bool
	 */
	function insertDbtxx() {
		
		//判断打包台编号是否存在
		

		if ($this->getDbtxx ( $_POST ['DBTBH'],$_POST['CKBH'] ) != FALSE) {
			return false;
		} else {
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['CKBH'] = $_POST ['CKBH']; //仓库编号
			$data ['DBTBH'] = $_POST ['DBTBH']; //打包台编号
			$data ['DBTMCH'] = $_POST ['DBTMCH']; //打包台名称
			$data ['DLZHT'] = '0'; //登陆状态
			$data ['DLZH'] = $_SESSION ['auth']->userId; //作成者
		    $data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		    $data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		    $data ['BGZH'] = $_SESSION ['auth']->userId; //操作用户	
			$data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //变更日期
			$data ['ZHUANGTAI'] = '1'; //状态
							
			
			//打包台信息表
			$this->_db->insert ( "H01DB012442", $data );
			return true;
		}
	}
	

	/**
	 * 更新打包台信息
	 *
	 * @return bool
	 */
	function updateDbtxx() {
	
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss'),BGZH FROM H01DB012442 WHERE QYBH = :QYBH AND CKBH = :CKBH AND DBTBH = :DBTBH  FOR UPDATE WAIT 10";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'DBTBH' => $_POST ['DBTBH'],'CKBH' => $_POST['CKBH'] );
		$timestamp = $this->_db->fetchOne ( $sql, $bind );
		
		//时间戳已经变更
		

		if ($timestamp != $_POST ['BGRQ']) {
				return false;
		
		} else {
			$sql = "UPDATE  H01DB012442 SET " . "DBTMCH = :DBTMCH," . " BGRQ = SYSDATE," . " BGZH = :BGZH" . " WHERE QYBH = :QYBH AND CKBH=:CKBH AND DBTBH =:DBTBH";
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['DBTMCH'] = $_POST ['DBTMCH']; //打名台名称
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
	 * 打包台信息锁定和解锁
	 * @param string $ckbh  仓库编号
	 * @param string $dbtbh  打包台编号
	 * @param string $dbtzht 状态
	 * @return unknown
	 */
	function updateStatus($dbtbh,$dbtzht,$ckbh) {
		
		$sql = "UPDATE H01DB012442 " . " SET ZHUANGTAI = :ZHUANGTAI" . " WHERE QYBH =:QYBH AND CKBH = :CKBH AND DBTBH =:DBTBH";
		$bind = array ('ZHUANGTAI' => $dbtzht, 'QYBH' => $_SESSION ['auth']->qybh, 'DBTBH' => $dbtbh,'CKBH'=>$ckbh);
		return $this->_db->query ( $sql, $bind );
	
	}

	
	/**
	 * 查找对应库位的状态信息
	 * @param string $ckbh   仓库编号
	 * @param string $kqzht  查找打包台的状态 1：可用；2：冻结；X：删除
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