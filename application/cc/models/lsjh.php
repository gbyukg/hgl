<?php
/**********************************************************
 * 模     块：    仓储模块(CC)
 * 机     能：  零散拣货(lsjh)
 * 作成者：    姚磊
 * 作成日：    2011/03/22
 * 更新履历：
 **********************************************************/

class cc_models_lsjh extends Common_Model_Base {

	/**
	 * 得到分箱查询信息
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter){
		//排序用字段名
		$fields = array ("", "Q.ZHZHXH","ZHUANGTAI","Q.DJBH","Q.FENXIANGHAO","Q.ZXSH","Q.SHLHJ");

		//检索SQL
		$sql = "SELECT Q.ZHZHXH,DECODE(Q.ZHUANGTAI,'0','已分箱','1','拣货中','2','已装箱') AS ZHUANGTAI,A.CHSDCHK,Q.DJBH,Q.FENXIANGHAO ,".
			   "Q.ZXSH,A.SHLHJ, TO_CHAR(Q.FXRQ,'YYYY-MM-DD') AS FXRQ,Q.DYZCQ,Q.BGZH  FROM H01DB012437 A 
				LEFT JOIN H01DB012433 Q ON A.QYBH = Q.QYBH  AND A.ZHZHXH = Q.ZHZHXH 
				LEFT JOIN H01DB012443 C ON A.QYBH = C.QYBH AND A.CKBH = C.CKBH AND A.CHSDCHK = C.CHSDCHK 
				WHERE A.QYBH=:QYBH AND C.DLZH =:DLZH AND C.DLZHT ='1' AND A.ZHUANGTAI IN ('0','1','2','3','4')  ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DLZH'] = $_SESSION ['auth']->userId;
		//查询条件(分箱日期从<=分箱日期<=分箱日期到)
		if ($filter ["fxrqc"] != "" || $filter ["fxrqd"] != "")
		{
			$sql .= " AND :FXRQC <= TO_CHAR(Q.FXRQ,'YYYY-MM-DD') AND TO_CHAR(Q.FXRQ,'YYYY-MM-DD') <= :FXRQD";
			$bind ['FXRQC'] = $filter ["fxrqc"] == ""?"1900-01-01":$filter ["fxrqc"];
			$bind ['FXRQD'] = $filter ["fxrqd"] == ""?"9999-12-31":$filter ["fxrqd"];
		}
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键

		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true,$totalCount, $filter ["posStart"] );
	}
	
	/**
	 * 修改所选记录状态
	 *
	 * @param array 
	 * @return string xml
	 */
	public function update($filter){
		$sql = "UPDATE  H01DB012433 SET " . " ZHUANGTAI = '1',"  . " BGRQ = sysdate," . " BGZH = :BGZH" . " WHERE QYBH = :QYBH AND DYTM=:DYTM";
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['DYTM'] = $filter ['dytm']; //对应条码
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			return $this->_db->query ( $sql, $bind );		
	}
	
	/**
	 * 得到采购订单维护明细信息列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	
	public function getMingxiGridData($dytm, $filter) {

		//检索SQL
		$sql = "SELECT A.SHPBH,B.SHPMCH, A.KWBH,A.SHULIANG, A.PIHAO,
				DECODE(A.ZHUANGTAI,'0','未拣货','1','已拣货') AS ZHUANGTAI ,C.CKMCH,A.CHSDCHK 
				FROM H01DB012434 A  LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH  
				LEFT JOIN H01DB012401 C ON A.QYBH = C.QYBH AND A.CKBH = C.CKBH  WHERE A.QYBH = :QYBH 
				 ORDER BY A.SHPBH ";

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DYTM'] = $dytm;

		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true, $totalCount, $filter ["posStart"] );
	}
	
}