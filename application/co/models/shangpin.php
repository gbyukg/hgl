<?php
/*
 * 商品选择画面
 */
class co_models_shangpin extends Common_Model_Base {
	
	public function __construct() {
		$this->_db = Zend_Registry::get ( "db" );
	}
	
	/*
	 * 列表数据取得（xml格式）
	 */
	function getXmlData($filter) {
		//排序用列定义
		$fields = array ("", "SHPBH","SHPMC" );
	
		//检索SQL
		$sql = "SELECT SHPBH,SHPMC,SHPGG,BZHDWM,SHOUJIA,HSHSHJ,KOULV,SHUILV,PEISONGJIA,".
		       "GONGHUOJIA,LSHJ,ZGSHJ,SHPTM,FLBM,PZHWH,JIXING,SHCHCHJ,CHANDI,SHFOTCM,JLGG".
		       " FROM H01VIEW012001 WHERE QYBH = '".$_SESSION['auth']->qybh."' ";

		
		//快速查找条件
		if ($filter ["searchkey"] != "") {
			$key = $filter ["searchkey"];
			$sql .= " AND (SHPBH LIKE '%$key%' OR SHPMC LIKE '%$key%' OR ZHJMA LIKE '%$key%' OR HXMING LIKE '%$key%' OR CHYMING LIKE '%$key%'  OR SUMING LIKE '%$key%')";
		}elseif ($filter ["flbm"] != "")
		{
			//分类编码
			$sql .= " AND FLBM IN(SELECT SHPFL FROM H01DB012109 START WITH SHPFL =  '" . $filter ["flbm"] . "' CONNECT BY PRIOR SHPFL = SHJFL)";
	
		}
		
	
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = $this->getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"] );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"] );
		
		//return $pagedSql ["sql_count"];
		
		return $this->createXml ( $recs,true,"SHPBH",$totalCount, $filter ["posStart"] );
	}
	
	/*
	 * 单条数据取得
	 */
	public function getSingleData($searchkey) {
		//检索SQL
		$sql = "SELECT * FROM H01VIEW012001 WHERE QYBH = '".$_SESSION['auth']->qybh."' AND ( SHPBH LIKE '%$searchkey%' OR SHPMC LIKE '%$searchkey%' OR ZHJMA  LIKE '%$searchkey%')";
	
		$count = $this->_db->fetchOne("SELECT COUNT(*) FROM ($sql)");
		
		//只要一条匹配时返回数据
		if($count==1)
		{
			return $this->_db->fetchRow($sql);
		}
		else{
			return $count;
		}
	
	}

}
