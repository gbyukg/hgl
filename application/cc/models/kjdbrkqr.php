<?php
/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：        库间调拨入库确认(KJDBRKQR)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/01/20
 ***** 更新履历：
 ******************************************************************/

class cc_models_kjdbrkqr extends Common_Model_Base {
	private $bh = null;                 // 编号
	private $idx_ROWNUM = 0;            // 行号
	private $idx_SHPBH = 1;             // 商品编号
	private $idx_SHPMCH = 2;            // 商品名称
	private $idx_GUIGE = 3;             // 规格
	private $idx_BZHDWM = 4;            // 包装单位
	private $idx_KUWEI = 5;             // 调入库位
	private $idx_PIHAO = 6;             // 批号
	private $idx_SHCHRQ = 7;            // 生产日期
	private $idx_BZHQZH = 8;            // 保质期至
	private $idx_BZHSHL = 9;            // 包装数量
	private $idx_LSSHL = 10;            // 零散数量
	private $idx_SHULIANG = 11;         // 数量
	private $idx_WSHSL = 12;            // 未收货数量
	private $idx_CHANDI = 13;           // 产地
	private $idx_BEIZHU = 14;           // 备注
	private $idx_BZHDWBH = 15;          // 包装单位编号
	private $idx_TYMCH = 16;            // 通用名称
	private $idx_JLGG = 17;             // 计量规格
	private $idx_CKBH = 18;             // 仓库编号
	private $idx_KQBH = 19;             // 库区编号
	private $idx_KWBH = 20;             // 库位编号
	private $idx_SHFSHKW = 21;          // 是否散货库位


	/**
	 * 库间调拨出库明细列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridMingxiData($filter) {
				//排序用字段名
		$fields = array ("", "SHPBH", "NLSSORT(SHPMCH,'NLS_SORT=SCHINESE_PINYIN_M')");

		$sql = "SELECT "          
                ."SHPBH,"               //商品编号
                ."SHPMCH,"              //商品名称
                ."GUIGE,"               //规格
                ."BZHDWMCH,"   //包装单位
                ."PIHAO,"               //批号
                ."TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,"     //生产日期
                ."TO_CHAR(BZHQZH,'YYYY-MM') AS BZHQZH,"     //保质期至
                ."BZHSHL,"              //包装数量
                ."LSSHL,"               //零散数量
                ."SHULIANG,"            //数量
                ."WSHHSHL,"             //未收货数量
                ."CHANDI,"              //产地
                ."BEIZHU,"              //备注
                ."BZHDWBH,"             //包装单位编号
                ."TYMCH,"               //通用名
                ."JLGG "                //计量规格
              ."FROM H01VIEW012411 "
              ."WHERE QYBH = :QYBH "       //区域编号
              ."AND DJBH = :DJBH "; 
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DJBH'] = $filter ["bh"];
				
		//排序
		$sql .= " ORDER BY " . $fields [$filter["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",DJBH,XUHAO";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne( $pagedSql["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll( $pagedSql["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true, $totalCount, $filter["posStart"] );
		
	}

	/**
	 * 库间调拨出库单信息获取
	 *
	 * @param string $bh
	 * @return array[]
	 */
	function getinfoData($bh){
		//检索SQL
		$sql = "SELECT KPRQ,"           //开票日期
                ."DJBH,"                //单据编号
                ."BMMCH,"               //部门名称
                ."YWYXM,"                //员工名称
                ."DCHCKMCH,"    //调出仓库
                ."DRCKMCH,"    //调入仓库
                ."DRCKDZH,"             //调入仓库地址
                ."SHFPS,"               //是否配送
                ."DHHM,"                //电话号码
                ."BEIZHU,"              //备注
                ."DCHCK,"               //调出仓库编号
                ."DRCK,"                //调入仓库编号
                ."TO_CHAR(BGRQ,'YYYY-MM-DD HH24:mi:ss') AS BGRQ "    //变更日期
              ."FROM H01VIEW012410 "
              ."WHERE QYBH = :QYBH "
              ."AND DJBH = :DJBH ";

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
		$bind ['DJBH'] = $bh;                         //单据编号
		
		return $this->_db->fetchRow( $sql, $bind );
	}
	
	
	/**
	 * CHECK数据时间是否与画面取的时候时间一致
	 *
	 * @return bool
	 */
	function timeCheck(){
		//检索SQL
		$sql = "SELECT TO_CHAR(BGRQ,'yyyy-mm-dd hh24:mi:ss') AS BGRQ "    //变更日期
			  ."FROM H01DB012410 "
			  ."WHERE QYBH = :QYBH "
			  ."AND DJBH = :DJBH ";

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
		$bind ['DJBH'] = $_POST ['DJBH'];             //单据编号
		
		$TIME = $this->_db->fetchOne( $sql, $bind );
		$BGRQ = $_POST ['BGRQ'];
		
		if($TIME == $BGRQ){
			return true;
		}else{
			return false;
		}
	}
	
	
	/**
	 * CHECK入库数量大于未收货数量
	 *
	 * @return bool
	 */
	function slCheck(){
		$result ['status'] = '0';
		//检索SQL
		$sql = "SELECT WSHHSHL "   
			  ."FROM H01DB012411 "
			  ."WHERE QYBH = :QYBH "
			  ."AND DJBH = :DJBH "
			  ."AND SHPBH = :SHPBH "
			  ."AND SHCHRQ = TO_DATE(:SHCHRQ ,'YYYY-MM-DD') "
			  ."AND PIHAO = :PIHAO ";

		foreach( $_POST ["#grid_mingxi"] as $grid ){
			//绑定查询条件
			$data ['QYBH'] = $_SESSION ['auth']->qybh;           //区域编号
			$data ['DJBH'] = $_POST ['DJBH'];                    //单据编号
			$data ['SHPBH'] = $grid [$this->idx_SHPBH];          //商品编号
			$data ['SHCHRQ'] = $grid [$this->idx_SHCHRQ];        //生产日期
			$data ['PIHAO'] = $grid [$this->idx_PIHAO];          //批号

			$wshsl = $this->_db->fetchOne( $sql, $data );

			if ( (int)$wshsl < (int)$grid [$this->idx_SHULIANG] ){
				$result ['status'] = '4';   
				$result ['data']['rIdx'] = (int)$grid[$this->idx_ROWNUM];  //定位明细行index
			}
		}
		return $result;
	}
	
	
	/**
	 * CHECK入库库区类型是否与该商品指定库区类型一致
	 *
	 * @return bool
	 */
	function kqlxCheck(){
		$result ['status'] = '0';
		//检索SQL
		$sql1 = "SELECT KQLX "   
			  ."FROM H01DB012402 "
			  ."WHERE QYBH = :QYBH "
			  ."AND CKBH = :CKBH "
			  ."AND KQBH = :KQBH ";
			  
		$sql2 = "SELECT ZHDKQLX "   
			  ."FROM H01DB012101 "
			  ."WHERE QYBH = :QYBH "
			  ."AND SHPBH = :SHPBH ";

		foreach( $_POST ["#grid_mingxi"] as $grid ){
			if ( $grid [$this->idx_SHFSHKW] == '1' )continue;   //是否散货库位 = 0(否)的场合，进行以下验证
			//绑定查询条件
			$data1 ['QYBH'] = $_SESSION ['auth']->qybh;       //区域编号
			$data1 ['CKBH'] = $grid [$this->idx_CKBH];        //仓库编号
			$data1 ['KQBH'] = $grid [$this->idx_KQBH];        //库区编号
			$data2 ['QYBH'] = $_SESSION ['auth']->qybh;       //区域编号
			$data2 ['SHPBH'] = $grid [$this->idx_SHPBH];      //商品编号

			$KQLX = $this->_db->fetchOne( $sql1, $data1 );
			$ZHDKQLX = $this->_db->fetchOne( $sql2, $data2 );

			if( $KQLX != $ZHDKQLX ){
				$result ['status'] = '5';   
				$result ['data']['rIdx'] = (int)$grid[$this->idx_ROWNUM];  //定位明细行index
				
				$sql3 = "SELECT NEIRONG FROM H01DB012001 "
						  ."WHERE QYBH = :QYBH "
						  ."AND CHLID = 'KQLX' "
						  ."AND ZIHAOMA = :ZIHAOMA";
				$data3 ['ZIHAOMA'] = $ZHDKQLX;
				$ZHDKQLXM = $this->_db->fetchOne( $sql3, $data3 );		  
				$result ['data']['kqlx'] = $ZHDKQLXM;
			}
		}
		return $result;
	}
	
	
	/**
	 * 获取库位状态
	 * 
	 * @return array[]
	 */
	function getKwzht(){
		$result ['status'] = '0';

		$sql = "SELECT A.KWZHT,A.SHFSHKW,B.KQZHT,C.CKZHT FROM H01DB012403 AS A "
				."LEFT JOIN H01DB012402 AS B ON A.QYBH = B.QYBH AND A.CKBH = B.CKBH AND A.KQBH = B.KQBH "
				."LEFT JOIN H01DB012401 AS C ON A.QYBH = C.QYBH AND A.CKBH = C.CKBH "
				."WHERE A.QYBH = :QYBH AND A.CKBH = :CKBH AND A.KQBH = :KQBH AND A.KWBH = :KWBH";

		foreach( $_POST ["#grid_mingxi"] as $grid ) {
			$data ['QYBH'] = $_SESSION ['auth']->qybh;         //区域编号
			$data ['CKBH'] = $grid [$this->idx_CKBH];          //仓库编号
			$data ['KQBH'] = $grid [$this->idx_KQBH];          //库区编号
			$data ['KWBH'] = $grid [$this->idx_KWBH];          //库位编号
	
			$zht = $this->_db->fetchOne( $sql, $data );

			if ( $zht['KWZHT'] == '0' || $zht['KWZHT'] == '9' ){
				$result ['status'] = '9';      //库位被冻结
				$result ['data']['rIdx'] = (int)$grid[$this->idx_ROWNUM];  //定位明细行index
			}
		}
		return $result;
	}
	
	
	/**
	 * 单据信息保存
	 * @param  string  $bh:编号
	 * 
	 * @return bool
	 */
	public function saveMain($bh){
		$data ['QYBH'] = $_SESSION ['auth']->qybh;       //区域编号
		$data ['DJBH'] = $bh;                            //出库单编号
		$data ['DYDBCHKD'] = $_POST ['DJBH'];            //对应调拨出库单
		$data ['KPRQ'] = new Zend_Db_Expr( "TO_DATE('".$_POST ['KPRQ']."','YYYY-MM-DD')" );    //开票日期
		$data ['BMBH'] = $_POST ['BMBH'];                //部门编号
		$data ['YWYBH'] = $_POST ['YWYBH'];              //业务员编号
		$data ['DCHCK'] = $_POST ['DCCKBH'];             //调出仓库
		$data ['DRCK'] = $_POST ['DRCKBH'];              //调入仓库
		$data ['DRCKDZH'] = $_POST ['DIZHI'];            //调入仓库地址
		$data ['DHHM'] = $_POST ['DHHM'];                //电话
		$data ['SHFPS'] = $_POST ['SHFPS'];              //是否配送         0:否(未选中) 1:是(选中)
		$data ['BEIZHU'] = $_POST ['BEIZHU'];            //备注
		$data ['SHLHJ'] = $_POST ['SHULIANGHJ'];         //数量合计
		$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId;     //变更者
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		return $this->_db->insert ( "H01DB012412", $data );     //插入库间调拨入库单信息
	}
	
	
	/*
	 * 单据明细保存
	 * @param  string  $chkdbh:   出库单编号
	 * 
	 */
	public function saveMingxi($bh) {
		$idx = 1;           //序号自增
        //循环所有明细行，保存出库单明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == '')continue;

			$data ['QYBH'] = $_SESSION ['auth']->qybh;        //区域编号
			$data ['DJBH'] = $bh;                             //出库单编号
			$data ['XUHAO'] = $idx ++;                        //序号
			$data ['SHPBH'] = $grid [$this->idx_SHPBH];       //商品编号
			$data ['DRKQ'] = $grid [$this->idx_KQBH];         //调入库区
			$data ['DRKW'] = $grid [$this->idx_KWBH];         //调入库位
			$data ['PIHAO'] = $grid [$this->idx_PIHAO];       //批号
			$data ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" ); //生产日期
			$data ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM')" ); //保质期至
			$data ['BZHSHL'] = ($grid [$this->idx_BZHSHL] == null) ? 0 : $grid [$this->idx_BZHSHL]; //包装数量
			$data ['LSSHL'] = ($grid [$this->idx_LSSHL] == null) ? 0 : $grid [$this->idx_LSSHL];    //零散数量
			$data ['SHULIANG'] = $grid [$this->idx_SHULIANG]; //数量
			$data ['BEIZHU'] = $grid [$this->idx_BEIZHU];     //备注
		    $data ['BZHDWBH'] = $grid [$this->idx_BZHDWBH];   //包装单位编号

			$this->_db->insert ( "H01DB012413", $data );	  //插入库间调拨入库单明细信息	
		}
	}
	
	
	/*
	 * 更新DB:在库商品信息（H01DB012404),商品移动履历（H01DB012405),
	 * 		     库间调拨出库单明细信息（H01DB012411)的未收货数量.
	 */
	public function updateKucun($bh){
		$result ['status'] = '0';
		//检索库间调拨出库单明细信息。
		foreach ( $_POST ["#grid_mingxi"] as $row ){
			if ($row [$this->idx_SHPBH] == '')continue;

			$RKSL = $row [$this->idx_SHULIANG];      //预入库数量

			$sql1 = "SELECT WSHHSHL,DCHKQ,DCHKW,XUHAO".
			       " FROM H01DB012411 " .
			       " WHERE QYBH = :QYBH " .          //区域编号
                   " AND DJBH = :DJBH " .            //单据编号
                   " AND SHPBH = :SHPBH " .          //商品编号
                   " AND PIHAO = :PIHAO " .          //批号
				   " AND SHCHRQ = TO_DATE(:SHCHRQ ,'YYYY-MM-DD') ".    //生产日期
				   " AND WSHHSHL > 0 ".              //未收货数量   > 0
                   " ORDER BY XUHAO";

			//绑定查询变量
			$bind1 ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind1 ['DJBH'] = $_POST ['DJBH'];
			$bind1 ['SHPBH'] = $row [$this->idx_SHPBH];
			$bind1 ['PIHAO'] = $row [$this->idx_PIHAO];
			$bind1 ['SHCHRQ'] = $row [$this->idx_SHCHRQ];

			$infos = $this->_db->fetchAll( $sql1, $bind1 );

			foreach( $infos as $info ){
				$sql2 = "SELECT RKDBH,MAX(ZKZHT) AS ZKZHT FROM H01DB012405 ".
						"WHERE QYBH = :QYBH ".         //区域编号
						"AND SHPBH = :SHPBH ".         //商品编号
						"AND PIHAO = :PIHAO ".         //批号
						"AND YDDH = :YDDH ".           //移动单号
						"AND BZHDWBH = :BZHDWBH ".     //包装单位编号
						"AND CKBH = :CKBH ".           //仓库编号
						"AND KQBH = :KQBH ".           //库区编号
						"AND KWBH = :KWBH ".           //库位编号
						"AND SHCHRQ = TO_DATE(:SHCHRQ ,'YYYY-MM-DD') ".    //生产日期
						"GROUP BY RKDBH ".
						"ORDER BY ZKZHT DESC,RKDBH ASC";
				
				//绑定查询变量
				$bind2 ['QYBH'] = $_SESSION ['auth']->qybh;
				$bind2 ['SHPBH'] = $row [$this->idx_SHPBH];
				$bind2 ['PIHAO'] = $row [$this->idx_PIHAO];
				$bind2 ['YDDH'] = $_POST ['DJBH'];
				$bind2 ['BZHDWBH'] = $row [$this->idx_BZHDWBH];
				$bind2 ['CKBH'] = $_POST ['DCCKBH'];
				$bind2 ['KQBH'] = $info['DCHKQ'];
				$bind2 ['KWBH'] = $info['DCHKW'];
				$bind2 ['SHCHRQ'] = $row [$this->idx_SHCHRQ];
				
				$recs = $this->_db->fetchAll( $sql2, $bind2 );
				
				$idx = 1;                         //定义序号
				foreach( $recs as $rec ){
					$sql3 = "SELECT -1*SUM(SHULIANG) FROM H01DB012405 ".
							"WHERE QYBH = :QYBH ".             //区域编号
							"AND SHPBH = :SHPBH ".             //商品编号
							"AND PIHAO = :PIHAO ".             //批号
							"AND BEIZHU = :BEIZHU ".           //备注
							"AND BZHDWBH = :BZHDWBH ".         //包装单位编号
							"AND RKDBH = :RKDBH ".             //入库单编号
							"AND SHCHRQ = TO_DATE(:SHCHRQ ,'YYYY-MM-DD') ";    //生产日期
				
					//绑定查询变量
					$bind3 ['QYBH'] = $_SESSION ['auth']->qybh;
					$bind3 ['SHPBH'] = $row [$this->idx_SHPBH];
					$bind3 ['PIHAO'] = $row [$this->idx_PIHAO];
					$bind3 ['BEIZHU'] = $_POST ['DJBH'];
					$bind3 ['BZHDWBH'] = $row [$this->idx_BZHDWBH];
					$bind3 ['RKDBH'] = $rec['RKDBH'];
					$bind3 ['SHCHRQ'] = $row [$this->idx_SHCHRQ];
					//以变量：入库单号等条件，检索tbl:商品移动履历中，待入库商品的数量合计
					$slhj = $this->_db->fetchOne( $sql3, $bind3 );  //获取入库单单位未收货数量合计 
					
					if( $RKSL >= $slhj ){   //入库数量 >=变量：入库单单位未收货数量
						$sql4 = "SELECT SHULIANG FROM H01DB012404 ".
								"WHERE QYBH = :QYBH ".             //区域编号
								"AND SHPBH = :SHPBH ".             //商品编号
								"AND PIHAO = :PIHAO ".             //批号
								"AND CKBH = :CKBH ".               //仓库编号
								"AND KQBH = :KQBH ".               //库区编号
								"AND KWBH = :KWBH ".               //库位编号
								"AND ZKZHT = :ZKZHT ".             //在库状态
								"AND BZHDWBH = :BZHDWBH ".          //包装单位编号
								"AND RKDBH = :RKDBH ".              //入库单编号
								"AND SHCHRQ = TO_DATE(:SHCHRQ ,'YYYY-MM-DD') ".    //生产日期
								"FOR UPDATE WAIT 10 ";             //锁定
					
						//绑定查询变量
						$bind4 ['QYBH'] = $_SESSION ['auth']->qybh;
						$bind4 ['SHPBH'] = $row [$this->idx_SHPBH];
						$bind4 ['PIHAO'] = $row [$this->idx_PIHAO];
						$bind4 ['CKBH'] = $row [$this->idx_CKBH];
						$bind4 ['KQBH'] = $row [$this->idx_KQBH];
						$bind4 ['KWBH'] = $row [$this->idx_KWBH];
						$bind4 ['ZKZHT'] = '0';
						$bind4 ['BZHDWBH'] = $row [$this->idx_BZHDWBH];
						$bind4 ['RKDBH'] = $rec['RKDBH'];
						$bind4 ['SHCHRQ'] = $row [$this->idx_SHCHRQ];
						
						$sl = $this->_db->fetchAll( $sql4, $bind4 );
						
						if( $sl == false ){     //上记在库商品信息不存在时，新做成在库商品信息
							//生成新在库信息
							$zaiku ["QYBH"] = $_SESSION ['auth']->qybh;          //区域编号
							$zaiku ["CKBH"] = $row [$this->idx_CKBH];            //仓库编号
							$zaiku ["KQBH"] = $row [$this->idx_KQBH];            //库区编号
							$zaiku ["KWBH"] = $row [$this->idx_KWBH];            //库位编号
							$zaiku ["SHPBH"] = $row [$this->idx_SHPBH];          //商品编号
							$zaiku ["PIHAO"] = $row [$this->idx_PIHAO];          //批号
							$zaiku ["RKDBH"] = $rec['RKDBH'];                    //入库单号
							$zaiku ["ZKZHT"] = '0';                              //在库状态
							$zaiku ['BZHDWBH'] = $row [$this->idx_BZHDWBH];      //包装单位编号
							$zaiku ['ZZHCHKRQ'] = new Zend_Db_Expr("TO_DATE('9999/12/31 23:59:59','YYYY-MM-DD HH24:mi:ss')"); //最终出库日期
							$zaiku ['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_SHCHRQ]."','YYYY-MM-DD')");   //生产日期
							$zaiku ['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_BZHQZH]."','YYYY-MM')");      //保质期至
							$zaiku ["SHULIANG"] = $slhj;                         //数量
				
							$this->_db->insert ( 'H01DB012404', $zaiku );
							
						}else{    //上记在库商品信息存在时，更新该在库商品信息
							//更新在库信息
							$sql_zaiku = "UPDATE H01DB012404 ".
							             "SET SHULIANG = SHULIANG + :SHULIANG," .
							             " ZZHCHKRQ = TO_DATE('9999/12/31 23:59:59','YYYY/MM/DD HH24:mi:ss') ".
							             " WHERE QYBH = :QYBH ".
							             " AND CKBH = :CKBH " .
							             " AND KQBH = :KQBH ".
							             " AND KWBH = :KWBH ".
							             " AND SHPBH = :SHPBH " .
							             " AND PIHAO = :PIHAO " .
							             " AND ZKZHT = :ZKZHT " .
							             " AND RKDBH = :RKDBH " .
							             " AND BZHDWBH = :BZHDWBH ".
							             " AND SHCHRQ = TO_DATE(:SHCHRQ ,'YYYY-MM-DD') ";
							
							$bind_zaiku ['SHULIANG'] = $slhj;  
							$bind_zaiku ['QYBH'] = $_SESSION ['auth']->qybh;
							$bind_zaiku ['CKBH'] = $row [$this->idx_CKBH];
							$bind_zaiku ['KQBH'] = $row [$this->idx_KQBH];
							$bind_zaiku ['KWBH'] = $row [$this->idx_KWBH]; 
							$bind_zaiku ['SHPBH'] = $row [$this->idx_SHPBH]; 
							$bind_zaiku ['PIHAO'] = $row [$this->idx_PIHAO];  
							$bind_zaiku ['BZHDWBH'] = $row [$this->idx_BZHDWBH];  
							$bind_zaiku ['RKDBH'] = $rec['RKDBH']; 
							$bind_zaiku ['ZKZHT'] = '0';
							$bind_zaiku ['SHCHRQ'] = $row [$this->idx_SHCHRQ];
							             
							$this->_db->query ( $sql_zaiku,$bind_zaiku );
						}
						
						//生成在库移动履历
						$lvli ["QYBH"] = $_SESSION ['auth']->qybh;    //区域编号
						$lvli ["CKBH"] = $row [$this->idx_CKBH];      //仓库编号
						$lvli ["KQBH"] = $row [$this->idx_KQBH];      //库区编号
						$lvli ["KWBH"] = $row [$this->idx_KWBH];      //库位编号
						$lvli ["SHPBH"] = $row [$this->idx_SHPBH];    //商品编号
						$lvli ["PIHAO"] = $row [$this->idx_PIHAO];    //批号
						$lvli ["RKDBH"] = $rec['RKDBH'];              //入库单号
						$lvli ["YDDH"] = $bh;                         //移动单号
						$lvli ["XUHAO"] = $idx ++;                    //序号
						$lvli ['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_SHCHRQ]."','YYYY-MM-DD')"); //生产日期
						$lvli ['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_BZHQZH]."','YYYY-MM')");    //保质期至
						$lvli ['CHLSHJ'] = new Zend_Db_Expr('SYSDATE');
						$lvli ["SHULIANG"] = $slhj;                   //移动数量
						$lvli ["ZHYZHL"] = '33';                            //转移种类 [33:库间调拨入库]
						$lvli ["BZHDWBH"] = $row [$this->idx_BZHDWBH];      //包装单位编号
						$lvli ["ZKZHT"] = '0';                              //在库状态
						$lvli ["BEIZHU"] = $_POST ['DJBH'];                 //备注
						$lvli['BGRQ'] = new Zend_Db_Expr('SYSDATE');   //变更日期
						$lvli['BGZH'] = $_SESSION ['auth']->userId;    //变更者
						$lvli ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
						$lvli ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
						$this->_db->insert ( 'H01DB012405', $lvli );
						
						
						//更新库间调拨出库单明细未收货数量
						$sql_wshsl = "UPDATE H01DB012411".
						             " SET WSHHSHL = WSHHSHL - :SHULIANG" .
						             " WHERE QYBH = :QYBH ".
						             " AND DJBH = :DJBH " .
						             " AND XUHAO = :XUHAO ";
						
						$bind_wshsl ['SHULIANG'] = $slhj;
						$bind_wshsl ['QYBH'] = $_SESSION ['auth']->qybh;
						$bind_wshsl ['DJBH'] = $_POST ['DJBH'];
						$bind_wshsl ['XUHAO'] = $info ['XUHAO'];
						             
						$this->_db->query ( $sql_wshsl,$bind_wshsl );
						
						if ($RKSL == $slhj){
							$RKSL = 0;
							break;
						}else{
							$RKSL = $RKSL - $slhj;
						}
					}else{         //入库数量 < 变量：入库单单位未收货数量
						$sql4 = "SELECT SHULIANG FROM H01DB012404 ".
								"WHERE QYBH = :QYBH ".             //区域编号
								"AND SHPBH = :SHPBH ".             //商品编号
								"AND PIHAO = :PIHAO ".             //批号
								"AND CKBH = :CKBH ".               //仓库编号
								"AND KQBH = :KQBH ".               //库区编号
								"AND KWBH = :KWBH ".               //库位编号
								"AND ZKZHT = :ZKZHT ".             //在库状态
								"AND BZHDWBH = :BZHDWBH ".          //包装单位编号
								"AND RKDBH = :RKDBH ".              //入库单编号
								"AND SHCHRQ = TO_DATE(:SHCHRQ ,'YYYY-MM-DD') ".    //生产日期
								"FOR UPDATE WAIT 10 ";             //锁定
					
						//绑定查询变量
						$bind4 ['QYBH'] = $_SESSION ['auth']->qybh;
						$bind4 ['SHPBH'] = $row [$this->idx_SHPBH];
						$bind4 ['PIHAO'] = $row [$this->idx_PIHAO];
						$bind4 ['CKBH'] = $row [$this->idx_CKBH];
						$bind4 ['KQBH'] = $row [$this->idx_KQBH];
						$bind4 ['KWBH'] = $row [$this->idx_KWBH];
						$bind4 ['ZKZHT'] = '0';
						$bind4 ['BZHDWBH'] = $row [$this->idx_BZHDWBH];
						$bind4 ['RKDBH'] = $rec['RKDBH'];
						$bind4 ['SHCHRQ'] = $row [$this->idx_SHCHRQ];
						
						$sl = $this->_db->fetchAll( $sql4, $bind4 );
						
						if( $sl == false ){     //上记在库商品信息不存在时，新做成在库商品信息
							//生成新在库信息
							$zaiku ["QYBH"] = $_SESSION ['auth']->qybh;          //区域编号
							$zaiku ["CKBH"] = $row [$this->idx_CKBH];            //仓库编号
							$zaiku ["KQBH"] = $row [$this->idx_KQBH];            //库区编号
							$zaiku ["KWBH"] = $row [$this->idx_KWBH];            //库位编号
							$zaiku ["SHPBH"] = $row [$this->idx_SHPBH];          //商品编号
							$zaiku ["PIHAO"] = $row [$this->idx_PIHAO];          //批号
							$zaiku ["RKDBH"] = $rec['RKDBH'];                    //入库单号
							$zaiku ["ZKZHT"] = '0';                              //在库状态
							$zaiku ['BZHDWBH'] = $row [$this->idx_BZHDWBH];      //包装单位编号
							$zaiku ['ZZHCHKRQ'] = new Zend_Db_Expr("TO_DATE('9999/12/31 23:59:59','YYYY-MM-DD HH24:mi:ss')"); //最终出库日期
							$zaiku ['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_SHCHRQ]."','YYYY-MM-DD')");   //生产日期
							$zaiku ['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_BZHQZH]."','YYYY-MM')");      //保质期至
							$zaiku ["SHULIANG"] = $RKSL;                         //数量
				
							$this->_db->insert ( 'H01DB012404', $zaiku );
						}else{    //上记在库商品信息存在时，更新该在库商品信息
							//更新在库信息
							$sql_zaiku = "UPDATE H01DB012404 ".
							             "SET SHULIANG = SHULIANG + :SHULIANG," .
							             " ZZHCHKRQ = TO_DATE('9999/12/31 23:59:59','YYYY/MM/DD HH24:mi:ss') ".
							             " WHERE QYBH = :QYBH ".
							             " AND CKBH = :CKBH " .
							             " AND KQBH = :KQBH ".
							             " AND KWBH = :KWBH ".
							             " AND SHPBH = :SHPBH " .
							             " AND PIHAO = :PIHAO " .
							             " AND ZKZHT = :ZKZHT " .
							             " AND RKDBH = :RKDBH " .
							             " AND BZHDWBH = :BZHDWBH ".
							             " AND SHCHRQ = TO_DATE(:SHCHRQ ,'YYYY-MM-DD') ";
							
							$bind_zaiku ['SHULIANG'] = $RKSL;  
							$bind_zaiku ['QYBH'] = $_SESSION ['auth']->qybh;
							$bind_zaiku ['CKBH'] = $row [$this->idx_CKBH];
							$bind_zaiku ['KQBH'] = $row [$this->idx_KQBH];
							$bind_zaiku ['KWBH'] = $row [$this->idx_KWBH]; 
							$bind_zaiku ['SHPBH'] = $row [$this->idx_SHPBH]; 
							$bind_zaiku ['PIHAO'] = $row [$this->idx_PIHAO];  
							$bind_zaiku ['BZHDWBH'] = $row [$this->idx_BZHDWBH];  
							$bind_zaiku ['RKDBH'] = $rec['RKDBH']; 
							$bind_zaiku ['ZKZHT'] = '0';
							$bind_zaiku ['SHCHRQ'] = $row [$this->idx_SHCHRQ];
							             
							$this->_db->query ( $sql_zaiku,$bind_zaiku );
						}
						//生成在库移动履历
						$lvli ["QYBH"] = $_SESSION ['auth']->qybh;    //区域编号
						$lvli ["CKBH"] = $row [$this->idx_CKBH];      //仓库编号
						$lvli ["KQBH"] = $row [$this->idx_KQBH];      //库区编号
						$lvli ["KWBH"] = $row [$this->idx_KWBH];      //库位编号
						$lvli ["SHPBH"] = $row [$this->idx_SHPBH];    //商品编号
						$lvli ["PIHAO"] = $row [$this->idx_PIHAO];    //批号
						$lvli ["RKDBH"] = $rec['RKDBH'];              //入库单号
						$lvli ["YDDH"] = $bh;                         //移动单号
						$lvli ["XUHAO"] = $idx ++;                    //序号
						$lvli ['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_SHCHRQ]."','YYYY-MM-DD')"); //生产日期
						$lvli ['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_BZHQZH]."','YYYY-MM')");    //保质期至
						$lvli ['CHLSHJ'] = new Zend_Db_Expr('SYSDATE');
						$lvli ["SHULIANG"] = $RKSL;                         //移动数量
						$lvli ["ZHYZHL"] = '33';                            //转移种类 [33:库间调拨入库]
						$lvli ["BZHDWBH"] = $row [$this->idx_BZHDWBH];      //包装单位编号
						$lvli ["ZKZHT"] = '0';                              //在库状态
						$lvli ["BEIZHU"] = $_POST ['DJBH'];                 //备注
						$lvli['BGRQ'] = new Zend_Db_Expr('SYSDATE');  //变更日期
						$lvli['BGZH'] = $_SESSION ['auth']->userId;   //变更者
						$lvli ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
						$lvli ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
						$this->_db->insert ( 'H01DB012405', $lvli );
						
						//更新库间调拨出库单明细未收货数量
						$sql_wshsl = "UPDATE H01DB012411".
						             " SET WSHHSHL = WSHHSHL - :SHULIANG" .
						             " WHERE QYBH = :QYBH ".
						             " AND DJBH = :DJBH " .
						             " AND XUHAO = :XUHAO ";
						
						$bind_wshsl ['SHULIANG'] = $RKSL;
						$bind_wshsl ['QYBH'] = $_SESSION ['auth']->qybh;
						$bind_wshsl ['DJBH'] = $_POST ['DJBH'];
						$bind_wshsl ['XUHAO'] = $info ['XUHAO'];

						$this->_db->query ( $sql_wshsl,$bind_wshsl );
						
						$RKSL = 0;
						break;
					}
				}
			}
			if( $RKSL > 0 ){
				$result ['status'] = '3';           //入库数量超过了调拨出库数量
				$result ['data']['rIdx'] = (int)$row[$this->idx_ROWNUM];  //定位明细行index
			}
		}

		return $result;
	}

	
	/*
	 * 更新库间调拨出库单出库状态
	 */
	public function updateCgthzht(){
		$sqlSELECT = "SELECT SUM(WSHHSHL) AS WSHHSHL,SUM(THSHL) AS THSHL"
					." FROM H01DB012411"
                    ." WHERE QYBH = :QYBH"
                    ." AND DJBH = :DJBH";
        $bindSELECT ['QYBH'] = $_SESSION ['auth']->qybh;
		$bindSELECT ['DJBH'] = $_POST ['DJBH'];      
		$SELECT = $this->_db->fetchRow( $sqlSELECT, $bindSELECT );
		
		$sql = "UPDATE H01DB012410 "
             ." SET CHKDZHT = :CHKDZHT, "      // 2：未完全入库        3：已入库
             ." BGRQ = SYSDATE, "
             ." BGZH = :BGZH "
             ." WHERE QYBH = :QYBH "
             ." AND DJBH = :DJBH " ;
		if( $SELECT['WSHHSHL'] != 0 || $SELECT['THSHL'] != 0 ){
			$bind ['CHKDZHT'] = '2';
		}else{
			$bind ['CHKDZHT'] = '3';
		}
		$bind ['BGZH'] = $_SESSION ['auth']->userId;;
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DJBH'] = $_POST ['DJBH']; 

		$this->_db->query( $sql,$bind );
	}


	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck() {
		if ($_POST ["KPRQ"] == "" ||         //开票日期
            $_POST ["BMBH"] == "" ||         //部门编号
            $_POST ["DJBH"] == "" ||         //单据编号
            $_POST ["YWYBH"] == "" ||        //业务员编号   
            $_POST ["#grid_mingxi"] == "") { //明细表格
			return false;
		}
		
		$isHasMingxi = false; //是否存在至少一条明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] != "") {
				$isHasMingxi = true;
				if ($grid [$this->idx_PIHAO] == "" ||     //批号
					$grid [$this->idx_SHULIANG] == "" ||  //数量
					$grid [$this->idx_SHULIANG] == "0" || //数量
					$grid [$this->idx_CKBH] == "" ||      //仓库编号
					$grid [$this->idx_KQBH] == "" ||      //库区编号 
					$grid [$this->idx_KWBH] == "") {      //库位编号
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
	 * 根据调拨出库单号取得调拨出库单明细信息
	 */
	public function getmingxi($filter) {
		//检索SQL
//		$sql = "SELECT A.XUHAO,"          //序号
//				."A.SHPBH,"      		  //商品编号
//				."B.SHPMCH,"     		  //商品名称
//				."B.GUIGE,"      		  //规格
//				."C.NEIRONG AS BZHDWM,"   //包装单位
//				."A.PIHAO,"      		  //批号
//				."TO_CHAR(A.SHCHRQ,'yyyy-mm-dd') AS SHCHRQ,"   //生产日期
//				."TO_CHAR(A.BZHQZH,'yyyy-mm') AS BZHQZH,"   //保质期至
//				."A.BZHSHL,"     		  //包装数量
//				."A.LSSHL,"      		  //零散数量
//				."A.SHULIANG,"  		  //数量
//				."A.WSHHSHL,"             //未收货数量
//				."B.CHANDI,"     		  //产地
//				."A.BEIZHU,"      	      //备注
//				."A.BZHDWBH,"    		  //包装单位编号
//				."B.TYMCH,"               //通用名
//				."B.JLGG "                //计量规格
//			  ."FROM H01VIEW012411 A "
//			  ."LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "
//			  ."LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' "
//			  ."WHERE A.QYBH = :QYBH "
//			  ."AND A.DJBH = :DJBH "
//			  ."ORDER BY A.XUHAO";

		$sql = "SELECT XUHAO,"          //序号
                ."SHPBH,"               //商品编号
                ."SHPMCH,"              //商品名称
                ."GUIGE,"               //规格
                ."BZHDWMCH,"   //包装单位
                ."PIHAO,"               //批号
                ."TO_CHAR(SHCHRQ,'yyyy-mm-dd') AS SHCHRQ,"   //生产日期
                ."TO_CHAR(BZHQZH,'yyyy-mm') AS BZHQZH,"   //保质期至
                ."BZHSHL,"              //包装数量
                ."LSSHL,"               //零散数量
                ."SHULIANG,"            //数量
                ."WSHHSHL,"             //未收货数量
                ."CHANDI,"              //产地
                ."BEIZHU,"              //备注
                ."BZHDWBH,"             //包装单位编号
                ."TYMCH,"               //通用名
                ."JLGG "                //计量规格
              ."FROM H01VIEW012411 "
              ."WHERE QYBH = :QYBH "
              ."AND DJBH = :DJBH "
              ."ORDER BY XUHAO";

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
		$bind ['DJBH'] = $filter ['bh'];              //单据编号
		return $this->_db->fetchAll($sql, $bind);
		
//		$recs = $this->_db->fetchAll( $sql, $bind );
//		//调用表格xml生成函数
//        return Common_Tool::createXml ( $recs,true);
	}
	
	
	/*
	 * 库位选择画面列表数据取得（xml格式）
	 */
	function getkuweiData($ckbh) {
		//检索SQL
//		$sql = "SELECT C.CKMCH,B.KQMCH,A.KWMCH,DECODE(A.SHFSHKW,'1','散货库位','0','包装库位','库位类型未知') AS SHFSHKWMCH,"
//				."A.CKBH,A.KQBH,A.KWBH,A.SHFSHKW FROM H01DB012403 A "
//				."LEFT JOIN H01DB012401 C ON A.QYBH = C.QYBH AND A.CKBH = C.CKBH "
//				."LEFT JOIN H01DB012402 B ON A.QYBH = B.QYBH AND A.CKBH = B.CKBH AND A.KQBH = B.KQBH "
//				."WHERE A.QYBH = :QYBH "           //区域编号
//				."AND A.CKBH = :CKBH ";            //商品编号

		$sql = "SELECT CKMCH,KQMCH,KWMCH,DECODE(SHFSHKW,'1','散货库位','0','包装库位','库位类型未知') AS SHFSHKWMCH,"
                ."CKBH,KQBH,KWBH,SHFSHKW FROM H01VIEW012403 "
                ."WHERE QYBH = :QYBH "           //区域编号
                ."AND CKBH = :CKBH ";            //商品编号
		//排序
		$sql .=" ORDER BY KWBH,KQBH,CKBH";
		
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['CKBH'] = $ckbh;
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $bind );
		
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"],$bind );	 //总行数
		
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"],$bind );		     //当前页数据
		
		return Common_Tool::createXml ( $recs, false, $totalCount );
	}
	
	
	/**
	 * 取得库间调拨出库单状态
	 * @param 	string 	$bh	编号
	 * 
	 * @return 	array 
	 */
	public function getzht($bh) {
		$sql = "SELECT CHKDZHT FROM H01DB012410 WHERE QYBH = :QYBH AND DJBH = :DJBH";
			
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DJBH'] = $bh;
		
		return $this->_db->fetchRow( $sql, $bind );	
	}
	
	
	/**
	 * 取得仓库/库区/库位状态信息
	 * @param 	string 	$ckbh	仓库编号
	 * @param	string 	$ckbh	库区编号
	 * @param	string	$kwbh	库位编号
	 * 
	 * @return 	array 
	 */
	public function getkuweizht($ckbh,$kqbh,$kwbh) {
		$sql = "SELECT C.KWBH,"
			." C.SHFSHKW,"
			." C.KWZHT,"
			." A.CKZHT,"
			." B.KQZHT,"
			." B.KQLX "
			." FROM H01DB012403 C "
			." LEFT JOIN H01DB012401 A ON C.QYBH = A.QYBH AND C.CKBH = A.CKBH "
			." LEFT JOIN H01DB012402 B ON C.QYBH = B.QYBH AND C.CKBH = B.CKBH AND C.KQBH =B.KQBH"
			." WHERE C.QYBH = :QYBH AND C.CKBH = :CKBH AND C.KQBH = :KQBH AND C.KWBH = :KWBH";
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CKBH'] = $ckbh;
		$bind ['KQBH'] = $kqbh;
		$bind ['KWBH'] = $kwbh;

		return $this->_db->fetchRow( $sql, $bind );	
	}
	
}