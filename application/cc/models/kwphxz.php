<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    库位批号选择
 * 作成者：刘枞
 * 作成日：2010/12/02
 * 更新履历：
 *********************************/
class cc_models_kwphxz extends Common_Model_Base {
	
	/*
	 * 列表数据取得（xml格式）
	 */
	function getListData($filter) {
		//检索SQL
		//显示排列顺序：商品名称,仓库名称,库区名称,库位名称,批号,批次数量,单位,在库状态,保质期至,生产日期,仓库编号,库区编号,库位编号,单位编号,在库状态值
		$sql = "SELECT E.SHPMCH, D.CKMCH, C.KQMCH, B.KWMCH, A.PIHAO, SUM(A.SHULIANG) AS SHULIANG, "
				."F.NEIRONG AS BZHDWM, DECODE(A.ZKZHT,'0','可销','1','催销','冻结') AS ZKZHTM,"
			    ."TO_CHAR(A.BZHQZH, 'YYYY-MM') AS BZHQZH, TO_CHAR(A.SHCHRQ, 'YYYY-MM-DD') AS SHCHRQ, "
			    ."A.CKBH, A.KQBH, A.KWBH, A.BZHDWBH, A.ZKZHT, MIN(A.RKDBH) AS RKDBH, B.SHFSHKW "
			    ."FROM H01DB012404 A "
			    ."LEFT OUTER JOIN H01DB012403 B ON A.KWBH = B.KWBH AND A.KQBH = B.KQBH AND A.CKBH = B.CKBH AND A.QYBH = B.QYBH AND B.SHFSHKW = '1' "
			    ."LEFT OUTER JOIN H01DB012402 C ON A.KQBH = C.KQBH AND A.CKBH = C.CKBH AND A.QYBH = C.QYBH "
			    ."LEFT OUTER JOIN H01DB012401 D ON A.CKBH = D.CKBH AND A.QYBH = D.QYBH "
			    ."LEFT OUTER JOIN H01DB012101 E ON A.SHPBH = E.SHPBH AND A.QYBH = E.QYBH "
			    ."LEFT OUTER JOIN H01DB012001 F ON A.BZHDWBH = F.ZIHAOMA AND F.CHLID = 'DW' "
				."WHERE A.ZKZHT <> '2' AND A.QYBH =:QYBH AND A.SHPBH =:SHPBH AND B.SHFSHKW = '1'"
				."GROUP BY A.CKBH, A.KQBH, A.KWBH, A.PIHAO, A.ZKZHT,A.BZHDWBH,"
			    ."BZHQZH, SHCHRQ, A.BZHDWBH, B.KWMCH, B.SHFSHKW, C.KQMCH, D.CKMCH, E.SHPMCH, F.NEIRONG HAVING SUM(A.SHULIANG) > 0";
		
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['SHPBH'] = $filter['shpbh'];
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"],$bind );
		
		//当前页数据
		//$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"],$bind );
		$recs = $this->_db->fetchAll ( $sql,$bind );
		
		return Common_Tool::createXml ( $recs,false, $totalCount, $filter ["posStart"] );
	}
	
}
