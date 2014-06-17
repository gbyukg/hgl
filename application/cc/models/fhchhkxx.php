<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   发货出货口信息(fhchhkxx)
 * 作成者：handong
 * 作成日：2011/05/17
 * 更新履历：
 *********************************/
 
class cc_models_fhchhkxx extends Common_Model_Base {
	
	/**
	 * 得到出货口列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter){
		//排序用字段名
		$fields=array("","ZHUANGTAI","CKBH","FHQBH","CHHKBH","BGRQ");
		//检索SQL
		$sql="SELECT DECODE(ZHUANGTAI,'1','正常','X','禁用') AS FHCHHKZHT,CKMCH,FHQMCH,CHHKMCH,CKBH,FHQBH,CHHKBH," .
		       " TO_CHAR(BGRQ,'YYYY-MM-DD hh24:mi:ss') AS BGRQ,BGZH,BGZHXM,ZCHZH,ZCHZHXM,ZCHRQ " .
			     " FROM H01VIEW012445 " .
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
	   //自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_FHCHHKXX",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 项目最后添加主键
		$sql .=" ,CKBH,FHQBH,CHHKBH";
		
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
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	
	function getFhchhkxx($ckbh,$fhqbh,$chhkbh, $filter, $flg = 'current') {
		
		//检索SQL
		$fields=array("","ZHUANGTAI","CKBH","FHQBH","CHHKBH","BGRQ");
					$sql_list = "SELECT  CHHKROWID,LEAD(CHHKROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",CKBH,FHQBH,CHHKBH) AS NEXTROWID," . 
		" 						   LAG(CHHKROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",CKBH,FHQBH,CHHKBH) AS PREVROWID " . 
		"  ,CKBH,FHQBH,CHHKBH " .
		 " FROM H01VIEW012445 " .
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
		 //自动生成精确查询用Sql
		$sql_list .= Common_Tool::createFilterSql("CC_FHCHHKXX",$filter['filterParams'],$bind);
		
		$sql_single = " SELECT DECODE(ZHUANGTAI,'1','正常','X','禁用') AS FHCHHKZHT,CKMCH,FHQMCH,CHHKMCH,CKBH,FHQBH,CHHKBH,".
		         " TO_CHAR(BGRQ,'YYYY-MM-DD hh24:mi:ss') AS BGRQ,BGZH,BGZHXM " .
			     " FROM H01VIEW012445 " ;
		
		//当前				
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH = :QYBH AND CKBH = :CKBH AND FHQBH = :FHQBH  AND CHHKBH = :CHHKBH";
//			unset ( $bind ['SEARCHKEY'] );
//			unset ( $bind ['SEARCHKEYKQBH'] );
		
		} else if ($flg == 'next') { //下一条		
			$sql_single .= " WHERE CHHKROWID = (SELECT NEXTROWID FROM  (SELECT NEXTROWID,CKBH,FHQBH,CHHKBH FROM ( $sql_list ) WHERE CKBH= :CKBH AND FHQBH = :FHQBH  AND CHHKBH = :CHHKBH))";
		} else if ($flg == 'prev') { //前一条
			$sql_single .= " WHERE CHHKROWID = (SELECT PREVROWID FROM  (SELECT PREVROWID,CKBH,FHQBH,CHHKBH FROM ( $sql_list ) WHERE CKBH= :CKBH AND FHQBH = :FHQBH  AND CHHKBH = :CHHKBH))";
		}
		//绑定 区域编号 & 仓库编号 & 发货区编号&出货口编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CKBH'] = $ckbh;
		$bind ['FHQBH'] = $fhqbh;
		$bind ['CHHKBH'] = $chhkbh;
		
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	/**
	 * 生成发货出货口信息
	 *
	 * @return bool
	 */
	function insertFhchhkxx() {
		
		//判断出货口是否已存在
		

		if ($this->getChhkxx ( $_POST ['CKBH'], $_POST ['FHQ']) != FALSE) {
			return false;
		} else {
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['CHHKBH'] = $_POST ['CHHKBH']; //出货口编号
			$data ['CKBH'] = $_POST ['CKBH']; //仓库编号
			$data ['CHHKMCH'] = $_POST ['CHHKMCH']; //出货口名称	
			$data ['FHQBH'] = $_POST ['FHQ']; //发货区
            $data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
            $data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期	
            $data ['BGZH'] = $_SESSION ['auth']->userId; //变更者	
            $data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //变更日期		
			$data ['ZHUANGTAI'] = '1'; //状态
			
			//保存发货出货口信息
			$this->_db->insert ( "H01DB012445", $data );
	         return  true;
		}
	}
	
	/**
	 * 更新库区信息
	 *
	 * @return bool
	 */
	function updateFhchhkxx() {
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss'),BGZH FROM H01DB012445 WHERE QYBH = :QYBH AND CKBH= :CKBH AND FHQBH = :FHQBH AND CHHKBH =:CHHKBH FOR UPDATE WAIT 10";
		$bind1 = array ('QYBH' => $_SESSION ['auth']->qybh,'CKBH' => $_POST['CKBH'],'FHQBH' => $_POST ['FHQBH'] ,'CHHKBH' => $_POST ['CHHKBH']);
		$timestamp = $this->_db->fetchOne ( $sql, $bind1 );
		
		//时间戳已经变更
		
		if ($timestamp != $_POST ['BGRQ']) {
			return false;
		} else {
			$sql = "UPDATE  H01DB012445 SET CHHKMCH = :CHHKMCH, BGRQ = sysdate, BGZH = :BGZH WHERE QYBH = :QYBH AND CKBH= :CKBH AND FHQBH = :FHQBH AND CHHKBH =:CHHKBH";
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['CKBH'] = $_POST ['CKBH']; //仓库编号
			$bind ['FHQBH'] = $_POST ['FHQBH']; //发货区
			$bind ['CHHKMCH'] = $_POST ['CHHKMCH']; //出货口名称
			$bind ['CHHKBH'] = $_POST ['CHHKBH']; //出货口编号
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			

			$this->_db->query ( $sql, $bind );
			return true;
		}
        
		
	}
	
   /**
	 * 判断出货口是否已存在
	 *
	 * 
	 * @return unknown
	 */
	public function getChhkxx($ckbh,$fhq){
		$sql = "SELECT CHHKBH FROM H01DB012445 WHERE QYBH = :QYBH AND CKBH = :CKBH AND FHQBH = :FHQBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh );
		$bind['CKBH'] = $ckbh;
		$bind['FHQBH'] = $fhq;
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
	public function  getChhkbh($chhkbh){
		$sql = "SELECT CHHKBH FROM H01DB012445 WHERE QYBH = :QYBH AND CHHKBH = :CHHKBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh );
		$bind['CHHKBH'] = $chhkbh;
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
   /**
	 * 获取发货区类型编号
	 *
	 * 
	 * @return unknown
	 */

	public function getFhqList($ckbh) {
		$sql = "SELECT CKBH, FHQBH,FHQMCH FROM H01DB012422" . " WHERE QYBH =:QYBH AND CKBH = :CKBH AND FHQZHT = '1' ". " ORDER BY FHQBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh );
		$bind['CKBH'] = $ckbh;
		
		return $this->_db->fetchAll ( $sql, $bind );
	
	}
	
	
/**
	 * 发货出货口信息禁用和启用
	 * @param string $ckbh  仓库编号
	 * @param string $fhqbh  发货区编号
	 * @param string $chhkbh 出货口编号
	 * @param string $zhuangtai 状态
	 * @return unknown
	 */
	function updateStatus($ckbh, $fhqbh,$chhkbh, $zhuangtai) {
		
		$sql = "UPDATE H01DB012445 " . " SET ZHUANGTAI = :ZHUANGTAI" . " WHERE QYBH =:QYBH AND CKBH =:CKBH AND FHQBH = :FHQBH AND CHHKBH = :CHHKBH";
		$bind = array ('ZHUANGTAI' => $zhuangtai, 'QYBH' => $_SESSION ['auth']->qybh, 'CKBH' => $ckbh, 'FHQBH' => $fhqbh, 'CHHKBH' => $chhkbh );
		return $this->_db->query ( $sql, $bind );
	
	}
	
	/**
	 * 获取发货区信息状态
	 *
	 * @param string $fhqbh  发货区编号
	 * @return unknown
	 */
	function getFhqzht($fhq){
		$sql = "SELECT FHQZHT FROM H01DB012422 WHERE QYBH = :QYBH AND FHQBH = :FHQBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'FHQBH' => $fhq );
		$fhqzht = $this->_db->fetchOne($sql,$bind);
		return $fhqzht;
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