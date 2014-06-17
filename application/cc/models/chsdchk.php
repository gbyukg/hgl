<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   传送带出口(chsdchk)
 * 作成者：handong
 * 作成日：2011/05/12
 * 更新履历：
 *********************************/
 
class cc_models_chsdchk extends Common_Model_Base {
	
	/**
	 * 得到退货列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter){
		//排序用字段名
		$fields=array("","ZHUANGTAI","CKBH","CHSDCHK");
		//检索SQL
		$sql="SELECT DECODE(ZHUANGTAI,'1','正常','X','禁用') AS CHSDCHKZHT,CKMCH,CHSDCHK,CKBH,DECODE(DLZHT,'1','正常','X','禁用') AS DLZHT" .
			     " FROM H01VIEW012443" .
		     " WHERE QYBH=:QYBH";
		
		
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
	   //自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_CHSDCHK",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 项目最后添加主键
		$sql .=" ,CKBH,CHSDCHK";
		
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
	 * @param string &$chsdchk  传送带出口
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	
	function getChsdchk($ckbh,$chsdchk, $filter, $flg = 'current') {
		
		//检索SQL
		$fields=array("","ZHUANGTAI","CKBH","CHSDCHK"); //状态，仓库，传送带出口
			$sql_list = "SELECT  ROWID,LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",CKBH,CHSDCHK) AS NEXTROWID," . 
		" 						   LAG(ROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",CKBH,CHSDCHK) AS PREVROWID " . 
		"  ,CKBH, CHSDCHK " .
		 " FROM H01VIEW012443 " .
		" WHERE QYBH = :QYBH ";

		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
	 //查找条件  编号或名称
		if($filter['searchParams']['CKBH']!=""){
			$sql_list .= " AND( CKBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(CKMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['CKBH']);
		}
		
		if($filter['searchParams']['CHSDCHK']!=""){
			$sql_list .= " AND( CHSDCHK LIKE '%' || :SEARCHKEYCHSDCHK || '%')";
			$bind ['SEARCHKEYCHSDCHK'] = strtolower($filter ["searchParams"]['CHSDCHK']);
		}
		
		$sql_list .= Common_Tool::createFilterSql("CC_CHSDCHK",$filter['filterParams'],$bind);
		
		$sql_single = " SELECT DECODE(ZHUANGTAI,'1','正常','X','禁用') AS CHSDCHKZHT,CKMCH,CHSDCHK,CKBH" .
		 " FROM H01VIEW012443 " ;
		
		//当前				
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH = :QYBH AND CKBH = :CKBH AND CHSDCHK = :CHSDCHK ";
//			unset ( $bind ['SEARCHKEY'] );
//			unset ( $bind ['SEARCHKEYKQBH'] );
		
		} else if ($flg == 'next') { //下一条		
			$sql_single .= " WHERE ROWID = (SELECT NEXTROWID FROM  (SELECT NEXTROWID,CKBH,CHSDCHK FROM ( $sql_list ) WHERE CKBH= :CKBH AND CHSDCHK = :CHSDCHK))";
		} else if ($flg == 'prev') { //前一条
			$sql_single .= " WHERE ROWID = (SELECT PREVROWID FROM  (SELECT PREVROWID,CKBH,CHSDCHK FROM ( $sql_list ) WHERE CKBH= :CKBH AND CHSDCHK = :CHSDCHK))";
		}
		//绑定 区域编号 & 仓库编号 & 传送带出口
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CKBH'] = $ckbh;
		$bind ['CHSDCHK'] = $chsdchk;
		
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	/**
	 * 生成退货区信息
	 *
	 * @return bool
	 */
	function insertChsdchk() {
		
		//判断是否退货区编号是否存在
		

		if ($this->getChsdchk ( $_POST ['CKBH'], $_POST ['CHSDCHK'] ) != FALSE) {
			return false;
		} else {
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['ZHUANGTAI'] = '1'; //状态
			$data ['CKBH'] = $_POST ['CKBH']; //仓库编号
			$data ['CHSDCHK'] = $_POST ['CHSDCHK']; //传送带出口
			$data ['DLZHT'] = '1';
            $data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
            $data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期	
            $data ['BGZH'] = $_SESSION ['auth']->userId; //变更者	
            $data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //变更日期		
			//保存退货区信息
			$this->_db->insert ( "H01DB012443", $data );
	         return  true;
		}
	}
	
	
	
/**
	 * 退货区信息禁用和启用
	 * @param string $ckbh  仓库编号
	 * @param string $chsdchk  传送带出口
	 * @param string $chsdchkzht 状态
	 * @return unknown
	 */
	function updateStatus($ckbh, $chsdchk, $chsdchkzht) {
		
		$sql = "UPDATE H01DB012443 " . " SET ZHUANGTAI = :ZHUANGTAI" . " WHERE QYBH =:QYBH AND CKBH =:CKBH AND CHSDCHK =:CHSDCHK ";
		$bind = array ('ZHUANGTAI' => $chsdchkzht, 'QYBH' => $_SESSION ['auth']->qybh, 'CKBH' => $ckbh, 'CHSDCHK' => $chsdchk );
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