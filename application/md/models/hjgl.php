<?php
/*********************************
 * 模块：   门店模块(MD)
 * 机能：   货架管理(hjgl)
 * 作成者：李洪波
 * 作成日：2011/02/16
 * 更新履历：
 *********************************/
class md_models_hjgl extends Common_Model_Base {
	
	/**
	 * 得到柜组列表数据
	 * @param array $filter
	 * @return string xml
	 */
	public function getListData($filter) {
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
	 * 自动得到柜组列表数据
	 * @param array $filter
	 * @return string xml
	 */

	public function getAutocompleteData() {
	
		//检索SQL
		$sql = "SELECT GZBH,GZMC,FZRBH,TO_CHAR(CHJRQ,'YYYY-MM-DD')," . 
		       " DECODE(SHYZHT,'0','停用','1','正常',''),BEIZHU" . 
		       " FROM H01VIEW012516 " . 
		       " WHERE QYBH = :QYBH AND MDBH=:MDBH AND SHYZHT='1' ORDER BY GZBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['MDBH'] = $_SESSION ['auth']->mdbh;

		return $this->_db->fetchAll($sql,$bind);
	}
	
	/**
	 * 得到货架列表数据
	 * @param array $filter
	 * @return string xml
	 */

	public function getGridData($filter) {
		//排序用字段名
		$fields = array ("","GZBH","HJBH","HJMCH" ,"FZRBH", "CHJRQ", "SHYZHT");//货架编号，负责人，建立日期

		//检索SQL
		$sql = "SELECT GZMCH,HJBH,HJMCH,FZRBH,TO_CHAR(CHJRQ,'YYYY-MM-DD')," . 
		       " DECODE(SHYZHT,'0','停用','1','正常',''),BEIZHU,GZBH" . 
		       " FROM H01VIEW012517 " . 
		       " WHERE QYBH = :QYBH AND MDBH=:MDBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['MDBH'] = $_SESSION ['auth']->mdbh;
		
		if ($filter ["searchParams"]['SEARCHKEY_H']!=""){
			$sql.=" AND GZBH=:GZBH";			
			$bind ['GZBH'] = strtolower($filter ["searchParams"]['SEARCHKEY_H']);
		}		

		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("MD_HJGL",$filter['filterParams'],$bind);
		
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
	 * 取得货架信息
	 * @param string $gzbh 货架编号
	 * @param array $filter  查询条件
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	function getHjxx($gzbh,$mdbh,$hjbh,$filter=null, $flg = 'current') {
		//排序用字段名
		$fields = array ("","GZMCH","HJBH","HJMCH" ,"FZRBH", "CHJRQ", "SHYZHT");//货架编号，负责人，建立日期
		$sql_list = "SELECT  ROWID,LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",HJBH) AS NEXTROWID,".
		            "                LAG(ROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] ." ,HJBH) AS PREVROWID,".
		            " GZMCH,HJBH,HJMCH,FZRBH,TO_CHAR(CHJRQ,'YYYY-MM-DD') AS CHJRQ," . 
		            " SHYZHT,BEIZHU,to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') AS BGRQ,GZBH" . 
		            " FROM H01VIEW012517 " . 
					" WHERE QYBH = :QYBH AND MDBH=:MDBH AND GZBH=:GZBH";
		
        //绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		//货架信息单条查询
		$sql_single = "SELECT GZMCH,HJBH,HJMCH,FZRBH,".
		              "TO_CHAR(CHJRQ,'YYYY-MM-DD') AS CHJRQ," .
		              "SHYZHT,BEIZHU,to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') AS BGRQ,GZBH".
		              " FROM H01VIEW012517 ";		            		
		//当前
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH = :QYBH AND MDBH=:MDBH AND GZBH = :GZBH AND HJBH = :HJBH";
			
		} else if ($flg == 'next') {//下一条
			$sql_single .= " WHERE ROWID =  (SELECT NEXTROWID FROM  (SELECT NEXTROWID,HJBH FROM ( $sql_list ) WHERE HJBH = :HJBH))";		
		} else if ($flg == 'prev') {//前一条
			$sql_single .= " WHERE ROWID =  (SELECT PREVROWID FROM  (SELECT PREVROWID,HJBH FROM ( $sql_list ) WHERE HJBH = :HJBH))";
		}
      	$bind['GZBH'] = $gzbh; //当前柜组编号	
		$bind['MDBH'] = $mdbh; //当前门店编号
		$bind['HJBH'] = $hjbh; //当前货架编号
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	
	/**
	 * 取得货架信息
	 * @param string $gzbh 货架编号
	 * @param array $filter  查询条件
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	function getGzxx($gzbh) {
		
		//货架信息单条查询
		$sql = "SELECT GZBH,GZMC,FZRBH,".
		              "TO_CHAR(CHJRQ,'YYYY-MM-DD') AS CHJRQ," .
		              "SHYZHT,BEIZHU,to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') AS BGRQ".
		              " FROM H01VIEW012516 ".
					  " WHERE QYBH = :QYBH AND MDBH=:MDBH AND GZBH = :GZBH"; 
		            		
	     //绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['MDBH'] = $_SESSION ['auth']->mdbh;
		$bind ['GZBH'] = $gzbh; //当前柜组编号
		$Djxx = $this->_db->fetchRow( $sql, $bind );
		return $Djxx;   
	}
	
	/**
	 * 生成货架信息
	 * @return bool
	 */
	function insertGZxx() {
		
		//判断是否员工编号是否存在
		if ($this->getHjxx ( $_POST ['GZBH'], $_POST ['MDBH_H'], $_POST ['HJBH']) != FALSE) {
			return false;
		} else {
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['MDBH'] = $_POST ['MDBH_H']; // 门店编号
			$bind ['GZBH'] = $_POST ['GZBH']; // 柜组编号
			$bind ['HJBH'] = $_POST ['HJBH']; // 货架编号
			$bind ['HJMCH'] = $_POST ['HJMCH']; //货架名称
			$bind ['FZRBH'] = $_POST ['FZRBH']; //负责人编号
			$bind ['SHYZHT'] = $_POST['SHYZHT']; //使用状态		
			$bind ['BEIZHU'] = $_POST ['BEIZHU']; //备注
			$bind ['BGRQ'] = new Zend_Db_Expr ("SYSDATE");	//变更日期
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			if ($_POST ['JLRQ'] != "") {
				$bind ['CHJRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['JLRQ'] . "','YYYY-MM-DD')" ); //出生日期
			}
		
			//货架信息表
			$this->_db->insert ( "H01DB012517", $bind );
			return true;
		}
}
	
	/**
	 * 更新货架信息*
	 * @return bool
	 */
	function updateGzxx() {
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') FROM H01VIEW012517 ".
			   "WHERE QYBH = :QYBH AND GZBH = :GZBH AND MDBH = :MDBH AND HJBH = :HJBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'GZBH' => $_POST ['GZBH'], 'MDBH' => $_POST ['MDBH_H'], 'HJBH' => $_POST ['HJBH']);
		$timestamp = $this->_db->fetchOne ( $sql, $bind );
		
		//时间戳已经变更
		if ($timestamp != $_POST ['BGRQ']) {
			return false;
		} else {
			$sql = "UPDATE H01DB012517 SET " .
			       " HJMCH = :HJMCH," .
			       " FZRBH = :FZRBH," . 
			       " SHYZHT = :SHYZHT," .
			 	   " CHJRQ = TO_DATE(:CHJRQ,'YYYY-MM-DD')," .			 
			       " BEIZHU = :BEIZHU," . 
			       " BGRQ = SYSDATE," . 
			       " BGZH = :BGZH" . 
			       " WHERE QYBH = :QYBH AND GZBH =:GZBH AND MDBH =:MDBH AND HJBH =:HJBH";
			
			$bind ['HJMCH'] = $_POST ['HJMCH']; //货架名称
			$bind ['FZRBH'] = $_POST ['FZRBH']; //负责人编号
			$bind ['SHYZHT'] = $_POST['SHYZHT']; //使用状态
			if ($_POST ['JLRQ'] != "") {
				$bind ['CHJRQ'] = $_POST ['JLRQ']; //出生日期
			}
			$bind ['BEIZHU'] = $_POST ['BEIZHU']; //备注
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['MDBH'] = $_POST ['MDBH_H']; // 门店编号
			$bind ['GZBH'] = $_POST ['GZBH']; // 柜组编号
			$bind ['HJBH'] = $_POST ['HJBH']; // 货架编号
			$this->_db->query ( $sql, $bind );
			
			return true;
		}
	}
}
