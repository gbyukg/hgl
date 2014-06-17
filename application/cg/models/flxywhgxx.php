<?php
/*********************************
 * 模块：   采购模块(CG)
 * 机能：   返利协议维护信息(供应商)(flxywhgxx)
 * 作成者：handong
 * 作成日：2011/06/02
 * 更新履历：
 *********************************/
 
class cg_models_flxywhgxx extends Common_Model_Base {
	/**
	 * 取得用户的姓名与所属部门信息
	 */
    function getGridData($filter){
    	//排序用字段名
    	$fields=array("","XYBH","","KPRQ","KSHRQ","ZHZHRQ","","","","","","ZHUANGTAI");
	     //检索SQL
	     $sql = " SELECT XYBH,DWBH,TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ,TO_CHAR(KSHRQ,'YYYY-MM-DD') AS KSHRQ,TO_CHAR(ZHZHRQ,'YYYY-MM-DD') AS ZHZHRQ,DECODE(FLFSH,'1','数量累计','2','金额累计') AS FLFSH,ZHCLJSHL,ZHCLJJE,FLJE,BEIZHU, " .
	            " DECODE(ZHUANGTAI,'1','启用','X','禁用') AS ZHUANGTAI " .
	            " FROM H01DB012313 WHERE QYBH = :QYBH  AND XYLX ='0'" ;
	    		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;

	    
      //查找条件  编号 状态
		if($filter['searchParams']['DWBH']!=""){
			$sql .= " AND( DWBH LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['DWBH']);
		}
		
	    if($filter['searchParams']['SHFXSHLSHSHJ']==""){
			$sql .= " AND TO_CHAR(ZHZHRQ,'YYYY-MM-DD') < TO_CHAR(SYSDATE,'YYYY-MM-DD') ";
		}
	   //自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CG_FLXYWHGXX",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 项目最后添加主键
		$sql .=" ,XYBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true,$totalCount, $filter ["posStart"] );
    }
/**
	 * 取得发货出货口信息
	 * @param string $xybh	  协议编号
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	
	function getFlxywhgxx($xybh, $filter, $flg = 'current') {
		
		//检索SQL
    	$fields=array("","XYBH","","KPRQ","KSHRQ","ZHZHRQ","","","","","","ZHUANGTAI");
		$sql_list = "SELECT  ROWID,LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",XYBH) AS NEXTROWID," . 
		" 						   LAG(ROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",XYBH) AS PREVROWID " . 
		"  ,XYBH" .
		 " FROM H01DB012313 " .
		" WHERE QYBH = :QYBH AND XYLX = '0'";

		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
	 //查找条件  编号 状态
		if($filter['searchParams']['DWBH']!=""){
			$sql_list .= " AND( DWBH LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['DWBH']);
		}
		
	    if($filter['searchParams']['SHFXSHLSHSHJ']==""){
			$sql_list .= " AND TO_CHAR(ZHZHRQ,'YYYY-MM-DD') > TO_CHAR(SYSDATE,'YYYY-MM-DD') ";
		}
	
		 //自动生成精确查询用Sql
		$sql_list .= Common_Tool::createFilterSql("CG_FLXYWHGXX",$filter['filterParams'],$bind);
		
		$sql_single = " SELECT XYBH,DWBH,TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ,TO_CHAR(KSHRQ,'YYYY-MM-DD') AS KSHRQ,TO_CHAR(ZHZHRQ,'YYYY-MM-DD') AS ZHZHRQ,DWMCH,DHHM,DIZHI,FLFSH,ZHCLJSHL,ZHCLJJE,FLJE,BEIZHU, " .
	            " TO_CHAR(BGRQ,'YYYY-MM-DD hh24:mi:ss') AS BGRQ,DECODE(ZHUANGTAI,'1','启用','X','禁用') AS ZHUANGTAI " .
	            " FROM H01VIEW012313  " ;
		
		//当前				
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH = :QYBH AND XYBH = :XYBH ";
//			unset ( $bind ['SEARCHKEY'] );
//			unset ( $bind ['SEARCHKEYKQBH'] );
		
		} else if ($flg == 'next') { //下一条		
			$sql_single .= " WHERE ROWID = (SELECT NEXTROWID FROM  (SELECT NEXTROWID,XYBH FROM ( $sql_list ) WHERE XYBH = :XYBH))";
		} else if ($flg == 'prev') { //前一条
			$sql_single .= " WHERE ROWID = (SELECT PREVROWID FROM  (SELECT PREVROWID,XYBH FROM ( $sql_list ) WHERE XYBH = :XYBH))";
		}
		//绑定 区域编号 & 协议编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['XYBH'] = $xybh;
		
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
    /**
	 * 返利协议禁用和启用
	 * @param string $xybh 协议编号
	 * @return unknown
	 */
	function updateStatus($xybh,$zhuangtai) {
		
		$sql = "UPDATE H01DB012313 " . " SET ZHUANGTAI = :ZHUANGTAI" . " WHERE QYBH =:QYBH AND XYBH =:XYBH ";
		$bind = array ('ZHUANGTAI' => $zhuangtai, 'QYBH' => $_SESSION ['auth']->qybh, 'XYBH' => $xybh  );
		return $this->_db->query ( $sql, $bind );
	
	}
}
?>