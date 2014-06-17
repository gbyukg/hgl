<?php
/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：       补货上架确认(bhshjqr)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/07/19
 ***** 更新履历：
 ******************************************************************/

class cc_models_bhshjqr extends Common_Model_Base {
	
	/*
	 * 取得传送带出口信息
	 */
	public function getCHSDCHK() {
		
		$sql = "SELECT CHSDCHK,CHSDCHK FROM H01DB012443 "
				."WHERE QYBH = :QYBH "
				."AND CKBH = :CKBH "
				."AND ZHUANGTAI = '1'";
				
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CKBH'] = $_SESSION ['auth']->ckbh;
		
		$result = $this->_db->fetchPairs ( $sql, $bind );
		
		$result [''] = '--传送带出口--';
		
		ksort ( $result );
		
		return $result;
	}
	
	
	/**
	 * 得到毛利查询GRID列表XML数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter){
		//排序用字段名
		$fields = array ("", "BHDBH", "SHPBH", "NLSSORT(SHPMCH,'NLS_SORT=SCHINESE_PINYIN_M')");
		
		//检索SQL
		$sql = "SELECT A.BHDBH,"                          //补货单编号
				."A.SHPBH,"                               //商品编号
				."B.SHPMCH,"                              //商品名称
				."A.PIHAO,"                               //批号
				."SUM(A.BHSHL) AS SHULIANG,"              //补货数量
				."D.CKMCH,"                               //移入仓库
				."A.YRKW,"                                //移入库位
				."DECODE(A.BHLX,'1','统一补货','2','随单自动补货','3','手动补货') AS BHLX, "       //补货类型
				."'确认^javascript:QueRen(' || '\"' || A.BHDBH || '\"' || ',' || '\"' || A.SHPBH || '\"' || ',"
				."' || '\"' || A.PIHAO || '\"' || ',' || '\"' || A.YRCK || '\"' || ',' || '\"' || A.YRKW || '\"' || ')^_self' "
				."FROM H01DB012450 A "
				."LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "
				."LEFT JOIN H01DB012403 C ON A.QYBH = C.QYBH AND A.YRKW = C.KWBH AND A.YRKQ = C.KQBH AND A.YRCK = C.CKBH "
				."LEFT JOIN H01DB012401 D ON A.QYBH = D.QYBH AND A.YRCK = D.CKBH "
				."WHERE A.QYBH = :QYBH "
				."AND A.ZHUANGTAI = '1' ";
		
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查询条件(出库单编号)
		if ($filter ["shpbh"] != "") {
			$sql .= " AND A.SHPBH LIKE '%' || :SHPBH || '%' ";
			$bind ['SHPBH'] = $filter["shpbh"];
		}
		
		//查询条件(单位)
		if ($filter ["chsdchk"] != "") {
			$sql .= " AND C.CHSDCHK = :CHSDCHK ";
			$bind ['CHSDCHK'] = $filter["chsdchk"];
		}

		//分组
		$sql .= " GROUP BY A.BHDBH,A.SHPBH,B.SHPMCH,A.PIHAO,D.CKMCH,A.YRKW,A.BHLX,A.YRCK ";
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.BHDBH,A.SHPBH";
		
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
	 * 确认操作
	 * @param 	string 	$filter  参数数组
	 * 
	 * @return 	array 
	 */
	public function queren($filter){
			
		$sql = "UPDATE H01DB012450 "
				."SET ZHUANGTAI = '2',"
				."BGZH = :BGZH,"
				."BGRQ = SYSDATE "
				."WHERE QYBH = :QYBH "
				."AND BHDBH = :BHDBH "
				."AND SHPBH = :SHPBH "
				."AND PIHAO = :PIHAO "
				."AND YRCK = :YRCK "
				."AND YRKW = :YRKW ";
				
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['BGZH'] = $_SESSION ['auth']->userid;
		$bind ['BHDBH'] = $filter['bhdbh'];
		$bind ['SHPBH'] = $filter['shpbh'];
		$bind ['PIHAO'] = $filter['pihao'];
		$bind ['YRCK'] = $filter['yrck'];
		$bind ['YRKW'] = $filter['yrkw'];
		
		return $this->_db->query( $sql, $bind );
		
	}


}