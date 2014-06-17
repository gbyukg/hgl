<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   库存报警(kcbj)
 * 作成者：侯殊佳 
 * 作成日：2011/05/19
 * 更新履历：
 *********************************/
class cc_models_kcbj extends Common_Model_Base {
	

	/**
	 * 得到库存信息列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	
	public function getGridData($filter) {
		
		//检索SQL
		$sql = "SELECT A.SHPBH,A.SHPMCH,SUM(A.SHULIANG) AS SL,A.GUIGE,A.NEIRONG,A.SHCHCHJ,A.KCXX,A.KCSHX".
			   " FROM H01UV012407 A".
			   " WHERE A.QYBH = :QYBH  ".
			   " AND A.KCXX > (SELECT SUM(B.SHULIANG) FROM H01UV012407 B".
			   " WHERE A.QYBH=B.QYBH AND A.SHPBH=B.SHPBH )".
			   " AND SHPBH NOT IN ( SELECT X.SHPBH FROM H01VIEW012307 X ".
			   " LEFT JOIN H01VIEW012306 Y ON X.QYBH = Y.QYBH AND X.CGDBH = Y.CGDBH".
			   " WHERE X.QYBH = :QYBH AND Y.CGDZHT ='1')".
			   " GROUP BY SHPBH,SHPMCH,GUIGE,NEIRONG,SHCHCHJ,KCXX,KCSHX".
			   " ORDER BY SHPBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
	
		
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