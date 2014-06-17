<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    商品选择
 * 作成者：周义
 * 作成日：2010/12/15
 * 更新履历：
 *********************************/
class gt_models_shangpin extends Common_Model_Base {
	
	/*
	 * 列表数据取得（xml格式）
	 */
	function getListData($filter) {
		//排序用列定义
		$fields = array ("", "SHPBH","SHPMCH" );		
		$bind['QYBH'] = $_SESSION['auth']->qybh; //区域编号
	
		//检索SQL
		$sql = "SELECT SHPBH,SHPMCH,GUIGE,BZHDWMCH,SHCHCHJ,CHANDI ".
		       "FROM H01VIEW012101 ".
		       "WHERE QYBH = :QYBH ";

		//销售
		if($filter['flg'] == '0'){
			//禁销商品
			$sql .= " AND SHPBH NOT IN (SELECT SHPBH FROM H01DB012114  WHERE QYBH = :QYBH AND DWBH = :DWBH AND JXBZH = '3') ";
			//限定商品
			$sql .= " AND (XDBZH = '0' OR XDBZH = '1' AND SHPBH IN (SELECT SHPBH FROM H01DB012114  WHERE QYBH = :QYBH AND DWBH = :DWBH AND JXBZH <> '3')) ";
			$bind['DWBH'] = $filter['dwbh'];  //单位编号
		}
	
		//采购
		if ($filter['flg'] == '1'){
			//首营通过的药品和非药品
			$sql .= " AND (SHFYP = '1' AND SHPTG = '1' OR SHFYP = '0')";
		}
		
		//可用
		if($filter['status']=='0'){
			$sql .= " AND SHPZHT = '1' ";
		}
			
		//有条件时以条件为准无条件时以分类为准
		if ($filter['searchParams']['SEARCHKEY'] != "") {
			$sql .= " AND (SHPBH LIKE '%' || :SEARCHKEY || '%' OR lower(SHPMCH) LIKE '%' || :SEARCHKEY || '%' OR lower(ZHJM) LIKE '%' || lower(:SEARCHKEY) || '%' OR lower(HUAXUEMING) LIKE '%' || lower(:SEARCHKEY) || '%' OR lower(CHYM) LIKE '%' || lower(:SEARCHKEY) || '%' )";
			$bind['SEARCHKEY'] = strtolower($filter['searchParams']['SEARCHKEY']);
		}elseif ($filter ["flbm"] != ""){
			//分类编码
			$sql .= " AND FLBM IN(SELECT SHPFL FROM H01DB012109 WHERE QYBH = :QYBH START WITH SHPFL = :FLBM OR SHJFL = :FLBM CONNECT BY PRIOR SHPFL = SHJFL)";
		    $bind['FLBM'] = $filter ["flbm"];
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("GT_SHANGPIN",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",QYBH,SHPBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"],$bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"],$bind );
		return Common_Tool::createXml ( $recs,true,$totalCount, $filter ["posStart"] );
	}
	
	/*
	 * 根据商品编号取得商品信息
	 */
	public function getShangpinInfo($filter) {
		
		//检索SQL
		$sql = "SELECT " .
		       "SHPBH," . //商品编号
               "SHPMCH," . //商品名称
               "GUIGE," . //规格
               "BZHDWBH," . //包装单位编号
               "BZHDWMCH," . //包装单位
               "SHOUJIA," . //售价
               "HSHSHJ," . //含税售价
		       "LSHJ,".    //零售价
               "KOULV," . //扣率
               "SHUILV," . //税率
               "ZGSHJ," . //最高售价
               "SHPTM," . //商品条码
               "FLBM," . //分类编码
               "PZHWH," . //批准文号
               "JIXINGMCH," . //剂型
               "SHCHCHJ," . //生产厂家
               "CHANDI," . //产地
               "SHFOTCMCH," . //是否Otc
               "JLGG," . //计量规格
               "XDBZH " . //限定标志
               "FROM H01VIEW012101 " .
		       "WHERE QYBH = :QYBH AND SHPBH = :SHPBH " ;
			//销售
		if($filter['flg'] == '0'){
			//禁销商品
			$sql .= " AND SHPBH NOT IN (SELECT SHPBH FROM H01DB012114  WHERE QYBH = :QYBH AND DWBH = :DWBH AND JXBZH = '3') ";
			//限定商品
			$sql .= " AND (XDBZH = '0' OR XDBZH = '1' AND SHPBH IN (SELECT SHPBH FROM H01DB012114  WHERE QYBH = :QYBH AND DWBH = :DWBH AND JXBZH <> '3')) ";
			$bind['DWBH'] = $filter['dwbh'];  //单位编号
		}
	
		//采购
		if ($filter['flg'] == '1'){
			//首营通过的药品和非药品
			$sql .= " AND (SHFYP = '1' AND SHPTG = '1' OR SHFYP = '0')";
		}
		
		//可用
		if($filter['status']=='0'){
			$sql .= " AND SHPZHT = '1' ";
		}	
		
		$bind ['SHPBH'] = $filter ['shpbh']; //商品编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		return $this->_db->fetchRow ( $sql, $bind );
	}
    /*
	 * 自动完成数据取得
	 */
	public function getAutocompleteData($filter){
		
	   //检索SQL
		$sql = "SELECT SHPBH,SHPMCH,GUIGE,BZHDWMCH,SHCHCHJ,ZHJM,CHANDI ".
		       "FROM H01VIEW012101 A ".
		       "WHERE QYBH = :QYBH ";
		
		//销售
		if($filter['flg'] == '0'){
			//禁销商品
			$sql .= " AND SHPBH NOT IN (SELECT SHPBH FROM H01DB012114  WHERE QYBH = :QYBH AND DWBH = :DWBH AND JXBZH = '3') ";
			//限定商品
			$sql .= " AND (XDBZH = '0' OR XDBZH = '1' AND SHPBH IN (SELECT SHPBH FROM H01DB012114  WHERE QYBH = :QYBH AND DWBH = :DWBH AND JXBZH <> '3')) ";
			$bind['DWBH'] = $filter['dwbh'];  //单位编号
		}
		
		//采购
		if ($filter['flg'] == '1'){
			//首营通过的药品和非药品
			$sql .= " AND (A.SHFYP = '1' AND SHPTG = '1' OR SHFYP = '0')";
		}

		//可用
		if($filter['status']=='0'){
			$sql .= " AND SHPZHT = '1' ";
		}
			
		//查询条件	
		if ($filter ["searchkey"] != "") {
			$sql .= " AND (lower(SHPBH) LIKE :SEARCHKEY || '%' OR lower(SHPMCH) LIKE :SEARCHKEY || '%' OR lower(ZHJM) LIKE lower(:SEARCHKEY) || '%' )";
			$bind['SEARCHKEY'] = $filter ["searchkey"];
		}
		
		$sql .= " AND ROWNUM < 40";
		$sql .= " ORDER BY SHPBH";
	
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		return $this->_db->fetchAll($sql,$bind);
	}
}
