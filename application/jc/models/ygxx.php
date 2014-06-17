<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   员工信息(ygxx)
 * 作成者：周义
 * 作成日：2010/10/14
 * 更新履历：
 *********************************/
class jc_models_ygxx extends Common_Model_Base {

	/**
	 * 取得列表数据
	 *
	 * @param unknown_type $filter
	 * @return unknown
	 */
    public function getGridData($filter) {
		//排序用字段名
		$fields = array ("", "YGZHT", "YGBH", "YGXM", "", "SSBM","XINGBIE" ); //编号，姓名，所属部门,性别

		//检索SQL
		$sql = "SELECT DECODE(YGZHT,'X','禁用','1','正常','未知') AS YGZHT,YGBH,YGXM,ZHJM,SSBMMCH,DECODE(XINGBIE,'0','男','1','女','')," . 
		       " TO_CHAR(CHSHRQ,'YYYY-MM-DD'),SHFZHH,DHHM,SHJHM,DZYJ,ZHZH," . 
		       " BEIZHU,TO_CHAR(BGRQ,'YYYY-MM-DD'),BGZHXM" . 
		       " FROM H01VIEW012113 " .  
		       " WHERE QYBH = :QYBH ";

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		if($filter['searchParams']['SEARCHKEY']!=""){
			$sql .= " AND( YGBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(YGXM) LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(ZHJM) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['SEARCHKEY']);
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("JC_YGXX",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 项目最后添加主键
		$sql .=",YGBH";
		
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
	 * 取得报表数据
	 *
	 * @param unknown_type $filter
	 * @return unknown
	 */
    public function getListReportData($filter) {
		//排序用字段名
		$fields = array ("", "YGZHT", "YGBH", "YGXM", "", "SSBM","XINGBIE" ); //编号，姓名，所属部门,性别

		//检索SQL
		$sql = "SELECT DECODE(YGZHT,'X','禁用','1','正常','未知') AS YGZHT,YGBH,YGXM,SSBMMCH,DECODE(XINGBIE,'0','男','1','女','') AS XINGBIE," . 
		       " TO_CHAR(CHSHRQ,'YYYY-MM-DD') AS CHSHRQ,SHFZHH,DHHM,SHJHM,DZYJ,ZHZH".
		       " FROM H01VIEW012113 " .  
		       " WHERE QYBH = :QYBH ";

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		if($filter['searchParams']['SEARCHKEY']!=""){
			$sql .= " AND( YGBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(YGXM) LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(ZHJM) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['SEARCHKEY']);
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("JC_YGXX",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY ". $fields [$filter["sortParams"] ["orderby"]] . " " . $filter["sortParams"]["direction"];
		//防止重复数据引发翻页排序异常，orderby 项目最后添加主键
		$sql .=",YGBH";
		//当前页数据
		$recs = $this->_db->fetchAll ( $sql, $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createRptXml($recs,1);
	}
	
	
	
	
	/**
	 * 取得员工信息
	 * @param string $ygbh 员工编号
	 * @param array $filter  查询条件
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	function getYgxx($ygbh,$filter=null, $flg = 'current') {
		//列表查询条件及排序（上一条下一条需按照列表的查询条件和排序为基础）
		//排序用字段名
		$fields = array ("", "YGZHT", "YGBH", "YGXM", "", "SSBM","XINGBIE" ); //编号，姓名，所属部门		
		
		$sql_list = "SELECT  ROWID,LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",YGBH) AS NEXTROWID,".
		            "              LAG(ROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] ." ,YGBH) AS PREVROWID".
		            " ,YGBH".
		            " FROM H01VIEW012113" . 
		            " WHERE QYBH = :QYBH";
		
       //绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
	    if($filter['searchParams']['SEARCHKEY']!=""){
		 	$sql_list .= " AND( YGBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(YGXM) LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(ZHJM) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['SEARCHKEY']);
	     }
		
		//自动生成精确查询用Sql
		$sql_list .= Common_Tool::createFilterSql("JC_YGXX",$filter['filterParams'],$bind);

		//员工信息单条查询
		$sql_single = "SELECT YGBH,YGXM,ZHJM,SSBM,SSBMMCH,".
		              "XINGBIE,to_char(CHSHRQ,'YYYY-MM-DD') AS CHSHRQ,SHFZHH," .
		              "DZYJ,DHHM,SHJHM,YGZHT,ZHZH,BEIZHU,to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') AS BGRQ,".
		              "BGZH,YGXM AS BHZHXM" .
		              " FROM H01VIEW012113 ";
		//当前
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH = :QYBH AND YGBH = :YGBH";
			unset($bind['SEARCHKEY']);
		} else if ($flg == 'next') {//下一条
			$sql_single .= "WHERE ROWID =  (SELECT NEXTROWID FROM  (SELECT NEXTROWID,YGBH FROM ( $sql_list ) WHERE YGBH = :YGBH))";		
		} else if ($flg == 'prev') {//前一条
			$sql_single .= "WHERE ROWID =  (SELECT PREVROWID FROM  (SELECT PREVROWID,YGBH FROM ( $sql_list ) WHERE YGBH = :YGBH))";		
		}
		
		$bind['YGBH'] = $ygbh; //当前员工编号
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	
	/**
	 * 生成员工信息
	 *
	 * @return bool
	 */
	function insertYgxx() {
		
		//判断是否员工编号是否存在
		if ($this->getYgxx ( $_POST ['YGBH'] ) != FALSE) {
			return false;
		} else {
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['YGBH'] = $_POST ['YGBH']; //员工编号
			$data ['YGXM'] = $_POST ['YGXM']; //员工姓名
			$data ['ZHJM'] = $_POST ['ZHJM']; //助记码
			$data ['SSBM'] = $_POST ['BMBH']; //所属部门
			$data ['XINGBIE'] = $_POST ['XINGBIE']; //性别
			if ($_POST ['CHSHRQ'] != "") {
				$data ['CHSHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['CHSHRQ'] . "','YYYY-MM-DD')" ); //出生日期
			}
			$data ['SHFZHH'] = $_POST ['SHFZHH']; //身份证号
			$data ['DZYJ'] = $_POST ['DZYJ']; //电子邮件
			$data ['DHHM'] = $_POST ['DHHM']; //电话号码
			$data ['SHJHM'] = $_POST ['SHJHM']; //手机号码
			$data ['YGZHT'] = '1'; //使用状态可用
			$data ['ZHZH'] = $_POST ['ZHZH']; //手机号码
			$data ['BEIZHU'] = $_POST ['BEIZHU']; //手机号码
			$data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			

			//员工信息表
			$this->_db->insert ( "H01DB012113", $data );
			return true;
		}
	
	}
	
	/**
	 * 更新员工信息
	 *
	 * @return bool
	 */
	function updateYgxx() {
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') FROM H01DB012113 WHERE QYBH = :QYBH AND YGBH = :YGBH FOR UPDATE WAIT 10";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'YGBH' => $_POST ['YGBH'] );
		$timestamp = $this->_db->fetchOne ( $sql, $bind );
		
		//时间戳已经变更
		if ($timestamp != $_POST ['BGRQ']) {
			return false;
		} else {
			$sql = "UPDATE H01DB012113 SET " .
			       " YGXM = :YGXM," .
			       " ZHJM = :ZHJM," . 
			       " SSBM = :SSBM," . 
			       " XINGBIE = :XINGBIE," . 
			       " CHSHRQ = TO_DATE(:CHSHRQ,'YYYY-MM-DD')," . 
			       " SHFZHH = :SHFZHH," .
			       " DZYJ = :DZYJ," . 
			       " DHHM = :DHHM," . 
			       " SHJHM = :SHJHM," . 
			       " ZHZH = :ZHZH," . 
			       " BEIZHU = :BEIZHU," . 
			       " BGRQ = SYSDATE," . 
			       " BGZH = :BGZH" . "
			        WHERE QYBH = :QYBH AND YGBH =:YGBH";
			
			$bind ['YGXM'] = $_POST ['YGXM']; //员工姓名
			$bind ['ZHJM'] = $_POST ['ZHJM']; //助记码
			$bind ['SSBM'] = $_POST['BMBH']; //所属部门
			$bind ['XINGBIE'] = $_POST ['XINGBIE']; //性别
			$bind ['CHSHRQ'] = $_POST ['CHSHRQ'];//出生日期
			$bind ['SHFZHH'] = $_POST ['SHFZHH']; //身份证号
			$bind ['DZYJ'] = $_POST ['DZYJ']; //电子邮件
			$bind ['DHHM'] = $_POST ['DHHM']; //电话号码
			$bind ['SHJHM'] = $_POST ['SHJHM']; //手机号码		
			$bind ['ZHZH'] = $_POST ['ZHZH']; //住址
			$bind ['BEIZHU'] = $_POST ['BEIZHU']; //备注
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['YGBH'] = $_POST ['YGBH']; //员工编号

			$this->_db->query ( $sql, $bind );
			
			return true;
		}
	}
	
	/**
	 * 员工锁定和解锁
	 *
	 * @param string $ygbh  员工编号
	 * @param string $shyzht 状态
	 * @return unknown
	 */
	function updateStatus($ygbh, $ygzht) {
		$sql = "UPDATE H01DB012113 " .
		       " SET YGZHT = :YGZHT" .
		       " WHERE QYBH =:QYBH AND YGBH =:YGBH";
		
		$bind['QYBH'] =$_SESSION ['auth']->qybh;
		$bind['YGBH']= $ygbh;
		$bind['YGZHT'] = $ygzht;
		return $this->_db->query ( $sql, $bind );
	
	}

}
