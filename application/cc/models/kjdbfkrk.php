<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：   库间调拨返库入库(KJDBFKRK)
 * 作成者：苏迅
 * 作成日：2011/1/26
 * 更新履历：
 *********************************/
class cc_models_kjdbfkrk extends Common_Model_Base {
		
	private $idx_ROWNUM=0;// 行号
	private $idx_SHPBH=1;// 商品编号
	private $idx_SHPMCH=2;// 商品名称
	private $idx_GUIGE=3;// 规格
	private $idx_PIHAO=4;// 批号
	private $idx_BZHDWM=5;// 包装单位
	private $idx_HWMCH=6;// 货位
	private $idx_SHCHRQ=7;// 生产日期
	private $idx_BZHQZH=8;// 保质期至
	private $idx_JLGG=9;// 计量规格
	private $idx_BZHSHL=10;// 包装数量
	private $idx_LSSHL=11;// 零散数量
	private $idx_SHULIANG=12;// 数量
	private $idx_CHANDI=13;// 产地
	private $idx_BEIZHU=14;// 备注
	private $idx_BZHDWBH = 15; // 包装单位编号
	private $idx_ZHDKQLX=16;// 指定库区类型
	private $idx_KQLXMCH=17;// 指定库区类型名称
	private $idx_TYMCH=18;// 通用名称
	private $idx_KQBH=19;// 返库库区编号
	private $idx_KWBH=20;// 返库库位编号
	private $idx_SHFSHKW=21;// 是否散货区

    /*
	 * 仓库自动完成数据取得
	 */
	public function getAutocompleteData($filter){
		
		//检索SQL
		$sql = "SELECT CKBH,CKMCH" . 
		       " FROM H01DB012401" .
		       " WHERE QYBH = :QYBH ";//区域编号
		
		//快速查找条件
		if ($filter ["searchkey"] != "") {
			$bind ['SEARCHKEY'] = strtolower($filter ["searchkey"]);
			$sql .= " AND (lower(CKBH) LIKE '%' || :SEARCHKEY || '%' OR  lower(CKMCH) LIKE '%' || :SEARCHKEY || '%')";
		}
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		return $this->_db->fetchAll($sql,$bind);
	}
	
	/**
	 * 得到退货单列表数据(退货单选择页面)--退货单
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridDanjuData($filter) {
		//排序用字段名
		$fields = array ("", "DJBH", "CHKDZHT", "NLSSORT(DCHCKMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "NLSSORT(DRCKMCH,'NLS_SORT=SCHINESE_PINYIN_M')","KPRQ","DYDBCHKD","NLSSORT(YWYXM,'NLS_SORT=SCHINESE_PINYIN_M')", "NLSSORT(BMMCH,'NLS_SORT=SCHINESE_PINYIN_M')");
		
		//检索SQL
		$sql = "SELECT DJBH,DECODE(CHKDZHT,'1','未入库','2','已入库','') AS CHKDZHT,DCHCKMCH,DRCKMCH,TO_CHAR(KPRQ,'YYYY-MM-DD'),DYDBCHKD,YWYXM,BMMCH" 
		     . " FROM H01VIEW012423 WHERE QYBH = :QYBH AND CHKDZHT = '1'";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter ['searchParams']["KSRQKEY"] != "" || $filter ['searchParams']["ZZRQKEY"] != "")
		{
			$sql .= " AND :KSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :ZZRQ";
			$bind ['KSRQ'] = $filter ['searchParams']["KSRQKEY"] == ""?"1900-01-01":$filter ['searchParams']["KSRQKEY"];
			$bind ['ZZRQ'] = $filter ['searchParams']["ZZRQKEY"] == ""?"9999-12-31":$filter ['searchParams']["ZZRQKEY"];
		}
		
		//查询条件(调出仓库输入)
		if ($filter ['searchParams']["DCCKBHKEY"] != "") {
			$sql .= " AND DCHCK = :DCHCK";
			$bind ['DCHCK'] = $filter ['searchParams']["DCCKBHKEY"];
		}
		
		//查询条件(调入仓库输入)
		if ($filter ['searchParams']["DRCKBHKEY"] != "") {
			$sql .= " AND DRCK = :DRCK";
			$bind ['DRCK'] = $filter ['searchParams']["DRCKBHKEY"];
		}
		
		//查询条件(返库单据号)
		if($filter ['searchParams']["FKDJH"] != "") {
			$sql .= " AND DJBH LIKE '%' || :DJBH || '%'";
			$bind ['DJBH'] = $filter ['searchParams']["FKDJH"];
		}
		
		//查询条件(对应调拨出库单)
		if($filter ['searchParams']["DBCKD"] != "") {
			$sql .= " AND DYDBCHKD = :DYDBCHKD";
			$bind ['DYDBCHKD'] = $filter ['searchParams']["DBCKD"];
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_KJDBFKRK_02_DBFK",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",DJBH DESC";
		
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
	 * 得到调拨返库明细列表数据(调拨返库单选择页面)--明细
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridMingxiData($filter) {
		//排序用字段名
		//$fields = array ("", "A.SHPBH", "NLSSORT(B.SHPMCH,'NLS_SORT=SCHINESE_PINYIN_M')");
		
		//检索SQL
		$sql = "SELECT SHPBH,SHPMCH,GUIGE,PIHAO,BZHDWMCH,TO_CHAR(SHCHRQ,'YYYY-MM-DD'),TO_CHAR(BZHQZH,'YYYY-MM-DD'),"
			 . "JLGG,HGL_DEC(BZHSHL),HGL_DEC(LSSHL),HGL_DEC(SHULIANG),CHANDI,BEIZHU,BZHDWBH,ZHDKQLX,ZHDKQLXMCH,TYMCH"
		     . " FROM H01VIEW012424" 
		     . " WHERE QYBH = :QYBH AND DJBH = :DJBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DJBH'] = $filter ["fkdbh"];
				
		//排序
		$sql .= " ORDER BY DJBH,XUHAO";
		
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
	 * 得到仓库信息
	 *
	 * @param string $ckbh
	 * @return array
	 */
	public function getCkxx($ckbh) {
		//检索SQL
		$sql = "SELECT CKBH,CKMCH,DIZHI FROM H01DB012401 WHERE QYBH = :QYBH AND CKBH = :CKBH";
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['CKBH'] = $ckbh; //仓库编号
		
		return $this->_db->fetchRow( $sql , $bind );
	}
	
	/*
	 * 库位列表数据取得(xml格式)
	 */
	function getListData($filter) {
		//排序用字段名
		$fields=array("","B.KQBH","NLSSORT(B.KQMCH,'NLS_SORT=SCHINESE_PINYIN_M')","B.KQLX","A.KWBH","NLSSORT(A.KWMCH,'NLS_SORT=SCHINESE_PINYIN_M')","A.JHSHX","","SHFSHKW","KWZHT");
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['CKBH'] = $filter['ckbh']; //仓库
		
		//检索SQL
		$sql = "SELECT B.KQBH,B.KQMCH,B.KQLX,C.NEIRONG AS KQLXM,A.KWBH,A.KWMCH,A.JHSHX,DECODE(A.SHFSHKW,'1','散货位','0','包装位','未知') AS SHFSHKWM,A.SHFSHKW".
		       " FROM H01DB012403 A LEFT JOIN H01DB012402 B ON A.QYBH = B.QYBH AND A.KQBH = B.KQBH AND A.CKBH = B.CKBH" .
			   " LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.KQLX = C.ZIHAOMA AND C.CHLID = 'KQLX'".
		       " WHERE A.QYBH = :QYBH ".  //区域编号
		       " AND A.CKBH = :CKBH ".  //仓库编号
		       " AND A.KWZHT = '1'";
		
		//排序
		$sql .= " ORDER BY ".$fields[$filter["orderby"]]." ".$filter["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.CKBH,A.KQBH,A.KWBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
	     
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"] ,$bind);
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"],$bind );
		
		return Common_Tool::createXml ($recs,true,$totalCount,$filter["posStart"]);
	}
	
	/*
	 * 根据返库单编号取得返库单信息
	 * 
	 * @param string $fkdbh:返库单编号
	 * @return array
	 */
	public function getSingleFkdInfo($fkdbh) {
		
		$sql = "SELECT DJBH,DYDBCHKD,DCHCK,DCHCKMCH,DRCK,DRCKMCH,DRCKDZH,SHFPS,DHHM,BEIZHU,TO_CHAR(BGRQ,'yyyy-mm-dd hh24:mi:ss') AS BGRQ" 
		     . " FROM H01VIEW012423 WHERE QYBH = :QYBH AND DJBH = :DJBH";
		     
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DJBH'] = $fkdbh;
		
		return $this->_db->fetchRow ( $sql, $bind );
		     
	}	
	
	/**
	 * 得到退货单明细列表数据
	 *
	 * @param array $filter
	 * @return array 
	 */
	public function getfkdmingxi($fkdbh) {	
		//检索SQL
		$sql = "SELECT SHPBH,"
					."SHPMCH,"
					."GUIGE,"
					."PIHAO,"
					."BZHDWMCH,"
					."TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,"
					."TO_CHAR(BZHQZH,'YYYY-MM-DD') AS BZHQZH,"
					."HGL_DEC(BZHSHL) AS BZHSHL,"
					."HGL_DEC(LSSHL) AS LSSHL,"
					."HGL_DEC(SHULIANG) AS SHULIANG,"
					."CHANDI,"
					."BEIZHU,"
					."BZHDWBH,"
					."JLGG,"
					."ZHDKQLX,"
					."TYMCH,"
					."ZHDKQLXMCH" 
		     . " FROM H01VIEW012424" 
		     . " WHERE QYBH = :QYBH AND DJBH = :DJBH ORDER BY XUHAO";
		     
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DJBH'] = $fkdbh;
		
		return $this->_db->fetchAll( $sql, $bind );
		
	}
	
	/**
	 * 取得仓库/库区/库位状态信息
	 * @param 	string 	$ckbh	仓库编号
	 * 			string 	$ckbh	库区编号
	 * 			string	$kwbh	库位编号
	 * 
	 * @return 	array 
	 */
	public function getkuweizht($ckbh,$kqbh,$kwbh) {
		$sql = "SELECT C.KWBH,"
			."C.SHFSHKW,"
			."C.KWZHT,"
			."A.CKZHT,"
			."B.KQZHT,"
			."B.KQLX"
			." FROM H01DB012403 C LEFT JOIN H01DB012401 A ON C.QYBH = A.QYBH AND C.CKBH = A.CKBH LEFT JOIN H01DB012402 B ON C.QYBH = B.QYBH AND C.CKBH = B.CKBH AND C.KQBH =B.KQBH"
			." WHERE C.QYBH = :QYBH AND C.CKBH = :CKBH AND C.KQBH = :KQBH AND C.KWBH = :KWBH";
			
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CKBH'] = $ckbh;
		$bind ['KQBH'] = $kqbh;
		$bind ['KWBH'] = $kwbh;
		
		return $this->_db->fetchRow( $sql, $bind );	
	}
	
	/**
	 * 取得返库状态
	 * @param 	string 	$fkdbh	返库单编号
	 * @return 	array 
	 */
	public function getfkzht($fkdbh) {
		$sql = "SELECT CHKDZHT FROM H01DB012423 WHERE QYBH = :QYBH AND DJBH = :DJBH";
			
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DJBH'] = $fkdbh;
		
		return $this->_db->fetchRow( $sql, $bind );	
	}
	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck() {
		if ($_POST ["KPRQ"] == "" || //开票日期
            $_POST ["BMBH"] == "" || //部门编号
            $_POST ["YWYBH"] == "" || //业务员编号   
            $_POST ["#grid_mingxi"] == "") { //明细表格
            	
			return false;
		}
		
		$isHasMingxi = false; //是否存在至少一条明细


		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] != "") {
				$isHasMingxi = true;
				if ($grid [$this->idx_PIHAO] == "" || //批号
					$grid [$this->idx_SHULIANG] == "" || //数量
					$grid [$this->idx_SHULIANG] == "0" || //数量
					$grid [$this->idx_KQBH] == "" || //库区编号 
					$grid [$this->idx_KWBH] == "") { //库位编号
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
	 * 保存库间调拨返库入库单信息
	 * @param 	string 	$rkdbh	新生成的退货入库单编号
	 * @return 	bool
	 */
	public function saveRukudan($rkdbh) {
		
		$rukudan['QYBH'] = $_SESSION ['auth']->qybh;	
		$rukudan['DJBH'] = $rkdbh;
		$rukudan['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$rukudan['BMBH'] = $_POST["BMBH"];
		$rukudan['YWYBH'] = $_POST["YWYBH"];	
		$rukudan['DCHCK'] = $_POST["DCHCKBH"];
		$rukudan['DRCK'] = $_POST["DRCKBH"];	
		$rukudan['DRCKDZH'] = $_POST["DRCKDZH"];
		$rukudan['DHHM'] = $_POST["DHHM"];
		$rukudan['SHFPS'] = ($_POST ['SHFPS'] == null) ? '0' : '1';//是否配送
		$rukudan['BEIZHU'] = $_POST["BEIZHU"];
		$rukudan['SHLHJ'] = $_POST["SHULIANG_HEJI"];
		$rukudan['DYDBFKD'] = $_POST["DYDBFKD"];
		$rukudan['DYDBCHKD'] = $_POST["DYDBCHKD"];
		$rukudan ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$rukudan ['BGZH'] = $_SESSION ['auth']->userId; //变更者		$rukudan ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$rukudan ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		
		$this->_db->insert ( "H01DB012425", $rukudan );
		
	}
	
	/*
	 * 循环读取明细信息,返库入库更新操作
	 * 
	 * @param 	string 	$rkdbh	新生成的调拨返库入库单编号
	 * @return 	array	$result
	 */
	public function executeMingxi($rkdbh) {
		$result ['status'] = '0';//正常入库
		$idx_rukumingxi = 1; //入库单明细信息序号	
		$idx_lvli = 1; //在库移动履历
			
		//循环所有明细行
		foreach ( $_POST ["#grid_mingxi"] as $row ) {
			
			//生成入库单明细信息H01DB012426
			$this->InsertRukumingxi($row,$rkdbh,$idx_rukumingxi);
						
			//入库单明细信息序号自增	
			$idx_rukumingxi ++;
			
			//变量:返库入库数量 = 画面项目：数量
			$shuliang_shengyu = ( int ) $row [$this->idx_SHULIANG];
			
			//用商品编号，批号，包装单位，调拨出库单号为条件，检索库间调拨出库单明细信息
			$recs = $this->getChukumx($row);
			
			//循环上面取到的出库信息
			foreach ( $recs as $rec ) {
				//$dckq = $rec['DCHKQ'];		//变量：调出库区
				//$dckw = $rec['DCHKW'];		//变量：调出库位
					
				//抽取移动履历，取出该条调拨出库单明细对应的移动履历。
				$recs_Chukulvli = $this->getChukuLvli($row,$rec);
				
				//循环调拨出库单明细对应的移动履历(明细信息对应的商品调拨出库时对应的入库单数组)
				foreach ( $recs_Chukulvli as $rec_Chukulvli ) {
					//以变量：入库单号等条件，检索tbl:商品移动履历中，待入库商品的数量合计
					$ruku_needed = $this->getRukuNeededLvli($row,$rec_Chukulvli);
					
					//该出库入库单已全部入库,处理下一个入库单对应的移动履历
					if($ruku_needed == 0) continue;
					
					//对应入库单可全部入库
					if($shuliang_shengyu >= $ruku_needed){
						$shuliang_update = $ruku_needed;
						$shuliang_shengyu -= $ruku_needed;
					}else{
					//对应入库单只能部分入库($shuliang_shengyu<$ruku_needed)
						$shuliang_update = $shuliang_shengyu;
						$shuliang_shengyu = 0;
					}
					
					//检索在库商品信息是否存在
					$kucuncunzai = $this->checkCunzai($row,$rec_Chukulvli);
					
					//存在,更新该在库商品信息
					if($kucuncunzai != false){
						$this->updateZaiku($row,$shuliang_update,$rec_Chukulvli);
					}
					//不存在时，新做成在库商品信息
					else{
						$this->insertZaiku($row,$shuliang_update,$rec_Chukulvli);
					}								
					//商品移动履历的新生成				
					$this->insertLvli($row,$rkdbh,$shuliang_update,$rec_Chukulvli,$idx_lvli);
					$idx_lvli++;
					
					//更新库间调拨出库单明细信息的退货数量
					$this->updateTuihuo($shuliang_update,$rec);
					
					//该明细记录全部入库,处理下一条明细记录
					if($shuliang_shengyu == 0) break;
				
				}
				//该明细记录全部入库($shuliang_shengyu == 0),退出调拨出库信息循环,处理下一条明细记录
				if($shuliang_shengyu ==0) break;
				//该明细记录部分入库($shuliang_shengyu > 0),处理下一条调拨出库信息记录
				if($shuliang_shengyu >0) continue;				
								
			}
			//if数量还有剩余->返库入库数量超过了返库数量,退出整个明细的循环,返回页面
			if($shuliang_shengyu > 0){
				$result ['status'] = '3'; //返库入库数量超过了返库数量,错误,rollback
				$result ['data']['rIdx'] = ( int ) $row [$this->idx_ROWNUM]; //定位明细行index
				break;
			}
		}
		
		return $result;
		
	}
	
	/*
	 * 生成入库单明细信息
	 * 
	 * @param 	array 	$row:明细
	 * 			string 	$rkdbh:新生成的返库入库单编号
	 * 			int 	$idx_rukumingxi:入库单明细信息序号	
	 * @return array 
	 */
	public function InsertRukumingxi($row,$rkdbh,$idx_rukumingxi) {
		
		$data['QYBH'] = $_SESSION ['auth']->qybh;
		$data['DJBH'] = $rkdbh;
		$data['XUHAO'] = $idx_rukumingxi;
		$data['SHPBH'] = $row [$this->idx_SHPBH];
		$data['BZHSHL'] = $row [$this->idx_BZHSHL];
		$data['LSSHL'] = $row [$this->idx_LSSHL];
		$data['SHULIANG'] = $row [$this->idx_SHULIANG];
		$data['BEIZHU'] = $row [$this->idx_BEIZHU];
		$data['PIHAO'] = $row [$this->idx_PIHAO];
		$data['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		if ($row [$this->idx_SHCHRQ] != ""){
			$data['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" );
		}
		if ($row [$this->idx_BZHQZH] != ""){
			$data['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_BZHQZH] . "','YYYY-MM-DD')" );
		}
		$data['DRKQ'] = $row [$this->idx_KQBH];
		$data['DRKW'] = $row [$this->idx_KWBH];

		$this->_db->insert ( "H01DB012426", $data );
				
	}
	
	/*
	 * 用商品编号，批号，单位，变量：入库单号为条件，检索库间调拨出库单明细信息
	 * 
	 * @param 	array 	$row:明细	
	 * @return 	array ：	1)退货中数量
	 * 					2)调出库区
	 * 					3)调出库位
	 * 					4)序号
	 */
	public function getChukumx ($row) {
		
		$sql_Chuku = "SELECT THZHSHL,"				//退货中数量
					."DCHKQ,"						//调出库区
					."DCHKW,"						//调出库位
					."XUHAO"						//序号
					." FROM H01DB012411 WHERE QYBH = :QYBH AND DJBH = :DJBH AND SHPBH = :SHPBH"
					." AND PIHAO = :PIHAO AND BZHDWBH = :BZHDWBH AND TO_CHAR(SHCHRQ,'YYYY-MM-DD') = :SHCHRQ"				
					." AND THZHSHL > 0 ORDER BY XUHAO DESC FOR UPDATE";
						
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['DJBH'] = $_POST["DYDBCHKD"];
		$bind['SHPBH'] = $row [$this->idx_SHPBH];
		$bind['PIHAO'] = $row [$this->idx_PIHAO];
		$bind['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$bind['SHCHRQ'] = $row [$this->idx_SHCHRQ];
	
		return $this->_db->fetchAll( $sql_Chuku, $bind );
	}
	
	/*
	 * 抽取移动履历，取出该条调拨出库单明细对应的移动履历。
	 * 
	 * @param 	array 	$row:明细
	 * 			string  $rec:出库信息
	 * 
	 * @return 	array	该条调拨出库单明细对应的移动履历(入库单号)
	 *      or  bool	false
	 */
	public function getChukuLvli ($row,$rec) {
		
		//抽取移动履历，取出该条调拨出库单明细对应的移动履历
		$sql_chukuLvli = "SELECT RKDBH,MIN(ZKZHT) AS ZKZHT"
						. " FROM H01DB012405"
						. " WHERE QYBH = :QYBH AND SHPBH = :SHPBH AND PIHAO = :PIHAO AND YDDH = :YDDH"
						. " AND BZHDWBH = :BZHDWBH AND CKBH = :CKBH AND KQBH = :KQBH AND KWBH = :KWBH"
						. " AND TO_CHAR(SHCHRQ,'YYYY-MM-DD') = :SHCHRQ"
						. " GROUP BY RKDBH ORDER BY ZKZHT ASC,RKDBH DESC";
											
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['SHPBH'] = $row [$this->idx_SHPBH];
		$bind['PIHAO'] = $row [$this->idx_PIHAO];
		$bind['YDDH'] = $_POST["DYDBCHKD"];
		$bind['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$bind['SHCHRQ'] = $row [$this->idx_SHCHRQ];
		$bind['CKBH'] = $_POST["DCHCKBH"];
		$bind['KQBH'] = $rec["DCHKQ"];
		$bind['KWBH'] = $rec["DCHKW"];
			
		return $this->_db->fetchAll ( $sql_chukuLvli, $bind );
		
	}
	
	/*
	 * 抽取移动履历，取出该条调拨出库单明细对应的移动履历。
	 * 
	 * @param 	array 	$row:明细
	 * 			string  $rec:出库信息
	 * 
	 * @return 	int		其他退货数量
	 *      or  bool	false(没有其他退货)
	 */
	public function getRukuNeededLvli ($row,$rec_Chukulvli) {
		
		$sql_rukuneededlvli = "SELECT (-1) * SUM(SHULIANG) AS SHULIANG"
							. " FROM H01DB012405"
							. " WHERE QYBH = :QYBH AND SHPBH = :SHPBH AND PIHAO = :PIHAO AND BEIZHU = :BEIZHU"
							. " AND BZHDWBH = :BZHDWBH AND RKDBH = :RKDBH AND TO_CHAR(SHCHRQ,'YYYY-MM-DD') = :SHCHRQ";
		
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['SHPBH'] = $row [$this->idx_SHPBH];
		$bind['PIHAO'] = $row [$this->idx_PIHAO];
		$bind['BEIZHU'] = $_POST["DYDBCHKD"];
		$bind['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$bind['SHCHRQ'] = $row [$this->idx_SHCHRQ];
		$bind['RKDBH'] = $rec_Chukulvli["RKDBH"];
		
		return $this->_db->fetchOne ( $sql_rukuneededlvli, $bind );
	}
	
	
	
	/*
	 * 检索在库商品信息是否存在
	 * 
	 * @param 	array 	$row:明细
	 * 			string  $rkdbh_xiaoshou:入库单号
	 * 
	 * @return 	int		
	 */
	public function checkCunzai($row,$rec_Chukulvli) {
		
		$sql_kucun = "SELECT RKDBH"
					." FROM H01DB012404"
					." WHERE QYBH = :QYBH"
					." AND CKBH = :CKBH"
					." AND KQBH = :KQBH"
					." AND KWBH = :KWBH"
					." AND SHPBH = :SHPBH"
					." AND PIHAO = :PIHAO"
					." AND BZHDWBH = :BZHDWBH"
					." AND ZKZHT = '0'"
					." AND RKDBH = :RKDBH"
					." AND TO_CHAR(SHCHRQ,'YYYY-MM-DD') = :SHCHRQ FOR UPDATE WAIT 10";

		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['CKBH'] = $_POST ["DCHCKBH"];
		$bind['KQBH'] = $row [$this->idx_KQBH];
		$bind['KWBH'] = $row [$this->idx_KWBH];
		$bind['SHPBH'] = $row [$this->idx_SHPBH];
		$bind['PIHAO'] = $row [$this->idx_PIHAO];
		$bind['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$bind['RKDBH'] = $rec_Chukulvli['RKDBH'];
		$bind['SHCHRQ'] = $row [$this->idx_SHCHRQ];
				
		return $this->_db->fetchOne ( $sql_kucun, $bind );
		
	}
		
	/*
	 * 更新在库商品信息
	 * 
	 * @param 	array 	$row:明细
	 * 			int		$shuliang_update:更新数量
	 * 			array   $rec_Chukulvli:入库单号
	 * 
	 * @return 	bool	
	 */
	public function updateZaiku($row,$shuliang_update,$rec_Chukulvli) {
		
		$sql_update = "UPDATE H01DB012404"
					. " SET SHULIANG = SHULIANG + :SHULIANG_UPDATE,"
					. "ZZHCHKRQ = TO_DATE(:ZZHCHKRQ,'YYYY-MM-DD hh24:mi:ss')"
					. " WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQBH = :KQBH AND KWBH = :KWBH"
					. " AND SHPBH = :SHPBH AND PIHAO = :PIHAO AND BZHDWBH = :BZHDWBH AND ZKZHT = '0'"
					. " AND RKDBH = :RKDBH AND TO_CHAR(SHCHRQ,'YYYY-MM-DD') = :SHCHRQ";
		
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['SHULIANG_UPDATE'] = $shuliang_update;
		$bind['ZZHCHKRQ'] = '9999/12/31 23:59:59';
		$bind['CKBH'] = $_POST ["DCHCKBH"];
		$bind['KQBH'] = $row [$this->idx_KQBH];
		$bind['KWBH'] = $row [$this->idx_KWBH];
		$bind['SHPBH'] = $row [$this->idx_SHPBH];
		$bind['PIHAO'] = $row [$this->idx_PIHAO];
		$bind['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$bind['RKDBH'] = $rec_Chukulvli['RKDBH'];
		$bind['SHCHRQ'] = $row [$this->idx_SHCHRQ];
			
		$this->_db->query ( $sql_update,$bind );
	}
	
	/*
	 * 新做成在库商品信息
	 * 
	 * @param 	array 	$row:明细
	 * 			int		$shuliang_update:更新数量
	 * 			array  $rec_Chukulvli:入库单号
	 * 
	 * @return 	bool	
	 */
	public function insertZaiku($row,$shuliang_update,$rec_Chukulvli) {
		
		$zaiku['QYBH'] = $_SESSION ['auth']->qybh;
		$zaiku['CKBH'] = $_POST ["DCHCKBH"];
		$zaiku['KQBH'] = $row [$this->idx_KQBH];
		$zaiku['KWBH'] = $row [$this->idx_KWBH];
		$zaiku['SHPBH'] = $row [$this->idx_SHPBH];
		$zaiku['PIHAO'] = $row [$this->idx_PIHAO];
		$zaiku['RKDBH'] = $rec_Chukulvli['RKDBH'];
		$zaiku['ZKZHT'] = '0';
		$zaiku['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$zaiku['ZZHCHKRQ'] = new Zend_Db_Expr("TO_DATE('9999/12/31 23:59:59','YYYY-MM-DD hh24:mi:ss')");
		$zaiku['SHULIANG'] = $shuliang_update;
		if ($row [$this->idx_SHCHRQ] != ""){
			$zaiku['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" );
		}
		if ($row [$this->idx_BZHQZH] != ""){
			$zaiku['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_BZHQZH] . "','YYYY-MM-DD')" );
		}
		
		$this->_db->insert ( "H01DB012404", $zaiku );
	}
	
	/*
	 * 移动履历做成
	 * 
	 * @param 	array 	$row:明细
	 * 			string	$rkdbh:新生成的退货入库单编号
	 * 			int		$shuliang_update:更新数量
	 * 			array   $rec_Chukulvli:入库单号
	 * 			int		$idx_lvli:移动履历序号
	 * 
	 * @return 	bool	
	 */
	public function insertLvli($row,$rkdbh,$shuliang_update,$rec_Chukulvli,$idx_lvli) {
		
		$lvli['QYBH'] = $_SESSION ['auth']->qybh;
		$lvli['CKBH'] = $_POST ["DCHCKBH"];
		$lvli['KQBH'] = $row [$this->idx_KQBH];
		$lvli['KWBH'] = $row [$this->idx_KWBH];
		$lvli['SHPBH'] = $row [$this->idx_SHPBH];
		$lvli['PIHAO'] = $row [$this->idx_PIHAO];
		$lvli['RKDBH'] = $rec_Chukulvli['RKDBH'];
		$lvli['YDDH'] = $rkdbh;
		$lvli['XUHAO'] = $idx_lvli;
		if ($row [$this->idx_SHCHRQ] != ""){
			$lvli['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_SHCHRQ]."','YYYY-MM-DD')"); //生产日期;
		}
		if ($row [$this->idx_BZHQZH] != ""){
			$lvli['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_BZHQZH]."','YYYY-MM-DD')"); //保质期至
		}
		$lvli['CHLSHJ'] = new Zend_Db_Expr('SYSDATE');
		$lvli['SHULIANG'] = $shuliang_update;
		$lvli['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$lvli['ZHYZHL'] = '32';
		$lvli['ZKZHT'] = '0';
		$lvli['BEIZHU'] = $_POST["DYDBCHKD"];
		$lvli['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
		$lvli['BGZH'] = $_SESSION ['auth']->userId; //变更者		
		$lvli ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$lvli ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$this->_db->insert ( 'H01DB012405', $lvli );
	}
	
	/*
	 * 更新库间调拨出库单明细信息的退货数量
	 * 
	 * @param 	array 	$rec:退货信息
	 * 			int		$shuliang_update:更新数量
	 * 
	 * @return 	bool	
	 */
	public function updateTuihuo($shuliang_update,$rec) {
		
		$sql_tuihuo = "UPDATE H01DB012411"
					. " SET THZHSHL = THZHSHL - :SHULIANG_UPDATE,"
					. " THSHL = THSHL + :SHULIANG_UPDATE"
					. " WHERE QYBH = :QYBH AND DJBH = :DJBH AND XUHAO = :XUHAO";
		
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['SHULIANG_UPDATE'] = $shuliang_update;
		$bind['DJBH'] = $_POST ["DYDBCHKD"];
		$bind['XUHAO'] = $rec ["XUHAO"];
			
		$this->_db->query ( $sql_tuihuo,$bind );
	}
	
	/*
	 * 出库单状态更新H01DB012410
	 * 
	 * @param
	 * @return 	bool	
	 */
	public function updateChukuZht() {
		$sql = "SELECT SUM(WSHHSHL) AS WSHHSHL,"//未收货数量
			 . "SUM(THZHSHL) AS THZHSHL"		//退货中数量
			 . " FROM H01DB012411 WHERE QYBH = :QYBH AND DJBH = :DJBH";

		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['DJBH'] = $_POST ["DYDBCHKD"];
			 
		$recs = $this->_db->fetchRow( $sql,$bind );
		
		//如果集计的未出库数量 ≠ 0 或者 集计的退货中数量 ≠ 0,出库单状态(CHKDZHT) = ‘2’未完全入库
		if($recs["WSHHSHL"] != 0 || $recs["WSHHSHL"] != 0){
			$upt_zht = '2';
		}else{
			//如果集计的未出库数量 = 0 并且 集计的退货中数量 = 0,出库单状态(CHKDZHT) = ‘3’已入库
			$upt_zht = '3';
		}
		
		$sql_updchukuzht = "UPDATE H01DB012410"
						 . " SET CHKDZHT = :CHKDZHT,BGRQ = sysdate,BGZH = :BGZH"
						 . " WHERE QYBH = :QYBH"
			 			 . " AND DJBH = :DJBH";
		
		$bind['CHKDZHT'] = $upt_zht;		 
		$bind['BGZH'] = $_SESSION ['auth']->userId; //变更者	
		
		$this->_db->query ( $sql_updchukuzht,$bind );
	}
	
	/*
	 * 返库单状态更新H01DB012423
	 * 
	 * @param
	 * @return 	bool	
	 */
	public function updateFankuZht() {
		$sql = "UPDATE H01DB012423"
			 . " SET CHKDZHT = '2',BGRQ = sysdate,BGZH = :BGZH"
			 . " WHERE QYBH = :QYBH"
			 . " AND DJBH = :DJBH";
			 
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['DJBH'] = $_POST["DYDBFKD"];
		$bind['BGZH'] = $_SESSION ['auth']->userId; //变更者	
			 
		$this->_db->query ( $sql,$bind );
	}
	
}
	
	
	