<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    采购入库审核(RKSHH)
 * 作成者：ZhangZeliang
 * 作成日：2011/03/28
 * 更新履历：
 *********************************/
class cc_models_rkshh extends Common_Model_Base {
	/*
	 * 获取采购质检单中未审核的数据
	 *
	 * @param none
	 * @return array JSON
	 */
	public function getdjinfo($filter) {
		$fields = array ("", "A.YRKDBH", "A.CKDBH", "KPRQ", "A.DWBH", "E.DWMCH", "B.BMMCH", "YWY", "RKLX", "CZY" );
		$sql = "SELECT A.YRKDBH,A.CKDBH,TO_CHAR(A.KPRQ,'YYYY-MM-DD') AS KPRQ,A.DWBH,E.DWMCH,B.BMMCH,C.YGXM AS YWY,DECODE(A.RKLX,'1','采购入库','0','退货入库') AS RKLX,D.YGXM AS CZY " .
	    "FROM H01DB012429 A " . 
	    "LEFT JOIN H01DB012112 B ON A.QYBH = B.QYBH AND A.BMBH = B.BMBH " . 
	    "LEFT JOIN H01DB012113 C ON A.QYBH = C.QYBH AND A.YWYBH = C.YGBH " . 
	    "LEFT JOIN H01DB012113 D ON A.QYBH = D.QYBH AND A.ZCHZH = D.YGBH " . 
	    "LEFT JOIN H01DB012106 E ON A.QYBH = E.QYBH AND A.DWBH = E.DWBH " . 
	    "LEFT JOIN H01DB012306 F ON A.QYBH = F.QYBH AND A.CKDBH = F.CGDBH " . 
		"WHERE A.QYBH=:QYBH AND A.CGYFHZHT = '0' AND F.YWYBH = :YWYBH";
		
		//绑定查询条件
		$bind ["QYBH"] = $_SESSION ['auth']->qybh;
		$bind ["YWYBH"] = $_SESSION ["auth"]->userId;
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true, $totalCount, $filter ["posStart"] );
	}
	
	/*
	 * 获取采购质检单中未审核的数据
	 *
	 * @param none
	 * @return array JSON
	 */
	public function getmxinfo($filter) {
		$sql = "SELECT A.SHPBH,B.SHPMCH,B.GUIGE,C.NEIRONG AS BZDWMC,A.PIHAO,TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS SHCHRQ," . 
		"TO_CHAR(A.BZHQZH,'YYYY-MM-DD') AS BZHQZH,A.BZHSHL,A.LSSHL,A.SHULIANG,A.KRKSHL,A.DANJIA,A.HSHJ,A.KOULV," . 
		"B.SHUILV,A.JINE,A.HSHJE,A.SHUIE,B.LSHJ,B.CHANDI,A.BEIZHU " . 
		"FROM H01DB012430 A " . 
		"LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH " . 
		"LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' " . 
		"WHERE A.QYBH=:QYBH AND A.YRKDBH=:YRKDBH";
		
		//绑定查询条件
		$bind ["QYBH"] = $_SESSION ['auth']->qybh;
		$bind ["YRKDBH"] = $filter ["yrkdbh"];
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true, $totalCount, $filter ["posStart"] );
	}
	
	/*
	 * 更新审核状态(on:审核通过；off:审核部通过)
	 *
	 * @param none
	 * @return array JSON
	 */
	public function updateVerify($filter) {
		$sql = "UPDATE H01DB012429 SET CGYFHZHT = :CGYFHZHT,BGZH = :BGZH,BGRQ = SYSDATE,CGYFHYJ = :CGYFHYJ,CGYBH = :CGYBH,CGYFHRQ= SYSDATE,RKZHT = '0' " . 
		"WHERE QYBH = :QYBH AND YRKDBH = :YRKDBH";
		$bind ["CGYFHZHT"] = $filter ["status"]; //审核状态
		$bind ["BGZH"] = $_SESSION ["auth"]->userId; //变更者
		$bind ["CGYFHYJ"] = $filter ["cgyfhyj"]; //采购员复合意见
		$bind ["CGYBH"] = $_SESSION ["auth"]->userId; //采购员编号
		$bind ["QYBH"] = $_SESSION ["auth"]->qybh; //区域编号
		$bind ["YRKDBH"] = $filter ["yrkdbh"]; //预入库单编号
		return $this->_db->query ( $sql, $bind );
	}
}

?>