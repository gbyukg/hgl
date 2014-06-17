<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   不动产品报警(bdchpbj)
 * 作成者：侯殊佳 
 * 作成日：2011/05/19
 * 更新履历：
 *********************************/
class cc_models_bdchpbj extends Common_Model_Base {
	

	/**
	 * 得到库存信息列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	
	public function getGridData($filter) {
		
		//检索SQL
		$sql = "SELECT A.SHPBH,A.SHPMCH,SUM(A.SHULIANG) AS SL,A.GUIGE,A.NEIRONG,A.SHCHCHJ
				FROM H01UV012407 A
				WHERE A.QYBH = :QYBH AND A.SHULIANG > 0 
				AND A.SHPBH NOT IN ( SELECT DISTINCT X.SHPBH FROM H01DB012202 X 
				LEFT JOIN H01DB012201 Y ON X.QYBH = Y.QYBH AND X.XSHDBH = Y.XSHDBH
				WHERE X.QYBH = Y.QYBH AND Y.KPRQ > TO_DATE(:QSRQ,'YYYY-MM-DD') AND Y.KPRQ < TO_DATE(:ZZRQ,'YYYY-MM-DD')AND Y.QXBZH != 'X')
				GROUP BY SHPBH,SHPMCH,GUIGE,NEIRONG,SHCHCHJ
				ORDER BY SHPBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		//查询条件
		
		$bind ['QSRQ'] = $filter ["QSRQ"];
		$bind ['ZZRQ'] = $filter ["ZZRQ"];
		
		
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