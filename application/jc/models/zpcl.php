<?php
/*********************************
 * 模块：   基础模块(jc)
 * 机能：    赠品策略(ZPCL)
 * 作成者：姚磊
 * 作成日：2011/7/27
 * 更新履历：
 *********************************/
class jc_models_zpcl extends Common_Model_Base {
	
			
			private $idxx_ROWNUM = 0;// 行号
			private $idxx_SHPBH = 1;// 商品编号
			private $idxx_SHPMCH = 2;// 商品名称
			private $idxx_GUIGE = 3;// 规格
			private $idxx_DWBH=4;//单位
			private $idxx_SCHCJ=5;//生成厂家
			private $idxx_BEIZHU = 6;// 备注
			
			
	/**
	 * 得到赠品策略信息列表数据
	 *
	 * @param $shpbh
	 * @return 
	 */
		public function getdataList($filter){		
				//排序用字段名
		$fields = array ("", "ZHUANGTAI","CLZHL","SHDRQ","KSHRQ","ZHZHRQ","LJFSH","ZPFSH","ZPFSH","JZHFSH","BEIZHU");

		//检索SQL
		$sql = " SELECT DISTINCT  DECODE(ZHUANGTAI,'1','启用','2','作废') AS ZHUANGTAI ,DECODE(CLZHL,'1','单品','2','组合') AS CLZHL,ZPCLBH,".
				   " TO_CHAR(SHDRQ,'YYYY-MM-DD') AS SHDRQ ,TO_CHAR(KSHRQ,'YYYY-MM-DD') AS KSHRQ ,".
				   " TO_CHAR(ZHZHRQ,'YYYY-MM-DD') AS ZHZHRQ  ,DECODE(LJFSH,'1','满足数量','2','满足金额') AS LJFSH,".
				   " DECODE(ZPFSH,'1','实物','2','返现') AS ZPFSH,DECODE(JZHFSH,'1','日期范围','2','赠完为止') AS JZHFSH ,BEIZHU ".
				   " FROM H01VIEW012471  WHERE QYBH =:QYBH"; 

		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;

			//查询条件(开始日期<=终止日期)
		if ($filter['searchParams']["SERCHKSRQ"] != "" || $filter['searchParams']["SERCHJSRQ"] != "")
		{
			$sql .= " AND :SERCHKSRQ <= TO_CHAR(SHDRQ,'YYYY-MM-DD')AND TO_CHAR(SHDRQ,'YYYY-MM-DD') <= :SERCHJSRQ ";
			$bind ['SERCHKSRQ'] = $filter['searchParams']["SERCHKSRQ"] == ""?"1900-01-01":$filter['searchParams']["SERCHKSRQ"];
			$bind ['SERCHJSRQ'] = $filter['searchParams']["SERCHJSRQ"] == ""?"9999-12-31":$filter['searchParams']["SERCHJSRQ"];
		}
		
		//查找条件  赠品策略种类
		if($filter['searchParams']['CLZHL']!=""){
			if($filter['searchParams']['CLZHL']!='0'){
			$sql .= " AND( CLZHL LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(CLZHL) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['CLZHL']);
		}
		}
			//查找条件  赠品策略状态

		if($filter['searchParams']['ZHUANGTAI']!=""){
			if($filter['searchParams']['ZHUANGTAI']!='0'){
			$sql .= " AND( ZHUANGTAI LIKE '%' || :SEARCHKEYZHUANGTAI || '%'".
			        "      OR  lower(ZHUANGTAI) LIKE '%' || :SEARCHKEYZHUANGTAI || '%')";
			$bind ['SEARCHKEYZHUANGTAI'] = strtolower($filter ["searchParams"]['ZHUANGTAI']);
		}
		}
		//商品编号
		if($filter['searchParams']['SHPBH']!=""){
			$sql .= " AND( SHPBH LIKE '%' || :SEARCHKEYSHPBH || '%'".
			        "      OR  lower(SHPBH) LIKE '%' || :SEARCHKEYSHPBH || '%')";
			$bind ['SEARCHKEYSHPBH'] = strtolower($filter ["searchParams"]['SHPBH']);
		}

		$sql .= Common_Tool::createFilterSql("JC_ZPCLWHXX",$filter['filterParams'],$bind);
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= " ,ZHUANGTAI ";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true,$totalCount, $filter ["posStart"] );	
		}
		
	/**
	 * 获取赠品组合策略grid 上一条 下一条
	 *
	 * @param $shpbh
	 * @return 
	 */
		public function getdatagrid($filter,$zpclbh){		
				//排序用字段名
		$fields = array ("", "SHPBH","SHPMCH","GUIGE","BZHDWMCH","SHCHCHJ","BEIZHU");

		//检索SQL
		$sql = " SELECT SHPBH , SHPMCH , GUIGE , BZHDWMCH , SHCHCHJ , BEIZHU  ".
				   " FROM H01VIEW012471  WHERE QYBH =:QYBH AND ZPCLBH =:ZPCLBH"; 

		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;//区域编号
		$bind ['ZPCLBH'] = $zpclbh; //赠品策略编号

		$sql .= Common_Tool::createFilterSql("JC_ZPCLWHXX",$filter['filterParams'],$bind);
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= " ,SHPBH ";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true,$totalCount, $filter ["posStart"] );	
		}
		

	/**
	 * 保存赠品单品策略
	 *
	 * @param $shpbh
	 * @return 
	 */
		public  function saveDanpincl($zpclbh){
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['ZPCLBH'] = $zpclbh; //赠品策略编号
			$data ['CLZHL'] = '1'; //策略种类 1 单品 2 组合
			$data ['SHDRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST['SHDRQ'] . "','YYYY-MM-DD')" ) ; //设定时间
			$data ['ZPFSH'] = $_POST['ZPFSH']; //赠品方式
			$data ['LJFSH'] = $_POST['LJFSH']; //累计方式
			$data ['LJSHL'] = $_POST['LJSHL']; //累计数量
			$data ['LJJE'] = $_POST['LJJE']; //累计金额
			$data ['ZPBH'] = $_POST['ZPBH']; //赠品编号
			$data ['ZSSHL'] = $_POST['ZSSHL']; //赠送数量
			$data ['FXJE'] = $_POST['FXJE']; //返现金额
			$data ['JZHFSH'] = $_POST['JZHFSH']; //截止方式
			$data ['ZHUANGTAI'] = '1'; //状态
			$data ['KSHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST['KSHRQ'] . "','YYYY-MM-DD')" ) ; //开始日期
			$data ['ZHZHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST['ZHZHRQ'] . "','YYYY-MM-DD')" );//结束日期
			$data ['BEIZHU'] = $_POST['BEIZHU']; //备注
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者


			$this->_db->insert ( "H01DB012471", $data );
		
		}
		
		/*
		 * 保存单品策略 商品信息
		 */
		function  saveDanpin($zpclbh){
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['ZPCLBH'] = $zpclbh; //赠品策略编号
			$data ['SHPBH'] = $_POST['SHPBH']; //商品编号
			$data ['BEIZHU'] = $_POST['BEIZHU']; //备注
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			

			$this->_db->insert ( "H01DB012473", $data );
			
			
			
		}
		
		/**
		 * ***
		 * 保存from2 赠品组合策略
		 */
		public function saveForm($zpclbh){
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['ZPCLBH'] = $zpclbh; //赠品策略编号
			$data ['CLZHL'] = '2'; //策略种类 1 单品 2 组合
			$data ['SHDRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST['SHDRQE'] . "','YYYY-MM-DD')" ) ; //设定时间
			$data ['ZPFSH'] = $_POST['ZPFSHE']; //赠送方式
			$data ['LJFSH'] = $_POST['LJFSHE']; //累计方式
			$data ['LJSHL'] = $_POST['LJSHLE']; //累计数量
			$data ['LJJE'] = $_POST['LJJEE']; //累计金额
			$data ['ZPBH'] = $_POST['ZPBHE']; //赠品编号
			$data ['ZSSHL'] = $_POST['ZSSHLE']; //赠送数量
			$data ['FXJE'] = $_POST['FXJEE']; //返现金额
			$data ['JZHFSH'] = $_POST['JZHFSHE']; //截止方式
			$data ['ZHUANGTAI'] = '1'; //状态
			$data ['KSHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST['KSHRQE'] . "','YYYY-MM-DD')" ) ; //开始日期
			$data ['ZHZHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST['ZHZHRQE'] . "','YYYY-MM-DD')" ); //结束日期
			$data ['BEIZHU'] = $_POST['BEIZHUE']; //备注
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者


			$this->_db->insert ( "H01DB012471", $data );
			
			
		}
		
		
	/**
	 * 保存赠品组合策略 grid
	 *
	 * @param $shpbh
	 * @return 
	 */
		public  function saveGrid($zpclbh){

		//循环所有明细行，保存商品信息
		foreach ( $_POST ["#grid_zpclsdxx"] as $grid ) {
			if ($grid [$this->idxx_SHPBH] == '')
				continue;
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['ZPCLBH'] = $zpclbh; //赠品策略编号
			$data ['SHPBH'] = $grid [$this->idxx_SHPBH]; //商品编号
			$data ['BEIZHU'] = $grid [$this->idxx_BEIZHU]; //备注
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$this->_db->insert ( "H01DB012473", $data );
		}
		}
		
	/**
	 * 得到商品信息
	 *
	 * @param $shpbh
	 * @return 
	 */
		public function getShpInfo($filter){
			
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
		 * 获取赠品策略状态 判断单品 组合
		 * 策略种类
		 */
		public function getzhuangtai($zpclbh){			
		$sql ="SELECT  CLZHL FROM H01DB012471 WHERE ZPCLBH =:ZPCLBH AND QYBH =:QYBH ";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;		
		$bind ['ZPCLBH'] = $zpclbh;
		$recs = $this->_db->fetchOne ( $sql, $bind );
		return	$recs;
		
		}
		
		/*
		 * 单品详情信息
		 */
		public function getDate($zpclbh,$filter,$flg = 'current'){
		//排序用字段名
		$fields = array ("", "ZHUANGTAI","CLZHL","SHDRQ","KSHRQ","ZHZHRQ","LJFSH","ZPFSH","ZPFSH","JZHFSH","BEIZHU");

		//检索SQL
		$sql_list = "SELECT  DBTROWID,LEAD(DBTROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",ZPCLBH) AS NEXTROWID," . 
		" LAG(DBTROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",ZPCLBH) AS PREVROWID " . 
		"  ,ZPCLBH " .
		" FROM H01VIEW012471 " .
		" WHERE QYBH = :QYBH ";
	
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			//查询条件(开始日期<=终止日期)
		if ($filter['searchParams']["SERCHKSRQ"] != "" || $filter['searchParams']["SERCHJSRQ"] != "")
		{
			$sql_list .= " AND :SERCHKSRQ <= TO_CHAR(SHDRQ,'YYYY-MM-DD')AND TO_CHAR(SHDRQ,'YYYY-MM-DD') <= :SERCHJSRQ ";
			$bind ['SERCHKSRQ'] = $filter['searchParams']["SERCHKSRQ"] == ""?"1900-01-01":$filter['searchParams']["SERCHKSRQ"];
			$bind ['SERCHJSRQ'] = $filter['searchParams']["SERCHJSRQ"] == ""?"9999-12-31":$filter['searchParams']["SERCHJSRQ"];
		}
		
		//查找条件  赠品策略种类
		if($filter['searchParams']['CLZHL']!=""){
			if($filter['searchParams']['CLZHL']!='0'){
			$sql_list .= " AND( CLZHL LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(CLZHL) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['CLZHL']);
		}
		}
			//查找条件  赠品策略状态

		if($filter['searchParams']['ZHUANGTAI']!=""){
			if($filter['searchParams']['ZHUANGTAI']!='0'){
			$sql_list .= " AND( ZHUANGTAI LIKE '%' || :SEARCHKEYZHUANGTAI || '%'".
			        "      OR  lower(ZHUANGTAI) LIKE '%' || :SEARCHKEYZHUANGTAI || '%')";
			$bind ['SEARCHKEYZHUANGTAI'] = strtolower($filter ["searchParams"]['ZHUANGTAI']);
		}
		}
		//商品编号
		if($filter['searchParams']['SHPBH']!=""){
			$sql_list .= " AND( SHPBH LIKE '%' || :SEARCHKEYSHPBH || '%'".
			        "      OR  lower(SHPBH) LIKE '%' || :SEARCHKEYSHPBH || '%')";
			$bind ['SEARCHKEYSHPBH'] = strtolower($filter ["searchParams"]['SHPBH']);
		}

		$sql_list .= Common_Tool::createFilterSql("JC_ZPCLWHXX",$filter['filterParams'],$bind);
		$sql_single = " SELECT  ZPCLBH,TO_CHAR(SHDRQ,'YYYY-MM-DD') AS SHDRQ,ZHUANGTAI,CLZHL,LJFSH,LJSHL,LJJE ".
				      " ,ZPFSH , ZSSHL ,FXJE, ZPBH,ZPMCH , JZHFSH ,TO_CHAR(KSHRQ,'YYYY-MM-DD') AS KSHRQ ".
					  " ,TO_CHAR(ZHZHRQ,'YYYY-MM-DD') AS ZHZHRQ,SHPBH,SHPMCH  ".
					  " ,GUIGE,BZHDWMCH,SHCHCHJ ,BEIZHU,TO_CHAR(BGRQ,'YYYY-MM-DD') AS BGRQ ,BGZH".
			   		  " FROM H01VIEW012471   ";
		//当前				
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH = :QYBH AND ZPCLBH = :ZPCLBH ";		
		} else if ($flg == 'next') { //下一条		
			$sql_single .= "WHERE DBTROWID = (SELECT NEXTROWID FROM  (SELECT NEXTROWID,ZPCLBH FROM ( $sql_list ) WHERE ZPCLBH = :ZPCLBH ))";
		} else if ($flg == 'prev') { //前一条
			$sql_single .= "WHERE DBTROWID = (SELECT PREVROWID FROM  (SELECT PREVROWID,ZPCLBH FROM ( $sql_list ) WHERE ZPCLBH = :ZPCLBH ))";
		}
		unset($bind);
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['ZPCLBH'] = $zpclbh;
		
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
		
	/*
	 * 更新赠品策略单品 
	 */
	public function updatezpclxx($zpclbh){
		
		
		$sql = " UPDATE  H01DB012471 SET " .
		 			" ZPFSH =:ZPFSH," . 
		 			" LJJE =:LJJE," . 
		 			" ZPBH =:ZPBH," .
			 	    " ZSSHL =:ZSSHL," .
				    " LJFSH=:LJFSH,".
		 		    " LJSHL =:LJSHL ,FXJE =:FXJE,"  .
		 		    " JZHFSH = :JZHFSH ,".
		 		    " ZHUANGTAI=:ZHUANGTAI ,". 
					" KSHRQ = TO_DATE(:KSHRQ,'YYYY-MM-DD')," . 
					" ZHZHRQ = TO_DATE(:ZHZHRQ,'YYYY-MM-DD')," . 
				    " BEIZHU=:BEIZHU , ".
			 	    " BGRQ = sysdate," . " BGZH =:BGZH " .
			 	    " WHERE QYBH = :QYBH AND ZPCLBH =:ZPCLBH ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['ZPCLBH'] = $zpclbh; //赠品策略编号
			$bind ['ZPFSH'] = $_POST ['ZPFSH']; //赠送方式
			$bind ['LJJE'] = (int)($_POST ['LJJE']); //累计金额
			$bind ['ZPBH'] = $_POST ['ZPBH']; //赠品编号
			$bind ['ZSSHL'] = (int)($_POST ['ZSSHL']); //赠品数量
			$bind ['LJFSH'] = $_POST ['LJFSH']; //累计方式
			if($_POST ['LJSHL'] == ""){
			$bind ['LJSHL'] = '0';     //累计数量
			}else{
				$bind ['LJSHL'] = $_POST ['LJSHL'];
			} 			
			
			if($_POST ['FXJE'] == ""){
			$bind ['FXJE'] = '0';     //返现金额
			}else{
				$bind ['FXJE'] = $_POST ['FXJE'];
			} 
			$bind ['JZHFSH'] = $_POST ['JZHFSH']; //截止方式
			$bind ['ZHUANGTAI'] = $_POST ['ZHUANGTAI']; //状态  1启用 2作废
			$bind ['KSHRQ'] =  $_POST['KSHRQ']; //开始日期
			$bind ['ZHZHRQ'] = $_POST['ZHZHRQ'];//结束日期

			$bind ['BEIZHU'] = $_POST ['BEIZHU']; //备注
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //操作用户			
			$this->_db->query ( $sql, $bind );			
	}
	/*
	 * 删除赠品策略grid
	 * 
	 */
	public function del($zpclbh){
		
			$sql = " DELETE FROM  H01DB012473 " .
			 	   " WHERE QYBH = :QYBH AND ZPCLBH =:ZPCLBH ";
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号			
			$bind ['ZPCLBH'] = $zpclbh; //赠品策略编号
			$this->_db->query ( $sql, $bind );
	}
	
	/*
	 * 获取赠品名称
	 */
	public function getzpmch($filter){
				//排序用字段名
		$fields = array ("", "ZPBH","ZPMCH","GUIGE","SHPBH","SHCHCHJ","BZHQYSH");

		//检索SQL
		$sql = " SELECT  ZPBH,ZPMCH,SHPBH,GUIGE,SHCHCHJ,BZHQYSH ".
			   " FROM H01VIEW012470 WHERE QYBH=:QYBH AND ZHUANGTAI ='1' ";

		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;

		
		//查找条件  编号或名称
		if($filter['searchParams']['ZPXX']!=""){
			$sql .= " AND( lower(ZPBH) LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(ZPMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['ZPXX']);
		}

		$sql .= Common_Tool::createFilterSql("JC_ZPXX",$filter['filterParams'],$bind);
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= " ,ZPBH ";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true,$totalCount, $filter ["posStart"] );
		
		
	}
	
 /*
	 * 自动完成数据取得
	 */
	public function getAutocompleteData($filter){
		
	   		$sql = " SELECT  ZPBH,ZPMCH,SHPBH,GUIGE,SHCHCHJ,BZHQYSH ".
			   " FROM H01VIEW012470 WHERE QYBH=:QYBH AND ZHUANGTAI ='1' ";

			
		//查询条件	
		if ($filter ["searchkey"] != "") {
			$sql .= " AND (lower(ZPBH) LIKE :SEARCHKEY || '%' OR lower(ZPMCH) LIKE :SEARCHKEY  )";
			$bind['SEARCHKEY'] = $filter ["searchkey"];
		}
		
		$sql .= " AND ROWNUM < 40";
		$sql .= " ORDER BY ZPBH";
	
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		return $this->_db->fetchAll($sql,$bind);
	}
	
	
}