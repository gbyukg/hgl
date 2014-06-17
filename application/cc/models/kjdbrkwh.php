<?php
/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：       库间调拨入库维护(kjdbrkwh)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/01/25
 ***** 更新履历：
 ******************************************************************/

class cc_models_kjdbrkwh extends Common_Model_Base {

	/**
	 * 得到退货单列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridDanjuData($filter){
		//排序用字段名
		$fields = array ("", "A.DJBH", "DCCK", "DRCK", "KPRQ", "A.DYDBCHKD",
						 "NLSSORT(D.YGXM,'NLS_SORT=SCHINESE_PINYIN_M')",
						 "NLSSORT(E.BMMCH,'NLS_SORT=SCHINESE_PINYIN_M')",
						 "BGRQ", "A.BGZH");

		//检索SQL
		$sql = "SELECT A.DJBH,B.CKMCH AS DCCK,C.CKMCH AS DRCK,TO_CHAR(A.KPRQ,'YYYY-MM-DD') AS KPRQ,"
				."A.DYDBCHKD,D.YGXM,E.BMMCH,TO_CHAR(A.BGRQ,'YYYY-MM-DD') AS BGRQ,A.BGZH "
				."FROM H01DB012412 A "
				."LEFT JOIN H01DB012401 B ON A.QYBH = B.QYBH AND A.DCHCK = B.CKBH "
				."LEFT JOIN H01DB012401 C ON A.QYBH = B.QYBH AND A.DRCK = C.CKBH "
				."LEFT JOIN H01DB012113 D ON A.QYBH = D.QYBH AND A.YWYBH = D.YGBH "
				."LEFT JOIN H01DB012112 E ON A.QYBH = E.QYBH AND A.BMBH = E.BMBH "
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

		//查询条件(调拨入库单编号)
		if ($filter ["dbrkd"] != "") {
			$sql .= " AND A.DJBH LIKE '%' || :DJBH || '%' ";
			$bind ['DJBH'] = $filter ["dbrkd"];
		}
		
		//查询条件(调拨出库单编号)
		if ($filter ["dbckd"] != "") {
			$sql .= " AND A.DYDBCHKD LIKE '%' || :DYDBCHKD || '%' ";
			$bind ['DYDBCHKD'] = $filter ["dbckd"];
		}
		
		//查询条件(调拨出库单编号)
		if ($filter ["dcck"] != "") {
			$sql .= " AND A.DCHCK = :DCHCK ";
			$bind ['DCHCK'] = $filter ["dcck"];
		}
		
		//查询条件(调拨出库单编号)
		if ($filter ["drck"] != "") {
			$sql .= " AND A.DRCK = :DRCK ";
			$bind ['DRCK'] = $filter ["drck"];
		}
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.DJBH";
		
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
	 * 库间调拨出库单选择页面--单据列表数据获取
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridthdData($filter){
		//排序用字段名
		$fields = array ("", "A.DJBH", "DCCK", "DRCK", "KPRQ", "A.DRCKDZH", "A.DHHM",
						 "NLSSORT(D.YGXM,'NLS_SORT=SCHINESE_PINYIN_M')",
						 "NLSSORT(E.BMMCH,'NLS_SORT=SCHINESE_PINYIN_M')",
						 "BGRQ", "A.BGZH");
		
		//检索SQL
		$sql = "SELECT A.DJBH,B.CKMCH AS DCCK,C.CKMCH AS DRCK,TO_CHAR(A.KPRQ,'YYYY-MM-DD') AS KPRQ,"
				."A.DRCKDZH,A.DHHM,D.YGXM,E.BMMCH,TO_CHAR(A.BGRQ,'YYYY-MM-DD') AS BGRQ,A.BGZH "
				."FROM H01DB012410 A "
				."LEFT JOIN H01DB012401 B ON A.QYBH = B.QYBH AND A.DCHCK = B.CKBH "
				."LEFT JOIN H01DB012401 C ON A.QYBH = B.QYBH AND A.DRCK = C.CKBH "
				."LEFT JOIN H01DB012113 D ON A.QYBH = D.QYBH AND A.YWYBH = D.YGBH "
				."LEFT JOIN H01DB012112 E ON A.QYBH = E.QYBH AND A.BMBH = E.BMBH "
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
		
		if ($filter ["dcck"] != "") {
			$sql .= " AND A.DCHCK = :DCHCK";
			$bind ['DCHCK'] = $filter ["dcck"];
		}
		
		if($filter ["drck"] != "") {
			$sql .= " AND A.DRCK = :DRCK ";
			$bind ['DRCK'] = $filter ["drck"];
		}
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.DJBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true,$totalCount, $filter ["posStart"] );
	}
}