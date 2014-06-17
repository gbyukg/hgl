<?php
/*********************************
 * 模块：    仓储模块(cc)
 * 机能：    直接入库(ZJRK)
 * 作成者：姚磊
 * 作成日：2010/1/10
 * 更新履历：
 *********************************/
class cc_models_zjrk extends Common_Model_Base {

	private $idx_ROWNUM = 0; // 行号
	private $idx_SHPBH = 1;// 商品编号
	private $idx_SHPMCH = 2;// 商品名称
	private $idx_GUIGE = 3;// 商品规格
	private $idx_BZHDWM = 4;// 包装单位
	private $idx_PIHAO=5;// 批号
	private $idx_HWMCH=6;// 货位
	private $idx_SHCHRQ=7;// 生产日期
	private $idx_BZHQZH=8;// 保质期至
	private $idx_JLGG = 9;// 计量规格
	private $idx_BZHSHL = 10;// 包装数量
	private $idx_LSSHL = 11;// 零散数量
	private $idx_SHULIANG = 12;// 数量
	private $idx_DANJIA = 13;// 单价
	private $idx_HSHJ = 14;// 含税价
	private $idx_KOULV = 15;// 扣率
	private $idx_SHUILV = 16;// 税率
	private $idx_HSHJE = 17;// 含税金额
	private $idx_JINE = 18; // 金额
	private $idx_SHUIE = 19;// 税额
	private $idx_LSHJ = 20; // 零售价
	private $idx_CHANDI = 21;// 产地
	private $idx_BEIZHU = 22;// 备注
	private $idx_TONGYONGMING = 23; // 通用名
	private $idx_KWSHULIANG = 24;// 最大入库数量
	private $idx_BZHDWBH = 25; // 包装单位编号
	private $idx_XUHAO = 26; // 序号
	private $idx_ZHDKQLX=27;// 指定库区类型
	private $idx_KQLXMCH=28;// 指定库区类型名称
	private $idx_SHFSHKW=29;// 是否散货区
	private $idx_CKBH=30;// 仓库编号
	private $idx_KQBH=31;// 库区编号
	private $idx_KWBH=32;// 库位编号
	


		
	/*
	 *直接入库表单保存
	 */
	public function saveCgkpMain($cgkpbh) {
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['CGDBH'] = $cgkpbh; //采购挂账单编号
		$data ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$data ['BMBH'] = $_POST ['BMBH']; //部门编号
		$data ['YWYBH'] = $_POST ['YWYBH']; //业务员编号
		$data ['DWBH'] = $_POST ['DWBH']; //单位编号
		$data ['DIZHI'] = $_POST ['DIZHI']; //地址
		$data ['DHHM'] = $_POST ['DHHM']; //电话
		$data ['SHFZZHSH'] = $_POST ['SHFZZHSH']; //是否增值税
		$data ['KOULV'] = $_POST ['KOULV']; //扣率
		$data ['BEIZHU'] = $_POST ['BEIZHU']; //备注
		$data ['SHPZHT'] = $_POST ['SHPZHT']; //审批状态		
		$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		//采购开票单信息表
		return $this->_db->insert ( "H01DB012306", $data );
	}
	
	/*
	 * 直接入库明细保存
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
			$_POST ["BMBH"] == "" || //部门编号
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
		$sql = "SELECT A.DWBH,A.DWMCH,A.DIZHI,A.DHHM,A.KOULV,A.FHQBH," . "DECODE(A.XSHXDQ,NULL,0,XSHXDQ) XSHXDQ" . //销售信贷期 
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
	/**
	 * 得到采购开票订单信息列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getDingdanGridData($filter) {
		
		$fields = array ("", "", "T1.CGDDBH" ); //购挂账单编号
		//检索SQL
		$sql = "SELECT T1.CGDDBH,TO_CHAR(T1.KPRQ,'YYYY-MM-DD'),T1.DWBH,T4.DWMCH,T3.BMMCH,T2.YGXM AS YGXM,T2.YGXM AS CZYUN ,
				T1.SHFZZHSH,T1.DIZHI,T1.BEIZHU 
				FROM H01DB012301 T1  LEFT JOIN  H01DB012113 T2 ON T1.KPYBH = T2.YGBH  AND T1.YWYBH = T2.YGBH
				LEFT JOIN H01DB012112 T3 ON T1.QYBH  =T3.QYBH AND T1.BMBH = T3.BMBH
				LEFT JOIN H01DB012106 T4 ON T1.QYBH = T4.QYBH AND T1.DWBH = T4.DWBH
				WHERE T1.QYBH =:QYBH  ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		
		//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter ["serchksrq"] != "" || $filter ["serchjsrq"] != "")
		{
			$sql .= " AND :serchksrq <= TO_CHAR(T1.KPRQ,'YYYY-MM-DD') AND TO_CHAR(T1.KPRQ,'YYYY-MM-DD') <= :serchjsrq ";
			$bind ['SERCHKSRQ'] = $filter ["serchksrq"] == ""?"1900-01-01":$filter ["serchksrq"];
			$bind ['SERCHJSRQ'] = $filter ["serchjsrq"] == ""?"9999-12-31":$filter ["serchjsrq"];
		}
		
		if ($filter ["serchdwbh"] != "") {
			$sql .= " AND( T1.DWBH LIKE '%' || :serchdwbh || '%')";
			$bind ['SERCHDWBH'] = $filter ["serchdwbh"];
		}
		
		if ($filter ["serchdwmch"] != "") {
			$sql .= " AND( T4.DWMCH LIKE '%' || :serchdwmch || '%')";
			$bind ['SERCHDWMCH'] = $filter ["serchdwmch"];
		}
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 必须添加所有主键
		$sql .= " ,T1.CGDDBH";
			$recs = $this->_db->fetchAll($sql,$bind);
		    return Common_Tool::createXml ( $recs, true );

	
	}

	
	
	/*
	 * Check首营审批通过
	 */
	function shenpiCheck($dwbh) {
		$result ['status'] = '0';
		$sql = "SELECT SHPTG FROM H01DB012106 WHERE QYBH = :QYBH AND DWBH = :DWBH AND SHFJH ='1'";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'DWBH' => $dwbh );
		$spcheck = $this->_db->fetchRow ( $sql, $bind );
		
		if ($spcheck['SHPTG'] =='1') { //审批通过
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
				$result ['status'] = '0';			
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
		$data ['SHPZHT'] = '0'; //审批状态
		$data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$this->_db->insert ( "H01DB012303", $data );

	}
	}
	
	/*
	 * 更新直接入库信息表单
	 */
	function updateZjrkMain($zjrkbh,$cgkpbh){
				
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['RKDBH'] = $zjrkbh; //入库单编号
		$data ['CKDBH'] = $cgkpbh; //参考单编号
		$data ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$data ['BMBH'] = $_POST ['CANGKUBH']; //仓库部门编号
		$data ['YWYBH'] = $_POST ['YWYNBH']; //仓库业务员编号
		$data ['DWBH'] = $_POST ['DWBH']; //单位编号
		$data ['DIZHI'] = $_POST ['DIZHI']; //地址
		$data ['DHHM'] = $_POST ['DHHM']; //电话
		$data ['SHFZZHSH'] = $_POST ['SHFZZHSH']; //是否增值税
		$data ['KOULV'] = $_POST ['KOULV']; //扣率
		$data ['BEIZHU'] = $_POST ['BEIZHU']; //备注
		$data ['RKLX'] = '3'; //入库类型
		
		$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		//采购开票单信息表
		return $this->_db->insert ( "H01DB012406", $data );
		
	}
	
	/*
	 * 更新直接入库信息明细
	 */
	
	function updateZjrkMingxi($zjrkbh){
				$idx = 1; //序号自增
		//循环所有明细行，保存销售订单明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == '')
				continue;
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['RKDBH'] = $zjrkbh; //直接入库单编号
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
			$data ['PIHAO'] = $grid [$this->idx_PIHAO]; //批号
			if ($grid [$this->idx_SHCHRQ] != ""){
			$data ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" );
			}
			if ($grid [$this->idx_BZHQZH] != ""){
			$data ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM-DD')" );
			}
			$data ['CKBH'] = $grid [$this->idx_CKBH]; //仓库编号
			$data ['KQBH'] = $grid [$this->idx_KQBH]; //库区编号
			$data ['KWBH'] = $grid [$this->idx_KWBH]; //库位编号
			$data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			//采购开票单明细表
			$this->_db->insert ( "H01DB012407", $data );
		
	}
}
	/*
	 * 登陆在库信息同移动履历
	 */	
		 	function LoginZjrkMingxi($zjrkbh){
				
		//循环所有明细行，保存销售订单明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == '')
				continue;
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['CKBH'] = $grid [$this->idx_CKBH]; //仓库编号
			$data ['KQBH'] = $grid [$this->idx_KQBH]; //库区编号
			$data ['KWBH'] = $grid [$this->idx_KWBH]; //库位编号
			$data ['SHPBH'] = $grid [$this->idx_SHPBH]; //商品编号
			$data ['PIHAO'] = $grid [$this->idx_PIHAO]; //批号
			$data ['RKDBH'] = $zjrkbh; //直接入库单编号
			$data ['ZKZHT'] = '0'; //在库状态
			$data ['BZHDWBH'] = $grid [$this->idx_BZHDWBH]; //包装单位编号
			$data ['ZZHCHKRQ'] = new Zend_Db_Expr("TO_DATE('9999/12/31 23:59:59','YYYY-MM-DD hh24:mi:ss')");//最终出库日期
			$data ['SHULIANG'] = $grid [$this->idx_SHULIANG]; //数量
			if ($grid [$this->idx_SHCHRQ] != ""){
			$data ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" );
			}
			if ($grid [$this->idx_BZHQZH] != ""){
			$data ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM-DD')" );
			}
			//$data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
			//$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			//采购开票单明细表
			$this->_db->insert ( "H01DB012404", $data );
		
	}
}
	/*
	 * 移动履历做成
	 * 
	 * @param 	array 	
	 * 				 
	 * @return 	bool	
	 */
	public function Movelvl($zjrkbh) {
		$idx = 1; //序号自增
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == '')
				continue;
		$data['QYBH'] = $_SESSION ['auth']->qybh;
		$data['CKBH'] = $grid [$this->idx_CKBH];
		$data['KQBH'] = $grid [$this->idx_KQBH];
		$data['KWBH'] = $grid [$this->idx_KWBH];
		$data['SHPBH'] = $grid [$this->idx_SHPBH];
		$data['PIHAO'] = $grid [$this->idx_PIHAO];
		$data['RKDBH'] = $zjrkbh;
		$data['YDDH'] = $zjrkbh;
		$data['XUHAO'] = $idx++;
		if ($grid [$this->idx_SHCHRQ] != ""){
			$data['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$grid [$this->idx_SHCHRQ]."','YYYY-MM-DD')"); //生产日期;
		}
		if ($grid [$this->idx_BZHQZH] != ""){
			$data['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$grid [$this->idx_BZHQZH]."','YYYY-MM-DD')"); //保质期至
		}
		$data['CHLSHJ'] = new Zend_Db_Expr('SYSDATE');
		$data ['SHULIANG'] = $grid [$this->idx_SHULIANG]; //数量
		$data['BZHDWBH'] = $grid [$this->idx_BZHDWBH];
		$data['ZHYZHL'] = '11';
		$data['ZKZHT'] = '0';
		$data['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
		$data['BGZH'] = $_SESSION ['auth']->userId; //变更者		
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$this->_db->insert ( 'H01DB012405', $data );
	}
	}

}
	
	