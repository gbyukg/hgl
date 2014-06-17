<?php

/*********************************
 * 模块：   门店模块(MD)
 * 机能：   门店员工信息(MDYGXX)
 * 作成者：姚磊

 * 作成日：2011/02/09
 * 更新履历：

 *********************************/
class md_models_mdygxx extends Common_Model_Base {
	
	
	
	/**
	 * 取得列表数据
	 *
	 * @param unknown_type $filter
	 * @return unknown
	 */
    public function getGridData($filter) {
		//排序用字段名
	$fields = array ("", "A.YGBH", "B.MDMCH", "", "A.XINGBIE", "","A.CHSHRQ","","","","","A.ZHUANGTAI","A.MDBH","A.BGRQ");

		//检索SQL
	$sql = " SELECT ".
			"A.YGBH,".
			"B.MDMCH,".
			
			"A.YGXM,".
			"DECODE(A.XINGBIE,'0','男','1','女') AS XINGBIE,".
			"A.SHFZHH,".
			"TO_CHAR(A.CHSHRQ,'yyyy-MM-dd') AS CHSHRQ, ".
		    "A.LXDH,".
			"A.YZHBM,".
			"A.TXDZH,".
			"A.EMAIL,".
			"DECODE(A.ZHUANGTAI,'X','禁用','1','正常','未知') AS ZHUANGTAI,".
			"A.MDBH, ".
			"TO_CHAR(A.BGRQ,'yyyy-MM-dd') AS BGRQ ".
			//"TO_CHAR(A.BGRQ,'yyyy-MM-dd') AS BGRQ ".
		    "FROM H01VIEW012509 A LEFT JOIN H01VIEW012520  B ON A.QYBH = B.QYBH AND A.MDBH = B.MDBH ".
		    "WHERE A.QYBH =:QYBH AND A.MDBH=:MDBH ";

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
	    $bind ['MDBH'] = $_SESSION ['auth']->mdbh;
	

		
		if($filter['searchParams']['SEARCHKEY']!=""){
			$sql .= " AND( A.YGBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(A.YGXM) LIKE '%' || :SEARCHKEY || '%'".
			        "       OR  lower(A.LXDH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['SEARCHKEY']);
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("MD_YGXX",$filter['filterParams'],$bind);
		
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
	 * 取得员工信息
	 * @param string $ygbh 员工编号
	 * @param array $filter  查询条件
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	function getYgxx($ygbh,$filter=null, $flg = 'current') {
		//列表查询条件及排序（上一条下一条需按照列表的查询条件和排序为基础）
		//排序用字段名
        $fields = array ("", "A.YGBH", "B.MDMCH", "", "A.XINGBIE", "","A.CHSHRQ","","","","","A.ZHUANGTAI","A.MDBH","A.BGRQ");
		$sql_list =  " SELECT".
		  			 " A.ROWID,".
					 " LEAD(A.ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ", A.YGBH) AS NEXTROWID," . 
		 		     " LAG(A.ROWID)  OVER(ORDER BY " .$fields [$filter ["orderby"]] . " " .$filter ["direction"] . ", A.YGBH) AS PREVROWID ," . 
		 		     " A.YGBH,".
					 " B.MDMCH,".
					 " A.YGXM,".
					 " DECODE(A.XINGBIE,'0','男','1','女') AS XINGBIE,".
					 " A.SHFZHH,".
					 " TO_CHAR(A.CHSHRQ,'YYYY-MM-DD') AS CHSHRQ ," .
					 " A.LXDH,".
		             " A.YZHBM,".
					 " A.TXDZH,".
					 " A.EMAIL,".
					 " DECODE(A.ZHUANGTAI,'X','禁用','1','正常','未知') AS ZHUANGTAI,".
					 " A.MDBH ".
					
				" FROM H01VIEW012509 A LEFT JOIN H01VIEW012520  B ON A.QYBH = B.QYBH AND A.MDBH = B.MDBH " .
				" WHERE A.QYBH =:QYBH AND A.MDBH=:MDBH ";
		
       //绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['MDBH'] = $_SESSION ['auth']->mdbh;
		$bind['YGBH'] = $ygbh; //当前员工编号
	    if($filter['searchParams']['SEARCHKEY']!=""){
		 	$sql_list .= " AND( A.YGBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(A.YGXM) LIKE '%' || :SEARCHKEY || '%'".
			        "       OR  lower(A.LXDH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['SEARCHKEY']);
	     }
		
		//自动生成精确查询用Sql
		$sql_list .= Common_Tool::createFilterSql("MD_YGXX",$filter['filterParams'],$bind);
		

		//员工信息单条查询
		$sql_single =" SELECT ".
					"TO_CHAR(C.DJRQ,'YYYY-MM-DD ') AS DJRQ,".
					"C.YGBH,".
					"D.MDMCH,".
					"C.YGXM,".
					"C.XINGBIE,".
					"C.SHFZHH,".
					"TO_CHAR(C.CHSHRQ,'YYYY-MM-DD') AS CHSHRQ, ".
		   			" C.LXDH,".
					"C.YZHBM,".
					"C.TXDZH,".
					"C.EMAIL,".
					"C.MDBH ,".
					"C.BEIZHU ,".
					"TO_CHAR(C.BGRQ,'YYYY-MM-DD hh24:mi:ss') AS BGRQ ".
		            " FROM H01VIEW012509 C LEFT JOIN H01VIEW012520  D ON C.QYBH = D.QYBH AND C.MDBH = D.MDBH ".
		            " WHERE C.QYBH =:QYBH  AND C.MDBH=:MDBH ";
		
		
		
		//当前
		if ($flg == 'current') {
			$sql_single .= "AND C.YGBH=:YGBH ";
			unset($bind['SEARCHKEY']);
		} else if ($flg == 'next') {//下一条
			$sql_single .= "AND C.ROWID =  (SELECT NEXTROWID FROM  (SELECT NEXTROWID,YGBH FROM ( $sql_list ) WHERE YGBH = :YGBH))";		
		} else if ($flg == 'prev') {//前一条
			$sql_single .= "AND C.ROWID =  (SELECT PREVROWID FROM  (SELECT PREVROWID,YGBH FROM ( $sql_list ) WHERE YGBH = :YGBH))";		
		}
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['MDBH'] = $_SESSION ['auth']->mdbh;
		$bind['YGBH'] = $ygbh; //当前员工编号
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	
	/**
	 * 保存门店员工信息
	 *
	 * @return unknown
	 */
	public function insertMdygxx(){
		
       	if ($this->getYgxx ( $_POST ['YGBH'] ) != FALSE) {
			return false;
			} else{
		
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['YGBH'] = $_POST ['YGBH']; //员工编号
			$data ['MDBH'] = $_POST ['MDBH']; //门店编号
			$data ['DENGLUREN'] = $_POST ['DLRBH']; //登录人编号
			$data ['DJRQ'] = new Zend_Db_Expr ( 'sysdate' ); //登记日期
			$data ['YGXM'] = $_POST ['YGXM']; //员工姓名
			$data ['XINGBIE'] = $_POST ['XINGBIE']; //性别
			$data ['SHFZHH'] = $_POST ['SHFZHH']; //身份证
			if ($_POST ['CHSHRQ'] != "") {
				$data ['CHSHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['CHSHRQ'] . "','YYYY-MM-DD')" ); //出生日期
			}
			$data ['LXDH'] = $_POST ['LXDH']; //联系电话
			$data ['TXDZH'] = $_POST ['TXDZH']; //通讯地址
			$data ['YZHBM'] = $_POST ['YZHBM']; //邮政编码
			$data ['EMAIL'] = $_POST ['EMAIL']; //Email
			$data ['ZHUANGTAI'] = '1';; //状态 0 可用 1，禁止
			$data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
			$data ['BEIZHU'] = $_POST ['BEIZHU']; //备注
			$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者

			//员工信息表
			$this->_db->insert ( "H01DB012509", $data );
			return true;
			
		}
	}
	

	
	

	/**
	 * 修改员工信息保存
	 * @param 
	 */
	public function updateYgxx(){
		
		try {
			//开始一个事务

		$this->_db->beginTransaction();
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss'),BGZH FROM H01DB012509 WHERE QYBH = :QYBH AND YGBH = :YGBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'YGBH' => $_POST ['YGBH'] );
		$timestamp = $this->_db->fetchOne ( $sql, $bind );
		
		//时间戳已经变更
		

		if ($timestamp != $_POST ['BGRQ']) {
				return false;
			} else {
			$sql = " UPDATE  H01DB012509 SET " . 
				   " YGXM = :YGXM," . 
				   " XINGBIE = :XINGBIE, " . 
				   " SHFZHH = :SHFZHH, " . 
			 	   " CHSHRQ = TO_DATE(:CHSHRQ,'YYYY-MM-DD')," .
				   " LXDH = :LXDH, " . 
				   " TXDZH = :TXDZH," . 
				   " YZHBM = :YZHBM," .
				   " EMAIL = :EMAIL, " .
				
			       " BEIZHU = :BEIZHU," . 
				  " BGRQ = SYSDATE, " . 
				 " BGZH = :BGZH " . 
				   " WHERE QYBH = :QYBH AND YGBH =:YGBH AND MDBH=:MDBH ";
			

			
						
			
			//$bind ['DJRQ'] = $_POST ['DJRQ']; //登记日期
			$bind ['YGXM'] = $_POST ['YGXM']; //员工姓名
			$bind ['XINGBIE'] = $_POST ['XINGBIE']; //性别
			$bind ['SHFZHH'] = $_POST ['SHFZHH']; //身份证
			$bind ['CHSHRQ'] = $_POST ['CHSHRQ'] ; //出生日期
			$bind ['LXDH'] = $_POST ['LXDH']; //联系电话
			$bind ['TXDZH'] = $_POST ['TXDZH']; //通讯地址
			$bind ['YZHBM'] = $_POST ['YZHBM']; //邮政编码
			$bind ['EMAIL'] = $_POST ['EMAIL']; //Email
			$bind ['BEIZHU'] = $_POST ['BEIZHU']; //备注
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$bind ['YGBH'] = $_POST ['YGBH'];
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['MDBH'] = $_POST ['MDBH']; //门店编号
			$this->_db->query ( $sql, $bind );
			$this->_db->commit();
			
			return true;
		}
		}catch (Exception  $ex){
			$this->_db->rollBack();
			throw $ex;
		}
	


	
}
	
	
	/**
	 * 员工锁定和解锁
	 *
	 * @param string $ygbh  员工编号
	 * @param string $shyzht 状态
	 * @return unknown
	 */
	function updateStatus($ygbh, $zhuangtai) {
		$sql = "UPDATE H01DB012509 " .
		       " SET ZHUANGTAI = :ZHUANGTAI" .
		       " WHERE QYBH =:QYBH AND YGBH =:YGBH";
		
		$bind['QYBH'] =$_SESSION ['auth']->qybh;
		$bind['YGBH']= $ygbh;
		$bind['ZHUANGTAI'] = $zhuangtai;
		return $this->_db->query ( $sql, $bind );
	
	}


}