<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    库内商品移动(KNSHPYD)
 * 作成者：苏迅
 * 作成日：2011/1/17
 * 更新履历：
 *********************************/
class cc_models_knshpyd extends Common_Model_Base {
	
	private $idx_ROWNUM=0;// 行号
	private $idx_SHPBH=1;// 商品编号
	private $idx_SHPMCH=2;// 商品名称
	private $idx_GUIGE=3;// 规格
	private $idx_BZHDWM=4;// 包装单位
	private $idx_PIHAO=5;// 批号
	private $idx_SHCHRQ=6;// 生产日期
	private $idx_BZHQZH=7;// 保质期至
	private $idx_JLGG=8;// 计量规格
	private $idx_BZHSHL=9;// 包装数量
	private $idx_LSSHL=10;// 零散数量
	private $idx_SHULIANG=11;// 数量
	private $idx_ZKZHTM=12;// 在库状态
	private $idx_CHANDI=13;// 产地
	private $idx_BEIZHU=14;// 备注
	private $idx_BZHDWBH = 15; // 包装单位编号
	private $idx_ZHDKQLX=16;// 指定库区类型
	private $idx_KQLXMCH=17;// 指定库区类型名称
	private $idx_TYMCH=18;// 通用名称
	private $idx_ZKZHT=19;// 在库状态

	private	$idx = 1; //在库移动履历序号
	
	/*
	 * 库位列表数据取得(xml格式)
	 */
	function getListData($filter) {
		//排序用字段名
		$fields=array("","KQBH","NLSSORT(KQMCH,'NLS_SORT=SCHINESE_PINYIN_M')","NLSSORT(KQLXM,'NLS_SORT=SCHINESE_PINYIN_M')","KWBH","NLSSORT(KWMCH,'NLS_SORT=SCHINESE_PINYIN_M')","JHSHX","SHFSHKW");
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['CKBH'] = $filter['ckbh']; //仓库
		
		//检索SQL
		$sql = "SELECT KQBH,KQMCH,KQLX,KQLXM,KWBH,KWMCH,JHSHX,DECODE(SHFSHKW,'1','散货位','0','包装位','未知') AS SHFSHKWM,SHFSHKW".
		       " FROM H01UV012404".
		       " WHERE QYBH = :QYBH ".  //区域编号
		       " AND CKBH = :CKBH ".  //仓库编号
		       " AND KWZHT = '1'";
		
		//排序
		$sql .= " ORDER BY ".$fields[$filter["orderby"]]." ".$filter["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",CKBH,KQBH,KWBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
	     
		//总行数

		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"] ,$bind);
		
		//当前页数据

		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"],$bind );
		
		return Common_Tool::createXml ($recs,true,$totalCount,$filter["posStart"]);
	}
	
	/**
	 * 取得仓库列表数据
	 * @param array $filter 条件数组
	 * @return xml
	 */
	function getZaikuListData($filter) {
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['CKBH'] = $filter["ckbh"];
		$bind['KQBH'] = $filter["kqbh"];
		$bind['KWBH'] = $filter["kwbh"];
		
		//检索SQL
		$sql = "SELECT ".
		       "SHPBH,".
		       "SHPMCH,".
		       "GUIGE,".
		       "SUM(SHULIANG) AS SHULIANG,".
		       "BZHDWBHM,".
			   "PIHAO,".
			   "TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,".
			   "TO_CHAR(BZHQZH,'YYYY-MM-DD') AS BZHQZH,".
		       "DECODE(ZKZHT,'0','可销','1','催销','2','冻结','-') AS ZKZHTM,".
			   "CHANDI,".
			   "JLGG,TYMCH,ZHDKQLX,ZHDKQLXM,BZHDWBH,ZKZHT".
		       " FROM H01UV012405".
		       " WHERE QYBH = :QYBH AND SHULIANG > 0 AND CKBH = :CKBH AND KQBH = :KQBH AND KWBH = :KWBH";
		
		//快速查找条件

		if($filter["searchkey"]!=""){
			$bind['SEARCHKEY'] =strtolower(($filter["searchkey"]));
			$sql .=" AND (lower(SHPBH) LIKE '%' || :SEARCHKEY || '%' OR  lower(SHPMCH) LIKE '%' || :SEARCHKEY || '%')"; 			
		}
			       
		//排序
		$sql .= " GROUP BY SHPBH,SHPMCH,GUIGE,BZHDWBHM,PIHAO,SHCHRQ,BZHQZH,ZKZHT,CHANDI,JLGG,TYMCH,ZHDKQLX,ZHDKQLXM,BZHDWBH ORDER BY SHPBH,PIHAO,SHCHRQ,ZKZHT";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
	     
		//总行数

		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"] ,$bind);
		
		//当前页数据

		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"],$bind );
		
		return Common_Tool::createXml($recs,true,$totalCount,$filter["posStart"]);
	}
	

	/*
	 * 根据商品和库位，取得最新库存数据

	 */
/*	function getKucunData($filter){
		//检索SQL
		$sql = "SELECT SHPBH,PIHAO,SUM(SHULIANG) AS SHULIANG FROM H01DB012404 ".
		       "WHERE QYBH = :QYBH".
		       " AND SHPBH = :SHPBH ".
		       " AND CKBH = :CKBH AND KQBH = :KQBH AND KWBH = :KWBH".
			   " GROUP BY SHPBH,PIHAO";
		
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['SHPBH'] = $filter['shpbh'];
		$bind['CKBH'] = $filter['ckbh'];
		$bind['KQBH'] = $filter['kqbh'];
		$bind['KWBH'] = $filter['kwbh'];
		return $this->_db->fetchAll ( $sql,$bind );
	}*/
	
	
	/**
	 * 取得仓库/库区/库位状态信息
	 * @param 	string 	$ckbh	仓库编号
	 * 			string	$kwbh	库位编号
	 * 
	 * @return 	array 
	 */
	public function getkwzht($ckbh,$kqbh,$kwbh) {
		$sql = "SELECT KWBH,"
			."SHFSHKW,"
			."KWZHT,"
			."CKZHT,"
			."KQZHT,"
			."KQLX"
			." FROM H01UV012404"
			." WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQBH = :KQBH AND KWBH = :KWBH";
			
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CKBH'] = $ckbh;
		$bind ['KQBH'] = $kqbh;
		$bind ['KWBH'] = $kwbh;
		
		return $this->_db->fetchRow( $sql, $bind );	
	}
	
	/*
	 * 画面必须输入项验证


	 */
	public function inputCheck() {
		if ($_POST ["KPRQ"] == "" || //开票日期

            $_POST ["BMBH"] == "" || //部门编号
            $_POST ["YWYBH"] == "" || //业务员编号   
            $_POST ["CKBH"] == "" || //仓库编号
            $_POST ["DCKQBH"] == "" || //调出库区编号
            $_POST ["DCKWBH"] == "" || //调出库位编号
            $_POST ["DRKQBH"] == "" || //调入库区编号
            $_POST ["DRKWBH"] == "" || //调入库位编号
            $_POST ["#grid_mingxi"] == "") { //明细表格
            	
			return false;
		}
		
		$isHasMingxi = false; //是否存在至少一条明细



		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] != "") {
				$isHasMingxi = true;
				if ($grid [$this->idx_PIHAO] == "" || //批号
					$grid [$this->idx_SHULIANG] == "" || //数量
					$grid [$this->idx_SHULIANG] == "0") { //数量
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
		
		return true;
	}
	
	/**
	 * 库内移动信息保存
	 * @param 	string 	$yddbh	新生成的库内商品移动单
	 * @return 	bool
	 */
	public function saveKuneiXinxi($yddbh) {
		
		$kunei['QYBH'] = $_SESSION ['auth']->qybh;	
		$kunei['DJBH'] = $yddbh;
		$kunei['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期

		$kunei['BMBH'] = $_POST["BMBH"];
		$kunei['YWYBH'] = $_POST["YWYBH"];
		$kunei['CKBH'] = $_POST["CKBH"];
		$kunei['DCHKQ'] = $_POST["DCKQBH"];
		$kunei['DCHKW'] = $_POST["DCKWBH"];
		$kunei['DRKQ'] = $_POST["DRKQBH"];
		$kunei['DRKW'] = $_POST["DRKWBH"];
		$kunei['BEIZHU'] = $_POST["BEIZHU"];
		$kunei['SHLHJ'] = $_POST["SHULIANG_HEJI"];
		$kunei ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$kunei ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$kunei ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$kunei ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		
		$this->_db->insert ( "H01DB012414", $kunei );
		
	}
	
	/*
	 * 库内移动明细信息保存
	 */
	public function saveKuneiMingxi($yddbh) {
		$idx_mx = 1; //序号自增
        //循环所有明细行，保存销售订单明细

		foreach ( $_POST ["#grid_mingxi"] as $grid ) {			
			$knmingxi ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$knmingxi ['DJBH'] = $yddbh; 
			$knmingxi ['XUHAO'] = $idx_mx ++; //序号
			$knmingxi ['SHPBH'] = $grid [$this->idx_SHPBH]; //商品编号
			$knmingxi ['BZHDWBH'] = $grid [$this->idx_BZHDWBH]; //包装单位编号
			$knmingxi ['PIHAO'] = $grid [$this->idx_PIHAO]; //批号
			if ($grid [$this->idx_SHCHRQ] != ""){
				$knmingxi['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" );
			}
			if ($grid [$this->idx_BZHQZH] != ""){
				$knmingxi['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM-DD')" );
			}
			$knmingxi ['BZHSHL'] = ($grid [$this->idx_BZHSHL] == null) ? 0 : $grid [$this->idx_BZHSHL]; //包装数量
			$knmingxi ['LSSHL'] = ($grid [$this->idx_LSSHL] == null) ? 0 : $grid [$this->idx_LSSHL]; //零散数量
			$knmingxi ['SHULIANG'] = $grid [$this->idx_SHULIANG]; //数量
			$knmingxi ['BEIZHU'] = $grid [$this->idx_BEIZHU]; //备注

			//销售订单明细表
			$this->_db->insert ( "H01DB012415", $knmingxi );	
		}
	}
	
	/*
	 * 循环读取明细信息,在库信息更新操作
	 * 
	 * @param 	string 	$yddbh	新生成的库内商品移动单

	 * @return 	array	$result
	 */
	public function updateKucun($yddbh) {
		$result ['status'] = '0';
			
		//循环所有明细行进行库存数量检验

		foreach ( $_POST ["#grid_mingxi"] as $row ) {
			//取得即时库存信息
			$sql = "SELECT RKDBH,PIHAO,ZKZHT,BZHDWBH,SHULIANG,TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,TO_CHAR(BZHQZH,'YYYY-MM') AS BZHQZH " . 
			       "FROM H01DB012404 " .
			       "WHERE QYBH = :QYBH" . //区域编号
                   " AND CKBH = :CKBH " . //仓库编号
                   " AND KQBH = :KQBH " . //库区编号
                   " AND KWBH = :KWBH " . //库位编号
                   " AND SHPBH = :SHPBH " . //商品编号
                   " AND PIHAO = :PIHAO " . //批号
                   " AND ZKZHT = :ZKZHT" . //在库状态
					" AND TO_CHAR(BZHQZH,'YYYY-MM-DD') = :BZHQZH" .
                   " AND BZHDWBH = :BZHDWBH " . //包装单位
				   " AND TO_CHAR(SHCHRQ,'YYYY-MM-DD') = :SHCHRQ ". //生产日期
                   " ORDER BY ZKZHT DESC,RKDBH" . //在库状态 降序，入库单升序
                   " FOR UPDATE  OF SHULIANG WAIT 10"; //对象库存数据锁定
			//绑定查询变量
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['CKBH'] = $_POST["CKBH"];
			$bind ['KQBH'] = $_POST["DCKQBH"];
			$bind ['KWBH'] = $_POST["DCKWBH"];
			$bind ['SHPBH'] = $row [$this->idx_SHPBH];
			$bind ['PIHAO'] = $row [$this->idx_PIHAO];
			$bind ['BZHDWBH'] = $row [$this->idx_BZHDWBH];
			$bind ['ZKZHT'] = $row [$this->idx_ZKZHT];
			$bind ['SHCHRQ'] = $row [$this->idx_SHCHRQ];
			$bind ['BZHQZH'] = $row [$this->idx_BZHQZH];
			
			//当前明细行在库信息

			$recs = $this->_db->fetchAll ( $sql, $bind );
			$shuliang_zaiku = 0; //累计在库数量
			foreach ( $recs as $rec ) {
				$shuliang_zaiku += ( int ) $rec ['SHULIANG'];
			}
			
			//当前库存数量不足
			if ($shuliang_zaiku < ( int ) $row [$this->idx_SHULIANG]) {
				$result ['status'] = '1'; //库存不足
				$result ['data']['rIdx'] = ( int ) $row [$this->idx_ROWNUM]; //定位明细行index
				$result ['data']['shuliang'] = $shuliang_zaiku; //最新在库数量
				$result ['data']['pihao'] = $row [$this->idx_PIHAO];; //批号

			}
			
			//库存数量充足
			if($result['status']=='0'){
				//更新在库和移动履历信息

			    $this->updateZaiku ( $row, $recs, $yddbh );
			}else{
				break;
			}
		}
					
		return $result;
	}
	
	/*
	 * 更新在库和移动履历信息

	 */
	public function updateZaiku($row,$kucuns, $yddbh) {
		//同一货位批号 按照催销，先入先出（入库单）原则进行分摊出库
		$shuliang_shengyu = ( int ) $row [$this->idx_SHULIANG]; //移动数量

		foreach ( $kucuns as $kucun ) {
			$shuliang = 0; //在库更新数量
	
			//部分出库时 
			if ($shuliang_shengyu <= ( int ) $kucun ['SHULIANG']) {
				$shuliang = ( int ) $kucun ['SHULIANG'] - $shuliang_shengyu;
				$shuliang_yidong = $shuliang_shengyu;
				$shuliang_shengyu = 0;
			
			} else { //全部出库
				$shuliang = 0;
				$shuliang_yidong = ( int ) $kucun ['SHULIANG'];
				$shuliang_shengyu = $shuliang_shengyu - ( int ) $kucun ['SHULIANG'];
			}
			
			//更新在库信息
			$sql_zaiku = "UPDATE H01DB012404 ".
			             "SET SHULIANG = :SHULIANG " .
			             (($shuliang == 0) ? ",ZZHCHKRQ = SYSDATE " : "").
			             " WHERE QYBH = :QYBH ".
			             " AND CKBH = :CKBH " .
			             " AND KQBH = :KQBH ".
			             " AND KWBH = :KWBH ".
			             " AND SHPBH = :SHPBH " .
			             " AND PIHAO = :PIHAO " .
			             " AND ZKZHT = :ZKZHT " .
			             " AND RKDBH = :RKDBH " .
			             " AND BZHDWBH = :BZHDWBH ".
			             " AND TO_CHAR(SHCHRQ,'YYYY-MM-DD') = :SHCHRQ "; //生产日期;
			             
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['CKBH'] = $_POST["CKBH"];
			$bind ['KQBH'] = $_POST["DCKQBH"]; 
			$bind ['KWBH'] = $_POST["DCKWBH"];
			$bind ['SHPBH'] = $row [$this->idx_SHPBH];
			$bind ['PIHAO'] = $row [$this->idx_PIHAO]; 
			$bind ['BZHDWBH'] = $row [$this->idx_BZHDWBH];
			$bind ['RKDBH'] = $kucun ['RKDBH']; 
			$bind ['ZKZHT'] = $row [$this->idx_ZKZHT];
			$bind ['SHCHRQ'] = $row [$this->idx_SHCHRQ];
			$bind ['SHULIANG'] = $shuliang;               
			$this->_db->query ( $sql_zaiku,$bind );
			
			//生成在库移动履历(调出)
			$lvli ["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
			$lvli ["CKBH"] = $_POST["CKBH"]; //仓库编号
			$lvli ["KQBH"] = $_POST["DCKQBH"]; //库区编号
			$lvli ["KWBH"] = $_POST["DCKWBH"]; //库位编号
			$lvli ["SHPBH"] = $row [$this->idx_SHPBH]; //商品编号
			$lvli ["PIHAO"] = $row [$this->idx_PIHAO]; //批号
			$lvli ["RKDBH"] = $kucun ['RKDBH']; //入库单号
			$lvli ["YDDH"] = $yddbh; //库内移动单号(销售单编号)
			$lvli ["XUHAO"] = $this->idx ++; //序号
			if ($row [$this->idx_SHCHRQ] != ""){
				$lvli['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_SHCHRQ]."','YYYY-MM-DD')"); //生产日期;
			}
			if ($row [$this->idx_BZHQZH] != ""){
				$lvli['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_BZHQZH]."','YYYY-MM-DD')"); //保质期至
			}
			$lvli['CHLSHJ'] = new Zend_Db_Expr('SYSDATE');
			$lvli ["SHULIANG"] = $shuliang_yidong * - 1; //移动数量
			$lvli ["ZHYZHL"] = '35'; //转移种类 [出库]
			$lvli ["BZHDWBH"] = $row [$this->idx_BZHDWBH]; //包装单位编号
			$lvli ["ZKZHT"] = $row [$this->idx_ZKZHT];//在库状态

			$lvli['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
			$lvli['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$lvli ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$lvli ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			$this->_db->insert ( 'H01DB012405', $lvli );
			
			
			//生成在库移动履历(调入)
			$lvli_dr ["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
			$lvli_dr ["CKBH"] = $_POST["CKBH"]; //仓库编号
			$lvli_dr ["KQBH"] = $_POST["DRKQBH"]; //库区编号
			$lvli_dr ["KWBH"] = $_POST["DRKWBH"]; //库位编号
			$lvli_dr ["SHPBH"] = $row [$this->idx_SHPBH]; //商品编号
			$lvli_dr ["PIHAO"] = $row [$this->idx_PIHAO]; //批号
			$lvli_dr ["RKDBH"] = $kucun ['RKDBH']; //入库单号
			$lvli_dr ["YDDH"] = $yddbh; //库内移动单号(销售单编号)
			$lvli_dr ["XUHAO"] = $this->idx ++; //序号
			if ($row [$this->idx_SHCHRQ] != ""){
				$lvli_dr['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_SHCHRQ]."','YYYY-MM-DD')"); //生产日期;
			}
			if ($row [$this->idx_BZHQZH] != ""){
				$lvli_dr['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_BZHQZH]."','YYYY-MM-DD')"); //保质期至
			}
			$lvli_dr['CHLSHJ'] = new Zend_Db_Expr('SYSDATE');
			$lvli_dr ["SHULIANG"] = $shuliang_yidong; //移动数量
			$lvli_dr ["ZHYZHL"] = '35'; //转移种类 [出库]
			$lvli_dr ["BZHDWBH"] = $row [$this->idx_BZHDWBH]; //包装单位编号
			$lvli_dr ["ZKZHT"] = $row [$this->idx_ZKZHT];//在库状态

			$lvli_dr['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
			$lvli_dr['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$lvli_dr ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$lvli_dr ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			$this->_db->insert ( 'H01DB012405', $lvli_dr );
			
			//检索在库商品信息是否存在

			$kucuncunzai = $this->checkCunzai($row,$kucun);
			
			//存在,更新该在库商品信息

			if($kucuncunzai != false){
				$this->updateZk($row,$shuliang_yidong,$kucun);
			}
			//不存在时，新做成在库商品信息
			else{
				$this->insertZaiku($row,$shuliang_yidong,$kucun);
			}	
			
			//所有数量均出库完毕，不再继续循环

			if ($shuliang_shengyu <= 0) break;
		}
	}
		
	/*
	 * 检索在库商品信息是否存在

	 * 
	 * @param 	array 	$row:明细
	 * 			array   $kucun:入库单数组

	 * 
	 * @return 	int		
	 */
	public function checkCunzai($row,$kucun) {
		
		$sql_kucun = "SELECT SHULIANG"
					." FROM H01DB012404"
					." WHERE QYBH = :QYBH"
					." AND CKBH = :CKBH"
					." AND KQBH = :KQBH"
					." AND KWBH = :KWBH"
					." AND SHPBH = :SHPBH"
					." AND PIHAO = :PIHAO"
					." AND BZHDWBH = :BZHDWBH"
					." AND ZKZHT = :ZKZHT"
					." AND RKDBH = :RKDBH"
					." AND TO_CHAR(SHCHRQ,'YYYY-MM-DD') = :SHCHRQ FOR UPDATE WAIT 10";

		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['CKBH'] = $_POST["CKBH"]; //仓库编号
		$bind['KQBH'] = $_POST["DRKQBH"]; //库区编号
		$bind['KWBH'] = $_POST["DRKWBH"]; //库位编号
		$bind['SHPBH'] = $row [$this->idx_SHPBH];
		$bind['PIHAO'] = $row [$this->idx_PIHAO];
		$bind['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$bind['RKDBH'] = $kucun ['RKDBH']; //入库单号
		$bind['SHCHRQ'] = $row [$this->idx_SHCHRQ];
		$bind['ZKZHT'] = $row [$this->idx_ZKZHT];
				
		return $this->_db->fetchRow( $sql_kucun, $bind );
		
	}
		
	/*
	 * 更新在库商品信息
	 * 
	 * @param 	array 	$row:明细
	 * 			int		$shuliang_yidong:更新数量
	 * 			array   $kucun
	 * 
	 * @return 	bool	
	 */
	public function updateZk($row,$shuliang_yidong,$kucun) {
		
		$sql_update = "UPDATE H01DB012404"
					. " SET SHULIANG = SHULIANG + :SHULIANG_UPDATE,"
					. "ZZHCHKRQ = TO_DATE(:ZZHCHKRQ,'YYYY-MM-DD hh24:mi:ss')"
					. " WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQBH = :KQBH AND KWBH = :KWBH"
					. " AND SHPBH = :SHPBH AND PIHAO = :PIHAO AND BZHDWBH = :BZHDWBH AND ZKZHT = :ZKZHT"
					. " AND RKDBH = :RKDBH AND TO_CHAR(SHCHRQ,'YYYY-MM-DD') = :SHCHRQ";
		
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['SHULIANG_UPDATE'] = $shuliang_yidong;
		$bind['ZZHCHKRQ'] = '9999/12/31 23:59:59';
		$bind['CKBH'] = $_POST["CKBH"]; //仓库编号
		$bind['KQBH'] = $_POST["DRKQBH"]; //库区编号
		$bind['KWBH'] = $_POST["DRKWBH"]; //库位编号
		$bind['SHPBH'] = $row [$this->idx_SHPBH];
		$bind['PIHAO'] = $row [$this->idx_PIHAO];
		$bind['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$bind['ZKZHT'] = $row [$this->idx_ZKZHT];
		$bind['RKDBH'] = $kucun ['RKDBH']; //入库单号
		$bind['SHCHRQ'] = $row [$this->idx_SHCHRQ];
			
		$this->_db->query ( $sql_update,$bind );
	}
	
	/*
	 * 新做成在库商品信息

	 * 
	 * @param 	array 	$row:明细
	 * 			int		$shuliang_yidong:更新数量
	 * 			array   $kucun
	 * 
	 * @return 	bool	
	 */
	public function insertZaiku($row,$shuliang_yidong,$kucun) {
		
		$zaiku['QYBH'] = $_SESSION ['auth']->qybh;
		$zaiku['CKBH'] = $_POST["CKBH"]; //仓库编号
		$zaiku['KQBH'] = $_POST["DRKQBH"]; //库区编号
		$zaiku['KWBH'] = $_POST["DRKWBH"]; //库位编号
		$zaiku['SHPBH'] = $row [$this->idx_SHPBH];
		$zaiku['PIHAO'] = $row [$this->idx_PIHAO];
		$zaiku['RKDBH'] = $kucun ['RKDBH']; //入库单号
		$zaiku['ZKZHT'] = $row [$this->idx_ZKZHT];
		$zaiku['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$zaiku['ZZHCHKRQ'] = new Zend_Db_Expr("TO_DATE('9999/12/31 23:59:59','YYYY-MM-DD hh24:mi:ss')");
		$zaiku['SHULIANG'] = $shuliang_yidong;
		if ($row [$this->idx_SHCHRQ] != ""){
			$zaiku['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" );
		}
		if ($row [$this->idx_BZHQZH] != ""){
			$zaiku['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_BZHQZH] . "','YYYY-MM-DD')" );
		}
		
		$this->_db->insert ( "H01DB012404", $zaiku );
	}
}
	
	
	