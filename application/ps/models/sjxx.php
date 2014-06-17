<?php
/*********************************
 * 模块：     配送模块(PS)
 * 机能：     司机信息(sjxx)
 * 作成者：张宇
 * 作成日：2010/11/24
 * 更新履历：

 *********************************/
class ps_models_sjxx extends Common_Model_Base {
	
	/**
	 * 得到司机列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
   public function getGridData($filter) {
		//排序用字段名
		
		$fields = array ("", "ZHUANGTAI", "SJBH", "SJXM" ); //状态，编号，姓名


		//检索SQL
		$sql = "SELECT ".
		                "DECODE(ZHUANGTAI,'X','禁用','1','正常','未知') AS ZHUANGTAI,".
						"SJBH,".
						"SJXM,".
						"JSHZHH,".
						"LXDH,".
						"JTZHZH," . 
		       			"SSHDW,".
		       			"DWDZH,".
		       			"DWDH,".
		       			"BEIZHU," . 
						"TO_CHAR (BGRQ,'yyyy-MM-dd')," . 
						"BGZH" . 
		       " FROM H01VIEW012605" . 
		       " WHERE QYBH = :QYBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件  司机编号
  
		if($filter['searchParams']['SEARCHKEY']!=""){
			$sql .= " AND( SJBH LIKE '%' || :SEARCHKEY || '%'".
					" OR  lower(SJXM) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['SEARCHKEY']);
		}

		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("PS_SJXX",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .=",SJBH";
		
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
	 * 取得司机信息
	 * @param string $sjbh   	司机编号
	 * @param array $filter  	查询条件
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	function getSjxx($sjbh,$filter, $flg = 'current') {
		//列表查询条件及排序（上一条下一条需按照列表的查询条件和排序为基础）

		//排序用字段名
		$fields = array ("", "ZHUANGTAI", "SJBH", "SJXM" ); //状态，编号，姓名

		
		$sql_list = "SELECT ROWID,LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",SJBH) AS NEXTROWID,".
		            "                LAG(ROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] ." ,SJBH) AS PREVROWID,".
						"SJBH,".
						"SJXM,".
						"JSHZHH,".
						"LXDH,".
						"JTZHZH," . 
		       			"SSHDW,".
		       			"DWDZH,".
		       			"DWDH,".
		            	"BEIZHU".
					" FROM H01VIEW012605" .
		            " WHERE QYBH = :QYBH";
		
		
        //绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		//查找条件  司机编号
	    if($filter['searchParams']['SEARCHKEY']!=""){
			$sql_list .= " AND( SJBH LIKE '%' || :SEARCHKEY || '%'".
					" OR  lower(SJXM) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['SEARCHKEY']);
		}
		
		//自动生成精确查询用Sql
		$sql_list .= Common_Tool::createFilterSql("PS_SJXX",$filter['filterParams'],$bind);
		//员工信息单条查询
		$sql_single = "SELECT ".
						"SJBH,".
						"SJXM,".
						"JSHZHH,".
						"LXDH,".
						"JTZHZH," . 
		       			"SSHDW,".
		       			"DWDZH,".
		       			"DWDH,".
		            	"BEIZHU,".
						"BGZH,".
						"to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') AS BGRQ".
					" FROM H01VIEW012605 ";

		
		//当前
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH = :QYBH AND SJBH = :SJBH";
			unset($bind['SEARCHKEY']);

		} else if ($flg == 'next') {//下一条

			$sql_single .= "WHERE ROWID =  (SELECT NEXTROWID FROM  (SELECT NEXTROWID,SJBH FROM ( $sql_list ) WHERE SJBH = :SJBH))";		
		} else if ($flg == 'prev') {//前一条

			$sql_single .= "WHERE ROWID =  (SELECT PREVROWID FROM  (SELECT PREVROWID,SJBH FROM ( $sql_list ) WHERE SJBH = :SJBH))";		
		}
        
		
		$bind['SJBH'] = $sjbh; //当前员工编号
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
/**
	 * 生成司机信息
	 *
	 * @return bool
	 */
	function insertSjxx() {
		
		//判断是否司机编号是否存在
		if ($this->getSjxx( $_POST ['SJBH'] ) != FALSE) {
			return false;
		} else {
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['SJBH'] = $_POST ['SJBH']; //司机编号
			$data ['SJXM'] = $_POST ['SJXM']; //司机姓名
			$data ['JSHZHH'] = $_POST ['JSHZHH']; //驾驶证号
			$data ['LXDH'] = $_POST ['LXDH']; //联系电话
			$data ['JTZHZH'] = $_POST ['JTZHZH']; //家庭住址
			$data ['SSHDW'] = $_POST ['SSHDW']; //所属单位

			$data ['DWDZH'] = $_POST ['DWDZH']; //单位地址
			$data ['DWDH'] = $_POST ['DWDH']; //单位电话
			$data ['ZHUANGTAI'] = '1'; //使用状态可用

			$data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者

					$this->_db->insert ( "H01DB012605", $data );

					return true;

		}
	
	}
	
	/**
	 * 更新司机信息
	 *
	 * @return bool
	 */
function updateSjxx() {
	  
		
		try {
			//开始一个事务

		  $this->_db->beginTransaction();
			//检测时间戳是否发生变动
			$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') FROM H01DB012605 WHERE QYBH = :QYBH AND SJBH = :SJBH FOR UPDATE WAIT 10";
			$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'SJBH' => $_POST ['SJBH'] );
			$timestamp = $this->_db->fetchOne ( $sql, $bind );
			
			//时间戳已经变更

			if ($timestamp != $_POST ['BGRQ']) {
				return false;
			} else {
				$sql = "UPDATE H01DB012605 SET " 
				        . " SJXM = :SJXM,"
						. " JSHZHH = :JSHZHH,"
						. " LXDH = :LXDH,"
						. " JTZHZH = :JTZHZH,"
						. " SSHDW = :SSHDW,"
						. " DWDZH = :DWDZH,"
						. " DWDH = :DWDH,"
						. " BEIZHU = :BEIZHU,"
						. " BGRQ = SYSDATE," 
						. " BGZH = :BGZH"
				        ." WHERE QYBH = :QYBH AND SJBH =:SJBH";
			
				$bind ['SJXM'] = $_POST ['SJXM']; //司机姓名
				$bind ['JSHZHH'] = $_POST ['JSHZHH']; //驾驶证号
				$bind ['LXDH'] = $_POST ['LXDH']; //联系电话
				$bind ['JTZHZH'] = $_POST ['JTZHZH']; //家庭住址
				$bind ['SSHDW'] = $_POST ['SSHDW']; //所属单位

				$bind ['DWDZH'] = $_POST ['DWDZH']; //单位地址
				$bind ['DWDH'] = $_POST ['DWDH']; //单位电话
				$bind ['BEIZHU'] = $_POST ['BEIZHU']; //备注
				$bind ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
				$bind ['SJBH'] = $_POST ['SJBH']; //司机编号
	
				$this->_db->query ( $sql, $bind );
				$this->_db->commit();
				return true;
			}
		}
		catch (Exception  $ex)
		{
			$this->_db->rollBack();
			throw $ex;
		}
	}
	
	
	
	/**
	 * 司机锁定和解锁

	 *
	 * @param string $ygbh  司机编号
	 * @param string $shyzht 状态

	 * @return unknown
	 */
	function updateStatus($sjbh, $zhuangtai) {
		$sql = "UPDATE H01DB012605 " . " SET ZHUANGTAI = :ZHUANGTAI" . " WHERE QYBH =:QYBH AND SJBH =:SJBH";
		
		$bind = array ('ZHUANGTAI' => $zhuangtai, 'QYBH' => $_SESSION ['auth']->qybh, 'SJBH' => $sjbh);
		return $this->_db->query ( $sql, $bind );
	
	}

}