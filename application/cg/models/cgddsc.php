<?php
/*********************************
 * 模块：    采购模块(cg)
 * 机能：    采购开票生成(CGKPSC)
 * 作成者：姚磊
 * 作成日：2011/1/12
 * 更新履历：
 *********************************/
class cg_models_cgddsc extends Common_Model_Base {

	private $idx_ROWNUM = 0; // 行号
	private $idx_SHPBH = 1; // 商品编号
	private $idx_SHPMCH = 2; // 商品名称
	private $idx_GUIGE = 3; // 商品规格
	private $idx_BZHDWM = 4; // 包装单位
	private $idx_JLGG = 5; // 计量规格
	private $idx_BZHSHL = 6; // 包装数量
	private $idx_LSSHL = 7; // 零散数量
	private $idx_SHULIANG = 8; // 数量
	private $idx_CHANDI = 9; // 产地
	private $idx_BEIZHU = 10; // 备注
	private $idx_TONGYONGMING = 11; // 通用名	
	private $idx_BZHDWBH = 12; // 包装单位编号
	private $idx_XUHAO = 13; // 序号
	

	
		
	/*
	 * 采购订单生成列表保存
	 */
	public function saveCgkpMain($cgkpbh) {
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['CGDDBH'] = $cgkpbh; //采购挂账单编号
		$data ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$data ['BMBH'] = $_POST ['BMBH']; //部门编号
		$data ['YDHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['YDHRQ'] . "','YYYY-MM-DD')" ); //预到货日期
		$data ['YWYBH'] = $_POST ['YWYBH']; //业务员编号
		$data ['KPYBH'] = $_SESSION ['auth']->userId; //开票员编号
		$data ['DWBH'] = $_POST ['DWBH']; //单位编号
		$data ['DIZHI'] = $_POST ['DIZHI']; //地址
		$data ['DHHM'] = $_POST ['DHHM']; //电话
		$data ['SHFZZHSH'] = isset($_POST ['SHFZZHSH'])? '0' : '1'; ; //是否增值税
		$data ['BEIZHU'] = $_POST ['BEIZHU']; //备注
		$data ['QXBZH'] =  '1'; //取消标志
		$data ['SHHZHT']='0';//审核状态
		$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		//采购开票单信息表
		return $this->_db->insert ( "H01DB012301", $data );
	}
	
	/*
	 * 采购订单生成明细保存
	 */
	public function saveCgkpMingxi($cgkpbh) {
		$idx = 1; //序号自增
		//循环所有明细行，保存采购订单明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == '')
				continue;
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['CGDBH'] = $cgkpbh; //采购挂账单编号
			$data ['XUHAO'] = $idx ++; //序号
			$data ['SHPBH'] = $grid [$this->idx_SHPBH]; //商品编号
			$data ['BZHSHL'] = ($grid [$this->idx_BZHSHL] == null) ? 0 : $grid [$this->idx_BZHSHL]; //包装数量
			$data ['LSSHL'] = ($grid [$this->idx_LSSHL] == null) ? 0 : $grid [$this->idx_LSSHL]; //零散数量
			$data ['SHULIANG'] = $grid [$this->idx_SHULIANG]; //数量
			$data ['BEIZHU'] = $grid [$this->idx_BEIZHU]; //备注			
			$data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			//采购开票单明细表
			$this->_db->insert ( "H01DB012302", $data );
		}
	}
	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck() {
		if ($_POST ["KPRQ"] == "" || //开票日期
			//$_POST ["BMBH"] == "" || //部门编号
			$_POST ["DWBH"] == "" || //单位编号
			$_POST ["DWMCH"] == "" || //单位名称
			$_POST ["YWYBH"] == "" || //业务员编号   
			$_POST ["#grid_mingxi"] == "") { //明细表格
			return false;
		}
		$isHasMingxi = false; //是否存在至少一条明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] != "") {
				$isHasMingxi = true;
				if ($grid [$this->idx_SHULIANG] == "" || //数量
					$grid [$this->idx_SHULIANG] == "0") {
					return false;
				}
			}
		}
		
		//一条明细也没有输入
		if (! $isHasMingxi) {
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
		
		//商品合法性
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == '')
				continue;
			$filter ['shpbh'] = $grid [$this->idx_SHPBH];
			if ($this->getShangpinInfo ( $filter ) == FALSE) {
				return false;
			}
		}
		
		return true;
	}
	
	/*
	 * 根据商品编号取得商品信息
	 */
	public function getShangpinInfo($filter) {
		
		//检索SQL
		$sql = "SELECT " . "A.SHPBH," . //商品编号
			   "A.SHPMCH," . //商品名称
				"A.GUIGE," . //规格
				"A.BZHDWBH," . //包装单位编号
				"A.BZHDWMCH," . //包装单位
				"A.SHOUJIA," . //售价
				"A.HSHSHJ," . //含税售价
				"A.KOULV," . //扣率
				"A.SHUILV," . //税率
				"A.ZGSHJ," . 
				"A.SHPTM," . 
				"A.FLBM," . 
				"A.PZHWH," . 
				"A.JIXINGMCH," . 
				"A.SHCHCHJ," . 
				"C.LSHJ," . //零售价
				"A.CHANDI," . //产地
				"A.JLGG," . //计量规格				
				"C.TYMCH," . //通用名
				"A.XDBZH " . 
				" FROM H01VIEW012001 A " . 
				" LEFT JOIN H01DB012101 C ON A.QYBH = C.QYBH AND A.SHPBH = C.SHPBH".
				" WHERE A.QYBH = :QYBH " .
				 " AND A.SHPBH = :SHPBH " . 
				" AND A.SHPZHT = '1'";
		$sql .= " AND (A.SHFYP = '1' AND A.SHPTG = '1' OR A.SHFYP = '0')";		
		$bind ['SHPBH'] = $filter ['shpbh']; //商品编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		

		return $this->_db->fetchRow ( $sql, $bind );
	}
	
	/*
	 * 根据单位编号编号取得单位信息
	 */
	public function getDanweiInfo($filter) {
		//检索SQL
		$sql = "SELECT A.DWBH,A.DWMCH,A.DIZHI,A.DHHM,A.KOULV,A.FHQBH,DECODE(A.SHFZZHSH,NULL,1,A.SHFZZHSH) SHFZZHSH, " . 
				"DECODE(A.XSHXDQ,NULL,0,XSHXDQ) XSHXDQ" . //销售信贷期 
				" FROM H01DB012106 A" . " WHERE A.QYBH = :QYBH " . //区域编号
				" AND A.DWBH = :DWBH" . //单位编号
				" AND A.FDBSH ='0'" . //分店标识
				" AND A.SHFJH = '1'" . //是否销售
				" AND A.KHZHT = '1'"; //客户状态
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
	/*
	 * 检查信贷期
	 */
	public function checkXdq($filter) {
		$returnValue = 0;
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		
		//销售信贷期
		$sql = "SELECT DECODE(XSHXDQ,NULL,0,XSHXDQ) FROM H01DB012106 WHERE QYBH = :QYBH AND DWBH = :DWBH";
		$xdq = $this->_db->fetchOne ( $sql, $bind );
		//非账期客户
		if ($xdq == 1) {
			$returnValue = 0;
		} else {
			//账期销售单中未结账的最长天数
			$sql = "SELECT floor(SYSDATE - KPRQ) FROM H01DB012201 WHERE QYBH = :QYBH AND DWBH = :DWBH" . 
			" AND QXBZH ='1' AND FKFSH = '1' AND JSZHT = '0' " . 
			" ORDER BY KPRQ ";
			$days = $this->_db->fetchOne ( $sql, $bind );
			
			//账期已超
			if ($days > $xdq) {
				$returnValue = 1;
			}
		}
		
		return $returnValue;
	}
	
	/*
	 * 入库最大数量
	 * SHPBH 商量编号
	 */
	public function getRkxzhshlInfo($filter) {
		$sql = "SELECT JLGG,RKXZHSHL FROM H01DB012101 " . " WHERE QYBH = :QYBH " . "  AND SHPBH = :SHPBH";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['SHPBH'] = $filter ['shpbh'];
		$result = $this->_db->fetchOne ( $sql, $bind );
		return $result;
	}
	

}
	
	
	