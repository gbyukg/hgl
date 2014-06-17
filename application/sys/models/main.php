<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    部门选择Model
 * 作成者：周义
 * 作成日：2010/12/15
 * 更新履历：
 *********************************/
class sys_models_main extends Common_Model_Base {

	/**
	 * 导航菜单数据取得
	 * @param  
	 * @param 
	 * @return xml
	 */
	public function getMenuData() {
		$sql = " SELECT SYS_CONNECT_BY_PATH(CDBH,'/') AS PATH,CDBH,CDMCH,SHJCD,CDLJ FROM H01DB012004 " .
		       " WHERE QYBH =:QYBH  ".
		       " START WITH SHJCD = '00000000' CONNECT BY PRIOR CDBH = SHJCD " .
		       " ORDER SIBLINGS BY CDBH ";

		//绑定查询变量       
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$recs = $this->_db->fetchAll ( $sql,$bind);
		return Common_Tool::createTreeXml($recs,'CDBH','CDMCH','CDLJ');
	}
	
}
	
	
	