<?php
/**********************************************************
 * 模     块：    仓储模块(CC)
 * 机     能：  分箱维护(fxwh)
 * 作成者：    姚磊
 * 作成日：    2011/03/31
 * 更新履历：
 **********************************************************/	
class cc_models_fxwh extends Common_Model_Base {

	/**
	 * 得到分箱查询信息
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter){
		//排序用字段名
		$fields = array ("", "XSHDBH","XSHDZHT","DJBH","FENXIANGHAO","ZXSH","SHLHJ");

		//检索SQL
		$sql = " SELECT XSHDBH,DECODE(XSHDZHT,'0','未出库','3','已分箱') AS XSHDZHT,DWBH,DWMCH,TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ ".
			   " FROM H01VIEW012201  ".
			   " WHERE QYBH =:QYBH  AND SHHZHT IN ('0','1')".
			   " AND XSHDBH NOT IN (SELECT DISTINCT x.XSHDBH FROM H01DB012201 X ,H01DB012405 Y ,H01DB012406 Z WHERE X.QYBH = Y.QYBH AND X.QYBH = Z.QYBH
			     AND Y.QYBH=Z.QYBH AND X.XSHDBH = Y.YDDH AND Y.RKDBH = Z.RKDBH AND Z.RKDZHT != '2')".
			   " AND XSHDBH NOT IN (SELECT W.XSHDBH FROM H01DB012450 W WHERE W.QYBH =:QYBH AND W.ZHUANGTAI !='2')";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//是否未出库
		if($filter ['XSHDZHT'] == 0){
			$sql .= "AND  XSHDZHT IN ('0','3')";
			
		}else{
			$sql .= "AND  XSHDZHT ='0' ";
		}
		//查询条件(分箱日期从<=分箱日期<=分箱日期到)
		if ($filter['searchParams']["FXRQC"] != "" || $filter['searchParams']["FXRQD"] != "")
		{
			$sql .= " AND :FXRQC <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :FXRQD";
			$bind ['FXRQC'] = $filter['searchParams']["FXRQC"] == ""?"1900-01-01":$filter['searchParams']["FXRQC"];
			$bind ['FXRQD'] = $filter['searchParams']["FXRQD"] == ""?"9999-12-31":$filter['searchParams']["FXRQD"];
		}
		$sql .= Common_Tool::createFilterSql("CC_FXWH",$filter['filterParams'],$bind);
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= " ,XSHDBH ";
		
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
	 * 获取分箱状态
	 */
	public function getfxzt($xshdbh){
		
		$sql ="SELECT  XSHDZHT FROM H01DB012201 WHERE XSHDBH =:XSHDBH AND QYBH =:QYBH ";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;		
		$bind ['XSHDBH'] = $xshdbh;
		$recs = $this->_db->fetchRow($sql,$bind);	
		return $recs;
		
	}
}