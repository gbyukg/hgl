<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    采购入库质检(RKZJ)
 * 作成者：ZhangZeliang
 * 作成日：2011/03/22
 * 更新履历：
 *********************************/
class cc_models_rkzj extends Common_Model_Base {
	private $idx_ROWNUM = 0; // 行号
	private $idx_SHPBH = 1; // 商品编号
	private $idx_SHPMCH = 2; // 商品名称
	private $idx_GUIGE = 3; // 规格
	private $idx_PIHAO = 4; // 批号
	private $idx_HWMCH = 5; // 货位
	private $idx_BZHDWM = 6; // 包装单位
	private $idx_BZHSHL = 7; // 包装数量
	private $idx_LSSHL = 8; // 零散数量
	private $idx_SHULIANG = 9; // 数量
	private $idx_KRKSHL = 10; //可入库数量
	private $idx_SHCHRQ = 11; // 生产日期
	private $idx_BZHQZH = 12; // 保质期至
	private $idx_JLGG = 13; // 计量规格
	private $idx_DANJIA = 14; // 单价
	private $idx_HSHJ = 15; // 含税售价
	private $idx_KOULV = 16; // 扣率
	private $idx_SHUILV = 17; // 税率
	private $idx_JINE = 18; // 金额
	private $idx_HSHJE = 19; // 含税金额
	private $idx_SHUIE = 20; // 税额
	private $idx_LSHJ = 21; // 零售价
	private $idx_CHANDI = 22; // 产地
	private $idx_BEIZHU = 23; // 备注
	private $idx_BZHDWBH = 24; // 包装单位编号
	private $idx_ZHDKQLX = 25; // 指定库区类型
	private $idx_KQLXMCH = 26; // 指定库区类型名称
	private $idx_TYMCH = 27; // 通用名称
	private $idx_CKBH = 28; // 仓库编号
	private $idx_KQBH = 29; // 库区编号
	private $idx_KWBH = 30; // 库位编号
	private $idx_SHFSHKW = 31; // 是否散货区
	/*
	 * 得到预采购单列表数据(采购单选择页面)--采购单
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getYcgGridData($filter) {
		//排序用字段名
		$fields = array ("", "A.YRKDBH", "A.CKDBH", "A.KPRQ", "A.DWBH", "NLSSORT(B.DWMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "NLSSORT(C.BMMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "NLSSORT(D.YGXM,'NLS_SORT=SCHINESE_PINYIN_M')", "NLSSORT(A.ZCHZH,'NLS_SORT=SCHINESE_PINYIN_M')" );
		
		//检索SQL
		$sql = "SELECT A.YRKDBH,A.CKDBH,TO_CHAR(A.KPRQ,'YYYY-MM-DD'),A.DWBH,B.DWMCH,C.BMMCH,D.YGXM,E.YGXM AS CZY " . " FROM H01DB012427 A LEFT JOIN H01DB012106 B ON A.QYBH = B.QYBH AND A.DWBH = B.DWBH LEFT JOIN H01DB012112 C ON A.QYBH=C.QYBH AND A.BMBH=C.BMBH LEFT JOIN H01DB012113 D ON A.QYBH=D.QYBH AND A.YWYBH=D.YGBH " . " LEFT JOIN H01DB012113 E ON A.QYBH = E.QYBH AND A.ZCHZH = E.YGBH WHERE A.QYBH = :QYBH AND A.ZHJZHT = '0' ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter ["ksrqkey"] != "" || $filter ["zzrqkey"] != "") {
			$sql .= " AND :KSRQ <= TO_CHAR(A.KPRQ,'YYYY-MM-DD') AND TO_CHAR(A.KPRQ,'YYYY-MM-DD') <= :ZZRQ";
			$bind ['KSRQ'] = $filter ["ksrqkey"] == "" ? "1900-01-01" : $filter ["ksrqkey"];
			$bind ['ZZRQ'] = $filter ["zzrqkey"] == "" ? "9999-12-31" : $filter ["zzrqkey"];
		}
		
		//查询条件(单位编号输入)
		if ($filter ["dwbhkey"] != "") {
			$sql .= " AND A.DWBH = :DWBH";
			$bind ['DWBH'] = $filter ["dwbhkey"];
		}
		
		//查询条件(单位编号没输入,只输入单位名称)
		if ($filter ["dwbhkey"] == "" && $filter ["dwmchkey"] != "") {
			$sql .= " AND E.DWMCH LIKE '%' || :DWMCH || '%'";
			$bind ['DWMCH'] = $filter ["dwmchkey"];
		}
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.CKDBH";
		
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
	 * 得到预采购单列表数据的明细信息(采购单选择页面)--采购单
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getYcgMingxiData($filter) {
		$sql = "SELECT A.SHPBH,B.SHPMCH,B.GUIGE,C.NEIRONG,A.PIHAO,TO_CHAR(A.SHCHRQ,'YYYY-MM-DD'),TO_CHAR(A.BZHQZH,'YYYY-MM-DD'),B.JLGG,A.BZHSHL,A.LSSHL,A.SHULIANG," . "A.DANJIA,A.HSHJ,A.KOULV,B.SHUILV,A.JINE,A.HSHJE,A.SHUIE,B.LSHJ,B.CHANDI,A.BEIZHU " . " FROM H01DB012428 A LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH=B.SHPBH LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND A.SHPBH = B.SHPBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' " . " WHERE A.QYBH=:QYBH ";
		
		//绑定查询条件
		$bind ["QYBH"] = $_SESSION ["auth"]->qybh;
		//与入库单编号条件
		if ($filter ["yrkdbh"] != "") {
			$sql .= "AND A.YRKDBH=:YRKDBH ";
			$bind ["YRKDBH"] = $filter ["yrkdbh"];
		}
		$sql .= "ORDER BY A.XUHAO"; //按序号排序
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.YRKDBH";
		
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
	 * 得到预采购单信息--预采购单
	 *
	 * @param array $filter
	 * @return string array
	 */
	public function getdjinfo($filter) {
		$sql = "SELECT A.CKDBH,A.DWBH,A.DIZHI,A.DHHM,A.SHFZZHSH,A.BEIZHU,A.RKLX,A.ZHJZHT,A.ZCHZH,A.BGZH,B.DWMCH,B.KOULV " . " FROM H01DB012427 A LEFT JOIN H01DB012106 B ON A.QYBH = B.QYBH AND A.DWBH=B.DWBH " . " WHERE A.QYBH=:QYBH AND A.YRKDBH=:YRKDBH";
		
		//绑定查询条件
		$bind ["QYBH"] = $_SESSION ["auth"]->qybh;
		//与入库单编号条件
		$bind ["YRKDBH"] = $filter ["yrkdbh"];
		//$sql.="ORDER BY A.XUHAO";		//按序号排序
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
	/**
	 * 查询预入库单商品详细信息
	 *
	 * @param array $filter
	 * @return string array
	 */
	public function yrkdspmxinfo($filter) {
		$sql = "SELECT B.TYMCH,A.SHPBH,B.SHPMCH,B.GUIGE,C.NEIRONG,A.PIHAO,TO_CHAR(A.SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,"
		. "TO_CHAR(A.BZHQZH,'YYYY-MM-DD') AS BZHQZH,HGL_DEC(B.JLGG) AS JLGG,HGL_DEC(A.BZHSHL) AS BZHSHL,"
		. "HGL_DEC(A.LSSHL) AS LSSHL,HGL_DEC(A.SHULIANG) AS SHULIANG," 
		. "HGL_DEC(A.DANJIA) AS DANJIA,HGL_DEC(A.HSHJ) AS HSHJ,HGL_DEC(A.KOULV) AS KOULV,HGL_DEC(B.SHUILV) AS SHUILV,"
		. "HGL_DEC(A.JINE) AS JINE,HGL_DEC(A.HSHJE) AS HSHJE,HGL_DEC(A.SHUIE) AS SHUIE,HGL_DEC(B.LSHJ) AS LSHJ,"
		. "B.CHANDI,A.BEIZHU,D.CKMCH||E.KQMCH||F.KWMCH AS KWMCH" 
		. " FROM H01DB012428 A LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH=B.SHPBH LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND A.SHPBH = B.SHPBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' " 
		. " LEFT JOIN H01DB012401 D ON A.CKBH = D.CKBH AND A.QYBH = D.QYBH"
		. " LEFT JOIN H01DB012402 E ON A.QYBH = B.QYBH AND A.KQBH = E.KQBH AND A.CKBH = E.CKBH" 
		. " LEFT JOIN H01DB012403 F ON A.CKBH = F.CKBH AND A.QYBH = F.QYBH AND A.KQBH = F.KQBH AND A.KWBH = F.KWBH" 
		. " WHERE A.QYBH=:QYBH AND A.YRKDBH=:YRKDBH";
		
		//绑定与入库单编号参数
		$bind ["YRKDBH"] = $filter ["yrkdbh"];
		//绑定区域编号
		$bind ["QYBH"] = $_SESSION ['auth']->qybh;
		
		return $this->_db->fetchAll ( $sql, $bind );
	}
	
	/**
	 * 必填项验证
	 *
	 * @return boolean
	 */
	public function inputCheck() {
		if ($_POST ["KPRQ"] == '' || //开票日期
$_POST ["BMBH"] == '' || //部门
$_POST ["YWYBH"] == '' || //业务员
$_POST ["#grid_mingxi"] == '' || //明细表格
$_POST ["YRKDH"] == '' || //预入库单编号
$_POST ["CGDBH"] == '') {
			return false;
		}
		
		$isHasMingxi = false; //判断明细信息是否为空
		foreach ( $_POST ['#grid_mingxi'] as $grid ) {
			if ($grid [$this->idx_SHPBH] != '') {
				$isHasMingxi = true;
				if ($grid [$this->idx_KRKSHL] == "") //可入库数量
{
					return false;
				}
			}
		}
		//是否存在明细信息
		if (! $isHasMingxi) {
			return false;
		}
		return true;
	}
	
	/**
	 * 数据合法性逻辑性验证
	 *
	 * @return boolean
	 */
	public function logicCheck() {
		//单位合法性
		$filter ['dwbh'] = $_POST ['DWBH'];
		if ($this->getDanweiInfo ( $filter ) == FALSE) {
			return false;
		}
		return true;
	}
	
	/**
	 * 数据合法性逻辑性验证
	 *
	 * @param  $filter
	 * @return boolean
	 */
	public function getDanweiInfo($filter) {
		$sql = "SELECT A.DWBH,A.DWMCH,A.DIZHI,A.DHHM,HGL_DEC(A.KOULV),A.FHQBH," . "DECODE(A.XSHXDQ,NULL,0,XSHXDQ) XSHXDQ" . //销售信贷期 
" FROM H01DB012106 A" . " WHERE A.QYBH = :QYBH " . //区域编号
" AND A.DWBH = :DWBH" . //单位编号
" AND A.FDBSH ='0'" . //分店标识
" AND A.SHFXSH = '1'" . //是否销售
" AND A.KHZHT = '1'"; //客户状态
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
	/**
	 * 入库质检单信息保存
	 *
	 * @return boolean
	 */
	public function saveRkzj() {
		$rkzj ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$rkzj ['YRKDBH'] = $_POST ['YRKDH']; //预入库单编号
		$rkzj ['CKDBH'] = $_POST ['CGDBH']; //参考单编号
		$rkzj ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$rkzj ['BMBH'] = $_POST ['BMBH']; //部门编号
		$rkzj ['YWYBH'] = $_POST ['YWYBH']; //业务员编号
		$rkzj ['DWBH'] = $_POST ['DWBH']; //单位编号
		$rkzj ['DIZHI'] = $_POST ['DIZHI']; //地址
		$rkzj ['DHHM'] = $_POST ['DHHM']; //电话
		$rkzj ['SHFZZHSH'] = $_POST ['SHFZZHSH']; //是否是增值税
		$rkzj ['KOULV'] = $_POST ['KOULV']; //扣率
		$rkzj ['BEIZHU'] = $_POST ['BEIZHU']; //备注
		$rkzj ['RKLX'] = '1'; //入库类型
		$rkzj ['CGYFHZHT'] = '0'; //采购员复合状态
		$rkzj ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$rkzj ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$rkzj ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$rkzj ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$rkzj["RKZHT"] = '0';

		//插入到数据库
		$this->_db->insert ( "H01DB012429", $rkzj );
	}
	
	/**
	 * 循环保存入库质检单明细信息
	 *
	 */
	public function executeMingxi() {
		$idx_xuhao = 1; //序号
		//循环明细数据窗口
		foreach ( $_POST ['#grid_mingxi'] as $row ) {
			//向入库质检明细表中插入数据
			$this->insertRukumingxi ( $row, $idx_xuhao );
			//序号自增
			$idx_xuhao ++;
		}
	}
	
	/**
	 * 向入库质检明细表中插入数据
	 * 
	 * @parm $row,$idx_xuhao
	 * @return boolean
	 */
	public function insertRukumingxi($row, $idx_xuhao) {
		$rkzjmx ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$rkzjmx ['YRKDBH'] = $_POST ['YRKDH']; //预入库单编号
		$rkzjmx ['XUHAO'] = $idx_xuhao; //序号
		$rkzjmx ['SHPBH'] = $row [$this->idx_SHPBH]; //商品编号
		$rkzjmx ['BZHSHL'] = $row [$this->idx_BZHSHL]; //包装数量
		$rkzjmx ['LSSHL'] = $row [$this->idx_LSSHL]; //零散数量
		$rkzjmx ['SHULIANG'] = $row [$this->idx_SHULIANG]; //数量
		$rkzjmx ['KRKSHL'] = $row [$this->idx_KRKSHL]; //可入库数量
		$rkzjmx ['DANJIA'] = $row [$this->idx_DANJIA]; //单价
		$rkzjmx ['HSHJ'] = $row [$this->idx_HSHJ]; //含税价
		$rkzjmx ['KOULV'] = $row [$this->idx_KOULV]; //扣率
		$rkzjmx ['JINE'] = $row [$this->idx_JINE]; //金额
		$rkzjmx ['HSHJE'] = $row [$this->idx_HSHJE]; //含税金额
		$rkzjmx ['SHUIE'] = $row [$this->idx_SHUIE]; //税额
		$rkzjmx ['BEIZHU'] = $row [$this->idx_BEIZHU]; //备注
		$rkzjmx ['PIHAO'] = $row [$this->idx_PIHAO]; //批号
		if ($row [$this->idx_SHCHRQ] != "") {
			//生产日期
			$rkzjmx ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" );
		}
		if ($row [$this->idx_BZHQZH] != "") {
			//保质日期
			$rkzjmx ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_BZHQZH] . "','YYYY-MM-DD')" );
		}
		$rkzjmx ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
		$rkzjmx ['BGZH'] = $_SESSION ['auth']->userId; //变更者	
		$rkzjmx ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$rkzjmx ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$this->_db->insert ( "H01DB012430", $rkzjmx );
	}
	
	/**
	 * 更新预入库质检信息中的状态信息
	 * 
	 * @return boolean
	 */
	public function updateRkzjZt() {
		//更新语句
		$sql = "UPDATE H01DB012427 SET ZHJZHT = '1' WHERE QYBH = :QYBH AND YRKDBH = :YRKDBH";
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['YRKDBH'] = $_POST ['YRKDH'];
		//更新
		$this->_db->query ( $sql, $bind );
	}
}
?>