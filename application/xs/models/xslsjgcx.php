<?php
/*********************************
 * 模块：    销售模块(xs)
 * 机能：    销售历史价格查询
 * 作成者：周义
 * 作成日：2011/01/22
 * 更新履历：
 *********************************/
class xs_models_xslsjgcx extends Common_Model_Base {
	/*
	 * 列表数据取得(xml格式)
	 */
	function getListData($filter) {
		//排序用字段名
		$fields = array ("", "T2.KPRQ", "T1.XSHDBH", "T2.DWBH","NLSSORT(T2.DWMCH,'NLS_SORT = SCHINESE_PINYIN_M'",
		                 "T1.SHPBH","NLSSORT(T2.SHPMCH,'NLS_SORT = SCHINESE_PINYIN_M'","T1.DANJIA","T1.HSHJ",
		                 "NLSSORT(T2.YWYXM,'NLS_SORT = SCHINESE_PINYIN_M'","NLSSORT(T2.KPYXM,'NLS_SORT = SCHINESE_PINYIN_M'");
		//检索SQL
		$sql = "SELECT TO_CHAR(T2.KPRQ,'YYYY-MM-DD'),T1.XSHDBH,T2.DWBH,T2.DWMCH,T1.SHPBH,T1.SHPMCH,T1.DANJIA,T1.HSHJ,T2.YWYXM,T2.KPYXM 
                FROM H01VIEW012202 T1 
                JOIN H01VIEW012201 T2 ON T1.QYBH = T2.QYBH AND T1.XSHDBH = T2.XSHDBH
                WHERE T1.QYBH = :QYBH ";
		
		//单位编号
		if($filter['filterParams']['DWBH']!=""){
			$sql .=" AND T2.DWBH = :DWBH";
			$bind ['DWBH'] = $filter['filterParams']['DWBH'];
		}
		//商品编号
		if($filter['filterParams']['SHPBH']!=""){
			$sql .=" AND T1.SHPBH = :SHPBH";
			$bind ['SHPBH'] = $filter['filterParams']['SHPBH'];
		}
		
		//开始日期
		if($filter['filterParams']['BEGINDATE']!=""){
			$sql .=" AND T2.KPRQ >= TO_DATE(:BEGINDATE,'YYYY-MM-DD')";
			$bind ['BEGINDATE'] = $filter['filterParams']['BEGINDATE'];
		}
		//终止日期
		if($filter['filterParams']['ENDDATE']!=""){
			$sql .=" AND T2.KPRQ <= TO_DATE(:ENDDATE,'YYYY-MM-DD')";
			$bind ['ENDDATE'] = $filter['filterParams']['ENDDATE'];
		}
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		$sql .= " ,T2.KPRQ,T1.XSHDBH,T2.DWBH,T2.DWMCH,T1.SHPBH,T1.SHPMCH,T1.DANJIA,T1.HSHJ";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		return Common_Tool::createXml ( $recs, true, $totalCount, $filter ["posStart"] );
	}

}
