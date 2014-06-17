<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：   商品分类选择
 * 作成者：周义
 * 作成日：2010/12/02
 * 更新履历：
 *********************************/
class gt_models_shpfl extends Common_Model_Base  {
	/**
	 * 树形数据取得
	 *
	 * @return xml
	 */
	public function getTreeData($filter) {
		//无根节点
		if($filter['flg']=='0'){
			$sql = "SELECT SYS_CONNECT_BY_PATH(SHPFL,'/') PATH,SHPFL,FLMCH,SHJFL FROM H01DB012109 " .
			       " WHERE QYBH = :QYBH".
		           " START WITH SHJFL =  :SHPFL CONNECT BY PRIOR SHPFL=SHJFL  ORDER SIBLINGS BY SHPFL ";
		}elseif($filter['flg']=='1'){//有根节点
			$table = "(SELECT '999999' AS SHPFL, N'所有分类' AS FLMCH,'' AS SHJFL FROM DUAL ".
		         " UNION ALL ".
		         "SELECT SHPFL,FLMCH,SHJFL FROM H01DB012109 WHERE QYBH = :QYBH )";
			$sql = "SELECT SYS_CONNECT_BY_PATH(SHPFL,'/') PATH,SHPFL,FLMCH,SHJFL FROM $table " .
		       "START WITH SHPFL =  :SHPFL CONNECT BY PRIOR SHPFL=SHJFL  ORDER SIBLINGS BY SHPFL ";
		}
		
		$bind['QYBH'] = $_SESSION['auth']->qybh; //区域编号 
		$bind['SHPFL'] = '999999';
		$recs = $this->_db->fetchAll ( $sql,$bind );
		return Common_Tool::createTreeXml($recs,'SHPFL','FLMCH');
	}
}
