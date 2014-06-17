<?php
/**********************************************************
 * 模     块：  仓储模块(CC)
 * 机     能：  入库上架确认(rkshjqr)
 * 作成者：    姚磊
 * 作成日：    2011/07/13
 * 更新履历：
 **********************************************************/			
class cc_models_rkshjqr extends Common_Model_Base {

	private $idx_ROWNUM = 0;// 行号
	private $idx_SHPBH = 1;// 商品编号
	private $idx_SHPMCH = 2;// 商品名称
	private $idx_PIHAO =3;//批号
	private $idx_SHCHRQ =4;//生产日期
	private $idx_KWBH =5;//库位编号
	private $idx_SHULIANG =6;//入库数量
	private $idx_YSHJSHL =7;//已上架数量
	private $idx_BCSHJSHL =8;//本次上架数量
	private $idx_RKDBH = 9;//入库单编号
	private $idx_XUHAO = 10;//序号
	
	/**
	 * 得到分箱查询信息
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter){
		//排序用字段名
		$fields = array ("", "SHPBH","SHPMCH","PIHAO","SHCHRQ","KWBH","SHULIANG","YSHJSHL","BCSHJSHL","RKDBH","XUHAO");

		//检索SQL
		$sql = " SELECT  SHPBH,SHPMCH,PIHAO,TO_CHAR(SHCHRQ,'YYYY-MM-DD')AS SHCHRQ,KWBH,SHULIANG,YSHJSHL,'0' AS BCSHJSHL, ".
			   "RKDBH ,XUHAO ,''".
			   " FROM H01VIEW012407 WHERE QYBH=:QYBH AND SHJQRZHT ='1' ";

		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;

		
		//查找条件 商品 编号或名称
		if($filter['searchParams']['SHXX']!=""){
			$sql .= " AND( SHPBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(SHPMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['SHXX']);
		}
		
		//serchrkdbh 入库单编号
		if ($filter['searchParams']["RKDBH"] != "") {
			$sql .= " AND(RKDBH LIKE '%' || :SERCHRKDBH || '%')";  				//入库单编号模糊查询
			$bind ['SERCHRKDBH'] = $filter['searchParams']["RKDBH"];
		}
		
		
		$sql .= Common_Tool::createFilterSql("CC_RKSHJQRXX",$filter['filterParams'],$bind);
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= " ,SHPBH ";		

		
		//当前页数据
		$recs = $this->_db->fetchAll( $sql, $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true);
	}

	/*
	 * grid单条保存
	 */
	function saveOne($rkdbh,$xuhao,$bcsjsl){
		
		$sql_list = " SELECT YSHJSHL , SHULIANG FROM H01DB012407 WHERE QYBH=:QYBH AND RKDBH =:RKDBH AND XUHAO =:XUHAO";
		$bind_list ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$bind_list ['RKDBH'] = $rkdbh;     //入库单编号
		$bind_list ['XUHAO'] = (int)$xuhao;//序号
		$tamp =  $this->_db->fetchRow( $sql_list, $bind_list );
		
		$sql = " UPDATE  H01DB012407 SET " . " YSHJSHL = :YSHJSHL " .
			   ", SHJQRZHT =:SHJQRZHT ,BGRQ = SYSDATE ,BGZH =:BGZH ".
			   " WHERE  RKDBH =:RKDBH AND XUHAO = :XUHAO ";

		$bind ['YSHJSHL'] = $tamp['YSHJSHL'] + $bcsjsl; //已上架数量 = 已上架数量 + 本次上架数量
		$bind ['SHJQRZHT'] = ($tamp['SHULIANG'] == ($tamp['YSHJSHL'] + $bcsjsl)? '2' : '1'); //上架确认状态
		$bind ['XUHAO'] = $xuhao;//序号
		$bind ['RKDBH'] = $rkdbh;//入库单号
		//$bind ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
		$bind ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		
		$this->_db->query ( $sql, $bind );
		

		
	}
	
	/*
	 * 更改入库单状态
	 */
	function uprkdzt($rkdbh,$xuhao){
		
		$sql_list = " SELECT SHJQRZHT FROM H01DB012407 WHERE QYBH=:QYBH AND RKDBH =:RKDBH AND XUHAO =:XUHAO";
		$bind_list ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$bind_list ['RKDBH'] = $rkdbh;     //入库单编号
		$bind_list ['XUHAO'] = (int)$xuhao;//序号
		$tamp =  $this->_db->fetchOne( $sql_list, $bind_list );
		
		if($tamp == '2'){											//如果商品上架状态为2,更改入库单的入库但状态为2,已上架
			$sql = " UPDATE  H01DB012406 SET " . " RKDZHT = '2' " .
			   ",BGRQ = SYSDATE ,BGZH =:BGZH ".
			   " WHERE  RKDBH =:RKDBH  ";
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$bind ['RKDBH'] = $rkdbh;//入库单号
			$this->_db->query ( $sql, $bind );
		}
		
	}
	
	/*
	 * 保存所有数据
	 */
	function saveall(){
		
		
		foreach ( $_POST ["#grid_rkshjqr"] as $grid ) {
		$sql = " UPDATE  H01DB012407 SET " . " YSHJSHL = :YSHJSHL " .
			   ", SHJQRZHT =:SHJQRZHT ,BGRQ = SYSDATE ,BGZH =:BGZH ".
			   " WHERE  RKDBH =:RKDBH AND XUHAO = :XUHAO ";
		$yshjshl = $grid [$this->idx_YSHJSHL] + $grid [$this->idx_BCSHJSHL]; //已上架数量 = 已上架数量 + 本次上架数量
		$bind ['YSHJSHL'] = $yshjshl;
		$bind ['SHJQRZHT'] = ($grid [$this->idx_SHULIANG]  == $yshjshl ? '2' : '1'); //上架确认状态
		$bind ['XUHAO'] = (int)$grid [$this->idx_XUHAO];//序号
		$bind ['RKDBH'] = $grid [$this->idx_RKDBH];//入库单号
		$bind ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		
		if($bind ['SHJQRZHT'] == '2'){											//如果商品上架状态为2,更改入库单的入库但状态为2,已上架
			$sql_list = " UPDATE  H01DB012406 SET " . " RKDZHT = '2' " .
			   ",BGRQ = SYSDATE ,BGZH =:BGZH ".
			   " WHERE  RKDBH =:RKDBH  ";
			$bind_list ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$bind_list ['RKDBH'] = $grid [$this->idx_RKDBH];//入库单号;//入库单号
			$this->_db->query ( $sql_list, $bind_list );
		}
		
		
		$this->_db->query ( $sql, $bind );
		}
	}
	
	/*
	 * 自动获取入库单编号
	 */
	function getAutocompleteData(){
		$sql = "SELECT RKDBH FROM H01VIEW012406 WHERE QYBH = :QYBH";
		
		if($searchkey !=""){
			$sql .= " AND (RKDBH LIKE :SEARCHKEY || '%')";
		    $bind['SEARCHKEY']= $searchkey;
		}
		
		$bind['QYBH']= $_SESSION ['auth']->qybh;
		return $this->_db->fetchAll($sql,$bind);
		
		
	}
}