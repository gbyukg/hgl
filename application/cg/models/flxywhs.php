<?php
/*********************************
 * 模块：    采购模块(CG)
 * 机能：   返利协议维护商品(FLXYWHS)
 * 作成者：侯殊佳 
 * 作成日：2011/05/31
 * 更新履历：

 *********************************/
class cg_models_flxywhs extends Common_Model_Base {

	private $idx_ROWNUM = 0; // 行号
	private $idx_SHPBH = 1; // 商品编号
	private $idx_SHPMCH = 2; // 商品名称
	private $idx_GUIGE = 3; // 商品规格
	private $idx_BZHDWM = 4; // 包装单位
	private $idx_JLGG = 5; // 计量规格
	private $idx_CHANDI = 6; // 产地
	private $idx_QSRQ = 7; // 通用名	
	private $idx_ZZRQ = 8; // 包装单位编号
	private $idx_ZCLJSL = 9; // 序号
	private $idx_XYDJ = 10; // 序号
	private $idx_ZCLJJE = 11; // 序号
	private $idx_FLJE = 12; // 序号
	private $idx_BEIZHU = 13; // 备注
	
	
	public function getywybm (){
		$sql = "SELECT 
				A.YGBH,
				A.YGXM,
				A.SSBM,
				B.BMMCH
				FROM H01DB012113 A
				LEFT JOIN H01DB012112 B ON A.QYBH = B.QYBH AND A.SSBM = B.BMBH 
				WHERE A.QYBH = :QYBH AND A.YGBH = :YGBH";
				$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
				$bind ['YGBH'] = $_SESSION ['auth']->userId; //区域编号
				return $this->_db->fetchRow ($sql,$bind);
	}
	
	
	/**
	 * 获取返利协议维护商品明细信息列表
	 *
	 * @param 
	 * @return 
	 */
	
	public function getMingxiGridData($xybh, $filter) {
		//排序用字段名

		

		//检索SQL
		$sql = "SELECT A.SHPBH,B.SHPMCH,B.GUIGE,C.NEIRONG,B.JLGG,B.CHANDI,TO_CHAR(A.KSHRQ,'yyyy-mm-dd'),TO_CHAR(A.ZHZHRQ,'yyyy-mm-dd'),A.ZHCLJSHL,A.XYDJ,
		A.ZHCLJJE,A.FLJE,A.BEIZHU" . 
		 " FROM H01DB012314 A  LEFT  JOIN H01DB012101 B ON A.QYBH  =B.QYBH AND A.SHPBH = B.SHPBH  " .
		 " LEFT JOIN H01DB012001 C ON B.QYBH = C.QYBH  AND B.BZHDWBH=C.ZIHAOMA AND C.CHLID = 'DW'" .
		 " WHERE A.QYBH = :QYBH  AND A.XYBH = :XYBH AND A.ZHUANGTAI = '1' ORDER BY XYBH";
		;
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['XYBH'] = $xybh;
		
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true, $totalCount, $filter ["posStart"] );
	}
	
	
	/**
	 * 返利协议维护详情页面初始化
	 *
	 * @param 
	 * @return 
	 */
	public function getXymingxiData($xybh){
		$sql = "SELECT TO_CHAR(A.KPRQ,'yyyy-mm-dd') AS KPRQ,A.XYBH,A.BMBH,B.BMMCH,A.YWYBH,C.YGXM,TO_CHAR(A.KSHRQ,'yyyy-mm-dd') AS KSHRQ ,TO_CHAR(A.ZHZHRQ,'yyyy-mm-dd') AS ZHZHRQ,A.DWBH,D.DWMCH,A.DHHM,A.DIZHI,A.BEIZHU
				FROM H01DB012313 A 
				LEFT JOIN H01DB012112 B ON A.QYBH = B.QYBH AND A.BMBH = B.BMBH
				LEFT JOIN H01DB012113 C ON A.QYBH = C.QYBH AND A.YWYBH = C.YGBH
				LEFT JOIN H01DB012106 D ON A.QYBH = D.QYBH AND A.DWBH = D.DWBH 
				WHERE A. QYBH = :QYBH AND A.XYBH = :XYBH";
		//绑定查询条件
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['XYBH'] = $xybh;
			return $this->_db->fetchRow ( $sql, $bind );
		
	}
	
	
	
	public function getMingxiData($xybh) {
		//检索SQL
		$sql = "SELECT  A.SHPBH,B.SHPMCH,B.GUIGE,C.NEIRONG,B.JLGG,B.CHANDI,TO_CHAR(A.KSHRQ,'yyyy-mm-dd') AS KSHRQ,TO_CHAR(A.ZHZHRQ,'yyyy-mm-dd') AS ZHZHRQ,A.ZHCLJSHL,A.XYDJ,
		A.ZHCLJJE,A.FLJE,A.BEIZHU" . 
		 " FROM H01DB012314 A  LEFT  JOIN H01DB012101 B ON A.QYBH  =B.QYBH AND A.SHPBH = B.SHPBH  " .
		 " LEFT JOIN H01DB012001 C ON B.QYBH = C.QYBH  AND B.BZHDWBH=C.ZIHAOMA AND C.CHLID = 'DW'" .
		 " WHERE A.QYBH = :QYBH  AND A.XYBH = :XYBH";
		;
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['XYBH'] = $xybh;
		return $this->_db->fetchRow ( $sql, $bind );
	}
	/**
	 * 得到返利协议维护商品明细信息 
	 *
	 * @param array $filter
	 * @return string xml
	 */
	
	public function getFlxy($xybh)
	{
		//检索SQL
		$sql = "SELECT  A.SHPBH,B.SHPMCH,B.GUIGE,C.NEIRONG,B.JLGG,B.CHANDI,TO_CHAR(A.KSHRQ,'yyyy-mm-dd') AS KSHRQ,TO_CHAR(A.ZHZHRQ,'yyyy-mm-dd') AS ZHZHRQ,A.ZHCLJSHL,A.XYDJ,
		A.ZHCLJJE,A.FLJE,A.BEIZHU" . 
		 " FROM H01DB012314 A  LEFT  JOIN H01DB012101 B ON A.QYBH  =B.QYBH AND A.SHPBH = B.SHPBH  " .
		 " LEFT JOIN H01DB012001 C ON B.QYBH = C.QYBH  AND B.BZHDWBH=C.ZIHAOMA AND C.CHLID = 'DW'" .
		 " WHERE A.QYBH = :QYBH  AND A.XYBH = :XYBH AND A.ZHUANGTAI='1'";
		;
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['XYBH'] = $xybh;
		//return $this->_db->fetchRow ( $sql, $bind );
		
		$recs = $this->_db->fetchAll ( $sql, $bind );
		return Common_Tool::createXml ( $recs, true );
	}
	
	/**
	 * 获取返利协议维护信息列表
	 *
	 * @param array $filter
	 * @return string xml
	 */
	
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ("","ZHUANGTAI","XYBH", "DWBH","KPRQ", "KSHRQ", "ZHZHRQ");
		

		//检索SQL
		$sql = "SELECT 
				DECODE(A.ZHUANGTAI,'X','禁用','1','正常','未知') AS ZHUANGTAI,
				A.XYBH,
				A.DWBH,
				TO_CHAR(A.KPRQ,'yyyy-mm-dd '),
				TO_CHAR(A.KSHRQ,'yyyy-mm-dd'),
				TO_CHAR(A.ZHZHRQ,'yyyy-mm-dd'),
				A.BEIZHU
				FROM H01DB012313 A
				WHERE A.QYBH = :QYBH";  //AND A.SHHZHT =:SHZHT
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		if ($filter ['searchParams']["SERCHDWBH"] != "") {
			$sql .= " AND( DWBH LIKE '%' || :DWBH || '%')";
			$bind ['DWBH'] = $filter ['searchParams']["SERCHDWBH"];
		}
		
		if ($filter ['searchParams']["SERCHSPBH"] != "") {
			$sql .= " AND A.XYBH IN (SELECT B.XYBH FROM H01DB012314 B WHERE A.QYBH = B.QYBH AND B.SHPBH = :SHPBH )";
			$bind ['SHPBH'] = $filter ['searchParams']["SERCHSPBH"];
		}
		
		//查询条件是否显示历史数据
		if($filter ['searchParams']['LSSJ']!='on'){
			$sql .= " AND TO_CHAR(A.ZHZHRQ,'YYYY-MM-DD')  > TO_CHAR(sysdate,'YYYY-MM-DD')";
		}
		
		   //自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CG_FLXYWHS",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",XYBH";
		$recs = $this->_db->fetchAll($sql,$bind);
		return Common_Tool::createXml ( $recs, true );
	}
 	
		
	/**
	 *上下条信息检索
	 *
	 * @param string $xybh 返利协议订单编号

	 * @param array $filter 查询排序条件
	 * @param string $flg 查找方向  current,next,prev
	 * @return array 
	 */
	
	function getXyxx($xybh,$filter,$flg='current'){
		$fields = array ("","ZHUANGTAI","XYBH", "DWBH","KPRQ", "KSHRQ", "ZHZHRQ");
		
 		$sql_list = "SELECT ROWID,LEAD(ROWID) OVER (ORDER BY  " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",XYBH) AS NEXTROWID,
					 LAG(ROWID) OVER(ORDER BY  " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] ." ,XYBH) AS PREVROWID 
					 ,XYBH
					 FROM H01DB012313 A
					 WHERE A.QYBH = :QYBH ";
 					
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
	if ($filter['searchParams'] ["DWBH"] == "" && $filter['searchParams'] ["SHPBH"] == "")
		{
			$sql_list ;
		}
		
		if ($filter['searchParams'] ["SERCHDWBH"] != "") {
			$sql_list .= " AND A.DWBH = :DWBH";
			$bind ['DWBH'] = $filter['searchParams'] ["SERCHDWBH"];
		}
		
		if ($filter['searchParams'] ["SERCHSPBH"] != "") {
			$sql_list .= " AND A.XYBH IN (SELECT B.XYBH FROM H01DB012314 B WHERE A.QYBH = B.QYBH AND B.SHPBH = :SHPBH )";
			$bind ['SHPBH'] = $filter['filterParams'] ["SERCHSPBH"];
		}
	
		
		
		//查询条件是否审核
		if($filter['searchParams'] ['LSSJ']!='on'){
			$sql_list .= " AND TO_CHAR(A.ZHZHRQ,'YYYY-MM-DD')  > TO_CHAR(sysdate,'YYYY-MM-DD')";
		}
 		
 		$sql = " SELECT A.KPRQ, A.XYBH, A.BMBH, A.BMMCH, A.YWYBH, A.YGXM, A.KSHRQ , 
 				A.ZHZHRQ, A.DWBH, A.DWMCH, A.DHHM, A.DIZHI, A.BEIZHU FROM H01UV012302 A";
 		
 		if ($flg == 'current') {
			$sql .= " WHERE  QYBH =:QYBH AND XYBH =:XYBH";
		} else if ($flg == 'next') {
			$sql .= " WHERE FLXROWID = (SELECT NEXTROWID FROM (SELECT NEXTROWID,XYBH FROM ( $sql_list ) WHERE XYBH = :XYBH))";
		} else if ($flg == 'prev') {
			$sql .= " WHERE FLXROWID = (SELECT PREVROWID FROM (SELECT PREVROWID,XYBH FROM ( $sql_list ) WHERE XYBH = :XYBH))";
		}
		$bind['XYBH']= $xybh;
		return $this->_db->fetchRow ( $sql, $bind );
 		
 	}
	
	/*
	 * 根据单位编号取得单位信息
	 * 
	 * @param array $filter
	 * @return string array
	 */
	public function getDanweiInfo($filter) {
		//检索SQL
		$sql =  " SELECT A.DWBH,A.DWMCH,A.DIZHI,A.DHHM,A.KOULV,A.FHQBH" . 
				" FROM H01DB012106 A" . " WHERE A.QYBH = :QYBH " . //区域编号
				" AND A.DWBH = :DWBH" . //单位编号
				" AND A.SHFJH = '1'" . //是否采购
				" AND A.KHZHT = '1'"; //客户状态
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
	
		/**
	 * 协议锁定和解锁
	 *
	 * @param string $xybh  协议编号
	 * @param string $xyzht 协议状态
	 * @return unknown
	 */
	function updateStatus($xybh, $xyzht) {
		$sql = "UPDATE H01DB012313 " .
		       " SET ZHUANGTAI = :XYZHT" .
		       " WHERE QYBH =:QYBH AND XYBH =:XYBH";
		
		$bind['QYBH'] =$_SESSION ['auth']->qybh;
		$bind['XYBH']= $xybh;
		$bind['XYZHT'] = $xyzht;
		return $this->_db->query ( $sql, $bind );
	
	}	

}	
	
	