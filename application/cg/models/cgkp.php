<?php
/*********************************
 * 模块：    采购模块(cg)
 * 机能：    采购开票(CGKP)
 * 作成者：姚磊
 * 作成日：2011/1/20
 * 更新履历：
 *********************************/
class cg_models_cgkp extends Common_Model_Base {

	private $idx_ROWNUM = 0; // 行号
	private $idx_SHPBH = 1; // 商品编号
	private $idx_SHPMCH = 2; // 商品名称
	private $idx_GUIGE = 3; // 商品规格
	private $idx_BZHDWM = 4; // 包装单位
	private $idx_JLGG = 5; // 计量规格
	private $idx_BZHSHL = 6; // 包装数量
	private $idx_LSSHL = 7; // 零散数量
	private $idx_SHULIANG = 8; // 数量
	private $idx_DANJIA = 9; // 单价
	private $idx_HSHJ = 10; // 含税售价
	private $idx_KOULV = 11; // 扣率
	private $idx_SHUILV = 12; // 税率
	private $idx_HSHJE = 13; // 含税金额
	private $idx_JINE = 14; //金额
	private $idx_SHUIE = 15; // 税额
	private $idx_LSHJ = 16; // 零售价
	private $idx_CHANDI = 17; // 产地
	private $idx_BEIZHU = 18; // 备注
	private $idx_TONGYONGMING = 19; // 通用名	
	private $idx_ZDSHULIANG = 20; // 最大入库数量
	private $idx_SHFSHKW = 21; // 是否散货区
	private $idx_BZHDWBH = 22; // 包装单位编号
	private $idx_XUHAO = 23; // 序号
	

	private $idxx_ROWNUM = 0; // 行号
	private $idxx_CGGZHDBH = 1; // 单据编号
	private $idxx_KPRQ = 2; // 开票日期
	private $idxx_DWBH = 3; // 单位编号
	private $idxx_DWMCH = 4; // 单位名称
	private $idxx_BMMCH = 8; // 部门名称
	private $idxx_YWUY = 9; // 业务员
	private $idxx_CZUY = 10; // 操作员
	

	/*
	 *  挂单列表保存
	 */
	public function saveGuadanMain($kphdbh) {
		//$idx = 1; //序号自增
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['CGGZHDBH'] = $kphdbh; //采购挂账单编号
		$data ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$data ['BMBH'] = $_SESSION ["auth"]->bmbh; //部门编号
		$data ['YDHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['YDHRQ'] . "','YYYY-MM-DD')" ); //预到货日期
		$data ['YWYBH'] = $_POST ['YWYBH']; //业务员编号
		$data ['DWBH'] = $_POST ['DWBH']; //单位编号
		$data ['DIZHI'] = $_POST ['DIZHI']; //地址
		$data ['DHHM'] = $_POST ['DHHM']; //电话
		$data ['SHFZZHSH'] = isset($_POST ['SHFZZHSH'])? '1' : '0'; ; //是否增值税
		if($_POST ['FKFSH'] !=''){				//如果选择预付款
			$data ['FKFSH'] = $_POST ['FKFSH']; //付款方式			
		}
		if($_POST ['FKFSH'] == 4){              //如果预付款方式选择预付款
			$data ['YFKJE'] = $_POST ['YFKJE']; //预付款金额
		}
		$data ['KOULV'] = $_POST ['KOULV']; //扣率
		$data ['BEIZHU'] = $_POST ['BEIZHU']; //备注
		$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		//采购挂单信息表
		return $this->_db->insert ( "H01DB012304", $data );
	}
	
	/*
	 *  挂单明细保存
	 */
	public function saveGuadanMingxi($kphdbh) {
		$idx = 1; //序号自增
		//循环所有明细行，保存采购挂单明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['CGGZHDBH'] = $kphdbh; //采购挂账单编号
			$data ['XUHAO'] = $idx ++; //序号
			$data ['SHPBH'] = $grid [$this->idx_SHPBH]; //商品编号
			$data ['BZHSHL'] = ($grid [$this->idx_BZHSHL] == null) ? 0 : $grid [$this->idx_BZHSHL]; //包装数量
			$data ['LSSHL'] = ($grid [$this->idx_LSSHL] == null) ? 0 : $grid [$this->idx_LSSHL]; //零散数量
			$data ['SHULIANG'] = $grid [$this->idx_SHULIANG]; //数量
			$data ['DANJIA'] = $grid [$this->idx_DANJIA]; //单价
			$data ['HSHJ'] = $grid [$this->idx_HSHJ]; //含税价
			$data ['KOULV'] = $grid [$this->idx_KOULV]; //扣率
			$data ['JINE'] = $grid [$this->idx_JINE]; //金额
			$data ['HSHJE'] = $grid [$this->idx_HSHJE]; //含税金额
			$data ['SHUIE'] = $grid [$this->idx_SHUIE]; //税额
			$data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			//采购挂单明细表
			$this->_db->insert ( "H01DB012305", $data );
		}
	}
	
	/*
	 * 采购开票列表保存
	 */
	public function saveCgkpMain($cgkpbh) {
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['CGDBH'] = $cgkpbh; //采购挂账单编号
		$data ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$data ['BMBH'] = $_SESSION ['auth']->bmbh; //部门编号
		$data ['YDHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['YDHRQ'] . "','YYYY-MM-DD')" ); //预到货日期
		$data ['KPYBH'] = $_SESSION ['auth']->userId;  //开票员编号
		$data ['YWYBH'] = $_POST ['YWYBH']; //业务员编号
		$data ['DWBH'] = $_POST ['DWBH']; //单位编号
		$data ['DIZHI'] = $_POST ['DIZHI']; //地址
		$data ['DHHM'] = $_POST ['DHHM']; //电话
		$data ['SHFZZHSH'] = isset($_POST ['SHFZZHSH'])? '1' : '0'; //是否增值税		
		$data ['KOULV'] = $_POST ['KOULV']; //扣率
		$data ['BEIZHU'] = $_POST ['BEIZHU']; //备注		
		if($_POST ['FKFSH'] !=''){				//如果选择预付款
			$data ['FKFSH'] = $_POST ['FKFSH']; //付款方式			
		}
		if($_POST ['FKFSH'] == '4'){              //如果预付款方式选择预付款
			$data ['YFKJE'] = $_POST ['YFKJE']; //预付款金额
		}
		$data ['SHPZHT'] = '0'; //审批状态	
		$data ['QXBZH'] = '1'; //取消标志
		$data ['CGDZHT'] = '0'; //采购单状态
		$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		//采购开票单信息表
		return $this->_db->insert ( "H01DB012306", $data );
	}
	
	/*
	 * 采购开票明细保存
	 */
	public function saveCgkpMingxi($cgkpbh) {
		$idx = 1; //序号自增
		//循环所有明细行，保存销售订单明细
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
			$data ['DANJIA'] = $grid [$this->idx_DANJIA]; //单价
			$data ['HSHJ'] = $grid [$this->idx_HSHJ]; //含税价
			$data ['KOULV'] = $grid [$this->idx_KOULV]; //扣率
			$data ['JINE'] = $grid [$this->idx_JINE]; //金额
			$data ['HSHJE'] = $grid [$this->idx_HSHJE]; //含税金额
			$data ['SHUIE'] = $grid [$this->idx_SHUIE]; //税额
			$data ['BEIZHU'] = $grid [$this->idx_BEIZHU]; //备注
			$data ['RKZHT'] = '1';//未入库
			$data ['QXBZH'] = '1';//取消标志
			$data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			//采购开票单明细表
			$this->_db->insert ( "H01DB012307", $data );
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
		$danweiModel = new gt_models_danwei();
		$filter ['dwbh'] = $_POST ['DWBH'];
		
		if ($danweiModel->getDanweiInfo ( $filter ) == FALSE) {
			return false;
		}
		
		//商品合法性
		$shpModel = new gt_models_shangpin();
		
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == '')
				continue;
			$filter ['shpbh'] = $grid [$this->idx_SHPBH];
			$filter ['flg'] = '1';
			if ($shpModel->getShangpinInfo ( $filter ) == FALSE) {
				return false;
			}
		}
		
		return true;
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
	/**
	 * 得到采购开票订单导入信息列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getDingdanGridData($filter) {
		
		$fields = array ("",  "CGDDBH","KPRQ" ); //
		//检索SQL
		$sql = "SELECT CGDDBH,TO_CHAR(KPRQ,'YYYY-MM-DD'),DWBH,DWMCH,BMMCH,YWYXM AS YGXM,YWYXM AS CZYUN ,
				BMBH,YWYBH,DHHM,YDHRQ,DIZHI,TO_CHAR(YDHRQ,'YYYY-MM-DD'),BEIZHU 
				FROM H01VIEW012301 
				WHERE QYBH =:QYBH  ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
	//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter['searchParams']["SERCHKSRQ"] != "" || $filter['searchParams']["SERCHJSRQ"] != "")
		{
			$sql .= " AND :SERCHKSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD')AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :SERCHJSRQ ";
			$bind ['SERCHKSRQ'] = $filter['searchParams']["SERCHKSRQ"] == ""?"1900-01-01":$filter['searchParams']["SERCHKSRQ"];
			$bind ['SERCHJSRQ'] = $filter['searchParams']["SERCHJSRQ"] == ""?"9999-12-31":$filter['searchParams']["SERCHJSRQ"];
		}	
				
		//serchdwbh 单位编号
		if ($filter['searchParams']["SERCHDWBH"] != "") {
			$sql .= " AND(DWBH LIKE '%' || :SERCHDWBH || '%')";  				//单位编号模糊查询
			$bind ['SERCHDWBH'] = $filter['searchParams']["SERCHDWBH"];
		}
		//serchdwmch 单位名称
		if ($filter['searchParams']["SERCHDWMCH"] != "") {
			$sql .= " AND(DWMCH LIKE '%' || :SERCHDWMCH || '%')"; 		//单位名称模糊查询
			$bind ['SERCHDWMCH'] = $filter['searchParams']["SERCHDWMCH"];
		}

		$sql .= Common_Tool::createFilterSql("CG_CGKPXX_DD",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 必须添加所有主键
		$sql .= " ,CGDDBH";
		
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
	 * 得到采购入账列
	 *
	 * @param array $filter
	 * @return string xml
	 */
	
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ("", "CGGZHDBH","KPRQ" ); //挂账单编号
		

		//检索SQL
		$sql = "SELECT CGGZHDBH,TO_CHAR(KPRQ,'YYYY-MM-DD'),DWBH,DWMCH,BMMCH,YWYXM,".
		" YWYXM AS CZUY,DHHM,TO_CHAR(YDHRQ,'YYYY-MM-DD'),SHFZZHSH ,DIZHI,".
		" BEIZHU,KOULV ,BMBH, YWYBH , FKFSH ,YFKJE ". " FROM H01VIEW012304   " .
		" WHERE QYBH = :QYBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
	//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter['searchParams']["SERCHKSRQ"] != "" || $filter['searchParams']["SERCHJSRQ"] != "")
		{
			$sql .= " AND :SERCHKSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD')AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :SERCHJSRQ ";
			$bind ['SERCHKSRQ'] = $filter['searchParams']["SERCHKSRQ"] == ""?"1900-01-01":$filter['searchParams']["SERCHKSRQ"];
			$bind ['SERCHJSRQ'] = $filter['searchParams']["SERCHJSRQ"] == ""?"9999-12-31":$filter['searchParams']["SERCHJSRQ"];
		}	
				
		//serchdwbh 单位编号
		if ($filter['searchParams']["SERCHDWBH"] != "") {
			$sql .= " AND(DWBH LIKE '%' || :SERCHDWBH || '%')";  				//单位编号模糊查询
			$bind ['SERCHDWBH'] = $filter['searchParams']["SERCHDWBH"];
		}
		//serchdwmch 单位名称
		if ($filter['searchParams']["SERCHDWMCH"] != "") {
			$sql .= " AND(DWMCH LIKE '%' || :SERCHDWMCH || '%')"; 		//单位名称模糊查询
			$bind ['SERCHDWMCH'] = $filter['searchParams']["SERCHDWMCH"];
		}

		$sql .= Common_Tool::createFilterSql("CG_CGKPXX_RZ",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 必须添加所有主键
		$sql .= " ,CGGZHDBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true, $totalCount, $filter ["posStart"] );
	}
	
	/**采购开票订单导入明细
	 * @param array $filter
	 * @return string xml
	 */
	
	public function getDaoruGridData($cggzhdbh, $filter) {
		//排序用字段名
		$fields = array ("", "A.SHPBH" ); //挂账单编号
		

		//检索SQL
		$sql = "SELECT A.SHPBH,B.SHPMCH,B.GUIGE,C.NEIRONG,B.JLGG,A.BZHSHL,A.LSSHL,A.SHULIANG,'0.00' AS DANJIA,".
		 " '0.00' AS HSJ,B.KOULV,B.SHUILV,'0.00' AS HSHJE,'0.00' AS JINE,'0.00' AS SHUE,'0.00' AS LSHJ,B.CHANDI,A.BEIZHU,B.TYMCH " .
		 " FROM H01DB012302 A  LEFT OUTER JOIN H01DB012101 B ON A.QYBH  =B.QYBH AND A.SHPBH = B.SHPBH" . 
		 " LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH   AND B.BZHDWBH=C.ZIHAOMA AND  C.CHLID = 'DW'" . 
		 " LEFT JOIN H01DB012301 D ON A.QYBH=D.QYBH AND A.CGDBH = D.CGDDBH".
		 " WHERE A.QYBH = :QYBH AND A.CGDBH = :CGDBH  AND A.CGDBH = D.CGDDBH  ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGDBH'] = $cggzhdbh;
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 必须添加所有主键
		$sql .= " ,A.SHPBH";
		
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
	 * 采购开票历史价格查询
	 */
	public function getLishiGridData( $filter) {
		
		$fields = array ("",  "KPRQ","CGDBH","DWBH","DWMCH" ); //挂账单编号
		//检索SQL
		$sql = "SELECT TO_CHAR(KPRQ,'YYYY-MM-DD')AS KPRQ,CGDBH,DWBH,DWMCH,SHPBH,SHPMCH,DANJIA ,HSHJ,YWYUN,CZYUN ,TYMCH FROM ( SELECT T2.KPRQ,T1.CGDBH,T2.DWBH,T4.DWMCH,T1.SHPBH,T3.SHPMCH,T1.DANJIA ,T1.HSHJ,T2.YWYUN,T2.CZYUN ,T3.TYMCH ,T1.QYBH " . 
		" FROM H01DB012307 T1 LEFT JOIN  H01DB012101 T3 ON T1.QYBH = T3.QYBH  AND T1.SHPBH = T3.SHPBH  LEFT JOIN " . 
		" (SELECT A.QYBH,A.CGDBH,A.KPRQ,A.DWBH,B.YGXM AS YWYUN,C.YGXM AS CZYUN " . 
		" FROM H01DB012306 A ,H01DB012113 B,H01DB012113 C WHERE A.YWYBH = C.YGBH AND A.YWYBH = B.YGBH AND A.QYBH = B.QYBH AND A.QYBH = C.QYBH
		) T2 ON T1.QYBH = T2.QYBH AND T1.CGDBH = T2.CGDBH " .
		" LEFT JOIN H01DB012106 T4 ON T1.QYBH = T4.QYBH AND T2.DWBH = T4.DWBH )".
		" WHERE QYBH =:QYBH ";
		

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		
	//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter['searchParams']["SERCHKSRQ"] != "" || $filter['searchParams']["SERCHJSRQ"] != "")
		{
			$sql .= " AND :SERCHKSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD')AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :SERCHJSRQ ";
			$bind ['SERCHKSRQ'] = $filter['searchParams']["SERCHKSRQ"] == ""?"1900-01-01":$filter['searchParams']["SERCHKSRQ"];
			$bind ['SERCHJSRQ'] = $filter['searchParams']["SERCHJSRQ"] == ""?"9999-12-31":$filter['searchParams']["SERCHJSRQ"];
		}	
				
		//serchdwbh 单位编号
		if ($filter['searchParams']["SERCHDWBH"] != "") {
			$sql .= " AND(DWBH LIKE '%' || :SERCHDWBH || '%')";  				//单位编号模糊查询
			$bind ['SERCHDWBH'] = $filter['searchParams']["SERCHDWBH"];
		}
		//serchdwmch 单位名称
		if ($filter['searchParams']["SERCHDWMCH"] != "") {
			$sql .= " AND(DWMCH LIKE '%' || :SERCHDWMCH || '%')"; 		//单位名称模糊查询
			$bind ['SERCHDWMCH'] = $filter['searchParams']["SERCHDWMCH"];
		}
		//serchshbh 商品编号
		if ($filter['searchParams']["SERCHSHBH"] != "") {									//商品编号模糊查询
			$sql .= " AND(SHPBH LIKE '%' || :SERCHSHBH || '%')";
			$bind ['SERCHSHBH'] = $filter['searchParams']["SERCHSHBH"];
		}
		//serchshmch 商品名称
		if ($filter['searchParams']["SERCHSHMCH"] != "") {
			$sql .= " AND(SHPMCH LIKE '%' || :SERCHSHMCH || '%')";		//商品名称模糊查询
			$bind ['SERCHSHMCH'] = $filter['searchParams']["SERCHSHMCH"];
		}
		$sql .= Common_Tool::createFilterSql("CG_CGKPXX_LSCX",$filter['filterParams'],$bind);
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 必须添加所有主键
		$sql .= " ,CGDBH";
		
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
	 * 得到采购入账明细信息列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	
	public function getMingxiGridData($cggzhdbh, $filter) {
		//排序用字段名
		$fields = array ("", "", "A.SHPBH" ); //商品编号
		

		//检索SQL
		$sql = "SELECT A.SHPBH,B.SHPMCH,B.GUIGE,C.NEIRONG,B.JLGG,A.BZHSHL,A.LSSHL,A.SHULIANG,".
		 " A.DANJIA,A.HSHJ,A.KOULV,B.SHUILV,A.HSHJE,A.JINE,A.SHUIE,B.LSHJ,B.CHANDI,D.BEIZHU,B.TYMCH " . 
		 " FROM H01DB012305 A  LEFT OUTER JOIN H01DB012101 B ON A.QYBH  =B.QYBH AND A.SHPBH = B.SHPBH  " .
		 " LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH  AND B.BZHDWBH=C.ZIHAOMA AND C.CHLID = 'DW'" .
		 " LEFT JOIN H01DB012304 D ON A.QYBH = D.QYBH  AND A.CGGZHDBH = D.CGGZHDBH " .
		 " WHERE A.QYBH = :QYBH  AND A.CGGZHDBH = :CGGZHDBH";
		;
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGGZHDBH'] = $cggzhdbh;
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 必须添加所有主键
		$sql .= " ,A.SHPBH";
		
			$recs = $this->_db->fetchAll($sql,$bind);
		    return Common_Tool::createXml ( $recs, true );

	}
	/*
	 * 采购入账列表
	 */
	public function getcgGridData($flg) {
		
		$sql = "SELECT A.CGGZHDBH,TO_CHAR(A.YDHRQ,'YYYY-MM-DD'),TO_CHAR(A.KPRQ,'YYYY-MM-DD'),A.DWBH,".
		" A.DWMCH,B.BMMCH,C.YGXM,D.YGXM AS CZUY,A.DHHM,A.BEIZHU,A.DIZHI,A.KOULV,A.SHFZZHSH ,A.FKFSH,A.YFKJE " . 
		" FROM H01DB012304 A  LEFT OUTER JOIN H01DB012112 B ON A.QYBH  =B.QYBH AND A.BMBH = B.BMBH  " . 
		" LEFT JOIN H01DB012113 C ON A.QYBH = C.QYBH   AND A.YWYBH=C.YGBH " . 
		" LEFT JOIN H01DB012113 D ON A.QYBH = D.QYBH   AND A.KPYBH=D.YGBH " . 
		" WHERE A.QYBH = :QYBH AND A.CGGZHDBH = :CGGZHDBH";
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGGZHDBH'] = $flg;
		return $this->_db->fetchRow ( $sql, $bind );
	}
	/*
	 * 删除开票采购数据
	 */
	public function deletecgData($flg) {
		
		$sql = "DELETE H01DB012304 WHERE QYBH = :QYBH AND CGGZHDBH = :CGGZHDBH ";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'CGGZHDBH' => $flg );
		return $this->_db->query( $sql, $bind );
	
	}
	
	/*
	 * Check审批通过
	 */
	function shenpiCheck($dwbh) {
		$result ['status'] = '0';
		$sql = "SELECT SHPTG FROM H01DB012106 WHERE QYBH = :QYBH AND DWBH = :DWBH ";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'DWBH' => $dwbh );
		$spcheck = $this->_db->fetchRow ( $sql, $bind );
		
		if ($spcheck['SHPTG'] == 1) { //如果审批通过
		//$result['status']
		} else {
			$result ['status'] = '02';
		}
		return $result;
	}
	
	/*
	 * Check首营期限
	 */
	function qxCheck($dwbh) {
		$result ['status'] = '0';
		$sql = "SELECT SHPTG FROM H01DB012106 " . " WHERE QYBH = :QYBH AND DWBH = :DWBH " . "
	  			AND SHFJH = 1  AND YYZHZHYXQSHY >= SYSDATE " . "
	   			AND XKZHYXQSHY >= SYSDATE";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'DWBH' => $dwbh );
		$richeck = $this->_db->fetchRow ( $sql, $bind );
		
		if ($richeck != "") {
		
		} else {
			
			$result ['status'] = '01';
		}
		return $result; //返回结果数据数组集合
	}

	/*
	 * Check 商品审批通过
	 */
	function spCheck($shpbh) {
		$result ['status'] = '0';
			$sql = " SELECT SHPTG FROM H01DB012101 WHERE QYBH = :QYBH AND SHPBH = :SHPBH ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] = $shpbh;
			$spcheck = $this->_db->fetchRow ( $sql, $bind );
			if ($spcheck['SHPTG'] == '1') { //如果审批通过
			$result ['status'] = '0';
			} else {
				$result ['status'] = '04';
			}
			return $result;
	
	}
	/*
	 * Check 首营商品期限
	 */
	function spqxCheck($shpbh) {
		$result ['status'] = '0';

			$sql = "SELECT SHPBH FROM H01DB012101 " . " WHERE QYBH = :QYBH AND SHPBH = :SHPBH " . " AND PZHWHYXQ >= SYSDATE ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] = $shpbh;
			$richeck = $this->_db->fetchRow ( $sql, $bind );
			
			if ($richeck != "") {			
			} else {				
				$result ['status'] = '03';				
			}
		return $result; //返回结果数据数组集合
	}
	
	/*
	 * Check 商品最大采购数量
	 */
	public function shifMax($shpbh,$shuliang) {
		$result ['status'] = '0';
				//取得即时商品最大采购数量
			$sql = "SELECT RKXZHSHL FROM H01DB012101 " . " WHERE QYBH = :QYBH AND SHPBH =:SHPBH ";
			//绑定查询变量
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] = $shpbh;
			
			//当前明细行在库信息
			$recs = $this->_db->fetchOne ( $sql, $bind );
			$shuliang_zuida = $recs;
			if(( int )$shuliang_zuida > 0){
				
			}else{
			if (( int )$shuliang_zuida < ( int ) $shuliang) {
				$result ['status'] = '06'; //超过最大采购数量
		}
			}
		return $result;
	}
	
	/*
	 * Check 最大采购价格
	 */
	
	function jgMax() {
		$result ['status'] = '0';
		foreach ( $_POST ["#grid_mingxi"] as $row ) {
			if ($row [$this->idx_SHPBH] == '')
				continue;
			
			$sql = "SELECT MAX(DANJIA) FROM H01DB012407 " . " WHERE QYBH = :QYBH AND SHPBH =:SHPBH "; //单价
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] = $row [$this->idx_SHPBH];
			$recs = $this->_db->fetchOne ( $sql, $bind );
			$jine_zuida = 0;
			if ($recs == "") {
				//针对初次采购价格的处理，暂时未确定，需与客户商定处理办法。。。。。
			} else {
				$jine_zuida = $recs;
				if ($jine_zuida < ( int ) $row [$this->idx_DANJIA]) {
					$result ['status'] = '05'; //超过最大采购价格
					$result['SHPBH']= $row [$this->idx_SHPBH];
				}
			}
		
		}
		return $result;
	
	}
	
	/*
	 * Check 商品是否是有指定供应商
	 */
	
	function spyxCheck($shpbh) {
		$result ['status'] = '0';
			$sql = "SELECT SHPBH FROM H01DB012103 " . " WHERE QYBH = :QYBH AND SHPBH =:SHPBH ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] = $shpbh;
			$recs = $this->_db->fetchOne ( $sql, $bind );
			if ($recs != "") {
				$result ['status'] = '30';//有指定供应商
			} else {				
						//没有指定供应商，什么都不做
		}
		return $result;
	
	}
	
	/*
	 * 商品是否是指定供应商
	 */
	function gysCheck($shpbh) {
		$dwbh = $_POST ['DWBH'];
		$result ['status'] = '0';
			$sql = "SELECT SHPBH,DWBH FROM H01DB012103 " . " WHERE QYBH = :QYBH AND SHPBH =:SHPBH AND DWBH =:DWBH ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] = $shpbh;
			$bind ['DWBH'] = $dwbh;
			
			$recs = $this->_db->fetchOne ( $sql, $bind );
			if ($recs != "") {
				$result ['status'] = '40';				//此单位是指定商品的供应商
			} else {
				$result ['status'] = '08'; 			//此单位不是指定商品的供应商
				
		}
			return $result;
	}
	
	/*
	 * 当前单位是否是此商品的最优供应商
	 */
	function danwCheck($shpbh) {
		
		$dwbh = $_POST ['DWBH'];
		$result ['status'] = '0';
		//此商品的最大优先级MAX(YOUXIANJI)
		$sql = "SELECT MAX(YOUXIANJI) FROM H01DB012103 " . " WHERE QYBH = :QYBH AND SHPBH =:SHPBH";		
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['SHPBH'] = $shpbh;	
		$recs = $this->_db->fetchOne ( $sql, $bind );
		//此单位的优先级
		$sql_list = "SELECT YOUXIANJI FROM H01DB012103 " . " WHERE QYBH = :QYBH AND SHPBH =:SHPBH AND DWBH =:DWBH";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['SHPBH'] = $shpbh;
		$bind ['DWBH'] = $dwbh;		
		$rec = $this->_db->fetchOne ( $sql_list, $bind );
		if (( int )$recs <=( int )$rec) {	
			//此单位不是商品的最优供应商
		} else {
			$result ['status'] = '07'; //此单位不是商品的最优供应商
		}
		return $result; //单位是是指定供应商

	}
	/*
	 * 采购开票check信息保存审批
	 */
	function errorSave($cgkpbh,$filter) {
		$idx = 1; //序号自增	
		$fields = array ();
		$fields_1 = array ();
		foreach($filter as $key =>$value)
		$fields = explode("*",$filter ['errormeg']);
		$fields_1 = explode("*",$filter ['error']);
		$arr = array_combine($fields,$fields_1);
		foreach($arr as $key =>$value){	
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号		
		$data ['XUHAO'] = $idx ++; //序号
		$data ['CGDBH'] = $cgkpbh; //采购挂账单编号
		$data ['SHPYY'] = $key; //审批原因
		$data ['SHPYYFL'] = $value; //审批原因分类
		$data ['SHPZHT'] = 0; //审批状态
		$data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$this->_db->insert ( "H01DB012303", $data );

	}
	}
	/*
	 * auto 业务员信息
	 */
	function getData($filter){
		
		$sql = " SELECT T1.YGBH,T2.YGXM  FROM H01DB012110 T1 , H01DB012113 T2 " . " WHERE T1.QYBH = T2.QYBH AND T1.QYBH =:QYBH AND T1.DWBH =:DWBH AND".
			   " T1.YGQF = 'C' AND T2.SHFCGY ='1' AND T1.YGBH = T2.YGBH AND T2.YGZHT = '1'";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;			
			$bind ['DWBH'] = $filter ['dwbh'];
			//return $this->_db->fetchRow ( $sql, $bind );
			$quer = $this->_db->fetchALL ( $sql, $bind );
			$cnt = count($quer);
			if($cnt !='1'){
				return FALSE;
			}else{
				return $quer;
			}
	}

	/*
	 * 设置返回订单导入数据表单
	 */

	public function fhdata($cggzhdbh){
		
		$sql =" SELECT CGDDBH,TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ,DWBH,DWMCH,BMMCH,YWYXM AS YGXM,YWYXM AS CZYUN ,
				BMBH,YWYBH,DHHM,YDHRQ,DIZHI,TO_CHAR(YDHRQ,'YYYY-MM-DD') AS YDHRQ,BEIZHU ,SHFZZHSH
				FROM H01VIEW012301 
				WHERE QYBH =:QYBH AND CGDDBH =:CGDDBH ";
		
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGDDBH'] = $cggzhdbh;
		return $this->_db->fetchRow ( $sql, $bind );
	}

}
	
	