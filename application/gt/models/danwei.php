<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    单位选择model
 * 作成者：周义
 * 作成日：2010/12/15
 * 更新履历：
 *********************************/
class gt_models_danwei extends Common_Model_Base {
	/*
	 * 列表数据取得(xml格式)
	 */
	function getListData($filter) {
		//排序用字段名
		$fields = array ("", "DWBH", "DWMCH", "DIZHI" );
		//检索SQL
		$sql = "SELECT DWBH,DWMCH,DIZHI,DHHM,KOULV,FHQBH,FHQMCH" .
		      " FROM H01VIEW012106 " . 
		      " WHERE QYBH = :QYBH " . //区域编号
              " AND FDBSH ='0'"; //分店标识

		if ($filter ['flg'] == '0') { //销售
			$sql .= " AND SHFXSH = '1'";
		} elseif ($filter ['flg'] == '1') { //采购（进货）
			$sql .= " AND SHFJH = '1'";
		}
		
		//可用
		if ($filter ['status'] == '0') {
			$sql .= " AND KHZHT = '1'";
		}
		
	    if($filter['searchParams']['SEARCHKEY']!=""){
			$sql .= " AND( DWBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(DWMCH) LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(ZHJM) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['SEARCHKEY']);
		}
		

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("GT_DANWEI",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"].",DWBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		return Common_Tool::createXml ( $recs, true, $totalCount, $filter ["posStart"] );
	}

    /*
	 * 自动完成数据取得
	 */
	public function getAutocompleteData($filter){
		
  	   //检索SQL
		$sql = "SELECT DWBH,DWMCH,DIZHI,DHHM,KOULV,FHQBH,FHQMCH" .
		      " FROM H01VIEW012106 " . 
		      " WHERE QYBH = :QYBH " . //区域编号
		      " AND KHZHT = '1' " . //可用
              " AND FDBSH ='0'"; //分店标识

		if ($filter ['flg'] == '0') { //销售
			$sql .= " AND SHFXSH = '1'";
		} elseif ($filter ['flg'] == '1') { //采购（进货）
			$sql .= " AND SHFJH = '1'";
		}
		
		//快速查找条件
		if ($filter ["searchkey"] != "") {
			$bind ['SEARCHKEY'] = strtolower($filter ["searchkey"]);
			$sql .= " AND (lower(DWBH) LIKE '%' || :SEARCHKEY || '%' OR  lower(DWMCH) LIKE '%' || :SEARCHKEY || '%' OR lower(ZHJM) LIKE '%' || :SEARCHKEY || '%')";
		}
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		return $this->_db->fetchAll($sql,$bind);
	}
	
		/*
	 * 根据单位编号编号取得单位信息
	 */
	public function getDanweiInfo($filter) {
				
       //检索SQL
		$sql = "SELECT DWBH,DWMCH,(SZSHMCH || SZSHIMCH || DIZHI) AS DIZHI,DHHM,KOULV,FHQBH,FHQMCH,DECODE(XSHXDQ,NULL,0,XSHXDQ) AS XSHXDQ,SHFZZHSH" .
		      " FROM H01VIEW012106 " . 
		      " WHERE QYBH = :QYBH " . //区域编号
		      " AND DWBH = :DWBH ". //单位编号
		      " AND KHZHT = '1' " . //可用
		      " AND FDBSH ='0'"; //分店标识

		if ($filter ['flg'] == '0') { //销售
			$sql .= " AND SHFXSH = '1'";
		} elseif ($filter ['flg'] == '1') { //采购（进货）
			$sql .= " AND SHFJH = '1'";
		}
		
				
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		return $this->_db->fetchRow ( $sql, $bind );	
	}
	
}
