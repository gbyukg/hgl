<?php
/******************************************************************
 ***** 模         块：       基础模块(JC)
 ***** 机         能：       客户资料过期预警(KHZLGQYJ)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/08/12
 ***** 更新履历：
 ******************************************************************/

class jc_models_khzlgqyj extends Common_Model_Base {

	/**
	 * 得到列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridDanjuData($filter){
		//排序用字段名
		$fields = array ( "", "A.DWBH", "NLSSORT(A.DWMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "NLSSORT(DIZHI,'NLS_SORT=SCHINESE_PINYIN_M')" );
		
		//检索SQL
		$sql = "SELECT A.DWBH, A.DWMCH, C.SHENGMCH || B.SHIMCH || A.DIZHI AS DIZHI, A.DHHM, A.LXRXM, ".
				"DECODE(A.YXKZH,'0','无许可证','1','有许可证') AS YXKZH, A.XKZHH, TO_CHAR(A.XKZHYXQ,'YYYY-MM-DD'), ".
				"DECODE(A.SHFYYYZHZH,'0','无营业执照','1','有营业执照') AS SHFYYYZHZH, A.YYZHZHH, TO_CHAR(A.YYZHZHYXQ,'YYYY-MM-DD') ".
				"FROM H01DB012106 A ".
				"LEFT JOIN H01DB012115 B ON B.SZSHENG = A.SZSH AND B.SHIBH = A.SZSHI ".
				"LEFT JOIN H01DB012116 C ON C.SHENGBH = A.SZSH ".
				"WHERE A.QYBH = :QYBH AND (A.XKZHYXQ < SYSDATE OR A.YYZHZHYXQ < SYSDATE) AND A.KHZHT = '1' ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne( $pagedSql["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll( $pagedSql["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true, $totalCount, $filter["posStart"] );
	}
	
}