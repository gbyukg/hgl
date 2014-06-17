<?php
/*********************************
 * 模块：    销售模块(XS)
 * 机能：    销售开发票(XSKFP)
 * 作成者：苏迅
 * 作成日：2011/06/30
 * 更新履历：
 *********************************/
class xs_models_xskfp extends Common_Model_Base {
	
	private $idx_ROWNUM=0;// 行号
	private $idx_XSHDBH=1;// 销售单编号
	private $idx_KPRQ=2;// 开票日期
	private $idx_DWBH=3;// 单位编号
	private $idx_DWMCH=4;// 单位名称
	private $idx_JINE=5;// 金额
	private $idx_SHUIE=6;// 税额
	private $idx_HSHJE=7;// 含税金额
	private $idx_YKFPJE=8;// 已开发票金额
	private $idx_YKJE=9;// 应开发票金额
	private $idx_BCFPJE=10;// 本次发票金额
	
	/**
	 * 得到销售单据列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter) {
		
		//检索SQL
		$sql = "SELECT A.XSHDBH,TO_CHAR(B.KPRQ,'YYYY-MM-DD') AS KPRQ,B.DWBH,C.DWMCH,HGL_DEC(A.JINE) AS JINE,"
			 . "HGL_DEC(B.SHUIE) AS SHUIE,HGL_DEC(A.HSHJE) AS HSHJE,HGL_DEC(A.YKFPJE) AS YKFPJE,HGL_DEC(A.YKJE) AS YKJE"
			 . " FROM H01DB012211 A LEFT JOIN H01DB012201 B ON A.QYBH = B.QYBH AND A.XSHDBH = B.XSHDBH"
			 . " LEFT JOIN H01DB012106 C ON A.QYBH = C.QYBH AND B.DWBH = C.DWBH"
			 . " WHERE A.QYBH = :QYBH AND A.YKJE > 0 AND B.DWBH = :DWBH ORDER BY A.XSHDBH DESC";
		
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
		
		//检索SQL
		$sql = "SELECT A.SHPBH,B.SHPMCH,B.GUIGE,A.PIHAO,C.NEIRONG,TO_CHAR(A.SHCHRQ,'YYYY-MM-DD'),TO_CHAR(A.BZHQZH,'YYYY-MM-DD'),"
		     . "B.JLGG,HGL_DEC(A.BZHSHL),HGL_DEC(A.LSSHL),HGL_DEC(A.SHULIANG),HGL_DEC(A.DANJIA),HGL_DEC(A.HSHJ),HGL_DEC(A.KOULV),"
		     . "HGL_DEC(B.SHUILV),HGL_DEC(A.JINE),HGL_DEC(A.HSHJE),HGL_DEC(A.SHUIE),HGL_DEC(B.LSHJ),B.CHANDI,A.BEIZHU"
		     . " FROM H01DB012202 A LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH"
		     . " LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' " 
		     . " WHERE A.QYBH = :QYBH AND A.XSHDBH = :XSHDBH ORDER BY A.XUHAO";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['XSHDBH'] = $filter ["xshdbh"];

		//调用表格xml生成函数
		return Common_Tool::createXml($this->_db->fetchAll($sql, $bind));
	}
	
	/**
	 * 保存客户付款信息
	 *
	 * @return 
	 */
	public function saveFpxx() {

		//更新销售发票信息（H01DB012213）
		$this->InsXsfpxx();
				
		//针对单据部分的信息循环处理，更新所有本次发票金额>0的记录。
		foreach ( $_POST ["#grid_danju"] as $row ) {
			
			if($row [$this->idx_BCFPJE] == 0) continue;
			
			//销售订单发票明细信息（H01DB012212）
			$this->InsXsddfpmx($row);
			
			//销售订单发票信息（H01DB012211）	
			$this->UptXsddfpxx($row);
			
		}
	}
	
	/*
	 * 更新采购付款信息（H01DB012213）
	 */
	public function InsXsfpxx() {
		
		$data["QYBH"] = $_SESSION ['auth']->qybh;
		$data["DWBH"] = $_POST ["DWBH"];
		$data["FPBH"] = $_POST["FPBH"];
		$data["FPJE"] = $_POST["FPSKJE"];
		$data["KFPSHJ"] = new Zend_Db_Expr('SYSDATE');
		$data["KFPR"] = $_SESSION ['auth']->userId; //	
		
		$this->_db->insert ( "H01DB012213", $data );
				
	}
	/*
	 * 销售订单发票明细信息（H01DB012212）
	 * 
	 * @param 	array 	$row:明细
	 *
	 */
	public function InsXsddfpmx($row) {
		
		$data['QYBH'] = $_SESSION ['auth']->qybh;
		$data['XSHDBH'] = $row [$this->idx_XSHDBH];
		$data['FPBH'] = $_POST['FPBH'];
		$data['FPJE'] = $row [$this->idx_BCFPJE];
		$data['KFPSHJ'] = new Zend_Db_Expr('SYSDATE');//开发票时间
		$data['KFPR'] = $_SESSION ['auth']->userId; //开发票人
		
		$this->_db->insert ( "H01DB012212", $data );
				
	}
	
	/*
	 * 销售订单发票信息（H01DB012211）	
	 * 
	 * @param 	array 	$row:明细	
	 *
	 */
	public function UptXsddfpxx($row) {
		
		$sql = "UPDATE H01DB012211"
			 . " SET YKJE = YKJE - :BCFPJE,"
			 . "YKFPJE = YKFPJE + :BCFPJE"
			 . " WHERE QYBH = :QYBH AND XSHDBH = :XSHDBH";
		
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['BCFPJE'] = (float)$row [$this->idx_BCFPJE];
		$bind['XSHDBH'] = $row [$this->idx_XSHDBH];
		
		$this->_db->query ( $sql, $bind );
				
	}
	
	/*
	 * 画面必须输入项验证

	 */
	public function inputCheck() {
		if ($_POST ["DWBH"] == "" || $_POST["FPBH"] == "") { //明细表格
            	
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
	
	
	