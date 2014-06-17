<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   退货区信息(thqxx)
 * 作成者：handong
 * 作成日：2011/05/10
 * 更新履历：
 *********************************/
 
class cc_models_thqxx extends Common_Model_Base {
	
	/**
	 * 得到退货列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter){
		//排序用字段名
		$fields=array("","ZHT","CKBH","THQBH","THQMCH","KQLX","BGRQ","BGZHXM");
		//检索SQL
		$sql="SELECT DECODE(ZHT,'1','正常','X','禁用') AS ZHT,CKMCH,THQBH,THQMCH,KQLXMCH,TO_CHAR(BGRQ,'YYYY-MM-DD'),BGZHXM,CKBH" .
			     " FROM H01VIEW012436" .
		     " WHERE QYBH=:QYBH";
		
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;

	    
      //查找条件  编号或名称
		if($filter['searchParams']['CKBH']!=""){
			$sql .= " AND( CKBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(CKMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['CKBH']);
		}
		
		if($filter['searchParams']['THQBH']!=""){
			$sql .= " AND( THQBH LIKE '%' || :SEARCHKEYTHQBH || '%'".
			        "      OR  lower(THQMCH) LIKE '%' || :SEARCHKEYTHQBH || '%')";
			$bind ['SEARCHKEYTHQBH'] = strtolower($filter ["searchParams"]['THQBH']);
		}
	   //自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_THQXX",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 项目最后添加主键
		$sql .=" ,CKBH,THQBH";
		
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
	 * 取得退货区信息
	 * @param string $ckbh	  仓库编号
	 * @param string &$thqbh  退货区编号
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	
	function getThqxx($ckbh,$thqbh, $filter, $flg = 'current') {
		
		//检索SQL
		$fields=array("","ZHT","CKBH","THQBH","THQMCH","KQLX","BGRQ","BGZH"); //状态，仓库，库区编号
			$sql_list = "SELECT  THQROWID,LEAD(THQROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",CKBH,THQBH) AS NEXTROWID," . 
		" 						   LAG(THQROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",CKBH,THQBH) AS PREVROWID " . 
		"  ,CKBH, THQBH " .
		 " FROM H01VIEW012436 " .
		" WHERE QYBH = :QYBH ";

		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
	 //查找条件  编号或名称
		if($filter['searchParams']['CKBH']!=""){
			$sql_list .= " AND( CKBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(CKMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['CKBH']);
		}
		
		if($filter['searchParams']['THQBH']!=""){
			$sql_list .= " AND( THQBH LIKE '%' || :SEARCHKEYTHQBH || '%'".
			        "      OR  lower(THQMCH) LIKE '%' || :SEARCHKEYTHQBH || '%')";
			$bind ['SEARCHKEYTHQBH'] = strtolower($filter ["searchParams"]['THQBH']);
		}
		
		$sql_list .= Common_Tool::createFilterSql("CC_THQXX",$filter['filterParams'],$bind);
		
		$sql_single = " SELECT DECODE(ZHT,'1','正常','X','禁用') AS ZHT,CKMCH,THQBH,THQMCH,KQLXMCH,".
		 " TO_CHAR(BGRQ,'YYYY-MM-DD hh24:mi:ss') AS BGRQ,BGZH,CKBH,KQLX " .
		 " FROM H01VIEW012436 " ;
		
		//当前				
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH = :QYBH AND CKBH = :CKBH AND THQBH = :THQBH ";
//			unset ( $bind ['SEARCHKEY'] );
//			unset ( $bind ['SEARCHKEYKQBH'] );
		
		} else if ($flg == 'next') { //下一条		
			$sql_single .= " WHERE THQROWID = (SELECT NEXTROWID FROM  (SELECT NEXTROWID,CKBH,THQBH FROM ( $sql_list ) WHERE CKBH= :CKBH AND THQBH = :THQBH))";
		} else if ($flg == 'prev') { //前一条
			$sql_single .= " WHERE THQROWID = (SELECT PREVROWID FROM  (SELECT PREVROWID,CKBH,THQBH FROM ( $sql_list ) WHERE CKBH= :CKBH AND THQBH = :THQBH))";
		}
		//绑定 区域编号 & 仓库编号 & 退货区编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CKBH'] = $ckbh;
		$bind ['THQBH'] = $thqbh;
		
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	/**
	 * 生成退货区信息
	 *
	 * @return bool
	 */
	function insertThqxx() {
		
		//判断是否退货区编号是否存在
		

		if ($this->getThqxx ( $_POST ['CKBH'], $_POST ['THQBH'] ) != FALSE) {
			return false;
		} else {
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['THQBH'] = $_POST ['THQBH']; //退货区编号
			$data ['CKBH'] = $_POST ['CKBH']; //仓库编号
			$data ['THQMCH'] = $_POST ['THQMCH']; //退货区名称	
			$data ['KQLX'] = $_POST ['KQLX']; //类型
            $data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
            $data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期	
            $data ['BGZH'] = $_SESSION ['auth']->userId; //变更者	
            $data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //变更日期		
			$data ['ZHT'] = '1'; //状态
			
			//保存退货区信息
			$this->_db->insert ( "H01DB012436", $data );
	         return  true;
		}
	}
	
	/**
	 * 更新库区信息
	 *
	 * @return bool
	 */
	function updateThqxx() {
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss'),BGZH FROM H01DB012436 WHERE QYBH = :QYBH AND CKBH= :CKBH AND THQBH = :THQBH FOR UPDATE WAIT 10 ";
		$bind1 = array ('QYBH' => $_SESSION ['auth']->qybh,'CKBH' => $_POST['CKBH'],'THQBH' => $_POST ['THQBH'] );
		$timestamp = $this->_db->fetchOne ( $sql, $bind1 );
		
		//时间戳已经变更
		
		if ($timestamp != $_POST ['BGRQ']) {
			return false;
		} else {
			$sql = "UPDATE  H01DB012436 SET " . " QYBH = :QYBH," . " CKBH = :CKBH," . " THQBH = :THQBH," . "THQMCH = :THQMCH," .  "KQLX = :KQLX," . " BGRQ = sysdate," . " BGZH = :BGZH" . " WHERE QYBH = :QYBH AND CKBH =:CKBH AND THQBH = :THQBH";
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['CKBH'] = $_POST ['CKBH']; //仓库编号
			$bind ['THQBH'] = $_POST ['THQBH']; //退货区编号
			$bind ['THQMCH'] = $_POST ['THQMCH']; //退货区名称
			$bind ['KQLX'] = $_POST ['KQLX']; //库区类型
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			

			$this->_db->query ( $sql, $bind );
			return true;
		}
        
		
	}
	
   /**
	 * 获取退货区区类型编号
	 *
	 * 
	 * @return unknown
	 */
	public function getKqblxList() {
		$sql = "SELECT ZIHAOMA,NEIRONG FROM H01DB012001 WHERE QYBH =:QYBH AND CHLID = 'KQLX'";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh );
		$kqblxList = $this->_db->fetchPairs ( $sql, $bind );
		$kqblxList [''] = '- - 请 选 择 - -';
		ksort ( $kqblxList );
		return $kqblxList;
	
	}
	
/**
	 * 退货区信息禁用和启用
	 * @param string $ckbh  仓库编号
	 * @param string $thqbh  退货区编号
	 * @param string $zht 状态
	 * @return unknown
	 */
	function updateStatus($ckbh, $thqbh, $zht) {
		
		$sql = "UPDATE H01DB012436 " . " SET ZHT = :ZHT" . " WHERE QYBH =:QYBH AND CKBH =:CKBH AND THQBH = :THQBH ";
		$bind = array ('ZHT' => $zht, 'QYBH' => $_SESSION ['auth']->qybh, 'CKBH' => $ckbh, 'THQBH' => $thqbh );
		return $this->_db->query ( $sql, $bind );
	
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
	
}        
?>