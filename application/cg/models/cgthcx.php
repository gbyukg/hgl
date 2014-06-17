<?php
/******************************************************************
 ***** 模         块：       采购模块(CG)
 ***** 机         能：       采购退货查询(cgthcx)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/01/07
 ***** 更新履历：
 ******************************************************************/

class cg_models_cgthcx extends Common_Model_Base {

	/**
	 * 得到单据列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridDanjuData($filter) {
		//排序用字段名
		$fields = array ("", "CGTHDBH", "KPRQ", "NLSSORT(BMMCH,'NLS_SORT=SCHINESE_PINYIN_M')",
						 "NLSSORT(YGXM,'NLS_SORT=SCHINESE_PINYIN_M')", 
						 "NLSSORT(DWMCH,'NLS_SORT=SCHINESE_PINYIN_M')",
						 "DHHM","DIZHI","BEIZHU","SHHZHT","THDZHT","THLX");

		//检索SQL
		$sql = "SELECT CGTHDBH,TO_CHAR(KPRQ,'YYYY-MM-DD'),BMMCH,YWYXM,DWMCH,"
				."DHHM,DIZHI,BEIZHU,DECODE(SHHZHT,'0','未审核','1','审核通过','2','审核未通过') AS SHHZHT,"
				."DECODE(THDZHT,'0','未出库','已出库') AS THDZHT,DECODE(THLX,'1','合格品退货','2','不合格品退货') AS THLX "
				."FROM H01VIEW012308 WHERE QYBH = :QYBH ";
		
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
	
	
	/**
	 * 得到单据明细列表数据
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
				."TO_CHAR(A.BZHQZH,'YYYY-MM') AS BZHQZH,"        //保质期至
//				."A.BZHSHL,"     		  //包装数量
//				."A.LSSHL,"      		  //零散数量
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
			  ."FROM H01DB012309 A "
			  ."LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "  
			  ."LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' "
			  ."WHERE A.QYBH = :QYBH "       //区域编号
			  ."AND A.CGTHDBH = :CGTHDBH ";      //入库单编号 
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGTHDBH'] = $filter ["bh"];
				
		//排序
		$sql .= " ORDER BY " . $fields [$filter["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.CGTHDBH,A.XUHAO";
		
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

}
