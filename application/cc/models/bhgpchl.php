<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   不合格品处理(bhgpchl)
 * 作成者：姚磊
 * 作成日：2011/08/26
 * 更新履历：
 *********************************/
class cc_models_bhgpchl extends Common_Model_Base {
	
	
	
/**
	 * 得到不合格品列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridDanjuData($filter) {
		//排序用字段名
		$fields = array ( "", "A.BHGPRKDBH", "A.RKLX", "A.KPRQ" );
		
		//检索SQL
		$sql = "SELECT A.BHGPRKDBH,DECODE(A.RKLX,'1','采购拒收','2','合格品库移出'),TO_CHAR(A.KPRQ,'YYYY-MM-DD'),"
				."B.BMMCH,C.YGXM AS KPYXM,D.YGXM AS YWYXM,A.BEIZHU,A.CKDBH "
				."FROM H01DB012460 A "
				."LEFT JOIN H01DB012112 B ON A.QYBH = B.QYBH AND A.BMBH = B.BMBH "
				."LEFT JOIN H01DB012113 C ON A.QYBH = C.QYBH AND A.KPYBH = C.YGBH "
				."LEFT JOIN H01DB012113 D ON A.QYBH = D.QYBH AND A.YWYBH = D.YGBH "
				."WHERE A.QYBH = :QYBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter ["ksrqkey"] != "" || $filter ["zzrqkey"] != "")
		{
			$sql .= " AND :KSRQ <= TO_CHAR(A.KPRQ,'YYYY-MM-DD') AND TO_CHAR(A.KPRQ,'YYYY-MM-DD') <= :ZZRQ";
			$bind ['KSRQ'] = $filter ["ksrqkey"] == ""?"1900-01-01":$filter ["ksrqkey"];
			$bind ['ZZRQ'] = $filter ["zzrqkey"] == ""?"9999-12-31":$filter ["zzrqkey"];
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CG_CGTH_DJ",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.BHGPRKDBH";
		
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
	 * 得到不合格品明细列表数据
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
			  ."FROM H01DB012461 A "      //不合格品入库单明细信息
			  ."LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "
			  ."LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' "
			  ."WHERE A.QYBH = :QYBH "               //区域编号
			  ."AND A.BHGPRKDBH = :BHGPRKDBH ";      //不合格品入库单编号
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['BHGPRKDBH'] = $filter ["bh"];
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter["orderby"]] . " " . $filter ["direction"];
		
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.BHGPRKDBH,A.XUHAO";
		
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
	 * 根据商品编号取得商品信息
	 */
	public function getShangpinInfo($filter,$rkdbh) {
		$fields = array ("", "SHPBH", "SHPMCH", "PIHAO", "SHCHRQ", "BZHQZH", "HSHJ", "HSHJE", "SHULIANG" ); 		
		
		//检索SQL
		$sql = "SELECT " .
		       "A.SHPBH," . //商品编号
               "Y.SHPMCH," . //商品名称
			   "A.PIHAO ,".//批号
			   "TO_CHAR(A.SHCHRQ,'YYYY-MM-DD') AS SHCHRQ," .    //生产日期
			   "TO_CHAR(A.BZHQZH,'YYYY-MM-DD') AS BZHQZH," .  //保质期至
			   "A.HSHJ ,".//含税价
			   "A.HSHJE ,".//含税金额
			   "A.SHULIANG ".//数量
			   "FROM H01DB012461 A " .
			   " LEFT JOIN H01DB012101 Y ON A.QYBH = Y.QYBH AND   A.SHPBH = Y.SHPBH   ".           //做成view表,高级查询用    
		       " WHERE A.QYBH = :QYBH AND A.BHGPRKDBH=:BHGPRKDBH " ;
	
			
		$bind ['BHGPRKDBH'] = $rkdbh; //不合格品入库单编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		
			//查询条件(单位编号输入)
		if ($filter ['searchParams']["SEARCHKEY"] != "") {
			$sql .= " AND A.SHPBH LIKE '%' || :SHPBH || '%'";
			$bind ['SHPBH'] = $filter ['searchParams']["SEARCHKEY"];
		}
		
		//查询条件(单位编号没输入,只输入单位名称)
		if($filter ['searchParams']["SEARCHKEY"] == "" && $filter ['searchParams']["SEARCHKEY"] != "") {
			$sql .= " AND Y.SHPMCH LIKE '%' || :SHPMCH || '%'";
			$bind ['SHPMCH'] = $filter ['searchParams']["SEARCHKEY"];
		}
		
		$sql .= Common_Tool::createFilterSql("CC_BHGPSPXX",$filter['filterParams'],$bind);
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.SHPBH";
		
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
	 * 获取采购退货单编号
	 */
		public function getcgthData($filter) {
		//排序用字段名
		$fields = array ("", "CGTHDBH", "KPRQ", "NLSSORT(BMMCH,'NLS_SORT=SCHINESE_PINYIN_M')",
						 "NLSSORT(YGXM,'NLS_SORT=SCHINESE_PINYIN_M')", 
						 "NLSSORT(DWMCH,'NLS_SORT=SCHINESE_PINYIN_M')",
						 "DHHM","DIZHI","BEIZHU","SHHZHT","THDZHT","THLX");

		//检索SQL
		$sql = "SELECT CGTHDBH,TO_CHAR(KPRQ,'YYYY-MM-DD'),BMMCH,YWYXM,DWMCH,"
				."DHHM,DIZHI,BEIZHU,DECODE(SHHZHT,'0','未审核','1','审核通过','2','审核未通过') AS SHHZHT,"
				."DECODE(THDZHT,'0','未出库','已出库') AS THDZHT,DECODE(THLX,'1','合格品退货','2','不合格品退货') AS THLX "
				."FROM H01VIEW012308 WHERE QYBH = :QYBH AND THDZHT =1 AND THLX = 2 ";
		
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
		$sql .= Common_Tool::createFilterSql("CG_CGTHCX_DJ",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",CGTHDBH";
		
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
	 * //保存不合格品处理信息
	 */
	public function saveMain($cldbh) {
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['BHGPCHLDBH'] = $cldbh; //不合格品处理
		$data ['RKDBH'] = $_POST ['RKDBH']; //入库单编号
		$data ['CKBH'] = $_SESSION['auth']->ckbh; //仓库编号
		$data ['SHPBH'] = $_POST ['SHPBH']; //商品编号
		$data ['PIHAO'] = $_POST ['PIHAO']; //批号
		$data ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['SHCHRQ']. "','YYYY-MM-DD')" ); //生产日期
		$data ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['BZHQZH']. "','YYYY-MM-DD')" ); //保质期至
		$data ['SHULIANG'] = $_POST['SHULIANG'];//数量
		$data ['HSHJ'] = (float)$_POST ['HSHJ']; //含税价
		$data ['HSHJE'] = (float)$_POST ['HSHJE']; //含税金额
		$data ['BEIZHU'] = $_POST ['BEIZHU']; //备注	
		$data ['CHLFF'] = $_POST ['CHLFF']; //处理方式	
		if($data ['CHLFF'] == '1'){
			$data ['CGTHDBH'] = $_POST ['CGTHDBH']; //退货单编号
		}
		
		$data ['CHLR'] = $_POST ['CHLR']; //处理人
		$data ['CHLYJ'] = $_POST ['CHLYJ']; //处理意见
		$data ['SHFBQSH'] = isset($_POST ['SHFBQSH'])? '1' : '0'; //是否被签收
		$data ['SHFYFYF'] = isset($_POST ['SHFYFYF'])? '1' : '0'; //是否预付运费
		$data ['YFYFJE'] = (float)$_POST ['YFYFJE']; //预付金额
		$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		return $this->_db->insert ( "H01DB012464", $data );
	}
	
	
}