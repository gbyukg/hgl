<?php
/*
 * 首营企业选择列表画面
 */
class gt_models_shouying extends Common_Model_Base{

	/*
	 * 列表数据取得(xml格式)
	 */
	function getXmlData($filter) {
		//排序用字段名
		$fields=array("","QYBM","QYMCH","FRDB","","DIZHI");
		
		//检索SQL
		$sql = "SELECT QYBM,QYMCH,FRDB,DECODE(SHPTG,'1','是','否')AS SHPTG,DIZHI,SHCHJYFW,SHCHJYPZ,DECODE(YXKZH,'1','是','否')AS YXKZH,XKZHH,TO_CHAR(XKZHYXQ,'YYYY-MM-DD'),
		DECODE(YYYZHZH,'1','是','否')AS YYYZHZH,YYZHZHH,TO_CHAR(YYZHZHYXQ,'YYYY-MM-DD'),DECODE(YSHQWTSH,'1','是','否')AS YSHQWTSH,DECODE(YSHGZH,'1','是','否')AS YSHGZH,
		DECODE(YSHFZH,'1','是','否')AS YSHFZH,DECODE(YLXDH,'1','是','否')AS YLXDH,DECODE(YZHLBZHXY,'1','是','否')AS YZHLBZHXY,DECODE(TXJQ,'1','是','否')AS TXJQ,DECODE(YOUGONGBAO,'1','是','否')AS YOUGONGBAO,
		ZHJM,QITA,YWBYJ,ZHGBYJ,ZHGBMSHP,TO_CHAR(TBRQ,'YYYY-MM-DD'),TIANBAOREN,TBBM,TO_CHAR(BGRQ,'YYYY-MM-DD'),BGZH 
		FROM H01DB012107 WHERE QYBH = ".$_SESSION['auth']->qybh;

		//快速查找条件
		if($filter["searchkey"]!="")
		{
			$key = $filter["searchkey"];
			$sql .=" AND (QYBM LIKE '%$key%' OR QYMCH LIKE '%$key%' OR FRDB LIKE '%$key%')";
			
		}
		       
		//排序
		$sql .= " ORDER BY ".$fields[$filter["orderby"]]." ".$filter["direction"];
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
	     
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"] );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"] );
		
		return Common_Tool::createXml ($recs,true,$totalCount,$filter["posStart"]);
	
	}
	
	
	/*
	 * 单条数据取得
	 */
	public function getSingleData($searchkey) {
		//检索SQL
		$sql = "SELECT * FROM H01DB012107 WHERE QYBH='".$_SESSION['auth']->qybh."' AND( QYBM LIKE '%$searchkey%' OR QYMCH LIKE '%$searchkey%' OR FRDB  LIKE '%$searchkey%')";
	
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
