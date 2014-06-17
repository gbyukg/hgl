<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   发货暂存区信息(fhzcqxx)
 * 作成者：handong
 * 作成日：2011/05/24
 * 更新履历：
 *********************************/
 
class cc_models_fhzcqxx extends Common_Model_Base {
	
	/**
	 * 得到发货暂存区列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter){
		//排序用字段名
		$fields=array("","ZHUANGTAI","CKBH","FHQBH","CHHKBH","FHZCQBH","FHZCQLB","BGRQ");
		//检索SQL
		$sql="SELECT DECODE(ZHUANGTAI,'1','正常','X','禁用') AS FHZCQZHT,CKMCH,FHQMCH,CHHKMCH,FHZCQMCH,CKBH,FHQBH,CHHKBH,FHZCQBH," .
		       " TO_CHAR(BGRQ,'YYYY-MM-DD hh24:mi:ss') AS BGRQ,BGZH,DECODE(FHZCQLB,'1','近距离','2','中距离','3','远距离') AS FHZCQLB" .
			     " FROM H01VIEW012446 " .
		     " WHERE QYBH=:QYBH";
		
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;

	    
      //查找条件  编号或名称
		if($filter['searchParams']['CKBH']!=""){
			$sql .= " AND( CKBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(CKMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['CKBH']);
		}
		
		if($filter['searchParams']['FHQBH']!=""){
			$sql .= " AND( FHQBH LIKE '%' || :SEARCHKEYFHQBH || '%'".
			        "      OR  lower(FHQMCH) LIKE '%' || :SEARCHKEYFHQBH || '%')";
			$bind ['SEARCHKEYFHQBH'] = strtolower($filter ["searchParams"]['FHQBH']);
		}
	    if($filter['searchParams']['CHHKBH']!=""){
			$sql .= " AND( CHHKBH LIKE '%' || :SEARCHKEYCHHKBH || '%'".
			        "      OR  lower(CHHKMCH) LIKE '%' || :SEARCHKEYCHHKBH || '%')";
			$bind ['SEARCHKEYCHHKBH'] = strtolower($filter ["searchParams"]['CHHKBH']);
		}
	    if($filter['searchParams']['FHZCQBH']!=""){
			$sql .= " AND( FHZCQBH LIKE '%' || :SEARCHKEYFHZCQBH || '%'".
			        "      OR  lower(FHZCQMCH) LIKE '%' || :SEARCHKEYFHZCQBH || '%')";
			$bind ['SEARCHKEYFHZCQBH'] = strtolower($filter ["searchParams"]['FHZCQBH']);
		}
	   //自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_FHZCQXX",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 项目最后添加主键
		$sql .=" ,CKBH,CHHKBH,FHZCQBH";
		
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
	 * 取得发货出货口信息
	 * @param string $ckbh	  仓库编号
	 * @param string &$fhqbh  发货区编号
	 * @param string &$chhkbh  出货口编号
	 * @param string &$fhzcqbh  发货暂存区
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	
	function getFhzcqxx($ckbh,$fhqbh,$chhkbh,$fhzcqbh, $filter, $flg = 'current') {
		
		//检索SQL
		$fields=array("","ZHUANGTAI","CKBH","FHQBH","CHHKBH","FHZCQBH","FHZCQLB","BGRQ");
		$sql_list = "SELECT  ZCQROWID,LEAD(ZCQROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",CKBH,FHQBH,CHHKBH,FHZCQBH) AS NEXTROWID," . 
		" 						   LAG(ZCQROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",CKBH,FHQBH,CHHKBH,FHZCQBH) AS PREVROWID " . 
		"  ,CKBH,CHHKBH,FHQBH,FHZCQBH " .
		 " FROM H01VIEW012446 " .
		" WHERE QYBH = :QYBH ";

		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
	//查找条件  编号或名称
		if($filter['searchParams']['CKBH']!=""){
			$sql_list .= " AND( CKBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(CKMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['CKBH']);
		}
		
		if($filter['searchParams']['FHQBH']!=""){
			$sql_list .= " AND( FHQBH LIKE '%' || :SEARCHKEYFHQBH || '%'".
			        "      OR  lower(FHQMCH) LIKE '%' || :SEARCHKEYFHQBH || '%')";
			$bind ['SEARCHKEYFHQBH'] = strtolower($filter ["searchParams"]['FHQBH']);
		}
	    if($filter['searchParams']['CHHKBH']!=""){
			$sql_list .= " AND( CHHKBH LIKE '%' || :SEARCHKEYCHHKBH || '%'".
			        "      OR  lower(CHHKMCH) LIKE '%' || :SEARCHKEYCHHKBH || '%')";
			$bind ['SEARCHKEYCHHKBH'] = strtolower($filter ["searchParams"]['CHHKBH']);
		}
	    if($filter['searchParams']['FHZCQBH']!=""){
			$sql_list .= " AND( FHZCQBH LIKE '%' || :SEARCHKEYFHZCQBH || '%'".
			        "      OR  lower(FHZCQMCH) LIKE '%' || :SEARCHKEYFHZCQBH || '%')";
			$bind ['SEARCHKEYFHZCQBH'] = strtolower($filter ["searchParams"]['FHZCQBH']);
		}
		 //自动生成精确查询用Sql
		$sql_list .= Common_Tool::createFilterSql("CC_FHZCQXX",$filter['filterParams'],$bind);
		
		$sql_single = " SELECT DECODE(ZHUANGTAI,'1','正常','X','禁用') AS FHZCQZHT,CKMCH,FHQMCH,CHHKMCH,FHZCQBH,FHZCQMCH,FHZCQLB,CKBH,FHQBH,CHHKBH,".
		         " TO_CHAR(BGRQ,'YYYY-MM-DD hh24:mi:ss') AS BGRQ,BGZH,FHZCQLB" .
			     " FROM H01VIEW012446 " ;
		
		//当前				
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH = :QYBH AND CKBH = :CKBH AND FHQBH = :FHQBH  AND CHHKBH = :CHHKBH AND FHZCQBH = :FHZCQBH";
//			unset ( $bind ['SEARCHKEY'] );
//			unset ( $bind ['SEARCHKEYKQBH'] );
		
		} else if ($flg == 'next') { //下一条		
			$sql_single .= " WHERE ZCQROWID = (SELECT NEXTROWID FROM  (SELECT NEXTROWID,CKBH,FHQBH,CHHKBH,FHZCQBH FROM ( $sql_list ) WHERE CKBH= :CKBH AND FHQBH = :FHQBH  AND CHHKBH = :CHHKBH AND FHZCQBH = :FHZCQBH))";
		} else if ($flg == 'prev') { //前一条
			$sql_single .= " WHERE ZCQROWID = (SELECT PREVROWID FROM  (SELECT PREVROWID,CKBH,FHQBH,CHHKBH,FHZCQBH FROM ( $sql_list ) WHERE CKBH= :CKBH AND FHQBH = :FHQBH  AND CHHKBH = :CHHKBH AND FHZCQBH = :FHZCQBH))";
		}
		//绑定 区域编号 & 仓库编号 & 发货区编号&出货口编号&发货暂存区编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CKBH'] = $ckbh;
		$bind ['FHQBH'] = $fhqbh;
		$bind ['CHHKBH'] = $chhkbh;
		$bind ['FHZCQBH'] = $fhzcqbh;
		
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	/**
	 * 生成发货暂存区信息
	 *
	 * @return bool
	 */
	function insertFhzcqxx() {
		
		//判断暂存区编号是否存在
		

		if ($this->getFhzcqxx ($_POST ['CKBH'],$_POST['FHQBH'],$_POST ['CHHKBH'],$_POST['FHZCQBH'] ) != FALSE) {
			return false;
		} else {
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['CKBH'] = $_POST['CKBH']; //仓库编号
			$data ['FHZCQBH']= $_POST['FHZCQBH'];//发货暂存区编号
			$data ['FHZCQMCH']=$_POST['FHZCQMCH'];//发货暂存区名称
			$data ['CHHKBH'] =$_POST['CHHKBH']; //出货口
			$data ['ZHUANGTAI'] = '1'; //状态
            $data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
            $data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期	
            $data ['BGZH'] = $_SESSION ['auth']->userId; //变更者	
            $data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //变更日期		
			$data ['FHZCQLB'] = $_POST ['FHZCQLB'];
			//保存发货出货口信息
			$this->_db->insert ( "H01DB012446", $data );
	         return  true;
		}
	}
	
	/**
	 * 更新库区信息
	 *
	 * @return bool
	 */
	function updateFhzcqxx() {
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss'),BGZH FROM H01DB012446 WHERE QYBH = :QYBH AND CKBH= :CKBH AND CHHKBH =:CHHKBH AND FHZCQBH =:FHZCQBH FOR UPDATE WAIT 10";
		$bind1 = array ('QYBH' => $_SESSION ['auth']->qybh,'CKBH' => $_POST['CKBH'],'CHHKBH' => $_POST ['CHHKBH'],'FHZCQBH' => $_POST ['FHZCQBH']);
		$timestamp = $this->_db->fetchOne ( $sql, $bind1 );
		
		//时间戳已经变更
		
		if ($timestamp != $_POST ['BGRQ']) {
			return false;
		} else {
			$sql = "UPDATE  H01DB012446 SET FHZCQMCH = :FHZCQMCH,FHZCQLB = :FHZCQLB, BGRQ = sysdate, BGZH = :BGZH WHERE QYBH = :QYBH AND CKBH= :CKBH AND CHHKBH =:CHHKBH AND FHZCQBH =:FHZCQBH";
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['CKBH'] = $_POST ['CKBH']; //仓库编号
			$bind ['CHHKBH'] = $_POST ['CHHKBH']; //出货口编号
			$bind ['FHZCQBH'] = $_POST ['FHZCQBH']; //发货暂存区编号
			$bind ['FHZCQMCH'] = $_POST ['FHZCQMCH'];//发货暂存区名称
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			$bind ['FHZCQLB'] = $_POST ['FHZCQLB'];//类别

			$this->_db->query ( $sql, $bind );
			return true;
		}
        
		
	}
	
   /**
	 * 获取发货区
	 *
	 * 
	 * @return unknown
	 */
	public function getFhqList($ckbh) {
		$sql = "SELECT CKBH, FHQBH,FHQMCH FROM H01DB012422" . " WHERE QYBH =:QYBH AND CKBH = :CKBH AND FHQZHT ='1' ". " ORDER BY FHQBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh );
		$bind['CKBH'] = $ckbh;
		
		return $this->_db->fetchAll ( $sql, $bind );
	
	}
	
	/**
	 * 获取出货口
	 *
	 */
	public function getChhkList($ckbh,$fhqbh){
		$sql = "SELECT CKBH, FHQBH,CHHKMCH,CHHKBH FROM H01DB012445" . " WHERE QYBH =:QYBH AND CKBH = :CKBH AND FHQBH = :FHQBH AND ZHUANGTAI = '1'" ;
		$bind['QYBH'] = $_SESSION['auth']->qybh;
		$bind['CKBH'] = $ckbh;
		$bind['FHQBH'] = $fhqbh;
		return $this->_db->fetchRow ( $sql, $bind );
	}
    /**
	 * 发货出货口信息禁用和启用
	 * @param string $ckbh  仓库编号
	 * @param string $fhqbh  发货区编号
	 * @param string $chhkbh 出货口编号
	 * @param string $zhuangtai 状态
	 * @return unknown
	 */
	function updateStatus($ckbh,$chhkbh,$fhzcqbh,$zhuangtai) {
		
		$sql = "UPDATE H01DB012446 " . " SET ZHUANGTAI = :ZHUANGTAI" . " WHERE QYBH =:QYBH AND CKBH =:CKBH  AND CHHKBH = :CHHKBH AND FHZCQBH = :FHZCQBH";
		$bind = array ('ZHUANGTAI' => $zhuangtai, 'QYBH' => $_SESSION ['auth']->qybh, 'CKBH' => $ckbh,  'CHHKBH' => $chhkbh,'FHZCQBH' => $fhzcqbh );
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
	/**
	 * 获取发货区信息状态
	 *@param string $ckbh  仓库编号
	 * @param string $fhqbh  发货区编号
	 * @return unknown
	 */
	function getFhqzht($ckbh,$fhq){
		$sql = "SELECT FHQZHT FROM H01DB012422 WHERE QYBH = :QYBH AND CKBH = :CKBH AND FHQBH = :FHQBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'CKBH' => $ckbh, 'FHQBH' => $fhq );
		$fhqzht = $this->_db->fetchOne($sql,$bind);
		return $fhqzht;
	}
	/**
	 * 获取出货口信息状态
	 *@param string $ckbh  仓库编号
	 * @param string $fhqbh  发货区编号
	 * @param string $chhkbh  出货口编号
	 * @return unknown
	 */
	function getFhchhkzht($ckbh,$fhqbh,$chhkbh){
		$sql = "SELECT ZHUANGTAI FROM H01DB012445 WHERE QYBH = :QYBH AND CKBH = :CKBH AND FHQBH = :FHQBH AND CHHKBH = :CHHKBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'CKBH' => $ckbh, 'FHQBH' => $fhqbh, 'CHHKBH' => $chhkbh );
		$chhkzht = $this->_db->fetchOne($sql,$bind);
		return $chhkzht;
	}
		/**
	 * 获取出货口信息状态
	 *@param string $ckbh  仓库编号
	 * @param string $fhqbh  发货区编号
	 * @param string $fhzcqbh 发货暂存区编号
	 * @return unknown
	 */
	function getFhzcqzht($ckbh,$chhkbh,$fhzcqbh){
		$sql = "SELECT ZHUANGTAI FROM H01DB012446 WHERE QYBH = :QYBH AND CHHKBH = :CHHKBH AND FHZCQBH = : FHZCQBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'CKBH' => $ckbh, 'CHHKBH' => $chhkbh, 'FHZCQBH'=> $fhzcqbh );
		$fhzcqzht = $this->_db->fetchOne($sql,$bind);
		return $fhzcqzht;
	}
}        
?>