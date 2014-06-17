<?php
/*********************************
 * 模块：    销售模块(XS)
 * 机能：    销售收款(XSSK)
 * 作成者：苏迅
 * 作成日：2011/03/04
 * 更新履历：
 *********************************/
class xs_models_xssk extends Common_Model_Base {
	
	private $idx_ROWNUM=0;// 行号
	private $idx_XSHDBH=1;// 销售单编号
	private $idx_KPRQ=2;// 开票日期
	private $idx_DWBH=3;// 单位编号
	private $idx_DWMCH=4;// 单位名称
	private $idx_JINE=5;// 金额
	private $idx_SHUIE=6;// 税额
	private $idx_HSHJE=7;// 含税金额
	private $idx_YSQJE=8;// 已收取金额
	private $idx_YSJE=9;// 应收金额
	private $idx_BCZFJE=10;// 本次支付金额
	
	/**
	 * 得到销售单据列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter) {
		
		//检索SQL
		$sql = "SELECT A.XSHDBH,TO_CHAR(A.KPRQ,'YYYY-MM-DD') AS KPRQ,A.DWBH,C.DWMCH,HGL_DEC(B.JINE) AS JINE,"
			 . "HGL_DEC(A.SHUIE) AS SHUIE,HGL_DEC(B.HSHJE) AS HSHJE,HGL_DEC(B.SHQJE) AS SHQJE,HGL_DEC(B.YSHJE) AS YSHJE"
			 . " FROM H01DB012201 A LEFT JOIN H01DB012208 B ON A.QYBH = B.QYBH AND A.XSHDBH = B.XSHDBH"
			 . " LEFT JOIN H01DB012106 C ON A.QYBH = C.QYBH AND A.DWBH = C.DWBH"
			 . " WHERE A.QYBH = :QYBH AND B.JSZHT <> '1' AND A.DWBH = :DWBH ORDER BY A.XSHDBH DESC";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ["dwbh"];
		
		return $this->_db->fetchAll( $sql, $bind );
			
	}
	
	/**
	 * 得到销售单列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridMingxiData($filter) {
		//排序用字段名
		//$fields = array ("", "A.SHPBH", "NLSSORT(B.SHPMCH,'NLS_SORT=SCHINESE_PINYIN_M')");
		
		//检索SQL
		$sql = "SELECT A.SHPBH,B.SHPMCH,B.GUIGE,A.PIHAO,C.NEIRONG,TO_CHAR(A.SHCHRQ,'YYYY-MM-DD'),TO_CHAR(A.BZHQZH,'YYYY-MM-DD'),"
		     . "B.JLGG,HGL_DEC(A.BZHSHL),HGL_DEC(A.LSSHL),HGL_DEC(A.SHULIANG),HGL_DEC(A.DANJIA),HGL_DEC(A.HSHJ),HGL_DEC(A.KOULV),"
		     . "HGL_DEC(B.SHUILV),HGL_DEC(A.JINE),HGL_DEC(A.HSHJE),HGL_DEC(A.SHUIE),HGL_DEC(B.LSHJ),B.CHANDI,A.BEIZHU"
		     . " FROM H01DB012202 A LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH"
		     . " LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' " 
		     . " WHERE A.QYBH = :QYBH AND A.XSHDBH = :XSHDBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['XSHDBH'] = $filter ["xshdbh"];
				
		//排序
		$sql .= " ORDER BY A.XSHDBH,A.XUHAO";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true, $totalCount, $filter ["posStart"] );		
	}
	
	/**
	 * 得到客户预付款
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getYfk($filter) {	
		//检索SQL
		$sql = "SELECT YFKJE FROM H01DB012106 "
			 . " WHERE QYBH = :QYBH AND DWBH = :DWBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ["dwbh"];
		
		return $this->_db->fetchOne( $sql, $bind );
			
	}
	
	/**
	 * 保存客户付款信息
	 *
	 * @return 
	 */
	public function saveFkxx($khfkbh) {
		$check["status"] = "0";
		$WithCash = true;
		$WithYfk = true;	
		$xuhao = 1;
		//【实收金额不足！】
		if((float)$_POST["SSJE"] + (float)$_POST["YFKYE"] < (float)$_POST["BCZFHJ"]){
			$check["status"] = "3";
			return $check;
		}
		//【没有结算项目，请检查本次支付金额输入项！】
		if((float)$_POST["SSJE"] == 0 && (float)$_POST["BCZFHJ"] == 0){
			$check["status"] = "4";
			return $check;
		}
		//不发生现款结算（本次结算合计为0或者实收金额为0）
		if( (float)$_POST["BCZFHJ"] == 0 || (float)$_POST["SSJE"] == 0){
			$WithCash = false;
		}
		//不发生预付款结算（本次结算合计等于实收金额）
		if( (float)$_POST["BCZFHJ"] == (float)$_POST["SSJE"] ){
			$WithYfk = false;
		}
		//
		if($WithCash){
			$data["QYBH"] = $_SESSION ['auth']->qybh;
			$data["DWBH"] = $_POST ["DWBH"];
			$data["KHFKBH"] = $khfkbh;
			$data["XUHAO"] = $xuhao++;
			$data["SHSHJE"] = ((float)$_POST["SSJE"] <= (float)$_POST["BCZFHJ"])?(float)$_POST["SSJE"]:(float)$_POST["BCZFHJ"];
			$data["FKFSH"] = "1";	//现款
			$data["SHKSJ"] = new Zend_Db_Expr('SYSDATE');
			$data["SHKR"] = $_SESSION ['auth']->userId; //变更者	
					
			$this->_db->insert ( "H01DB012210", $data );
		}
		
		if($WithYfk){
			$data["QYBH"] = $_SESSION ['auth']->qybh;
			$data["DWBH"] = $_POST ["DWBH"];
			$data["KHFKBH"] = $khfkbh;
			$data["XUHAO"] = $xuhao++;
			$data["SHSHJE"] = abs((float)$_POST["SSJE"] - (float)$_POST["BCZFHJ"]);
			$data["FKFSH"] = ((float)$_POST["SSJE"] <= (float)$_POST["BCZFHJ"])?"3":"2";//付款方式  1：现款  2：预付加  3：预付减
			$data["SHKSJ"] = new Zend_Db_Expr('SYSDATE');
			$data["SHKR"] = $_SESSION ['auth']->userId; //变更者	
					
			$this->_db->insert ( "H01DB012210", $data );
			
			$sql = "UPDATE H01DB012106"
			 	 . " SET YFKJE = YFKJE + :YFKJE"
			 	 . " WHERE QYBH = :QYBH AND DWBH = :DWBH";
		
			$bind['QYBH'] = $_SESSION ['auth']->qybh;
			$bind['DWBH'] = $_POST ["DWBH"];
			$bind['YFKJE'] = (float)$_POST["SSJE"] - (float)$_POST["BCZFHJ"];
			
			$this->_db->query ( $sql, $bind );
		}
				
		//针对单据部分的信息循环处理，更新所有本次支付金额>0的记录。
		foreach ( $_POST ["#grid_danju"] as $row ) {
			
			if($row [$this->idx_BCZFJE] == 0) continue;
			
			//更新销售结算明细信息
			$this->InsXsjsmx($row,$khfkbh);
			
			//更新销售结算信息	
			$this->UptXsjsxx($row,$khfkbh);
			
			//更新销售订单信息
			$this->UptXsddxx($row,$khfkbh);
			
		}
				
		return $check;
	}
	
	/*
	 * 更新销售结算明细信息
	 * 
	 * @param 	array 	$row:明细
	 * 			string 	$khfkbh:新生成的客户付款编号	
	 *
	 */
	public function InsXsjsmx($row,$khfkbh) {
		
		$data['QYBH'] = $_SESSION ['auth']->qybh;
		$data['XSHDBH'] = $row [$this->idx_XSHDBH];
		$data['KHFKBH'] = $khfkbh;
		$data['ZHFJE'] = $row [$this->idx_BCZFJE];
		$data['SHKSJ'] = new Zend_Db_Expr('SYSDATE');//变更日期
		$data['SHKR'] = $_SESSION ['auth']->userId; //变更者
		
		$this->_db->insert ( "H01DB012209", $data );
				
	}
	
	/*
	 * 更新销售结算信息
	 * 
	 * @param 	array 	$row:明细	
	 *
	 */
	public function UptXsjsxx($row) {
		
		$sql = "UPDATE H01DB012208"
			 . " SET YSHJE = YSHJE - :BCZFJE,"
			 . "SHQJE = SHQJE + :BCZFJE,"
			 . "JSRQ = SYSDATE,"
			 . "JIESUANREN = :JIESUANREN"
			 . " WHERE QYBH = :QYBH AND XSHDBH = :XSHDBH";
		
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['BCZFJE'] = (float)$row [$this->idx_BCZFJE];
		$bind['JIESUANREN'] = $_SESSION ['auth']->userId; //变更者
		$bind['XSHDBH'] = $row [$this->idx_XSHDBH];
		
		$this->_db->query ( $sql, $bind );
				
	}
	
	/*
	 * 更新销售订单信息
	 * 
	 * @param 	array 	$row:明细	
	 *
	 */
	public function UptXsddxx($row) {
		$yue = ( float )$row [$this->idx_YSJE] - ( float )$row [$this->idx_BCZFJE];
		if($yue == 0){
			//已结
			$jszt = '1';
		}
		if($yue > 0){
			//部分结
			$jszt = '2';
		}
		
		$sql = "UPDATE H01DB012208"
			 . " SET JSZHT = :JSZHT"
			 . " WHERE QYBH = :QYBH AND XSHDBH = :XSHDBH";
		
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['XSHDBH'] = $row [$this->idx_XSHDBH];
		$bind['JSZHT'] = $jszt;
		
		$this->_db->query ( $sql, $bind );
				
	}
	
	/*
	 * 画面必须输入项验证

	 */
	public function inputCheck() {
		if ($_POST ["DWBH"] == "") { //明细表格
            	
			return false;
		}
		return true;
	}
	
	/*
	 * 数据合法性逻辑性验证
	 */
	public function logicCheck() {
		
		//单位合法性
		$filter ['dwbh'] = $_POST ['DWBH'];
		if ($this->getDanweiInfo ( $filter ) == FALSE) {
			return false;
		}
		
		return true;
	}
	
	/*
	 * 根据单位编号编号取得单位信息
	 * 
	 * @param array $filter
	 * @return string array
	 */
	public function getDanweiInfo($filter) {
		//检索SQL
		$sql =  "SELECT A.DWBH,A.DWMCH,A.DIZHI,A.DHHM,HGL_DEC(A.KOULV),A.FHQBH," . "DECODE(A.XSHXDQ,NULL,0,XSHXDQ) XSHXDQ" . //销售信贷期 
			    " FROM H01DB012106 A" . " WHERE A.QYBH = :QYBH " . //区域编号
				" AND A.DWBH = :DWBH" . //单位编号
				" AND A.SHFXSH = '1'" . //是否销售
				" AND A.KHZHT = '1'"; //客户状态

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
}
	
	
	