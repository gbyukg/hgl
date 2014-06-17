<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：  库间调拨出库单(kjdbckdcx)
 * 作成者：dltt
 * 作成日：2010-01-26 10:23:51
 * 更新履历：

 *********************************/
class cc_models_kjdbckdcx extends Common_Model_Base {

	
	/*
	 * 列表数据取得(xml格式)
	 */
	function getListData($filter) {
		
		$fields = array ("", "DJBH","CHKDZHT","OUTCK","INCK","KPRQ"); 
		
		//检索SQL
		$sql = "SELECT " .
			" DJBH,". //单据编号
			" DECODE(CHKDZHT,'1','已出库','2','部分处理','3','已完成') AS CHKDZHT,". //出库单状态
			" DCHCK AS OUTCK,". //调出仓库
			" DRCK AS INCK,". //调入仓库
			" TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ,". //开票日期
			" YWYXM AS YG,". //业务员编号
			" BMMCH,". //部门编号
			" TO_CHAR(BGRQ,'YYYY-MM-DD'),". //变更日期
			" BGZHXM AS CREATER". //变更者
			" FROM H01VIEW012410 ".
//			" LEFT JOIN H01DB012401 H2 ON  H1.QYBH =  H2.QYBH AND  H1.DCHCK =  H2.CKBH " . 
//			" LEFT JOIN H01DB012401 H6 ON  H1.QYBH =  H6.QYBH AND  H1.DRCK =  H6.CKBH " . 
//			" LEFT JOIN H01DB012113 H3 ON  H1.QYBH =  H3.QYBH AND  H1.YWYBH =  H3.YGBH " . 
//			" LEFT JOIN H01DB012112 H4 ON  H1.QYBH =  H4.QYBH AND  H1.BMBH =  H4.BMBH " . 
//			" LEFT JOIN H01DB012113 H5 ON  H1.QYBH =  H5.QYBH AND  H1.BGZH =  H5.YGBH " . 
			" WHERE QYBH = :QYBH " ;
		
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件 单据编号
		if ($filter ['searchParams']["DJBH"] != "") {
			$sql .= " AND LOWER(DJBH) LIKE LOWER('%' || :DJBH || '%') ";
			$bind ['DJBH'] =$filter ['searchParams']["DJBH"];
		}

		//调出仓库
		if ($filter ['searchParams']["DCHCK"] != "" && $filter ['searchParams']["DCHCK"] != "--双击选择调出仓库--") {
			$sql .= " AND DCHCK = :DCHCK  ";
			$bind ['DCHCK'] =$filter ['searchParams']["DCHCK"];
		}
		
		//调入仓库
		if ($filter ['searchParams']["DRCK"] != "" && $filter ['searchParams']["DRCK"] != "--双击选择调入仓库--") {
			$sql .= " AND DRCK = :DRCK  ";
			$bind ['DRCK'] =$filter ['searchParams']["DRCK"];
		}
		
		//开始时间
		if ($filter ['searchParams']["KPRQ_S"] != "") {
			$sql .= " AND KPRQ  >= to_date(:KPRQ_S,'yyyy-mm-dd')   ";
			$bind ['KPRQ_S'] =$filter ['searchParams']['KPRQ_S'];
		}
	    //结束时间
		if ($filter ['searchParams']["KPRQ_E"] != "") {
			$sql .= " AND KPRQ  <= to_date(:KPRQ_E,'yyyy-mm-dd')   ";
			$bind ['KPRQ_E'] =$filter ['searchParams']['KPRQ_E'];
		}
		
		//未完成单据
		if ($filter ['searchParams']["CHKDZHT"] != "") {
			$sql .= " AND CHKDZHT <> '3' ";
		}
		
		//自动生成精确查询用Sql
        $sql .= Common_Tool::createFilterSql("CC_KJDBCKDCX",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
	     
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"],$bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"] ,$bind);
		
		return Common_Tool::createXml($recs,true,$totalCount,$filter["posStart"]);
	
	}
	
	/**
	 * 判断数据是否正确
	 *
	 * @param unknown_type $djbh 盘点编号
	 * @param unknown_type $filter 关联页面内容
	 * @return bool
	 */
	function getkjdbckdcxOne($djbh){
			
//			$sql = "SELECT " .
//					" H1.DJBH,". //单据编号
//					" TO_CHAR(H1.KPRQ,'YYYY-MM-DD') AS KPRQ,". //开票日期
//					" H1.YWYBH,". //业务员编号
//					" H1.BMBH,". //部门编号
//					" H1.DCHCK,". //调出仓库
//					" H1.DRCK,". //调入仓库
//					" H1.DRCKDZH,". //调入仓库地址
//					" H1.SHFPS,". //是否配送
//					" H1.DHHM,". //电话号码
//					" H1.BEIZHU,". //备注
//					" H1.CHKDZHT ". //出库单状态
//			        " FROM H01DB012410  H1" .
//			        " WHERE H1.QYBH =:QYBH AND H1.DJBH =:DJBH AND H1.CHKDZHT = :CHKDZHT";

		$sql = "SELECT " .
                    " DJBH,". //单据编号
                    " TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ,". //开票日期
                    " YWYXM,". //业务员编号
                    " BMMCH,". //部门编号
                    " DCHCKMCH,". //调出仓库
                    " DRCKMCH,". //调入仓库
                    " DRCKDZH,". //调入仓库地址
                    " SHFPS,". //是否配送
                    " DHHM,". //电话号码
                    " BEIZHU,". //备注
                    " CHKDZHT ". //出库单状态
                    " FROM H01VIEW012410" .
                    " WHERE QYBH =:QYBH AND DJBH =:DJBH AND CHKDZHT = :CHKDZHT";
			
//			$bind = array('QYBH'=>$_SESSION ['auth']->qybh, 'DJBH' => $djbh ,'CHKDZHT'=>'3');
			$bind["QYBH"] = $_SESSION['auth']->qybh;
			$bind["DJBH"] = $djbh;
			$bind["CHKDZHT"] = '3';
			
			$recs = $this->_db->fetchRow($sql,$bind);
	
			return $recs;

	}
	/**
	 * 获得一条调出单据的信息
	 *
	 * @param unknown_type $djbh 调出编号
	 * @return bool
	 */
	function getkjdbckOne($djbh){
			
			$sql = "SELECT " .
					" DJBH,". //单据编号
					" TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ,". //开票日期
					" YWYXM AS YWYBH,". //业务员编号
					" BMMCH AS BMBH,". //部门编号
					" DCHCKMCH AS DCHCK,". //调出仓库
					" DCHCK AS DCHCKBH,". //调出仓库
					" DRCKMCH AS DRCK,". //调入仓库
					" DRCKDZH,". //调入仓库地址
					" SHFPS,". //是否配送
					" DHHM,". //电话号码
					" BEIZHU,". //备注
					" CHKDZHT ". //出库单状态
			        " FROM H01VIEW012410 " .
//					" LEFT JOIN H01DB012401 H4 ON  H1.QYBH =  H4.QYBH And  H1.DCHCK = H4.CKBH  " . 
//					" LEFT JOIN H01DB012113 H7 ON  H1.QYBH =  H7.QYBH And  H1.YWYBH =  H7.YGBH " . 
//					" LEFT JOIN H01DB012112 H8 ON  H1.QYBH =  H8.QYBH And  H1.BMBH =  H8.BMBH " .
//					" LEFT JOIN H01DB012401 H9 ON  H1.QYBH =  H9.QYBH And  H9.CKBH =  H1.DRCK " . 
			        " WHERE QYBH =:QYBH AND DJBH =:DJBH ";
			 
			$bind = array('QYBH'=>$_SESSION ['auth']->qybh, 'DJBH' => $djbh );
			
			$recs = $this->_db->fetchRow($sql,$bind);
	
			return $recs;

	}
	
	/**
	 * getDetailData获得调拨出库明细列表
	 *
	 * @param unknown_type $djbh 单据号
	 * @param unknown_type $filter
	 * @return unknown
	 */
	function getDetailData($djbh,$filter){
		
//		$sql = "SELECT " .
//				" H1.SHPBH,". //商品编号
//				" H7.SHPMCH,". //商品编号
//				" H7.GUIGE,". //商品规格
//				" H7.BZHDWMCH,". //包装单位编号
//				" (H3.KQMCH || H4.KWMCH) AS KW1,". //调出库位
//			    " (H5.KQMCH || H6.KWMCH) AS KW2,". //调出库位
//				" H1.PIHAO,". //批号
//				" TO_CHAR(H1.SHCHRQ,'YYYY-MM-DD'),". //生产日期
//				" TO_CHAR(H1.BZHQZH,'YYYY-MM') ,". //保质期至
//				" to_char(H1.BZHSHL,'999g999g990'),". //包装数量
//				" to_char(H1.LSSHL,'999g999g990'),". //零散数量
//				" to_char(H1.SHULIANG,'999g999g990'),". //数量
//				" to_char(H1.WSHHSHL,'999g999g990'),". //未收货数量
//				" to_char(H1.THSHL,'999g999g990'),". //退货数量
//				" H1.BEIZHU,". //备注
//				" （SELECT SUM(H1.SHULIANG)  from H01DB012411 H1 Where H1.QYBH = :QYBH AND H1.DJBH = :DJBH） AS COUNT ". //数量
//				" from H01DB012411 H1 ".
//				 
//				" LEFT JOIN H01DB012402 H3 ON  H1.QYBH =  H3.QYBH And  H3.CKBH = :CKBH And  H1.DCHKQ =  H3.KQBH " .
//				" LEFT JOIN H01DB012403 H4 ON  H1.QYBH =  H4.QYBH And  H4.CKBH = :CKBH And  H1.DCHKQ =  H4.KQBH And  H1.DCHKW =  H4.KWBH " . 
//				" LEFT JOIN H01DB012402 H5 ON  H1.QYBH =  H5.QYBH And  H5.CKBH = :CKBH And  H1.DRKQ =  H5.KQBH " .
//				" LEFT JOIN H01DB012403 H6 ON  H1.QYBH =  H6.QYBH And  H6.CKBH = :CKBH And  H1.DRKQ =  H6.KQBH And  H1.DRKW =  H6.KWBH " .
//				" LEFT JOIN H01VIEW012001 H7 ON  H1.QYBH =  H7.QYBH And  H1.SHPBH =  H7.SHPBH " . 
//				" Where  H1.QYBH = :QYBH AND H1.DJBH = :DJBH" ;

		$sql = "SELECT " .
                " SHPBH,". //商品编号
                " SHPMCH,". //商品编号
                " GUIGE,". //商品规格
                " BZHDWMCH,". //包装单位编号
                " (DCHKQMCH || DCHKWMCH) AS KW1,". //调出库位
                " (DRKQMCH || DRKWMCH) AS KW2,". //调入库位
                " PIHAO,". //批号
                " TO_CHAR(SHCHRQ,'YYYY-MM-DD'),". //生产日期
                " TO_CHAR(BZHQZH,'YYYY-MM') ,". //保质期至
                " to_char(BZHSHL,'999g999g990'),". //包装数量
                " to_char(LSSHL,'999g999g990'),". //零散数量
                " to_char(SHULIANG,'999g999g990'),". //数量
                " to_char(WSHHSHL,'999g999g990'),". //未收货数量
                " to_char(THSHL,'999g999g990'),". //退货数量
                " BEIZHU,". //备注
                " （SELECT SUM(SHULIANG)  from H01DB012411 Where QYBH = :QYBH AND DJBH = :DJBH） AS COUNT ". //数量
                " from HO1UV012401 ".
                " Where  QYBH = :QYBH AND DJBH = :DJBH" ;
		
//		$bind = array('QYBH'=>$_SESSION ['auth']->qybh, 'DJBH' => $djbh ,'CKBH'=> $filter['DCHCK']);

		$bind["QYBH"] = $_SESSION['auth']->qybh;
		$bind["DJBH"] = $djbh;
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
	     
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"],$bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"] ,$bind);
		
		return Common_Tool::createXml($recs,true,$totalCount,$filter["posStart"]);
						
	}
	
}
