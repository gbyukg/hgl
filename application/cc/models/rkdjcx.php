<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    入库单据查询(RKDJCX)
 * 作成者：苏迅
 * 作成日：2010/12/27
 * 更新履历：
 *********************************/
class cc_models_rkdjcx extends Common_Model_Base {
	/**
	 * 得到入库单据列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ("", "RKDBH", "RKLX", "NLSSORT(DWMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "KPRQ", "NLSSORT(YWYXM,'NLS_SORT=SCHINESE_PINYIN_M')", "NLSSORT(BMMCH,'NLS_SORT=SCHINESE_PINYIN_M')","BGRQ","NLSSORT(BGZHXM,'NLS_SORT=SCHINESE_PINYIN_M')");
		
		//检索SQL
		$sql = "SELECT RKDBH,DECODE(RKLX,'1','采购入库','2','退货入库','3','直接入库','') AS RKLXM,DWMCH,TO_CHAR(KPRQ,'YYYY-MM-DD'),YWYXM,BMMCH,TO_CHAR(BGRQ,'YYYY-MM-DD'),BGZHXM,RKLX"
			 . " FROM H01VIEW012406"
			 . " WHERE QYBH = :QYBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter ['searchParams']["KSRQKEY"] != "" || $filter ['searchParams']["ZZRQKEY"] != "")
		{
			$sql .= " AND :KSRQKEY <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :ZZRQKEY";
			$bind ['KSRQKEY'] = $filter ['searchParams']["KSRQKEY"] == ""?"1900-01-01":$filter ['searchParams']["KSRQKEY"];
			$bind ['ZZRQKEY'] = $filter ['searchParams']["ZZRQKEY"] == ""?"9999-12-31":$filter ['searchParams']["ZZRQKEY"];
		}
		
		//查询条件(单位编号)
		if ($filter ['searchParams']["DWBHKEY"] != "") {
			$sql .= " AND DWBH = :DWBHKEY";
			$bind ['DWBHKEY'] = $filter ['searchParams']["DWBHKEY"];
		}
		
		//查询条件(入库单据号)
		if($filter ['searchParams']["RKDBHKEY"] != "") {
			$sql .= " AND RKDBH LIKE '%' || :RKDBHKEY || '%'";
			$bind ['RKDBHKEY'] = $filter ['searchParams']["RKDBHKEY"];
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_RKDXX",$filter['filterParams'],$bind);
		
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
	function getRkdjxx($rkdbh, $filter=null, $flg = 'current') {
		//排序用字段名
		$fields = array ("", "RKDBH", "RKLX", "NLSSORT(DWMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "KPRQ", "NLSSORT(YWYXM,'NLS_SORT=SCHINESE_PINYIN_M')", "NLSSORT(BMMCH,'NLS_SORT=SCHINESE_PINYIN_M')","BGRQ","NLSSORT(BGZHXM,'NLS_SORT=SCHINESE_PINYIN_M')");
		
		//检索SQL--取上下条关系
		$sql_list = "SELECT RKDBH, ROWID, LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",RKDBH) AS NEXTROWID," 
			      . "LAG(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",RKDBH) AS PREVROWID"  
			 	  . " FROM H01VIEW012406"
			      . " WHERE QYBH = :QYBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter ['searchParams']["KSRQKEY"] != "" || $filter ['searchParams']["ZZRQKEY"] != "")
		{
			$sql_list .= " AND :KSRQKEY <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :ZZRQKEY";
			$bind ['KSRQKEY'] = $filter ['searchParams']["KSRQKEY"] == ""?"1900-01-01":$filter ['searchParams']["KSRQKEY"];
			$bind ['ZZRQKEY'] = $filter ['searchParams']["ZZRQKEY"] == ""?"9999-12-31":$filter ['searchParams']["ZZRQKEY"];
		}
		
		//查询条件(单位编号)
		if ($filter ['searchParams']["DWBHKEY"] != "") {
			$sql_list .= " AND DWBH = :DWBHKEY";
			$bind ['DWBHKEY'] = $filter ['searchParams']["DWBHKEY"];
		}
		
		//查询条件(入库单据号)
		if($filter ['searchParams']["RKDBHKEY"] != "") {
			$sql_list .= " AND RKDBH LIKE '%' || :RKDBHKEY || '%'";
			$bind ['RKDBHKEY'] = $filter ['searchParams']["RKDBHKEY"];
		}
		
		//自动生成精确查询用Sql
		$sql_list .= Common_Tool::createFilterSql("CC_RKDXX",$filter['filterParams'],$bind);
		
		//检索SQL--入库单信息H01DB012406
		$sql_single = "SELECT RKDBH,CKDBH,TO_CHAR(KPRQ,'yyyy-mm-dd') AS KPRQ,BMMCH,YWYXM,DWBH,DWMCH,DIZHI,DHHM,SHFZZHSH,HGL_DEC(KOULV) AS KOULV,BEIZHU,RKLX,BMMCH AS CGBMMCH,CGYXM" 
		            . " FROM H01VIEW012406";
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH = :QYBH AND RKDBH =:RKDBH";
			//绑定数组数超过需要绑定数，检索不出来
			unset ( $bind ['KSRQKEY'] );
			unset ( $bind ['ZZRQKEY'] );
			unset ( $bind ['DWBHKEY'] );
			unset ( $bind ['RKDBHKEY'] );
		} else if ($flg == 'next') {
			$sql_single .= " WHERE ROWID =  (SELECT NEXTROWID FROM  (SELECT NEXTROWID,RKDBH FROM ( $sql_list ) WHERE RKDBH = :RKDBH))";
		} else if ($flg == 'prev') {
			$sql_single .= " WHERE ROWID =  (SELECT PREVROWID FROM  (SELECT PREVROWID,RKDBH FROM ( $sql_list ) WHERE RKDBH = :RKDBH))";
		}
		//绑定查询条件
		$bind ['RKDBH'] = $rkdbh;
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	
	/**
	 * 得到入库单据明细xml数据
	 *
	 * @param string $rkdbh 入库单编号
	 * @return string xml
	 */
	public function getMxXmlData($rkdbh) {		
		//检索SQL		
	     $sql = "SELECT SHPBH,SHPMCH,GUIGE,PIHAO,CKMCH ||' '|| KQMCH ||' '|| KWMCH,BZHDWMCH,TO_CHAR(SHCHRQ,'yyyy-mm-dd'),TO_CHAR(BZHQZH,'yyyy-mm-dd'),BZHSHL,LSSHL,SHULIANG,HGL_DEC(DANJIA),HGL_DEC(HSHJ),HGL_DEC(KOULV),HGL_DEC(SHUILV),HGL_DEC(JINE),HGL_DEC(HSHJE),HGL_DEC(SHUIE),HGL_DEC(LSHJ),CHANDI,BEIZHU" 
		     . " FROM H01VIEW012407"
		     . " WHERE QYBH = :QYBH AND RKDBH = :RKDBH";
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['RKDBH'] = $rkdbh;
				
		//排序
		$sql .= " ORDER BY XUHAO";
		
		$recs = $this->_db->fetchAll ( $sql, $bind );
		
		//调用表格xml生成函数
 		return Common_Tool::createXml ( $recs, true );
				
	}
}
	
	
	