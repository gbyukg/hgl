<?php
/**********************************************************
 * 模     块：  基础模块(JC)
 * 机     能：  赠品信息(zpxx)
 * 作成者：    姚磊
 * 作成日：    2011/07/04
 * 更新履历：
 **********************************************************/		
class jc_models_zpxx extends Common_Model_Base {

	private $idx_ROWNUM = 0;// 行号
	private $idx_CKBH = 1;// 仓库编号
	private $idx_KWBH = 3;// 库位编号
	private $idx_ZHDSHPBH =7;//传送带出口
	/**
	 * 得到赠品信息
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter){
		//排序用字段名
		$fields = array ("", "ZPBH","ZPMCH","GUIGE","SHPBH","SHCHCHJ","BZHQYSH");

		//检索SQL
		$sql = " SELECT  ZPBH,ZPMCH,SHPBH,GUIGE,SHCHCHJ,BZHQYSH ".
			   " FROM H01VIEW012470 WHERE QYBH=:QYBH AND ZHUANGTAI ='1' ";

		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;

		
		//查找条件  编号或名称
		if($filter['searchParams']['ZPXX']!=""){
			$sql .= " AND( lower(ZPBH) LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(ZPMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['ZPXX']);
		}

		$sql .= Common_Tool::createFilterSql("JC_ZPXX",$filter['filterParams'],$bind);
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= " ,ZPBH ";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true,$totalCount, $filter ["posStart"] );
	}

	/*
	 * 修改/上一条/下一条
	 */
	public function getDate($zpbh,$filter,$flg = 'current'){
		//排序用字段名
		$fields = array ("", "ZPBH","ZPMCH","GUIGE","SHPBH","SHCHCHJ","BZHQYSH");

		//检索SQL
		$sql_list = "SELECT  ROWID,LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",ZPBH) AS NEXTROWID," . 
		" 						   LAG(ROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",ZPBH) AS PREVROWID " . 
		"  ,ZPBH " .
		 " FROM H01VIEW012470 " .
		" WHERE QYBH = :QYBH ";
	
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;

		
		//查找条件  编号或名称
		if($filter['searchParams']['ZPXX']!=""){
			$sql_list .= " AND( ZPBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(ZPMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['ZPXX']);
		}

		$sql_list .= Common_Tool::createFilterSql("JC_ZPXX",$filter['filterParams'],$bind);
		$sql_single = " SELECT  ZPBH,ZPMCH,SHPBH,SHPMCH,GUIGE,SHCHCHJ,BZHQYSH ,TO_CHAR(BGRQ,'YYYY-MM-DD hh24:mi:ss') AS BGRQ ,BGZH ".
			   " FROM H01VIEW012470   ";
		//当前				
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH = :QYBH AND ZPBH = :ZPBH  ";		
		} else if ($flg == 'next') { //下一条		
			$sql_single .= "WHERE ROWID = (SELECT NEXTROWID FROM  (SELECT NEXTROWID,ZPBH FROM ( $sql_list ) WHERE ZPBH = :ZPBH))";
		} else if ($flg == 'prev') { //前一条
			$sql_single .= "WHERE ROWID = (SELECT PREVROWID FROM  (SELECT PREVROWID,ZPBH FROM ( $sql_list ) WHERE ZPBH = :ZPBH ))";
		}
		//绑定 区域编号 & 仓库编号 & 库区编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['ZPBH'] = $zpbh;
		
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	
	/*
	 *保存修改信息 
	 */
		function updatezpxx() {
		try {
			//开始一个事务

		  $this->_db->beginTransaction();
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss'),BGZH FROM H01DB012470 WHERE QYBH = :QYBH AND ZPBH = :ZPBH  ";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'ZPBH' => $_POST ['ZPBHH'] );
		$timestamp = $this->_db->fetchOne ( $sql, $bind );
		
		//时间戳已经变更
		

		if ($timestamp != $_POST ['BGRQ']) {
			return false;
		} else {
			$sql = " UPDATE  H01DB012470 SET " . " QYBH = :QYBH," . " ZPMCH = :ZPMCH," . " BZHQYSH = :BZHQYSH," .
			 	   " SHPBH = :SHPBH," . "GUIGE = :GUIGE,"  ."SHCHCHJ = :SHCHCHJ,". " BGRQ = sysdate," . " BGZH = :BGZH" .
			 	   " WHERE QYBH = :QYBH AND ZPBH =:ZPBH ";
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号			
			$bind ['ZPMCH'] = $_POST ['ZPMCH']; //赠品名称
			$bind ['BZHQYSH'] = $_POST ['BZHQYSH']; //保质期月数
			$bind ['SHPBH'] = $_POST ['SHPBH']; //商品编号
			$bind ['GUIGE'] = $_POST ['GUIGE']; //规格
			$bind ['SHCHCHJ'] = $_POST ['SHCHCHJ']; //生产厂家
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			

			$this->_db->query ( $sql, $bind );
			$this->_db->commit();
			return true;
		}
		}catch (Exception  $ex){
			$this->_db->rollBack();
			throw $ex;
		}
	}
	/*
	 * 保存数据
	 */
	function insertzPxx($zpbh) {
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['ZPBH'] = $zpbh; //赠品编号
			$data ['ZPMCH'] = $_POST ['ZPMCH']; //赠品名称
			$data ['GUIGE'] = $_POST ['GUIGE']; //规格
			$data ['BZHQYSH'] = $_POST ['BZHQYSH']; //保质期月数			
			$data ['SHCHCHJ'] = $_POST ['SHCHCHJ']; //生产厂家
			$data ['SHPBH'] = $_POST ['SHPBH']; //商品编号
			$data ['ZHUANGTAI'] = '1'; //状态
			$data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; //操作用户					
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		try {
				    //开始一个事务

				    $this->_db->beginTransaction();
					//保存库区信息
					$this->_db->insert ( "H01DB012470", $data );
					$this->_db->commit();
					return true;
					
			}
			catch (Exception $ex) 
			{
				$this->_db->rollBack();
				throw $ex;
			}
		
	}
	/*
	 * 删除数据
	 */
	function del($flg){
		$sql = " UPDATE  H01DB012470 SET " . " QYBH = :QYBH," . " ZHUANGTAI = :ZHUANGTAI" .
			 	   " WHERE QYBH = :QYBH AND ZPBH =:ZPBH ";
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号			
			$bind ['ZHUANGTAI'] = 'X'; //赠品名称
			$bind ['ZPBH'] = $flg; //赠品编号
		$this->_db->query ( $sql, $bind );
		
	} 
	
	
}