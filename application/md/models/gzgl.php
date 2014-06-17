<?php
/*********************************
 * 模块：   门店模块(MD)
 * 机能：   柜组管理(gzgl)
 * 作成者：李洪波
 * 作成日：2011/02/09
 * 更新履历：
 *********************************/
class md_models_gzgl extends Common_Model_Base {
	/**
	 * 得到柜组列表数据
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ("","GZBH","GZMC" ,"FZRBH", "CHJRQ","SHYZHT");//柜组编号，负责人，建立日期
		//检索SQL
		$sql = "SELECT GZBH,GZMC,FZRBH,TO_CHAR(CHJRQ,'YYYY-MM-DD')," . 
		       " DECODE(SHYZHT,'0','停用','1','正常',''),BEIZHU" . 
		       " FROM H01VIEW012516 " . 
		       " WHERE QYBH = :QYBH AND MDBH=:MDBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['MDBH'] = $_SESSION ['auth']->mdbh;
			
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("MD_GZGL",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		
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
	 * 取得柜组信息
	 * @param string $gzbh 柜组编号
	 * @param array $filter  查询条件
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	function getGzxx($gzbh,$mdbh,$filter=null, $flg = 'current') {
		//列表查询条件及排序（上一条下一条需按照列表的查询条件和排序为基础）
		//排序用字段名
		$fields = array ("","GZBH","GZMC" ,"FZRBH", "CHJRQ","SHYZHT");//柜组编号，负责人，建立日期
		$sql_list = "SELECT  ROWID,LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",GZBH) AS NEXTROWID,".
		            "                LAG(ROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] ." ,GZBH) AS PREVROWID,".
		            " GZBH,GZMC,FZRBH,TO_CHAR(CHJRQ,'YYYY-MM-DD') AS CHJRQ," . 
		            " SHYZHT,BEIZHU,to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') AS BGRQ" . 
		            " FROM H01VIEW012516 " . 
		            " WHERE QYBH = :QYBH AND MDBH=:MDBH";
		
        //绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		if ($filter ["searchParams"]['SEARCHKEY']!=""){
			$sql_list.=" AND GZBH=:GZBH";			
			$bind ['GZBH'] = strtolower($filter ["searchParams"]['SEARCHKEY']);
		}	
		
		//自动生成精确查询用Sql
		$sql_list .= Common_Tool::createFilterSql("MD_GZGL",$filter['filterParams'],$bind);
		//排序
		$sql_list .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//$sql_list .=" QYBH,MDBH,GZBH";
			  
		//柜组信息单条查询
		$sql_single = "SELECT GZBH,GZMC,FZRBH,".
		              "TO_CHAR(CHJRQ,'YYYY-MM-DD') AS CHJRQ," .
		              "SHYZHT,BEIZHU,to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') AS BGRQ".
		              " FROM H01VIEW012516 "; 
		            		
		//当前
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH = :QYBH AND MDBH=:MDBH AND GZBH = :GZBH";
		} else if ($flg == 'next') {//下一条
			$sql_single .= " WHERE ROWID =  (SELECT NEXTROWID FROM  (SELECT NEXTROWID,GZBH FROM ( $sql_list ) WHERE GZBH = :GZBH))";		
		} else if ($flg == 'prev') {//前一条
			$sql_single .= " WHERE ROWID =  (SELECT PREVROWID FROM  (SELECT PREVROWID,GZBH FROM ( $sql_list ) WHERE GZBH = :GZBH))";		
		}
      
		$bind['GZBH'] = $gzbh; //当前柜组编号
		$bind['MDBH'] = $mdbh; //当前门店编号
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	
	/**
	 * 生成柜组信息
	 *
	 * @return bool
	 */
	function insertGZxx() {
		
		//判断是否员工编号是否存在
		if ($this->getGzxx ( $_POST ['GZBH'], $_POST ['MDBH_H']) != FALSE) {
			return false;
		} else {
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['MDBH'] = $_POST ['MDBH_H']; // 门店编号
			$bind ['GZBH'] = $_POST ['GZBH']; // 柜组编号
			$bind ['GZMC'] = $_POST ['GZMCH']; //柜组名称
			$bind ['FZRBH'] = $_POST ['FZRBH']; //负责人编号
			$bind ['SHYZHT'] = $_POST['SHYZHT']; //使用状态		
			$bind ['BEIZHU'] = $_POST ['BEIZHU']; //备注
			$bind ['BGRQ'] = new Zend_Db_Expr ("SYSDATE");	//变更日期
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			if ($_POST ['JLRQ'] != "") {
				$bind ['CHJRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['JLRQ'] . "','YYYY-MM-DD')" ); //出生日期
			}
		
			//柜组信息表
			$this->_db->insert ( "H01DB012516", $bind );
			return true;
		}
}
	
	/**
	 * 更新柜组信息
	 *
	 * @return bool
	 */
	function updateGzxx() {
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') FROM H01VIEW012516 WHERE QYBH = :QYBH AND GZBH = :GZBH AND MDBH = :MDBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'GZBH' => $_POST ['GZBH'], 'MDBH' => $_POST ['MDBH_H']);
		$timestamp = $this->_db->fetchOne ( $sql, $bind );
		
		//时间戳已经变更
		if ($timestamp != $_POST ['BGRQ']) {
			return false;
		} else {
			$sql = "UPDATE H01DB012516 SET " .
			       " GZMC = :GZMC," .
			       " FZRBH = :FZRBH," . 
			       " SHYZHT = :SHYZHT," .
			 	   " CHJRQ = TO_DATE(:CHJRQ,'YYYY-MM-DD')," .			 
			       " BEIZHU = :BEIZHU," . 
			       " BGRQ = SYSDATE," . 
			       " BGZH = :BGZH" . 
			       " WHERE QYBH = :QYBH AND GZBH =:GZBH AND MDBH =:MDBH";
			
			$bind ['GZMC'] = $_POST ['GZMCH']; //柜组名称
			$bind ['FZRBH'] = $_POST ['FZRBH']; //负责人编号
			$bind ['SHYZHT'] = $_POST['SHYZHT']; //使用状态
			if ($_POST ['JLRQ'] != "") {
				$bind ['CHJRQ'] = $_POST ['JLRQ']; //出生日期
			}
			$bind ['BEIZHU'] = $_POST ['BEIZHU']; //备注
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['MDBH'] = $_POST ['MDBH_H']; // 门店编号
			$bind ['GZBH'] = $_POST ['GZBH']; // 柜组编
			$this->_db->query ( $sql, $bind );
			
			return true;
		}
	}
}
