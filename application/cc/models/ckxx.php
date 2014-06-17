<?php

/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：       仓库信息(ckxx)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2010/11/11
 ***** 更新履历：
 ******************************************************************/

class cc_models_ckxx extends Common_Model_Base {
	
	/**
	 * 得到仓库列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ("","CKZHT", "CKBH", "CKMCH", "LXDH", "YZHBM", "DIZHI", "RQ", "BGZH"); //编号，姓名，地址，联系电话，邮政编码，仓库状态, 变更日期, 变更者
		//检索SQL
		$sql = "SELECT DECODE(CKZHT,'0','冻结','1','正常','禁用') AS CKZHT,CKBH,CKMCH,LXDH,YZHBM,DIZHI,to_char(BGRQ,'yyyy-mm-dd') AS RQ,BGZHXM FROM H01VIEW012401 WHERE QYBH = :QYBH ";

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件  编号或名称
		if($filter['searchParams']['CKBH']!=""){
			$sql .= " AND( CKBH LIKE '%' || :SEARCHKEY || '%'".
			        " OR  lower(CKMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['CKBH']);
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_CKXX",$filter['filterParams'],$bind);;
		
		//排序
		$sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .=",CKBH";
		
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
	 * 取得仓库信息
	 *
	 * @param string $ckbh   仓库编号
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	function getCkxx($ckbh, $filter, $flg = 'current') {
		//排序用字段名
		$fields = array ("","CKZHT", "CKBH", "CKMCH", "LXDH", "YZHBM", "DIZHI", "RQ", "BGZH"); //编号，姓名，地址，联系电话，邮政编码，仓库状态, 变更日期, 变更者
		//检索SQL
		$sql_list = "SELECT CKBH,ROWID,LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",CKBH) AS NEXTROWID,".
		            "             LAG(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",CKBH) AS PREVROWID".
					" FROM H01VIEW012401 ".
					" WHERE QYBH = :QYBH ";

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件  编号或名称
		if($filter['searchParams']['CKBH']!=""){
			$sql_list .= " AND( CKBH LIKE '%' || :SEARCHKEY || '%' OR  lower(CKMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['CKBH']);
		}
		
		//自动生成精确查询用Sql
		$sql_list .= Common_Tool::createFilterSql("CC_CKXX",$filter['filterParams'],$bind);
		
		//检索SQL
		$sql = "SELECT CKBH,CKMCH,LXDH,YZHBM,CKZHT,DIZHI,TO_CHAR(BGRQ,'yyyy-mm-dd hh24:mi:ss') AS RQ,BGZH FROM H01DB012401 ";
		if ($flg == 'current') {
			$sql .= " WHERE  QYBH =:QYBH AND CKBH =:CKBH";
		} else if ($flg == 'next') {
			$sql .= "WHERE ROWID =  (SELECT NEXTROWID FROM  (SELECT NEXTROWID,CKBH FROM ( $sql_list ) WHERE CKBH = :CKBH))";
		} else if ($flg == 'prev') {
			$sql .= "WHERE ROWID =  (SELECT PREVROWID FROM  (SELECT PREVROWID,CKBH FROM ( $sql_list ) WHERE CKBH = :CKBH))";
		}
		//绑定查询条件
		$bind['CKBH'] = $ckbh; //当前员工编号
		return $this->_db->fetchRow( $sql , $bind );
	}
	
	
	/**
	 * 查找对应库区的状态信息
	 * @param string $ckbh   仓库编号
	 * @param string $kqzht  查找库区的状态 0：冻结；1：可用；X：删除
	 * @return bool
	 */
	function getkqstatus( $ckbh, $kqzht ){
		if($kqzht == '0'){
			$sql = "SELECT COUNT(*) FROM H01DB012402 WHERE CKBH =:CKBH AND KQZHT =:KQZHT AND QYBH=:QYBH";
			$bind = array('CKBH' => $ckbh, 'KQZHT' => '1','QYBH' => $_SESSION ['auth']->qybh);
			$temp = $this->_db->fetchOne( $sql, $bind );
			if($temp == 0){
				return true;
			}else{
				return false;
			}
		} else {
			$sql = "SELECT COUNT(*) FROM H01DB012402 WHERE CKBH =:CKBH AND KQZHT !=:KQZHT AND QYBH=:QYBH";
			$bind = array('CKBH' => $ckbh, 'KQZHT' => 'X','QYBH' => $_SESSION ['auth']->qybh);
			$temp = $this->_db->fetchOne( $sql, $bind );
			if($temp == 0){
				return true;
			}else{
				return false;
			}
		}
	}
	
	/**
	 * 生成仓库信息
	 *
	 * @return bool
	 */
	function insertCkxx() {
		
		//判断仓库编号是否存在
		if ($this->getCkxx( $_POST ['CKBH'] ) != FALSE) {
			return false;
		} else {
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['CKBH'] = $_POST ['CKBH']; //仓库编号
			$data ['CKMCH'] = $_POST ['CKMCH']; //仓库名称
			$data ['LXDH'] = $_POST ['LXDH']; //联系电话
			$data ['YZHBM'] = $_POST ['YZHBM']; //邮政编码
			$data ['CKZHT'] = '1';
			$data ['DIZHI'] = $_POST ['DIZHI']; //仓库地址
			$data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			//插入仓库信息表
			$this->_db->insert ( "H01DB012401", $data );
			return true;
		}
	
	}
	
	/**
	 * 更新仓库信息
	 *
	 * @return bool
	 */
	function updateCkxx() {
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') FROM H01DB012401 WHERE QYBH = :QYBH AND CKBH = :CKBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'CKBH' => $_POST ['CKBH'] );
		$timestamp = $this->_db->fetchOne ( $sql, $bind );
		
		//时间戳已经变更
		if ($timestamp != $_POST ['BGRQ']) {
			return false;
		} else {
			$sql = "UPDATE H01DB012401 SET " . " CKMCH = :CKMCH," . " LXDH = :LXDH," . " YZHBM = :YZHBM," .  " DIZHI = :DIZHI," .  " BGRQ = SYSDATE," .  " BGZH = :BGZH" . " WHERE QYBH = :QYBH AND CKBH =:CKBH";			
			$data ['CKMCH'] = $_POST ['CKMCH'];           //仓库名称
			$data ['LXDH'] = $_POST ['LXDH'];             //联系电话
			$data ['YZHBM'] = $_POST ['YZHBM'];           //邮政编码
			//$data ['CKZHT'] = $_POST ['CKZHT'];           //仓库状态
			$data ['DIZHI'] = $_POST ['DIZHI'];           //仓库地址

			$data ['BGZH'] = $_SESSION ['auth']->userId;  //操作用户			
			$data ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
			$data ['CKBH'] = $_POST ['CKBH'];             //仓库编号
			$this->_db->query( $sql, $data );	//***		
			return true;
		}
	}
	
	/**
	 * 更新仓库冻结、解冻、删除状态
	 *
	 * @param string $ckbh  仓库编号
	 * @param string $shyzht 状态
	 * @return unknown
	 */
	function updateStatus($ckbh, $ckzht) {
		$sql = "UPDATE H01DB012401 " . " SET CKZHT = :CKZHT," .  " BGRQ = SYSDATE," .  " BGZH = :BGZH" . " WHERE QYBH =:QYBH AND CKBH =:CKBH";
		$bind = array ('CKZHT' => $ckzht, 'BGZH' => $_SESSION ['auth']->userId, 'QYBH' => $_SESSION ['auth']->qybh, 'CKBH' => $ckbh );
		return $this->_db->query ( $sql, $bind );
	}
}
