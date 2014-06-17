<?php
/*********************************
 * 模块：   采购 模块(CG)
 * 机能：    采购结算(CGJS)
 * 作成者：苏迅
 * 作成日：2011/03/04
 * 更新履历：
 *********************************/
class cg_models_cgjs extends Common_Model_Base {
	
	private $idx_ROWNUM=0;// 行号
	private $idx_CKDBH=1;// 参考单编号
	private $idx_KPRQ=2;// 开票日期
	private $idx_FKFSHMCH=3;// 付款方式
	private $idx_FKFSH=4;// 付款方式
	private $idx_DWBH=5;// 单位编号
	private $idx_DWMCH=6;// 单位名称
	private $idx_JINE=7;// 金额
	private $idx_SHUIE=8;// 税额
	private $idx_HSHJE=9;// 含税金额
	private $idx_YZHFJE=10;// 已收取金额
	private $idx_YFJE=11;// 应收金额
	private $idx_BCJSJE=12;// 本次支付金额
	
	/**
	 * 得到采购单据列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter) {
		
		//检索SQL
		$sql = "SELECT A.RKDBH AS CKDBH,TO_CHAR(A.KPRQ,'YYYY-MM-DD') AS KPRQ,DECODE(B.FKFSH,'1','账期','2','现金','3','货到付款','4','预付款','') AS FKFSHMCH,B.FKFSH,A.DWBH,C.DWMCH,HGL_DEC(B.JINE) AS JINE,"
			 . "HGL_DEC(B.HSHJE) AS HSHJE,HGL_DEC(B.HSHJE - B.JINE) AS SHUIE,HGL_DEC(B.ZHFJE) AS ZHFJE,HGL_DEC(B.YFJE) AS YFJE"
			 . " FROM H01DB012406 A LEFT JOIN H01DB012310 B ON A.QYBH = B.QYBH AND A.RKDBH = B.CKDBH LEFT JOIN H01DB012106 C ON A.QYBH = C.QYBH AND A.DWBH = C.DWBH"
			 . " WHERE A.QYBH = :QYBH AND B.JSZHT <> '1' AND B.ZHUANGTAI = '1' AND A.DWBH = :DWBH"
			 . " UNION"
			 . " SELECT A.CGDBH AS CKDBH,TO_CHAR(A.KPRQ,'YYYY-MM-DD') AS KPRQ,DECODE(B.FKFSH,'1','账期','2','现金','3','货到付款','4','预付款','') AS FKFSHMCH,B.FKFSH,A.DWBH,C.DWMCH,HGL_DEC(B.JINE) AS JINE,"
			 . "HGL_DEC(B.HSHJE) AS HSHJE,HGL_DEC(B.HSHJE - B.JINE) AS SHUIE,HGL_DEC(B.ZHFJE) AS ZHFJE,HGL_DEC(B.YFJE) AS YFJE"
			 . " FROM H01DB012306 A LEFT JOIN H01DB012310 B ON A.QYBH = B.QYBH AND A.CGDBH = B.CKDBH LEFT JOIN H01DB012106 C ON A.QYBH = C.QYBH AND A.DWBH = C.DWBH"
			 . " WHERE A.QYBH = :QYBH AND B.JSZHT <> '1' AND B.ZHUANGTAI = '1' AND A.DWBH = :DWBH ORDER BY KPRQ DESC";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ["dwbh"];
		
		return $this->_db->fetchAll( $sql, $bind );
			
	}
	
	/**
	 * 得到采购单列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridMingxiData($filter) {	
		//检索SQL
		$sql = "SELECT A.SHPBH,B.SHPMCH,B.GUIGE,C.NEIRONG,"
		     . "B.JLGG,HGL_DEC(A.BZHSHL),HGL_DEC(A.LSSHL),HGL_DEC(A.SHULIANG),HGL_DEC(A.DANJIA),HGL_DEC(A.HSHJ),HGL_DEC(A.KOULV),"
		     . "HGL_DEC(B.SHUILV),HGL_DEC(A.JINE),HGL_DEC(A.HSHJE),HGL_DEC(A.SHUIE),HGL_DEC(B.LSHJ),B.CHANDI,A.BEIZHU"
		     . " FROM H01DB012307 A LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH"
		     . " LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' " ;
		     
		 if($filter ["fkfsh"] != '4'){
		 	$sql .= " LEFT JOIN H01DB012406 E ON A.QYBH = E.QYBH AND E.CGDBH = A.CGDBH"
				  . " WHERE A.QYBH = :QYBH AND E.RKDBH = :CKDBH ";
		 }else{
		 	$sql .= " WHERE A.QYBH = :QYBH AND A.CGDBH = :CKDBH ";   
		 }
			     
		 $sql .= "ORDER BY A.CGDBH,A.XUHAO";
			     
		 //绑定查询条件
		 $bind ['QYBH'] = $_SESSION ['auth']->qybh;
		 $bind ['CKDBH'] = $filter ["ckdbh"];

		 //调用表格xml生成函数
		 return Common_Tool::createXml($this->_db->fetchAll($sql, $bind));
	}
	
	/**
	 * 保存客户付款信息
	 *
	 * @return 
	 */
	public function saveCgjs($fkbh) {
		$check["status"] = "0";	
		//【结算合计和实付金额不符】
		if((float)$_POST["ZFJE"] != (float)$_POST["BCJSHJ"]){
			$check["status"] = "3";
			return $check;
		}
		//【没有结算项目，请检查本次支付金额输入项！】
		if((float)$_POST["BCJSHJ"] == 0){
			$check["status"] = "4";
			return $check;
		}
		
		//更新采购付款信息
		$this->InsCgfkxx($fkbh);
				
		//针对单据部分的信息循环处理，更新所有本次结算金额>0的记录。
		foreach ( $_POST ["#grid_danju"] as $row ) {
			
			if($row [$this->idx_BCJSJE] == 0) continue;
			
			//更新采购结算明细信息
			$this->InsCgjsmx($row,$fkbh);
			
			//更新采购结算信息	
			$this->UptCgjsxx($row,$fkbh);
			
			//更新采购订单信息
			$this->UptCgjsxxzht($row,$fkbh);
			
		}
				
		return $check;
	}
	
	/*
	 * 更新采购付款信息
	 * 
	 * @param	string 	$fkbh:新生成的客户付款编号	
	 *
	 */
	public function InsCgfkxx($fkbh) {
		
		$data["QYBH"] = $_SESSION ['auth']->qybh;
		$data["DWBH"] = $_POST ["DWBH"];
		$data["FKBH"] = $fkbh;
		$data["SHFJE"] = $_POST["ZFJE"];
		$data["FKFSH"] = "1";	//现款
		$data["FKSHJ"] = new Zend_Db_Expr('SYSDATE');
		$data["FUKUANREN"] = $_SESSION ['auth']->userId; //	
		
		$this->_db->insert ( "H01DB012312", $data );
				
	}
	
	/*
	 * 更新采购结算明细信息
	 * 
	 * @param 	array 	$row:明细
	 * 			string 	$fkbh:新生成的客户付款编号	
	 *
	 */
	public function InsCgjsmx($row,$fkbh) {
		
		$data['QYBH'] = $_SESSION ['auth']->qybh;
		$data['CKDBH'] = $row [$this->idx_CKDBH];
		$data['FKBH'] = $fkbh;
		$data['ZHFJE'] = $row [$this->idx_BCJSJE];
		$data['FKSHJ'] = new Zend_Db_Expr('SYSDATE');//变更日期
		$data['FUKUANREN'] = $_SESSION ['auth']->userId; //变更者
		
		$this->_db->insert ( "H01DB012311", $data );
				
	}
	
	/*
	 * 更新采购结算信息
	 * 
	 * @param 	array 	$row:明细	
	 *
	 */
	public function UptCgjsxx($row) {
		
		$sql = "UPDATE H01DB012310"
			 . " SET YFJE = YFJE - :BCJSJE,"
			 . "ZHFJE = ZHFJE + :BCJSJE,"
			 . "JSRQ = SYSDATE,"
			 . "JIESUANREN = :JIESUANREN"
			 . " WHERE QYBH = :QYBH AND CKDBH = :CKDBH";
		
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['BCJSJE'] = (float)$row [$this->idx_BCJSJE];
		$bind['JIESUANREN'] = $_SESSION ['auth']->userId; //变更者
		$bind['CKDBH'] = $row [$this->idx_CKDBH];
		
		$this->_db->query ( $sql, $bind );
				
	}
	
	/*
	 * 更新采购结算信息状态
	 * 
	 * @param 	array 	$row:明细	
	 *
	 */
	public function UptCgjsxxzht($row) {
		$yue = ( float )$row [$this->idx_YFJE] - ( float )$row [$this->idx_BCJSJE];
		if($yue == 0){
			//已结
			$jszt = '1';
		}else{
			//部分结
			$jszt = '2';
		}
		
		$sql = "UPDATE H01DB012310"
			 . " SET JSZHT = :JSZHT"
			 . " WHERE QYBH = :QYBH AND CKDBH = :CKDBH";
		
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['CKDBH'] = $row [$this->idx_CKDBH];
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
		$sql =  "SELECT A.DWBH,A.DWMCH,A.DIZHI,A.DHHM,HGL_DEC(A.KOULV),A.FHQBH," . "DECODE(A.XSHXDQ,NULL,0,XSHXDQ) XSHXDQ" . //采购信贷期 
			    " FROM H01DB012106 A" . " WHERE A.QYBH = :QYBH " . //区域编号
				" AND A.DWBH = :DWBH" . //单位编号
				" AND A.SHFJH = '1'" . //是否供应商
				" AND A.KHZHT = '1'"; //客户状态

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
}
	
	
	