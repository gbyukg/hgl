<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   在库商品一览(spzkyl)
 * 作成者：魏峰
 * 作成日：2011/01/12
 * 更新履历：
 *********************************/
class cc_models_zkspyl extends Common_Model_Base {
	
	/**
	 * 得到在库商品数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	
	/* --------------------           在库一览                       ----------------------*/
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ("", "SHPBH", "SHPMCH", "CKMCH||KQMCH||KWMCH", "PIHAO", "SHCHRQ", "BZHQZH","ZKZHT");

		//检索SQL
		$sql = "SELECT SHPBH,SHPMCH,CKMCH||KQMCH||KWMCH ,PIHAO,TO_CHAR(SHCHRQ,'YYYY-MM-DD') SHCHRQ,TO_CHAR(BZHQZH,'YYYY-MM-DD') BZHQZH,DECODE(ZKZHT,'0','可销','1','催销','冻结') ZKZHT,SUM(SHULIANG),BZHDWMCH,BZHDWBH,CKBH,KQBH,KWBH,ZKZHT ZKZHTBH,KWZHT,JLGG,SHFSHKW". 
		       " FROM H01VIEW012404 ". 
		       " WHERE QYBH = :QYBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件  商品 编号
		if ($filter ["searchParams"]['SHPBH']!= "") {
			$sql .= " AND( SHPBH LIKE '%' || :SHPBH || '%')";
			$bind ['SHPBH'] = $filter ["searchParams"]['SHPBH'];
		}
		
		//查找条件  仓库编号
		if ($filter ["searchParams"]['CKBH'] != "") {
			$sql .= " AND CKBH = :CKBH AND KQBH = :KQBH AND KWBH = :KWBH";
			$bind ['CKBH'] = $filter ["searchParams"]["CKBH"];
			$bind ['KQBH'] = $filter ["searchParams"] ["KQBH"];
			$bind ['KWBH'] = $filter ["searchParams"] ["KWBH"];
		}
 
		$sql .= " GROUP BY SHPBH,SHPMCH,CKMCH||KQMCH||KWMCH ,PIHAO,SHCHRQ,BZHQZH,ZKZHT,BZHDWMCH,BZHDWBH,CKBH,KQBH,KWBH,ZKZHT,KWZHT,JLGG,SHFSHKW".
		        " HAVING SUM(SHULIANG) > 0";
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_ZKSPYL",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 项目最后添加主键
		//$sql .=",YGBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true,$totalCount, $filter ["posStart"] );
	}
	
	public function getRKDInfo($filter) {
		
		//检索SQL
		$sql = "SELECT RKDBH || ';' ||  SHULIANG,RKDBH". 
		       " FROM H01DB012404". 
		       " WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQBH = :KQBH AND KWBH = :KWBH AND SHPBH = :SHPBH" . 
		       " AND PIHAO = :PIHAO AND ZKZHT = :ZKZHT AND BZHDWBH = :BZHDWBH AND TO_CHAR(SHCHRQ,'YYYY-MM-DD') = :SHCHRQ AND SHULIANG > 0"; 

		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CKBH'] = $filter ['ckbh'];
		$bind ['KQBH'] = $filter ['kqbh'];
		$bind ['KWBH'] = $filter ['kwbh'];
		$bind ['SHPBH'] = $filter ['shpbh'];
		$bind ['PIHAO'] = $filter ['pihao'];
		$bind ['ZKZHT'] = $filter ['zkzhtbh'];
		$bind ['BZHDWBH'] = $filter ['danweibh'];
		$bind ['SHCHRQ'] = $filter ['shchrq']; 
	    $result = $this->_db->fetchPairs ( $sql, $bind );
		return $result;
			
		}

	/* --------------------           状态变更                       ----------------------*/
	public function getBgqshul($qufen,$zhuangtai='0') {
		
		//检索SQL
		$sql = "SELECT SHULIANG". 
		       " FROM H01DB012404". 
		       " WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQBH = :KQBH AND KWBH = :KWBH AND SHPBH = :SHPBH" . 
		       " AND PIHAO = :PIHAO AND RKDBH = :RKDBH AND ZKZHT = :ZKZHT AND BZHDWBH = :BZHDWBH AND TO_CHAR(SHCHRQ,'YYYY-MM-DD') = :SHCHRQ ".
		       " FOR UPDATE";

		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CKBH'] = $_POST ['CKBH'];
		$bind ['KQBH'] = $_POST ['KQBH'];
		$bind ['KWBH'] = $_POST ['KWBH'];
		$bind ['SHPBH'] = $_POST ['SHPBH'];
		$bind ['PIHAO'] = $_POST ['PIHAO'];
		$bind ['RKDBH'] = substr($_POST ['RKDJH'],0,14);
		//变更前在库数量检索
		if ($qufen == '1'){
		   $bind ['ZKZHT'] = $_POST ['ZKZHTBH'];
		//变更后在库数量检索
		}else{
		   $bind ['ZKZHT'] = $zhuangtai;
		}		
		$bind ['BZHDWBH'] = $_POST ['DANWEIBH'];
		$bind ['SHCHRQ'] = $_POST ['SHCHRQ']; 
	    $result = $this->_db->fetchOne ( $sql, $bind );
		return $result;
			
		}
		
	/*
	 * 更新在库商品信息
	 */
	public function updateZaiku($bgqshul,$qufen,$danjvbh,$ztphqufen) {
		
			//更新在库信息
			$sql_zaiku = "UPDATE H01DB012404 ".
			             "SET SHULIANG = :SHULIANG " .
			             (($qufen == 0) ? ",ZZHCHKRQ = SYSDATE " : "").
			             " WHERE QYBH = :QYBH ".
			             " AND CKBH = :CKBH " .
			             " AND KQBH = :KQBH ".
			             " AND KWBH = :KWBH ".
			             " AND SHPBH = :SHPBH " .
			             " AND PIHAO = :PIHAO " .
			             " AND ZKZHT = :ZKZHT " .
			             " AND RKDBH = :RKDBH " .
			             " AND BZHDWBH = :BZHDWBH" .
		                 " AND TO_CHAR(SHCHRQ,'YYYY-MM-DD') = :SHCHRQ";
		
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['CKBH'] = $_POST ['CKBH']; 
			$bind ['KQBH'] = $_POST ['KQBH']; 
			$bind ['KWBH'] = $_POST ['KWBH']; 
			$bind ['SHPBH'] = $_POST ['SHPBH']; 
			$bind ['PIHAO'] = $_POST ['PIHAO']; 
			$bind ['BZHDWBH'] = $_POST ['DANWEIBH']; 
			$bind ['RKDBH'] = substr($_POST ['RKDJH'],0,14);
			$bind ['ZKZHT'] = $_POST ['ZKZHTBH'];
			$bind ['SHCHRQ'] = $_POST ['SHCHRQ']; 
			if($qufen == 0){
				$bind ['SHULIANG'] = 0;  
			}else{
				$bind ['SHULIANG'] = $bgqshul - $_POST ['SHUL'];   
			}	            
			$this->_db->query ( $sql_zaiku,$bind );	
		
			//更新DB:商品移动履历（H01DB012405)
			$lvli ["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
			$lvli ["CKBH"] = $_POST ['CKBH']; //仓库编号
			$lvli ["KQBH"] = $_POST ['KQBH'];; //库区编号
			$lvli ["KWBH"] = $_POST ['KWBH'];; //库位编号
			$lvli ["SHPBH"] = $_POST ['SHPBH'];; //商品编号
			$lvli ["PIHAO"] = $_POST ['PIHAO'];; //批号
			$lvli ["RKDBH"] = substr($_POST ['RKDJH'],0,14);
			$lvli ["YDDH"] = $danjvbh; //移动单号
			$lvli ["XUHAO"] = 1; //序号
			$lvli['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$_POST['SHCHRQ']."','YYYY-MM-DD')"); //生产日期
			$lvli['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$_POST['BZHQZH']."','YYYY-MM-DD')"); //保质期至
			$lvli['CHLSHJ'] = new Zend_Db_Expr('SYSDATE'); //处理时间
			$lvli ["SHULIANG"] = $_POST ['SHUL'] * - 1; //移动数量
			if($ztphqufen == 0){
				//状态变更
			    $lvli ["ZHYZHL"] = '72'; //转移种类 （状态变更）
			}else{
				//批号调整
			    $lvli ["ZHYZHL"] = '71'; //转移种类 （批号调整）
			}		
			$lvli ["BZHDWBH"] = $_POST ['DANWEIBH']; //包装单位编号
			$lvli ["ZKZHT"] = $_POST ['ZKZHTBH'];//在库状态
			$lvli['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
			$lvli['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$lvli ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$lvli ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			$this->_db->insert ( 'H01DB012405', $lvli );		
			
	}
	
	/*
	 * 更新DB:商品移动履历（H01DB012405)(状态变更)
	 */
	public function insertYidongll($danjvbh,$zhtai) {
		
		//更新DB:商品移动履历（H01DB012405)
		$lvli ["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
		$lvli ["CKBH"] = $_POST ['CKBH']; //仓库编号
		$lvli ["KQBH"] = $_POST ['KQBH'];; //库区编号
		$lvli ["KWBH"] = $_POST ['KWBH'];; //库位编号
		$lvli ["SHPBH"] = $_POST ['SHPBH'];; //商品编号
		$lvli ["PIHAO"] = $_POST ['PIHAO'];; //批号
		$lvli ["RKDBH"] = substr($_POST ['RKDJH'],0,14);
		$lvli ["YDDH"] = $danjvbh; //移动单号
		$lvli ["XUHAO"] = 2; //序号
		$lvli['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$_POST['SHCHRQ']."','YYYY-MM-DD')"); //生产日期
		$lvli['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$_POST['BZHQZH']."','YYYY-MM-DD')"); //保质期至
		$lvli['CHLSHJ'] = new Zend_Db_Expr('SYSDATE'); //处理时间
		$lvli ["SHULIANG"] = $_POST ['SHUL']; //移动数量
		$lvli ["ZHYZHL"] = '72'; //转移种类 [状态变更]
		$lvli ["BZHDWBH"] = $_POST ['DANWEIBH']; //包装单位编号
		$lvli ["ZKZHT"] = $zhtai;//在库状态
		$lvli['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
		$lvli['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$lvli ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$lvli ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$this->_db->insert ( 'H01DB012405', $lvli );				
		
	}
	
	/*
	 * 在库商品信息保存
	 */
	public function insertZaiku($zhuangtai) {
		
		$data ['QYBH'] = $_SESSION ['auth']->qybh;           //区域编号
		$data ['CKBH'] = $_POST ['CKBH'];                    //仓库编号
		$data ['KQBH'] = $_POST ['KQBH'];                    //库区编号
		$data ['KWBH'] = $_POST ['KWBH'];                    //库位编号
		$data ['SHPBH'] = $_POST ['SHPBH'];                  //商品编号
		$data ['PIHAO'] = $_POST ['PIHAO'];                  //批号
		$data ['RKDBH'] = substr($_POST ['RKDJH'],0,14);     //入库单编号
		$data ['ZKZHT'] = $zhuangtai;                        //在库状态
		$data ['BZHDWBH'] = $_POST ['DANWEIBH'];             //包装单位编号
		$data ['ZZHCHKRQ'] = new Zend_Db_Expr("TO_DATE('9999-12-31','YYYY-MM-DD')");  //最终出库日期
		$data ['SHULIANG'] = $_POST ['SHUL'];                //数量
		$data ['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$_POST['SHCHRQ']."','YYYY-MM-DD')");   //生产日期
		$data ['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$_POST['BZHQZH']."','YYYY-MM-DD')");   //保质期至
		
		//在库商品信息表
		$this->_db->insert ( "H01DB012404", $data );	
	}
	
	/*
	 * 变更后更新在库商品信息
	 */
	public function updateBghZaiku($bghshul,$qufen,$zhtai) {
		
			//变更后更新在库信息
			$sql_zaiku = "UPDATE H01DB012404 ".
			             "SET SHULIANG = :SHULIANG " .
			             (($qufen == 0) ? ",ZZHCHKRQ = TO_DATE('9999-12-31','YYYY-MM-DD') " : "").
			             " WHERE QYBH = :QYBH ".
			             " AND CKBH = :CKBH " .
			             " AND KQBH = :KQBH ".
			             " AND KWBH = :KWBH ".
			             " AND SHPBH = :SHPBH " .
			             " AND PIHAO = :PIHAO " .
			             " AND ZKZHT = :ZKZHT " .
			             " AND RKDBH = :RKDBH " .
			             " AND BZHDWBH = :BZHDWBH" .
		                 " AND TO_CHAR(SHCHRQ,'YYYY-MM-DD') = :SHCHRQ";
		
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['CKBH'] = $_POST ['CKBH']; 
			$bind ['KQBH'] = $_POST ['KQBH']; 
			$bind ['KWBH'] = $_POST ['KWBH']; 
			$bind ['SHPBH'] = $_POST ['SHPBH']; 
			$bind ['PIHAO'] = $_POST ['PIHAO']; 
			$bind ['BZHDWBH'] = $_POST ['DANWEIBH']; 
			$bind ['RKDBH'] = substr($_POST ['RKDJH'],0,14);
			$bind ['ZKZHT'] = $zhtai;
			$bind ['SHCHRQ'] = $_POST ['SHCHRQ']; 
			if($qufen == 0){
				$bind ['SHULIANG'] = $_POST ['SHUL'];  
			}else{
				$bind ['SHULIANG'] = $bghshul + $_POST ['SHUL'];   
			}	            
			$this->_db->query ( $sql_zaiku,$bind );			
	}
	
	/*
	 * 登录在库商品状态更新信息
	 */
	public function insertZhangtaigx($danjvbh,$zhuangtai) {
		
		$data ['QYBH'] = $_SESSION ['auth']->qybh;       //区域编号
		$data ['DJBH'] = $danjvbh;                       //单据编号
		$data ['KPRQ'] = new Zend_Db_Expr('SYSDATE');    //开票日期
		$data ['YWYBH'] = $_POST ['YWYBH'];              //业务员编号
		$data ['BMBH'] = $_POST ['BUMENBH'];             //部门编号
		$data ['SHPBH'] = $_POST ['SHPBH'];              //商品编号
		$data ['CKBH'] = $_POST ['CKBH'];                //仓库编号
		$data ['KQBH'] = $_POST ['KQBH'];                //库区编号
		$data ['KWBH'] = $_POST ['KWBH'];                //库位编号
		$data ['SHULIANG'] = $_POST ['SHUL'];            //数量
		$data ['BZHDWBH'] = $_POST ['DANWEIBH'];         //包装单位编号
		$data ['GXQZKZHT'] = $_POST ['ZKZHTBH'];         //更新前在库状态
		$data ['GXHZKZHT'] = $zhuangtai;                 //更新后在库状态
		$data ['PIHAO'] = $_POST ['PIHAO'];              //批号
		$data ['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$_POST['SHCHRQ']."','YYYY-MM-DD')"); //生产日期
		$data ['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$_POST['BZHQZH']."','YYYY-MM-DD')"); //保质期至
		$data ['RKDBH'] = substr($_POST ['RKDJH'],0,14); //入库单编号
		$data ['BGRQ'] = new Zend_Db_Expr('SYSDATE');    //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId;     //变更者
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		//在库商品状态更新信息
		$this->_db->insert ( "H01DB012421", $data );	
	}
	
	/* --------------------           生产批号变更                       ----------------------*/
	
	public function getBgqshulPihao($qufen) {
		
		//检索SQL
		$sql = "SELECT SHULIANG,TO_CHAR(BZHQZH,'YYYY-MM-DD') AS BZHQZH ". 
		       " FROM H01DB012404". 
		       " WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQBH = :KQBH AND KWBH = :KWBH AND SHPBH = :SHPBH" . 
		       " AND PIHAO = :PIHAO AND RKDBH = :RKDBH AND ZKZHT = :ZKZHT AND BZHDWBH = :BZHDWBH AND TO_CHAR(SHCHRQ,'YYYY-MM-DD') = :SHCHRQ ".
		       " FOR UPDATE";

		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CKBH'] = $_POST ['CKBH'];
		$bind ['KQBH'] = $_POST ['KQBH'];
		$bind ['KWBH'] = $_POST ['KWBH'];
		$bind ['SHPBH'] = $_POST ['SHPBH'];
		//变更前在库数量检索
		if ($qufen == '1'){
			$bind ['PIHAO'] = $_POST ['PIHAO'];
			$bind ['SHCHRQ'] = $_POST ['SHCHRQ']; 
		//变更后在库数量检索
		}else{
			$bind ['PIHAO'] = $_POST ['TZHHPIHAO'];
			$bind ['SHCHRQ'] = $_POST ['TZHHSHCHRQ']; 
		}	
		$bind ['RKDBH'] = substr($_POST ['RKDJH'],0,14);
	    $bind ['ZKZHT'] = $_POST ['ZKZHTBH'];
		$bind ['BZHDWBH'] = $_POST ['DANWEIBH'];
	    $result = $this->_db->fetchRow ( $sql, $bind );
		return $result;		
	}
	
	/*
	 * 在库商品信息保存
	 */
	public function insertZaikuPihao() {
		
		$data ['QYBH'] = $_SESSION ['auth']->qybh;           //区域编号
		$data ['CKBH'] = $_POST ['CKBH'];                    //仓库编号
		$data ['KQBH'] = $_POST ['KQBH'];                    //库区编号
		$data ['KWBH'] = $_POST ['KWBH'];                    //库位编号
		$data ['SHPBH'] = $_POST ['SHPBH'];                  //商品编号
		$data ['PIHAO'] = $_POST ['TZHHPIHAO'];              //调整后批号
		$data ['RKDBH'] = substr($_POST ['RKDJH'],0,14);     //入库单编号
		$data ['ZKZHT'] = $_POST ['ZKZHTBH'];                //在库状态
		$data ['BZHDWBH'] = $_POST ['DANWEIBH'];             //包装单位编号
		$data ['ZZHCHKRQ'] = new Zend_Db_Expr("TO_DATE('9999-12-31','YYYY-MM-DD')");  //最终出库日期
		$data ['SHULIANG'] = $_POST ['SHUL'];                //数量
		$data ['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$_POST['TZHHSHCHRQ']."','YYYY-MM-DD')");   //调整后生产日期
		$data ['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$_POST['TZHHBZHQZH']."','YYYY-MM-DD')");   //调整后保质期至
		
		//在库商品信息表
		$this->_db->insert ( "H01DB012404", $data );	
	}
	
	/*
	 * 变更后更新在库商品信息
	 */
	public function updateBghZaikuPihao($bghshul,$qufen) {
		
			//变更后更新在库信息
			$sql_zaiku = "UPDATE H01DB012404 ".
			             "SET SHULIANG = :SHULIANG " .
			             (($qufen == 0) ? ",ZZHCHKRQ = TO_DATE('9999-12-31','YYYY-MM-DD') " : "").
			             " WHERE QYBH = :QYBH ".
			             " AND CKBH = :CKBH " .
			             " AND KQBH = :KQBH ".
			             " AND KWBH = :KWBH ".
			             " AND SHPBH = :SHPBH " .
			             " AND PIHAO = :PIHAO " .
			             " AND ZKZHT = :ZKZHT " .
			             " AND RKDBH = :RKDBH " .
			             " AND BZHDWBH = :BZHDWBH" .
		                 " AND TO_CHAR(SHCHRQ,'YYYY-MM-DD') = :SHCHRQ";
		
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['CKBH'] = $_POST ['CKBH']; 
			$bind ['KQBH'] = $_POST ['KQBH']; 
			$bind ['KWBH'] = $_POST ['KWBH']; 
			$bind ['SHPBH'] = $_POST ['SHPBH']; 
			$bind ['PIHAO'] = $_POST ['TZHHPIHAO']; 
			$bind ['BZHDWBH'] = $_POST ['DANWEIBH']; 
			$bind ['RKDBH'] = substr($_POST ['RKDJH'],0,14);
			$bind ['ZKZHT'] = $_POST ['ZKZHTBH']; 
			$bind ['SHCHRQ'] = $_POST ['TZHHSHCHRQ']; 
			if($qufen == 0){
				$bind ['SHULIANG'] = $_POST ['SHUL'];  
			}else{
				$bind ['SHULIANG'] = $bghshul + $_POST ['SHUL'];   
			}	            
			$this->_db->query ( $sql_zaiku,$bind );			
	}
	
	/*
	 * 更新DB:商品移动履历（H01DB012405)(批号调整)
	 */
	public function insertYidongllPh($danjvbh) {
		
		//更新DB:商品移动履历（H01DB012405)
		$lvli ["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
		$lvli ["CKBH"] = $_POST ['CKBH']; //仓库编号
		$lvli ["KQBH"] = $_POST ['KQBH'];; //库区编号
		$lvli ["KWBH"] = $_POST ['KWBH'];; //库位编号
		$lvli ["SHPBH"] = $_POST ['SHPBH'];; //商品编号
		$lvli ["PIHAO"] = $_POST ['TZHHPIHAO'];; //批号
		$lvli ["RKDBH"] = substr($_POST ['RKDJH'],0,14);
		$lvli ["YDDH"] = $danjvbh; //移动单号
		$lvli ["XUHAO"] = 2; //序号
		$lvli['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$_POST['TZHHSHCHRQ']."','YYYY-MM-DD')"); //生产日期
		$lvli['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$_POST['TZHHBZHQZH']."','YYYY-MM-DD')"); //保质期至
		$lvli['CHLSHJ'] = new Zend_Db_Expr('SYSDATE'); //处理时间
		$lvli ["SHULIANG"] = $_POST ['SHUL']; //移动数量
		$lvli ["ZHYZHL"] = '71'; //转移种类 [批号调整]
		$lvli ["BZHDWBH"] = $_POST ['DANWEIBH']; //包装单位编号
		$lvli ["ZKZHT"] = $_POST ['ZKZHTBH'];//在库状态
		$lvli['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
		$lvli['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$lvli ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$lvli ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$this->_db->insert ( 'H01DB012405', $lvli );				
		
	}
	
	/*
	 * 登录在库商品批号修改信息
	 */
	public function insertPihaoxg($danjvbh) {
		
		$data ['QYBH'] = $_SESSION ['auth']->qybh;       //区域编号
		$data ['DJBH'] = $danjvbh;                       //单据编号
		$data ['KPRQ'] = new Zend_Db_Expr('SYSDATE');    //开票日期
		$data ['YWYBH'] = $_POST ['YWYBH'];              //业务员编号
		$data ['BMBH'] = $_POST ['BMBH'];             //部门编号
		$data ['SHPBH'] = $_POST ['SHPBH'];              //商品编号
		$data ['CKBH'] = $_POST ['CKBH'];                //仓库编号
		$data ['KQBH'] = $_POST ['KQBH'];                //库区编号
		$data ['KWBH'] = $_POST ['KWBH'];                //库位编号
		$data ['SHULIANG'] = $_POST ['SHUL'];            //数量
		$data ['BZHDWBH'] = $_POST ['DANWEIBH'];         //包装单位编号		
		$data ['ZKZHT'] = $_POST ['ZKZHTBH'];            //在库状态
		$data ['TZHQPH'] = $_POST ['PIHAO'];             //调整前批号		
		$data ['TZHQSHCRQ'] = new Zend_Db_Expr("TO_DATE('".$_POST['SHCHRQ']."','YYYY-MM-DD')");  //调整前生产日期	
		$data ['TZHQBZHQZH'] = new Zend_Db_Expr("TO_DATE('".$_POST['BZHQZH']."','YYYY-MM-DD')"); //调整前保质期至
		$data ['TZHHPH'] = $_POST ['TZHHPIHAO'];             //调整后批号	
		$data ['TZHHSHCRQ'] = new Zend_Db_Expr("TO_DATE('".$_POST['TZHHSHCHRQ']."','YYYY-MM-DD')");  //调整后生产日期	
		$data ['TZHHBZHQZH'] = new Zend_Db_Expr("TO_DATE('".$_POST['TZHHBZHQZH']."','YYYY-MM-DD')"); //调整后保质期至
		$data ['RKDBH'] = substr($_POST ['RKDJH'],0,14); //入库单编号
		$data ['BGRQ'] = new Zend_Db_Expr('SYSDATE');    //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId;     //变更者
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		//在库商品状态更新信息
		$this->_db->insert ( "H01DB012420", $data );	
	}
	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck($qufen) {
		//状态变更
		if ($qufen == 1){
			if ($_POST ["SHUL"] == "" || //数量
			    $_POST ["SHUL"] == 0  || //数量
			    $_POST ["RKDJH"] == ""  || //入库单编号
			    $_POST ["BMBH"] == ""  || //部门编号
			    $_POST ["YWYBH"] == "") //业务员编号
	          { return false;}
	    //批号变更      
		}else{
			if ($_POST ["SHUL"] == "" ||      //数量
			    $_POST ["SHUL"] == 0  ||      //数量
			    $_POST ["RKDJH"] == ""  ||    //入库单编号
			    $_POST ["BMBH"] == ""  ||  //部门编号
			    $_POST ["YWYBH"] == "" ||     //业务员编号
			    $_POST ["TZHHPIHAO"] == "" || //调整后批号
			    $_POST ["TZHHSHCHRQ"] == "" ||     //调整后生产日期
			    $_POST ["TZHHBZHQZH"] == "" )      //调整后保质期至
	          { return false;}			
		}
		
		return true;
	}
}