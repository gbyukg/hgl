<?php
/*********************************
 * 模块：    采购模块(cg)
 * 机能：    采购确认(CGQR)
 * 作成者：姚磊
 * 作成日：2011/6/9
 * 更新履历：
 *********************************/
class cg_models_cgqr extends Common_Model_Base {

	/*
	 *  采购确认保存
	 */
	public function instercgqr() {
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['CKDBH'] = $_POST ['CGDBH']; //参考单编号
		$data ['JINE'] = $_POST ['JIN_1']; //金额
		$data ['HSHJE'] = $_POST ['HANSUI_1']; //含税金额
		$data ['ZHFJE'] = $_POST ['YFKJE']; //支付金额 = 预付款金额
		$data ['YFJE'] = ( float )($_POST ['HANSUI_1'] - $data ['ZHFJE']); //应付金额 = 含税金额 - 支付金额
		$data ['FKFSH'] ='4';//付款方式
		$data ['JSRQ'] = new Zend_Db_Expr ( 'sysdate' ); //结算日期
		$data ['JIESUANREN'] = $_SESSION ["auth"]->userId; //结算人
		if($data ['YFJE'] =='0'){
		$data ['JSZHT'] = '1'; //结算状态
		}else{
		$data ['JSZHT'] = '2'; //结算状态
		}
		$data ['ZHUANGTAI'] = '1'; //状态

		return $this->_db->insert ( "H01DB012310", $data );
	}

	/*
	 * 采购确认明细获取
	 */
	public function getcgGridData($flg) {
		
		$sql = " SELECT SHPBH,SHPMCH,GUIGE,BZHDW,JLGG,BZHSHL,LSSHL,SHULIANG,DANJIA,HSHJ,KOULV,SHUILV ".
			   " ,HSHJE,JINE,SHUIE,LSHJ ,CHANDI,BEIZHU ".
			   " FROM H01VIEW012307 WHERE QYBH=:QYBH AND CGDBH=:CGDBH ";
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGDBH'] = $flg;
		return $this->_db->fetchAll ( $sql, $bind );
	}

	
	/*
	 * 获取单据编号
	 */
	public function getdjbh($filter){
		
			//排序用字段名
		$fields = array ("", "T1.CGDBH", "KPRQ" );
		//检索SQL
		$sql = "SELECT distinct T1.CGDBH,TO_CHAR(T1.KPRQ,'YYYY-MM-DD') AS KPRQ,T1.BMMCH,T1.DWBH,T1.DWMCH,T1.DIZHI,".
			  " T1.DHHM,TO_CHAR( T1.YDHRQ,'YYYY-MM-DD') AS YDHRQ,TO_CHAR( T1.SHHRQ,'YYYY-MM-DD') AS SHHRQ,".
			  " T1.KOULV,T1.YWYXM ,DECODE(T1.SHFZZHSH,'0','否','1','是') AS SHFZZHSH  " .
		      " FROM H01VIEW012306 T1 LEFT JOIN  H01VIEW012107 T2  ON T1.QYBH = T2.QYBH AND T1.KPYBH = T2.YHID " . 
		      " WHERE T1.QYBH=:QYBH " . //区域编号
              " AND T1.SHPZHT ='1' ". //审批通过
			  " AND T1.CGDZHT ='0'".  //未确认
		      " AND T2.YHID =:YHID "; //用户ID	
//	    if($filter['searchParams']['SEARCHKEY']!=""){
//			$sql .= " AND( DWBH LIKE '%' || :SEARCHKEY || '%'".
//			        "      OR  lower(DWMCH) LIKE '%' || :SEARCHKEY || '%'".
//			        "      OR  lower(ZHJM) LIKE '%' || :SEARCHKEY || '%')";
//			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['SEARCHKEY']);
//		}
//		

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['YHID'] = $_SESSION ["auth"]->userId;
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CG_CGQRXX",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] ;
		//$sql .= " ,T1.CGDBH";
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		return Common_Tool::createXml ( $recs, true, $totalCount, $filter ["posStart"] );

	}
	
	/*
	 * 付款方式取得
	 */
	
	public function fkfs($CGDBH){
		$sql ="SELECT FKFSH FROM H01VIEW012306 WHERE CGDBH=:CGDBH AND QYBH=:QYBH ";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGDBH'] = $CGDBH;
		$rec = $this->_db->fetchRow ( $sql, $bind );
		return $rec;
		
	}
	/*
	 * 获取预付款金额
	 */
	function fkje($CGDBH){
		
		$sql ="SELECT YFKJE FROM H01VIEW012306 WHERE CGDBH=:CGDBH AND QYBH=:QYBH AND FKFSH = '4' ";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGDBH'] = $CGDBH;
		$rec = $this->_db->fetchRow ( $sql, $bind );
		return $rec;
	}
	
	/*
	 * 更新采购确认状态
	 */
	function updatezt($CGDBH){
		
		$sql = "UPDATE H01DB012306 " . " SET CGDZHT = 1" .",CGQRRQ = sysdate ". " WHERE QYBH =:QYBH AND CGDBH =:CGDBH ";
		$bind = array ('CGDBH' => $CGDBH, 'QYBH' => $_SESSION ['auth']->qybh);
		return $this->_db->query ( $sql, $bind );
		
	}
	
	/*
	 * 采购付款方式
	 */
	function instercgfkfs($fkbh){
		
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['DWBH'] = $_POST ['DWBH']; //单位编号
		$data ['FKBH'] = $fkbh; //付款编号
		$data ['SHFJE'] = $_POST ['YFKJE']; //实付金额
		$data ['FKSHJ'] =  new Zend_Db_Expr ( 'sysdate' ); //付款时间	
		$data ['FUKUANREN'] = $_SESSION ["auth"]->userId; //付款人
		$data ['FKFSH'] = '4'; //付款方式,因为只有预付款可以输入付款金额,所以为4	
		return $this->_db->insert ( "H01DB012312", $data );
		
	}
	/*
	 * 采购结算明细
	 */
	function instercgjsmx($fkbh){
		
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['RKDBH'] = $_POST['CGDBH']; //入库单编号
		$data ['FKBH'] = $fkbh; //付款编号
		$data ['ZHFJE'] = $_POST ['YFKJE']; //支付金额
		$data ['FKSHJ'] =  new Zend_Db_Expr ( 'sysdate' ); //付款时间	
		$data ['FUKUANREN'] = $_SESSION ["auth"]->userId; //付款人
		return $this->_db->insert ( "H01DB012311", $data );
		
	}
	
	
	
}
	
	