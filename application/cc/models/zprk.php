<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    赠品入库(ZPRK)
 * 作成者：dltt-苏迅
 * 作成日：2011/7/14
 * 更新履历：
 *********************************/
class cc_models_zprk extends Common_Model_Base {
	private $idx_ROWNUM=0;// 行号
	private $idx_ZPBH=1;// 赠品编号
	private $idx_ZPMCH=2;// 赠品名称
	private $idx_DYSHPBH=3;// 对应商品编号
	private $idx_PIHAO=4;// 批号
	private $idx_SHCHRQ=5;// 生产日期
	private $idx_BZHQZH=6;// 保质期至
	private $idx_SHULIANG=7;// 数量
	private $idx_BEIZHU=8;// 备注
	private $idx_BZHQYSH=9;// 保质期月数	
	private $idx_BZHDWBH=10;// 包装单位编号

	/*
	 * 列表数据取得(xml格式)
	 */
	public function getListData($filter) {
		//排序用字段名
		$fields = array ("", "ZPBH", "ZPMCH", "SHPBH" );
		//检索SQL
		$sql = "SELECT ZPBH,ZPMCH,SHPBH,SHPMCH,GUIGE,BZHQYSH,SHCHCHJ" .
		      " FROM H01VIEW012470 " . 
		      " WHERE QYBH = :QYBH " . //区域编号
              " AND ZHUANGTAI ='1'"; //状态可用
		
	    if($filter['searchParams']['SEARCHKEY']!=""){
			$sql .= " AND( ZPBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(ZPMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['SEARCHKEY']);
		}
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_ZPRK_ZPXZ",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"].",ZPBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		return Common_Tool::createXml ( $recs, true, $totalCount, $filter ["posStart"] );
	}
	
    /*
	 * 自动完成数据取得
	 */
	public function getAutocompleteData($filter){
		
	   //检索SQL
		$sql = "SELECT ZPBH,ZPMCH,GUIGE,SHPBH,SHCHCHJ,SHPMCH ".
		       "FROM H01VIEW012470 ".
		       "WHERE QYBH = :QYBH AND ZHUANGTAI = '1'";
			
		//查询条件	
		if ($filter ["searchkey"] != "") {
			$sql .= " AND (lower(ZPBH) LIKE '%' || :SEARCHKEY || '%' OR lower(ZPMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind['SEARCHKEY'] = $filter ["searchkey"];
		}
		
		$sql .= " AND ROWNUM < 40";
		$sql .= " ORDER BY ZPBH";
	
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		return $this->_db->fetchAll($sql,$bind);
	}
	
	
	/*
	 * 根据商品编号取得商品信息
	 */
	public function getZpInfo($filter) {
		
		//检索SQL
		$sql = "SELECT ZPBH,ZPMCH,GUIGE,SHPBH,BZHQYSH,SHCHCHJ,SHPMCH,BZHDWBH ".
		       "FROM H01VIEW012470 ".
		       "WHERE QYBH = :QYBH AND ZPBH = :ZPBH AND ZHUANGTAI = '1'";
		
		$bind ['ZPBH'] = $filter ['zpbh']; //赠品编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
	/*
	 * 根据单位编号取得单位信息
	 * 
	 * @param array $filter
	 * @return array
	 */
	public function getDanweiInfo($filter) {
		//检索SQL
		$sql =  "SELECT A.DWBH,A.DWMCH,A.DIZHI,A.DHHM" . 
			    " FROM H01DB012106 A" . " WHERE A.QYBH = :QYBH " . //区域编号
				" AND A.DWBH = :DWBH" . //单位编号
				" AND A.KHZHT = '1'"; //客户状态

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck() {
		if ($_POST ["KPRQ"] == "" || //开票日期
            $_POST ["BMBH"] == "" || //部门
            $_POST ["YWYBH"] == "" || //业务员
            $_POST ["DWBH"] == "" || //单位编号
            $_POST ["#grid_mingxi"] == "") { //明细表格
            	
			return false;
		}
		
		$isHasMingxi = false; //是否存在至少一条明细

		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_ZPBH] != "") {
				$isHasMingxi = true;
				if ($grid [$this->idx_SHULIANG] == "" || $grid [$this->idx_SHULIANG] == "0" ) {
					return false;
				}
				if($grid[$this->idx_DYSHPBH] != "" && 
					($grid[$this->idx_PIHAO] == "" || 
					$grid[$this->idx_SHCHRQ] == "" || 
					$grid[$this->idx_BZHQZH] == "")){
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
		
		return true;
	}
	
	/**
	 * 赠品入库单信息保存
	 * @param 	string 	$zprkdbh	新生成的赠品入库单编号
	 * @return 	bool
	 */
	public function saveRukudan($zprkdbh) {
		
		$rukudan['QYBH'] = $_SESSION ['auth']->qybh;	
		$rukudan['ZPRKDBH'] = $zprkdbh;
		$rukudan['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$rukudan['BMBH'] = $_POST["BMBH"];
		$rukudan['YWYBH'] = $_POST["YWYBH"];
		$rukudan['DWBH'] = $_POST["DWBH"];
		$rukudan['DIZHI'] = $_POST["DIZHI"];
		$rukudan['DHHM'] = $_POST["DHHM"];
		$rukudan['BEIZHU'] = $_POST["BEIZHU"];
		$rukudan ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$rukudan ['BGZH'] = $_SESSION ['auth']->userId; 	//变更者
		$rukudan ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$rukudan ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者

		$this->_db->insert ( "H01DB012466", $rukudan );
		
	}
	
	/*
	 * 循环读取明细信息,赠品入库更新操作
	 * 
	 * @param 	string 	$zprkdbh	新生成的赠品入库单编号
	 */
	public function executeMingxi($zprkdbh) {			
		$idx_rukumingxi = 1; //入库单明细信息序号	
		//循环所有明细行
		foreach ( $_POST ["#grid_mingxi"] as $row ) {
			//生成赠品入库单明细信息
			$this->InsertRukumingxi($row,$zprkdbh,$idx_rukumingxi);
			$idx_rukumingxi++;
			//生产赠品在库信息
			$this->insertZaiku($row,$zprkdbh);
		}
	}
	
	/*
	 * 新做成在库赠品信息
	 * 
	 * @param 	array 	$row:明细
	 * 			string  $zprkdbh:赠品入库单号
	 * 
	 * @return 	bool	
	 */
	public function insertZaiku($row,$zprkdbh) {
		
		$zaiku['QYBH'] = $_SESSION ['auth']->qybh;
		$zaiku['CKBH'] = "ZPK001";
		$zaiku['ZPBH'] = $row [$this->idx_ZPBH];
		$zaiku['PIHAO'] = $row [$this->idx_PIHAO];
		$zaiku['ZPRKDBH'] = $zprkdbh;
		$zaiku['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$zaiku['SHULIANG'] = $row [$this->idx_SHULIANG];
		if ($row [$this->idx_SHCHRQ] != ""){
			$zaiku['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" );
		}
		if ($row [$this->idx_BZHQZH] != ""){
			$zaiku['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_BZHQZH] . "','YYYY-MM-DD')" );
		}
		
		$this->_db->insert ( "H01DB012465", $zaiku );
	}
	
	/*
	 * 生成赠品入库单明细信息
	 * 
	 * @param 	array 	$row:明细
	 * 			string 	$zprkdbh:新生成的赠品入库单编号
	 * 			int 	$idx_rukumingxi:入库单明细信息序号	
	 * @return bool 
	 */
	public function InsertRukumingxi($row,$zprkdbh,$idx_rukumingxi) {
		
		$data['QYBH'] = $_SESSION ['auth']->qybh;
		$data['ZPRKDBH'] = $zprkdbh;
		$data['XUHAO'] = $idx_rukumingxi;
		$data['ZPBH'] = $row [$this->idx_ZPBH];
		$data['SHULIANG'] = $row [$this->idx_SHULIANG];
		$data['BEIZHU'] = $row [$this->idx_BEIZHU];
		$data['PIHAO'] = $row [$this->idx_PIHAO];
		if ($row [$this->idx_SHCHRQ] != ""){
			$data['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" );
		}
		if ($row [$this->idx_BZHQZH] != ""){
			$data['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_BZHQZH] . "','YYYY-MM-DD')" );
		}
		$data['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
		$data['BGZH'] = $_SESSION ['auth']->userId; //变更者	
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$this->_db->insert ( "H01DB012467", $data );
				
	}
	
	

}