<?php
/*********************************
 * 模块：   配送模块(PS)
 * 机能：  待配送货物预览(dpshwyl)
 * 作成者：梁兆新
 * 作成日：2011/1/28
 * 更新履历：
 *********************************/
class ps_models_dpshwyl extends Common_Model_Base {
	/*
	 * 得到发货区信息
	 */
	function getquhao(){
		$bind['QYBH'] = $_SESSION['auth']->qybh; //区域编号
		$sql='SELECT FHQBH,FHQMCH FROM H01DB012422 WHERE QYBH=:QYBH AND FHQZHT=\'1\'';
		$recs = $this->_db->fetchAll ($sql,$bind);
		return $recs;
		
	}
	
	//根据查询条件获得列表信息
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ('T1.CHKDBH', 'T2KPRQ' , 'T3.XSHDBH' , 'T3KPRQ' , 'T6.DWMCH' , 'T3.DIZHI' , 'T3.DHHM' , 'T4.SHPMCH' , 'T4.GUIGE' , 'T5.NEIRONG' , 'T1.PIHAO' , 'T1.SHCHRQ' , 'T1.SHULIANG','T1.DANJIA','T1.HSHJ','T1.JINE','T1.HSHJE' ); //查询排序字段
		//检索SQL
	    $sql =	"SELECT	T1.CHKDBH,TO_CHAR(T2.KPRQ,'YYYY-MM-DD') T2KPRQ,T3.XSHDBH,TO_CHAR(T3.KPRQ ,'YYYY-MM-DD') T3KPRQ,T6.DWMCH,T3.DIZHI,T3.DHHM,".																																								
				"	T4.SHPMCH,T4.GUIGE,T5.NEIRONG,T1.PIHAO,TO_CHAR(T1.SHCHRQ ,'YYYY-MM-DD'),T1.SHULIANG,T1.DANJIA,T1.HSHJ,T1.JINE T1JINE,T1.HSHJE T1HSHJE,T1.BEIZHU".																																									
				"	FROM H01DB012409 T1 LEFT OUTER JOIN H01DB012101 T4 ON T1.SHPBH=T4.SHPBH LEFT OUTER JOIN H01DB012001 T5 ON T4.BZHDWBH=T5.ZIHAOMA AND T5.CHLID='DW',H01DB012408 T2,H01DB012201 T3	LEFT OUTER JOIN H01DB012106 T6 ON T3.DWBH=T6.DWBH".																																					
				"	WHERE T1.QYBH= T2.QYBH AND  T1.QYBH= T3.QYBH AND  T1.QYBH= T4.QYBH AND  T1.QYBH= T6.QYBH AND T1.QYBH= T5.QYBH AND T1.QYBH =:QYBH".																																								
				"	AND	T1.CHKDBH = T2.CHKDBH ".
	   			"	AND	T1.CHKDBH = T2.CHKDBH ".																																							
				"	AND T2.CKDBH = T3.XSHDBH ".																																									
				"	AND	T3.SHFPS = '1'";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		//查找条件  车牌号码
		
		//起止日期
		if ($filter ['serchstime'] != '') {
			$sql .= " AND T2.KPRQ>=TO_DATE(:STIME,'YYYY-MM-DD')";
			$bind['STIME']=$filter['serchstime']; 
		}
	    //终止日期
		if(!empty($filter['serchetime'])){
			$sql .= " AND T2.KPRQ<=TO_DATE(:ETIME,'YYYY-MM-DD')";
			$bind['ETIME'] =$filter['serchetime']; 
		}
		//查找条件车牌号
		if ($filter ["serchfhqbh"] !='') {
			$sql .= " AND T2.FHQBH=:FHQBH";
			$bind ['FHQBH'] = $filter ["serchfhqbh"];
		}
			
		if(!isset($filter ['orderby'])&& !empty($filter ['orderby'])){
			//排序
			if(empty($filter ['direction'])&& $filter ['direction']=='ASC'){
				$sql .= ' AND  ' . $fields [$filter ['orderby']] . ">0" ;
			}else{
				$sql .= ' ORDER BY ' . $fields [$filter ['orderby']] . ' ' . $filter ['direction'];
			}
		}		
		//防止重复数据引发翻页排序异常，orderby 添加主键
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ['sql_count'], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ['sql_page'], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true,$totalCount, $filter ['posStart'] );
	}	
}
	