<?php
/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：       设备登录查询(SBDLCX)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/08/11
 ***** 更新履历：
 ******************************************************************/

class cc_models_sbdlcx extends Common_Model_Base {

	/**
	 * 得到列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridDanjuData($filter){
		//排序用字段名
		$fields = array ( "", "NLSSORT(ZHL,'NLS_SORT=SCHINESE_PINYIN_M')", "A.BH", "A.MCH", "A.DLZH", "B.YGXM" );
		
		//检索SQL
		$sql = "SELECT DECODE(A.ZHL,'1','库区','2','打包台','3','传送带出口') AS ZHL,A.BH,A.MCH,A.DLZH,B.YGXM FROM( ".
				"SELECT 1 AS ZHL,KQBH AS BH,KQMCH AS MCH,DLZH,QYBH,CKBH FROM H01DB012402 WHERE DLZHT = '1' AND KQZHT != 'X' ".
				"UNION ALL ".
				"SELECT 2 AS ZHL,DBTBH AS BH,DBTMCH AS MCH,DLZH,QYBH,CKBH FROM H01DB012442 WHERE DLZHT = '1' AND ZHUANGTAI != 'X' ".
				"UNION ALL ".
				"SELECT 3 AS ZHL,CHSDCHK AS BH,CAST(CHSDCHK AS NVARCHAR2(100)) AS MCH,DLZH,QYBH,CKBH FROM H01DB012443 WHERE DLZHT = '1' AND ZHUANGTAI != 'X' ) A ".
				"LEFT JOIN H01DB012113 B ON A.DLZH = B.YGBH AND A.QYBH = B.QYBH ".
				"WHERE A.QYBH = :QYBH AND A.CKBH = :CKBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CKBH'] = $_SESSION ['auth']->ckbh;
		
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