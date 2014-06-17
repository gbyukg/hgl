<?php
/******************************************************************
 ***** 模         块：       销售模块(XS)
 ***** 机         能：        销售订单详情(XSDDXQ)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/01/28
 ***** 更新履历：
 ******************************************************************/

class xs_models_xsddxq extends Common_Model_Base {
	/*
	 * 取得发货区信息
	 */
	public function getFHQInfo() {
		$sql = "SELECT FHQBH,FHQMCH FROM H01DB012422 WHERE QYBH = :QYBH AND FHQZHT = '1'";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$result = $this->_db->fetchPairs ( $sql, $bind );
		$result [''] = '--选择发货区--';
		ksort ( $result );
		return $result;
	}
	
	
	/**
	 * 销售订单信息获取
	 *
	 * @param string $bh
	 * @return array[]
	 */
	function getinfoData($bh){
		//检索SQL
		$sql = "SELECT TO_CHAR(T1.KPRQ,'YYYY-MM-DD') AS KPRQ,"        //开票日期
				."T1.XSHDBH,"      		   //单据编号
				."T1.SHFZZHSH,"            //是否增值税
				."T2.BMMCH,"     		   //部门名称
				."T3.XINGMING AS KPY,"     //开票员
				."T4.YGXM AS YWY,"         //业务员
				."T1.DWBH,"                //单位编号
				."T5.DWMCH,"               //单位名称
				."T1.DHHM,"     		   //电话号码
				."T1.DIZHI,"     		   //地址
				."T1.FHQBH,"     		   //发货区
				."T1.KOULV,"     		   //扣率
				."T1.BEIZHU "      		   //备注
			  ."FROM H01DB012201 T1, H01DB012112 T2, H01DB012107 T3, H01DB012113 T4, H01DB012106 T5 "
			  ."WHERE T1.QYBH = T2.QYBH "
			  ."AND T1.QYBH = T3.QYBH "
			  ."AND T1.QYBH = T4.QYBH "
			  ."AND T1.QYBH = T5.QYBH "
			  ."AND T1.QYBH = :QYBH "
			  ."AND T1.DWBH = T5.DWBH "
			  ."AND T1.BMBH = T2.BMBH "
			  ."AND T1.KPYBH = T3.YHID "
			  ."AND T1.YWYBH = T4.YGBH "
			  ."AND T1.XSHDBH = :XSHDBH ";

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
		$bind ['XSHDBH'] = $bh;                         //单据编号

		return $this->_db->fetchRow( $sql, $bind );
	}


	/*
	 * 销售订单明细信息
	 */
	public function getmingxi($filter) {
		//检索SQL
		$sql = "SELECT "
				."T1.SHPBH,"      		  //商品编号
				."T3.SHPMCH,"     		  //商品名称
				."T3.GUIGE,"      		  //规格
				."T4.NEIRONG AS BZHDWM,"  //包装单位
				."T1.PIHAO,"      		  //批号
				."TO_CHAR(T1.SHCHRQ,'yyyy-mm-dd') AS SHCHRQ,"   //生产日期
				."TO_CHAR(T1.BZHQZH,'yyyy-mm') AS BZHQZH,"   //保质期至
				."T1.BZHSHL,"     		  //包装数量
				."T1.LSSHL,"      		  //零散数量
				."T1.SHULIANG,"  		  //数量
				."T1.DANJIA,"  		      //单价
				."T1.HSHJ,"  		      //含税价
				."T1.KOULV,"  		      //扣率
				."T3.SHUILV,"  		      //税率
				."T1.HSHJE,"  		      //含税金额
				."T1.JINE,"  		      //金额
				."T1.SHUIE,"  		      //税额
				."T3.LSHJ,"     		  //零售价
				."T3.ZGSHJ,"     		  //最高售价
				."T3.SHPTM,"     		  //商品条码
				."T3.FLBM,"     		  //分类编码
				."T3.PZHWH,"     		  //批准文号
				."T5.NEIRONG AS JIXING,"  //剂型
				."T3.SHCHCHJ,"     		  //生产厂家
				."T3.CHANDI,"     		  //产地
				."T3.SHFOTC "     		  //是否OTC
			  ."FROM H01DB012202 T1 "
			  ."LEFT JOIN H01DB012201 T2 ON T1.QYBH = T2.QYBH AND T1.XSHDBH = T2.XSHDBH "
			  ."LEFT JOIN H01DB012101 T3 ON T1.QYBH = T3.QYBH AND T1.SHPBH = T3.SHPBH "
			  ."LEFT JOIN H01DB012001 T4 ON T1.QYBH = T4.QYBH AND T3.BZHDWBH = T4.ZIHAOMA AND T4.CHLID = 'DW'"
			  ."LEFT JOIN H01DB012001 T5 ON T1.QYBH = T5.QYBH AND T3.JIXING = T5.ZIHAOMA AND T5.CHLID = 'JX'"
			  ."WHERE T1.QYBH = :QYBH "
			  ."AND T1.XSHDBH = :XSHDBH "
			  ."ORDER BY T1.XUHAO ";

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;      //区域编号
		$bind ['XSHDBH'] = $filter ['bh'];              //单据编号
		
		return $this->_db->fetchAll( $sql, $bind );
	}
	
	
	/**
	 * 取得上下条销售订单详情
	 *
	 * @param string $bh   编号
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	function getxinxi($bh, $filter, $flg = 'current'){
		
		//排序用字段名
		$fields = array ( "","XSHDBH","KPRQ","DWBH","DWMCH","BMBH","BMMCH","YWYBH","YWYXM" );
		
		//检索SQL
		$sql_list = "SELECT ROWID,LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",XSHDBH) AS NEXTROWID,"
				."LAG(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",XSHDBH) AS PREVROWID,"
				."XSHDBH "
				."FROM H01DB012201 "
				."WHERE QYBH = :QYBH "       //区域编号
				."AND SHHZHT = '3' "         //审核状态      0: 不需审核； 1: 审核通过；2：审核不通过；3：待审核
				."AND QXBZH = '1' ";         //取消标准       1：正常                X：删除状态

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;

		//自动生成精确查询用Sql
		$sql_list .= Common_Tool::createFilterSql("XS_XSKPSH_DJ",$filter['filterParams'],$bind);
		
		//排序
		$sql_list .= " ORDER BY ".$fields[$filter ["orderby"]]." ".$filter["direction"];

		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql_list .= ",XSHDBH";
		
		//检索SQL
		$sql = "SELECT TO_CHAR(T1.KPRQ,'YYYY-MM-DD') AS KPRQ,"        //开票日期
				."T1.XSHDBH,"      		   //单据编号
				."T1.SHFZZHSH,"            //是否增值税
				."T2.BMMCH,"     		   //部门名称
				."T3.XINGMING AS KPY,"     //开票员
				."T4.YGXM AS YWY,"         //业务员
				."T1.DWBH,"                //单位编号
				."T5.DWMCH,"               //单位名称
				."T1.DHHM,"     		   //电话号码
				."T1.DIZHI,"     		   //地址
				."T1.FHQBH,"     		   //发货区
				."T1.KOULV,"     		   //扣率
				."T1.BEIZHU "      		   //备注
			  ."FROM H01DB012201 T1, H01DB012112 T2, H01DB012107 T3, H01DB012113 T4, H01DB012106 T5 "
			  ."WHERE T1.QYBH = T2.QYBH "
			  ."AND T1.QYBH = T3.QYBH "
			  ."AND T1.QYBH = T4.QYBH "
			  ."AND T1.QYBH = T5.QYBH "
			  ."AND T1.DWBH = T5.DWBH "
			  ."AND T1.BMBH = T2.BMBH "
			  ."AND T1.KPYBH = T3.YHID "
			  ."AND T1.YWYBH = T4.YGBH ";

		if ($flg == 'current') {
			$sql .= "AND T1.QYBH = :QYBH AND T1.XSHDBH = :XSHDBH ";
		} else if ($flg == 'next') {
			$sql .= "AND T1.ROWID = (SELECT NEXTROWID FROM (SELECT NEXTROWID,XSHDBH FROM ( $sql_list ) WHERE XSHDBH = :XSHDBH))";
		} else if ($flg == 'prev') {
			$sql .= "AND T1.ROWID = (SELECT PREVROWID FROM (SELECT PREVROWID,XSHDBH FROM ( $sql_list ) WHERE XSHDBH = :XSHDBH))";
		}

		//绑定查询条件
		$bind['XSHDBH'] = $bh;      //编号

		return $this->_db->fetchRow( $sql , $bind );
	}

}