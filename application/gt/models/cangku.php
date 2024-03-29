<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    仓库选择Model
 * 作成者：周义
 * 作成日：2010/12/15
 * 更新履历：
 *********************************/
class gt_models_cangku extends Common_Model_Base{
	/**
	 * 取得仓库列表数据
	 * @param array $filter 条件数组
	 * @return xml
	 */
	function getListData($filter) {
		//排序用字段名
		$fields=array("","CKBH","NLSSORT(CKMCH,'NLS_SORT=SCHINESE_PINYIN_M')","NLSSORT(DIZHI,'NLS_SORT=SCHINESE_PINYIN_M')","","","","CKZHT");
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		
		//检索SQL
		$sql = "SELECT ".
		       "CKBH,".
		       "CKMCH,".
		       "DIZHI,".
		       "LXDH,".
		       "YZHBM,".
		       "CKZHT,DECODE(CKZHT,'0','冻结','1','可用','X','删除','-') AS CKZHTMCH ".
		       "FROM H01VIEW012401 " .
		       "WHERE QYBH = :QYBH ";   //区域编号
		       
     	if($filter['flg']=='0'){//可用及冻结
			$sql .= " AND CKZHT IN ('0','1') ";
		}elseif($filter['flg']=='1'){//全部
			$sql .= " AND CKZHT IN ('0','1','X')";
		}else{
			$sql .= " AND CKZHT ='1' ";
		}
		
		//快速查找条件
		if($filter['searchParams']['SEARCHKEY']!=""){
			$bind['SEARCHKEY'] =strtolower(($filter['searchParams']['SEARCHKEY']));
			$sql .=" AND (lower(CKBH) LIKE '%' || :SEARCHKEY || '%' OR  lower(CKMCH) LIKE '%' || :SEARCHKEY || '%' )"; 			
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("GT_CANGKU",$filter['filterParams'],$bind);
			       
		//排序
		$sql .= " ORDER BY ".$fields[$filter["orderby"]]." ".$filter["direction"].",CKBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
	     
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"] ,$bind);
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"],$bind );
		
		return Common_Tool::createXml($recs,true,$totalCount,$filter["posStart"]);
	}
}
