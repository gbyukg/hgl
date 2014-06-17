<?php
/*********************************
 * 模块：    采购模块(cg)
 * 机能：    采购订单维护(CGDDWH)
 * 作成者：姚磊
 * 作成日：2011/1/20
 * 更新履历：
 *********************************/
class cg_models_cgddwh extends Common_Model_Base {

	private $idx_ROWNUM = 0; // 行号
	private $idx_SHPBH = 1; // 商品编号
	private $idx_SHPMCH = 2; // 商品名称
	private $idx_GUIGE = 3; // 商品规格
	private $idx_BZHDWM = 4; // 包装单位
	private $idx_JLGG = 5; // 计量规格
	private $idx_BZHSHL = 6; // 包装数量
	private $idx_LSSHL = 7; // 零散数量
	private $idx_SHULIANG = 8; // 数量
	private $idx_CHANDI = 9; // 产地
	private $idx_BEIZHU =10; // 备注	

	

	private $idxx_ROWNUM = 0; // 行号
	private $idxx_SHHZHT=1;//审核状态
	private $idxx_CGGZHDBH = 2;//采购开票单编号
	private $idxx_KPRQ = 3;// 开票日期
	private $idxx_DWBH = 4;// 单位编号
	private $idxx_DWMCH = 5;// 单位名称
	private $idxx_BMMCH = 6;// 部门名称
	private $idxx_YWUY = 7;// 业务员
	private $idxx_CZUY = 8;// 操作员
	private $idxx_DHHM = 9;// 电话号码
	private $idxx_YDHRQ = 10;// 预到货日期
	private $idxx_SHFZZHSH = 11;// 是否是增值税
	private $idxx_DIZHI = 12;// 地址
	private $idxx_BEIZHU = 13;// 备注
	private $idxx_KOULV = 14;// 扣率
	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck() {
		if ($_POST ["KPRQ"] == "" || //开票日期
			//$_POST ["BMBH"] == "" || //部门编号
			$_POST ["DWBH"] == "" || //单位编号
			$_POST ["DWMCH"] == "" || //单位名称
			$_POST ["YWYBH"] == "" || //业务员编号   
			$_POST ["#grid_mingxi"] == "") { //明细表格
			return false;
		}
		$isHasMingxi = false; //是否存在至少一条明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] != "") {
				$isHasMingxi = true;
				if ($grid [$this->idx_SHULIANG] == "" || //数量
					$grid [$this->idx_SHULIANG] == "0") {
					return false;
				}
			}
		}
		
		//一条明细也没有输入
		if (! $isHasMingxi) {
			return false;
		}
		
		return true;
	}
	
	/*
	 * 数据合法性逻辑性验证
	 */
	public function logicCheck() {
		
		//单位合法性
		

		$filter ['dwbh'] = $_POST ['DWBH'];
		if ($this->getDanweiInfo ( $filter ) == FALSE) {
			return false;
		}
		
		//商品合法性
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == '')
				continue;
			$filter ['shpbh'] = $grid [$this->idx_SHPBH];
			if ($this->getShangpinInfo ( $filter ) == FALSE) {
				return false;
			}
		}
		
		return true;
	}
	
	
	/**
	 * 得到采购订单维护明细信息列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	
	public function getMingxiGridData($cggzhdbh, $filter) {
		//排序用字段名

		

		//检索SQL
		$sql = "SELECT A.SHPBH,B.SHPMCH,B.GUIGE,C.NEIRONG,B.JLGG,A.BZHSHL,A.LSSHL,A.SHULIANG,".
		 " B.CHANDI ,A.BEIZHU" . 
		 " FROM H01DB012302 A  LEFT OUTER JOIN H01DB012101 B ON A.QYBH  =B.QYBH AND A.SHPBH = B.SHPBH  " .
		 " LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH  AND B.BZHDWBH=C.ZIHAOMA AND C.CHLID = 'DW'" .
		 " WHERE A.QYBH = :QYBH  AND A.CGDBH = :CGDBH";
		;
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGDBH'] = $cggzhdbh;

		
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
	 * 采购订单维护列表
	 *
	 * @param array $filter
	 * @return string xml
	 */
	
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ("", "CGDDBH", "KPRQ", "DWBH", "DWMCH", "BMMCH","YGXM","BGRQ", "KPYXM");
		

		//检索SQL
		$sql = "SELECT CGDDBH,TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ,DWBH,DWMCH,BMMCH,".
		" YWYXM AS CZUY,KPYXM,DHHM ,DIZHI, BEIZHU ".
		" FROM H01VIEW012301   " .
		" WHERE QYBH =:QYBH  AND QXBZH ='1'";  //AND A.SHHZHT =:SHZHT
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;

		
	//查询条件(开始日期<=开票日期<=终止日期)
		
		if ($filter['searchParams']["SERCHKSRQ"] != "" || $filter['searchParams']["SERCHJSRQ"] != "")
		{
			$sql .= " AND :SERCHKSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD')AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :SERCHJSRQ ";
			$bind ['SERCHKSRQ'] = $filter['searchParams']["SERCHKSRQ"] == ""?"1900-01-01":$filter['searchParams']["SERCHKSRQ"];
			$bind ['SERCHJSRQ'] = $filter['searchParams']["SERCHJSRQ"] == ""?"9999-12-31":$filter['searchParams']["SERCHJSRQ"];
		}
		
					
		//serchdwbh 单位编号
		if ($filter['searchParams']["SERCHDWBH"] != "") {
			$sql .= " AND(DWBH LIKE '%' || :SERCHDWBH || '%')";  				//单位编号模糊查询
			$bind ['SERCHDWBH'] = $filter['searchParams']["SERCHDWBH"];
		}
		//serchdwmch 单位名称
		if ($filter['searchParams']["SERCHDWMCH"] != "") {
			$sql .= " AND(DWMCH LIKE '%' || :SERCHDWMCH || '%')"; 		//单位名称模糊查询
			$bind ['SERCHDWMCH'] = $filter['searchParams']["SERCHDWMCH"];
		}
		$sql .= Common_Tool::createFilterSql("CG_CGDDWH",$filter['filterParams'],$bind);	
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",CGDDBH";
		$recs = $this->_db->fetchAll($sql,$bind);
		return Common_Tool::createXml ( $recs, true );
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
	 *更新采购订单维护取消标志
	 */
	function upCgdata($cggzhdbh){
		
		$sql = "UPDATE H01DB012301 SET QXBZH = 'X' WHERE QYBH =:QYBH AND CGDDBH =:CGDDBH";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGDDBH'] = $cggzhdbh;
		$this->_db->query ( $sql, $bind );
		return true;
	}
	/*
	 * 采购订单明细列表
	 */
	function getXiangqing($cgkpdbh){
		
		$sql = "SELECT A.CGDBH,TO_CHAR(A.KPRQ,'YYYY-MM-DD') AS KPRQ,A.DWBH,E.DWMCH,B.BMMCH,C.YGXM,".
		 " A.DHHM,TO_CHAR(A.YDHRQ,'YYYY-MM-DD')AS YDHRQ,A.SHFZZHSH ,A.DIZHI, ".
		 " A.BEIZHU,A.KOULV   FROM H01DB012306 A  LEFT OUTER JOIN H01DB012112 B ON A.QYBH  =B.QYBH AND A.BMBH = B.BMBH ".
		 " LEFT JOIN H01DB012113 C ON A.QYBH = C.QYBH   AND A.YWYBH=C.YGBH ".
		 " LEFT JOIN H01DB012106 E ON A.QYBH = E.QYBH AND A.DWBH = E.DWBH ".
		 " WHERE A.QYBH = :QYBH  AND A.CGDBH =:CGDBH " ;
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'CGDBH' => $cgkpdbh );
		$recs = $this->_db->fetchRow ( $sql, $bind );
		return $recs;
	}
	
	/*
	 * 采购开票详情明细
	 */
	function xiangxiMingxi($cgkpdbh){
		
		 $sql = "SELECT A.SHPBH,B.SHPMCH,B.GUIGE,C.NEIRONG,B.JLGG,A.BZHSHL,A.LSSHL,A.SHULIANG,".
		 " B.CHANDI,A.BEIZHU " . 
		 " FROM H01DB012302 A  LEFT OUTER JOIN H01DB012101 B ON A.QYBH  =B.QYBH AND A.SHPBH = B.SHPBH  " .
		 " LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH  AND B.BZHDWBH=C.ZIHAOMA AND C.CHLID = 'DW'" .
		 " WHERE A.QYBH = :QYBH  AND A.CGDBH = :CGDBH";
		 $bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'CGDBH' => $cgkpdbh );
		 $recs = $this->_db->fetchAll($sql,$bind);
		return Common_Tool::createXml ( $recs, true );
		
	}
	
	
/**
	 *上下条信息检索
	 *
	 * @param string $cgdbh 采购订单编号

	 * @param array $filter 查询排序条件
	 * @param string $flg 查找方向  current,next,prev
	 * @return array 
	 */
	function getCgdxx($cgdbh, $filter, $flg = 'current') {
		//排序用字段名
		$fields = array ("", "CGDDBH", "KPRQ", "DWBH", "DWMCH", "BMMCH", "YGXM","BGRQ","KPYXM");
				
		//检索SQL--取上下条关系
		$sql_list = " SELECT ROWID, LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",CGDDBH) AS NEXTROWID,"  . 
					" LAG(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",CGDDBH) AS PREVROWID"  . 
					" ,CGDDBH FROM H01DB012301   " .
					" WHERE QYBH = :QYBH  AND QXBZH ='1'";
			      
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
	//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter['searchParams']["SERCHKSRQ"] != "" || $filter['searchParams']["SERCHJSRQ"] != "")
		{
			$sql_list .= " AND :SERCHKSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD')AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :SERCHJSRQ ";
			$bind ['SERCHKSRQ'] = $filter['searchParams']["SERCHKSRQ"] == ""?"1900-01-01":$filter['searchParams']["SERCHKSRQ"];
			$bind ['SERCHJSRQ'] = $filter['searchParams']["SERCHJSRQ"] == ""?"9999-12-31":$filter['searchParams']["SERCHJSRQ"];
		}
					
		//serchdwbh 单位编号
		if ($filter['searchParams']["SERCHDWBH"] != "") {
			$sql_list .= " AND(DWBH LIKE '%' || :SERCHDWBH || '%')";  				//单位编号模糊查询
			$bind ['SERCHDWBH'] = $filter['searchParams']["SERCHDWBH"];
		}
		//serchdwmch 单位名称
		if ($filter['searchParams']["SERCHDWMCH"] != "") {
			$sql_list .= " AND(DWMCH LIKE '%' || :SERCHDWMCH || '%')"; 		//单位名称模糊查询
			$bind ['SERCHDWMCH'] = $filter['searchParams']["SERCHDWMCH"];
		}
		$sql_list .= Common_Tool::createFilterSql("CG_CGDDWH",$filter['filterParams'],$bind);	
		
		//检索SQL--采购订单信息H01DB012301
			 		
		$sql_single = "SELECT CGDDBH,TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ,DWBH,DWMCH,BMMCH,".
		" YWYXM AS CZUY,KPYXM,DHHM ,DIZHI, BEIZHU ,TO_CHAR(YDHRQ,'YYYY-MM-DD') AS YDHRQ ".
		" FROM H01VIEW012301   ";
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH = :QYBH AND CGDDBH =:CGDDBH  AND QXBZH ='1'";//F.SHHZHT =:SHZHT
			//绑定数组数超过需要绑定数，检索不出来
		} else if ($flg == 'next') {
			$sql_single .= " WHERE ROWID =  (SELECT NEXTROWID FROM  (SELECT NEXTROWID,CGDDBH FROM ( $sql_list ) WHERE CGDDBH = :CGDDBH))";
		} else if ($flg == 'prev') {
			$sql_single .= " WHERE ROWID =  (SELECT PREVROWID FROM  (SELECT PREVROWID,CGDDBH FROM ( $sql_list ) WHERE CGDDBH = :CGDDBH))";
		}
		//绑定查询条件
		$bind ['CGDDBH'] = $cgdbh;
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	
	/*
	 * 
	 */
	function getcgkpdbh($cgkpdbh){
		
		$sql = "SELECT TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ,DWBH,DWMCH,BMMCH,".
		" YWYXM AS CZUY,KPYXM,DHHM ,DIZHI, BEIZHU ".
		" FROM H01VIEW012301   " .
		" WHERE QYBH =:QYBH  AND QXBZH ='1' AND CGDDBH =:CGDDBH";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGDDBH'] = $cgkpdbh;
		$recs = $this->_db->fetchRow($sql,$bind);	
		return $recs;
	}
}
	
	
	