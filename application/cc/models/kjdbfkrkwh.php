<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    库间调拨返库入库维护(KJDBFKRKWH)
 * 作成者：苏迅
 * 作成日：2010/1/30
 * 更新履历：
 *********************************/
class cc_models_kjdbfkrkwh extends Common_Model_Base {
	
    /*
	 * 仓库自动完成数据取得
	 */
	public function getAutocompleteData($filter){
		
		//检索SQL
		$sql = "SELECT CKBH,CKMCH" . 
		       " FROM H01DB012401" .
		       " WHERE QYBH = :QYBH ";//区域编号
		
		//快速查找条件
		if ($filter ["searchkey"] != "") {
			$bind ['SEARCHKEY'] = strtolower($filter ["searchkey"]);
			$sql .= " AND (lower(CKBH) LIKE '%' || :SEARCHKEY || '%' OR  lower(CKMCH) LIKE '%' || :SEARCHKEY || '%')";
		}
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		return $this->_db->fetchAll($sql,$bind);
	}
	
	/**
	 * 得到入库单据列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ("", "DJBH", "NLSSORT(DCHCKMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "NLSSORT(DRCKMCH,'NLS_SORT=SCHINESE_PINYIN_M')","NLSSORT(DRCKDZH,'NLS_SORT=SCHINESE_PINYIN_M')",
		 "KPRQ", "NLSSORT(YWYXM,'NLS_SORT=SCHINESE_PINYIN_M')", "NLSSORT(BMMCH,'NLS_SORT=SCHINESE_PINYIN_M')","BGRQ","NLSSORT(BGZHXM,'NLS_SORT=SCHINESE_PINYIN_M')","DYDBFKD","DYDBCHKD");
		
		//检索SQL
		$sql = "SELECT DJBH,DCHCKMCH,DRCKMCH,DRCKDZH,TO_CHAR(KPRQ,'YYYY-MM-DD'),YWYXM,BMMCH,TO_CHAR(BGRQ,'YYYY-MM-DD'),BGZHXM,DYDBFKD,DYDBCHKD"
			 . " FROM H01VIEW012425 WHERE QYBH = :QYBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter ['searchParams']["KSRQKEY"] != "" || $filter ['searchParams']["ZZRQKEY"] != "")
		{
			$sql .= " AND :KSRQKEY <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :ZZRQKEY";
			$bind ['KSRQKEY'] = $filter ['searchParams']["KSRQKEY"] == ""?"1900-01-01":$filter ['searchParams']["KSRQKEY"];
			$bind ['ZZRQKEY'] = $filter ['searchParams']["ZZRQKEY"] == ""?"9999-12-31":$filter ['searchParams']["ZZRQKEY"];
		}
		
		//查询条件(调出仓库)
		if ($filter ['searchParams']["DCCKKEY"] != "") {
			$sql .= " AND DCHCK = :DCHCK";
			$bind ['DCHCK'] = $filter ['searchParams']["DCCKKEY"];
		}
		
		//查询条件(调拨返库入库单号)
		if($filter ['searchParams']["DBFKRKDKEY"] != "") {
			$sql .= " AND DJBH LIKE '%' || :DJBH || '%'";
			$bind ['DJBH'] = $filter ['searchParams']["DBFKRKDKEY"];
		}
		
		//查询条件(对应调拨返库单)
		if ($filter ['searchParams']["DYDBFKDKEY"] != "") {
			$sql .= " AND DYDBFKD = :DYDBFKD";
			$bind ['DYDBFKD'] = $filter ['searchParams']["DYDBFKDKEY"];
		}
		
		//查询条件(对应调拨出库单)
		if($filter ['searchParams']["DYDBCHKDKEY"] != "") {
			$sql .= " AND DYDBCHKD = :DYDBCHKD";
			$bind ['DYDBCHKD'] = $filter ['searchParams']["DYDBCHKDKEY"];
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_KJDBFKRK",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",DJBH DESC";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true, $totalCount, $filter ["posStart"] );
			
	}
	
	/**
	 * 取得入库单据信息
	 *
	 * @param string $rkdbh 入库单编号
	 * @param array $filter 查询排序条件
	 * @param string $flg 查找方向  current,next,prev
	 * @return array 
	 */
	function getRkdjxx($djbh, $filter=null, $flg = 'current') {
		//排序用字段名
		$fields = array ("", "DJBH", "NLSSORT(DCHCKMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "NLSSORT(DRCKMCH,'NLS_SORT=SCHINESE_PINYIN_M')","NLSSORT(DRCKDZH,'NLS_SORT=SCHINESE_PINYIN_M')",
		 "KPRQ", "NLSSORT(YWYXM,'NLS_SORT=SCHINESE_PINYIN_M')", "NLSSORT(BMMCH,'NLS_SORT=SCHINESE_PINYIN_M')","BGRQ","NLSSORT(BGZHXM,'NLS_SORT=SCHINESE_PINYIN_M')","DYDBFKD","DYDBCHKD");
				
		//检索SQL--取上下条关系
		$sql_list = "SELECT DJBH, ROWID, LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",DJBH) AS NEXTROWID," 
			      . "LAG(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",DJBH) AS PREVROWID"  
			 	  . " FROM H01VIEW012425 WHERE QYBH = :QYBH";
			      		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter ['searchParams']["KSRQKEY"] != "" || $filter ['searchParams']["ZZRQKEY"] != "")
		{
			$sql_list .= " AND :KSRQKEY <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :ZZRQKEY";
			$bind ['KSRQKEY'] = $filter ['searchParams']["KSRQKEY"] == ""?"1900-01-01":$filter ['searchParams']["KSRQKEY"];
			$bind ['ZZRQKEY'] = $filter ['searchParams']["ZZRQKEY"] == ""?"9999-12-31":$filter ['searchParams']["ZZRQKEY"];
		}
		
		//查询条件(调出仓库)
		if ($filter ['searchParams']["DCCKKEY"] != "") {
			$sql_list .= " AND DCHCK = :DCHCK";
			$bind ['DCHCK'] = $filter ['searchParams']["DCCKKEY"];
		}
		
		//查询条件(调拨返库入库单号)
		if($filter ['searchParams']["DBFKRKDKEY"] != "") {
			$sql_list .= " AND DJBH LIKE '%' || :DJBH || '%'";
			$bind ['DJBH'] = $filter ['searchParams']["DBFKRKDKEY"];
		}
		
		//查询条件(对应调拨返库单)
		if ($filter ['searchParams']["DYDBFKDKEY"] != "") {
			$sql_list .= " AND DYDBFKD = :DYDBFKD";
			$bind ['DYDBFKD'] = $filter ['searchParams']["DYDBFKDKEY"];
		}
		
		//查询条件(对应调拨出库单)
		if($filter ['searchParams']["DYDBCHKDKEY"] != "") {
			$sql_list .= " AND DYDBCHKD = :DYDBCHKD";
			$bind ['DYDBCHKD'] = $filter ['searchParams']["DYDBCHKDKEY"];
		}
		
		//自动生成精确查询用Sql
		$sql_list .= Common_Tool::createFilterSql("CC_KJDBFKRK",$filter['filterParams'],$bind);
			 	  		
		//检索SQL--入库单信息H01DB012425
		$sql_single = "SELECT DJBH,TO_CHAR(KPRQ,'yyyy-mm-dd') AS KPRQ,DCHCKMCH,DRCKMCH,BMMCH,YWYXM,DRCKDZH,SHFPS,DHHM,BEIZHU,DYDBFKD,DYDBCHKD" 
		            . " FROM H01VIEW012425";
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH = :QYBH AND DJBH =:DJBH";
			//绑定数组数超过需要绑定数，检索不出来
			unset ( $bind ['KSRQKEY'] );
			unset ( $bind ['ZZRQKEY'] );
			unset ( $bind ['DCHCK'] );
			unset ( $bind ['DJBH'] );
			unset ( $bind ['DYDBFKD'] );
			unset ( $bind ['DYDBCHKD'] );
		} else if ($flg == 'next') {
			$sql_single .= " WHERE ROWID =  (SELECT NEXTROWID FROM  (SELECT NEXTROWID,DJBH FROM ( $sql_list ) WHERE DJBH = :DJBH))";
		} else if ($flg == 'prev') {
			$sql_single .= " WHERE ROWID =  (SELECT PREVROWID FROM  (SELECT PREVROWID,DJBH FROM ( $sql_list ) WHERE DJBH = :DJBH))";
		}
		//绑定查询条件
		$bind ['DJBH'] = $djbh;
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	
	/**
	 * 得到入库单据明细xml数据
	 *
	 * @param string $rkdbh 入库单编号
	 * @return string xml
	 */
	public function getMxXmlData($djbh) {		
		//检索SQL
		$sql = "SELECT SHPBH,SHPMCH,GUIGE,PIHAO,BZHDWMCH, DRKQMCH ||' '|| DRKWMCH,TO_CHAR(SHCHRQ,'yyyy-mm-dd'),"
			 . "TO_CHAR(BZHQZH,'yyyy-mm-dd'),JLGG,BZHSHL,LSSHL,SHULIANG,CHANDI,BEIZHU" 
		     . " FROM H01VIEW012426 WHERE QYBH = :QYBH AND DJBH = :DJBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DJBH'] = $djbh;
				
		//排序
		$sql .= " ORDER BY XUHAO";
		
		$recs = $this->_db->fetchAll ( $sql, $bind );
		
		//调用表格xml生成函数
 		return Common_Tool::createXml ( $recs, true );
				
	}
}
	
	
	