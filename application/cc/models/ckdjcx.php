<?php
/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：       出库单据查询(ckdjcx)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2010/12/22
 ***** 更新履历：
 ******************************************************************/

class cc_models_ckdjcx extends Common_Model_Base {

	/**
	 * 得到退货单列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridDanjuData($filter){
		//排序用字段名
		$fields = array ("", "CHKDBH", "CHKLXM", "DWBH", "NLSSORT(DWMCH,'NLS_SORT=SCHINESE_PINYIN_M')",
						 "KPRQ", "NLSSORT(YWYXM,'NLS_SORT=SCHINESE_PINYIN_M')",
						 "NLSSORT(BMMCH,'NLS_SORT=SCHINESE_PINYIN_M')");
		
		//检索SQL
		$sql = "SELECT CHKDBH,DECODE(CHKLX,'1','销售出库','2','退货出库','3','直接出库','4','调拨出库') AS CHKLXM,"
				."DWBH,DWMCH,TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ,YWYXM,BMMCH,"
				."TO_CHAR(BGRQ,'YYYY-MM-DD'),BGZHXM,CHKLX FROM H01VIEW012408 "
				."WHERE QYBH = :QYBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter ["ksrqkey"] != "" || $filter ["zzrqkey"] != "")
		{
			$sql .= " AND :KSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :ZZRQ";
			$bind ['KSRQ'] = $filter ["ksrqkey"] == ""?"1900-01-01":$filter ["ksrqkey"];
			$bind ['ZZRQ'] = $filter ["zzrqkey"] == ""?"9999-12-31":$filter ["zzrqkey"];
		}
		
		//查询条件(出库单编号)
		if ($filter ["ckdkey"] != "") {
			$sql .= " AND CHKDBH LIKE '%' || :CHKDBH || '%' ";
			$bind ['CHKDBH'] = $filter ["ckdkey"];
		}
		
		//查询条件(单位)
		if ($filter ["dwkey"] != "") {
			$sql .= " AND (DWMCH LIKE '%' || :DWKEY || '%' OR DWBH LIKE '%' || :DWKEY || '%')";
			$bind ['DWKEY'] = $filter ["dwkey"];
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_CKDJCX",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",CHKDBH,CHKLX";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true,$totalCount, $filter ["posStart"] );
	}
	
}