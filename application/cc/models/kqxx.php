<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   库区信息(kqxx)
 * 作成者：姚磊
 * 作成日：2010/11/11
 * 更新履历：
 *********************************/
class cc_models_kqxx extends Common_Model_Base {
	
	/**
	 * 获取仓库名称
	 *
	 * 
	 * @return unknown
	 */
	public function getCangkuList() {
		$sql = "SELECT CKBH,CKMCH FROM H01DB012401 " . "WHERE  QYBH =:QYBH " . " ORDER BY CKBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh );
		$cangkuList = $this->_db->fetchPairs ( $sql, $bind );
		$cangkuList ['0'] = '- - 请 选 择 - -';
		ksort ( $cangkuList );
		return $cangkuList;
	}
	
	/**
	 * 获取库区类型名称
	 *
	 * 
	 * @return unknown
	 */
	public function getKqlxList() {
		$sql = "SELECT ZIHAOMA,NEIRONG FROM H01DB012001" . " WHERE QYBH =:QYBH AND CHLID = 'KQLX'";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh );
		$kqlxList = $this->_db->fetchPairs ( $sql, $bind );
		$kqlxList [''] = '- - 请 选 择 - -';
		ksort ( $kqlxList );
		return $kqlxList;
	
	}
	
	/**
	 * 获取库区名称
	 *
	 * 
	 * @return unknown
	 */
	public function getKuquList($cangku) {
		$sql = "SELECT KQBH,KQMCH FROM H01DB012402";
		$sql .= " WHERE QYBH =:QYBH AND CKBH = " . $cangku . " AND KQZHT = '1' ORDER BY KQBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh );
		$KuquList = $this->_db->fetchPairs ( $sql, $bind );
		
		$KuquList [''] = '- - 请 选 择 - -';
		ksort ( $KuquList );
		return $KuquList;
	
	}
	
	/**
	 * 获取库区类型编号
	 *
	 * 
	 * @return unknown
	 */
	public function getKqblxList() {
		$sql = "SELECT ZIHAOMA,NEIRONG FROM H01DB012001 WHERE QYBH =:QYBH AND CHLID = 'KQLX'";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh );
		$kqblxList = $this->_db->fetchPairs ( $sql, $bind );
		$kqblxList [''] = '- - 请 选 择 - -';
		ksort ( $kqblxList );
		return $kqblxList;
	
	}
	
	/**
	 * 得到库区信息列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ("", "KQZHT", "CKBH", "KQBH" ,"KQMCH","JHSHX","KQLXMCH","BGRQ","BGZHXM"); //状态，仓库编号，库区编号，
		

		//检索SQL
		$sql = "SELECT DECODE(KQZHT,'0','冻结','1','正常','X','禁用') AS KQZHT,CKMCH,KQBH, KQMCH,JHSHX,KQLXMCH,TO_CHAR(BGRQ,'YYYY-MM-DD'),BGZHXM,CKBH 
		      FROM H01VIEW012402 WHERE QYBH = :QYBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件  编号或名称
		if($filter['searchParams']['CKBH']!=""){
			$sql .= " AND( CKBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(CKMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['CKBH']);
		}
		
		if($filter['searchParams']['KQBH']!=""){
			$sql .= " AND( KQBH LIKE '%' || :SEARCHKEYKQBH || '%'".
			        "      OR  lower(KQMCH) LIKE '%' || :SEARCHKEYKQBH || '%')";
			$bind ['SEARCHKEYKQBH'] = strtolower($filter ["searchParams"]['KQBH']);
		}
		
				//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_KQXX",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 必须添加所有主键
		$sql .= " ,CKBH, KQBH";
		
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
	 * 取得库区信息
	 * @param string $ckbh	  仓库编号
	 * @param string &$kqbh  库区编号
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	
	function getKqxx($ckbh, $kqbh, $filter, $flg = 'current') {
		
		//检索SQL
		$fields = array ("", "KQZHT", "CKBH", "KQBH" ); //状态，仓库，库区编号
		$sql_list = "SELECT  KQROWID,LEAD(KQROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",CKBH,KQBH) AS NEXTROWID," . 
		" 						   LAG(KQROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",CKBH,KQBH) AS PREVROWID " . 
		"  ,CKBH, KQBH " .
		 " FROM H01VIEW012402 " .
		" WHERE QYBH = :QYBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件 仓库
			//查找条件  编号或名称
		if($filter['searchParams']['CKBH']!=""){
			$sql_list .= " AND( CKBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(CKMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['CKBH']);
		}
		
		if($filter['searchParams']['KQBH']!=""){
			$sql_list .= " AND( KQBH LIKE '%' || :SEARCHKEYKQBH || '%'".
			        "      OR  lower(KQMCH) LIKE '%' || :SEARCHKEYKQBH || '%')";
			$bind ['SEARCHKEYKQBH'] = strtolower($filter ["searchParams"]['KQBH']);
		}
		
		$sql_list .= Common_Tool::createFilterSql("CC_KQXX",$filter['filterParams'],$bind);
		
		$sql_single = " SELECT DECODE(KQZHT,'0','冻结','1','正常','X','禁用') AS KQZHT,CKMCH,KQBH,KQMCH,JHSHX,KQLXMCH,".
		" TO_CHAR(BGRQ,'YYYY-MM-DD hh24:mi:ss') AS BGRQ,BGZH,CKBH ,KQLX " .
		 " FROM H01VIEW012402 " ;
		
		//当前				
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQBH = :KQBH ";
//			unset ( $bind ['SEARCHKEY'] );
//			unset ( $bind ['SEARCHKEYKQBH'] );
		
		} else if ($flg == 'next') { //下一条		
			$sql_single .= "WHERE KQROWID = (SELECT NEXTROWID FROM  (SELECT NEXTROWID,CKBH,KQBH FROM ( $sql_list ) WHERE CKBH = :CKBH AND KQBH = :KQBH))";
		} else if ($flg == 'prev') { //前一条
			$sql_single .= "WHERE KQROWID = (SELECT PREVROWID FROM  (SELECT PREVROWID,CKBH,KQBH FROM ( $sql_list ) WHERE CKBH = :CKBH AND KQBH = :KQBH))";
		}
		//绑定 区域编号 & 仓库编号 & 库区编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CKBH'] = $ckbh;
		$bind ['KQBH'] = $kqbh;
		
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	
	/**
	 * 生成库区信息
	 *
	 * @return bool
	 */
	function insertKqxx() {
		
		//判断是否库区编号是否存在
		

		if ($this->getKqxx ( $_POST ['CKBH'], $_POST ['KQBH'] ) != FALSE) {
			return false;
		} else {
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['CKBH'] = $_POST ['CKBH']; //仓库编号
			$data ['KQBH'] = $_POST ['KQBH']; //库区编号
			$data ['KQMCH'] = $_POST ['KQMCH']; //库区名称
			$data ['JHSHX'] = $_POST ['JHSHX']; //拣货顺序					
			$data ['KQLX'] = $_POST ['KQLX']; //类型
			$data ['KQZHT'] = '1'; //状态
			$data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; //操作用户					
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		try {
				    //开始一个事务

				    $this->_db->beginTransaction();
					//保存库区信息
					$this->_db->insert ( "H01DB012402", $data );
					$this->_db->commit();
					return true;
					
			}
			catch (Exception $ex) 
			{
				$this->_db->rollBack();
				throw $ex;
			}
				return true;
		}
	}
	/**
	 * 更新库区信息
	 *
	 * @return bool
	 */
	function updateKqxx() {
		try {
			//开始一个事务

		  $this->_db->beginTransaction();
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss'),BGZH FROM H01DB012402 WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQBH = :KQBH ";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'CKBH' => $_POST ['CKBH'], 'KQBH' => $_POST ['KQBH'] );
		$timestamp = $this->_db->fetchOne ( $sql, $bind );
		
		//时间戳已经变更
		

		if ($timestamp != $_POST ['BGRQ']) {
			$_POST ['BGRQ'];
			return false;
		} else {
			$sql = "UPDATE  H01DB012402 SET " . " QYBH = :QYBH," . " CKBH = :CKBH," . " KQBH = :KQBH," . "KQMCH = :KQMCH," . "JHSHX = :JHSHX," . "KQLX = :KQLX," . " BGRQ = sysdate," . " BGZH = :BGZH" . " WHERE QYBH = :QYBH AND CKBH =:CKBH AND KQBH = :KQBH";
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['CKBH'] = $_POST ['CKBH']; //仓库编号
			$bind ['KQBH'] = $_POST ['KQBH']; //库区编号
			$bind ['KQMCH'] = $_POST ['KQMCH']; //库位名称
			$bind ['JHSHX'] = $_POST ['JHSHX']; //拣货顺序
			$bind ['KQLX'] = $_POST ['KQLX']; //库区类型
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			

			$this->_db->query ( $sql, $bind );
			$this->_db->commit();
		
		}
		}catch (Exception  $ex){
			$this->_db->rollBack();
			throw $ex;
		}
			return true;
	}
	
	/**
	 * 库区信息锁定和解锁
	 * @param string $ckbh  仓库编号
	 * @param string $kqbh  库区编号
	 * @param string $kqzht 状态
	 * @return unknown
	 */
	function updateStatus($ckbh, $kqbh, $kqzht) {
		
		$sql = "UPDATE H01DB012402 " . " SET KQZHT = :KQZHT" . " WHERE QYBH =:QYBH AND CKBH =:CKBH AND KQBH = :KQBH ";
		$bind = array ('KQZHT' => $kqzht, 'QYBH' => $_SESSION ['auth']->qybh, 'CKBH' => $ckbh, 'KQBH' => $kqbh );
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
	