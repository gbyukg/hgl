<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    员工选择
 * 作成者：周义
 * 作成日：2010/12/15
 * 更新履历：
 *********************************/
class gt_models_yuangong extends Common_Model_Base{
	/*
	 * 列表数据取得(xml格式)
	 */
	function getListData($filter) {
		//排序用字段名
		$fields=array("","YGBH","YGXM","","SSBM");
		
		//检索SQL
		$sql = "SELECT YGBH,YGXM,SSBM,SSBMMCH,DHHM,SHJHM,DZYJ FROM H01VIEW012113 ".
		       "WHERE QYBH =:QYBH AND YGZHT = '1' ";

		if($filter['flg']=='0'){
			$sql .= "AND SHFCGY = '1'";  //采购员
		}else if($filter['flg']=='1'){
			$sql .= "AND SHFXSHY = '1'";   //销售员
		}else if($filter['flg']=='2'){
			$sql .= "AND SHFCKGLY = '1'"; //仓库管理员
		}
		
		//查询条件		
		if($filter['searchParams']['SEARCHKEY'] !=""){
			$sql .= " AND (YGBH LIKE :SEARCHKEY || '%' OR lower(YGXM) LIKE :SEARCHKEY || '%' OR lower(ZHJM) LIKE :SEARCHKEY || '%')";
		    $bind['SEARCHKEY']= $filter['searchParams']['SEARCHKEY'];
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("GT_YUANGONG",$filter['filterParams'],$bind);
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//排序
		$sql .= " ORDER BY ".$fields[$filter["orderby"]]." ".$filter["direction"].",YGBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
	     
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"],$bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"] ,$bind);
		
		return Common_Tool::createXml($recs,true,$totalCount,$filter["posStart"]);
	}

	/*
	 * 自动完成数据取得
	 */
	public function getAutocompleteData($filter){
		//检索SQL
		$sql = "SELECT YGBH,YGXM,SSBM,SSBMMCH,DHHM,SHJHM,DZYJ FROM H01VIEW012113 ".
		       "WHERE QYBH =:QYBH AND YGZHT = '1' ";
		
	    //选择类别
		if($filter['flg']=='0'){
			$sql .= "AND SHFCGY = '1'";  //采购员
		}else if($filter['flg']=='1'){
			$sql .= "AND SHFXSHY = '1'";   //销售员
		}else if($filter['flg']=='2'){
			$sql .= "AND SHFCKGLY = '1'"; //仓库管理员
		}
		
		//查询条件		
		if($filter['searchkey'] !=""){
			$sql .= " AND (YGBH LIKE :SEARCHKEY || '%' OR lower(YGXM) LIKE :SEARCHKEY || '%' OR lower(ZHJM) LIKE :SEARCHKEY || '%')";
		    $bind['SEARCHKEY']= $filter['searchkey'];
		}

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		return $this->_db->fetchAll($sql,$bind);
	}
}
