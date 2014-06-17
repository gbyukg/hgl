<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   随时报损报溢(ssbsby)
 * 作成者：李洪波
 * 作成日：2011/01/17
 * 更新履历：
 * 1.赔偿机能增加--苏迅--2011/08/05
 * (1)需要赔偿时,赔偿销售单及销售明细生成,库存减少,商品移动履历生成(转移种类：销售出库)
 * (2)需要赔偿时,盘点单及盘点明细单也要生成
 * (3)需要赔偿时,需生成应付应收信息
 *********************************/
class cc_models_ssbsby extends Common_Model_Base {

		private $idx_ROWNUM=0;		// 行号
		private $idx_SHPBH=1;		// 商品编号
		private $idx_KWMCH=2;		// 库位编号
		private $idx_SHPMCH=3;		// 商品名称
		private $idx_SHPGG=4;		// 商品规格
		private $idx_BZHDWMCH=5;	// 包装单位
		private $idx_PIHAO=6;		// 批号
		private $idx_SHCHRQ=7;		// 生产日期
		private $idx_BZHSHL=8;		// 包装数量
		private $idx_LSSHL=9;		// 零散数量
		private $idx_SHPSHL=10;		// 实盘数量
		private $idx_SHPJE=11;		// 实盘金额
		private $idx_SHULIANG=12;	// 账面数量
		private $idx_JINE=13;		// 账面金额
		private $idx_CHBDJ=14;		// 成本单价
		private $idx_PSSHL=15;		// 盘损数量
		private $idx_PSJE=16;		// 盘损金额
		private $idx_LSHJ=17;		// 零售价
		private $idx_CHANDI=18;		// 产地
		private $idx_KWBH=19;		// 库位编号
		private $idx_BZHDWBH=20;	// 包装单位
		private $idx_JLGG=21;		// 计量规格
		private $idx_BZHQZH=22;		// 保质期至
		
	/*
	 * 列表数据取得(xml格式)
	 */
	function getListData($filter) {
		
		//检索SQL
			$sql = "SELECT KWMCH,DECODE(SHFSHKW,'0','整件库位','1','零散库位'),".
				"PIHAO,SUM (SHULIANG) AS PCSHL,NEIRONG".
				",BZHQZH,SHCHRQ,KWBH,BZHDWBH,ZKZHT".
		       	" FROM HO1UV012403".
				" WHERE QYBH=:QYBH".
				" AND SHPBH=:SHPBH".
				" AND (ZKZHT='0' or ZKZHT='1')".
				" AND CKBH=:CKBH".
				" AND KQBH=:KQBH";
				
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['SHPBH'] = $filter ['shpbh']; //商品编号
		$bind ['CKBH'] = $filter ['ckbh']; //仓库编号
		$bind ['KQBH'] = $filter ['kqbh']; //库区编号
	
        //自动生成精确查询用Sql
		$sql.= Common_Tool::createFilterSql("CC_KWPHXZ",$filter['filterParams'],$bind);
		$sql.=" GROUP BY KWMCH,SHFSHKW,PIHAO,NEIRONG,BZHQZH,SHCHRQ,KWBH,BZHDWBH,ZKZHT";

		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true,$totalCount, $filter ["posStart"] );

	}

	/*
	 * 自动完成数据取得
	 */
	public function getAutocompleteData($filter){
			//检索SQL
			$sql = "SELECT KWMCH,SHFSHKW,".
				"PIHAO,SUM (SHULIANG) AS PCSHL,NEIRONG".
				",BZHQZH,SHCHRQ,KWBH,BZHDWBH,ZKZHT".
		       	" FROM HO1UV012403".
				" WHERE QYBH=:QYBH".
				" AND SHPBH=:SHPBH".
				" AND (ZKZHT='0' or ZKZHT='1')".
				" AND CKBH=:CKBH".
				" AND KQBH=:KQBH".
				" GROUP BY KWMCH,SHFSHKW,.PIHAO,NEIRONG,BZHQZH,SHCHRQ,KWBH,BZHDWBH,ZKZHT";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['SHPBH'] = $filter ['shpbh']; //商品编号
		$bind ['CKBH'] = $filter ['ckbh']; //仓库编号
		$bind ['KQBH'] = $filter ['kqbh']; //库区编号
	
		return $this->_db->fetchAll($sql,$bind);

	}
	
	/*
	 * 根据商品编号取得商品信息
	 */
	public function getShangpinInfo($filter) {
		
		//检索SQL
		$sql = "SELECT " .
		       "SHPBH," . //商品编号
               "SHPMCH," . //商品名称
           	   "BZHDWMCH,GUIGE,LSHJ,JLGG,".
			   "CHANDI".															
			   " FROM H01VIEW012101 ".
			   " WHERE QYBH = :QYBH AND SHPBH =:SHPBH";
		$bind ['SHPBH'] = $filter ['shpbh']; //商品编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		return $this->_db->fetchRow ( $sql, $bind );
	}	
	
	/*
	 * 取得商品成本单价
	 */
	public function getChbdjInfo($filter) {
		
		//检索SQL
		$sql = "SELECT CHBJS".															
			   " FROM H01VIEW012101 ".
			   " WHERE QYBH = :QYBH AND SHPBH =:SHPBH";
		$bind ['SHPBH'] = $filter ['shpbh']; //商品编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$chbjs = $this->_db->fetchOne ( $sql, $bind );
		
		$chbdj = 0;
		
		if($chbjs=="001"){
			$sql_shp = "SELECT HGL_DEC(CHBDJ) AS CHBDJ FROM H01DB012440 WHERE QYBH = :QYBH AND SHPBH =:SHPBH";
			$chbdj = $this->_db->fetchOne ( $sql_shp, $bind );
		}elseif($chbjs=="002"){
			$sql_pihao = "SELECT HGL_DEC(CHBDJ) AS CHBDJ FROM H01DB012441 WHERE QYBH = :QYBH AND SHPBH =:SHPBH AND PIHAO = :PIHAO";
			$bind["PIHAO"] = $filter ['pihao'];
			$chbdj = $this->_db->fetchOne ( $sql_pihao, $bind );
		}
		
		return $chbdj;
	}	
	
	/*
	 * 实盘信息保存
	 */
	public function accountSsbsby($djbh) {
		//①仓库的存在check
		$ckcheckSql="SELECT COUNT(*) as SHULIANG".															
					" FROM H01DB012401". 														
					" WHERE QYBH =:QYBH".															
					" AND CKBH =:CKBH"	.														
					" AND CKZHT = '1' ";
		
		$checkData["QYBH"]=$_SESSION ['auth']->qybh; 		//区域编号;
		$checkData["CKBH"]=$_POST ["CKBH_H"]; 		//仓库编号;
		$ckcheck=$this->_db->fetchAll($ckcheckSql, $checkData);
		if (count($ckcheck)==0 || $ckcheck[0]["SHULIANG"]<=0){
			$result["status"]=1;
			return $result;
		}
		
		//②库区的存在check
		
		$kqcheckSql="SELECT COUNT(*) as SHULIANG".															
					" FROM H01DB012402". 														
					" WHERE QYBH =:QYBH".															
					" AND CKBH =:CKBH".
					" AND KQBH =:KQBH".															
					" AND KQZHT = '1' ";

		$checkData["KQBH"]=$_POST ["KQBH_H"]; 		//库区编号
		$kqcheck=$this->_db->fetchAll($kqcheckSql, $checkData);
		if (count($kqcheck)==0 || $kqcheck[0]["SHULIANG"]<=0){
			$result["status"]=2;
			return $result;
		}	
		
		//新增盘点信息
		$pdxxData["QYBH"]=$_SESSION ['auth']->qybh; 		//区域编号
		$pdxxData["DJBH"]=$djbh;							//单据编号		
		$pdxxData["PDLX"]=2;								//盘点类型
		$pdxxData["PDKSHSHJ"]=new Zend_Db_Expr ( "TO_DATE('" . $_POST ["KPRQ"] . "','YYYY-MM-DD')" );				//盘点开始时间
		$pdxxData["PDJSHSHJ"]=null;							//盘点结束时间
		$pdxxData["PDJHDH"]=null;							//盘点计划单号
		$pdxxData["CKBH"]=$_POST ["CKBH_H"];				//仓库编号														
		$pdxxData["KQBH"]=$_POST ["KQBH_H"];				//库区编号												
		$pdxxData["KWBH"]=null;								//库位编号
		$pdxxData["ZHMSHLTJ"]=null;							//账面数量条件
		$pdxxData["DJBZH"]=null;							//冻结标志
		$pdxxData["YWYBH"]=$_POST ["YEWUYUAN_H"];			//业务员													
		$pdxxData["BMBH"]=$_SESSION ['auth']->bmbh;				//部门													
		$pdxxData["SHPYWY"]=$_POST ["YEWUYUAN_H"];			//实盘业务员													
		$pdxxData["SHPBM"]=$_SESSION ['auth']->bmbh;				//实盘部门													
		$pdxxData["PDZHT"]="2";								//盘点状态
		$pdxxData["JZHZHT"]="2";								//记账状态
		$pdxxData["ZHMJEHJ"]=$_POST ["ZHJEHJ_H"];			//账面金额合计
		$pdxxData["SHPJEHJ"]=$_POST ["SPJEHJ_H"];			//实盘金额合计			
		$pdxxData["SYJEHJ"]=$_POST ["SYJEHJ_H"];			//损溢金额合计								
		$pdxxData["BEIZHU"]=$_POST ["BEIZHU"];				//备注																
		$pdxxData['BGRQ'] = new Zend_Db_Expr ("SYSDATE");	//变更日期
		$pdxxData['BGZH'] = $_SESSION ['auth']->userId; 	//变更者	
		$pdxxData ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$pdxxData ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		
		//赔偿机能增加--苏迅--2011/08/05
		if($_POST["SHFPCH"] != null){
			$pdxxData["SHFPCH"] = "1";//员工赔偿
		}else{
			$pdxxData["SHFPCH"] = "0";//不赔偿
		}
		//赔偿机能增加--苏迅--2011/08/05
			
		$this->_db->insert ("H01DB012417", $pdxxData);	
		
		//赔偿机能增加--苏迅--2011/08/05--赔偿销售单及明细生成以及结算信息
		if($_POST["SHFPCH"] != null){
			
			$xshdbhforpay = Common_Tool::getDanhao('XSD'); 			//销售单编号--赔偿销售
		
			$xshd ['QYBH'] = $_SESSION ['auth']->qybh; 				//区域编号
			$xshd ['XSHDBH'] = $xshdbhforpay; 						//销售单编号--赔偿销售
			$xshd ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
			$xshd ['BMBH'] = $_SESSION ['auth']->bmbh; 				//部门编号
			$xshd ['KPYBH'] = $_SESSION ['auth']->userId; 			//开票员编号
			$xshd ['YWYBH'] = $_POST ['YWYBH']; 					//业务员编号
			$xshd ['DWBH'] = "99999999"; 							//单位编号--公共名称(赔偿专用)
			$xshd ['BEIZHU'] = $_POST ['PCHR']; 					//备注--画面赔偿人项
			$xshd ['HSHJE'] = $_POST ['SUM_PSJE_H'];				//含税金额--盘损金额合计--不算盘盈
			$xshd ['SHULIANG'] = $_POST ['SUM_PSSHL_H']; 			//数量--盘损数量合计--不算盘盈
			$xshd ['PCHTHDBH'] = $djbh;								//本次盘点单据号
			$xshd ['QXBZH'] = '1';//取消标志
			$xshd ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); 		//作成日期
			$xshd ['ZCHZH'] = $_SESSION ['auth']->userId; 			//作成者
			$xshd ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); 		//变更日期
			$xshd ['BGZH'] = $_SESSION ['auth']->userId; 			//变更者
			//销售订单信息表
			$this->_db->insert ( "H01DB012201", $xshd );
			
			//生成赔偿销售单结算信息
			$jsd["QYBH"] = $_SESSION ['auth']->qybh;
			$jsd["XSHDBH"] = $xshdbhforpay; //新生成的赔偿销售单编号
			//$jsd["JINE"] = $_POST ['JINE_HEJI']; //金额
			$jsd["HSHJE"] = $_POST ['SUM_PSJE_H'];//含税金额
			$jsd["YSHJE"] = $_POST ['SUM_PSJE_H'];//应收金额
			$jsd["SHQJE"] = "0"; //收取金额
			$jsd["JSRQ"] = new Zend_Db_Expr ("TO_DATE('1900-01-01','YYYY-MM-DD')"); //结算日期
			$jsd["JIESUANREN"] = ""; //结算人
			$jsd["JSZHT"] = "0"; //结算状态 未结
			//结算单
			$this->_db->insert("H01DB012208",$jsd);	
			
			//生成赔偿销售订单明细--只针对画面grid盘损记录
			$idx=1;
			foreach ( $_POST ["#grid_ssbsby"] as $grid ) {			
				if($grid [$this->idx_PSSHL] > 0){
					$xshdmx ['QYBH'] = $_SESSION ['auth']->qybh; 			//区域编号
					$xshdmx ['XSHDBH'] = $xshdbhforpay; 					//销售单编号--赔偿销售
					$xshdmx ['XUHAO'] = $idx ++; 							//序号
					$xshdmx ['SHPBH'] = $grid [$this->idx_SHPBH]; 			//商品编号
					$xshdmx ['PIHAO'] = $grid [$this->idx_PIHAO]; 			//批号
					$xshdmx ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" ); //生产日期
					$xshdmx ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM-DD')" ); //保质期至
					$xshdmx ['SHULIANG'] = $grid [$this->idx_PSSHL]; 		//数量
					$xshdmx ['BZHSHL'] = (int)($grid [$this->idx_PSSHL] / $grid [$this->idx_JLGG]) ;			//包装数量
					$xshdmx ['LSSHL'] = (int)($grid [$this->idx_PSSHL] % $grid [$this->idx_JLGG]);				//零散数量					
					$xshdmx ['HSHJ'] = $grid [$this->idx_CHBDJ]; 			//含税价--取画面成本单价？？QA
					//$xshdmx ['JINE'] = $grid [$this->idx_JINE]; 			//金额--QA
					//$xshdmx ['SHUIE'] = $grid [$this->idx_SHUIE]; 		//税额--QA
					//$xshdmx ['DANJIA'] = $grid [$this->idx_CHBDJ]; 			//单价--QA
					$xshdmx ['HSHJE'] = $grid [$this->idx_PSJE]; 			//含税金额--取盘损金额					
					$xshdmx ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); 		//变更日期
					$xshdmx ['BGZH'] = $_SESSION ['auth']->userId; 			//变更者
					$xshdmx ['ZCHRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); 	//作成日期
					$xshdmx ['ZCHZH'] = $_SESSION ['auth']->userId; 		//作成者
					//销售订单明细表
					$this->_db->insert ( "H01DB012202", $xshdmx );					
					
				}
			}
		}
		//赔偿机能增加--苏迅--2011/08/05--赔偿销售单及明细生成

		
        //循环所有明细行，保存实盘明细信息
		foreach ( $_POST ["#grid_ssbsby"] as $grid ) {
			$rkdbh="";
			//实盘明细信息
			$data ['QYBH'] = $_SESSION ['auth']->qybh; 		//区域编号
			$data ['DJBH'] = $djbh; 						//单据编号
			$data ['SHPBH'] = $grid [$this->idx_SHPBH];		//商品编号
			$data ['BZHDWBH'] = $grid [$this->idx_BZHDWBH];	//包装单位
			$data ['PIHAO'] = $grid [$this->idx_PIHAO];		//批号
			$data ['KWBH'] = $grid [$this->idx_KWBH];		//库位编号
			$data ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" );	//生产日期
			$data ['BZHSHL'] = $grid [$this->idx_BZHSHL];	//包装数量
			$data ['LSSHL'] = $grid [$this->idx_LSSHL];		//零散数量
			$data ['SHULIANG'] = $grid [$this->idx_SHULIANG];	//数量
			$data ['JINE'] = $grid [$this->idx_JINE];		//金额
			$data ['SHPSHL'] = $grid [$this->idx_SHPSHL];	//实盘数量
			$data ['SHPJE'] = $grid [$this->idx_SHPJE];		//实盘金额
			$data ['CHBDJ'] = $grid [$this->idx_CHBDJ];		//成本单价
			$data ['PSSHL'] = $grid [$this->idx_PSSHL];	    //盘损数量
			$data ['PSJE'] = $grid [$this->idx_PSJE]; 		//盘损金额			
			$data ['BGRQ'] = new Zend_Db_Expr ("SYSDATE");	//变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; 	//变更者			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			$data ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM-DD')" );	//保质期至
		
			$this->_db->insert ("H01DB012418", $data);
			$pyshl=$grid [$this->idx_SHULIANG]-$grid [$this->idx_SHPSHL];	
					
			//当盘损数量 < 0
			if($pyshl < 0){
				$pyshl=$pyshl*(-1);
				//1.判断该商品的盘溢库存信息是否存在。
				$selectLockSql="SELECT SHULIANG,RKDBH,".
						"TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,".
						"TO_CHAR(BZHQZH,'YYYY-MM-DD') AS BZHQZH,ZKZHT".
						" FROM H01DB012404".
						" WHERE QYBH=:QYBH AND CKBH=:CKBH".
						" AND KQBH=:KQBH AND KWBH=:KWBH".
						" AND SHPBH=:SHPBH AND PIHAO=:PIHAO".
						" AND BZHDWBH=:BZHDWBH ".
						" AND SHCHRQ=TO_DATE(:SHCHRQ,'YYYY-MM-DD')".
						" AND ZKZHT = '0' AND RKDBH='99999999999999'".
						" FOR UPDATE";
				$zkData["QYBH"]=$_SESSION ['auth']->qybh; 		//区域编号
				$zkData['CKBH'] = $_POST ["CKBH_H"]; 		//仓库编号
				$zkData['KQBH'] = $_POST ["KQBH_H"]; 		//库区编号
				$zkData['SHPBH'] = $grid [$this->idx_SHPBH];		//商品编号
				$zkData['KWBH'] = $grid [$this->idx_KWBH]; 		//库位编号
				$zkData['PIHAO'] = $grid [$this->idx_PIHAO]; 		//批号
				$zkData['BZHDWBH'] = $grid [$this->idx_BZHDWBH];		//包装单位
				$zkData['SHCHRQ'] = $grid [$this->idx_SHCHRQ]; 		//生产日期
				
				$shpzkxx=$this->_db->fetchAll($selectLockSql, $zkData);
				
				if(count($shpzkxx)!=0){
					$updatezkshpSql="UPDATE H01DB012404 SET ".
									"SHULIANG=SHULIANG +:SHULIANG,ZZHCHKRQ=TO_DATE('9999-12-31 23:59:59','YYYY-MM-DD HH24:MI:SS')".
									" WHERE QYBH=:QYBH AND CKBH=:CKBH AND SHPBH=:SHPBH AND PIHAO=:PIHAO".
									" AND BZHDWBH=:BZHDWBH AND KWBH=:KWBH".
									" AND SHCHRQ=TO_DATE(:SHCHRQ,'YYYY-MM-DD') AND ZKZHT = '0'".
									" AND KQBH=:KQBH AND RKDBH=:RKDBH";
						
							$zkshpData["QYBH"]=$_SESSION ['auth']->qybh; 		//区域编号
							$zkshpData ['CKBH'] = $_POST ["CKBH_H"]; 		//仓库编号
							$zkshpData ['KQBH'] = $_POST ["KQBH_H"]; 		//库区编号
							$zkshpData ['SHPBH'] = $grid [$this->idx_SHPBH];		//商品编号
							$zkshpData ['KWBH'] = $grid [$this->idx_KWBH]; 		//库位编号
							$zkshpData ['PIHAO'] = $grid [$this->idx_PIHAO]; 		//批号
							$zkshpData ['BZHDWBH'] = $grid [$this->idx_BZHDWBH];		//包装单位
							$zkshpData ['RKDBH'] = $shpzkxx["0"]["RKDBH"]; 		//入库单号
							$rkdbh=$shpzkxx["0"]["RKDBH"]; 
							$zkshpData ['SHCHRQ'] = $grid [$this->idx_SHCHRQ]; 		//生产日期
							$zkshpData ['SHULIANG'] = $pyshl; 	//盘溢数量
							//$zkshpData ['BGRQ'] = new Zend_Db_Expr ("SYSDATE");	//变更日期
							//$zkshpData ['BGZH'] = $_SESSION ['auth']->userId; 	//变更者
							
							$this->_db->query($updatezkshpSql,$zkshpData);	
					
				}
				else{
						$kcData['QYBH'] =$_SESSION ['auth']->qybh; 		//区域编号
						$kcData['CKBH'] = $_POST ["CKBH_H"]; 		//仓库编号
						$kcData['KQBH'] = $_POST ["KQBH_H"]; 		//库区编号
						$kcData['KWBH'] = $grid [$this->idx_KWBH]; 		//库位编号
						$kcData['SHPBH'] = $grid [$this->idx_SHPBH];		//商品编号						
						$kcData['PIHAO'] = $grid [$this->idx_PIHAO]; 		//批号
						$kcData['RKDBH'] = "99999999999999"; 		//入库单号
						$rkdbh="99999999999999";
						$kcData['ZKZHT'] ="0"; 		//在库状态
						$kcData['BZHDWBH'] = $grid [$this->idx_BZHDWBH];		//包装单位						
						$kcData['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" .$grid [$this->idx_SHCHRQ]. "','YYYY-MM-DD')" ); 		//生产日期
						$kcData['ZZHCHKRQ'] = new Zend_Db_Expr ( "TO_DATE('9999-12-31','YYYY-MM-DD')" ); 		//最终出库日期
						$kcData['SHULIANG'] = $pyshl; 	//盘溢数量
						$kcData['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" .$grid [$this->idx_BZHQZH]. "','YYYY-MM-DD')" ); 		//保质期日期						
						
						$this->_db->insert ( "H01DB012404", $kcData );					
				}
				
				//3.更新tbl:商品移动履历
				$selectShpydXuhaoSql="Select DECODE(max(XUHAO),null,1,max(XUHAO)+1) AS XUHAO from H01DB012405".
													" Where QYBH=:QYBH AND YDDH=:YDDH";
							
				$shpydData["QYBH"]=$_SESSION ['auth']->qybh; 		//区域编号			
				$shpydData['YDDH'] = $djbh; 		//移动单号
				
				$MaxXuhao=$this->_db->fetchAll($selectShpydXuhaoSql, $shpydData);
								
				$shpydData['XUHAO'] =$MaxXuhao['0']["XUHAO"];		//序号				
				$shpydData['CKBH'] = $_POST ["CKBH_H"]; 		//仓库编号
				$shpydData['KQBH'] = $_POST ["KQBH_H"]; 		//库区编号
				$shpydData['SHPBH'] = $grid [$this->idx_SHPBH];		//商品编号
				$shpydData['KWBH'] = $grid [$this->idx_KWBH]; 		//库位编号
				$shpydData['PIHAO'] = $grid [$this->idx_PIHAO]; 		//批号
				$shpydData['RKDBH'] = $rkdbh; 		//入库单号
				$shpydData['BZHDWBH'] = $grid [$this->idx_BZHDWBH];		//包装单位
					
				if ($grid [$this->idx_SHCHRQ]!= ""){
					//生产日期
					$shpydData['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" .$grid [$this->idx_SHCHRQ]. "','YYYY-MM-DD')" );
				}					
				$shpydData['CHLSHJ'] = new Zend_Db_Expr ("SYSDATE");	//处理时间							
				if ($pyshl > 0){
					$shpydData['ZHYZHL'] = "51"; 	//转移种类
				}else if($pyshl < 0){
					$shpydData['ZHYZHL'] = "52"; 	//转移种类	
				} 	
				if ($grid [$this->idx_BZHQZH] != ""){
					//保质期至
					$shpydData['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM-DD')" );
				}
				if($shpzkxx["0"]["ZKZHT"]!=""){
					$shpydData['ZKZHT'] = $shpzkxx["0"]["ZKZHT"]; 	//在库状态	
				}else{
					$shpydData['ZKZHT'] ="0";
				}
				
				$shpydData['SHULIANG'] = $pyshl; 	//盘溢数量
				$shpydData['BGRQ'] = new Zend_Db_Expr ("SYSDATE");	//变更日期
				$shpydData['BGZH'] = $_SESSION ['auth']->userId; 	//变更者				$shpydData ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
				$shpydData ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			
				$this->_db->insert ( "H01DB012405", $shpydData );	
						
			}
			else if($pyshl > 0){
				
				//1.抽取并且锁定的商品在库信息
				$selectLockSql="SELECT SHULIANG,RKDBH,".
						"TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,".
						"TO_CHAR(BZHQZH,'YYYY-MM-DD') AS BZHQZH,ZKZHT".
						" FROM H01DB012404".
						" WHERE QYBH=:QYBH AND CKBH=:CKBH AND KQBH=:KQBH AND KWBH=:KWBH AND SHPBH=:SHPBH AND PIHAO=:PIHAO".
						" AND BZHDWBH=:BZHDWBH AND SHCHRQ=TO_DATE(:SHCHRQ,'YYYY-MM-DD')".
						" AND ZKZHT <> '2'". 
						" AND SHULIANG > '0'".
						" ORDER BY RKDBH,ZKZHT DESC".
						" FOR UPDATE";
				$zkData["QYBH"]=$_SESSION ['auth']->qybh; 		//区域编号
				$zkData['CKBH'] = $_POST ["CKBH_H"]; 			//仓库编号
				$zkData['KQBH'] = $_POST ["KQBH_H"]; 			//库区编号
				$zkData['SHPBH'] = $grid [$this->idx_SHPBH];	//商品编号
				$zkData['KWBH'] = $grid [$this->idx_KWBH]; 		//库位编号
				$zkData['PIHAO'] = $grid [$this->idx_PIHAO]; 	//批号
				$zkData['BZHDWBH'] = $grid [$this->idx_BZHDWBH];		//包装单位
				$zkData['SHCHRQ'] = $grid [$this->idx_SHCHRQ]; 	//生产日期				
				$shpzkxx=$this->_db->fetchAll($selectLockSql, $zkData);
											
				$updatezkshpSql="UPDATE H01DB012404 SET ".
									"SHULIANG=0,ZZHCHKRQ=SYSDATE".
									" WHERE QYBH=:QYBH AND CKBH=:CKBH AND SHPBH=:SHPBH AND PIHAO=:PIHAO".
									" AND BZHDWBH=:BZHDWBH AND KWBH=:KWBH".
									" AND SHCHRQ=TO_DATE(:SHCHRQ,'YYYY-MM-DD') AND ZKZHT =:ZKZHT".
									" AND KQBH=:KQBH AND RKDBH=:RKDBH";
						
				$zkshpData["QYBH"]=$_SESSION ['auth']->qybh; 		//区域编号
				$zkshpData ['CKBH'] = $_POST ["CKBH_H"]; 		//仓库编号
				$zkshpData ['KQBH'] = $_POST ["KQBH_H"]; 		//库区编号
				$zkshpData ['SHPBH'] = $grid [$this->idx_SHPBH];		//商品编号
				$zkshpData ['KWBH'] = $grid [$this->idx_KWBH]; 		//库位编号
				$zkshpData ['PIHAO'] = $grid [$this->idx_PIHAO]; 		//批号
				$zkshpData ['BZHDWBH'] = $grid [$this->idx_BZHDWBH];		//包装单位
				$zkshpData ['SHCHRQ'] = $grid [$this->idx_SHCHRQ]; 		//生产日期
				//$zkshpData ['BGRQ'] = new Zend_Db_Expr ("SYSDATE");	//变更日期
				//$zkshpData ['BGZH'] = $_SESSION ['auth']->userId; 	//变更者
														
				$shpydData["QYBH"]=$_SESSION ['auth']->qybh; 		//区域编号
				$shpydData['CKBH'] = $_POST ["CKBH_H"]; 		//仓库编号
				$shpydData['KQBH'] = $_POST ["KQBH_H"]; 		//库区编号
				$shpydData['SHPBH'] = $grid [$this->idx_SHPBH];		//商品编号
				$shpydData['KWBH'] = $grid [$this->idx_KWBH]; 		//库位编号
				$shpydData['PIHAO'] = $grid [$this->idx_PIHAO]; 		//批号
												
				$shpydData['BZHDWBH'] = $grid [$this->idx_BZHDWBH];		//包装单位
				$shpydData ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
				$shpydData ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
				
				if ($grid [$this->idx_SHCHRQ]!= ""){
						//生产日期
						$shpydData['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ]. "','YYYY-MM-DD')" );
				}
						
				$shpydData['CHLSHJ'] = new Zend_Db_Expr ("SYSDATE");	//处理时间							
				$shpydData['BGRQ'] = new Zend_Db_Expr ("SYSDATE");	//变更日期
				$shpydData['BGZH'] = $_SESSION ['auth']->userId; 	//变更者				
				//赔偿机能增加--苏迅--2011/08/05--赔偿时移动单号保存新生成的赔偿销售单号,不需赔偿保存新生成的盘点单据号
				$shpydData['YDDH'] = ($_POST["SHFPCH"] != null) ? $xshdbhforpay : $djbh;
				
									
				for ($i=0;$i<count($shpzkxx);$i++){
					$zkshpData ['RKDBH'] = $shpzkxx[$i]["RKDBH"]; 		//入库单号
					$zkshpData ['ZKZHT'] = $shpzkxx[$i]["ZKZHT"]; 	//在库状态
					if($shpzkxx[$i]["ZKZHT"]!=""){
						$shpydData['ZKZHT'] = $shpzkxx[$i]["ZKZHT"]; 	//在库状态	
					}else{
						$shpydData['ZKZHT'] ="0";
					}
				
					$shpydData['RKDBH'] = $shpzkxx[$i]["RKDBH"]; 		//入库单号
					if ($shpzkxx[$i]["BZHQZH"] != ""){
						//保质期至
						$shpydData['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $shpzkxx[$i]["BZHQZH"] . "','YYYY-MM-DD')" );
					}
					$shpydData['XUHAO'] = $i+1;		//序号
						
					if ($pyshl > $shpzkxx[$i]["SHULIANG"]){
						$this->_db->query($updatezkshpSql,$zkshpData);
							
						$pyshl=$pyshl-$shpzkxx[$i]["SHULIANG"];
						
						$shpydData['SHULIANG'] = (-1)*$shpzkxx[$i]["SHULIANG"]; 	//移动数量
						if ($shpydData['SHULIANG'] > 0){
								$shpydData['ZHYZHL'] = "51"; 	//转移种类
							}else if($shpydData['SHULIANG'] < 0){
								//赔偿机能增加--苏迅--2011/08/05--赔偿时转移种类"21"出库，不需赔偿时"52"盘点减少
								$shpydData['ZHYZHL'] = ($_POST["SHFPCH"] != null) ? "21" : "52"; 	//转移种类	
						} 	
						$this->_db->insert ( "H01DB012405", $shpydData );	
						
					}else if ($pyshl == $shpzkxx[$i]["SHULIANG"]){
						$this->_db->query($updatezkshpSql,$zkshpData);
						$shpydData['SHULIANG'] = (-1)*$shpzkxx[$i]["SHULIANG"]; 	//移动数量
						if ($shpydData['SHULIANG'] > 0){
								$shpydData['ZHYZHL'] = "51"; 	//转移种类
							}else if($shpydData['SHULIANG'] < 0){
								//赔偿机能增加--苏迅--2011/08/05--赔偿时转移种类"21"出库，不需赔偿时"52"盘点减少
								$shpydData['ZHYZHL'] = ($_POST["SHFPCH"] != null) ? "21" : "52"; 	//转移种类	
						} 	
						$this->_db->insert ( "H01DB012405", $shpydData );		
						$pyshl=0;
						break;
					}else {
						
						$updatezkshpSql="UPDATE H01DB012404 SET ".
									"SHULIANG=:SHULIANG".
									" WHERE QYBH=:QYBH AND CKBH=:CKBH AND SHPBH=:SHPBH AND PIHAO=:PIHAO".
									" AND BZHDWBH=:BZHDWBH AND KWBH=:KWBH".
									" AND SHCHRQ=TO_DATE(:SHCHRQ,'YYYY-MM-DD') AND ZKZHT =:ZKZHT".
									" AND KQBH=:KQBH AND RKDBH=:RKDBH";
						
						$shyshl=$shpzkxx[$i]["SHULIANG"]-$pyshl;//剩余数量
						$zkshpData["SHULIANG"]=$shyshl;
						$this->_db->query($updatezkshpSql,$zkshpData);
						$shpydData['SHULIANG'] = (-1)*$pyshl; 	//移动数量
						if ($shpydData['SHULIANG'] > 0){
								$shpydData['ZHYZHL'] = "51"; 	//转移种类
							}else if($shpydData['SHULIANG'] < 0){
								//赔偿机能增加--苏迅--2011/08/05--赔偿时转移种类"21"出库，不需赔偿时"52"盘点减少
								$shpydData['ZHYZHL'] = ($_POST["SHFPCH"] != null) ? "21" : "52"; 	//转移种类	
						} 	
						$this->_db->insert ( "H01DB012405", $shpydData );	
						$pyshl=0;
						break;
					}
				}
				//3.当循环Loop2正常结束，变量:盘损数量仍然非零（变量:盘损数量 > 0)时，
				if($pyshl > 0){
					$result["status"]=3;
					$result["shpbh"]=$grid [$this->idx_SHPBH];
					return $result;
				}
			}												

		}
			
		$result["status"]=0;
		$result["shpbh"]=$grid [$this->idx_SHPBH];
		return $result;
	}
}
