<?php
/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：       库间调拨返库库维护(kjdbfkwh)
 ***** 作  成  者：        姚磊
 ***** 作  成  日：        2011/01/25
 ***** 更新履历：
 ******************************************************************/

class cc_models_kjdbfkwh extends Common_Model_Base {

	/**
	 * 得到退货单列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridDanjuData($filter){
		//排序用字段名
		$fields = array ("", "DJBH","CHKDZHT" ,"DCCK", "DRCK", "KPRQ", "DYDBCHKD",
						 "NLSSORT(YGXM,'NLS_SORT=SCHINESE_PINYIN_M')",
						 "NLSSORT(BMMCH,'NLS_SORT=SCHINESE_PINYIN_M')",
						 "BGRQ", "BGZH");

		//检索SQL
		$sql = "SELECT DJBH,DECODE(CHKDZHT,'1','已返库','2','已入库') AS CHKDZHT,DCHCKMCH, DRCKMCH,TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ,"
				."DYDBCHKD,YWYXM,BMMCH,TO_CHAR(BGRQ,'YYYY-MM-DD') AS BGRQ,BGZH "
				."FROM H01VIEW012423  "
				."WHERE QYBH = :QYBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
			//查询条件(调拨返库单据号)
			if($filter['searchParams']['DBRKD']!=""){
				$sql .= " AND( DJBH LIKE '%' || :SEARCHKEY || '%')";
				$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['DBRKD']);
			}
			
		
			//查询条件(调出仓库)
	
			if($filter['searchParams']['DCCK']!=""){
			$sql .= " AND( DCHCK LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(DCHCKMCH) LIKE '%' || :SEARCHKEYDCCK || '%')";
			$bind ['SEARCHKEYDCCK'] = strtolower($filter ["searchParams"]['DCCK']);
			}

		//查询条件(调入仓库)
		if($filter['searchParams']['DRCK']!=""){
			$sql .= " AND( DRCK LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(DRCKMCH) LIKE '%' || :SEARCHKEYDRCK || '%')";
			$bind ['SEARCHKEYDRCK'] = strtolower($filter ["searchParams"]['DRCK']);
			}
			
		//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter['searchParams']["KSRQKEY"] != "" || $filter['searchParams'] ["ZZRQKEY"] != "")
		{
			$sql .= " AND :KSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :ZZRQ";
			$bind ['KSRQ'] = $filter['searchParams'] ["KSRQKEY"] == ""?"1900-01-01":$filter['searchParams'] ["KSRQKEY"];
			$bind ['ZZRQ'] = $filter['searchParams'] ["ZZRQKEY"] == ""?"9999-12-31":$filter['searchParams'] ["ZZRQKEY"];
		}
		
			
			
		//查询条件(对应调拨出库单)
		if ($filter['searchParams']["DBCKD"] != "") {
			$sql .= " AND DYDBCHKD LIKE '%' || :SEARCHKEYDYDBCHKD || '%' ";
			$bind ['SEARCHKEYDYDBCHKD'] = $filter['searchParams']["DBCKD"];
		}

		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_KJDBFK_WH",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",DJBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true,$totalCount, $filter ["posStart"] );
	}
	
	
	/**
	 * 库间调拨出库单选择页面--单据列表数据获取
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridthdData($filter){
		//排序用字段名
		$fields = array ("", "A.DJBH","CHKDZHT","DCCK", "DRCK", "KPRQ", "A.DRCKDZH", "A.DHHM",
						 "NLSSORT(D.YGXM,'NLS_SORT=SCHINESE_PINYIN_M')",
						 "NLSSORT(E.BMMCH,'NLS_SORT=SCHINESE_PINYIN_M')",
						 "BGRQ", "A.BGZH");
		
		//检索SQL
		$sql = "SELECT A.DJBH,DECODE(A.CHKDZHT,'1','已返库','2','已入库') AS CHKDZHT,B.CKMCH AS DCCK,C.CKMCH AS DRCK,TO_CHAR(A.KPRQ,'YYYY-MM-DD') AS KPRQ,"
				."A.DRCKDZH,A.DHHM,D.YGXM,E.BMMCH,TO_CHAR(A.BGRQ,'YYYY-MM-DD') AS BGRQ,A.BGZH "
				."FROM H01DB012410 A "
				."LEFT JOIN H01DB012401 B ON A.QYBH = B.QYBH AND A.DCHCK = B.CKBH "
				."LEFT JOIN H01DB012401 C ON A.QYBH = B.QYBH AND A.DRCK = C.CKBH "
				."LEFT JOIN H01DB012113 D ON A.QYBH = D.QYBH AND A.YWYBH = D.YGBH "
				."LEFT JOIN H01DB012112 E ON A.QYBH = E.QYBH AND A.BMBH = E.BMBH "
				."WHERE A.QYBH = :QYBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter ["ksrqkey"] != "" || $filter ["zzrqkey"] != "")
		{
			$sql .= " AND :KSRQ <= TO_CHAR(A.KPRQ,'YYYY-MM-DD') AND TO_CHAR(A.KPRQ,'YYYY-MM-DD') <= :ZZRQ";
			$bind ['KSRQ'] = $filter ["ksrqkey"] == ""?"1900-01-01":$filter ["ksrqkey"];
			$bind ['ZZRQ'] = $filter ["zzrqkey"] == ""?"9999-12-31":$filter ["zzrqkey"];
		}
		
		if ($filter ["dcck"] != "") {
			$sql .= " AND A.DCHCK = :DCHCK";
			$bind ['DCHCK'] = $filter ["dcck"];
		}
		
		if($filter ["drck"] != "") {
			$sql .= " AND A.DRCK = :DRCK ";
			$bind ['DRCK'] = $filter ["drck"];
		}
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.DJBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true,$totalCount, $filter ["posStart"] );
	}
	/**
	 * 判断数据是否正确
	 *
	 * @param unknown_type $djbh 盘点编号
	 * @param unknown_type $filter 关联页面内容
	 * @return bool
	 */
	function getkjdbckdcxOne($djbh){
			
			$sql = "SELECT " .
					" H1.DJBH,". //单据编号
					" TO_CHAR(H1.KPRQ,'YYYY-MM-DD') AS KPRQ,". //开票日期
					" H1.YWYBH,". //业务员编号
					" H1.BMBH,". //部门编号
					" H1.DCHCK,". //调出仓库
					" H1.DRCK,". //调入仓库
					" H1.DRCKDZH,". //调入仓库地址
					" H1.SHFPS,". //是否配送
					" H1.DHHM,". //电话号码
					" H1.BEIZHU,". //备注
					" H1.CHKDZHT ". //出库单状态
			        " FROM H01DB012410  H1" .
			        " WHERE H1.QYBH =:QYBH AND H1.DJBH =:DJBH AND H1.CHKDZHT = :CHKDZHT";
			
			$bind = array('QYBH'=>$_SESSION ['auth']->qybh, 'DJBH' => $djbh ,'CHKDZHT'=>'3');
			
			$recs = $this->_db->fetchRow($sql,$bind);
	
			return $recs;

	}
}