<?php
/*
 * 单位选择列表画面
 */
class co_models_danwei extends Common_Model_Base{

	public  function __construct() {
		$this->_db = Zend_Registry::get ( "db" );
	}
	
	/*
	 * 列表数据取得(xml格式)
	 */
	function getXmlData($filter) {
		//排序用字段名
		$fields=array("","DWBH","DWMCH","DIZHI","LIANXIREN","ZHJM");
		
		//检索SQL
		$sql = "SELECT DWBH,DWMCH,DIZHI,LIANXIREN,DIANHUA,ZHJM,KOULV FROM H01DB012106  WHERE QYBH = '".$_SESSION['auth']->qybh."' AND FDBSH ='". $filter["flg"] ."'";

		//快速查找条件
		if($filter["searchkey"]!="")
		{
			$key = $filter["searchkey"];
			$sql .=" AND (DWBH LIKE '%$key%' OR DWMCH LIKE '%$key%' OR ZHJM LIKE '%$key%')";
			
		}
		       
		//排序
		$sql .= " ORDER BY ".$fields[$filter["orderby"]]." ".$filter["direction"];
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = $this->getPageSql ( $sql, $filter );
	     
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"] );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"] );
		
		return $this->createXml($recs,true,"DWBH",$totalCount,$filter["posStart"]);
	
	}
	
	
	/*
	 * 单条数据取得
	 */
	public function getSingleData($searchkey) {
		//检索SQL
		$sql = "SELECT * FROM H01DB012106 WHERE QYBH='".$_SESSION['auth']->qybh."' AND( DWBH LIKE '%$searchkey%' OR DWMCH LIKE '%$searchkey%' OR ZHJM  LIKE '%$searchkey%')";
	
		$count = $this->_db->fetchOne("SELECT COUNT(*) FROM ($sql)");
        
		//只要一条匹配时返回数据
		if($count==1)
		{
			return $this->_db->fetchRow($sql);
		}else 
		{
			return $count;
		}
	}
	


}
