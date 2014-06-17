<?php
/******************************************************************
 ***** 模         块：       门店模块(MD)
 ***** 机         能：     门店信息维护(MDYGXXWH)
 ***** 作  成  者：        姚磊
 ***** 作  成  日：        2011/02/12
 ***** 更新履历：
 ******************************************************************/

class md_models_mdxxwh extends Common_Model_Base {
	/*
	 * 查询 显示门店信息
	 */
	public function getGridData($filter){
	
	$fields = array ("", "MDBH", "MDMCH", "SHFBXYYY", "SHFKZHZDXJ", "DBZDXL","RYZS","YYSCH","DIZHI","PHMS","CKBH","CHJRQ","BEIZHU","YYZHT");
		
	$sql = " SELECT MDBH,MDMCH,DECODE(SHFBXYYY,'1','是','0','否') AS SHFBXYYY,DECODE(SHFKZHZDXJ,'1','是','0','否') AS SHFKZHZDXJ, ".
		   " DBZDXL,RYZS,YYSCH,DIZHI,DECODE(PHMS,'0','精确管理','X','模糊管理') AS PHMS, ".
		   " CKBH,TO_CHAR(CHJRQ,'YYYY-MM-DD') AS CHJRQ  ,BEIZHU ,DECODE(YYZHT,'1','启用','X','禁用') AS YYZHT ".
		   " FROM H01VIEW012520  ".
		   " WHERE QYBH =:QYBH  ";
	
	$bind ['QYBH'] = $_SESSION ['auth']->qybh;
	if ($filter ['searchParams']['SEARCHKEY'] != "") {
			$sql .= " AND( 	MDBH LIKE '%' || :SEARCHKEY || '%'".
			         " OR lower(MDMCH) LIKE '%' || :SEARCHKEY || '%' ".
			         " OR lower(LXDH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['SEARCHKEY']);
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("MD_MDXXWH",$filter['filterParams'],$bind);
		$sql.= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",MDBH";
		
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
	 * 获取门店信息详情
	 */
	public function getMdxx($mdbh,$filter=null,$flg = 'current'){
		//列表查询条件及排序（上一条下一条需按照列表的查询条件和排序为基础）
		//排序用字段名
		$fields = array ("", "MDBH", "MDMCH", "SHFBXYYY", "SHFKZHZDXJ", "DBZDXL","RYZS","YYSCH","DIZHI","PHMS","CKBH","CHJRQ","BEIZHU","YYZHT");
		$sql_list = " SELECT ROWID,LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",MDBH) AS NEXTROWID," . 
		 		     " LAG(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",MDBH) AS PREVROWID" . 
		 		     " ,MDBH".
		   		     " FROM H01VIEW012520  ".
		  		     " WHERE QYBH =:QYBH  ";
		
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;

		if ($filter ['searchParams']['SEARCHKEY']!= "") {
			$sql_list.= " AND( MDBH LIKE '%' || :SEARCHKEY || '%'".
			        " OR lower(MDMCH) LIKE '%' || :SEARCHKEY || '%'".
			        " OR lower(LXDH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['SEARCHKEY']);
		}
		//自动生成精确查询用Sql
		$sql_list .= Common_Tool::createFilterSql("MD_MDXXWH",$filter['filterParams'],$bind);
		//员工信息单条查询
		$sql_single = " SELECT MDBH,MDMCH,DECODE(SHFBXYYY,'1','是','0','否') AS SHFBXYYY,SHFBXYYY AS SHFBXY ,DECODE(SHFKZHZDXJ,'1','是','0','否') AS SHFKZHZDXJ,SHFKZHZDXJ AS SHFK,".
		   " DBZDXL,RYZS,YYSCH,DIZHI,DECODE(PHMS,'0','精确管理','X','模糊管理') AS PHMS,PHMS AS ZHTAI ,".
		   " CKBH,TO_CHAR(CHJRQ,'YYYY-MM-DD') AS CHJRQ  ,BEIZHU ,YYZHT AS YYZT,TO_CHAR(BGRQ,'YYYY-MM-DD hh24:mi:ss') AS BGRQ".
		   " FROM H01VIEW012520  ";
		
		if ($flg == 'current') {
			$sql_single .= "  WHERE QYBH =:QYBH  AND MDBH=:MDBH ";
			unset ( $bind ['SEARCHKEY']);					
		} else if ($flg == 'next') { //下一条		
			$sql_single .= " WHERE ROWID = (SELECT NEXTROWID FROM  (SELECT NEXTROWID,MDBH FROM ( $sql_list ) WHERE MDBH=:MDBH))";
		} else if ($flg == 'prev') { //前一条
			$sql_single .= "WHERE ROWID = (SELECT PREVROWID FROM  (SELECT PREVROWID,MDBH FROM ( $sql_list ) WHERE MDBH=:MDBH ))";
		}
		
		$bind ['MDBH'] = $mdbh;
     	return $this->_db->fetchRow ( $sql_single, $bind );
		
	}
	
	/**
	 * 删除门店信息
	 * @param string $mdbh  门店编号
	 * @return unknown
	 */
	public function deletemdxx($mdbh) {
		
		$sql = "UPDATE H01DB012520 " . " SET YYZHT = '1'" . " WHERE QYBH =:QYBH AND MDBH =:MDBH  ";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['MDBH'] =$mdbh;
		return $this->_db->query ( $sql, $bind );
	
	}

	/**
	 * 修改员工信息保存
	 * @param 
	 */
	public function updateMdxx(){
		
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss'),BGZH FROM H01DB012520 WHERE QYBH = :QYBH AND MDBH = :MDBH FOR UPDATE WAIT 10 ";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'MDBH' => $_POST ['MDBH'] );
		$timestamp = $this->_db->fetchOne ( $sql, $bind );
		
		//时间戳已经变更
		

		if ($timestamp != $_POST ['BGRQ']) {
			return false;
		} else {
			$sql = " UPDATE  H01DB012520 SET " . 
				   " CHJRQ = TO_DATE(:CHJRQ,'YYYY-MM-DD'),". 
				   " MDMCH = :MDMCH," . 
				   " SHFBXYYY = :SHFBXYYY, " . 
				   " SHFKZHZDXJ = :SHFKZHZDXJ, " . 
			 	   " LXDH = :LXDH," .
				   " DBZDXL = :DBZDXL," . 
				   " RYZS = :RYZS," . 
				   " YYSCH = :YYSCH, " . 
				   " CKBH = :CKBH," .
				   " DIZHI = :DIZHI," .
				   " PHMS = :PHMS," .
			       " BEIZHU = :BEIZHU," . 
				   " BGRQ = sysdate, " . 
				   " BGZH = :BGZH " . 
				   " WHERE QYBH = :QYBH AND MDBH=:MDBH ";
					
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['MDBH'] = $_POST ['MDBH']; //门店编号
			$bind ['MDMCH'] = $_POST ['MDMCH']; //门店名称
			$bind ['CHJRQ'] =  $_POST ['CHJRQ']; //创建日期
			$bind ['SHFBXYYY'] = ($_POST ['SHFBXYYY']== null)? '0' : '1'; //是否营业员
			$bind ['SHFKZHZDXJ'] = ($_POST ['SHFKZHZDXJ']== null)? '0' : '1'; //是否控制最低价格
			$bind ['DBZDXL'] = $_POST ['DBZDXL']; //单笔最大销量
			$bind ['LXDH'] = $_POST ['LXDH']; //联系电话
			$bind ['RYZS'] = $_POST ['RYZS']; //人员数目
			$bind ['YYSCH'] = $_POST ['YYSCH']; //营业时长
			$bind ['CKBH'] = $_POST ['CKBH']; //仓房编号
			$bind ['DIZHI'] = $_POST ['DIZHI']; //地址
			$bind ['PHMS'] = $_POST ['PHMS']; //批号管理模式
			$bind ['BEIZHU'] = $_POST ['BEIZHU']; //备注
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$this->_db->query ( $sql, $bind );			
			return true;
		}
		
	}

	/**
	 * 保存门店信息
	 *
	 * @return unknown
	 */
	public function insertMdxx(){
		
			//判断是否员工编号是否存在
		    if ($this->getMdxx( $_POST ['MDBH'] ) != FALSE) {
			return false;
			} else{
		
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['MDBH'] = $_POST ['MDBH']; //门店编号
			$data ['MDMCH'] = $_POST ['MDMCH']; //门店名称
			$data ['CHJRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['CHJRQ'] . "','YYYY-MM-DD')" ); //创建日期
			$data ['SHFBXYYY'] = ($_POST ['SHFBXYYY']==null)? '0' : '1'; //是否营业员
			$data ['SHFKZHZDXJ'] = ($_POST ['SHFKZHZDXJ']==null)? '0' : '1'; //是否控制最低价格
			$data ['DBZDXL'] = $_POST ['DBZDXL']; //单笔最大销量
			$data ['LXDH'] = $_POST ['LXDH']; //联系电话
			$data ['RYZS'] = $_POST ['RYZS']; //人员数目
			$data ['YYSCH'] = $_POST ['YYSCH']; //营业时长
			$data ['CKBH'] = $_POST ['CKBH']; //仓房编号
			$data ['DIZHI'] = $_POST ['DIZHI']; //地址
			$data ['PHMS'] = $_POST ['PHMS']; //批号管理模式
			$data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
			$data ['YYZHT'] =1;//营业状态
			$data ['BEIZHU'] = $_POST ['BEIZHU']; //备注
			$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者

			
					//保存库区信息
					$this->_db->insert ( "H01DB012520", $data );
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
	function updateStatus($mdbh, $yyzht) {
		$sql = "UPDATE H01DB012520 " .
		       " SET YYZHT = :YYZHT" .
		       " WHERE QYBH =:QYBH AND MDBH =:MDBH";
		
		$bind['QYBH'] =$_SESSION ['auth']->qybh;
		$bind['MDBH']= $mdbh;
		$bind['YYZHT'] = $yyzht;
		return $this->_db->query ( $sql, $bind );
	
	}
	
	
}