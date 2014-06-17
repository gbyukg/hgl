<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    销售单选择
 * 作成者：周义
 * 作成日：2010/11/15
 * 更新履历：
 *********************************/
class gt_models_xshd extends Common_Model_Base{
	/*
	 * 列表数据取得(xml格式)
	 */
	function getListData($filter) {
		//排序用字段名
		$fields=array("","XSHDBH","KPRQ","DWBH","DWMCH");
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//检索SQL
		$sql = "SELECT XSHDBH,to_char(KPRQ,'YYYY-MM-DD'),
		        DWBH,DWMCH,SUM(JINE) AS JINE,SUM(SHUIE) AS SHUIE, SUM(HSHJE) AS HSHJE,
                BMMCH,YWYXM,KPYXM 
                FROM H01UV012003 ".  
               "WHERE QYBH = :QYBH AND QXBZH = '1' ";
		
		//销售单状态
		if($filter['flg']=='0'){
			$sql .= " AND XSHDZHT = '0' ";  //未出库
		}else if($filter['flg']=='1'){
			$sql .= " AND XSHDZHT <> '0' "; //已出库
		}
		//单位编号
		if($filter['searchParams']['DWBH'] !=""){
			$sql .= " AND DWBH = :DWBH";
			$bind ['DWBH'] = $filter['searchParams']['DWBH'];
		}
		//开始日期
		if($filter['searchParams']['BEGINDATE']!=""){
			$sql .= " AND KPRQ >= to_date(:BEGINDATE,'YYYY-MM-DD')";
			$bind ['BEGINDATE'] = $filter['searchParams']['BEGINDATE'];
		}
		//截止日期
		if($filter['searchParams']['ENDDATE'] !=""){
			$sql .= " AND KPRQ <= to_date(:ENDDATE,'YYYY-MM-DD')";
			$bind ['ENDDATE'] = $filter['searchParams']['ENDDATE'];
		}
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("GT_XSHD",$filter['filterParams'],$bind);

		$sql .= " GROUP BY XSHDBH,KPRQ,DWBH,DWMCH,BMMCH,YWYXM,KPYXM";
	
		//排序
		$sql .= " ORDER BY ".$fields[$filter["orderby"]]." ".$filter["direction"] .",XSHDBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
	    
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"],$bind );

		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"] ,$bind);

		return Common_Tool::createXml($recs,true,$totalCount,$filter["posStart"]);
	}

	
    /*
	 * 明细列表数据取得(xml格式)
	 */
	function getMxListData($filter) {
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['XSHDBH'] = $filter['xshdbh'];
		
		//检索SQL
		$sql = "SELECT SHPBH,SHPMCH,GUIGE,BZHDWMCH,PIHAO,TO_CHAR(SHCHRQ,'YYYY-MM-DD'),BZHSHL,".
		       "LSSHL,SHULIANG,DANJIA,HSHJ,KOULV,JINE,SHUILV,".
		       "SHUIE,HSHJE".
		       " FROM H01UV012004 ".
		       " WHERE QYBH = :QYBH ".
		       " AND XSHDBH = :XSHDBH ".
		       " ORDER BY XUHAO";
  		
		//取得数据
		$recs = $this->_db->fetchAll ( $sql,$bind);

		return Common_Tool::createXml($recs,true);
	}
}
