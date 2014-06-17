<?php
/*********************************
 * 模块：   配送模块(PS)
 * 机能：  车辆信息(clxx)
 * 作成者：姚磊
 * 作成日：2010/11/25
 * 更新履历：
 *********************************/
class ps_models_clxx extends Common_Model_Base {
	
	/**
	 * 得到车辆信息列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ("", "CHPHM", "CHLXH","","","CHLFL","SHYZHT","","BGRQ" );//车牌号码，车辆型号
		

		//检索SQL
		$sql = "SELECT CHPHM,CHLXH,SJXM,FJSHYBH,DECODE(CHLFL,'1','本公司车辆','2','挂靠车辆') AS CHLFL,DECODE(SHYZHT,'1','启用','X','禁用') AS SHYZHT,BEIZHU,TO_CHAR(BGRQ,'YYYY-MM-DD'),BGZH  
		      FROM H01VIEW012604  WHERE QYBH = :QYBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件  
		if ($filter ['searchParams']["SEARCHKEY"] != "") {
			$sql .= " AND (CHPHM LIKE '%' || : SEARCHKEY || '%'".
			        " OR lower(SJXM) LIKE '%' || : SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ['searchParams']["SEARCHKEY"]);
		}
		
        //自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("PS_CLXX",$filter['filterParams'],$bind);
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= " , chphm ";
		
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
	 * 取得车辆信息
	 *
	 * @param string $chphm   车牌号码
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	
	function getClxx($chphm, $filter=null, $flg = 'current') {
		
		//检索SQL
		$fields = array ("", "CHPHM", "CHLXH","","","CHLFL","SHYZHT","","BGRQ" ); //车牌号码，车辆型号
		$sql_list = "SELECT  ROWID,LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . 
		            $filter ["direction"] . ",CHPHM) AS NEXTROWID," . 
		            "                    LAG(ROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . 
		            $filter ["direction"] . ",CHPHM) AS PREVROWID" . 		   
		            ",CHPHM".
		            " FROM H01VIEW012604 ".
		            " WHERE QYBH=:QYBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		//查找条件  车牌号码
		if ($filter['searchParams']['SEARCHKEY'] != "") {
			$sql_list .=" AND (CHPHM LIKE '%' || :SEARCHKEY || '%'".
			            " OR lower(SJXM) LIKE '%' || : SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] =strtolower($filter ["searchParams"]['SEARCHKEY']);
		}
		//自动生成精确查询用Sql
		$sql_list .= Common_Tool::createFilterSql("JC_YGXX",$filter['filterParams'],$bind);
		//车辆信息单条查询
		$sql_single = "SELECT CHPHM,CHLXH,SJXM,FJSHYBH,CHLFL, SHYZHT,BEIZHU,TO_CHAR(BGRQ,'YYYY-MM-DD hh24:mi:ss') AS BGRQ,BGZH FROM H01VIEW012604 ";
		//当前			
		if ($flg == 'current') {
			$sql_single .= " WHERE  QYBH = :QYBH AND CHPHM = : CHPHM";
			unset ( $bind ['SEARCHKEY'] );
			
		} else if ($flg == 'next') { //下一条
			

			$sql_single .= "WHERE ROWID = (SELECT NEXTROWID FROM  (SELECT NEXTROWID,CHPHM FROM ( $sql_list ) WHERE CHPHM = :CHPHM ))";
		} else if ($flg == 'prev') { //前一条
			$sql_single .= "WHERE ROWID = (SELECT PREVROWID FROM  (SELECT PREVROWID,CHPHM FROM ( $sql_list ) WHERE CHPHM = :CHPHM ))";
		}
		$bind ['CHPHM'] = $chphm;
		
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	
	/**
	 * 更新车辆信息
	 *
	 * @return bool
	 */
	function insertClxx() {
		
		//判断是否车辆号码是否存在
		if ($this->getClxx ( $_POST ['CHPHM'] ) != FALSE) {
			return false;
		} else {
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['CHPHM'] = $_POST ['CHPHM']; //车牌号码
			$data ['CHLXH'] = $_POST ['CHLXH']; //车辆型号
			$data ['SJXM'] = $_POST ['SJXM']; //司机姓名
			$data ['FJSHYBH'] = $_POST ['FJSHYBH']; //副驾驶员					
			$data ['CHLFL'] = $_POST ['CHLFL']; //车辆分类
			$data ['SHYZHT'] = '1'; //使用状态
			$data ['BEIZHU'] = $_POST ['BEIZHU']; //备注
			$data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者			
					//保存库区信息
					$this->_db->insert ( "H01DB012604", $data );
					//$this->_db->commit();
					return true;
					


		
		}
	}
	
	/**
	 * 更新车辆信息
	 *
	 * @return bool
	 */
	function updateClxx() {
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss'),BGZH FROM H01DB012604 WHERE QYBH = :QYBH AND CHPHM = :CHPHM FOR UPDATE WAIT 10  ";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'CHPHM' => $_POST ['CHPHM'] );
		$timestamp = $this->_db->fetchOne ( $sql, $bind );
		//时间戳已经变更
		

		if ($timestamp != $_POST ['BGRQ']) {
			//$_POST ['BGRQ'];
			return false;
		} else {
			$sql = "UPDATE  H01DB012604 SET " . " QYBH = :QYBH," . " CHPHM = :CHPHM," . " CHLXH = :CHLXH," . "SJXM = :SJXM," . "FJSHYBH = :FJSHYBH," . "CHLFL = :CHLFL," . "BEIZHU = :BEIZHU," . " BGRQ = sysdate," . " BGZH = :BGZH" . " WHERE QYBH = :QYBH AND CHPHM = :CHPHM";
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['CHPHM'] = $_POST ['CHPHM']; //车牌号码
			$bind ['CHLXH'] = $_POST ['CHLXH']; //车辆型号
			$bind ['SJXM'] = $_POST ['SJXM']; //司机姓名
			$bind ['FJSHYBH'] = $_POST ['FJSHYBH']; //副驾驶员					
			$bind ['CHLFL'] = $_POST ['CHLFL']; //车辆分类
			$bind ['BEIZHU'] = $_POST ['BEIZHU']; //备注
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			

			$this->_db->query ( $sql, $bind );
			//$this->_db->commit();
			return true;
		}
		
	
	}
	
	/**
	 * 车辆信息锁定和解锁
	 * @param string $chphm  车牌号码
	 * 
	 * @param string $shyzht 状态
	 * @return unknown
	 */
	function updateStatus($chphm, $shyzht) {
		
		$sql = "UPDATE H01DB012604 " . " SET SHYZHT = :SHYZHT" . " WHERE QYBH =:QYBH AND CHPHM = :CHPHM ";
		$bind = array ('SHYZHT' => $shyzht, 'QYBH' => $_SESSION ['auth']->qybh, 'CHPHM' => $chphm );
		return $this->_db->query ( $sql, $bind );
	
	}
	
	/**
	 * 获取车辆信息状态
	 *
	 * @param string $chphm  车牌号码
	 * @return unknown
	 */
	function getShyzht($chphm) {
		
		$sql = "SELECT SHYZHT FROM H01DB012604 WHERE QYBH = :QYBH AND CHPHM = :CHPHM";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'CHPHM' => $chphm );
		$shyzht = $this->_db->fetchOne ( $sql, $bind );
		return $shyzht;
	}
}
	