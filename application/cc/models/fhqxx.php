
<?php
/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：      发货区信息维护(fhqxx)
 ***** 作  成  者：        姚磊
 ***** 作  成  日：        2011/01/29
 ***** 更新履历：
 ******************************************************************/

class cc_models_fhqxx extends Common_Model_Base {
	
	/*
	 * 查询 显示发货区信息
	 */
	public function getlistdata($filter){
	
	$fields = array ("", "FHQZHT", "FHQBH", "FHQMCH","CKBH","CKMCH");
		
	$sql = " SELECT DECODE(FHQZHT,'1','启用','X','禁用') AS FHQZHT ,FHQBH,FHQMCH,CKBH,CKMCH,BGZH,TO_CHAR(BGRQ,'YYYY-MM-DD') AS BGRQ FROM H01VIEW012422 WHERE QYBH=:QYBH";
	
	$bind ['QYBH'] = $_SESSION ['auth']->qybh;

	if($filter['searchParams']['SERCHFHQMCH']!=""){
		$sql .= " AND( FHQBH LIKE '%' || :SEARCHKEYFHQXX || '%'".
		        "      OR  lower(FHQMCH) LIKE '%' || :SEARCHKEYFHQXX|| '%')";
		$bind ['SEARCHKEYFHQXX'] = strtolower($filter ["searchParams"]['SERCHFHQMCH']);
	}
	$sql .= Common_Tool::createFilterSql("CC_FHQXXWH",$filter['filterParams'],$bind);
	$sql.= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
	//防止重复数据引发翻页排序异常，orderby 添加主键
	$sql .= ",FHQBH";
	//翻页表格用SQL生成(总行数与单页记录)
	$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
	
	//总行数
	$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
	
	//当前页数据
	$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
	
	//调用表格xml生成函数
	return Common_Tool::createXml ( $recs, true,$totalCount, $filter ["posStart"] );
	
	}
	
	/*
	 * 获取发货区信息详情
	 */
	public function getmingxi($con,$filter,$flg = 'current'){
		
		$fields = array ("", "FHQZHT","FHQBH", "FHQMCH", "FHQZHT","CKBH","CKMCH");
		$sql =  " SELECT  ROWID,LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",FHQBH) AS NEXTROWID," . 
		 		" LAG(ROWID)  OVER(ORDER BY " .$fields [$filter ["orderby"]] . " " .$filter ["direction"] . ",FHQBH) AS PREVROWID ," . 
		 		" FHQBH,FHQMCH,DECODE(FHQZHT,'1','启用','X','禁用') ,FHQZHT AS FHQZHTBH ,BGRQ FROM H01DB012422 WHERE QYBH=:QYBH ORDER BY FHQBH";
			
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['FHQBH'] =$con;
		if($filter['searchParams']['SERCHFHQMCH']!=""){
		$sql .= " AND( FHQBH LIKE '%' || :SEARCHKEYFHQXX || '%'".
		        "      OR  lower(FHQMCH) LIKE '%' || :SEARCHKEYFHQXX|| '%')";
		$bind ['SEARCHKEYFHQXX'] = strtolower($filter ["searchParams"]['SERCHFHQMCH']);
		}
		$sql .= Common_Tool::createFilterSql("CC_FHQXXWH",$filter['filterParams'],$bind);
		
		$sql_list = " SELECT FHQBH,FHQMCH,DECODE(FHQZHT,'1','启用','X','禁用') AS FHQZHT,CKBH,CKMCH,TO_CHAR(BGRQ,'YYYY-MM-DD hh24:mi:ss') AS BGRQ FROM H01VIEW012422 WHERE QYBH=:QYBH  ";
		
		if ($flg == 'current') {
			$sql_list .= " AND FHQBH =:FHQBH ";
			
		
		} else if ($flg == 'next') { //下一条		
			$sql_list .= " AND ROWID = (SELECT NEXTROWID FROM  (SELECT NEXTROWID,FHQBH FROM ( $sql ) WHERE FHQBH = :FHQBH  ))";
		} else if ($flg == 'prev') { //前一条
			$sql_list .= " AND ROWID = (SELECT PREVROWID FROM  (SELECT PREVROWID,FHQBH FROM ( $sql ) WHERE FHQBH = :FHQBH ))";
		}
		
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['FHQBH'] =$con;
		
		return $this->_db->fetchRow ( $sql_list, $bind );
		
	}
	
	/**
	 * 删除发货区信息
	 * @param string $ckbh  发货区编号
	 * @return unknown
	 */
	public function deletefhxx($fhqbh,$fhqzht) {
		
		if($fhqzht =='1'){				//禁用发货区
		$sql = "UPDATE H01DB012422 " . " SET FHQZHT = 'X'" . " WHERE QYBH =:QYBH AND FHQBH =:FHQBH  ";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['FHQBH'] =$fhqbh;
		$this->_db->query( $sql, $bind );
		
		}else{
		$sql = "UPDATE H01DB012422 " . " SET FHQZHT = '1'" . " WHERE QYBH =:QYBH AND FHQBH =:FHQBH  ";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['FHQBH'] =$fhqbh;
		$this->_db->query( $sql, $bind );
	
		}
	}
	/**
	 * 修改发货区信息
	 * @param string $fhqbh 发货区编号
	 */
	public function updatefhqxx($fhqbh){
		
		$sql = "UPDATE H01DB012422 " . " SET FHQZHT = 'X'" . " BGRQ = sysdate," . " BGZH = :BGZH" ." WHERE QYBH =:QYBH AND FHQBH =:FHQBH  ";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['FHQBH'] =$fhqbh;
		$bind ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
		return $this->_db->query ( $sql, $bind );
	}
	/**
	 * 获取发货区状态
	 * @param string $fhqbh 发货区编号
	 */
	public function getfhqzt($fhqbh){
		
		$sql = "SELECT FHQZHT FROM H01DB012422 WHERE QYBH =:QYBH AND FHQBH =:FHQBH";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['FHQBH'] =$fhqbh;
		$fhqzht = $this->_db->fetchPairs ( $sql, $bind );
		$fhqzht [''] = '- - 请 选 择 - -';
		ksort ( $fhqzht );
		return $fhqzht;
	}
	/**
	 * 修改发货区信息
	 * @param 
	 */
	public function updateFhqbh(){
		
		try {
			//开始一个事务

		  $this->_db->beginTransaction();
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss'),BGZH FROM H01DB012422 WHERE QYBH = :QYBH AND FHQBH = :FHQBH ";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'FHQBH' => $_POST ['FHQBH'] );
		$timestamp = $this->_db->fetchOne ( $sql, $bind );
		
		//时间戳已经变更
		

		if ($timestamp != $_POST ['BGRQ']) {
			return false;
		} else {
			$sql = " UPDATE  H01DB012422 SET " . 
				   " QYBH = :QYBH," .
			 	   " FHQBH = :FHQBH," .
				   " FHQMCH = :FHQMCH," .
				   //" FHQZHT = :FHQZHT," . 
				   " BGRQ = sysdate," . " BGZH = :BGZH" . 
				   " WHERE QYBH = :QYBH AND FHQBH =:FHQBH ";
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['FHQBH'] = $_POST ['FHQBH']; //发货区编号
			$bind ['FHQMCH'] = $_POST ['FHQMCH']; //发货区名称
			//$bind ['FHQZHT'] = $_POST ['FHQZHT']; //发货区状态
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			

			$this->_db->query ( $sql, $bind );
			$this->_db->commit();
			//return true;
		}
		}catch (Exception  $ex){
			$this->_db->rollBack();
			throw $ex;
		}
		return true;
	}
	
	/**
	 * 验证发货区编号唯一
	 */
	public function getFhqbh($fhqbh){
		
		$sql = " SELECT FHQBH FROM H01DB012422 WHERE QYBH =:QYBH AND FHQBH =:FHQBH";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['FHQBH'] =$fhqbh;
		return $this->_db->fetchRow ( $sql, $bind );
		
	}
	/**
	 * 发货区信息新规
	 *
	 * @return bool
	 */
	public function insertFhqbh() {
		
		//判断是否发货区编号是否存在
		
		$sfbh  = $this->getFhqbh( $_POST ['FHQBH'] );
		if ($sfbh != FALSE) {
			return false;
		} else {			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['FHQBH'] = $_POST ['FHQBH']; //发货区编号
			$data ['FHQMCH'] = $_POST ['FHQMCH']; //发货区名称
			$data ['FHQZHT'] = '1'; //发货区状态 默认启用
			$data ['CKBH'] = $_POST['CKBH'];//仓库编号
			$data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; //操作用户					
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			try {
				    //开始一个事务

				    $this->_db->beginTransaction();
					//保存库区信息
					$this->_db->insert ( "H01DB012422", $data );
					$this->_db->commit();
					//return true;
					
			}
			catch (Exception $ex) 
			{
				$this->_db->rollBack();
				throw $ex;
			}
			return true;
		}
	}
	
}