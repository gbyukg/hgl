<?php
/*********************************
 * 模块：    销售模块(XS)
 * 机能：    销售一步完成(XSYBWC)
 * 作成者：魏峰
 * 作成日：2011/01/21
 * 更新履历：
 *********************************/
class xs_models_xsybwc extends Common_Model_Base {
	private $_xsdbh = null; //销售单编号
	private $idx_ROWNUM = 0; //行号
	private $idx_SHPBH = 1; //商品编号
	private $idx_SHPMCH = 2; //商品名称
	private $idx_GUIGE = 3; //规格
	private $idx_BZHDWM = 4; //包装单位
	private $idx_HWMCH = 5; //货位
	private $idx_PIHAO = 6; //批号
	private $idx_SHCHRQ = 7; //生产日期
	private $idx_BZHQZH = 8; //保质期至
	private $idx_JLGG = 9; //计量规格
	private $idx_BZHSHL = 10; //包装数量
	private $idx_LSSHL = 11; //零散数量
	private $idx_SHULIANG = 12; //数量
	private $idx_DANJIA = 13; //单价
	private $idx_HSHJ = 14; //含税售价
	private $idx_KOULV = 15; //扣率
	private $idx_SHUILV = 16; //税率
	private $idx_HSHJE = 17; //含税金额
	private $idx_JINE = 18; //金额
	private $idx_SHUIE = 19; //税额
	private $idx_LSHJ = 20; //零售价
	private $idx_ZGSHJ = 21; //最高售价
	private $idx_SHPTM = 22; //商品条码
	private $idx_FLBM = 23; //分类编码
	private $idx_PZHWH = 24; //批准文号
	private $idx_JIXINGM = 25; //剂型
	private $idx_SHCHCHJ = 26; //生产厂家
	private $idx_CHANDI = 27; //产地
	private $idx_SHFOTC = 28; //是否otc
	private $idx_CHAE = 29; //差额
	private $idx_BEIZHU=30;//备注
	private $idx_BZHDWBH = 31; //包装单位编号
	private $idx_CKBH = 32; //仓库编号
	private $idx_KQBH = 33; //库区编号
	private $idx_KWBH = 34; //库位编号
	private $idx_KWSHULIANG = 35; //库位数量
	private $idx_SHFSHKW = 36; //是否散货区
	
	/*
	 * 取得发货区信息
	 */
	public function getFHQInfo() {
		$sql = "SELECT FHQBH,FHQMCH FROM H01DB012422 WHERE QYBH = :QYBH AND FHQZHT = '1'";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$result = $this->_db->fetchPairs ( $sql, $bind );
		return $result;
	}
	
	/*
	 * 根据单位编号编号取得单位信息
	 */
	public function getDanweiInfo($filter) {
		//检索SQL
		$sql = "SELECT A.DWBH,A.DWMCH,A.DIZHI,A.DHHM,A.KOULV,A.FHQBH," .
		       "DECODE(A.XSHXDQ,NULL,0,XSHXDQ) XSHXDQ" . //销售信贷期 
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
			$sql = "SELECT floor(SYSDATE - KPRQ) FROM H01DB012201 WHERE QYBH = :QYBH AND DWBH = :DWBH" . " AND QXBZH ='1' AND FKFSH = '1' AND JSZHT = '0' " . " ORDER BY KPRQ ";
			$days = $this->_db->fetchOne ( $sql, $bind );
			
			//账期已超
			if ($days > $xdq) {
				$returnValue = 1;
			}
		}
		
		return $returnValue;
	}	
	
	/*
	 * 根据商品编号取得商品信息
	 */
	public function getShangpinInfo($filter) {
		
		//检索SQL
		$sql = "SELECT " .
		       "A.SHPBH," . //商品编号
               "A.SHPMCH," . //商品名称
               "A.GUIGE," . //规格
               "A.BZHDWBH," . //包装单位编号
               "A.BZHDWMCH," . //包装单位
               "A.SHOUJIA," . //售价
               "A.HSHSHJ," . //含税售价
		       "A.LSHJ,".    //零售价
               "A.KOULV," . //扣率
               "A.SHUILV," . //税率
               "A.ZGSHJ," . //最高售价
               "A.SHPTM," . //商品条码
               "A.FLBM," . //分类编码
               "A.PZHWH," . //批准文号
               "A.JIXINGMCH," . //剂型
               "A.SHCHCHJ," . //生产厂家
               "A.CHANDI," . //产地
               "A.SHFOTCMCH," . //是否Otc
               "A.JLGG," . //计量规格
               "A.XDBZH," . //限定标志
               "B.JXBZH " . //禁销标志
               "FROM H01VIEW012001 A " . //商品指定客户信息
		       " LEFT JOIN H01DB012114 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH AND B.DWBH = :DWBH " . " WHERE A.QYBH = :QYBH " . " AND A.SHPBH = :SHPBH " . " AND A.SHPZHT = '1'";
		//禁销商品
		$sql .= " AND A.SHPBH NOT IN (SELECT SHPBH FROM H01DB012114  WHERE QYBH = :QYBH AND DWBH = :DWBH AND JXBZH = '3' )";
		//限定商品
		$sql .= " AND (A.XDBZH = '0' OR A.XDBZH = '1' AND A.SHPBH IN (SELECT SHPBH FROM H01DB012114  WHERE QYBH = :QYBH AND DWBH = :DWBH AND JXBZH <> '3')) ";
		
		$bind ['SHPBH'] = $filter ['shpbh']; //商品编号
		$bind ['DWBH'] = $filter ['dwbh']; //单位编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		return $this->_db->fetchRow ( $sql, $bind );
	}	
	
	/*
	 * 商品价格信息
	 */
	public function getJiageInfo($filter) {
		$result ['type'] = 'none'; //无特价无一品多价
	
		$sql = "SELECT HSHSHJTJ,XSHTJ" . 
		       " FROM H01DB012105 " .
		       " WHERE QYBH = :QYBH " . 
		       " AND SHPBH = :SHPBH " . 
		       " AND DWBH = :DWBH" . 
		       " AND QSHZHXRQ <= SYSDATE" . //起始执行日期
               " AND ZHZHZHXRQ >=SYSDATE"; //终止执行日期

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		$bind ['SHPBH'] = $filter ['shpbh'];
		
		$recs = $this->_db->fetchRow ( $sql, $bind ); //特价信息

		//特价不存在的时候，看一品多价是否存在
		if ($recs == FALSE) {
			//查看一品多价是否存在
			$sql = "SELECT COUNT(*) FROM H01DB012104 A" .
			       " INNER JOIN H01DB012106 B ON A.QYBH = B.QYBH AND A.KHDJ = B.KHDJ" .
			       " WHERE A.QYBH = :QYBH " . 
			       " AND A.SHPBH = :SHPBH " .
			       " AND B.DWBH = :DWBH";
			
			//存在一品多价
			if ($this->_db->fetchOne ( $sql, $bind ) != "0") {
				$result ['type'] = '1'; //一品多价
			}
		} else {
			$result ['type'] = '0'; //特价存在
			$result ['data'] = $recs; //特价信息
		}
		
		return $result;
	}
	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck() {
		if ($_POST ["KPRQ"] == "" || //开票日期
            $_POST ["XSBMBH"] == "" || //销售部门编号
            $_POST ["CCBMBH"] == "" || //仓储部门编号
            $_POST ["DWBH"] == "" || //单位编号
            $_POST ["XSYWYBH"] == "" || //销售业务员编号   
            $_POST ["CCYWYBH"] == "" || //仓储业务员编号               
            $_POST ["FAHUOQU"] == "0" || //发货区
            $_POST ["FKFSH"] == "0" || //付款方式
            $_POST ["#grid_mingxi"] == "") { //明细表格
			return false;
		}
		
		$isHasMingxi = false; //是否存在至少一条明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] != "") {
				$isHasMingxi = true;
				if ($grid [$this->idx_PIHAO] == "" || //批号
                    $grid [$this->idx_SHULIANG] == "" || //数量
                    $grid [$this->idx_SHULIANG] == "0" || //数量
                    $grid [$this->idx_CKBH] == "" || //仓库编号
                    $grid [$this->idx_KQBH] == "" || //库区编号 
                    $grid [$this->idx_KWBH] == "") { //库位编号
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
	 * 销售订单保存
	 */
	public function saveXshdMain($xshdbh) {
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['XSHDBH'] = $xshdbh; //销售单编号
		$data ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$data ['BMBH'] = $_POST ['XSBMBH']; //部门编号
		$data ['KPYBH'] = $_SESSION ['auth']->userId; //开票员编号
		$data ['YWYBH'] = $_POST ['XSYWYBH']; //业务员编号
		$data ['DWBH'] = $_POST ['DWBH']; //单位编号
		$data ['DIZHI'] = $_POST ['DIZHI']; //地址
		$data ['DHHM'] = $_POST ['DHHM']; //电话
		$data ['SHFZZHSH'] = $_POST ['SHFZZHSH']; //是否增值税
		$data ['KOULV'] = $_POST ['KOULV']; //扣率
		$data ['XSHDZHT'] = '1'; //销售单状态(已出库)
		$data ['FHQBH'] = $_POST ['FAHUOQU']; //发货区
		$data ['BEIZHU'] = $_POST ['BEIZHU']; //备注
		$data['SHHZHT'] = '0';//审核状态
		//$data['SHHR'] = '';//审核人
		//$data['SHHYJ'] = '';//审核意见
		//$data['SHHRQ'] = new Zend_Db_Expr("SYSDATE");//审核日期
		$data['QXBZH'] = '1';//取消标志
		$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者				
		$data ['FKFSH'] = $_POST ['FKFSH']; //付款方式	
		$data ['SHFPS'] = $_POST ['SHFPS']; //是否配送
		$data ['JSZHT'] = '0'; //结算状态
		$data ['PSYXJ'] = '1'; //配送优先级		
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		//销售订单信息表
		return $this->_db->insert ( "H01DB012201", $data );
	}
	
	/*
	 * 销售订单明细保存
	 */
	public function saveXshdMingxi($xshdh) {
		$idx = 1; //序号自增
        //循环所有明细行，保存销售订单明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == '')continue;
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['XSHDBH'] = $xshdh; //销售单编号
			$data ['XUHAO'] = $idx ++; //序号
			$data ['SHPBH'] = $grid [$this->idx_SHPBH]; //商品编号
			$data ['CKBH'] = $grid [$this->idx_CKBH]; //仓库编号
			$data ['KQBH'] = $grid [$this->idx_KQBH]; //库区编号
			$data ['KWBH'] = $grid [$this->idx_KWBH]; //库位编号
			$data ['PIHAO'] = $grid [$this->idx_PIHAO]; //批号
			$data ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" ); //生产日期
			$data ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM')" ); //保质期至
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
			//销售订单明细表
			$this->_db->insert ( "H01DB012202", $data );	
		}
	}	
	
	/*
	 * 销售单保存处理
	 */
	public function updateKucun($xshdbh) {
		$result ['status'] = '0';
		
		//循环所有明细行进行库存数量检验
		foreach ( $_POST ["#grid_mingxi"] as $row ) {
			if ($row [$this->idx_SHPBH] == '')continue;
			//取得即时库存信息
			$sql = "SELECT A.QYBH,A.CKBH,A.KQBH,A.KWBH,A.SHPBH,A.PIHAO,A.RKDBH,A.ZKZHT,A.BZHDWBH,A.SHULIANG,TO_CHAR(A.SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,TO_CHAR(A.BZHQZH,'YYYY-MM') AS BZHQZH " . 
			       "FROM H01DB012404 A " .
			       " JOIN H01DB012403 B ON B.QYBH = A.QYBH AND B.CKBH = A.CKBH AND B.KQBH = A.KQBH AND B.KWBH = A.KWBH ".
			       " JOIN H01DB012402 C ON C.QYBH = A.QYBH AND C.CKBH = A.CKBH AND C.KQBH = A.KQBH ".
			       " JOIN H01DB012401 D ON D.QYBH = A.QYBH AND D.CKBH = A.CKBH ".
			       " WHERE A.QYBH = :QYBH" . //区域编号
                   " AND A.CKBH = :CKBH " . //仓库编号
                   " AND A.KQBH = :KQBH " . //库区编号
                   " AND A.KWBH = :KWBH " . //库位编号
                   " AND A.SHPBH = :SHPBH " . //商品编号
                   " AND A.PIHAO = :PIHAO " . //批号
                   " AND A.ZKZHT IN ('0','1')" . //在库状态
                   " AND A.BZHDWBH = :BZHDWBH " . //包装单位
			       " AND A.SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD')".
			       " AND B.KWZHT = '1' AND C.KQZHT = '1' AND D.CKZHT = '1' ".
                   " ORDER BY ZKZHT DESC,RKDBH" . //在库状态 降序，入库单升序
                   " FOR UPDATE  OF A.SHULIANG WAIT 10"; //对象库存数据锁定
			//绑定查询变量
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['CKBH'] = $row [$this->idx_CKBH];
			$bind ['KQBH'] = $row [$this->idx_KQBH];
			$bind ['KWBH'] = $row [$this->idx_KWBH];
			$bind ['SHPBH'] = $row [$this->idx_SHPBH];
			$bind ['PIHAO'] = $row [$this->idx_PIHAO];
			$bind ['BZHDWBH'] = $row [$this->idx_BZHDWBH];
			$bind ['SHCHRQ'] = $row [$this->idx_SHCHRQ];
			
			//当前明细行在库信息
			$recs = $this->_db->fetchAll ( $sql, $bind );
			$shuliang_zaiku = 0; //累计在库数量
			foreach ( $recs as $rec ) {
				$shuliang_zaiku += ( int ) $rec ['SHULIANG'];
			}
			
			//当前库存数量不足
			if ($shuliang_zaiku < ( int ) $row [$this->idx_SHULIANG]) {
				$result ['status'] = '1'; //库存不足
				$result ['data']['rIdx'] = ( int ) $row [$this->idx_ROWNUM]; //定位明细行index
				$result ['data']['shuliang'] = $shuliang_zaiku; //最新在库数量
				$kucunModel = new gt_models_kucun ( );//库存不足时取得最新库存数据，返回页面用
				$result ['data']['kucundata'] = $kucunModel->getKucunData ( array('shpbh'=>$row [$this->idx_SHPBH],'shfshkw'=> $row [$this->idx_SHFSHKW]));
			}
			
			//库存数量充足
			if($result['status']=='0'){
				//更新在库和移动履历信息
			    $this->updateZaiku ( $row, $recs, $xshdbh );
			}
		}
					
		return $result;
	}
	
	/*
	 * 更新在库和移动履历信息
	 */
	public function updateZaiku($row,$kucuns, $xshdbh) {
		//同一货位批号 按照催销，先入先出（入库单）原则进行分摊出库
		$shuliang_shengyu = ( int ) $row [$this->idx_SHULIANG]; //销售数量
		$idx = 0; //在库移动履历序号
		foreach ( $kucuns as $kucun ) {
			$shuliang = 0; //在库更新数量
	
			//部分出库时 
			if ($shuliang_shengyu <= ( int ) $kucun ['SHULIANG']) {
				$shuliang = ( int ) $kucun ['SHULIANG'] - $shuliang_shengyu;
				$shuliang_yidong = $shuliang_shengyu;
				$shuliang_shengyu = 0;
			
			} else { //全部出库
				$shuliang = 0;
				$shuliang_yidong = ( int ) $kucun ['SHULIANG'];
				$shuliang_shengyu = $shuliang_shengyu - ( int ) $kucun ['SHULIANG'];
			}
			
			//更新在库信息
			$sql_zaiku = "UPDATE H01DB012404 ".
			             "SET SHULIANG = :SHULIANG " .
			             (($shuliang == 0) ? ",ZZHCHKRQ = SYSDATE " : "").
			             " WHERE QYBH = :QYBH ".
			             " AND CKBH = :CKBH " .
			             " AND KQBH = :KQBH ".
			             " AND KWBH = :KWBH ".
			             " AND SHPBH = :SHPBH " .
			             " AND PIHAO = :PIHAO " .
			             " AND ZKZHT = :ZKZHT " .
			             " AND RKDBH = :RKDBH " .
			             " AND BZHDWBH = :BZHDWBH ".
			             " AND SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD')";
			             
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['CKBH'] = $kucun ['CKBH']; 
			$bind ['KQBH'] = $kucun ['KQBH']; 
			$bind ['KWBH'] = $kucun ['KWBH']; 
			$bind ['SHPBH'] = $kucun ['SHPBH']; 
			$bind ['PIHAO'] = $kucun ['PIHAO']; 
			$bind ['BZHDWBH'] = $kucun ['BZHDWBH']; 
			$bind ['SHCHRQ'] = $kucun ['SHCHRQ']; 
			$bind ['RKDBH'] = $kucun ['RKDBH']; 
			$bind ['ZKZHT'] = $kucun ['ZKZHT'];
			$bind ['SHULIANG'] = $shuliang;               
			$this->_db->query ( $sql_zaiku,$bind );
			
			//生成在库移动履历
			$lvli ["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
			$lvli ["CKBH"] = $kucun ['CKBH']; //仓库编号
			$lvli ["KQBH"] = $kucun ['KQBH'];; //库区编号
			$lvli ["KWBH"] = $kucun ['KWBH'];; //库位编号
			$lvli ["SHPBH"] = $kucun ['SHPBH'];; //商品编号
			$lvli ["PIHAO"] = $kucun ['PIHAO'];; //批号
			$lvli ["RKDBH"] = $kucun ['RKDBH']; //入库单号
			$lvli ["YDDH"] = $xshdbh; //移动单号(销售单编号)
			$lvli ["XUHAO"] = $idx ++; //序号
			$lvli['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$kucun['SHCHRQ']."','YYYY-MM-DD')"); //生产日期
			$lvli['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$kucun['BZHQZH']."','YYYY-MM')"); //保质期至
			$lvli['CHLSHJ'] = new Zend_Db_Expr('SYSDATE'); //处理时间
			$lvli ["SHULIANG"] = $shuliang_yidong * - 1; //移动数量
			$lvli ["ZHYZHL"] = '21'; //转移种类 [出库]
			$lvli ["BZHDWBH"] = $kucun ['BZHDWBH']; //包装单位编号
			$lvli ["ZKZHT"] = $kucun ['ZKZHT'];//在库状态
			$lvli['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
			$lvli['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$lvli ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$lvli ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			$this->_db->insert ( 'H01DB012405', $lvli );
			
			//所有数量均出库完毕，不再继续循环
			if ($shuliang_shengyu <= 0) break;
		}
	}	
	
	/*
	 * 出库单信息保存
	 */
	public function saveChkdMain($chkdbh,$xshdbh) {
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['CHKDBH'] = $chkdbh; //出库单编号
		$data ['CKDBH'] = $xshdbh; //参考单编号				
		$data ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期		
		$data ['BMBH'] = $_POST ['CCBMBH']; //仓储部门编号		
		$data ['KPYBH'] = $_SESSION ['auth']->userId; //开票员编号		
		$data ['YWYBH'] = $_POST ['CCYWYBH']; //仓储业务员编号		
		$data ['DWBH'] = $_POST ['DWBH']; //单位编号		
		$data ['DIZHI'] = $_POST ['DIZHI']; //地址
		$data ['DHHM'] = $_POST ['DHHM']; //电话
		$data ['SHFZZHSH'] = $_POST ['SHFZZHSH']; //是否增值税
		$data ['KOULV'] = $_POST ['KOULV']; //扣率
		$data ['FHQBH'] = $_POST ['FAHUOQU']; //发货区
		$data ['BEIZHU'] = $_POST ['BEIZHU']; //备注
		$data ['FKFSH'] = $_POST ['FKFSH']; //付款方式	
		$data ['SHFPS'] = $_POST ['SHFPS']; //是否配送
		$data ['CHKLX'] = '3'; //出库类型
		$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者				
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
    	//出库单信息保存
		return $this->_db->insert ( "H01DB012408", $data );
	}	
	
	/*
	 * 出库单明细保存
	 */
	public function saveChkdMingxi($chkdbh) {
		$idx = 1; //序号自增
        //循环所有明细行，保存销售订单明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == '')continue;
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['CHKDBH'] = $chkdbh; //出库单编号		
			$data ['XUHAO'] = $idx ++; //序号		
			$data ['SHPBH'] = $grid [$this->idx_SHPBH]; //商品编号
			$data ['CKBH'] = $grid [$this->idx_CKBH]; //仓库编号
			$data ['KQBH'] = $grid [$this->idx_KQBH]; //库区编号
			$data ['KWBH'] = $grid [$this->idx_KWBH]; //库位编号
			$data ['PIHAO'] = $grid [$this->idx_PIHAO]; //批号
			$data ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" ); //生产日期
			$data ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM')" ); //保质期至
			$data ['BZHSHL'] = ($grid [$this->idx_BZHSHL] == null) ? 0 : $grid [$this->idx_BZHSHL]; //包装数量
			$data ['LSSHL'] = ($grid [$this->idx_LSSHL] == null) ? 0 : $grid [$this->idx_LSSHL]; //零散数量
			$data ['SHULIANG'] = $grid [$this->idx_SHULIANG]; //数量
			$data ['DANJIA'] = $grid [$this->idx_DANJIA]; //单价
			$data ['HSHJ'] = $grid [$this->idx_HSHJ]; //含税价
			$data ['KOULV'] = $grid [$this->idx_KOULV]; //扣率
			$data ['JINE'] = $grid [$this->idx_JINE]; //金额
			$data ['HSHJE'] = $grid [$this->idx_HSHJE]; //含税金额
			$data ['SHUIE'] = $grid [$this->idx_SHUIE]; //税额
			$data ['ZGSHJ'] = $grid [$this->idx_ZGSHJ]; //最高售价		
		    $data ['BEIZHU'] = $grid [$this->idx_BEIZHU]; //备注			
			$data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			//销售订单明细表
			$this->_db->insert ( "H01DB012409", $data );	
		}
	}

	/**
	 * 销售挂账单保存
	 *
	 */
	public function saveTempMain($xshgzhdbh){
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['XSHGZHDBH'] = $xshgzhdbh; //销售单编号
		$data ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$data ['BMBH'] = $_POST ['XSBMBH']; //部门编号
		$data ['KPYBH'] = $_SESSION ['auth']->userId; //开票员编号
		$data ['YWYBH'] = $_POST ['XSYWYBH']; //业务员编号
		$data ['DWBH'] = $_POST ['DWBH']; //单位编号
		$data ['DIZHI'] = $_POST ['DIZHI']; //地址
		$data ['DHHM'] = $_POST ['DHHM']; //电话
		$data ['SHFZZHSH'] = $_POST ['SHFZZHSH']; //是否增值税
		$data ['KOULV'] = $_POST ['KOULV']; //扣率
		$data ['FHQBH'] = $_POST ['FAHUOQU']; //发货区
		$data ['BEIZHU'] = $_POST ['BEIZHU']; //备注
		$data ['FKFSH'] = $_POST ['FKFSH']; //付款方式
		$data ['SHFPS'] = $_POST ['SHFPS']; //是否配送
		$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者	
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		//销售挂账单信息表
		return $this->_db->insert ( "H01DB012204", $data );
		
	}
	/*
	 * 销售挂账单明细保存
	 */
	public function saveTempMingxi($xshgzhdbh) {
		$idx = 1; //序号自增
        //循环所有明细行，保存销售订单明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == '')continue;
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['XSHGZHDBH'] = $xshgzhdbh; //挂账单编号
			$data ['XUHAO'] = $idx ++; //序号
			$data ['SHPBH'] = $grid [$this->idx_SHPBH]; //商品编号
			$data ['CKBH'] = $grid [$this->idx_CKBH]; //仓库编号
			$data ['KQBH'] = $grid [$this->idx_KQBH]; //库区编号
			$data ['KWBH'] = $grid [$this->idx_KWBH]; //库位编号
			$data ['PIHAO'] = $grid [$this->idx_PIHAO]; //批号
			$data ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" ); //生产日期
			$data ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM')" ); //保质期至
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
			//销售订单明细表
			$this->_db->insert ( "H01DB012205", $data );	
		}
	}	
	
}	
	
	
	
	
	
	
