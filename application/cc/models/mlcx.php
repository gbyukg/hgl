<?php
/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：       毛利查询(mlcx)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/07/15
 ***** 更新履历：
 ******************************************************************/

class cc_models_mlcx extends Common_Model_Base {

	/**
	 * 得到毛利查询GRID列表XML数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter){
		//排序用字段名
		$fields = array ("", "SHPBH", "NLSSORT(SHPMCH,'NLS_SORT=SCHINESE_PINYIN_M')",);
		
		//检索SQL
		$sql = "SELECT A.SHPBH,"                          //商品编号
				."C.SHPMCH,"                              //商品名称
				."A.PIHAO,"                               //批号
				."A.SHULIANG AS XSSL,"                    //销售数量
				."A.HSHJ AS XSJE,"                        //销售金额
				."DECODE(E.SHULIANG,NULL,'0',E.SHULIANG) AS THSL,"       //退货数量
				."DECODE(E.HSHJ,NULL,'0',E.HSHJ) AS THJE,"               //退货金额
				."C.CHBJS "                               //成本计算
				."FROM H01DB012202 A "
				."LEFT JOIN H01DB012201 B ON A.QYBH = B.QYBH AND A.XSHDBH = B.XSHDBH "
				."LEFT JOIN H01DB012101 C ON A.QYBH = C.QYBH AND A.SHPBH = C.SHPBH "
				."LEFT JOIN H01DB012206 D ON A.QYBH = D.QYBH AND A.XSHDBH = D.XSHDBH "
				."LEFT JOIN H01DB012207 E ON A.QYBH = E.QYBH AND D.THDBH = E.THDBH AND E.SHPBH = A.SHPBH AND E.PIHAO = A.PIHAO "
				."WHERE A.QYBH = :QYBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter ["ksrq"] != "" || $filter ["zzrq"] != "")
		{
			$sql .= " AND :KSRQ <= TO_CHAR(B.KPRQ,'YYYY-MM-DD') AND TO_CHAR(B.KPRQ,'YYYY-MM-DD') <= :ZZRQ";
			$bind ['KSRQ'] = $filter ["ksrq"] == ""?"1900-01-01":$filter ["ksrq"];
			$bind ['ZZRQ'] = $filter ["zzrq"] == ""?"9999-12-31":$filter ["zzrq"];
		}
		
		//查询条件(出库单编号)
		if ($filter ["shpbh"] != "") {
			$sql .= " AND A.SHPBH LIKE '%' || :SHPBH || '%' ";
			$bind ['SHPBH'] = $filter ["shpbh"];
		}
		
		//查询条件(单位)
		if ($filter ["pihao"] != "") {
			$sql .= " AND A.PIHAO LIKE '%' || :PIHAO || '%' ";
			$bind ['PIHAO'] = $filter ["pihao"];
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_MLCX",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.SHPBH,A.PIHAO";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne( $pagedSql["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll( $pagedSql["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true, $totalCount, $filter["posStart"] );
	}
	
	
	/**
	 * 取得成本单价
	 * @param 	string 	$filter  参数数组
	 * 
	 * @return 	array 
	 */
	public function getchbdj($filter){
		
		if( $filter['chbjs'] == '001' ){         //商品累加
			
			$sql = "SELECT CHBDJ FROM H01DB012440 "
					."WHERE QYBH = :QYBH "
					."AND SHPBH = :SHPBH";
					
			//绑定查询条件
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] = $filter['shpbh'];
			
			return $this->_db->fetchOne( $sql, $bind );	
					
		}elseif( $filter['chbjs'] == '002' ){    //按商品批号累计
			
			$sql = "SELECT CHBDJ FROM H01DB012441 "
					."WHERE QYBH = :QYBH "
					."AND SHPBH = :SHPBH "
					."AND PIHAO = :PIHAO";
					
			//绑定查询条件
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] = $filter['shpbh'];
			$bind ['PIHAO'] = $filter['pihao'];
			
			return $this->_db->fetchOne( $sql, $bind );
			
		}else{
			
			return "暂无信息";
			
		}
		
	}
	
}