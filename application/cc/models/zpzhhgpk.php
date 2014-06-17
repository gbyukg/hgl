<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    赠品转合格品库(ZPZHHGPK)
 * 作成者：苏迅
 * 作成日：2011/07/20
 * 更新履历：
 *********************************/
class cc_models_zpzhhgpk extends Common_Model_Base {
	
	private $idx_ROWNUM=0;		// 行号
	private $idx_ZPBH=1;		// 赠品编号
	private $idx_ZPMCH=2;		// 赠品名称
	private $idx_SHPBH=3;		// 商品编号
	private $idx_SHPMCH=4;		// 商品名称
	private $idx_HWMCH=5;		// 货位名称
	private $idx_BZHSHL=6;		// 包装数量
	private $idx_LSSHL=7;		// 零散数量
	private $idx_SHULIANG=8;	// 数量
	private $idx_JLGG=9;		// 计量规格
	private $idx_GUIGE=10;		// 规格
	private $idx_BZHDWM=11;		// 包装单位
	private $idx_PIHAO=12;		// 批号
	private $idx_SHCHRQ=13;		// 生产日期
	private $idx_BZHQZH=14;		// 保质期至
	private $idx_CHANDI=15;		// 产地
	private $idx_BEIZHU=16;		// 备注
	private $idx_BZHDWBH = 17; 	// 包装单位编号
	private $idx_ZHDKQLX=18;	// 指定库区类型
	private $idx_ZHDKQLXMCH=19;	// 指定库区类型名称
	private $idx_TYMCH=20;		// 通用名称
	private $idx_CKBH=21;		// 仓库编号
	private $idx_KQBH=22;		// 库区编号
	private $idx_KWBH=23;		// 库位编号
	private $idx_SHFSHKW=24;	// 是否散货区
	
	/**
	 * 取得赠品在库信息
	 * @param array $filter 条件数组
	 * @return xml
	 */
	function getZaikuListData($filter) {
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		
		//检索SQL
		$sql = "SELECT ".
			   "ZPBH,".
			   "ZPMCH,".
		       "SHPBH,".
		       "SHPMCH,".
		       "GUIGE,".
		       "SUM(SHULIANG) AS SHULIANG,".
		       "BZHDWMCH,".
			   "PIHAO,".
			   "TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,".
			   "TO_CHAR(BZHQZH,'YYYY-MM-DD') AS BZHQZH,".
			   "CHANDI,".
			   "JLGG,TYMCH,ZHDKQLX,ZHDKQLXMCH,BZHDWBH".
		       " FROM H01VIEW012465".
		       " WHERE QYBH = :QYBH AND SHULIANG > 0";
		
		//快速查找条件
		if($filter["searchkey"]!=""){
			$bind['SEARCHKEY'] =strtolower(($filter["searchkey"]));
			$sql .=" AND (lower(ZPBH) LIKE '%' || :SEARCHKEY || '%' OR  lower(ZPMCH) LIKE '%' || :SEARCHKEY || '%'"; 
			$sql .=" OR lower(SHPBH) LIKE '%' || :SEARCHKEY || '%' OR lower(SHPMCH) LIKE '%' || :SEARCHKEY || '%')";		
		}
			       
		//排序
		$sql .= " GROUP BY ZPBH,ZPMCH,SHPBH,SHPMCH,GUIGE,BZHDWMCH,PIHAO,SHCHRQ,BZHQZH,CHANDI,JLGG,TYMCH,ZHDKQLX,ZHDKQLXMCH,BZHDWBH ORDER BY SHPBH,PIHAO,SHCHRQ";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
	     
		//总行数

		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"] ,$bind);
		
		//当前页数据

		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"],$bind );
		
		return Common_Tool::createXml($recs,true,$totalCount,$filter["posStart"]);
	}
	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck() {
		if ($_POST ["KPRQ"] == "" || //开票日期
            $_POST ["BMBH"] == "" || //部门编号
            $_POST ["YWYBH"] == "" || //业务员编号   
            $_POST ["#grid_mingxi"] == "") { //明细表格
            	
			return false;
		}
		
		$isHasMingxi = false; //是否存在至少一条明细

		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_ZPBH] != "") {
				$isHasMingxi = true;
				if ($grid [$this->idx_SHPBH] == "" || //商品
					$grid [$this->idx_PIHAO] == "" || //批号
					$grid [$this->idx_SHCHRQ] == "" || //生产日期
					$grid [$this->idx_BZHQZH] == "" || //保质期至
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
						$shpchkgxx['status'] = '4';
					}
					
					if($singlechkg["DBZHCH"] == "0" || $singlechkg["DBZHK"] == "0" || $singlechkg["DBZHG"] == "0" ){
						$shpchkgxx['status'] = '3';					
						$singlechkg['exist'] = '1';	
					}else{
						$singlechkg['exist'] = '0';
					}
					$singlechkg['rIdx'] = ( int ) $grid [$this->idx_ROWNUM];
					$shpchkgxx['data'][] = $singlechkg;
				}
	
			}	
		}
	
		return $shpchkgxx;	
	}
	
	/**
	 * 赠品出库信息保存
	 * @param 	string 	$zpckdbh  新生成的赠品出库单编号
	 * @param 	string 	$rkdbh    新生成的合格品入库单编号
	 * @return 	bool
	 */
	public function saveZpChkd($zpckdbh,$rkdbh) {
		
		$zpchkd['QYBH'] = $_SESSION ['auth']->qybh;	
		$zpchkd['ZPCHKDBH'] = $zpckdbh;
		$zpchkd['XSHDBH'] = $rkdbh;
		$zpchkd['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$zpchkd['BMBH'] = $_POST["BMBH"];
		$zpchkd['YWYBH'] = $_POST["YWYBH"];
		$zpchkd['KPYBH'] = $_SESSION ['auth']->userId; //开票员
		$zpchkd['BEIZHU'] = $_POST["BEIZHU"];
		$zpchkd ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$zpchkd ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$zpchkd ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$zpchkd ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		
		$this->_db->insert ( "H01DB012468", $zpchkd );
		
	}
	
	/*
	 * 赠品出库明细信息保存
	 * @param 	string 	$zpckdbh  新生成的赠品出库单编号
	 */
	public function saveZpchkdmx($zpckdbh) {
		$idx_mx = 1; //序号自增
        //循环所有grid明细行，赠品出库明细信息--
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {			
			$zpchkdmx ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$zpchkdmx ['ZPCHKDBH'] = $zpckdbh; 
			$zpchkdmx ['XUHAO'] = $idx_mx ++; //序号
			$zpchkdmx ['ZPBH'] = $grid [$this->idx_ZPBH]; //赠品编号
			$zpchkdmx ['PIHAO'] = $grid [$this->idx_PIHAO]; //批号
			if ($grid [$this->idx_SHCHRQ] != ""){
				$zpchkdmx['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" );
			}
			if ($grid [$this->idx_BZHQZH] != ""){
				$zpchkdmx['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM-DD')" );
			}
			$zpchkdmx ['SHULIANG'] = $grid [$this->idx_SHULIANG]; //数量
			$zpchkdmx ['BEIZHU'] = $grid [$this->idx_BEIZHU]; //备注
			$zpchkdmx ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
			$zpchkdmx ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$zpchkdmx ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$zpchkdmx ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者

			//销售订单明细表
			$this->_db->insert ( "H01DB012469", $zpchkdmx );	
		}
	}
	
	/**
	 * 合格品入库单信息保存
	 * @param 	string 	$zpckdbh  新生成的赠品出库单编号
	 * @param 	string 	$rkdbh    新生成的合格品入库单编号
	 * @return 	bool
	 */
	public function saveHgpRkd($zpckdbh,$rkdbh) {
		
		$rkd['QYBH'] = $_SESSION ['auth']->qybh;	
		$rkd['RKDBH'] = $rkdbh;							//入库单编号：本次新生成的赠品转合格品入库单编号
		$rkd['CKDBH'] = $zpckdbh;						//参考单编号：本次新生成的赠品出库单编号
		$rkd['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$rkd['BMBH'] = $_POST["BMBH"];					//部门编号
		$rkd['YWYBH'] = $_POST["YWYBH"];				//业务员编号
		$rkd['BEIZHU'] = $_POST["BEIZHU"];				//备注
		$rkd['RKLX'] = '3';								//入库类型：3->赠品转入
		$rkd['RKDZHT'] = '1';							//入库单状态：1->未上架确认
		$rkd ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$rkd ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$rkd ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$rkd ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		
		$this->_db->insert ( "H01DB012406", $rkd );
		
	}
	
	/*
	 * 循环读取明细信息,在库信息更新操作--赠品在库信息库存减少,合格品在库信息增加
	 * 
	 * 
	 * @param 	string 	$rkdbh	新生成的合格品入库单编号
	 * @return 	array	$result
	 */
	public function updateKucun($rkdbh,$zpckdbh) {
		$result ['status'] = '0';
		$idx_rukumingxi = 1; //入库单明细信息序号
		$idx_lvli = 1;		 //移动履历序号
			
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
					
					$i++;
				}
			}
		//若用户手动指定库位,画面明细直接作为入库明细数组
		}else{
			$afterassign = $_POST ["#grid_mingxi"];
		}
		//循环入库明细数组$afterassign进行赠品库存数量检查
		//**防止前台画面录入同时赠品库存有可能减少,不够出库！若库存不够出库，返回画面重新输入赠品出库数量，返回最新库存**
		foreach ( $afterassign as $row ) {
			//取得赠品即时库存信息--此种赠品的在库信息
			$sql = "SELECT ZPRKDBH,ZPBH,PIHAO,BZHDWBH,SHULIANG,TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,TO_CHAR(BZHQZH,'YYYY-MM') AS BZHQZH " . 
			       "FROM H01DB012465 " .
			       "WHERE QYBH = :QYBH" . //区域编号
                   " AND CKBH = :CKBH " . //仓库编号
                   " AND ZPBH = :ZPBH " . //赠品编号
                   " AND PIHAO = :PIHAO " . //批号
				   " AND SHULIANG > 0".		//数量
				   " AND TO_CHAR(BZHQZH,'YYYY-MM-DD') = :BZHQZH" . //保质期至
                   " AND BZHDWBH = :BZHDWBH " . 					//包装单位
				   " AND TO_CHAR(SHCHRQ,'YYYY-MM-DD') = :SHCHRQ ".  //生产日期
                   " ORDER BY ZPRKDBH" . //入库单升序
                   " FOR UPDATE  OF SHULIANG WAIT 10"; //对象库存数据锁定
			//绑定查询变量
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['CKBH'] = "ZPK001";						//唯一的赠品库
			$bind ['ZPBH'] = $row [$this->idx_ZPBH];		//赠品编号
			$bind ['PIHAO'] = $row [$this->idx_PIHAO];		//批号
			$bind ['BZHDWBH'] = $row [$this->idx_BZHDWBH];
			$bind ['SHCHRQ'] = $row [$this->idx_SHCHRQ];
			$bind ['BZHQZH'] = $row [$this->idx_BZHQZH];
			
			//当前明细行在库信息
			$recs = $this->_db->fetchAll ( $sql, $bind );
			$shuliang_zaiku = 0; //累计在库数量
			foreach ( $recs as $rec ) {
				$shuliang_zaiku += ( int ) $rec ['SHULIANG'];
			}
			
			//当前库存数量不足，返回画面重新输入赠品出库数量，返回最新库存
			if ($shuliang_zaiku < ( int ) $row [$this->idx_SHULIANG]) {
				$result ['status'] = '1'; //库存不足
				$result ['data']['rIdx'] = ( int ) $row [$this->idx_ROWNUM]; //定位明细行index
				$result ['data']['shuliang'] = $shuliang_zaiku; //最新在库数量
				$result ['data']['pihao'] = $row [$this->idx_PIHAO]; //批号
			}
			
			//赠品库存数量充足
			if($result['status']=='0'){
				//更新赠品在库信息，库存减少，先入先出（入库单）原则进行分摊出库
			    $this->updateZpZaiku ( $row, $recs );
			    //更新合格品在库信息，生成移动履历，生成合格品入库明细，针对画面grid明细
			    $this->updateHgpZaiku( $row, $rkdbh, $idx_rukumingxi,$idx_lvli,$zpckdbh );
			}else{
				break;
			}
		}
					
		return $result;
	}
	
	/*
	 * 更新赠品在库信息，库存减少
	 */
	public function updateZpZaiku($row,$kucuns) {
		//先入先出（入库单）原则进行分摊出库
		$shuliang_shengyu = ( int ) $row [$this->idx_SHULIANG]; 

		foreach ( $kucuns as $kucun ) {
			$shuliang = 0; //赠品在库更新数量
			
			//赠品部分出库时 
			if ($shuliang_shengyu <= ( int ) $kucun ['SHULIANG']) {
				$shuliang = ( int ) $kucun ['SHULIANG'] - $shuliang_shengyu;
				$shuliang_shengyu = 0;
			
			} else { //赠品全部出库--**对应当前赠品入库单全部出库，在库更新数量->0,数量减去当前入库单对应数量,循环下一入库单**
				$shuliang = 0;
				$shuliang_shengyu = $shuliang_shengyu - ( int ) $kucun ['SHULIANG'];
			}
			
			//更新在库信息
			$sql_zaiku = "UPDATE H01DB012465 ".
			             "SET SHULIANG = :SHULIANG " .
			             " WHERE QYBH = :QYBH ".
			             " AND CKBH = :CKBH " .
			             " AND ZPBH = :ZPBH " .
			             " AND PIHAO = :PIHAO " .
			             " AND ZPRKDBH = :ZPRKDBH " .
			             " AND BZHDWBH = :BZHDWBH ".
			             " AND TO_CHAR(SHCHRQ,'YYYY-MM-DD') = :SHCHRQ ".
			             " AND TO_CHAR(BZHQZH,'YYYY-MM-DD') = :BZHQZH";
			             
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['CKBH'] = "ZPK001";
			$bind ['ZPBH'] = $row [$this->idx_ZPBH];
			$bind ['PIHAO'] = $row [$this->idx_PIHAO]; 
			$bind ['BZHDWBH'] = $row [$this->idx_BZHDWBH];
			$bind ['ZPRKDBH'] = $kucun ['ZPRKDBH']; 
			$bind ['SHCHRQ'] = $row [$this->idx_SHCHRQ];
			$bind ['BZHQZH'] = $row [$this->idx_BZHQZH];
			$bind ['SHULIANG'] = $shuliang;               
			$this->_db->query ( $sql_zaiku,$bind );
			
			//所有数量均出库完毕，不再继续循环
			if ($shuliang_shengyu <= 0) break;
		}
	}
	
	/*
	 * 更新合格品在库信息，生成移动履历，生成合格品入库明细
	 */
	public function updateHgpZaiku($row,$rkdbh,&$idx_rukumingxi,&$idx_lvli,$zpckdbh) {
		
		//生产合格品入库单明细信息
		$this->InsertRukumingxi($row,$rkdbh,$idx_rukumingxi);
		$idx_rukumingxi++;
		
		//在库商品信息新生成
		$this->insertZaiku($row,$rkdbh);
		
		//商品移动履历的新生成
		$this->insertLvli($row,$rkdbh,$idx_lvli,$zpckdbh);
		$idx_lvli++;
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
		$data['RKDBH'] = $rkdbh;						//本次赠品转合格品新生成的合格品入库单编号
		$data['XUHAO'] = $idx_rukumingxi;				//序号
		$data['SHPBH'] = $row [$this->idx_SHPBH];
		$data['BZHSHL'] = $row [$this->idx_BZHSHL];
		$data['LSSHL'] = $row [$this->idx_LSSHL];
		$data['SHULIANG'] = $row [$this->idx_SHULIANG];
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
		$data['SHJQRZHT'] = '1';						//上架确认状态："1"->"未上架"
		$data['BGRQ'] = new Zend_Db_Expr('SYSDATE');	//变更日期
		$data['BGZH'] = $_SESSION ['auth']->userId; 	//变更者	
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; 	//作成者
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
		
		$zaiku['QYBH'] = $_SESSION ['auth']->qybh;			//区域编号
		$zaiku['CKBH'] = $row [$this->idx_CKBH];			//仓库编号
		$zaiku['KQBH'] = $row [$this->idx_KQBH];			//库区编号
		$zaiku['KWBH'] = $row [$this->idx_KWBH];			//库位编号
		$zaiku['SHPBH'] = $row [$this->idx_SHPBH];			//商品编号
		$zaiku['PIHAO'] = $row [$this->idx_PIHAO];			//批号
		$zaiku['RKDBH'] = $rkdbh;							//新生成的入库单编号
		$zaiku['ZKZHT'] = '0';								//在库状态 0：可销
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
	 * 			string	$zpckdbh:新生成的赠品出库单编号
	 * @return 	bool	
	 */
	public function insertLvli($row,$rkdbh,$idx_lvli,$zpckdbh) {
		
		$lvli['QYBH'] = $_SESSION ['auth']->qybh;		//区域编号
		$lvli['CKBH'] = $row [$this->idx_CKBH];			//仓库编号
		$lvli['KQBH'] = $row [$this->idx_KQBH];			//库区编号
		$lvli['KWBH'] = $row [$this->idx_KWBH];			//库位编号
		$lvli['SHPBH'] = $row [$this->idx_SHPBH];		//商品编号
		$lvli['PIHAO'] = $row [$this->idx_PIHAO];		//批号
		$lvli['RKDBH'] = $rkdbh;						//新生成的入库单编号					
		$lvli['YDDH'] = $zpckdbh;						//移动单号：新生成的赠品出库单编号
		$lvli['XUHAO'] = $idx_lvli;						//序号
		if ($row [$this->idx_SHCHRQ] != ""){
			$lvli['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_SHCHRQ]."','YYYY-MM-DD')"); //生产日期;
		}
		if ($row [$this->idx_BZHQZH] != ""){
			$lvli['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_BZHQZH]."','YYYY-MM-DD')"); //保质期至
		}
		$lvli['CHLSHJ'] = new Zend_Db_Expr('SYSDATE');	//处理时间
		$lvli['SHULIANG'] = $row [$this->idx_SHULIANG];	//数量：画面grid明细数量--自动分配货位时，计算后对应库位的数量
		$lvli['BZHDWBH'] = $row [$this->idx_BZHDWBH];	//包装单位编号
		$lvli['ZHYZHL'] = '11';							//转移种类？？QA:算正常入库吧？
		$lvli['ZKZHT'] = '0';							//在库状态 0：可销
		$lvli['BEIZHU'] = $row [$this->idx_BEIZHU];		//备注
		$lvli['BGRQ'] = new Zend_Db_Expr('SYSDATE');	//变更日期
		$lvli['BGZH'] = $_SESSION ['auth']->userId; 	//变更者		
		$lvli ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$lvli ['ZCHZH'] = $_SESSION ['auth']->userId; 	//作成者
		$this->_db->insert ( 'H01DB012405', $lvli );
	}
}
	
	
	