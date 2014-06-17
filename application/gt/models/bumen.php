<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    部门选择Model
 * 作成者：周义
 * 作成日：2010/12/15
 * 更新履历：
 *********************************/
class gt_models_bumen extends Common_Model_Base  {

	/**
	 * 部门树形数据取得
	 * @param  string $flg  选择范围  0: 仅可用  1:全部
	 * @param 
	 * @return xml
	 */
	public function getTreeData($flg) {
		$sql = "SELECT SYS_CONNECT_BY_PATH(BMBH,'/') AS PATH,BMBH,BMMCH,SHJBM FROM H01DB012112 " .
		       " WHERE QYBH =:QYBH  ".
		        ($flg=="0"? " AND BMZHT = '1'":"").
		       "START WITH SHJBM = '999999' CONNECT BY PRIOR BMBH = SHJBM " .
		       "ORDER SIBLINGS BY BMBH ";

		//绑定查询变量       
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$recs = $this->_db->fetchAll ( $sql,$bind);
		return Common_Tool::createTreeXml($recs,'BMBH','BMMCH');
	}
	
	/**
	 * 自动完成数据取得
	 *
	 * @param string $searchkey
	 * @return 部门数组
	 */
	public function getAutocompleteData($searchkey){
		$sql = "SELECT BMBH,BMMCH FROM H01DB012112 ".
		       " WHERE QYBH = :QYBH ".
		       " AND BMZHT = '1' ";
		
		if($searchkey !=""){
			$sql .= " AND (BMBH LIKE :SEARCHKEY || '%' OR lower(BMMCH) LIKE :SEARCHKEY || '%' OR lower(ZHJM) LIKE :SEARCHKEY || '%')";
		    $bind['SEARCHKEY']= $searchkey;
		}
		
		$bind['QYBH']= $_SESSION ['auth']->qybh;
		return $this->_db->fetchAll($sql,$bind);
	}
}
