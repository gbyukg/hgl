<?php
/*********************************
 * 模块：    销售模块(XS)
 * 机能：    销售退货查询(XSTHCX)
 * 作成者：孙宏志
 * 作成日：2011/01/14
 * 更新履历：
 *********************************/
class xs_models_xsthcx extends Common_Model_Base {
	private $_xsdbh = null; 	//销售单编号
	private $idx_ROWNUM = 0; 	//行号
	private $idx_XUANZE = 1; 	//选择
	private $idx_SHPBH = 2; 	//商品编号
	private $idx_GUIGE = 3; 	//规格
	private $idx_BZHDWM = 4; 	//包装单位
	private $idx_HWMCH = 5; 	//货位
	private $idx_PIHAO = 6; 	//批号
	private $idx_SHCHRQ = 7; 	//生产日期
	private $idx_BZHQZH = 8; 	//保质期至
	private $idx_JLGG = 9; 		//计量规格
	private $idx_BZHSHL = 10; 	//包装数量
	private $idx_LSSHL = 11; 	//零散数量
	private $idx_KTSHL = 12;
	private $idx_SHULIANG = 13; //数量
	private $idx_DANJIA = 14; 	//单价
	private $idx_HSHJ = 15; 	//含税售价
	private $idx_KOULV = 16; 	//扣率
	private $idx_SHUILV = 17; 	//税率
	private $idx_HSHJE = 18; 	//含税金额
	private $idx_JINE = 19; 	//金额
	private $idx_SHUIE = 20; 	//税额
	private $idx_LSHJ = 21; 	//零售价
	private $idx_ZGSHJ = 22; 	//最高售价
	private $idx_SHPTM = 23; 	//商品条码
	private $idx_FLBM = 24; 	//分类编码
	private $idx_PZHWH = 25; 	//批准文号
	private $idx_JIXINGM = 26; 	//剂型
	private $idx_SHCHCHJ = 27; 	//生产厂家
	private $idx_CHANDI = 28; 	//产地
	private $idx_SHFOTC = 29; 	//是否otc
	private $idx_TYMCH = 30;	//通用名

	/*
	 * 选中明细行的商品信息
	 */
	public function getXiangQing($filter) {
		//检索SQL
		$sql = "SELECT A.TYMCH,A.CHANDI,TO_CHAR(B.SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,B.PIHAO " .
               " FROM H01DB012101 A LEFT JOIN H01DB012207 B ON A.QYBH=B.QYBH AND A.SHPBH=B.SHPBH AND B.THDBH=:THDBH ".
               " WHERE A.QYBH = :QYBH " . //区域编号
               " AND A.SHPBH = :SHPBH"; //单位编号
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['SHPBH'] = $filter ['spbh'];
		$bind ['THDBH'] = $filter ['thdbh'];
		return $this->_db->fetchRow ( $sql, $bind );
	}

	/*
	 * 根据单位编号编号取得单位信息
	 */
	public function getDanweiInfo($filter) {
		//检索SQL
		$sql = "SELECT DWBH,DWMCH " .
               " FROM H01DB012106 A WHERE A.QYBH = :QYBH " . //区域编号
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
	 * 取得退货单信息
	 */
	public function getTHD($filter) {
				//排序用字段名
		$fields = array ("", "THDBH", "THRQ", "DWBH","NLSSORT(DWMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "XSHDBH", "XSHRQ", "NLSSORT(BMMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "NLSSORT(YWYXM,'NLS_SORT=SCHINESE_PINYIN_M')");
		//检索SQL
		$sql = "SELECT THDBH,TO_CHAR(KPRQ,'YYYY-MM-DD') AS THRQ,DWBH,DWMCH,XSHDBH,".
				"TO_CHAR(XSHRQ,'YYYY-MM-DD') AS XSHRQ,BMMCH,YWYXM,QYBH FROM ".
				"(SELECT T1.THDBH,T1.KPRQ,T1.DWBH,T1.DWMCH,T1.XSHDBH,".
				"T2.KPRQ AS XSHRQ, T1.BMMCH,T1.YWYXM,T1.QYBH ".
                "FROM H01VIEW012206 T1 ".
			    "LEFT JOIN H01DB012201 T2 ON T1.QYBH = T2.QYBH AND T1.XSHDBH = T2.XSHDBH )".
                "WHERE QYBH = :QYBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
		
		//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter ["kshrq"] != "" || $filter ["zhzhrq"] != "")
		{
			$sql .= " AND :KSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :ZZRQ ";
			$bind ['KSRQ'] = $filter ["kshrq"] == ""?"1900-01-01":$filter ["kshrq"];
			$bind ['ZZRQ'] = $filter ["zhzhrq"] == ""?"9999-12-31":$filter ["zhzhrq"];
		}
		
		//查询条件(单位编号输入)
		if ($filter ['dwbh'] != "") {
			$sql .= " AND DWBH = :DWBH ";
			$bind ['DWBH'] = $filter ['dwbh'];
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("XS_XSTHCX_DJ",$filter['filterParams'],$bind);;
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",THDBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		return Common_Tool::createXml($this->_db->fetchAll( $sql, $bind ),true,$totalCount, $filter ["posStart"]);
	}

	/*
	 * 取得退货单详细信息
	 */
	public function getTHDXX($filter) {
		//检索SQL
		$sql = "SELECT ".
				  "A.SHPBH,".
				  "B.SHPMCH,".
				  "B.GUIGE,".
				  "C.NEIRONG AS BZHDW,".
				  "A.BZHSHL,".
				  "A.LSSHL,".
				  "A.SHULIANG,".
				  "A.DANJIA,".
				  "A.HSHJ,".
				  "A.KOULV,".
				  "A.JINE,".		
				  "B.SHUILV,".
				  "A.SHUIE,".
				  "A.HSHJE,".
				  "A.BEIZHU, ".
				  "A.PIHAO,".
				  "TO_CHAR(A.SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,".
				  "B.TYMCH,".
		          "B.CHANDI ".
               "FROM H01DB012207 A ".
               "LEFT JOIN H01DB012101 B ON A.QYBH=B.QYBH AND A.SHPBH=B.SHPBH ".
		       "LEFT JOIN H01DB012001 C ON A.QYBH=C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' ".
               "WHERE A.QYBH=:QYBH ".
		       "AND A.THDBH=:THDBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
		$bind ['THDBH'] = $filter ['thdh'];    //区域编号
		
		return Common_Tool::createXml($this->_db->fetchAll( $sql, $bind ),true);
	}
	
	/**
	 * 取得退货单据信息（详情）
	 *
	 * @param string $thdbh 退货单编号
	 * @param array $filter 查询排序条件
	 * @param string $flg 查找方向  current,next,prev
	 * @return array 
	 */
	function getTHDNR($thdbh, $filter, $flg) {
		//排序用字段名
		$fields = array ("", "THDBH", "THRQ", "DWBH","NLSSORT(DWMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "XSHDBH", "XSHRQ", "NLSSORT(BMMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "NLSSORT(YWYXM,'NLS_SORT=SCHINESE_PINYIN_M')");

		//检索SQL--取上下条关系
		$sql_list = "SELECT THDBH,ROWID,".
					"LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",THDBH) AS NEXTROWID, ".
			        "LAG(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",THDBH) AS PREVROWID ".  
					"FROM H01DB012206 ".
               		"WHERE QYBH=:QYBH ";
				
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;

		//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter ["ksrqkey"] != "" || $filter ["zzrqkey"] != "")
		{
			$sql_list .= " AND :KSRQKEY <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :ZZRQKEY";
			$bind ['KSRQKEY'] = $filter ["ksrqkey"] == ""?"1900-01-01":$filter ["ksrqkey"];
			$bind ['ZZRQKEY'] = $filter ["zzrqkey"] == ""?"9999-12-31":$filter ["zzrqkey"];
		}

		//查询条件(审核状态)
		if ($filter ["thshzt"] != "") {
			$sql_list .= " AND SHHZHT = :SHHZHT";
			$bind ['SHHZHT'] = $filter ["thshzt"];
		}

		//查询条件(单位编号)
		if ($filter ["dwbhkey"] != "") {
			$sql_list .= " AND DWBH = :DWBHKEY";
			$bind ['DWBHKEY'] = $filter ["dwbhkey"];
		}
		
		if ($filter ["BJ"] == "0") {
			//自动生成精确查询用Sql
			$sql_list .= Common_Tool::createFilterSql("XS_XSTHCX_DJ",$filter['filterParams'],$bind);
		} else {
			//自动生成精确查询用Sql
			$sql_list .= "AND SHHZHT = '0' AND THDZHT = '0' ";    
			$sql_list .= Common_Tool::createFilterSql("XS_XSTHSH_DJ",$filter['filterParams'],$bind);
		}

		//检索SQL--退货单信息H01DB012206
		$sql_single = "SELECT TO_CHAR(T1.KPRQ,'YYYY-MM-DD') AS THRQ,T1.THDBH,T1.SHFZZHSH,T1.XSHDBH,T2.BMMCH,T4.YGXM AS YEWUYUAN,T1.DWBH,T5.DWMCH,T6.YGXM AS KPYUAN,T1.DHHM,T1.DIZHI,T1.FKFSH,T1.KOULV,T1.BEIZHU " .
					"FROM H01DB012206 T1 ".
               		"LEFT JOIN H01DB012112 T2 ON T1.QYBH=T2.QYBH AND T1.BMBH=T2.BMBH ".
		       		"LEFT JOIN H01DB012106 T5 ON T1.QYBH=T5.QYBH AND T1.DWBH=T5.DWBH ".
			   		"LEFT JOIN H01DB012113 T4 ON T1.QYBH=T4.QYBH AND T1.YWYBH=T4.YGBH ".
			   		"LEFT JOIN H01DB012201 T3 ON T1.QYBH=T3.QYBH AND T1.XSHDBH=T3.XSHDBH ".
			   		"LEFT JOIN H01DB012113 T6 ON T1.QYBH=T4.QYBH AND T1.KPYBH=T6.YGBH ".
               		"WHERE T1.QYBH=:QYBH ";
		
		if ($flg == 'current') {
			$sql_single .= " AND T1.THDBH =:THDBH";
			//绑定数组数超过需要绑定数，检索不出来
			//unset ( $bind ['KSRQKEY'] );
			//unset ( $bind ['ZZRQKEY'] );
			//unset ( $bind ['DWBHKEY'] );
			//unset ( $bind ['RKDBHKEY'] );
		} else if ($flg == 'next') {
			$sql_single .= " AND T1.ROWID = (SELECT NEXTROWID FROM (SELECT NEXTROWID,THDBH FROM ( $sql_list ) WHERE THDBH = :THDBH))";
		} else if ($flg == 'prev') {
			$sql_single .= " AND T1.ROWID = (SELECT PREVROWID FROM (SELECT PREVROWID,THDBH FROM ( $sql_list ) WHERE THDBH = :THDBH))";
		}
		//绑定查询条件
		$bind ['THDBH'] = $thdbh;
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
}	