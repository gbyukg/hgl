<?php
/**********************************************************
 * 模块：    销售模块(XS)
 * 机能：    销售综合查询(XSZHCX)
 * 作成者：刘枞
 * 作成日：2011/12/13
 * 更新履历：
 **********************************************************/
class xs_models_xszhcx extends Common_Model_Base{
	/**
	 * 销售订单信息列表
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ("","A.XSHDBH","B.KPRQ","A.SHPBH","A.SHPMCH","B.DWBH","B.DWMCH","A.PIHAO",
						"A.SHCHRQ","A.BZHQZH","A.BZHSHL","A.LSSHL","A.SHULIANG","A.DANJIA","A.HSHJ",
						"A.KOULV","A.JINE","A.HSHJE","A.SHUIE","A.BEIZHU");
		//检索SQL
		$sql = "SELECT A.XSHDBH,B.KPRQ,A.SHPBH,A.SHPMCH,B.DWBH,B.DWMCH,A.PIHAO,A.SHCHRQ,A.BZHQZH,A.BZHSHL,A.LSSHL,".
				"A.SHULIANG,A.DANJIA,A.HSHJ,A.KOULV,A.JINE,A.HSHJE,A.SHUIE,A.BEIZHU ".
				"FROM H01VIEW012202 A ".
				"LEFT JOIN H01VIEW012201 B ON A.QYBH = B.QYBH AND A.XSHDBH = B.XSHDBH ".
				"WHERE A.QYBH = :QYBH AND B.QXBZH != 'X' AND B.SHHZHT != '2' AND B.SHHZHT != '3' ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查询条件
		if ($filter ["ksrq"] != "" || $filter ["zzrq"] != "")
		{
			$sql .= " AND :KSRQ <= TO_CHAR(B.KPRQ,'YYYY-MM-DD') AND TO_CHAR(B.KPRQ,'YYYY-MM-DD') <= :ZZRQ ";
			$bind ['KSRQ'] = $filter ["ksrq"] == ""?"1900-01-01":$filter ["ksrq"];
			$bind ['ZZRQ'] = $filter ["zzrq"] == ""?"9999-12-31":$filter ["zzrq"];
		}
		
		if ($filter ["dwbh"] != "") {
			$sql .= " AND ( B.DWBH LIKE '%' || :DWBH || '%')";
			$bind ['DWBH'] = $filter ["dwbh"];
		}
		
		if ($filter ["shpbh"] != "") {
			$sql .= " AND ( A.SHPBH LIKE '%' || :SHPBH || '%')";
			$bind ['SHPBH'] = $filter ["shpbh"];
		}
		
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("XS_XSDDWH_DJ",$filter['filterParams'],$bind);;
			
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",XSHDBH";

		$recs = $this->_db->fetchAll($sql,$bind);
		
		return Common_Tool::createXml( $recs, true );
	}
	

	/**
	 * 销售订单明细信息列表
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getMingxiGridData($filter){
		//检索SQL
		$sql = "SELECT A.SHPBH,B.SHPMCH,B.GUIGE,C.NEIRONG,B.JLGG,A.BZHSHL,A.LSSHL,A.SHULIANG,".
		 " A.DANJIA,A.HSHJ,A.KOULV,A.JINE,A.HSHJE,B.SHUILV,A.SHUIE,B.CHANDI,A.BEIZHU ". 
		 " FROM H01DB012202 A " .
	     " LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH " .
		 " LEFT JOIN H01DB012001 C ON B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' " .
		 " WHERE A.QYBH = :QYBH " .
		 " AND A.XSHDBH = :XSHDBH ";
		 
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['XSHDBH'] = $filter ['bh'];

		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $bind );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true, $totalCount, $filter ["posStart"] );
	}
	
	
	/*
	 * 销售订单明细信息
	 */
	public function getmingxi($filter) {
		//检索SQL
		$sql = "SELECT "
				."T1.SHPBH,"      		  //商品编号
				."T3.SHPMCH,"     		  //商品名称
				."T3.GUIGE,"      		  //规格
				."T4.NEIRONG AS BZHDWM,"  //包装单位
				."T1.PIHAO,"      		  //批号
				."TO_CHAR(T1.SHCHRQ,'yyyy-mm-dd') AS SHCHRQ,"   //生产日期
				."TO_CHAR(T1.BZHQZH,'yyyy-mm') AS BZHQZH,"   //保质期至
				."T1.BZHSHL,"     		  //包装数量
				."T1.LSSHL,"      		  //零散数量
				."T1.SHULIANG,"  		  //数量
				."T1.DANJIA,"  		      //单价
				."T1.HSHJ,"  		      //含税价
				."T1.KOULV,"  		      //扣率
				."T3.SHUILV,"  		      //税率
				."T1.HSHJE,"  		      //含税金额
				."T1.JINE,"  		      //金额
				."T1.SHUIE,"  		      //税额
				."T3.LSHJ,"     		  //零售价
				."T3.ZGSHJ,"     		  //最高售价
				."T3.SHPTM,"     		  //商品条码
				."T3.FLBM,"     		  //分类编码
				."T3.PZHWH,"     		  //批准文号
				."T5.NEIRONG AS JIXING,"  //剂型
				."T3.SHCHCHJ,"     		  //生产厂家
				."T3.CHANDI,"     		  //产地
				."T3.SHFOTC "     		  //是否OTC
			  ."FROM H01DB012202 T1 "
			  ."LEFT JOIN H01DB012201 T2 ON T1.QYBH = T2.QYBH AND T1.XSHDBH = T2.XSHDBH "
			  ."LEFT JOIN H01DB012101 T3 ON T1.QYBH = T3.QYBH AND T1.SHPBH = T3.SHPBH "
			  ."LEFT JOIN H01DB012001 T4 ON T1.QYBH = T4.QYBH AND T3.BZHDWBH = T4.ZIHAOMA AND T4.CHLID = 'DW'"
			  ."LEFT JOIN H01DB012001 T5 ON T1.QYBH = T5.QYBH AND T3.JIXING = T5.ZIHAOMA AND T5.CHLID = 'JX'"
			  ."WHERE T1.QYBH = :QYBH "
			  ."AND T1.XSHDBH = :XSHDBH "
			  ."ORDER BY T1.XUHAO ";

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;      //区域编号
		$bind ['XSHDBH'] = $filter ['bh'];              //单据编号
		
		return $this->_db->fetchAll( $sql, $bind );
	}
	

	public function getDanweiInfo($filter) {
		//检索SQL
		$sql =  " SELECT A.DWBH,A.DWMCH,A.DIZHI,A.DHHM,A.KOULV,A.FHQBH" . 
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
	
	
	/**
	 * 获取商品的名称和规格等必要数据
	 */
	function getSpxx($spbh){
		$sql ="SELECT  A.SHPMCH,A.CHANDI,B.NEIRONG,A.LSHJ,A.GUIGE,A.SHPTM,A.JLGG ".
		      "FROM H01DB012101 A left join H01DB012001 B ".
		      "ON A.QYBH = B.QYBH and B.ZIHAOMA = A.BZHDWBH AND B.CHLID='DW' ".
		      "WHERE A.QYBH = :QYBH AND A.SHPBH =:SHPBH ";
		
		$bind = array( 'QYBH' => $_SESSION ['auth']->qybh, 'SHPBH' => $spbh );
		
		$Spxx = $this->_db->fetchRow( $sql, $bind );
		
		return $Spxx;     
	}
	
}
