<?php
/******************************************************************
 ***** 模         块：       配送模块(PS)
 ***** 机         能：       线路汇总单(XLHZD)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/08/19
 ***** 更新履历：
 ******************************************************************/

class ps_models_xlhzd extends Common_Model_Base {
	private $idx_ROWNUM = 0;      // 行号

	
	
	/**
	 * 得到退货单列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridDanjuData($filter){
		
		//检索SQL
		$sql = "SELECT TO_CHAR(T3.KPRQ,'YYYY-MM-DD'), T3.XSHDBH, T5.DWMCH, T5.DIZHI, T5.DHHM, T4.SHPMCH, T4.GUIGE, ".
				"T1.PIHAO, T1.SHULIANG, TO_CHAR(T1.SHCHRQ,'YYYY-MM-DD'), TO_CHAR(T1.BZHQZH,'YYYY-MM'), T4.CHANDI, T1.BEIZHU ".
				"FROM H01DB012409 T1 ".	
				"JOIN H01DB012408 T2 ON T1.QYBH = T2.QYBH AND T1.CHKDBH = T2.CHKDBH	".
				"JOIN H01DB012201 T3 ON T2.QYBH = T3.QYBH AND T2.CKDBH = T3.XSHDBH ".
				"JOIN H01DB012101 T4 ON T1.QYBH = T4.QYBH AND T1.SHPBH = T4.SHPBH ".
				"JOIN H01DB012106 T5 ON T3.QYBH = T5.QYBH AND T3.DWBH = T5.DWBH	".
				"WHERE T1.QYBH = :QYBH ".
				"AND T2.CHKDZHT = '2' ";
				
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//发货区
		if ($filter ["FAHUOQU"] != ""  ) {
			$sql .= " AND T3.FHQBH = :FHQBH ";
			$bind ['FHQBH'] = $filter ["FAHUOQU"];
		}
		
		//当前页数据
		$recs = $this->_db->fetchAll( $sql, $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs );
	}
	
	
	/**
	 * 获取发货区信息
	 */
	function getFHQ()
	{
		$sql = "SELECT FHQBH,FHQMCH FROM H01DB012422 WHERE QYBH = :QYBH AND CKBH = :CKBH AND FHQZHT = '1'";
		
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CKBH'] = $_SESSION ['auth']->ckbh;
		
		$result = $this->_db->fetchPairs ( $sql, $bind );
		
		$result [''] = '--选择发货区--';
		ksort ( $result );
		
		return $result; 
	}
	
	
}