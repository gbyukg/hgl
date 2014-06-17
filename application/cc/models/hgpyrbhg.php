<?php
/*********************************
 * 模块：    仓储模块(cc)
 * 机能：    合格品移入不合格品区(CGKP)
 * 作成者：姚磊
 * 作成日：2011/08/13
 * 更新履历：
 *********************************/
class cc_models_hgpyrbhg extends Common_Model_Base {

	private $idx_ROWNUM = 0; // 行号
	private $idx_SHPBH = 1;// 商品编号
	private $idx_SHPMCH = 2;// 商品名称
	private $idx_GUIGE = 3;// 商品规格
	private $idx_BZHDWM = 4;// 包装单位
	private $idx_JLGG = 5;// 计量规格
	private $idx_DCKUW = 6;//调出库位 
	private $idx_PIHAO = 7;//批号
	private $idx_SHCHRQ = 8;//生成日期
	private $idx_BZHQZH = 9;//保质期至
	private $idx_BZHSHL = 10;// 包装数量
	private $idx_LSSHL = 11;// 零散数量
	private $idx_SHULIANG = 12;// 数量
	private $idx_CHANDI = 13;// 产地
	private $idx_BEIZHU = 14;// 备注
	private $idx_CKBH = 15;//仓库编号
	private $idx_KQBH = 16;//库区编号
	private $idx_KWBH = 17;//库位编号
	private $idx_KCSHUL= 18;//库存数量
	private $idx_DANJIA = 19;// 单价
	private $idx_HSHJ = 20;// 含税价
	private $idx_KOULV = 21;// 扣率
	private $idx_SHUILV = 22;// 税率
	private $idx_HSHJE = 23;// 含税金额
	private $idx_JINE = 24; // 金额
	private $idx_SHUIE = 25;// 税额
	private $idx_SHFSHKW = 26; // 是否零散库位
	private $idx_TONGYONGMING = 27;//通用名
	private $idx_BZHDWBH = 28;//包装单位编号
	private $idx_TYMCH = 29;//通用名
	private $idx_KWZT = 30;//库位状态

	/*
	 * //保存出库信息
	 */
	public function saveMain($RKDBH,$rkdbh) {
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['CHKDBH'] = $RKDBH; //出库单编号
		$data ['CKDBH'] = $rkdbh; //参考单编号
		$data ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$data ['BMBH'] = $_POST ['BMBH']; //部门编号
		$data ['YWYBH'] = $_POST ['YWYBH']; //业务员编号
		$data ['KPYBH'] = $_SESSION ["auth"]->userId; //开票员编号
		$data ['DWBH'] = $_POST ['DWBH']; //单位编号
		$data ['DIZHI'] = $_POST ['DIZHI']; //地址
		$data ['DHHM'] = $_POST ['DHHM']; //电话
		$data ['SHFZZHSH'] = isset($_POST ['SHFZZHSH'])? '1' : '0'; //是否增值税		
		$data ['KOULV'] = $_POST ['KOULVF']; //扣率
		$data ['BEIZHU'] = $_POST ['BEIZHU']; //备注		
		$data ['SHULIANG'] = $_POST['SHULIANG_1'];//数量
		$data ['CHKDZHT'] = '5'; //出库类型
		$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		return $this->_db->insert ( "H01DB012408", $data );
	}
	/*
	 * 保存不合格品入库信息
	 */
	
	public function savebhgMain($rkdbh){
		
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['BHGPRKDBH'] = $rkdbh; //出库单编号
		$data ['CKDBH'] = $_POST ['RKDBH']; //参考单编号
		$data ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$data ['BMBH'] = $_POST ['BMBH']; //部门编号
		$data ['YWYBH'] = $_POST ['YWYBH']; //业务员编号
		$data ['KPYBH'] = $_SESSION ["auth"]->userId; //开票员编号	
		$data ['BEIZHU'] = $_POST ['BEIZHU']; //备注		
		$data ['RKLX'] = '2'; //出库类型 - 合格品库移出
		$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		return $this->_db->insert ( "H01DB012460", $data );

	}
	
	
	
	/*
	 * 保存出库单明细
	 */
	public function saveMingxi($RKDBH) {
		
		
		$idx = 1; //序号自增
		//循环所有明细行，保存销售订单明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == '')
				continue;
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['CHKDBH'] = $RKDBH; //出库单编号
			$data ['RKDBH'] = $_POST ['RKDBH']; //出库单编号			
			$data ['XUHAO'] = $idx ++; //序号
			$data ['SHPBH'] = $grid [$this->idx_SHPBH]; //商品编号
			$data ['CKBH'] = $grid [$this->idx_CKBH]; //仓库编号
			$data ['KQBH'] = $grid [$this->idx_KQBH]; //库区编号
			$data ['KWBH'] = $grid [$this->idx_KWBH]; //库位编号
			$data ['PIHAO'] = $grid [$this->idx_PIHAO]; //批号
			$data ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ]. "','YYYY-MM-DD')" ); //生产日期
			$data ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH]. "','YYYY-MM-DD')" ); //保质期至
			$data ['BZHSHL'] = $grid [$this->idx_BZHSHL]; //包装数量
			$data ['LSSHL'] = $grid [$this->idx_LSSHL]; //零散数量
			$data ['SHULIANG'] = $grid [$this->idx_SHULIANG]; //数量
			$data ['DANJIA'] = $grid [$this->idx_DANJIA]; //数量
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
			$this->_db->insert ( "H01DB012409", $data );
		}
	}

	/*
	 * 不合格品入库信息明细
	 */
	public function savebhgxx($rkdbh){
		
				$idx = 1; //序号自增
		//循环所有明细行，保存销售订单明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == '')
				continue;
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['BHGPRKDBH'] = $rkdbh; //不合格品入库单编号		
			$data ['XUHAO'] = $idx ++; //序号
			$data ['SHPBH'] = $grid [$this->idx_SHPBH]; //商品编号
			$data ['CKBH'] = $_SESSION['auth']->ckbh; //仓库编号
			$data ['PIHAO'] = $grid [$this->idx_PIHAO]; //批号
			$data ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ]. "','YYYY-MM-DD')" ); //生产日期
			$data ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH]. "','YYYY-MM-DD')" ); //保质期至
			$data ['BZHSHL'] = $grid [$this->idx_BZHSHL]; //包装数量
			$data ['LSSHL'] = $grid [$this->idx_LSSHL]; //零散数量
			$data ['SHULIANG'] = $grid [$this->idx_SHULIANG]; //数量
			$data ['DANJIA'] = $grid [$this->idx_DANJIA]; //数量
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
			$this->_db->insert ( "H01DB012461", $data );
		
		}
		
	}
	
	/*
	 * 更新不合格品在库信息
	 */
	public function updatebhgxx($rkdbh){
		
		$sort = $_POST ["#grid_mingxi"];
		foreach ( $sort as $key => $row ) {
			$shpbh[$key] = $row[$this->idx_SHPBH]; //商品编号
			$pihao[$key] = $row[$this->idx_PIHAO]; //批号
			$bzhdwbh[$key] = $row[$this->idx_BZHDWBH];//包装单位编号
		}
			//array_multisort($shpbh,SORT_ASC,$pihao,SORT_DESC, $_POST ["#grid_mingxi"]);
		array_multisort($shpbh,$pihao,$bzhdwbh,$sort);	
		$checkvalue = "";
		$curr = "";
		$shuliang = 0;
		$sumsort = array();
		$c = 0;
		foreach ( $sort as $k => $row ) {
			
			$checkvalue = $row[$this->idx_SHPBH].$row[$this->idx_PIHAO].$row[$this->idx_BZHDWBH];
			if($checkvalue == $curr || $curr == ""){
				$shuliang += (int) $row[$this->idx_SHULIANG];		//数量加算

			}else{
				$sumsort[] = $sort[$k-1];
				$sumsort[$c][$this->idx_SHULIANG] = $shuliang; 
				$shuliang = (int) $row[$this->idx_SHULIANG];

				$c++;
			}
			
			$curr = $checkvalue;
		}
		
		$sumsort[] = $sort[$k];
		$sumsort[$c][$this->idx_SHULIANG] = $shuliang;
		foreach ($sumsort as $grid){
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['RKDBH'] = $rkdbh; //不合格品入库单编号
			$data ['CKBH'] = $_SESSION['auth']->ckbh; //仓库编号	
			$data ['PIHAO'] = $grid [$this->idx_PIHAO]; //批号	
			$data ['SHPBH'] = $grid [$this->idx_SHPBH]; //商品编号
			$data ['BZHDWBH'] = $grid [$this->idx_BZHDWBH]; //包装单位编号
			$data ['SHULIANG'] = $grid [$this->idx_SHULIANG]; //数量
			$data ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ]. "','YYYY-MM-DD')" ); //生产日期
			$data ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH]. "','YYYY-MM-DD')" ); //保质期至
			$this->_db->insert ( "H01DB012459", $data );
		}
		
	}
	
	/*
	 * 在库商品,移动履历
	 */
	public function getYdlvli($RKDBH){
		
		$result['status'] = '0'; 
		$idx = 1; //序号自增
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
		$sql =" SELECT SHULIANG,RKDBH FROM H01DB012404 WHERE QYBH=:QYBH AND ".
			  " CKBH=:CKBH AND KQBH=:KQBH AND KWBH=:KWBH AND SHPBH=:SHPBH AND ".
			  " RKDBH=:RKDBH AND PIHAO=:PIHAO AND ZKZHT=:ZKZHT AND BZHDWBH=:BZHDWBH AND SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD') FOR UPDATE" ;

		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CKBH'] = $grid [$this->idx_CKBH]; //仓库编号
		$bind ['KQBH'] = $grid [$this->idx_KQBH]; //库区编号
		$bind ['KWBH'] = $grid [$this->idx_KWBH]; //库位编号
		$bind ['SHPBH'] = $grid [$this->idx_SHPBH];//商品编号
		$bind ['RKDBH'] = $_POST ['RKDBH'];       //入库单编号
		$bind ['PIHAO'] = $grid [$this->idx_PIHAO];       //入库单编号
		$bind ['ZKZHT'] = $grid [$this->idx_KWZT];       //在库状态
		$bind ['BZHDWBH'] = $grid [$this->idx_BZHDWBH];       //包装单位编号
		$bind ['SHCHRQ'] = $grid [$this->idx_SHCHRQ]; //生产日期
		
		$rek = $this->_db->fetchOne ( $sql, $bind );
		if( $grid [$this->idx_SHULIANG] >$rek ){
			$result['status'] = '3'; 
			$result ['data']['rIdx'] = ( int ) $grid [$this->idx_ROWNUM]; //定位明细行index
			break;	
		}else{
			unset($bind_tem);
			if((int)($grid [$this->idx_SHULIANG] - $rek) == '0'){
			$sql_temp = "UPDATE H01DB012404 SET SHULIANG =:SHULIANG ,ZZHCHKRQ = SYSDATE WHERE QYBH=:QYBH AND ".
						" CKBH=:CKBH AND KQBH=:KQBH AND KWBH=:KWBH AND SHPBH=:SHPBH AND ".
			  			" RKDBH=:RKDBH AND PIHAO=:PIHAO AND ZKZHT=:ZKZHT AND BZHDWBH=:BZHDWBH AND SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD')" ;
			}else{
			$sql_temp = "UPDATE H01DB012404 SET SHULIANG =:SHULIANG  WHERE QYBH=:QYBH AND ".
						" CKBH=:CKBH AND KQBH=:KQBH AND KWBH=:KWBH AND SHPBH=:SHPBH AND ".
			  			" RKDBH=:RKDBH AND PIHAO=:PIHAO AND ZKZHT=:ZKZHT AND BZHDWBH=:BZHDWBH AND SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD')" ;	
			}
		$bind_tem ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind_tem['SHULIANG'] = (int)($rek - $grid [$this->idx_SHULIANG]);//数量
		$bind_tem ['CKBH'] = $grid [$this->idx_CKBH]; //仓库编号
		$bind_tem ['KQBH'] = $grid [$this->idx_KQBH]; //库区编号
		$bind_tem ['KWBH'] = $grid [$this->idx_KWBH]; //库位编号
		$bind_tem ['SHPBH'] = $grid [$this->idx_SHPBH];//商品编号
		$bind_tem ['RKDBH'] = $_POST ['RKDBH'];       //入库单编号
		$bind_tem ['PIHAO'] = $grid [$this->idx_PIHAO];       //入库单编号
		$bind_tem ['ZKZHT'] = $grid [$this->idx_KWZT];       //在库状态
		$bind_tem ['BZHDWBH'] = $grid [$this->idx_BZHDWBH];       //包装单位编号
		$bind_tem ['SHCHRQ'] = $grid [$this->idx_SHCHRQ]; //生产日期
		$this->_db->query ( $sql_temp,$bind_tem );
		}
		//更新商品移动履历
		unset($data_tem);
		$data_tem ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data_tem ['YDDH'] = $RKDBH; //出库单编号
		$data_tem ['RKDBH'] = $_POST ['RKDBH']; //入库库单编号			
		$data_tem ['XUHAO'] = $idx ++; //序号
		$data_tem ['SHPBH'] = $grid [$this->idx_SHPBH]; //商品编号
		$data_tem ['CKBH'] = $grid [$this->idx_CKBH]; //仓库编号
		$data_tem ['KQBH'] = $grid [$this->idx_KQBH]; //库区编号
		$data_tem ['KWBH'] = $grid [$this->idx_KWBH]; //库位编号
		$data_tem ['PIHAO'] = $grid [$this->idx_PIHAO]; //批号
		$data_tem ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ]. "','YYYY-MM-DD')" ); //生产日期
		$data_tem ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH]. "','YYYY-MM-DD')" ); //保质期至
		$data_tem ['CHLSHJ'] = new Zend_Db_Expr ( 'SYSDATE' ); //处理时间
		$data_tem ['ZHYZHL'] = '73'; //转移种类
		$data_tem ['SHULIANG'] = $grid [$this->idx_SHULIANG]; //数量
		$data_tem ['ZKZHT'] = $grid [$this->idx_KWZT]; //在库状态
		$data_tem ['BEIZHU'] = $grid [$this->idx_BEIZHU]; //备注
		$data_tem ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
		$data_tem ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$this->_db->insert ( "H01DB012405", $data_tem );
		return  $result ;
		
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
		//$fields = array ("", "A.SHPMCH", "A.SHPMCH" ); //
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
	//	$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 必须添加所有主键
	//	$sql .= " ,A.SHPBH";

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

	/**
	 * 在线库位商品第一次数据查询
	 */
	public function getfristGridData( $filter,$rkdh) {
		
 		$fields = array ("",  "SHPBH","SHPMCH" ); //挂账单编号
		//检索SQL
		$sql = " SELECT distinct SHPBH,SHPMCH,CKMCH || KQMCH ||　KWMCH,SHULIANG,DWMCH,PIHAO,GUIGE,BZHDWMCH,TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS  SHCHRQ ".
			   " ,TO_CHAR(BZHQZH,'YYYY-MM-DD') AS  BZHQZH ,DECODE(ZKZHT,'0','可销','1','催销','2','冻结') AS ZKZHT  , CHANDI,JLGG,RKDBH ".
			   " ,CKBH,KQBH,KWBH,DIZHI,DHHM,SHFZZHSH,KOULVF,DANJIA,HSHJ,KOULVG,JINE,HSHJE,SHUIE,SHFSHKW,DWBH ,BZHDWBH ,SHUILV ,TYMCH ,ZKZHT as KWZHT ".
			   " FROM H01VIEW012404 WHERE QYBH =:QYBH AND SHULIANG > 0 ";
		

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		//取得入库单下的商品信息
		if($rkdh!=NULL){
			$sql .=" AND RKDBH=:RKDBH ";
			$bind ['RKDBH'] = $rkdh;
		}			
		//查找条件  编号或名称
		if($filter['searchParams']['SERCHSPXX']!=""){
			$sql .= " AND( SHPBH LIKE '%' || :SEARCHKEY || '%'".
			        " OR  lower(SHPMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['SERCHSPXX']);
		}
		//serchdwmch 单位名称
		if ($filter['searchParams']["SERCHDCKW"] != "") {
			$sql .= "AND CKBH=:CKBH AND KQBH =:KQBH AND KWBH=:KWBH "; 		//调出仓库模糊查询
			$bind ['CKBH'] = $filter['searchParams']["CKBH"];
			$bind ['KQBH'] = $filter['searchParams']["KQBH"];
			$bind ['KWBH'] = $filter['searchParams']["KWBH"];
		}

		$sql .= Common_Tool::createFilterSql("CC_HGPZKXX",$filter['filterParams'],$bind);
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 必须添加所有主键
		$sql .= " ,SHPBH";
		
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
	 * 在线库位商品第一次数据查询
	 */
	public function getafristGridData( $filter) {
		
		$fields = array ("",  "SHPBH","SHPMCH" ); //挂账单编号
		//检索SQL
		$sql = " SELECT  SHPBH,SHPMCH,GUIGE,BZHDWMCH,JLGG,CKMCH||KQMCH||KWMCH ,PIHAO,TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS  SHCHRQ ".
			   " ,TO_CHAR(BZHQZH,'YYYY-MM-DD') AS  BZHQZH ,'' ,'', CHANDI,'' FROM H01VIEW012404 WHERE QYBH =:QYBH AND RKDBH=:RKDBH AND SHPBH=:SHPBH";
		

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['SHPBH'] = $filter['SHPBH']; //商品编号
		$bind ['RKDBH'] = $filter['RKDBH'];//入库单编号
		
		
//	//查询条件(开始日期<=开票日期<=终止日期)
//		if ($filter['searchParams']["SERCHKSRQ"] != "" || $filter['searchParams']["SERCHJSRQ"] != "")
//		{
//			$sql .= " AND :SERCHKSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD')AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :SERCHJSRQ ";
//			$bind ['SERCHKSRQ'] = $filter['searchParams']["SERCHKSRQ"] == ""?"1900-01-01":$filter['searchParams']["SERCHKSRQ"];
//			$bind ['SERCHJSRQ'] = $filter['searchParams']["SERCHJSRQ"] == ""?"9999-12-31":$filter['searchParams']["SERCHJSRQ"];
//		}	
//				
//		//serchdwbh 单位编号
//		if ($filter['searchParams']["SERCHDWBH"] != "") {
//			$sql .= " AND(DWBH LIKE '%' || :SERCHDWBH || '%')";  				//单位编号模糊查询
//			$bind ['SERCHDWBH'] = $filter['searchParams']["SERCHDWBH"];
//		}
//		//serchdwmch 单位名称
//		if ($filter['searchParams']["SERCHDWMCH"] != "") {
//			$sql .= " AND(DWMCH LIKE '%' || :SERCHDWMCH || '%')"; 		//单位名称模糊查询
//			$bind ['SERCHDWMCH'] = $filter['searchParams']["SERCHDWMCH"];
//		}
//		//serchshbh 商品编号
//		if ($filter['searchParams']["SERCHSHBH"] != "") {									//商品编号模糊查询
//			$sql .= " AND(SHPBH LIKE '%' || :SERCHSHBH || '%')";
//			$bind ['SERCHSHBH'] = $filter['searchParams']["SERCHSHBH"];
//		}
//		//serchshmch 商品名称
//		if ($filter['searchParams']["SERCHSHMCH"] != "") {
//			$sql .= " AND(SHPMCH LIKE '%' || :SERCHSHMCH || '%')";		//商品名称模糊查询
//			$bind ['SERCHSHMCH'] = $filter['searchParams']["SERCHSHMCH"];
//		}
		$sql .= Common_Tool::createFilterSql("CC_HGPYRBHGPXX",$filter['filterParams'],$bind);
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 必须添加所有主键
		$sql .= " ,SHPBH";
		
		return $this->_db->fetchRow ( $sql, $bind );

	
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
	 * 根据单位编号编号取得单位信息
	 */
	function DanweiInfo($filter) {
		//检索SQL
		$sql = "SELECT A.DWBH,A.DWMCH,A.DIZHI,A.DHHM,A.KOULV,A.FHQBH," .
		       "DECODE(A.XSHXDQ,NULL,0,XSHXDQ) XSHXDQ" . //销售信贷期 
               " FROM H01DB012106 A" . " WHERE A.QYBH = :QYBH " . //区域编号
               " AND A.DWBH = :DWBH" . //单位编号
               " AND A.FDBSH ='0'" . //分店标识
               " AND A.SHFXSH = '1'" . //是否采购
               " AND A.KHZHT = '1'"; //客户状态
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		return $this->_db->fetchRow ( $sql, $bind );
	}



}
	
	