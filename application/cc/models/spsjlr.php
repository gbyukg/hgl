<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   实盘数据录入(spsjlr)
 * 作成者：李洪波
 * 作成日：2011/01/17
 * 更新履历：
 *********************************/
class cc_models_spsjlr extends Common_Model_Base {

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
		private $idx_SJSHPSHL=21;	// 实际实盘数量
		private $idx_BZHQZH=23;		// 保质期至
		
	/*
	 * 列表数据取得(xml格式)
	 */
	function getListData($filter) {
		
		//检索SQL
			$sql = " SELECT DJBH,CKMCH,KQMCH,".
		       	" case when PDZHT='1' then '开始盘点'".
				" when PDZHT='2' then '盘点结束' end as PDZHT,".
				" case when JZHZHT='0' then '初期值'".
				" when JZHZHT='1' then '实盘已录入'".
				" when JZHZHT='2' then '已记账' end as JZHZHT,".
				" TO_CHAR(PDKSHSHJ,'YYYY-MM-DD') AS PDKSHSHJ,".
				" TO_CHAR(PDJSHSHJ,'YYYY-MM-DD') AS PDJSHSHJ,".
				" case when DJBZH='0' then '不冻结'".
				" when DJBZH='1' then '冻结' end as DJBZHMCH,".
				" DJBZH,CKBH,KQBH".
				" FROM H01VIEW012417 ".
		        " WHERE QYBH =:QYBH AND PDZHT <> '2' AND PDLX = '1'";
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_SPDJHXZ",$filter['filterParams'],$bind);
		//排序
		$sql .=" ORDER BY PDKSHSHJ DESC";
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
	     
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"],$bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"] ,$bind);
		
		return Common_Tool::createXml($recs,true,$totalCount,$filter["posStart"]);
	}

	/*
	 * 自动完成数据取得
	 */
	public function getAutocompleteData(){
		//检索SQL
		$sql = " SELECT DJBH,CKMCH,KQMCH,".
		       "  case when PDZHT='1' then '开始盘点'".
				" when PDZHT='2' then '盘点结束' end as PDZHT,".
				" case when A.JZHZHT='0' then '初期值'".
				" when JZHZHT='1' then '实盘已录入'".
				" when JZHZHT='2' then '已记账' end as JZHZHT,".
				" TO_CHAR(PDKSHSHJ,'YYYY-MM-DD') AS PDKSHSHJ,".
				" TO_CHAR(PDJSHSHJ,'YYYY-MM-DD') AS PDJSHSHJ,".
				" case when DJBZH='0' then '不冻结'".
				" when DJBZH='1' then '冻结' end as DJBZHMCH,".
				" DJBZH,CKBH,KQBH".
				" FROM H01VIEW012417 ".
		        " WHERE QYBH =:QYBH AND PDZHT <> '2' AND PDLX = '1'".
			    " ORDER BY PDKSHSHJ DESC";
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		return $this->_db->fetchAll($sql,$bind);
	}
	
	/**
	 * 获取单据信息
	 */
	function getDanjuxx($djbh){
			$sql = " SELECT A.DJBH,B.CKMCH,C.KQMCH,".
		      	"A.DJBZH,A.CKBH,A.KQBH,A.BGZH,to_char(A.BGRQ,'yyyy-mm-dd hh24:mi:ss') AS BGRQ,A.JZHZHT,A.ZHMJEHJ,A.SHPJEHJ,A.SYJEHJ".
				" FROM H01DB012417 A".
				" LEFT JOIN H01DB012401 B ON A.CKBH=B.CKBH". 
				" LEFT JOIN H01DB012402 C ON A.KQBH=C.KQBH".
		        " WHERE A.QYBH =:QYBH AND A.DJBH =:DJBH AND A.PDLX = '1'";
			
		$bind = array( 'QYBH' => $_SESSION ['auth']->qybh, 'DJBH' => $djbh );
		$Djxx = $this->_db->fetchRow( $sql, $bind );
		return $Djxx;     
	}
	
	/**
	 * 得到商品明细数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter) {
		$tablefields = array ("SHPBH", "KWMCH", "SHPMCH", "GUIGE", "NEIRONG", 
								"PIHAO", "SHCHRQ", "BZHSHL", "LSSHL", "SHPSHL", 
								"SHPJE","SHULIANG","JINE","CHBDJ", "PSSHL", "PSJE",
								"LSHJ", "CHANDI","KWBH","BZHDWBH" );
		$sql = "SELECT SHPBH,KWMCH,SHPMCH,GUIGE,NEIRONG,".
			   "PIHAO,SHCHRQ,BZHSHL,LSSHL,SHPSHL,SHPJE,SHULIANG,JINE,".
			   "CHBDJ,PSSHL,PSJE,LSHJ,CHANDI,KWBH,BZHDWBH,SJSHPSHL,JLGG,BZHQZH".
			   " FROM HO1UV012402".
			   " WHERE QYBH = :QYBH AND DJBH =:DJBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DJBH'] = $filter ['searchParams']["DJBH"];
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_SPSJLR",$filter['filterParams'],$bind);
		
		if($filter ["orderby"]==""){
				$sql .= " ORDER BY SHPBH";
		}
		else
		{
			$sql .= " ORDER BY " . $tablefields [$filter ["orderby"]] . " " . $filter ["direction"];
		}
		$recs=$this->_db->fetchAll($sql, $bind);
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs);
	}
	
	/*
	 * 实盘信息保存
	 */
	public function saveShpshjlr($djbh) {
		
		//赔偿机能增加--苏迅--2011/08/09--赔偿销售单及明细生成
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
			foreach ( $_POST ["#grid_spsjlr"] as $grid ) {			
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
					//$xshdmx ['HSHJ'] = $grid [$this->idx_HSHJ]; 			//含税价--QA
					//$xshdmx ['JINE'] = $grid [$this->idx_JINE]; 			//金额--QA
					//$xshdmx ['SHUIE'] = $grid [$this->idx_SHUIE]; 		//税额--QA
					$xshdmx ['DANJIA'] = $grid [$this->idx_CHBDJ]; 			//单价--取画面成本单价？？QA
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
		//赔偿机能增加--苏迅--2011/08/09--赔偿销售单及明细生成--end
		
		//更改SQL(实盘明细表)
		$updateSql="UPDATE H01DB012418 SET ".
				"BZHSHL=:BZHSHL,LSSHL=:LSSHL,".
				"SHPSHL=:SHPSHL,SHPJE=:SHPJE,PSSHL=:PSSHL,PSJE=:PSJE,BGRQ=SYSDATE,BGZH=:BGZH".
				" WHERE QYBH=:QYBH AND DJBH=:DJBH AND SHPBH=:SHPBH AND PIHAO=:PIHAO".
				" AND BZHDWBH=:BZHDWBH AND KWBH=:KWBH AND SHCHRQ=TO_DATE(:SHCHRQ,'YYYY-MM-DD')";
        //循环所有明细行，保存实盘明细信息
		foreach ( $_POST ["#grid_spsjlr"] as $grid ) {
			$shpydData=array();
			$rkdbh="";
			//实盘明细信息
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['DJBH'] = $djbh; //单据编号
			$data ['SHPBH'] = $grid [$this->idx_SHPBH];//商品编号
			$data ['SHCHRQ'] = $grid [$this->idx_SHCHRQ]; //生产日期
			$data ['BZHSHL'] = $grid [$this->idx_BZHSHL];//包装数量
			$data ['LSSHL'] = $grid [$this->idx_LSSHL];//零散数量
			$data ['SHPSHL'] = $grid [$this->idx_SHPSHL];//实盘数量
			$data ['SHPJE'] = $grid [$this->idx_SHPJE];//实盘金额
			$data ['PSSHL'] = $grid [$this->idx_PSSHL];//盘损数量
			$data ['PSJE'] = $grid [$this->idx_PSJE]; //盘损金额
			$data ['PIHAO'] = $grid [$this->idx_PIHAO];//批号
			$data ['BZHDWBH'] = $grid [$this->idx_BZHDWBH];//包装单位编号
			$data ['KWBH'] = $grid [$this->idx_KWBH]; //库位编号
			//$data ['BGRQ'] = new Zend_Db_Expr ("SYSDATE");//变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			if ($grid [$this->idx_SHPSHL]!=$grid [$this->idx_SJSHPSHL]){
				$this->_db->query($updateSql,$data);
			}
			$pyshl=$grid [$this->idx_SJSHPSHL]-$grid [$this->idx_SHPSHL];
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
				$zkData["QYBH"]=$_SESSION ['auth']->qybh; //区域编号
				$zkData['CKBH'] = $_POST ["CKBH_H"]; //仓库编号
				$zkData['KQBH'] = $_POST ["KQBH_H"]; //库区编号
				$zkData['SHPBH'] = $grid [$this->idx_SHPBH];//商品编号
				$zkData['KWBH'] = $grid [$this->idx_KWBH]; //库位编号
				$zkData['PIHAO'] = $grid [$this->idx_PIHAO]; //批号
				$zkData['BZHDWBH'] = $grid [$this->idx_BZHDWBH];//包装单位
				$zkData['SHCHRQ'] = $grid [$this->idx_SHCHRQ]; //生产日期
				$shpzkxx=$this->_db->fetchAll($selectLockSql, $zkData);
				if(count($shpzkxx)!=0){
					$updatezkshpSql="UPDATE H01DB012404 SET ".
									"SHULIANG=SHULIANG +:SHULIANG,ZZHCHKRQ=TO_DATE('9999-12-31 23:59:59','YYYY-MM-DD HH24:MI:SS')".
									" WHERE QYBH=:QYBH AND CKBH=:CKBH AND SHPBH=:SHPBH AND PIHAO=:PIHAO".
									" AND BZHDWBH=:BZHDWBH AND KWBH=:KWBH".
									" AND SHCHRQ=TO_DATE(:SHCHRQ,'YYYY-MM-DD') AND ZKZHT = '0'".
									" AND KQBH=:KQBH AND RKDBH=:RKDBH";
							$zkshpData["QYBH"]=$_SESSION ['auth']->qybh; //区域编号
							$zkshpData ['CKBH'] = $_POST ["CKBH_H"]; //仓库编号
							$zkshpData ['KQBH'] = $_POST ["KQBH_H"]; //库区编号
							$zkshpData ['SHPBH'] = $grid [$this->idx_SHPBH];//商品编号
							$zkshpData ['KWBH'] = $grid [$this->idx_KWBH]; //库位编号
							$zkshpData ['PIHAO'] = $grid [$this->idx_PIHAO]; //批号
							$zkshpData ['BZHDWBH'] = $grid [$this->idx_BZHDWBH];//包装单位
							$zkshpData ['RKDBH'] = $shpzkxx["0"]["RKDBH"]; //入库单号
							$rkdbh=$shpzkxx["0"]["RKDBH"]; 
							$zkshpData ['SHCHRQ'] = $grid [$this->idx_SHCHRQ]; //生产日期
							$zkshpData ['SHULIANG'] = $pyshl; //盘溢数量
							//$zkshpData ['BGRQ'] = new Zend_Db_Expr ("SYSDATE");//变更日期
							//$zkshpData ['BGZH'] = $_SESSION ['auth']->userId; //变更者
							$this->_db->query($updatezkshpSql,$zkshpData);
				}
				else{
						$kcData['QYBH'] =$_SESSION ['auth']->qybh; //区域编号
						$kcData['CKBH'] = $_POST ["CKBH_H"]; //仓库编号
						$kcData['KQBH'] = $_POST ["KQBH_H"]; //库区编号
						$kcData['KWBH'] = $grid [$this->idx_KWBH]; //库位编号
						$kcData['SHPBH'] = $grid [$this->idx_SHPBH];//商品编号
						$kcData['PIHAO'] = $grid [$this->idx_PIHAO]; //批号
						$kcData['RKDBH'] = "99999999999999"; //入库单号
						$rkdbh="99999999999999";
						$kcData['ZKZHT'] ="0"; //在库状态
						$kcData['BZHDWBH'] = $grid [$this->idx_BZHDWBH];   //包装单位
						$kcData['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" .$grid [$this->idx_SHCHRQ]. "','YYYY-MM-DD')" ); //生产日期
						$kcData['ZZHCHKRQ'] = new Zend_Db_Expr ( "TO_DATE('9999-12-31 23:59:59','YYYY-MM-DD HH24:MI:SS')" ); //最终出库日期
						$kcData['SHULIANG'] = $pyshl; 	//盘溢数量
						$kcData['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" .$shpzkxx[0]["BZHQZH"]. "','YYYY-MM-DD')" ); //保质期日期
						$this->_db->insert ( "H01DB012404", $kcData );
				}
				//3.更新tbl:商品移动履历
				$selectShpydXuhaoSql="Select DECODE(max(XUHAO),null,1,max(XUHAO)+1) AS XUHAO from H01DB012405".
													" Where QYBH=:QYBH AND YDDH=:YDDH";
				$shpydData["QYBH"]=$_SESSION ['auth']->qybh; //区域编号
				$shpydData['YDDH'] = $djbh; //移动单号
				$MaxXuhao=$this->_db->fetchAll($selectShpydXuhaoSql, $shpydData);
				$shpydData['XUHAO'] =$MaxXuhao['0']["XUHAO"];//序号
				$shpydData['CKBH'] = $_POST ["CKBH_H"]; //仓库编号
				$shpydData['KQBH'] = $_POST ["KQBH_H"]; //库区编号
				$shpydData['SHPBH'] = $grid [$this->idx_SHPBH];//商品编号
				$shpydData['KWBH'] = $grid [$this->idx_KWBH]; //库位编号
				$shpydData['PIHAO'] = $grid [$this->idx_PIHAO]; //批号
				$shpydData['RKDBH'] = $rkdbh; //入库单号
				$shpydData['BZHDWBH'] = $grid [$this->idx_BZHDWBH];//包装单位
				if ($grid [$this->idx_SHCHRQ]!= ""){
					//生产日期
					$shpydData['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" .$grid [$this->idx_SHCHRQ]. "','YYYY-MM-DD')" );
				}
				$shpydData['CHLSHJ'] = new Zend_Db_Expr ("SYSDATE");//处理时间
				if ($pyshl > 0){
					$shpydData['ZHYZHL'] = "51"; //转移种类
				}else if($pyshl < 0){
					$shpydData['ZHYZHL'] = "52"; //转移种类
				} 
				if ($grid [$this->idx_BZHQZH] != ""){
					//保质期至
					$shpydData['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM-DD')" );
				}else{
					//保质期至
					$shpydData['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $shpzkxx[0]["BZHQZH"] . "','YYYY-MM-DD')" );
				}
				if($shpzkxx["0"]["ZKZHT"]!=""){
					$shpydData['ZKZHT'] = $shpzkxx["0"]["ZKZHT"]; //在库状态
				}else{
					$shpydData['ZKZHT'] ="0";
				}
				$shpydData['SHULIANG'] = $pyshl; //盘溢数量
				$shpydData['BGRQ'] = new Zend_Db_Expr ("SYSDATE");//变更日期
				$shpydData['BGZH'] = $_SESSION ['auth']->userId; //变更者
				$shpydData ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
				$shpydData ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
				$this->_db->insert ( "H01DB012405", $shpydData );
			}
			elseif ($pyshl > 0) {
				//1.抽取并且锁定的商品在库信息
				$selectLockSql="SELECT SHULIANG,RKDBH,SHCHRQ,TO_CHAR(BZHQZH,'YYYY-MM-DD') AS BZHQZH,ZKZHT".
						" FROM H01DB012404".
						" WHERE QYBH=:QYBH AND CKBH=:CKBH AND KQBH=:KQBH AND KWBH=:KWBH AND SHPBH=:SHPBH AND PIHAO=:PIHAO".
						" AND BZHDWBH=:BZHDWBH AND SHCHRQ=TO_DATE(:SHCHRQ,'YYYY-MM-DD')".
						" AND ZKZHT <> '2'". 
						" AND SHULIANG > '0'".
						" ORDER BY RKDBH,ZKZHT DESC".
						" FOR UPDATE";
				$zkData["QYBH"]=$_SESSION ['auth']->qybh; //区域编号
				$zkData['CKBH'] = $_POST ["CKBH_H"]; //仓库编号
				$zkData['KQBH'] = $_POST ["KQBH_H"]; //库区编号
				$zkData['SHPBH'] = $grid [$this->idx_SHPBH];//商品编号
				$zkData['KWBH'] = $grid [$this->idx_KWBH]; //库位编号
				$zkData['PIHAO'] = $grid [$this->idx_PIHAO]; //批号
				$zkData['BZHDWBH'] = $grid [$this->idx_BZHDWBH];//包装单位
				$zkData['SHCHRQ'] = $grid [$this->idx_SHCHRQ]; //生产日期
				$shpzkxx=$this->_db->fetchAll($selectLockSql, $zkData);
				$updatezkshpSql="UPDATE H01DB012404 SET ".
									"SHULIANG=0,ZZHCHKRQ=SYSDATE".
									" WHERE QYBH=:QYBH AND CKBH=:CKBH AND SHPBH=:SHPBH AND PIHAO=:PIHAO".
									" AND BZHDWBH=:BZHDWBH AND KWBH=:KWBH".
									" AND SHCHRQ=TO_DATE(:SHCHRQ,'YYYY-MM-DD') AND ZKZHT =:ZKZHT".
									" AND KQBH=:KQBH AND RKDBH=:RKDBH";
				$zkshpData["QYBH"]=$_SESSION ['auth']->qybh; //区域编号
				$zkshpData ['CKBH'] = $_POST ["CKBH_H"]; //仓库编号
				$zkshpData ['KQBH'] = $_POST ["KQBH_H"]; //库区编号
				$zkshpData ['SHPBH'] = $grid [$this->idx_SHPBH];//商品编号
				$zkshpData ['KWBH'] = $grid [$this->idx_KWBH]; //库位编号
				$zkshpData ['PIHAO'] = $grid [$this->idx_PIHAO]; //批号
				$zkshpData ['BZHDWBH'] = $grid [$this->idx_BZHDWBH];//包装单位
				$zkshpData ['SHCHRQ'] = $grid [$this->idx_SHCHRQ]; //生产日期
				//$zkshpData ['BGRQ'] = new Zend_Db_Expr ("SYSDATE");//变更日期
				//$zkshpData ['BGZH'] = $_SESSION ['auth']->userId; //变更者
				$selectShpydXuhaoSql="Select DECODE(max(XUHAO),null,1,max(XUHAO)+1) AS XUHAO from H01DB012405".
													" Where QYBH=:QYBH AND YDDH=:YDDH";
				$shpydData["QYBH"]=$_SESSION ['auth']->qybh; //区域编号
				$MaxXuhao=$this->_db->fetchAll($selectShpydXuhaoSql, $shpydData);
				$shpydData['XUHAO'] =$MaxXuhao['0']["XUHAO"];//序号
				$shpydData['CKBH'] = $_POST ["CKBH_H"]; //仓库编号
				$shpydData['KQBH'] = $_POST ["KQBH_H"]; //库区编号
				$shpydData['SHPBH'] = $grid [$this->idx_SHPBH];//商品编号
				$shpydData['KWBH'] = $grid [$this->idx_KWBH]; //库位编号
				$shpydData['PIHAO'] = $grid [$this->idx_PIHAO]; //批号
				$shpydData ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
				$shpydData ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
				
				//赔偿机能增加--苏迅--2011/08/05--赔偿时移动单号保存新生成的赔偿销售单号,不需赔偿保存新生成的盘点单据号
				$shpydData['YDDH'] = ($_POST["SHFPCH"] != null) ? $xshdbhforpay : $djbh;
				
				for ($i=0;$i<count($shpzkxx);$i++){
					$zkshpData ['RKDBH'] = $shpzkxx[$i]["RKDBH"]; //入库单号
					$shpydData['RKDBH'] = $shpzkxx[$i]["RKDBH"]; //入库单号
					$zkshpData ['ZKZHT'] = $shpzkxx[$i]["ZKZHT"]; //在库状态

					if($shpzkxx[$i]["ZKZHT"]!=""){
						$shpydData['ZKZHT'] = $shpzkxx[$i]["ZKZHT"]; //在库状态
					}else{
						$shpydData['ZKZHT'] ="0";
					}
					
					if ($shpzkxx[$i]["BZHQZH"] != ""){
						//保质期至
						$shpydData['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $shpzkxx[$i]["BZHQZH"] . "','YYYY-MM-DD')" );
					}else{
						//保质期至
						$shpydData['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM-DD')" );
					}
					$shpydData['BZHDWBH'] = $grid [$this->idx_BZHDWBH];//包装单位
					if ($grid [$this->idx_SHCHRQ]!= ""){
						//生产日期
						$shpydData['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ]. "','YYYY-MM-DD')" );
					}
					$shpydData['CHLSHJ'] = new Zend_Db_Expr ("SYSDATE");//处理时间
					$shpydData['BGRQ'] = new Zend_Db_Expr ("SYSDATE");//变更日期
					$shpydData['BGZH'] = $_SESSION ['auth']->userId; //变更者
					if ($pyshl > $shpzkxx[$i]["SHULIANG"]){
						$this->_db->query($updatezkshpSql,$zkshpData);
						$pyshl=$pyshl-$shpzkxx[$i]["SHULIANG"];
						$shpydData['SHULIANG'] = (-1)*$shpzkxx[$i]["SHULIANG"]; //移动数量
						if ($shpydData['SHULIANG'] > 0){
								$shpydData['ZHYZHL'] = "51"; //转移种类
							}else if($shpydData['SHULIANG'] < 0){
								//赔偿机能增加--苏迅--2011/08/05--赔偿时转移种类"21"出库，不需赔偿时"52"盘点减少
								$shpydData['ZHYZHL'] = ($_POST["SHFPCH"] != null) ? "21" : "52"; 	//转移种类
						} 
						$this->_db->insert ( "H01DB012405", $shpydData );
					}else if ($pyshl == $shpzkxx[$i]["SHULIANG"]){
						$this->_db->query($updatezkshpSql,$zkshpData);
						$shpydData['SHULIANG'] = (-1)*$shpzkxx[$i]["SHULIANG"]; //移动数量
						if ($shpydData['SHULIANG'] > 0){
								$shpydData['ZHYZHL'] = "51"; //转移种类
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
						$shpydData['SHULIANG'] = (-1)*$pyshl; //移动数量
						if ($shpydData['SHULIANG'] > 0){
								$shpydData['ZHYZHL'] = "51"; //转移种类
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
					$result["status"]=2;
					$result["shpbh"]=$grid [$this->idx_SHPBH];
					return $result;
				}
			}
		}
		//更改SQL(实盘信息表)
		$updateSqlKeyTab="UPDATE H01DB012417 SET ".
				"QYBH=:QYBH,DJBH=:DJBH,SHPYWY=:SHPYWY,".
				"SHPBM=:SHPBM,JZHZHT=1,ZHMJEHJ=:ZHMJEHJ,".
				"SHPJEHJ=:SHPJEHJ,SYJEHJ=:SYJEHJ,BGRQ=SYSDATE,BGZH=:BGZH,SHFPCH=:SHFPCH".	//是否赔偿增加
				" WHERE QYBH=:QYBH AND DJBH=:DJBH";
		$keyTab ['QYBH'] = $_SESSION ['auth']->qybh;            //区域编号
		$keyTab ['DJBH'] = $djbh;                               //单据编号
		$keyTab ['SHPYWY'] = $_POST ["YEWUYUAN_H"];             //实盘业务员
		$keyTab ['SHPBM'] = $_SESSION ['auth']->bmbh;                 //部门编号
		$keyTab ['ZHMJEHJ'] = $_POST ["ZHJEHJ_H"];              //账面金额合计
		$keyTab ['SHPJEHJ'] = $_POST ["SPJEHJ_H"];              //实盘金额合计
		$keyTab ['SYJEHJ'] = $_POST ["SYJEHJ_H"];               //损溢金额合计
		//$keyTab ['BGRQ'] = new Zend_Db_Expr ("SYSDATE");      //变更日期
		$keyTab ['BGZH'] = $_SESSION ['auth']->userId;          //变更者
		$keyTab ['SHFPCH'] = ($_POST["SHFPCH"] != null) ? "1" : "0";	//是否赔偿--1：员工赔偿；0：不赔偿
		$this->_db->query($updateSqlKeyTab,$keyTab);
		
		$result["status"]=0;
		$result["shpbh"]=$grid [$this->idx_SHPBH];
		return $result;
	}
	/**
	 * 查找对应盘点信息
	 *
	 * @param unknown_type $djbh 盘点编号
	 * @param unknown_type $filter 关联页面内容
	 * @param unknown_type $flg 判断上一页下一页和第一次打开详细画面flg
	 * @return bool
	 */
	function getPdwhOne($djbh,$filter, $flg){
			$fields = array ("", "DJBH", "PDZHT", "CKBH", "KQBH","PDKSHSHJ" ,"PDJSHSHJ");			
			$sql_list = "SELECT  CCSHPROWID,LEAD(CCSHPROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",DJBH) AS NEXTROWID,".
            " LAG(CCSHPROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] ." ,DJBH) AS PREVROWID,".
			" DJBH ".
			" FROM H01VIEW012417 " .
            " WHERE QYBH = :QYBH";
			$bind ['QYBH'] =$_SESSION ['auth']->qybh;
			if($filter['searchParams']["DJBH"] != "")
			{
				$sql_list .= " AND DJBH LIKE '%' || :DJBH || '%'";
				$bind['DJBH'] = $filter['searchParams']["DJBH"];
			}
			//查找条件 状态
			if ($filter ['searchParams']["PDZHT"] != "9" && $filter['searchParams']['PDZHT'] != "") {
				$sql_list .= " AND PDZHT = :PDZHT ";
				$bind ['PDZHT'] =$filter ['searchParams']["PDZHT"];
			}
			
			//盘点开始时间
			if ($filter ['searchParams']["PDKSHSHJ"] != "") {
				
				$sql_list .= " AND TO_CHAR(PDKSHSHJ,'YYYY-MM-DD') = :PDKSHSHJ ";
				$bind ['PDKSHSHJ'] =$filter ['searchParams']['PDKSHSHJ'];
			}
		    //盘点结束时间
			if ($filter ['searchParams']["PDJSHSHJ"] != "") {
				$sql_list .= " AND TO_CHAR(PDJSHSHJ,'YYYY-MM-DD') = :PDJSHSHJ ";
				$bind ['PDJSHSHJ'] =$filter ['searchParams']['PDJSHSHJ'];
			}
					
			$sql_single = "SELECT SHPBMMCH AS KSBMBH,JSBMMCH AS JSBMCMH ,SHPYWYXM AS KSYGBH,JSHYWYXM AS JSYEWUYUAN,CKMCH,KQMCH,KWMCH,QYBH,DJBH,PDLX,TO_CHAR(PDKSHSHJ,'YYYY-MM-DD HH24:MI:SS') AS PDKSHSHJ,TO_CHAR(PDJSHSHJ,'YYYY-MM-DD HH24:MI:SS') AS PDJSHSHJ,PDJHDH,CKBH,KQBH,KWBH,ZHMSHLTJ,DJBZH,SHPYWY,SHPBM,PDZHT,JZHZHT,HGL_DEC(SYJEHJ),	BEIZHU,BGRQ,BGZH ,TO_CHAR(BGRQ,'YYYY-MM-DD HH24:MI:SS') AS BGRQ,BGZH,TO_CHAR(PDJSHSHJ,'YYYY-MM-DD HH24:MI:SS') AS PDJSHSHJ,HGL_DEC(ZHMJEHJ) as ZHMJEHJ,HGL_DEC(SHPJEHJ) as SHPJEHJ " 
			      ." FROM H01VIEW012417 " ;

			//当前
			if ($flg == 'current') {
				$sql_single .= " WHERE QYBH = :QYBH AND DJBH = :DJBH";
				unset($bind['DJBHKEY']);
				unset($bind['PDZHT']);
				unset($bind['PDKSHSHJ']);
				unset($bind['PDJSHSHJ']);
				
			} else if ($flg == 'next') {//下一条
				
			    //自动生成精确查询用Sql
                $sql_list .= Common_Tool::createFilterSql("CC_PDWH",$filter['filterParams'],$bind);
				$sql_single .= "WHERE CCSHPROWID =  (SELECT NEXTROWID FROM  (SELECT NEXTROWID,DJBH FROM ( $sql_list ) WHERE DJBH = :DJBH))";		
			} else if ($flg == 'prev') {//前一条
				
			    //自动生成精确查询用Sql
                $sql_list .= Common_Tool::createFilterSql("CC_PDWH",$filter['filterParams'],$bind);
				$sql_single .= "WHERE CCSHPROWID =  (SELECT PREVROWID FROM  (SELECT PREVROWID,DJBH FROM ( $sql_list ) WHERE DJBH = :DJBH))";		
			}
			$bind ['DJBH'] =$djbh;
			$recs = $this->_db->fetchRow($sql_single,$bind);
			if($recs == false){
				return false;
			}else{
				return $recs;
			}
       }
}