<?php
/*********************************
 * 模块：    销售模块(XS)
 * 机能：    一品多价Model
 * 作成者：周义
 * 作成日：2010/08/05
 * 更新履历：
 *********************************/
class xs_models_ypdj extends Common_Model_Base{

	/**
	 * 功能：取得一品多价列表数据
	 * 参数： array $filter 查询条件
	 * 返回值： xml
	 */
	function getListData($filter) {
		
		//检索SQL
		$sql = "SELECT A.HSHJ,A.BHSHJG,".
		       "C.NEIRONG AS JLDWMCH,".
		       "A.JLGG,".
		       "A.KHDJ,".
		       "A.BEIZHU".
		       " FROM H01DB012104 A ".
		       "INNER JOIN H01DB012106 B ON A.QYBH = B.QYBH AND A.KHDJ = B.KHDJ ".
		       "LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND C.CHLID = 'DW'  AND A.JLDW = C.ZIHAOMA ".
		       "WHERE A.QYBH = :QYBH ".
		       "AND A.SHPBH = :SHPBH ".
		       "AND B.DWBH = :DWBH";

		//排序
		$sql .= " ORDER BY A.JLDW,A.JLGG";
				
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$bind ['DWBH'] = $filter['dwbh']; //单位编号
		$bind ['SHPBH'] = $filter['shpbh'];//商品编号
		
		$recs = $this->_db->fetchAll($sql,$bind);
		return Common_Tool::createXml ( $recs, true );
	}
}

