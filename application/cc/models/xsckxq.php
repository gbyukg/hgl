<?php
/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：       销售出库详情(XSCKXQ)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2010/12/29
 ***** 更新履历：
 *****
 ******************************************************************/

class cc_models_xsckxq extends Common_Model_Base {
	/**
	 * 取得发货区信息
	 */
	public function getFHQInfo(){
		$sql = "SELECT FHQBH,FHQMCH FROM H01DB012422 WHERE QYBH = :QYBH";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$result = $this->_db->fetchPairs ( $sql, $bind );
		$result [''] = '--选择发货区--';
		ksort ( $result );
		return $result;
	}
	
	
	/**
	 * 根据出库单编号取得出库单信息
	 * 
	 * @param array $filter
	 * @return array
	 */
	public function getxsckInfo($ckdbh){
		//检索SQL
		$sql = "SELECT TO_CHAR(A.KPRQ,'yyyy-mm-dd') AS KPRQ,A.CHKDBH,A.CKDBH,C.BMMCH,B.YGXM,A.DWBH,"
			  ."D.DWMCH,A.DIZHI,A.DHHM,A.SHFZZHSH,A.KOULV,A.FHQBH,A.FKFSH,A.SHFPS,A.BEIZHU "
			  ."FROM H01DB012408 A "
			  ."LEFT JOIN H01DB012113 B ON A.QYBH = B.QYBH AND A.YWYBH = B.YGBH "
			  ."LEFT JOIN H01DB012112 C ON A.QYBH = C.QYBH AND A.BMBH = C.BMBH "
			  ."LEFT JOIN H01DB012106 D ON A.QYBH = D.QYBH AND A.DWBH = D.DWBH "
			  ."WHERE A.QYBH = :QYBH "       //区域编号
			  ."AND A.CHKDBH = :CHKDBH "     //出库单编号
			  ."AND A.CHKLX = '1' ";         //出库类型     固定值：‘1’销售出库

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth'] -> qybh;
		$bind ['CHKDBH'] = $ckdbh;
		return $this->_db->fetchRow( $sql, $bind );
	}
	
	
	/**
	 * 根据出库单编号得到出库单明细列表数据
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
				."D.CKMCH || E.KQMCH || F.KWMCH AS HWMCH,"       //货位名称
				."A.PIHAO,"      		  //批号
				."TO_CHAR(A.SHCHRQ,'yyyy-mm-dd') AS SHCHRQ,"     //生产日期
				."TO_CHAR(A.BZHQZH,'yyyy-mm-dd') AS BZHQZH,"     //保质期至
				."A.BZHSHL,"     		  //包装数量
				."A.LSSHL,"      		  //零散数量
				."A.SHULIANG,"  		  //数量
				."A.DANJIA,"      	      //单价
				."A.HSHJ,"       		  //含税价
				."A.KOULV,"               //扣率
				."B.SHUILV,"    	 	  //税率
				."A.HSHJE,"     	 	  //含税金额
				."A.JINE,"      	 	  //金额
				."A.SHUIE,"      		  //税额
				."A.BEIZHU "      	      //备注
			  ."FROM H01DB012409 A "
			  ."LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "  
			  ."LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' "
			  ."LEFT JOIN H01DB012401 D ON A.QYBH = D.QYBH AND A.CKBH = D.CKBH "
			  ."LEFT JOIN H01DB012402 E ON A.QYBH = E.QYBH AND A.CKBH = E.CKBH AND A.KQBH = E.KQBH "
			  ."LEFT JOIN H01DB012403 F ON A.QYBH = F.QYBH AND A.CKBH = F.CKBH AND A.KQBH = F.KQBH AND A.KWBH = F.KWBH "
			  ."WHERE A.QYBH = :QYBH "       //区域编号
			  ."AND A.CHKDBH = :CHKDBH ";    //出库单编号
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CHKDBH'] = $filter ["ckdbh"];
				
		//排序
		$sql .= " ORDER BY " . $fields [$filter["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.CHKDBH,A.XUHAO";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne( $pagedSql["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll( $pagedSql["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true,$totalCount, $filter["posStart"] );
	}
}