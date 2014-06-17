<?php
/*
 * 库位选择画面
 */
class co_models_kuwei extends Common_Model_Base {
	
	public function __construct() {
		$this->_db = Zend_Registry::get ( "db" );

	}
	
	/*
	 * 列表数据取得（xml格式）
	 */
	function getXmlData($filter) {
		//检索SQL
		$sql = "SELECT A.CKMCH,A.KQMCH,A.KWMCH,A.PIHAO,A.SHULIANG,A.BZHQZH,A.SHCHRQ,A.CKBH,A.KQBH,A.KWBH,A.SHFSHKW,decode(A.SHFSHKW,'1','散货库位','包装库位') as SHFSHKWM FROM H01VIEW012002 A WHERE A.QYBH = '".$_SESSION['auth']->qybh."' AND A.SHPBH = '".$filter ["shpbh"]."'";
						
		//排序
		$sql .=" ORDER BY SHFSHKW,ZKZHT DESC,PIHAO,RKDH,CKBH,KQBH,KWBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = $this->getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"] );
		
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"] );
		
		return $this->createXml ( $recs,false,"", $totalCount, $filter ["posStart"] );
	}
	
	function getKucunData($shpbh,$shfshkw){
			//检索SQL
		$sql = "SELECT A.* FROM H01VIEW012002 A WHERE A.QYBH = '".$_SESSION['auth']->qybh.
		       "' AND A.SHPBH = '".$shpbh.
		       "' AND A.SHFSHKW = '".$shfshkw."'";
						
		//排序
		$sql .=" ORDER BY ZKZHT,PIHAO,RKDH,CKBH,KQBH,KWBH";
		
		return $this->_db->fetchAll ( $sql );
		
		
		
		
	}
	
}
