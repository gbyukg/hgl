<?php
/******************************************************************
 ***** 模         块：       采购模块(CG)
 ***** 机         能：       采购退货(cgth)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/01/07
 ***** 更新履历：

 ******************************************************************/


class cg_models_cgth extends Common_Model_Base {
	private $_rkdbh = null;           // 入库单编号
	private $idx_ROWNUM=0;            // 行号
	private $idx_SHPBH=1;             // 商品编号
	private $idx_SHPMCH=2;            // 商品名称
	private $idx_GUIGE=3;             // 规格
	private $idx_BZHDWM=4;            // 包装单位
	private $idx_HWMCH=5;             // 库位
	private $idx_PIHAO=6;             // 批号
	private $idx_SHCHRQ=7;            // 生产日期
	private $idx_BZHQZH=8;            // 保质期至
	private $idx_THSHULIANG=9;        // 退货数量
	private $idx_SHULIANG=10;         // 库存数量
	private $idx_DANJIA=11;           // 单价
	private $idx_HSHJ=12;             // 含税价
	private $idx_KOULV=13;            // 扣率
	private $idx_SHUILV=14;           // 税率
	private $idx_HSHJE=15;            // 含税金额
	private $idx_JINE=16;             // 金额
	private $idx_SHUIE=17;            // 税额
	private $idx_LSHJ=18;             // 零售价	
	private $idx_CHANDI=19;           // 产地
	private $idx_BEIZHU=20;           // 备注
	private $idx_BZHDWBH=21;          // 包装单位编号
	private $idx_TYMCH=22;            // 通用名称
	private $idx_JLGG=23;             // 计量规格
	private $idx_CKBH=24;             // 仓库编号
	private $idx_KQBH=25;             // 库区编号
	private $idx_KWBH=26;             // 库位编号
	private $idx_SHFSHKW=27;          // 是否散货库位
	private $idx_ZKZHT=28;            // 在库状态


	/*
	 * 取得发货区信息
	 */
	public function getFHQInfo() {
		$sql = "SELECT FHQBH,FHQMCH FROM H01DB012422 WHERE QYBH = :QYBH AND FHQZHT = '1'";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$result = $this->_db->fetchPairs ( $sql, $bind );
		$result [''] = '--选择发货区--';
		ksort ( $result );
		return $result;
	}
	
	
	/**
	 * 得到退货单列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridDanjuData($filter) {
		//排序用字段名
		$fields = array ("", "RKDBH", "KPRQ", "NLSSORT(BMMCH,'NLS_SORT=SCHINESE_PINYIN_M')",
						 "NLSSORT(YGXM,'NLS_SORT=SCHINESE_PINYIN_M')","NLSSORT(DWMCH,'NLS_SORT=SCHINESE_PINYIN_M')");
		
		//检索SQL
		$sql = "SELECT RKDBH,TO_CHAR(KPRQ,'YYYY-MM-DD'),BMMCH,YWYXM,DWMCH,"
				."DHHM,DIZHI,KOULV,BEIZHU,CKDBH "
				."FROM H01VIEW012406 WHERE QYBH = :QYBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter ["ksrqkey"] != "" || $filter ["zzrqkey"] != "")
		{
			$sql .= " AND :KSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :ZZRQ";
			$bind ['KSRQ'] = $filter ["ksrqkey"] == ""?"1900-01-01":$filter ["ksrqkey"];
			$bind ['ZZRQ'] = $filter ["zzrqkey"] == ""?"9999-12-31":$filter ["zzrqkey"];
		}
		
		//查询条件(单位编号输入)
		if ($filter ["dwbhkey"] != "") {
			$sql .= " AND DWBH LIKE '%' || :DWBH || '%'";
			$bind ['DWBH'] = $filter ["dwbhkey"];
		}
		
		//查询条件(单位编号没输入,只输入单位名称)
		if($filter ["dwbhkey"] == "" && $filter ["dwmchkey"] != "") {
			$sql .= " AND DWMCH LIKE '%' || :DWMCH || '%'";
			$bind ['DWMCH'] = $filter ["dwmchkey"];
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CG_CGTH_DJ",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",RKDBH";
		
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
	 * 得到退货单明细列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridMingxiData($filter) {
				//排序用字段名
		$fields = array ("", "A.SHPBH", "NLSSORT(B.SHPMCH,'NLS_SORT=SCHINESE_PINYIN_M')");
		     
		$sql = "SELECT "          
				."A.SHPBH,"      		  //商品编号
				."B.SHPMCH,"     		  //商品名称
				."B.GUIGE,"      		  //规格
				."C.NEIRONG AS BZHDWM,"   //包装单位
				."A.PIHAO,"      		  //批号
				."TO_CHAR(A.SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,"     //生产日期
				."TO_CHAR(A.BZHQZH,'YYYY-MM-DD') AS BZHQZH,"     //保质期至
				."A.BZHSHL,"     		  //包装数量
				."A.LSSHL,"      		  //零散数量
				."A.SHULIANG,"  		  //数量
				."A.DANJIA,"      	      //单价
				."A.HSHJ,"       		  //含税价
				."A.KOULV,"               //扣率
				."B.SHUILV,"    	 	  //税率
				."A.HSHJE,"     	 	  //含税金额
				."A.JINE,"      	 	  //金额
				."A.SHUIE,"      		  //税额
				."B.CHANDI,"     		  //产地
				."A.BEIZHU,"      	      //备注
				."B.BZHDWBH,"    		  //包装单位编号
				."B.TYMCH,"               //通用名
				."B.JLGG "                //计量规格
			  ."FROM H01DB012407 A "
			  ."LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "  
			  ."LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' "
			  ."WHERE A.QYBH = :QYBH "       //区域编号
			  ."AND A.RKDBH = :RKDBH ";      //入库单编号 
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['RKDBH'] = $filter ["bh"];
				
		//排序
		$sql .= " ORDER BY " . $fields [$filter["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.RKDBH,A.XUHAO";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne( $pagedSql["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll( $pagedSql["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true, $totalCount, $filter["posStart"] );
		
	}
	
	
	/*
	 * 根据单位编号编号取得单位信息
	 */
	public function getDanweiInfo($filter) {
		//检索SQL
		$sql =  "SELECT A.DWBH,A.DWMCH,A.DIZHI,A.DHHM,A.KOULV,A.FHQBH," . "DECODE(A.XSHXDQ,NULL,0,XSHXDQ) XSHXDQ" . //销售信贷期 
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
	 * 根据商品编号，批号，生产日期，入库单号等条件获取已退货数量
	 */
//	public function getkthsl($filter){
//		//检索SQL
//		$sql =  "SELECT SUM(A.SHULIANG) AS SHULIANG "
//			    ." FROM H01DB012309 A,H01DB012308 B "
//				." WHERE A.QYBH = B.QYBH "                 //区域编号
//				." AND A.PIHAO = :PIHAO "                  //批号
//				." AND A.SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD') "    
//				." AND A.SHPBH = :SHPBH "                  //商品编号
//				." AND A.CGTHDBH = B.CGTHDBH "             //采购退货单编号
//				." AND B.YRKDBH = :YRKDBH"                 //原入库单编号
//				." AND A.QYBH = :QYBH";
//
//		//绑定查询条件
//		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
//		$bind ['PIHAO'] = $filter ['pihao'];
//		$bind ['SHCHRQ'] = $filter ['scrq'];
//		$bind ['SHPBH'] = $filter ['shpbh'];
//		$bind ['YRKDBH'] = $filter ['rkdbh'];
//		return $this->_db->fetchRow( $sql, $bind );
//	}


	/*
	 * 建立在库商品冻结信息
	 * 
	 */
	public function updateKC() {
        //循环所有明细行， 建立在库商品冻结信息
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_THSHULIANG] == '0')continue;
			
			//获取数据库中该商品冻结信息的数量
			$sql = "SELECT SHULIANG FROM H01DB012404 ".
			             " WHERE QYBH = :QYBH ".
			             " AND CKBH = :CKBH " .
			             " AND KQBH = :KQBH ".
			             " AND KWBH = :KWBH ".
			             " AND SHPBH = :SHPBH " .
			             " AND PIHAO = :PIHAO " .
			             " AND ZKZHT = '2' " .
			             " AND RKDBH = :RKDBH " .
			             " AND BZHDWBH = :BZHDWBH ".
			             " AND SHCHRQ = TO_DATE(:SHCHRQ ,'YYYY-MM-DD') ";
			             
			$bind1 ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind1 ['CKBH'] = $grid [$this->idx_CKBH];             //仓库编号
			$bind1 ['KQBH'] = $grid [$this->idx_KQBH];             //库区编号
			$bind1 ['KWBH'] = $grid [$this->idx_KWBH];             //库位编号
			$bind1 ['SHPBH'] = $grid [$this->idx_SHPBH];           //商品编号
			$bind1 ['PIHAO'] = $grid [$this->idx_PIHAO];           //批号
			$bind1 ['BZHDWBH'] = $grid [$this->idx_BZHDWBH];       //包装单位编号
			$bind1 ['RKDBH'] = $_POST ['RKDBH'];                   //入库单编号
			$bind1 ['SHCHRQ'] = $grid [$this->idx_SHCHRQ];         //生产日期     
			 
			$CHECKSL = $this->_db->fetchOne( $sql, $bind1 );       //获取数据库中该商品冻结信息的数量
				
			if( $grid [$this->idx_THSHULIANG] == $grid [$this->idx_SHULIANG] )
			{
				if( $CHECKSL == null ){
				
					//更新在库信息
					$sql_zaiku = "UPDATE H01DB012404 ".
					             " SET ZKZHT = '2' " .
					             " WHERE QYBH = :QYBH ".
					             " AND CKBH = :CKBH " .
					             " AND KQBH = :KQBH ".
					             " AND KWBH = :KWBH ".
					             " AND SHPBH = :SHPBH " .
					             " AND PIHAO = :PIHAO " .
					             " AND ZKZHT = :ZKZHT " .
					             " AND RKDBH = :RKDBH " .
					             " AND BZHDWBH = :BZHDWBH ".
					             " AND SHCHRQ = TO_DATE(:SHCHRQ ,'YYYY-MM-DD') ";
					             
					$bind2 ['QYBH'] = $_SESSION ['auth']->qybh;
					$bind2 ['CKBH'] = $grid [$this->idx_CKBH];             //仓库编号
					$bind2 ['KQBH'] = $grid [$this->idx_KQBH];             //库区编号
					$bind2 ['KWBH'] = $grid [$this->idx_KWBH];             //库位编号
					$bind2 ['SHPBH'] = $grid [$this->idx_SHPBH];           //商品编号
					$bind2 ['PIHAO'] = $grid [$this->idx_PIHAO];           //批号
					$bind2 ['BZHDWBH'] = $grid [$this->idx_BZHDWBH];       //包装单位编号
					$bind2 ['RKDBH'] = $_POST ['RKDBH'];                   //入库单编号
					$bind2 ['ZKZHT'] = $grid [$this->idx_ZKZHT];           //在库状态
					$bind2 ['SHCHRQ'] = $grid [$this->idx_SHCHRQ];         //生产日期
					      
					$this->_db->query( $sql_zaiku,$bind2 );
					
				}else{
					
					//更新在库信息
					$sql_zaiku = "UPDATE H01DB012404 ".
					             "SET SHULIANG = SHULIANG - :SHULIANG " .
					             " WHERE QYBH = :QYBH ".
					             " AND CKBH = :CKBH " .
					             " AND KQBH = :KQBH ".
					             " AND KWBH = :KWBH ".
					             " AND SHPBH = :SHPBH " .
					             " AND PIHAO = :PIHAO " .
					             " AND ZKZHT = :ZKZHT " .
					             " AND RKDBH = :RKDBH " .
					             " AND BZHDWBH = :BZHDWBH ".
					             " AND SHCHRQ = TO_DATE(:SHCHRQ ,'YYYY-MM-DD') ";
					             
					$bind3 ['QYBH'] = $_SESSION ['auth']->qybh;
					$bind3 ['CKBH'] = $grid [$this->idx_CKBH];             //仓库编号
					$bind3 ['KQBH'] = $grid [$this->idx_KQBH];             //库区编号
					$bind3 ['KWBH'] = $grid [$this->idx_KWBH];             //库位编号
					$bind3 ['SHPBH'] = $grid [$this->idx_SHPBH];           //商品编号
					$bind3 ['PIHAO'] = $grid [$this->idx_PIHAO];           //批号
					$bind3 ['BZHDWBH'] = $grid [$this->idx_BZHDWBH];       //包装单位编号
					$bind3 ['RKDBH'] = $_POST ['RKDBH'];                   //入库单编号
					$bind3 ['ZKZHT'] = $grid [$this->idx_ZKZHT];           //在库状态
					$bind3 ['SHCHRQ'] = $grid [$this->idx_SHCHRQ];         //生产日期
					$bind3 ['SHULIANG'] = $grid [$this->idx_THSHULIANG];   //退货数量          
					 
					$this->_db->query ( $sql_zaiku,$bind3 );
					
					
					//更新在库信息
					$sql_zaiku = "UPDATE H01DB012404 ".
					             "SET SHULIANG = SHULIANG + :SHULIANG " .
					             " WHERE QYBH = :QYBH ".
					             " AND CKBH = :CKBH " .
					             " AND KQBH = :KQBH ".
					             " AND KWBH = :KWBH ".
					             " AND SHPBH = :SHPBH " .
					             " AND PIHAO = :PIHAO " .
					             " AND ZKZHT = '2' " .
					             " AND RKDBH = :RKDBH " .
					             " AND BZHDWBH = :BZHDWBH ".
					             " AND SHCHRQ = TO_DATE(:SHCHRQ ,'YYYY-MM-DD') ";
					             
					$bind4 ['QYBH'] = $_SESSION ['auth']->qybh;
					$bind4 ['CKBH'] = $grid [$this->idx_CKBH];             //仓库编号
					$bind4 ['KQBH'] = $grid [$this->idx_KQBH];             //库区编号
					$bind4 ['KWBH'] = $grid [$this->idx_KWBH];             //库位编号
					$bind4 ['SHPBH'] = $grid [$this->idx_SHPBH];           //商品编号
					$bind4 ['PIHAO'] = $grid [$this->idx_PIHAO];           //批号
					$bind4 ['BZHDWBH'] = $grid [$this->idx_BZHDWBH];       //包装单位编号
					$bind4 ['RKDBH'] = $_POST ['RKDBH'];                   //入库单编号
					$bind4 ['SHCHRQ'] = $grid [$this->idx_SHCHRQ];         //生产日期
					$bind4 ['SHULIANG'] = $grid [$this->idx_THSHULIANG];   //退货数量          
					 
					$this->_db->query ( $sql_zaiku,$bind4 );
				}
				
			}else{
				
				if( $CHECKSL == null ){

					$data ['QYBH'] = $_SESSION ['auth']->qybh;            //区域编号
					$data ['CKBH'] = $grid [$this->idx_CKBH];             //仓库编号
					$data ['KQBH'] = $grid [$this->idx_KQBH];             //库区编号
					$data ['KWBH'] = $grid [$this->idx_KWBH];             //库位编号
					$data ['SHPBH'] = $grid [$this->idx_SHPBH];           //商品编号
					$data ['PIHAO'] = $grid [$this->idx_PIHAO];           //批号
					$data ['RKDBH'] = $_POST ['RKDBH'];                   //入库单编号
					$data ['ZKZHT'] = '2';                                //在库状态，2:冻结
					$data ['BZHDWBH'] = $grid [$this->idx_BZHDWBH];       //包装单位编号
					$data ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" ); //生产日期
					$data ['ZZHCHKRQ'] = new Zend_Db_Expr("TO_DATE('9999/12/31 23:59:59','YYYY-MM-DD HH24:mi:ss')");     //最终出库日期
					$data ['SHULIANG'] = $grid [$this->idx_THSHULIANG];                                                  //退货数量
					$data ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM')" );    //保质期至
					
					$this->_db->insert ( "H01DB012404", $data );	      //在库商品信息表
					
				}else{
					
					//更新在库信息
					$sql_zaiku = "UPDATE H01DB012404 ".
					             "SET SHULIANG = SHULIANG + :SHULIANG " .
					             " WHERE QYBH = :QYBH ".
					             " AND CKBH = :CKBH " .
					             " AND KQBH = :KQBH ".
					             " AND KWBH = :KWBH ".
					             " AND SHPBH = :SHPBH " .
					             " AND PIHAO = :PIHAO " .
					             " AND ZKZHT = '2' " .
					             " AND RKDBH = :RKDBH " .
					             " AND BZHDWBH = :BZHDWBH ".
					             " AND SHCHRQ = TO_DATE(:SHCHRQ ,'YYYY-MM-DD') ";
					             
					$bind5 ['QYBH'] = $_SESSION ['auth']->qybh;
					$bind5 ['CKBH'] = $grid [$this->idx_CKBH];             //仓库编号
					$bind5 ['KQBH'] = $grid [$this->idx_KQBH];             //库区编号
					$bind5 ['KWBH'] = $grid [$this->idx_KWBH];             //库位编号
					$bind5 ['SHPBH'] = $grid [$this->idx_SHPBH];           //商品编号
					$bind5 ['PIHAO'] = $grid [$this->idx_PIHAO];           //批号
					$bind5 ['BZHDWBH'] = $grid [$this->idx_BZHDWBH];       //包装单位编号
					$bind5 ['RKDBH'] = $_POST ['RKDBH'];                   //入库单编号
					$bind5 ['SHCHRQ'] = $grid [$this->idx_SHCHRQ];         //生产日期
					$bind5 ['SHULIANG'] = $grid [$this->idx_THSHULIANG];   //退货数量          
					 
					$this->_db->query ( $sql_zaiku,$bind5 );
					
				}
					
				//更新在库信息
				$sql_zaiku = "UPDATE H01DB012404 ".
				             "SET SHULIANG = SHULIANG - :SHULIANG " .
				             " WHERE QYBH = :QYBH ".
				             " AND CKBH = :CKBH " .
				             " AND KQBH = :KQBH ".
				             " AND KWBH = :KWBH ".
				             " AND SHPBH = :SHPBH " .
				             " AND PIHAO = :PIHAO " .
				             " AND ZKZHT = :ZKZHT " .
				             " AND RKDBH = :RKDBH " .
				             " AND BZHDWBH = :BZHDWBH ".
				             " AND SHCHRQ = TO_DATE(:SHCHRQ ,'YYYY-MM-DD') ";
				             
				$bind6 ['QYBH'] = $_SESSION ['auth']->qybh;
				$bind6 ['CKBH'] = $grid [$this->idx_CKBH];             //仓库编号
				$bind6 ['KQBH'] = $grid [$this->idx_KQBH];             //库区编号
				$bind6 ['KWBH'] = $grid [$this->idx_KWBH];             //库位编号
				$bind6 ['SHPBH'] = $grid [$this->idx_SHPBH];           //商品编号
				$bind6 ['PIHAO'] = $grid [$this->idx_PIHAO];           //批号
				$bind6 ['BZHDWBH'] = $grid [$this->idx_BZHDWBH];       //包装单位编号
				$bind6 ['RKDBH'] = $_POST ['RKDBH'];                   //入库单编号
				$bind6 ['ZKZHT'] = $grid [$this->idx_ZKZHT];           //在库状态
				$bind6 ['SHCHRQ'] = $grid [$this->idx_SHCHRQ];         //生产日期
				$bind6 ['SHULIANG'] = $grid [$this->idx_THSHULIANG];   //退货数量          
				 
				$this->_db->query ( $sql_zaiku,$bind6 );
				
			}
		}
	}
	
	
	/**
	 * 退货单信息保存
	 * @param  string  $bh:   单据编号
	 * 
	 * @return bool
	 */
	public function saveMain($bh) {
		$data ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
		$data ['CGTHDBH'] = $bh;                      //编号
		$data ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$data ['BMBH'] = $_POST ['BMBH'];             //部门编号
		$data ['YWYBH'] = $_POST ['YWYBH'];           //业务员编号
		$data ['DWBH'] = $_POST ['DWBH'];             //单位编号
		$data ['DIZHI'] = $_POST ['DIZHI'];           //地址
		$data ['DHHM'] = $_POST ['DHHM'];             //电话
		$data ['SHFZZHSH'] = $_POST ['SHFZZHSH']=='1'?'1':'0';     //是否增值税    0:否(未选中) 1:是(选中)
		$data ['KOULV'] = $_POST ['KOULV'];           //扣率
//		$data ['JLQZ'] = $_POST ['JLQZ'];             //经理签字
		$data ['FKFSH'] = $_POST ['FKFSH'];           //付款方式
		$data ['THDZHT'] = "0";                       //退货单状态
		$data ['SHFPS'] = $_POST ['SHFPS']=='1'?'1':'0';           //是否配送         0:否(未选中) 1:是(选中)
		$data ['BEIZHU'] = $_POST ['BEIZHU'];         //备注
		$data ['SHHZHT'] = "0";                       //审核状态
		$data ['QXBZH'] = "1";                        //取消标志
		$data ['YRKDBH'] = $_POST ['RKDBH'];          //原入库单号
		$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId;     //变更者
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$data ['THLX'] =  "1";                        //退货类型  1：合格品退货

		return $this->_db->insert ( "H01DB012308", $data );     //插入出库单信息
	}
	
	
	/*
	 * 退货单明细保存
	 * @param  string  $bh:   单据编号
	 * 
	 */
	public function saveMingxi($bh) {
		$idx = 1;           //序号自增
		
        //循环所有明细行，保存出库单明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_THSHULIANG] == '0')continue;
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh;        //区域编号
			$data ['CGTHDBH'] = $bh;                          //编号
			$data ['XUHAO'] = $idx ++;                        //序号
			$data ['SHPBH'] = $grid [$this->idx_SHPBH];       //商品编号
			$data ['PIHAO'] = $grid [$this->idx_PIHAO];       //批号
			$data ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" ); //生产日期
			$data ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM')" );    //保质期至
//			$data ['BZHSHL'] = ($grid [$this->idx_BZHSHL] == null) ? 0 : $grid [$this->idx_BZHSHL]; //包装数量
//			$data ['LSSHL'] = ($grid [$this->idx_LSSHL] == null) ? 0 : $grid [$this->idx_LSSHL];    //零散数量
			$data ['SHULIANG'] = $grid [$this->idx_THSHULIANG]; //退货数量
			$data ['DANJIA'] = $grid [$this->idx_DANJIA];     //单价
			$data ['HSHJ'] = $grid [$this->idx_HSHJ];         //含税价
			$data ['KOULV'] = $grid [$this->idx_KOULV];       //扣率
			$data ['JINE'] = $grid [$this->idx_JINE];         //金额
			$data ['HSHJE'] = $grid [$this->idx_HSHJE];       //含税金额
			$data ['SHUIE'] = $grid [$this->idx_SHUIE];       //税额
			$data ['BEIZHU'] = $grid [$this->idx_BEIZHU];     //备注
			$data ['CKBH'] = $grid [$this->idx_CKBH];         //仓库编号
			$data ['KQBH'] = $grid [$this->idx_KQBH];         //库区编号
			$data ['KWBH'] = $grid [$this->idx_KWBH];         //库位编号
			$data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' );  //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId;      //变更者
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId;     //作成者
			
			$this->_db->insert ( "H01DB012309", $data );	  //出库单明细表	
		}
	}


	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck() {
		if ($_POST ["KPRQ"] == "" ||            //开票日期
            $_POST ["RKDBH"] == "" ||           //入库单编号
            $_POST ["#grid_mingxi"] == "") {    //明细表格
			return false;
		}
		
		return true;
	}
	
	
	/*
	 * 数据合法性逻辑性验证
	 */
	public function logicCheck($data) {
		
		//库存数量是否被他人修改验证
		foreach ( $data ["#grid_mingxi"] as $grid ) {

			//获取数据库中该商品冻结信息的数量
			$sql = "SELECT SHULIANG FROM H01DB012404 ".
			             " WHERE QYBH = :QYBH ".
			             " AND CKBH = :CKBH " .
			             " AND KQBH = :KQBH ".
			             " AND KWBH = :KWBH ".
			             " AND SHPBH = :SHPBH " .
			             " AND PIHAO = :PIHAO " .
			             " AND ZKZHT = :ZKZHT " .
			             " AND RKDBH = :RKDBH " .
			             " AND BZHDWBH = :BZHDWBH ".
			             " AND SHCHRQ = TO_DATE(:SHCHRQ ,'YYYY-MM-DD') ";
			             
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['CKBH'] = $grid [$this->idx_CKBH];             //仓库编号
			$bind ['KQBH'] = $grid [$this->idx_KQBH];             //库区编号
			$bind ['KWBH'] = $grid [$this->idx_KWBH];             //库位编号
			$bind ['SHPBH'] = $grid [$this->idx_SHPBH];           //商品编号
			$bind ['PIHAO'] = $grid [$this->idx_PIHAO];           //批号
			$bind ['ZKZHT'] = $grid [$this->idx_ZKZHT];           //在库状态
			$bind ['BZHDWBH'] = $grid [$this->idx_BZHDWBH];       //包装单位编号
			$bind ['RKDBH'] = $_POST ['RKDBH'];                   //入库单编号
			$bind ['SHCHRQ'] = $grid [$this->idx_SHCHRQ];         //生产日期     
			 
			$CHECKSL = $this->_db->fetchOne( $sql, $bind );       //获取数据库中该商品冻结信息的数量
			
			if ( $CHECKSL != $grid [$this->idx_SHULIANG] ) {
				return false;
			}
		}
		
		return true;
	}
	
	
	/*
	 * 根据入库单单编号取得入库单信息
	 */
	public function getInfo($filter) {
		//检索SQL
		$sql = "SELECT A.RKDBH,A.DWBH,B.DWMCH,A.YWYBH,C.YGXM AS YWYMCH,A.DHHM,A.DIZHI,to_char(A.KOULV,'fm990.00') AS KOULV,"
			  ."A.CKDBH,A.SHFZZHSH,A.FKFSH "
			  ."FROM H01DB012406 A "
			  ."LEFT JOIN H01DB012106 B ON A.QYBH = B.QYBH AND A.DWBH = B.DWBH "
			  ."LEFT JOIN H01DB012113 C ON A.QYBH = C.QYBH AND A.YWYBH = C.YGBH "
			  ."WHERE A.QYBH = :QYBH "         //区域编号
			  ."AND A.RKDBH = :RKDBH ";        //入库单编号

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['RKDBH'] = $filter ['bh'];
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
	
	/*
	 * 根据入库单编号取得入库单明细信息
	 */
	public function getmingxi($filter) {
		//检索SQL
		$sql = "SELECT A.XUHAO,"          //序号
				."A.SHPBH,"      		  //商品编号
				."B.SHPMCH,"     		  //商品名称
				."B.GUIGE,"      		  //规格
				."C.NEIRONG AS BZHDWM,"   //包装单位
				."D.CKMCH || E.KQMCH || F.KWMCH AS HWMCH,"     //货位名称
				."A.PIHAO,"      		  //批号
				."TO_CHAR(A.SHCHRQ,'yyyy-mm-dd') AS SHCHRQ,"   //生产日期
				."TO_CHAR(A.BZHQZH,'yyyy-mm') AS BZHQZH,"   //保质期至
				."A.BZHSHL,"     		  //包装数量
				."A.LSSHL,"      		  //零散数量
				."A.SHULIANG,"  		  //数量
				."A.DANJIA,"      	      //单价
				."A.HSHJ,"       		  //含税售价
				."A.KOULV,"               //扣率
				."B.SHUILV,"    	 	  //税率
				."A.HSHJE,"     	 	  //含税金额
				."A.JINE,"      	 	  //金额
				."A.SHUIE,"      		  //税额
				."B.LSHJ,"                //零售价
				."B.CHANDI,"     		  //产地
				."A.BEIZHU,"      	      //备注
				."B.BZHDWBH,"    		  //包装单位编号
				."B.TYMCH,"               //通用名
				."B.JLGG,"                //计量规格
				."A.CKBH,"                //仓库编号
				."A.KQBH,"                //库区编号
				."A.KWBH "                //库位编号
			  ."FROM H01DB012407 A "
			  ."LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "
			  ."LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' "
			  ."LEFT JOIN H01DB012401 D ON A.QYBH = D.QYBH AND A.CKBH = D.CKBH "
			  ."LEFT JOIN H01DB012402 E ON A.QYBH = E.QYBH AND A.CKBH = E.CKBH AND A.KQBH = E.KQBH "
			  ."LEFT JOIN H01DB012403 F ON A.QYBH = F.QYBH AND A.CKBH = F.CKBH AND A.KQBH = F.KQBH AND A.KWBH = F.KWBH "
			  ."WHERE A.QYBH = :QYBH "      //区域编号
			  ."AND A.RKDBH = :RKDBH "      //入库单编号  
			  ."ORDER BY A.XUHAO";

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
		$bind ['RKDBH'] = $filter ['bh'];             //编号
		
		return $this->_db->fetchAll( $sql, $bind );
	}
	
	
	/**
	 * 得到退货单明细列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getRKDMingxiData($filter) {
				//排序用字段名
		$fields = array ("", "SHPBH", "NLSSORT(SHPMCH,'NLS_SORT=SCHINESE_PINYIN_M')");

		$sql = "SELECT * FROM "
			."(SELECT A.SHPBH,"                                      //商品编号
			."B.SHPMCH,"                                             //商品名称
			."B.GUIGE,"                                              //规格
			."C.NEIRONG,"                                            //包装单位
			."D.CKMCH || E.KQMCH || F.KWMCH AS KWMCH,"               //库位名称
			."A.PIHAO,"                                              //批号
			."TO_CHAR(A.SHCHRQ,'yyyy-mm-dd') AS SHCHRQ,"             //生产日期
			."TO_CHAR(A.BZHQZH,'yyyy-mm') AS BZHQZH,"                //保质期至
			."A.SHULIANG,"                                           //数量
			."DECODE(A.ZKZHT,'0','可销','1','催销','2','冻结') AS ZKZHTMCH,"      //在库状态名称
			."G.DANJIA,"      	                                     //单价
			."G.HSHJ,"       		                                 //含税售价
			."G.KOULV,"                                              //扣率
			."B.SHUILV,"    	 	                                 //税率
			."G.HSHJE,"     	 	                                 //含税金额
			."G.JINE,"      	 	                                 //金额
			."G.SHUIE,"      		                                 //税额
			."B.LSHJ,"                                               //零售价
			."B.CHANDI,"     		                                 //产地
			."B.BEIZHU,"      	                                     //备注
			."A.BZHDWBH,"    		                                 //包装单位编号
			."B.TYMCH,"                                              //通用名
			."B.JLGG,"                                               //计量规格
			."A.CKBH,"                                               //仓库编号
			."A.KQBH,"                                               //库区编号
			."A.KWBH,"                                               //库位编号
			."F.SHFSHKW,"                                            //是否散货库位
			."A.ZKZHT,"                                              //在库状态
			."A.QYBH,"
			."A.RKDBH "
			."FROM H01DB012404 A "
			."LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "
			."LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND A.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' "
			."LEFT JOIN H01DB012401 D ON A.QYBH = D.QYBH AND A.CKBH = D.CKBH "
			."LEFT JOIN H01DB012402 E ON A.QYBH = E.QYBH AND A.CKBH = E.CKBH AND A.KQBH = E.KQBH "
			."LEFT JOIN H01DB012403 F ON A.QYBH = F.QYBH AND A.CKBH = F.CKBH AND A.KQBH = F.KQBH AND A.KWBH = F.KWBH "
			."LEFT JOIN H01DB012407 G ON A.QYBH = G.QYBH AND A.RKDBH = G.RKDBH AND A.SHPBH = G.SHPBH AND A.PIHAO = G.PIHAO) "
			."WHERE QYBH = :QYBH "      //区域编号
			."AND ZKZHT != '2' "        //在库状态
			."AND RKDBH = :RKDBH ";     //入库单编号

		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['RKDBH'] = $filter ["rkdbh"];
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CG_CGTH_RKDMX",$filter['filterParams'],$bind);
				
		//排序
		$sql .= " ORDER BY " . $fields [$filter["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",SHPBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne( $pagedSql["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll( $pagedSql["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true, $totalCount, $filter["posStart"] );
		
	}
	
	
	/*
	 * 明细信息商品编号自动完成数据取得
	 */
	public function rkdbhAutocompleteData($filter){	
		//检索SQL
		$sql = "SELECT RKDBH FROM H01VIEW012406 WHERE QYBH = :QYBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;

		//快速查找条件
		if ($filter ["searchkey"] != "") {
			$bind ['SEARCHKEY'] = $filter ["searchkey"];
			$sql .= " AND lower(RKDBH) LIKE '%'||:SEARCHKEY||'%' ";
		}
		
		return $this->_db->fetchAll($sql,$bind);
	}


}
