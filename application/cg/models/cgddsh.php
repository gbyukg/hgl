<?php
/*********************************
 * 模块：    采购模块(cg)
 * 机能：    采购订单维护(CGDDSP)
 * 作成者：姚磊
 * 作成日：2011/1/20
 * 更新履历：
 *********************************/
class cg_models_cgddsh extends Common_Model_Base {

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
	private $idxx_CGGZHDBH = 1; // 单据编号
	private $idxx_KPRQ = 2; // 开票日期
	private $idxx_DWBH = 3; // 单位编号
	private $idxx_DWMCH = 4; // 单位名称
	private $idxx_BMMCH = 8; // 部门名称
	private $idxx_YWUY = 9; // 业务员
	private $idxx_CZUY = 10; // 操作员
	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck() {
		if ($_POST ["KPRQ"] == "" || //开票日期
			$_POST ["BMBH"] == "" || //部门编号
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
	 * 得到采购订单审核明细信息列表数据
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
	 * 采购订单审核列表
	 *
	 * @param array $filter
	 * @return string xml
	 */
	
	public function getGridData($filter) {
		//检索SQL
		$fields = array ("", "A.CGDDBH", "A.KPRQ", "A.DWBH", "E.DWMCH", "B.BMMCH", "C.YGXM","D.YGXM");
		$sql = " SELECT A.CGDDBH,TO_CHAR(A.KPRQ,'YYYY-MM-DD') AS KPRQ,A.DWBH,E.DWMCH,B.BMMCH,C.YGXM,".
		" D.YGXM AS CZUY,A.DHHM ,A.DIZHI, A.BEIZHU ".
		" FROM H01DB012301 A  LEFT OUTER JOIN H01DB012112 B ON A.QYBH  =B.QYBH AND A.BMBH = B.BMBH  " .
		" LEFT JOIN H01DB012113 C ON A.QYBH = C.QYBH   AND A.YWYBH=C.YGBH " . 
		" LEFT JOIN H01DB012106 E ON A.QYBH = E.QYBH AND A.DWBH = E.DWBH ".
		" LEFT JOIN H01DB012113 D ON A.QYBH = D.QYBH   AND A.YWYBH=D.YGBH " . 
		" WHERE A.QYBH = :QYBH AND A.SHHZHT ='0' AND A.QXBZH ='1' ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.CGDDBH";
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
	 *更改采购订单审批状态
	 */
	function upCgdata($cggzhdbh){
		
		$sql = "UPDATE H01DB012301 SET QXBZH = 'X' WHERE QYBH =:QYBH AND CGDDBH =:CGDDBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'CGDDBH' => $cggzhdbh );
		return $this->_db->fetchRow ( $sql, $bind );
	}
	/*
	 * 采购订单审核列表
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
	 * 采购订单审核详情明细
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
		$fields = array ("", "A.CGDDBH", "A.KPRQ", "A.DWBH", "E.DWMCH", "B.BMMCH", "C.YGXM","A.BGRQ","D.YGXM");
				
		//检索SQL--取上下条关系
		$sql_list = " SELECT A.CGDDBH, A.ROWID, LEAD(A.ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",A.CGDDBH) AS NEXTROWID,"  . 
					" LAG(A.ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",A.CGDDBH) AS PREVROWID"  . 
					" FROM H01DB012301 A  LEFT OUTER JOIN H01DB012112 B ON A.QYBH  =B.QYBH AND A.BMBH = B.BMBH  " .
					" LEFT JOIN H01DB012113 C ON A.QYBH = C.QYBH   AND A.YWYBH=C.YGBH " . 
					" LEFT JOIN H01DB012106 E ON A.QYBH = E.QYBH AND A.DWBH = E.DWBH".
					" LEFT JOIN H01DB012113 D ON A.QYBH = D.QYBH   AND A.YWYBH=D.YGBH " . 
					" WHERE A.QYBH = :QYBH AND A.SHHZHT ='0' AND A.QXBZH ='1' ";
			      			      			      
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
	   //检索SQL--采购订单信息H01DB012301
			 		
		$sql_single = "SELECT F.CGDDBH,TO_CHAR(F.KPRQ,'YYYY-MM-DD') AS KPRQ,F.DWBH,T4.DWMCH,T2.BMMCH,T3.YGXM,".
		" T5.YGXM AS CZUY,F.DHHM, F.SHFZZHSH,F.DIZHI,TO_CHAR(F.YDHRQ,'YYYY-MM-DD') AS YDHRQ ,".
		" F.BEIZHU" . " FROM H01DB012301 F  LEFT OUTER JOIN H01DB012112 T2 ON F.QYBH  =T2.QYBH AND F.BMBH = T2.BMBH  " .
		" LEFT JOIN H01DB012113 T3 ON F.QYBH = T3.QYBH   AND F.YWYBH=T3.YGBH " . 
		" LEFT JOIN H01DB012106 T4 ON F.QYBH = T4.QYBH AND F.DWBH = T4.DWBH".
		" LEFT JOIN H01DB012113 T5 ON F.QYBH = T5.QYBH   AND F.YWYBH=T5.YGBH " ;
		if ($flg == 'current') {
			$sql_single .= " WHERE F.QYBH = :QYBH AND F.CGDDBH =:CGDDBH";
		} else if ($flg == 'next') {
			$sql_single .= " WHERE F.ROWID =  (SELECT NEXTROWID FROM  (SELECT NEXTROWID,CGDDBH FROM ( $sql_list ) WHERE CGDDBH = :CGDDBH))";
		} else if ($flg == 'prev') {
			$sql_single .= " WHERE F.ROWID =  (SELECT PREVROWID FROM  (SELECT PREVROWID,CGDDBH FROM ( $sql_list ) WHERE CGDDBH = :CGDDBH))";
		}
		//绑定查询条件
		$bind ['CGDDBH'] = $cgdbh;
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	
	/*
	 * 	更新采购订单信息表					
	 *  审核通过  SHHZHT = 1
	 *  $cgddbh ,$shhyj 采购订单编号,审核意见
	 */
	function upcgdd($cgddbh,$shyj){
		
		$sql = " UPDATE H01DB012301 SET SHHZHT ='1' ,SHHYJ =:SHHYJ ,SHHR =:SHHR ,SHHRQ =SYSDATE  ,BGRQ =SYSDATE , BGZH =:BGZH ".
			   " WHERE QYBH =:QYBH AND CGDDBH =:CGDDBH ";

		$bind['QYBH'] = $_SESSION ['auth']->qybh; 			
		$bind['CGDDBH'] = $cgddbh; 						//采购单单号
		$bind['SHHR'] = $_SESSION ['auth']->userId; 	//审核人
		$bind['BGZH'] = $_SESSION ['auth']->userId; 	//变更人
		$bind['SHHYJ'] = $shyj; 						//审核意见
		return $this->_db->query( $sql, $bind );
	}
	
/*
	 * 	更新采购订单信息表					
	 *  审核不通过  SHHZHT = 2
	 *  $cgddbh ,$shhyj 采购订单编号,审核意见
	 */
	function upcgddbtg($cgddbh,$shyj){
		
		$sql = " UPDATE H01DB012301 SET SHHZHT ='2' ,SHHYJ =:SHHYJ ,SHHR =:SHHR ,SHHRQ = SYSDATE ,BGRQ = SYSDATE , BGZH=:BGZH ".
			   " WHERE QYBH =:QYBH AND CGDDBH =:CGDDBH ";
		$bind['QYBH'] = $_SESSION ['auth']->qybh; 			
		$bind['CGDDBH'] = $cgddbh; 						//采购单单号
		$bind['SHHR'] = $_SESSION ['auth']->userId; 	//审核人
		$bind['BGZH'] = $_SESSION ['auth']->userId; 	//变更人
		$bind['SHHYJ'] = $shyj; 						//审核意见
		return $this->_db->query( $sql, $bind );
	}

}
	
	
	