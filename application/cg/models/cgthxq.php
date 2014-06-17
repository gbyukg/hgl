<?php
/******************************************************************
 ***** 模         块：       采购模块(CG)
 ***** 机         能：       采购退货详情(CGTHXQ)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/01/14
 ***** 更新履历：
 *****
 ******************************************************************/

class cg_models_cgthxq extends Common_Model_Base {
	
	/**
	 * 根据采购退货单编号取得采购退货单信息
	 * 
	 * @param array $filter
	 * @return array
	 */
	public function getInfo($bh){
		//检索SQL
		$sql = "SELECT TO_CHAR(A.KPRQ,'yyyy-mm-dd') AS KPRQ,A.CGTHDBH,A.YRKDBH,C.BMMCH,B.YGXM,A.DWBH,"
			  ."D.DWMCH,A.DIZHI,A.DHHM,A.SHFZZHSH,TO_CHAR(A.KOULV,'fm990.00') AS KOULV,A.FKFSH,A.SHFPS,A.BEIZHU "
			  ."FROM H01DB012308 A "
			  ."LEFT JOIN H01DB012113 B ON A.QYBH = B.QYBH AND A.YWYBH = B.YGBH "
			  ."LEFT JOIN H01DB012112 C ON A.QYBH = C.QYBH AND A.BMBH = C.BMBH "
			  ."LEFT JOIN H01DB012106 D ON A.QYBH = D.QYBH AND A.DWBH = D.DWBH "
			  ."WHERE A.QYBH = :QYBH "        //区域编号
			  ."AND A.CGTHDBH = :CGTHDBH ";   //采购退货单编号

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth'] -> qybh;
		$bind ['CGTHDBH'] = $bh;
		return $this->_db->fetchRow( $sql, $bind );
	}
	
	
	/**
	 * 根据采购退货单编号得到采购退货单明细列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getMingxiData($filter){
		//排序用字段名
		$fields = array ("", "A.SHPBH", "NLSSORT(B.SHPMCH,'NLS_SORT=SCHINESE_PINYIN_M')");

		$sql = "SELECT "          
				."A.SHPBH,"      		  //商品编号
				."B.SHPMCH,"     		  //商品名称
				."B.GUIGE,"      		  //规格
				."C.NEIRONG AS BZHDWM,"   //包装单位
				."A.PIHAO,"      		  //批号
				."TO_CHAR(A.SHCHRQ,'yyyy-mm-dd') AS SHCHRQ,"     	  //生产日期
				."TO_CHAR(A.BZHQZH,'yyyy-mm') AS BZHQZH,"     		  //保质期至
				."A.SHULIANG,"  		  //数量
				."A.DANJIA,"      	      //单价
				."A.HSHJ,"       		  //含税价
				."A.KOULV,"               //扣率
				."B.SHUILV,"    	 	  //税率
				."A.HSHJE,"     	 	  //含税金额
				."A.JINE,"      	 	  //金额
				."A.SHUIE,"      		  //税额
				."A.BEIZHU "      	      //备注
			  ."FROM H01DB012309 A "
			  ."LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "  
			  ."LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' "
			  ."WHERE A.QYBH = :QYBH "         //区域编号
			  ."AND A.CGTHDBH = :CGTHDBH ";    //采购退货单编号  例：CGT10121300001

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGTHDBH'] = $filter ["bh"];
				
		//排序
		$sql .= " ORDER BY " . $fields [$filter["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.CGTHDBH,A.XUHAO";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne( $pagedSql["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll( $pagedSql["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true,$totalCount, $filter["posStart"] );
		
	}
	
	/**
	 * 取得上下条采购退货详情
	 *
	 * @param string $bh   编号
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	function getxinxi($bh, $filter, $flg = 'current'){
		//排序用字段名
		$fields = array ("", "CGTHDBH", "KPRQ", "NLSSORT(BMMCH,'NLS_SORT=SCHINESE_PINYIN_M')",
						 "NLSSORT(YWYXM,'NLS_SORT=SCHINESE_PINYIN_M')", 
						 "NLSSORT(DWMCH,'NLS_SORT=SCHINESE_PINYIN_M')",
						 "DHHM","DIZHI","BEIZHU","SHHZHT","THDZHT");
		
		//检索集合
		$sql_list = "SELECT ROWID,LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",CGTHDBH) AS NEXTROWID,".
		            "LAG(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",CGTHDBH) AS PREVROWID,".
					"CGTHDBH FROM H01VIEW012308 WHERE QYBH = :QYBH ";

		//审核标识
		if ($filter ["sh"] == "1") {
			$sql_list .= "AND SHHZHT = '0' AND QXBZH = '1' ";  
		}
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;

		//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter ["ksrqkey"] != "" || $filter ["zzrqkey"] != "")
		{
			$sql_list .= " AND :KSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :ZZRQ";
			$bind ['KSRQ'] = $filter ["ksrqkey"] == ""?"1900-01-01":$filter ["ksrqkey"];
			$bind ['ZZRQ'] = $filter ["zzrqkey"] == ""?"9999-12-31":$filter ["zzrqkey"];
		}

		//查询条件(单位编号输入)
		if ($filter ["dwbhkey"] != "") {
			$sql_list .= " AND DWBH LIKE '%' || :DWBH || '%'";
			$bind ['DWBH'] = $filter ["dwbhkey"];
		}

		//查询条件(单位编号没输入,只输入单位名称)
		if($filter ["dwbhkey"] == "" && $filter ["dwmchkey"] != ""){
			$sql_list .= " AND DWMCH LIKE '%' || :DWMCH || '%'";
			$bind ['DWMCH'] = $filter ["dwmchkey"];
		}

		if ($filter ["BJ"] == "0") {
			//自动生成精确查询用Sql
			$sql_list .= Common_Tool::createFilterSql("CG_CGTHCX_DJ",$filter['filterParams'],$bind);
		} else {
			//自动生成精确查询用Sql
			$sql_list .= Common_Tool::createFilterSql("CG_CGTHSH_DJ",$filter['filterParams'],$bind);
		}
		
		//排序
		$sql_list .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];

		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql_list .= ",CGTHDBH";
			  
		//检索SQL
		$sql = "SELECT TO_CHAR(X.KPRQ,'yyyy-mm-dd') AS KPRQ,X.CGTHDBH,X.YRKDBH,Z.BMMCH,Y.YGXM,X.DWBH,"
			  ."O.DWMCH,X.DIZHI,X.DHHM,X.SHFZZHSH,TO_CHAR(X.KOULV,'fm990.00') AS KOULV,X.FKFSH,X.SHFPS,X.BEIZHU "
			  ."FROM H01DB012308 X "
			  ."LEFT JOIN H01DB012113 Y ON X.QYBH = Y.QYBH AND X.YWYBH = Y.YGBH "
			  ."LEFT JOIN H01DB012112 Z ON X.QYBH = Z.QYBH AND X.BMBH = Z.BMBH "
			  ."LEFT JOIN H01DB012106 O ON X.QYBH = O.QYBH AND X.DWBH = O.DWBH ";
		if ($flg == 'current') {
			$sql .= " WHERE  X.QYBH =:QYBH AND X.CGTHDBH =:CGTHDBH";
		} else if ($flg == 'next') {
			$sql .= "WHERE X.ROWID = (SELECT NEXTROWID FROM (SELECT NEXTROWID,CGTHDBH FROM ( $sql_list ) WHERE CGTHDBH = :CGTHDBH))";
		} else if ($flg == 'prev') {
			$sql .= "WHERE X.ROWID = (SELECT PREVROWID FROM (SELECT PREVROWID,CGTHDBH FROM ( $sql_list ) WHERE CGTHDBH = :CGTHDBH))";
		}

		//绑定查询条件
		$bind['CGTHDBH'] = $bh;      //编号

		return $this->_db->fetchRow( $sql , $bind );
	}
	
	
}