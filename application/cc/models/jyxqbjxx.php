<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   近有效期报警信息(jyxqbjxx)
 * 作成者：handong
 * 作成日：2011/05/25
 * 更新履历：
 *********************************/
 
class cc_models_jyxqbjxx extends Common_Model_Base {
     /**
	 * 得到报警列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter){
		//排序用字段名
	    $fields=array("","SHPBH","","","KCSL","GUIGE","","","BZHQZH");
		//检索SQL
		$sql = " SELECT A.SHPBH,A.SHPMCH,A.PIHAO,SUM(A.KCSL) AS KCSL,A.GUIGE,A.DANWEI,A.SHCHCHJ,TO_CHAR (A.BZHQZH,'yyyy-mm-dd') AS BZHQZH,A.SHJCH ". 
               " FROM H01UV012408 A ".
               " WHERE A.QYBH = :QYBH AND TO_CHAR(A.ZZHCHKRQ,'YYYY-MM-DD') = '9999-12-31' AND DECODE(A.YJYSH,NULL,0,A.YJYSH) > A.SHJCH ". 
               " GROUP BY A.SHPBH, A.SHPMCH, A.PIHAO, A.GUIGE, A.DANWEI, A.SHCHCHJ, A.BZHQZH  ";
		
	    //绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
	    //排序
		$sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];

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
?>