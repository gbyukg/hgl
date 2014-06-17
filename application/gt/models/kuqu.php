<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    库区选择
 * 作成者：周义
 * 作成日：2010/12/15
 * 更新履历：
 *********************************/
class gt_models_kuqu extends Common_Model_Base{
	/*
	 * 列表数据取得(xml格式)
	 */
	function getListData($filter) {
		//排序用字段名
		$fields=array("","KQBH","NLSSORT(KQMCH,'NLS_SORT=SCHINESE_PINYIN_M')","JHSHX","","KQLX","","KQZHT");
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['CKBH'] = $filter['ckbh'];
		
		//检索SQL
		$sql = "SELECT KQBH,KQMCH,JHSHX,KQLX,KQLXMCH,KQZHT,DECODE(KQZHT,'0','冻结','1','可用','X','删除','未知') AS KQZHTMCH ".
		       " FROM H01VIEW012402 " .
		       " WHERE QYBH = :QYBH ".   //区域编号
		       " AND   CKBH = :CKBH ";
		       
     	if($filter['flg']=='0'){//可用及冻结
			$sql .= " AND KQZHT IN ('0','1') ";
		}elseif($filter['flg']=='1'){//全部
			$sql .= " AND KQZHT IN ('0','1','X')";
		}
		
		//快速查找条件
		if($filter['searchParams']['SEARCHKEY']!=""){
			$bind['SEARCHKEY'] =strtolower(($filter['searchParams']['SEARCHKEY']));
			$sql .=" AND (lower(KQBH) LIKE '%' || :SEARCHKEY || '%' OR  lower(KQMCH) LIKE '%' || :SEARCHKEY || '%' )"; 			
		}
		
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("GT_KUQU",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY ".$fields[$filter["orderby"]]." ".$filter["direction"];
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
	     
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"] ,$bind);
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"],$bind );
		
		return Common_Tool::createXml ($recs,true,$totalCount,$filter["posStart"]);
	}
	
    /*
	 * 自动完成数据取得
	 */
	public function getAutocompleteData($searchkey){	
		$sql = "SELECT CKBH,CKMCH FROM H01DB012401 ".
		       " WHERE QYBH = :QYBH ".
		       " AND CKZHT = '1' ";
		
		if($searchkey !=""){
			$sql .= " AND (CKBH LIKE :SEARCHKEY || '%' OR lower(CKMCH) LIKE :SEARCHKEY || '%' )";
		    $bind['SEARCHKEY']= $searchkey;
		}
		
		$bind['QYBH']= $_SESSION ['auth']->qybh;
		
		return $this->_db->fetchAll($sql,$bind);

	}	
}
