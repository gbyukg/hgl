<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    采购入库确认(CGRKQR)
 * 作成者：dltt-苏迅
 * 作成日：2011/6/14
 * 更新履历：
 * 1.自动分配货位追加
 * 2.2011/08/15--入库明细与采购订单可以不完全相符，以前必须完全一致，预付款的情况也必须要修改应付应收信息--修改position(搜索2011/08/15)
 *********************************/
class cc_models_cgrkqr extends Common_Model_Base {
	private $idx_ROWNUM=0;// 行号
	private $idx_SHPBH=1;// 商品编号
	private $idx_SHPMCH=2;// 商品名称
	private $idx_GUIGE=3;// 规格
	private $idx_BZHDWM=4;// 包装单位
	private $idx_PIHAO=5;// 批号
	private $idx_SHCHRQ=6;// 生产日期
	private $idx_BZHQZH=7;// 保质期至
	private $idx_DYQ=8;// 待验区
	private $idx_HWMCH=9;// 货位		
	private $idx_BZHSHL=10;// 包装数量
	private $idx_LSSHL=11;// 零散数量
	private $idx_SHULIANG=12;// 数量
	private $idx_JLGG=13;// 计量规格
	private $idx_DANJIA=14;// 单价
	private $idx_HSHJ=15;// 含税售价
	private $idx_KOULV=16;// 扣率
	private $idx_SHUILV=17;// 税率
	private $idx_JINE=18;// 金额
	private $idx_HSHJE=19;// 含税金额
	private $idx_SHUIE=20;// 税额
	private $idx_LSHJ=21;// 零售价
	private $idx_CHANDI=22;// 产地
	private $idx_BZHDWBH = 23; // 包装单位编号
	private $idx_ZHDKQLX=24;// 指定库区类型
	private $idx_KQLXMCH=25;// 指定库区类型名称
	private $idx_TYMCH=26;// 通用名称
	private $idx_CHBJS=27;// 成本计算
	private $idx_DYQKWBH=28;// 待验区库位编号
	private $idx_CKBH=29;// 仓库编号
	private $idx_KQBH=30;// 库区编号
	private $idx_KWBH=31;// 库位编号
	private $idx_SHFSHKW=32;// 是否散货区


	/**
	 * 得到预入库单列表数据(预入库单选择页面)
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridDanjuData($filter) {
		//排序用字段名
        $fields = array ("", 'YRKDBH', 'CKDBH', 'SHQDH', 'KPRQ', 'DWBH', 'DWMCH', 'BMMCH', 'YWYXM', 'ZCHZHXM', 'DYCGYXM' ); //编号，姓名，所属部门,性别

        //检索SQL
        $sql = "SELECT YRKDBH,CKDBH,SHQDH,TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ,DWBH,DWMCH,BMMCH,YWYXM,ZCHZHXM,DYCGYXM FROM H01VIEW012427 "
               . " WHERE QYBH = :QYBH AND ZHUANGTAI = '0'";

        //绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        
	    //查询条件(开始日期<=开票日期<=终止日期)
        if ($filter ['searchParams']["KSRQKEY"] != "" || $filter ['searchParams']["ZZRQKEY"] != "") {
            $sql .= " AND :KSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :ZZRQ";
            $bind ['KSRQ'] = $filter ['searchParams']["KSRQKEY"] == "" ? "1900-01-01" : $filter ['searchParams']["KSRQKEY"];
            $bind ['ZZRQ'] = $filter ['searchParams']["ZZRQKEY"] == "" ? "9999-12-31" : $filter ['searchParams']["ZZRQKEY"];
        }
        
        //查询条件(单位编号输入)
        if ($filter ['searchParams']["DWBHKEY"] != "") {
            $sql .= " AND DWBH = :DWBH";
            $bind ['DWBH'] = $filter ['searchParams']["DWBHKEY"];
        }
        
        //查询条件(单位编号没输入,只输入单位名称)
        if ($filter ['searchParams']["DWBHKEY"] == "" && $filter ['searchParams']["DWMCHKEY"] != "") {
            $sql .= " AND DWMCH LIKE '%' || :DWMCH || '%'";
            $bind ['DWMCH'] = $filter ['searchParams']["DWMCHKEY"];
        }
        
	    //查询条件(送货清单号)
        if ($filter ['searchParams']["SHQDHKEY"] != "" ) {
            $sql .= " AND SHQDH LIKE '%' || :SHQDH || '%'";
            $bind ['SHQDH'] = $filter ['searchParams']["SHQDHKEY"];
        }
        
        //自动生成精确查询用Sql
        $sql .= Common_Tool::createFilterSql("CC_YRKCGSHH",$filter['filterParams'],$bind);
        
        //排序
        $sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
        //防止重复数据引发翻页排序异常，orderby 项目最后添加主键
        $sql .=",YRKDBH";
        
        //翻页表格用SQL生成(总行数与单页记录)
        $pagedSql = Common_Tool::getPageSql ( $sql, $filter );
        
        //总行数
        $totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
        
        //当前页数据
        $recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
        
        //调用表格xml生成函数
        return Common_Tool::createXml ( $recs, true,$totalCount, $filter ["posStart"] );
			
	}
	
	/**
     * 取得预入库单明细列表数据
     *
     * @param string $yrkdbh
     * @return xml
     */
	public function getmxdata($yrkdbh)
	{
		$sql = 'SELECT SHPBH,SHPMCH,GUIGE,BZHDWMCH,PIHAO,TO_CHAR(SHCHRQ,\'YYYY-MM-DD\'),TO_CHAR(BZHQZH,\'YYYY-MM-DD\'),'
			 . 'DYQKWMCH,HGPSHL,BHGPSHL,JLGG,DANJIA,HSHJ,KOULV,SHUILV,JINE,HSHJE,SHUIE,LSHJ,CHANDI,BZHDWBH,ZHDKQLX,ZHDKQLXMCH,TYMCH,CHBJS,DYQKWBH'
             . ' FROM H01UV012409 WHERE QYBH = :QYBH AND YRKDBH = :YRKDBH';
               
        //绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        $bind['YRKDBH'] = $yrkdbh;
        
        //调用表格xml生成函数
        return Common_Tool::createXml($this->_db->fetchAll($sql, $bind));
	}
	
	
	/*
	 * 根据单位编号取得单位信息
	 * 
	 * @param array $filter
	 * @return array
	 */
	public function getDanweiInfo($filter) {
		//检索SQL
		$sql =  "SELECT A.DWBH,A.DWMCH,A.DIZHI,A.DHHM,HGL_DEC(A.KOULV),A.FHQBH" . 
			    " FROM H01DB012106 A" . " WHERE A.QYBH = :QYBH " . //区域编号
				" AND A.DWBH = :DWBH" . //单位编号
				" AND A.FDBSH ='0'" . //分店标识
				" AND A.SHFJH = '1'" . //是否进货
				" AND A.KHZHT = '1'"; //客户状态

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
	/*
	 * 根据预入库单编号取得预入库单信息
	 * 
	 * @param string $yrkdbh:预入库单编号

	 * @return array
	 */
	public function getSingleYrkdInfo($yrkdbh) {
		
		//查询语句
		$sql = "SELECT TO_CHAR(KPRQ, 'YYYY-MM-DD') AS KPRQ,YRKDBH,SHFZZHSH,CKDBH,CGDDBH,DWBH,DWMCH,DHHM,DIZHI,KOULV,SHQDH,FPBH,BMMCH,YWYXM,BEIZHU,DYCGYXM,FKFSH "
		. "FROM H01VIEW012427 WHERE QYBH = :QYBH AND YRKDBH = :YRKDBH";
		//绑定条件
		$bind['QYBH'] = $_SESSION['auth']->qybh;
        $bind['YRKDBH'] = $yrkdbh;
        
        return $this->_db->fetchRow($sql, $bind);
		     
	}	
	
	/**
	 * 得到预入库单明细列表数据
	 *
	 * @param array $filter
	 * @return array 
	 */
	public function getyrkmingxi($yrkdbh) {	
		//检索SQL
		$sql = "SELECT SHPBH,"
					."SHPMCH,"
					."GUIGE,"
					."BZHDWMCH,"
					."PIHAO,"
					."TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,"
					."TO_CHAR(BZHQZH,'YYYY-MM-DD') AS BZHQZH,"
					."DYQKWMCH,"
					."SUM(HGPSHL) AS SHULIANG,"
					."SUM(BHGPSHL) AS BHGSHULIANG,"
					."HGL_DEC(DANJIA) AS DANJIA,"
					."HGL_DEC(HSHJ) AS HSHJ,"
					."HGL_DEC(KOULV) AS KOULV,"
					."HGL_DEC(SHUILV) AS SHUILV,"
					."HGL_DEC(LSHJ) AS LSHJ,"
					."SUM(JINE) AS JINE,"
					."SUM(HSHJE) AS HSHJE,"
					."SUM(SHUIE) AS SHUIE,"
					."CHANDI,"
					."BZHDWBH,"
					."JLGG,"
					."ZHDKQLXMCH,"
					."ZHDKQLX,"
					."TYMCH,"
					."CHBJS,"
					."DYQKWBH"
		     . " FROM H01UV012409 WHERE QYBH = :QYBH AND YRKDBH = :YRKDBH "
		     . " GROUP BY SHPBH,SHPMCH,GUIGE,BZHDWMCH,PIHAO,SHCHRQ,BZHQZH,DYQKWMCH,DANJIA,HSHJ,KOULV,SHUILV,"
		     . " LSHJ,CHANDI,BZHDWBH,JLGG,ZHDKQLXMCH,ZHDKQLX,TYMCH,CHBJS,DYQKWBH"
		     . " ORDER BY SHPBH,PIHAO";
		     
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['YRKDBH'] = $yrkdbh;
		
		return $this->_db->fetchAll( $sql, $bind );
		
	}
	
	/**
	 * 取得仓库/库区/库位状态信息
	 * @param 	string 	$ckbh	仓库编号
	 * 			string 	$ckbh	库区编号
	 * 			string	$kwbh	库位编号
	 * 
	 * @return 	array 
	 */
	public function getkuweizht($ckbh,$kqbh,$kwbh) {
		$sql = "SELECT C.KWBH,"
			."C.SHFSHKW,"
			."C.KWZHT,"
			."A.CKZHT,"
			."B.KQZHT,"
			."B.KQLX"
			." FROM H01DB012403 C LEFT JOIN H01DB012401 A ON C.QYBH = A.QYBH AND C.CKBH = A.CKBH LEFT JOIN H01DB012402 B ON C.QYBH = B.QYBH AND C.CKBH = B.CKBH AND C.KQBH =B.KQBH"
			." WHERE C.QYBH = :QYBH AND C.CKBH = :CKBH AND C.KQBH = :KQBH AND C.KWBH = :KWBH";
			
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CKBH'] = $ckbh;
		$bind ['KQBH'] = $kqbh;
		$bind ['KWBH'] = $kwbh;
		
		return $this->_db->fetchRow( $sql, $bind );	
	}
	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck() {
		if ($_POST ["KPRQ"] == "" || //开票日期
            $_POST ["BMBH"] == "" || //部门
            $_POST ["YWYBH"] == "" || //业务员
            $_POST ["#grid_mingxi"] == "") { //明细表格
            	
			return false;
		}
		
		$isHasMingxi = false; //是否存在至少一条明细

		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] != "") {
				$isHasMingxi = true;
				if ($grid [$this->idx_PIHAO] == "" || //批号
					$grid [$this->idx_SHULIANG] == "" || //数量
					$grid [$this->idx_SHULIANG] == "0") { //数量
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
		
		return true;
	}
	
	/**
	 * 取得商品大包装长宽高信息
	 * @return 	array 
	 */
	public function getshpchkg() {
		$shpchkgxx['status'] = '0';
		//自动分配货位时
		if($_POST ["AUTOHUOWEI"] == "1"){
			foreach ( $_POST ["#grid_mingxi"] as $grid ) {
				if((int)$grid [$this->idx_BZHSHL] > 0){
					$sql = "SELECT SHPBH,NVL(DBZHCH,0) AS DBZHCH,NVL(DBZHK,0) AS DBZHK,NVL(DBZHG,0) AS DBZHG"
						  ." FROM H01DB012101"
						  ." WHERE QYBH = :QYBH AND SHPBH = :SHPBH";
					
					//绑定查询条件
					$bind ['QYBH'] = $_SESSION ['auth']->qybh;
					$bind ['SHPBH'] = $grid [$this->idx_SHPBH];
					
					$singlechkg = $this->_db->fetchRow( $sql, $bind );
					
					if($singlechkg == false){
						$shpchkgxx['status'] = '4';//商品无效
					}
					
					if($singlechkg["DBZHCH"] == "0" || $singlechkg["DBZHK"] == "0" || $singlechkg["DBZHG"] == "0" ){
						$shpchkgxx['status'] = '3';	//包装信息错误	
						$singlechkg['rIdx'] = ( int ) $grid [$this->idx_ROWNUM];
						$shpchkgxx['bzhdata'][] = $singlechkg;		
					}
				}
	
			}	
		}
	
		return $shpchkgxx;	
	}
	
	/**
	 * 对于指定库位的商品,需要检查该指定库位是否含有其他批号该商品
	 * @return 	array 
	 */
	public function checkpihao(){
		$otherpihao['status'] = '0';
		//手动分配货位时check
		if($_POST ["AUTOHUOWEI"] == "0"){
			foreach ( $_POST ["#grid_mingxi"] as $grid ) {
				
				$sql="SELECT PIHAO FROM " 
				. "(SELECT DISTINCT PIHAO FROM H01DB012404 " 
				. "WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQBH = :KQBH AND KWBH = :KWBH AND SHPBH = :SHPBH AND SHULIANG <> 0) " 
				. "WHERE PIHAO <> :PIHAO";
				//绑定查询条件
				$bind["QYBH"] = $_SESSION["auth"]->qybh;
				$bind["CKBH"] = $grid [$this->idx_CKBH];
				$bind["KQBH"] = $grid [$this->idx_KQBH];
				$bind["KWBH"] = $grid [$this->idx_KWBH];
				$bind["SHPBH"] = $grid [$this->idx_SHPBH];
				$bind["PIHAO"] = $grid [$this->idx_PIHAO];
				
				$otherpihaorow = $this->_db->fetchAll($sql, $bind);
				
				$pihao = "";
				if($otherpihaorow == false){
					continue;
				}else{
					$otherpihao['status'] = '5';
					foreach ($otherpihaorow as $singlerow){
						 $pihao .= $singlerow['PIHAO'].",";
					}
					//$pihao=substr($pihao,0,strlen($pihao)-1);
					$pihao = substr($pihao,0,-1);
					$singlerow['rIdx'] = ( int ) $grid [$this->idx_ROWNUM];
					$singlerow['PIHAO'] = $pihao;
					$singlerow['SHPBH'] = $grid [$this->idx_SHPBH];
					$otherpihao['phdata'][] = $singlerow;
				}
			}

		}
		
		return $otherpihao;
		
	}
	
	/**
	 * 入库单信息保存
	 * @param 	string 	$rkdbh	新生成的采购入库单编号
	 * @return 	bool
	 */
	public function saveRukudan($rkdbh) {
		
		$rukudan['QYBH'] = $_SESSION ['auth']->qybh;	
		$rukudan['RKDBH'] = $rkdbh;
		$rukudan['CKDBH'] = $_POST["YRKDBH"];
		$rukudan['CGDBH'] = $_POST["CGDDBH"]!=''? $_POST["CGDDBH"]:$_POST["CKDBH"];
		$rukudan['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$rukudan['BMBH'] = $_POST["BMBH"];
		$rukudan['YWYBH'] = $_POST["YWYBH"];
		$rukudan['DWBH'] = $_POST["DWBH"];
		$rukudan['DIZHI'] = $_POST["DIZHI"];
		$rukudan['DHHM'] = $_POST["DHHM"];
		$rukudan['SHFZZHSH'] = ($_POST ['SHFZZHSH'] == null) ? '0' : '1';//是否增值税
		$rukudan['KOULV'] = $_POST["KOULV"];
		$rukudan['BEIZHU'] = $_POST["BEIZHU"];
		$rukudan['RKLX'] = '1';								//采购入库
		$rukudan ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$rukudan ['BGZH'] = $_SESSION ['auth']->userId; 	//变更者
		$rukudan ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$rukudan ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$rukudan ['FKFSH'] = $_POST['FKFSH'];

		$this->_db->insert ( "H01DB012406", $rukudan );
		
	}
	
	/**
	 * 保存采购结算信息（应付应收）
	 * 修改履历：入库明细与采购订单可以不完全相符，以前必须完全一致，预付款的情况也必须要修改应付应收信息--2011/08/15修改
	 * @param 	string 	$rkdbh	新生成的采购入库单编号
	 * @return 	bool
	 */
	public function saveCgjs($rkdbh) {
		
		if($_POST['FKFSH'] != '4'){
			$cgjs['QYBH'] = $_SESSION ['auth']->qybh;
			$cgjs['CKDBH'] = $rkdbh;
			$cgjs['JINE'] = $_POST["JINE_HEJI"];
			$cgjs['HSHJE'] = $_POST["HANSHUIJINE_HEJI"];
			$cgjs['YFJE'] = $_POST["HANSHUIJINE_HEJI"];
			$cgjs['ZHFJE'] = 0;
			$cgjs['JSZHT'] = '0';
			$cgjs['FKFSH'] = $_POST['FKFSH'];
			$cgjs['ZHUANGTAI'] = '1';
	
			$this->_db->insert ( "H01DB012310", $cgjs );
			
		}else{
		//预付款时，先取出已入库的金额合计，再更新该预入库单对应的采购订单对应的结算信息--因为预付款的情况在采购订单生成时结算信息已经生成
			//1.取得对应该采购订单的所有入库金额合计			
			$sql_rkmx = "SELECT SUM(JINE) AS JINE,SUM(HSHJE) AS HSHJE FROM H01VIEW012407 WHERE QYBH = :QYBH AND CGDBH = :CGDBH";
			
			$bind_rkmx['QYBH'] = $_SESSION ['auth']->qybh;
			$bind_rkmx['CGDBH'] = $_POST["CGDDBH"]!=''? $_POST["CGDDBH"]:$_POST["CKDBH"];

			$sum = $this->_db->fetchRow($sql_rkmx, $bind_rkmx);
			
			//更新该采购订单的结算信息
			$sql_uptyfys = "UPDATE H01DB012310 SET JINE = :JINE,HSHJE = :HSHJE,YFJE = :HSHJE - ZHFJE"
						  ." WHERE QYBH = :QYBH AND CKDBH = :CKDBH";
			
			$bind_js['QYBH'] = $_SESSION ['auth']->qybh;			 
			$bind_js['CKDBH'] = $_POST["CGDDBH"]!=''? $_POST["CGDDBH"]:$_POST["CKDBH"];
			$bind_js['JINE'] = $sum['JINE'];
			$bind_js['HSHJE'] = $sum['HSHJE'];
			
			$this->_db->query ( $sql_uptyfys, $bind_js );
			
			//结算信息应付若为0，更改状态为已结，应付不为0，更改状态为部分结算
			$sql_jsxx = "SELECT YFJE FROM H01DB012310 WHERE QYBH = :QYBH AND CKDBH = :CKDBH";
			
			unset($bind_js['JINE']);
			unset($bind_js['HSHJE']);
			
			$yfje = $this->_db->fetchOne($sql_jsxx, $bind_js);
			
			if((int) $yfje == 0){
				$jszht = "1";//已结
			}else{
				$jszht = "2";//部分结算
			}
			
			$upt_jszht = "UPDATE H01DB012310 SET JSZHT = :JSZHT WHERE QYBH = :QYBH AND CKDBH = :CKDBH";
			
			$bind_js['JSZHT'] = $jszht;
			
			$this->_db->query ( $upt_jszht, $bind_js );
		}
		

		
	}
	
	/*
	 * 循环读取明细信息,采购入库更新操作--由于同商品同批号待验区可能不同，入库时需要合计同商品同批号同库位的记录,处理麻烦！！QA:是否预入库时必须保证同商品批号必须入一个待验区
	 * 
	 * 1.如果画面grid均未指定库位,即自动分配货位时,调用共通方法,得到入库货位信息(一条画面明细可能对应多个货位),由画面grid明细生成新的入库明细数组(带货位)$afterassign
	 * 2.入库明细数组按商品编号,批号,仓库库区库位编号排序,排序后数组循环生成入库单明细(带待验区位置)--排序为了方便下记3合计处理
	 * 3.同商品同批号同货位，数量金额合计处理，去掉待验区信息,生成新的入库信息数组(更新在库信息，移动履历用)
	 * 4.更新在库信息，移动履历
	 * 
	 * @param 	string 	$rkdbh	新生成的采购入库单编号
	 */
	public function executeMingxi($rkdbh) {	

		$afterassign = array();
		$i=0;
		//若自动分配库位,调用自动分配货位共通方法,返回自动分配库位信息,重新生成入库明细数组$afterassign
		if($_POST ["AUTOHUOWEI"] == "1"){
			foreach ( $_POST ["#grid_mingxi"] as $beforeassign ){
				
				$shpbh = $beforeassign[$this->idx_SHPBH];	//商品编号
				$pihao = $beforeassign[$this->idx_PIHAO];	//批号
				$bzhshl = $beforeassign[$this->idx_BZHSHL];	//包装数量	
				$lsshl = $beforeassign[$this->idx_LSSHL];	//零散数量
				//自动分配货位共通函数--商品编号，批号，包装数量，零散数量
				$auto = new gt_models_tool();
				$kuwei = $auto->autoAssignKuwei($shpbh,$pihao,$bzhshl,$lsshl);
				//返回数组--仓库编号，库区编号，库位编号，数量，是否散货库位
				foreach ($kuwei as $singlekuwei){
					$afterassign[] = $beforeassign;
					$afterassign[$i][$this->idx_CKBH] = $singlekuwei['CKBH'];
					$afterassign[$i][$this->idx_KQBH] = $singlekuwei['KQBH'];
					$afterassign[$i][$this->idx_KWBH] = $singlekuwei['KWBH'];
					if($singlekuwei['SHFSHKW'] == "1"){	//散货货位时
						$afterassign[$i][$this->idx_SHULIANG] = $singlekuwei['SHULIANG'];	//数量 = 共通库位数量
						$afterassign[$i][$this->idx_LSSHL] = $singlekuwei['SHULIANG'];		//零散数量 = 共通库位数量
						$afterassign[$i][$this->idx_BZHSHL] = "0";							//包装数量
					}else{ //包装货位时
						$afterassign[$i][$this->idx_SHULIANG] = ( int )$beforeassign[$this->idx_JLGG] * ( int )$singlekuwei['SHULIANG'];	//数量=库位数量*计量规格
						$afterassign[$i][$this->idx_LSSHL] = "0";							//零散数量
						$afterassign[$i][$this->idx_BZHSHL] = $singlekuwei['SHULIANG'];		//包装数量 = 共通库位数量
					}
					//金额,含税金额,税额重新计算				
					$afterassign[$i][$this->idx_JINE] = ( float )$afterassign[$i][$this->idx_SHULIANG] * ( float )$beforeassign[$this->idx_DANJIA] * ( float )$beforeassign[$this->idx_KOULV] / 100;
					$afterassign[$i][$this->idx_HSHJE] = ( float )$afterassign[$i][$this->idx_SHULIANG] * ( float )$beforeassign[$this->idx_HSHJ] * ( float )$beforeassign[$this->idx_KOULV] / 100;
					$afterassign[$i][$this->idx_SHUIE] = $afterassign[$i][$this->idx_HSHJE] - $afterassign[$i][$this->idx_JINE];
									
					$i++;
				}
			}
		//若用户手动指定库位,画面明细直接作为入库明细数组
		}else{
			$afterassign = $_POST ["#grid_mingxi"];
		}
		
		//画面明细项目排序处理，$sort为排序后数组，按照商品编号，批号，库位升序排序
		$sort = $afterassign;
		foreach ( $sort as $key => $row ) {
			$shpbh[$key] = $row[$this->idx_SHPBH];
			$pihao[$key] = $row[$this->idx_PIHAO];
			$ckbh[$key] = $row[$this->idx_CKBH];
			$kqbh[$key] = $row[$this->idx_KQBH];
			$kwbh[$key] = $row[$this->idx_KWBH];
			
		}
		//array_multisort($shpbh,SORT_ASC,$pihao,SORT_DESC, $_POST ["#grid_mingxi"]);
		array_multisort($shpbh,$pihao,$ckbh,$kqbh,$kwbh,$sort);
				
		$idx_rukumingxi = 1; //入库单明细信息序号
		
		//循环所有明细行--入库单明细需要待验区信息,不sum同商品同批号同货位（待验区可能多个）		
		foreach ( $sort as $row ) {
								
			//生成入库单明细信息
			$this->InsertRukumingxi($row,$rkdbh,$idx_rukumingxi);			
			//入库单明细信息序号自增	
			$idx_rukumingxi ++;
			
		}
		
		$checkvalue = "";
		$curr = "";
		$shuliang = 0;
		$jine = 0;
		$hshje = 0;
		$shuie = 0;				
		$sumsort = array();
		$c = 0;
		
		//同商品同批号同货位，数量金额sum处理，$sumsort为处理后数组，不带待验区信息
		foreach ( $sort as $k => $row ) {
			
			$checkvalue = $row[$this->idx_SHPBH].$row[$this->idx_PIHAO].$row[$this->idx_CKBH].$row[$this->idx_KQBH].$row[$this->idx_KWBH];
			if($checkvalue == $curr || $curr == ""){
				$shuliang += (int) $row[$this->idx_SHULIANG];		//数量加算
				$jine += (float) $row[$this->idx_JINE];				//金额加算
				$hshje += (float) $row[$this->idx_HSHJE];			//含税金额加算
				$shuie += (float) $row[$this->idx_SHUIE];			//税额加算	
			}else{
				$sumsort[] = $sort[$k-1];
				$sumsort[$c][$this->idx_SHULIANG] = $shuliang;
				$sumsort[$c][$this->idx_JINE] = $jine;
				$sumsort[$c][$this->idx_HSHJE] = $hshje;
				$sumsort[$c][$this->idx_SHUIE] = $shuie;
				
				$shuliang = (int) $row[$this->idx_SHULIANG];
				$jine = (float) $row[$this->idx_JINE];
				$hshje = (float) $row[$this->idx_HSHJE];
				$shuie = (float) $row[$this->idx_SHUIE];
				$c++;
			}
			
			$curr = $checkvalue;
		}
		
		$sumsort[] = $sort[$k];
		$sumsort[$c][$this->idx_SHULIANG] = $shuliang;
		$sumsort[$c][$this->idx_JINE] = $jine;
		$sumsort[$c][$this->idx_HSHJE] = $hshje;
		$sumsort[$c][$this->idx_SHUIE] = $shuie;
		//同商品同批号同货位，数量金额sum处理，$sumsort为处理后数组，不带待验区信息--end
		
		
		$idx_lvli = 1; //在库移动履历		
		foreach ( $sumsort as $row ) {
			//在库商品信息新生成
			$this->insertZaiku($row,$rkdbh);
			
			//商品移动履历的新生成
			$this->insertLvli($row,$rkdbh,$idx_lvli);
			$idx_lvli++;
			
			//成本计算
			$this->chbjs($row);
			
			//更新返利协议的累计数量和金额(按商品)--QA:是否多条记录
			$this->updFlxyshp($row);
		}
		
		//更新返利协议的累计数量和金额(按供应商)--QA:是否多条记录
		$this->updFlxygys();
	}
	
	
	/*
	 * 成本计算
	 * @return 
	 */
	public function chbjs($row) {
		if($row[$this->idx_CHBJS] == '001'){		//商品累计
			
			$sql = "SELECT SHPBH FROM H01DB012440 WHERE QYBH = :QYBH AND SHPBH = :SHPBH";
			
			$bind["QYBH"] = $_SESSION["auth"]->qybh;
			$bind["SHPBH"] = $row[$this->idx_SHPBH];
			
			$shpbh = $this->_db->fetchOne($sql, $bind);
			
			if($shpbh==false){
				$data['QYBH'] = $_SESSION ['auth']->qybh;
				$data['SHPBH'] = $row[$this->idx_SHPBH];
				$data['LJSHL'] = $row[$this->idx_SHULIANG];
				$data['LJJE'] = $row[$this->idx_HSHJE];
				$data['CHBDJ'] = (float) $row[$this->idx_HSHJE] / (int) $row[$this->idx_SHULIANG];
				
				$this->_db->insert ( "H01DB012440", $data );
			}else{
				$sql_upt = "UPDATE H01DB012440 SET LJSHL = LJSHL + :LJSHL,LJJE = LJJE + :LJJE,"
						 . "CHBDJ = (LJJE + :LJJE)/(LJSHL + :LJSHL) WHERE QYBH = :QYBH AND SHPBH = :SHPBH";
						 
				$bind['LJSHL'] = $row[$this->idx_SHULIANG];
				$bind['LJJE'] = $row[$this->idx_HSHJE];
				
				$this->_db->query ( $sql_upt, $bind );
			}
			
			
		}elseif($row[$this->idx_CHBJS] == '002'){	//批号计价
			
			$sql1 = "SELECT SHPBH FROM H01DB012441 WHERE QYBH = :QYBH AND SHPBH = :SHPBH AND PIHAO = :PIHAO";
			
			$bind1["QYBH"] = $_SESSION["auth"]->qybh;
			$bind1["SHPBH"] = $row[$this->idx_SHPBH];
			$bind1["PIHAO"] = $row[$this->idx_PIHAO];
			
			$shpbh1 = $this->_db->fetchOne($sql1, $bind1);
			
			if($shpbh1==false){
				$data1['QYBH'] = $_SESSION ['auth']->qybh;
				$data1['SHPBH'] = $row[$this->idx_SHPBH];
				$data1['PIHAO'] = $row[$this->idx_PIHAO];
				$data1['LJSHL'] = $row[$this->idx_SHULIANG];
				$data1['LJJE'] = $row[$this->idx_HSHJE];
				$data1['CHBDJ'] = (float) $row[$this->idx_HSHJE] / (int) $row[$this->idx_SHULIANG];
				
				$this->_db->insert ( "H01DB012441", $data1 );
			}else{
				$sql_upt1 = "UPDATE H01DB012441 SET LJSHL = LJSHL + :LJSHL,LJJE = LJJE + :LJJE,"
						 . "CHBDJ = (LJJE + :LJJE)/(LJSHL + :LJSHL) WHERE QYBH = :QYBH AND SHPBH = :SHPBH AND PIHAO = :PIHAO";
				
				$bind1['LJSHL'] = $row[$this->idx_SHULIANG];
				$bind1['LJJE'] = $row[$this->idx_HSHJE];
				
				$this->_db->query ( $sql_upt1, $bind1 );
			}
		}
	}
	
	/*
	 * 更新客户返利协议--商品
	 * @param 	array 	$row
	 * 
	 */
	public function updFlxyshp($row) {
		//判断该明细对应的商品是否有返利协议--多个or一个？--目前系统可以多个
		$sql = "SELECT A.XYBH FROM H01DB012314 A LEFT JOIN H01DB012313 B ON A.QYBH = B.QYBH AND A.XYBH = B.XYBH "
			 . " WHERE A.QYBH = :QYBH AND B.DWBH = :DWBH AND A.SHPBH = :SHPBH AND A.KSHRQ <= SYSDATE AND A.ZHZHRQ >= SYSDATE";
		
		$bind["QYBH"] = $_SESSION["auth"]->qybh;
		$bind["DWBH"] = $_POST['DWBH'];
		$bind["SHPBH"] = $row[$this->idx_SHPBH];			
		
		//$mxxybhrec = $this->_db->fetchOne($sql, $bind);
		$mxxybhrec = $this->_db->fetchAll($sql, $bind);
		
		//该商品有返利协议，更新该协议合计数量、金额--可能多条记录更新，H01DB012314表里是否应该有客户编号
		if($mxxybhrec!=false){
			unset($bind["DWBH"]);
			foreach ($mxxybhrec as $mxxybhrow){
				$upd_flxymx = "UPDATE H01DB012314 " 
					  . "SET LJSHL = LJSHL + :LJSHL,"
					  . "LJJE = LJJE + :LJJE,"
					  . "BGRQ = SYSDATE,BGZH = :BGZH "
					  . "WHERE QYBH = :QYBH AND XYBH = :XYBH AND SHPBH = :SHPBH";

				$bind["LJSHL"] = $row[$this->idx_SHULIANG];//明细数量
				$bind["LJJE"] = $row[$this->idx_HSHJE];//明细含税金额
				$bind["XYBH"] = $mxxybhrow['xybh'];
				$bind['BGZH'] = $_SESSION ['auth']->userId; //变更者	
			
				$this->_db->query ( $upd_flxymx, $bind );
			}
			
		}
		
	}
	
	/*
	 * 更新客户返利协议--供应商
	 * @param 	array 	$row
	 * 
	 */
	public function updFlxygys() {
		//判断该供应商是否有返利协议--多个or一个？--目前系统可以多个
		$sql = "SELECT XYBH FROM H01DB012313 WHERE QYBH = :QYBH AND DWBH = :DWBH AND KSHRQ <= SYSDATE AND ZHZHRQ >= SYSDATE AND XYLX = '0'";
		
		$bind["QYBH"] = $_SESSION["auth"]->qybh;
		$bind["DWBH"] = $_POST['DWBH'];		
		
		//$mxxybhrec = $this->_db->fetchOne($sql, $bind);
		$flxygysrec = $this->_db->fetchAll($sql, $bind);		
		
		if($flxygysrec != false){
			//更新客户协议的合计数量、金额--可能多个协议更新
			$upd_flxy = "UPDATE H01DB012313 "
					  . "SET LJSHL = LJSHL + :LJSHL,"
					  . "LJJE = LJJE + :LJJE,"
					  . "BGRQ = SYSDATE,BGZH = :BGZH "
					  . "WHERE QYBH = :QYBH AND DWBH = :DWBH AND XYLX = '0' AND KSHRQ <= SYSDATE AND ZHZHRQ >= SYSDATE";
					  
			$bind["LJSHL"] = $_POST['SHULIANG_HEJI'];//总数量
			$bind["LJJE"] = $_POST['HANSHUIJINE_HEJI'];//总含税金额
			$bind['BGZH'] = $_SESSION ['auth']->userId; //变更者
			
			$this->_db->query ( $upd_flxy, $bind );
		}		
	}
	
	/*
	 * 生成入库单明细信息
	 * 
	 * @param 	array 	$row:明细
	 * 			string 	$rkdbh:新生成的采购入库单编号
	 * 			int 	$idx_rukumingxi:入库单明细信息序号	
	 * @return bool 
	 */
	public function InsertRukumingxi($row,$rkdbh,$idx_rukumingxi) {
		
		$data['QYBH'] = $_SESSION ['auth']->qybh;
		$data['RKDBH'] = $rkdbh;
		$data['XUHAO'] = $idx_rukumingxi;
		$data['SHPBH'] = $row [$this->idx_SHPBH];
		$data['BZHSHL'] = $row [$this->idx_BZHSHL];
		$data['LSSHL'] = $row [$this->idx_LSSHL];
		$data['SHULIANG'] = $row [$this->idx_SHULIANG];
		$data['DANJIA'] = $row [$this->idx_DANJIA];
		$data['HSHJ'] = $row [$this->idx_HSHJ];
		$data['KOULV'] = $row [$this->idx_KOULV];
		$data['JINE'] = $row [$this->idx_JINE];
		$data['HSHJE'] = $row [$this->idx_HSHJE];
		$data['SHUIE'] = $row [$this->idx_SHUIE];
		$data['BEIZHU'] = $row [$this->idx_BEIZHU];
		$data['PIHAO'] = $row [$this->idx_PIHAO];
		if ($row [$this->idx_SHCHRQ] != ""){
			$data['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" );
		}
		if ($row [$this->idx_BZHQZH] != ""){
			$data['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_BZHQZH] . "','YYYY-MM-DD')" );
		}
		$data['CKBH'] = $row [$this->idx_CKBH];
		$data['KQBH'] = $row [$this->idx_KQBH];
		$data['KWBH'] = $row [$this->idx_KWBH];
		$data['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
		$data['BGZH'] = $_SESSION ['auth']->userId; //变更者	
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$this->_db->insert ( "H01DB012407", $data );
				
	}
		
	
	/*
	 * 新做成在库商品信息
	 * 
	 * @param 	array 	$row:明细
	 * 			string  $rkdbh:新生成的入库单编号
	 * 
	 * @return 	bool	
	 */
	public function insertZaiku($row,$rkdbh) {
		
		$zaiku['QYBH'] = $_SESSION ['auth']->qybh;
		$zaiku['CKBH'] = $row [$this->idx_CKBH];
		$zaiku['KQBH'] = $row [$this->idx_KQBH];
		$zaiku['KWBH'] = $row [$this->idx_KWBH];
		$zaiku['SHPBH'] = $row [$this->idx_SHPBH];
		$zaiku['PIHAO'] = $row [$this->idx_PIHAO];
		$zaiku['RKDBH'] = $rkdbh;
		$zaiku['ZKZHT'] = '0';
		$zaiku['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$zaiku['ZZHCHKRQ'] = new Zend_Db_Expr("TO_DATE('9999/12/31 23:59:59','YYYY-MM-DD hh24:mi:ss')");
		$zaiku['SHULIANG'] = $row [$this->idx_SHULIANG];
		if ($row [$this->idx_SHCHRQ] != ""){
			$zaiku['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" );
		}
		if ($row [$this->idx_BZHQZH] != ""){
			$zaiku['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_BZHQZH] . "','YYYY-MM-DD')" );
		}
		
		$this->_db->insert ( "H01DB012404", $zaiku );
	}
	
	/*
	 * 移动履历做成
	 * 
	 * @param 	array 	$row:明细
	 * 			string	$rkdbh:新生成的入库单编号
	 * 			int		$idx_lvli:移动履历序号
	 * 
	 * @return 	bool	
	 */
	public function insertLvli($row,$rkdbh,$idx_lvli) {
		
		$lvli['QYBH'] = $_SESSION ['auth']->qybh;
		$lvli['CKBH'] = $row [$this->idx_CKBH];
		$lvli['KQBH'] = $row [$this->idx_KQBH];
		$lvli['KWBH'] = $row [$this->idx_KWBH];
		$lvli['SHPBH'] = $row [$this->idx_SHPBH];
		$lvli['PIHAO'] = $row [$this->idx_PIHAO];
		$lvli['RKDBH'] = $rkdbh;
		$lvli['YDDH'] = $rkdbh;
		$lvli['XUHAO'] = $idx_lvli;
		if ($row [$this->idx_SHCHRQ] != ""){
			$lvli['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_SHCHRQ]."','YYYY-MM-DD')"); //生产日期;
		}
		if ($row [$this->idx_BZHQZH] != ""){
			$lvli['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_BZHQZH]."','YYYY-MM-DD')"); //保质期至
		}
		$lvli['CHLSHJ'] = new Zend_Db_Expr('SYSDATE');
		$lvli['SHULIANG'] = $row [$this->idx_SHULIANG];
		$lvli['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$lvli['ZHYZHL'] = '11';
		$lvli['ZKZHT'] = '0';
		$lvli['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
		$lvli['BGZH'] = $_SESSION ['auth']->userId; //变更者		
		$lvli ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$lvli ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$this->_db->insert ( 'H01DB012405', $lvli );
	}
	

	

	
	/*
	 * 更新采购订单状态--2011/08/12修改--更新采购订单明细状态,所有明细均已入库才能更新采购订单状态
	 * 
	 * @return 	bool	
	 */
	public function uptCgddZht() {
		//取预入库的所有商品品目
		$sql_yrk = "SELECT DISTINCT SHPBH FROM H01DB012428 WHERE QYBH = :QYBH AND YRKDBH = :YRKDBH";
		
		$bind_yrk['QYBH'] = $_SESSION ['auth']->qybh;
		$bind_yrk['YRKDBH'] = $_POST["YRKDBH"];
		
		$shpbh = $this->_db->fetchAll($sql_yrk, $bind_yrk);
		
		//更新采购订单明细入库状态--预入库明细中的商品
		foreach ($shpbh as $row_shp){
			$sql_updcgmx = "UPDATE H01DB012307 SET RKZHT = '2',BGRQ = sysdate,BGZH = :BGZH"
					   . " WHERE QYBH = :QYBH AND CGDBH = :CGDDBH AND SHPBH =:SHPBH";
					   
			$bind_updcgmx['QYBH'] = $_SESSION ['auth']->qybh;
			$bind_updcgmx ['BGZH'] = $_SESSION ['auth']->userId;
			//预入库采购审核时,若重新做成采购订单,预入库单中：采购订单编号为新订单编号,参考单编号为原采购订单编号
			$bind_updcgmx['CGDDBH'] = $_POST["CGDDBH"]!=''? $_POST["CGDDBH"]:$_POST["CKDBH"];
			$bind_updcgmx['SHPBH'] = $row_shp['SHPBH'];
			
			$this->_db->query ( $sql_updcgmx,$bind_updcgmx );
		}
		
		//判断采购订单明细中是否均已入库
		//取得采购订单明细中入库状态不为已入库的和为null的
		$sql_isallrk = "SELECT DISTINCT SHPBH FROM H01DB012307 WHERE QYBH = :QYBH AND CGDBH = :CGDDBH AND RKZHT <> '2'";
		
		$bind_isallrk['QYBH'] = $_SESSION ['auth']->qybh;
		//预入库采购审核时,若重新做成采购订单,预入库单中：采购订单编号为新订单编号,参考单编号为原采购订单编号
		$bind_isallrk['CGDDBH'] = $_POST["CGDDBH"]!=''? $_POST["CGDDBH"]:$_POST["CKDBH"];
		
		//取得结果为空,即明细已全部入库,更新采购订单状态
		if( $this->_db->fetchAll($sql_isallrk, $bind_isallrk) == false){
			
			$sql_update = "UPDATE H01DB012306"
						. " SET CGDZHT = '1',"
						. " BGRQ = sysdate," 
						. " BGZH = :BGZH"
						. " WHERE QYBH = :QYBH AND CGDBH = :CGDDBH";
		
			$bind_update['QYBH'] = $_SESSION ['auth']->qybh;
			$bind_update['CGDDBH'] = $_POST["CGDDBH"]!=''? $_POST["CGDDBH"]:$_POST["CKDBH"];
			$bind_update ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			
			$this->_db->query ( $sql_update,$bind_update );
		}
		
	}
	
	/*
	 * 更新预入库单状态
	 * 
	 * @return 	bool
	 */
	public function uptYrkdZht() {
		$sql = "UPDATE H01DB012427"
			 . " SET ZHUANGTAI = '4',"
			 . " BGRQ = sysdate," 
			 . " BGZH = :BGZH"
			 . " WHERE QYBH = :QYBH AND YRKDBH = :YRKDBH";
		
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['YRKDBH'] = $_POST["YRKDBH"];
		$bind ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			
		$this->_db->query ( $sql,$bind );
	}
	
	/*
	 * 不合格品处理
	 * 
	 */
	public function executeBhgp() {
		$sql = "SELECT SHPBH,PIHAO,SHCHRQ,BZHQZH,"
		     . "SUM(BHGPSHL) AS BHGPSHL,SUM(BHGPSHL)*DANJIA*KOULV/100 AS BHGPJINE,SUM(BHGPSHL)*HSHJ*KOULV/100 AS BHGPHSHJE,"
		     . "(SUM(BHGPSHL)*HSHJ*KOULV/100 - SUM(BHGPSHL)*DANJIA*KOULV/100) AS BHGPSHUIE,DANJIA,HSHJ,KOULV,JLGG,BZHDWBH FROM H01UV012409 "
		     . "WHERE QYBH = :QYBH AND YRKDBH = :YRKDBH AND BHGPSHL > 0 GROUP BY SHPBH,PIHAO,SHCHRQ,BZHQZH,DANJIA,HSHJ,KOULV,JLGG,BZHDWBH";

		$bind["QYBH"] = $_SESSION["auth"]->qybh;
		$bind["YRKDBH"] = $_POST["YRKDBH"];
		
		$bhgprec = $this->_db->fetchAll( $sql, $bind );
		
		if($bhgprec != false){
			//取得不合格品入库单编号
			$bhrbh = Common_Tool::getDanhao('BHR',$_POST['KPRQ']);
			//保存单据信息到DB:不合格品入库单信息（H01DB012460)
			$this->insBhgprkd($bhrbh);
			
			$bhgindex = 1;
			foreach ($bhgprec as $row){
				//保存单据信息到DB:不合格品入库单明细信息（H01DB012461)
				$this->insBhgprkdmx($bhrbh,$row,$bhgindex);
				$bhgindex++;
				//保存单据信息到DB:不合格品在库商品信息（H01DB012459)
				$this->insBhgpzaiku($row,$bhrbh);
			}
		}
		     
	}
	
	/*
	 * 保存单据信息到DB:不合格品入库单信息（H01DB012460)
	 * 
	 * @param 	string $bhrbh--新生成的不合格品入库单编号
	 * @return 	bool
	 */
	public function insBhgprkd($bhrbh) {
		$data['QYBH'] = $_SESSION ['auth']->qybh;
		$data['BHGPRKDBH'] = $bhrbh;
		$data['CKDBH'] = $_POST["CGDDBH"]!=''? $_POST["CGDDBH"]:$_POST["CKDBH"];
		$data['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$data['BMBH'] = $_POST["BMBH"];
		$data['KPYBH'] = $_SESSION ['auth']->userId;
		$data['YWYBH'] = $_POST["YWYBH"];
		$data['RKLX'] = '1';
		$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId; 	//变更者
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			
		$this->_db->insert ( 'H01DB012460', $data );
	}
	
	/*
	 * 保存单据信息到DB:不合格品入库单明细信息（H01DB012461)
	 * 
	 * @param 	string $bhrbh--新生成的不合格品入库单编号,array $row,int $bhgindex
	 * @return 	bool
	 */
	public function insBhgprkdmx($bhrbh,$row,$bhgindex) {
		
		$data['QYBH'] = $_SESSION ['auth']->qybh;
		$data['BHGPRKDBH'] = $bhrbh;
		$data['XUHAO'] = $bhgindex;
		//$data['CKBH'] = $_SESSION ['auth']->bhgckbh; //不合格品仓库编号--session取得--以后对应
		$data['CKBH'] = "bhg001";
		$data['SHPBH'] = $row ['SHPBH'];
		$data['PIHAO'] = $row ['PIHAO'];
		if ($row ['SHCHRQ'] != ""){
			$data['SHCHRQ'] = $row ['SHCHRQ'];
		}
		if ($row ['BZHQZH'] != ""){
			$data['BZHQZH'] = $row ['BZHQZH'];
		}		
		$data['BZHSHL'] = floor((int)$row ["BHGPSHL"] /(int)$row ["JLGG"]);
		$data['LSSHL'] = (int)$row ["BHGPSHL"]%(int)$row ["JLGG"];
		$data['SHULIANG'] = $row ["BHGPSHL"];
		$data['DANJIA'] = $row ["DANJIA"];
		$data['HSHJ'] = $row ["HSHJ"];
		$data['KOULV'] = $row ["KOULV"];
		$data['JINE'] = $row ["BHGPJINE"];
		$data['HSHJE'] = $row ["BHGPHSHJE"];
		$data['SHUIE'] = $row ["BHGPSHUIE"];
		$data['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
		$data['BGZH'] = $_SESSION ['auth']->userId; //变更者	
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$this->_db->insert ( "H01DB012461", $data );
	}
	
	/*
	 * 新做成不合格品在库商品信息
	 * 
	 * @param 	array 	$row:明细
	 * 			string  $bhrbh:新生成的不合格品入库单编号
	 * 
	 * @return 	bool	
	 */
	public function insBhgpzaiku($row,$bhrbh) {
		
		$data['QYBH'] = $_SESSION ['auth']->qybh;
		//$data['CKBH'] = $_SESSION ['auth']->bhgckbh; //不合格品仓库编号--session取得--以后对应
		$data['CKBH'] = "bhg001";
		$data['SHPBH'] = $row ["SHPBH"];
		$data['PIHAO'] = $row ["PIHAO"];
		$data['RKDBH'] = $bhrbh;
		$data['BZHDWBH'] = $row ["BZHDWBH"];
		$data['SHULIANG'] = $row ["BHGPSHL"];
		if ($row ['SHCHRQ'] != ""){
			$data['SHCHRQ'] = $row ['SHCHRQ'];
		}
		if ($row ['BZHQZH'] != ""){
			$data['BZHQZH'] = $row ['BZHQZH'];
		}	
		
		$this->_db->insert ( "H01DB012459", $data );
	}
	
	public function updateShpdbzhxx(){
		$sql = "UPDATE H01DB012101 SET DBZHCH = :DBZHCH,DBZHK = :DBZHK,DBZHG = :DBZHG WHERE QYBH = :QYBH AND SHPBH = :SHPBH";
		
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$bind ['SHPBH'] = $_POST ['SHPBH']; //商品编号
		$bind ['DBZHCH'] = $_POST ['DBZHCH']; //商品大包装长
		$bind ['DBZHK'] = $_POST ['DBZHK']; //商品大包装宽
		$bind ['DBZHG'] = $_POST ['DBZHG']; //商品大包装高
		
		$this->_db->query ( $sql, $bind );
	}
	
	/**
	 * 判断选择库位选定商品是否存在其他批号
	 *
	 * @param array $filter
	 * @return array json
	 */
	public function pdPhHw($filter)
	{
		$sql="SELECT PIHAO FROM " 
		. "(SELECT DISTINCT PIHAO FROM H01DB012404 " 
		. "WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQBH = :KQBH AND KWBH = :KWBH AND SHPBH = :SHPBH AND SHULIANG <> 0) " 
		. "WHERE PIHAO <> :PIHAO";
		//绑定查询条件
		$bind["QYBH"] = $_SESSION["auth"]->qybh;
		$bind["CKBH"] = $filter["ckbh"];
		$bind["KQBH"] = $filter["kqbh"];
		$bind["KWBH"] = $filter["kwbh"];
		$bind["SHPBH"] = $filter["shpbh"];
		$bind["PIHAO"] = $filter["pihao"];
		return $this->_db->fetchAll($sql, $bind);
	}
	

	

}