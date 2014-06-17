<?php
/**********************************************************
 * 模块：    销售模块(XS)
 * 机能：    销售订单维护(XSDDWH)
 * 作成者：刘枞
 * 作成日：2011/01/30
 * 更新履历：
 **********************************************************/
class xs_models_xsddwh extends Common_Model_Base{
	/**
	 * 销售订单信息列表
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ("", "SHHZHT", "XSHDZHT", "XSHDBH", "KPRQ", "DWBH", "DWMCH", 
							"JINE", "SHUIE", "HSHJE", "BMMCH", "YWYXM", "KPYXM");
		//检索SQL
		$sql = "SELECT DECODE(SHHZHT,'0','未审核','1','审核通过','2','审核未通过','3','待审核') AS SHHZHT,".
		"DECODE(XSHDZHT,'0','未出库','1','已出库','2','客户已收') AS XSHDZHT,XSHDBH,TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ,".
		"DWBH,DWMCH,JINE,SHUIE,HSHJE,BMMCH,YWYXM,KPYXM,QYBH,QXBZH FROM ".
		"(SELECT A.SHHZHT,A.XSHDZHT,".
		"A.XSHDBH,A.KPRQ,A.DWBH,H.DWMCH,A.JINE,A.SHUIE,A.HSHJE,".
		"B.BMMCH,C.YGXM AS YWYXM,D.XINGMING AS KPYXM,A.QYBH,A.QXBZH ".
		"FROM H01DB012201 A ".
		"LEFT JOIN H01DB012112 B ON A.BMBH = B.BMBH AND A.QYBH = B.QYBH ".
		"LEFT JOIN H01DB012113 C ON A.YWYBH = C.YGBH AND A.QYBH = C.QYBH ".
		"LEFT JOIN H01DB012107 D ON A.KPYBH = D.YHID AND A.QYBH = D.QYBH ".
		"LEFT JOIN H01DB012106 H ON A.DWBH = H.DWBH AND A.QYBH = H.QYBH ".
		") WHERE QYBH = :QYBH AND QXBZH != 'X' ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查询条件
		if ($filter ["ksrq"] != "" || $filter ["zzrq"] != "")
		{
			$sql .= " AND :KSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :ZZRQ ";
			$bind ['KSRQ'] = $filter ["ksrq"] == ""?"1900-01-01":$filter ["ksrq"];
			$bind ['ZZRQ'] = $filter ["zzrq"] == ""?"9999-12-31":$filter ["zzrq"];
		}
		
		if ($filter ["dwbh"] != "") {
			$sql .= " AND ( DWBH LIKE '%' || :DWBH || '%')";
			$bind ['DWBH'] = $filter ["dwbh"];
		}
		
		if ($filter ["dwmch"] != "") {
			$sql .= " AND ( DWMCH LIKE '%' || :DWMCH || '%')";
			$bind ['DWMCH'] = $filter ["dwmch"];
		}
		
		if ($filter ["shsj"] != "1") {
			$sql .= " AND (SHHZHT = '0' OR SHHZHT = '3') ";
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("XS_XSDDWH_DJ",$filter['filterParams'],$bind);;
			
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",XSHDBH";

		$recs = $this->_db->fetchAll($sql,$bind);
		
		return Common_Tool::createXml( $recs, true );
	}
	

	/**
	 * 销售订单明细信息列表
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getMingxiGridData($filter){
		//检索SQL
		$sql = "SELECT A.SHPBH,B.SHPMCH,B.GUIGE,C.NEIRONG,B.JLGG,A.BZHSHL,A.LSSHL,A.SHULIANG,".
		 " A.DANJIA,A.HSHJ,A.KOULV,A.JINE,A.HSHJE,B.SHUILV,A.SHUIE,B.CHANDI,A.BEIZHU ". 
		 " FROM H01DB012202 A " .
	     " LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH " .
		 " LEFT JOIN H01DB012001 C ON B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' " .
		 " WHERE A.QYBH = :QYBH " .
		 " AND A.XSHDBH = :XSHDBH ";
		 
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['XSHDBH'] = $filter ['bh'];

		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $bind );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true, $totalCount, $filter ["posStart"] );
	}
	
	
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
		$fields = array ("", "SHHZHT", "XSHDZHT", "XSHDBH", "KPRQ", "DWBH", "DWMCH","JINE", "SHUIE", "HSHJE", "BMMCH", "YWYXM", "KPYXM");

		//检索集合
		$sql_list = "SELECT ROWID,LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",XSHDBH) AS NEXTROWID,".
		" LAG(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",XSHDBH) AS PREVROWID,".
		" XSHDBH FROM H01DB012201 ".
		" WHERE QYBH = :QYBH AND QXBZH != 'X'";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查询条件
		if ($filter ["ksrqkey"] != "" || $filter ["zzrqkey"] != "")
		{
			$sql_list .= " AND :KSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :ZZRQ ";
			$bind ['KSRQ'] = $filter ["ksrqkey"] == ""?"1900-01-01":$filter ["ksrqkey"];
			$bind ['ZZRQ'] = $filter ["zzrqkey"] == ""?"9999-12-31":$filter ["zzrqkey"];
		}
		
		if ($filter ["dwbhkey"] != "") {
			$sql_list .= " AND ( DWBH LIKE '%' || :DWBH || '%')";
			$bind ['DWBH'] = $filter ["dwbhkey"];
		}
		
		if ($filter ["dwmchkey"] != "") {
			$sql_list .= " AND ( DWMCH LIKE '%' || :DWMCH || '%')";
			$bind ['DWMCH'] = $filter ["dwmchkey"];
		}
		
		if ($filter ["shsj"] != "1") {
			$sql_list .= " AND (SHHZHT = '0' OR SHHZHT = '3') ";
		}
		
		//自动生成精确查询用Sql
		$sql_list .= Common_Tool::createFilterSql("XS_XSDDWH_DJ",$filter['filterParams'],$bind);

	  
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
	
	
	/*
	 * Check审批通过
	 */
	function shenpiCheck($dwbh) {
		$result ['status'] = '0';
		$sql = "SELECT SHPTG FROM H01DB012106 WHERE QYBH = :QYBH AND DWBH = :DWBH ";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'DWBH' => $dwbh );
		$spcheck = $this->_db->fetchRow ( $sql, $bind );
		if ($spcheck == 1) { //如果审批通过
		    //$result['status']
		} else {
			$result ['status'] = '02';
		}
		return $result;
	}
	
	
	public function getDanweiInfo($filter) {
		//检索SQL
		$sql =  " SELECT A.DWBH,A.DWMCH,A.DIZHI,A.DHHM,A.KOULV,A.FHQBH" . 
				" FROM H01DB012106 A" . " WHERE A.QYBH = :QYBH " . //区域编号
				" AND A.DWBH = :DWBH" . //单位编号
				" AND A.FDBSH ='0'" . //分店标识
				" AND A.SHFXSH = '1'" . //是否销售
				" AND A.KHZHT = '1'"; //客户状态
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
	
	/*
	 *更新销售订单取消标志
	 */
	function updataxsddzht($bh){
		$sql = "UPDATE H01DB012201 SET QXBZH = 'X' WHERE QYBH =:QYBH AND XSHDBH =:XSHDBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'XSHDBH' => $bh );
		return $this->_db->query( $sql,$bind );
	}
	
	
	/*
	 *库存相关数据更新（库存数量更新，商品移动履历）
	 */
	function updateKucun($bh){
		
		$sql = "SELECT QYBH,CKBH,KQBH,KWBH,SHPBH,PIHAO,RKDBH,BZHDWBH,SHULIANG,".
				   "TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,".
			 	   "TO_CHAR(BZHQZH,'YYYY-MM') AS BZHQZH,ZKZHT". 
			       " FROM H01DB012405 ".
			       " WHERE QYBH = :QYBH ".           //区域编号
                   " AND YDDH = :YDDH ";             //移动单号
		//绑定查询变量
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['YDDH'] = $bh;
		//当前明细行在库信息
		$recs = $this->_db->fetchAll( $sql, $bind );

		foreach ( $recs as $row ){	
			$sql_update = "UPDATE H01DB012404 "
						  ."SET ZZHCHKRQ = TO_DATE('9999/12/31 23:59:59','YYYY/MM/DD HH24:mi:ss'),"
						  ."SHULIANG = SHULIANG + :SHULIANG "
						  ."WHERE QYBH = :QYBH "
						  ."AND CKBH = :CKBH "
						  ."AND KQBH = :KQBH "
						  ."AND KWBH = :KWBH "
						  ."AND SHPBH = :SHPBH "
						  ."AND PIHAO = :PIHAO "
						  ."AND RKDBH = :RKDBH "
						  ."AND ZKZHT = :ZKZHT "
						  ."AND SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD') "
						  ."AND BZHDWBH = :BZHDWBH";
			//设定查询变量
			$bind_update ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind_update ['SHULIANG'] = $row['SHULIANG'] * -1;
			$bind_update ['CKBH'] = $row['CKBH'];
			$bind_update ['KQBH'] = $row['KQBH'];
			$bind_update ['KWBH'] = $row['KWBH'];
			$bind_update ['SHPBH'] = $row['SHPBH'];
			$bind_update ['PIHAO'] = $row['PIHAO'];
			$bind_update ['RKDBH'] = $row['RKDBH'];
			$bind_update ['ZKZHT'] = $row['ZKZHT'];
			$bind_update ['SHCHRQ'] = $row['SHCHRQ'];
			$bind_update ['BZHDWBH'] = $row['BZHDWBH'];

			$this->_db->query( $sql_update, $bind_update );
			
			
			$sql_xuhao = "SELECT MAX(XUHAO) FROM H01DB012405 ".
			       " WHERE QYBH = :QYBH ".           //区域编号
                   " AND YDDH = :YDDH ";             //移动单号
			//绑定查询变量
			$bind_xuhao ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind_xuhao ['YDDH'] = $bh;
			$xuhao = $this->_db->fetchOne( $sql_xuhao, $bind_xuhao );
			
			//生成在库移动履历
			$lvli ["QYBH"] = $_SESSION ['auth']->qybh;      //区域编号
			$lvli ["CKBH"] = $row['CKBH'];                  //仓库编号
			$lvli ["KQBH"] = $row['KQBH'];                  //库区编号
			$lvli ["KWBH"] = $row['KWBH'];                  //库位编号
			$lvli ["SHPBH"] = $row['SHPBH'];                //商品编号
			$lvli ["PIHAO"] = $row['PIHAO'];                //批号
			$lvli ["RKDBH"] = $row['RKDBH'];                //入库单号
			$lvli ["YDDH"] = $bh;                           //移动单号
			$lvli ["XUHAO"] = ++$xuhao ;                    //序号
			$lvli ['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$row['SHCHRQ']."','YYYY-MM-DD')"); //生产日期
			$lvli ['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$row['BZHQZH']."','YYYY-MM')");    //保质期至
			$lvli ['CHLSHJ'] = new Zend_Db_Expr('SYSDATE');
			$lvli ["SHULIANG"] = $row['SHULIANG'] * - 1;    //移动数量
			$lvli ["ZHYZHL"] = '22';                        //转移种类 [22：出库取消]
			$lvli ["BZHDWBH"] = $row['BZHDWBH'];            //包装单位编号
			$lvli ["ZKZHT"] = $row['ZKZHT'];                //在库状态
			$lvli['BGRQ'] = new Zend_Db_Expr('SYSDATE');    //变更日期
			$lvli['BGZH'] = $_SESSION ['auth']->userId;     //变更者
			$lvli ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$lvli ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			$this->_db->insert ( 'H01DB012405', $lvli );
		}
		
	}
	
}
