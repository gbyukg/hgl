<?php
/******************************************************************
 ***** 模         块：       采购模块(CG)
 ***** 机         能：       采购退货(不合格品)(cgthbhg)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/08/05
 ***** 更新履历：
 ******************************************************************/

class cg_models_cgthbhg extends Common_Model_Base {
	private $_rkdbh = null;           // 入库单编号
	private $idx_ROWNUM=0;            // 行号
	private $idx_SHPBH=1;             // 商品编号
	private $idx_SHPMCH=2;            // 商品名称
	private $idx_GUIGE=3;             // 规格
	private $idx_BZHDWM=4;            // 包装单位
	private $idx_PIHAO=5;             // 批号
	private $idx_SHCHRQ=6;            // 生产日期
	private $idx_BZHQZH=7;            // 保质期至
	private $idx_THSHULIANG=8;        // 退货数量
	private $idx_SHULIANG=9;          // 库存数量
	private $idx_DANJIA=10;           // 单价
	private $idx_HSHJ=11;             // 含税价
	private $idx_KOULV=12;            // 扣率
	private $idx_SHUILV=13;           // 税率
	private $idx_HSHJE=14;            // 含税金额
	private $idx_JINE=15;             // 金额
	private $idx_SHUIE=16;            // 税额
	private $idx_LSHJ=17;             // 零售价	
	private $idx_CHANDI=18;           // 产地
	private $idx_BEIZHU=19;           // 备注
	private $idx_BZHDWBH=20;          // 包装单位编号
	private $idx_TYMCH=21;            // 通用名称
	private $idx_JLGG=22;             // 计量规格


	/*
	 * 取得发货区信息
	 */
	public function getFHQInfo() {
		$sql = "SELECT FHQBH,FHQMCH FROM H01DB012422 WHERE QYBH = :QYBH AND FHQZHT = '1'";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$result = $this->_db->fetchPairs ( $sql, $bind );
		$result [''] = '--选择发货区--';
		ksort ( $result );
		return $result;
	}
	
	
	/**
	 * 得到退货单列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridDanjuData($filter) {
		//排序用字段名
		$fields = array ( "", "A.BHGPRKDBH", "A.RKLX", "A.KPRQ" );
		
		//检索SQL
		$sql = "SELECT A.BHGPRKDBH,DECODE(A.RKLX,'1','采购拒收','2','合格品库移出'),TO_CHAR(A.KPRQ,'YYYY-MM-DD'),"
				."B.BMMCH,C.YGXM AS KPYXM,D.YGXM AS YWYXM,A.BEIZHU,A.CKDBH "
				."FROM H01DB012460 A "
				."LEFT JOIN H01DB012112 B ON A.QYBH = B.QYBH AND A.BMBH = B.BMBH "
				."LEFT JOIN H01DB012113 C ON A.QYBH = C.QYBH AND A.KPYBH = C.YGBH "
				."LEFT JOIN H01DB012113 D ON A.QYBH = D.QYBH AND A.YWYBH = D.YGBH "
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
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CG_CGTH_DJ",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.BHGPRKDBH";
		
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
	 * 得到退货单明细列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridMingxiData($filter) {
		//排序用字段名
		$fields = array ("", "A.SHPBH", "NLSSORT(B.SHPMCH,'NLS_SORT=SCHINESE_PINYIN_M')");
		     
		$sql = "SELECT "          
				."A.SHPBH,"      		  //商品编号
				."B.SHPMCH,"     		  //商品名称
				."B.GUIGE,"      		  //规格
				."C.NEIRONG AS BZHDWM,"   //包装单位
				."A.PIHAO,"      		  //批号
				."TO_CHAR(A.SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,"     //生产日期
				."TO_CHAR(A.BZHQZH,'YYYY-MM-DD') AS BZHQZH,"     //保质期至
				."A.BZHSHL,"     		  //包装数量
				."A.LSSHL,"      		  //零散数量
				."A.SHULIANG,"  		  //数量
				."A.DANJIA,"      	      //单价
				."A.HSHJ,"       		  //含税价
				."A.KOULV,"               //扣率
				."B.SHUILV,"    	 	  //税率
				."A.HSHJE,"     	 	  //含税金额
				."A.JINE,"      	 	  //金额
				."A.SHUIE,"      		  //税额
				."B.CHANDI,"     		  //产地
				."A.BEIZHU,"      	      //备注
				."B.BZHDWBH,"    		  //包装单位编号
				."B.TYMCH,"               //通用名
				."B.JLGG "                //计量规格
			  ."FROM H01DB012461 A "      //不合格品入库单明细信息
			  ."LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "
			  ."LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' "
			  ."WHERE A.QYBH = :QYBH "               //区域编号
			  ."AND A.BHGPRKDBH = :BHGPRKDBH ";      //不合格品入库单编号
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['BHGPRKDBH'] = $filter ["bh"];
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter["orderby"]] . " " . $filter ["direction"];
		
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.BHGPRKDBH,A.XUHAO";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne( $pagedSql["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll( $pagedSql["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true, $totalCount, $filter["posStart"] );
		
	}
	
	
	/*
	 * 根据单位编号编号取得单位信息
	 */
	public function getDanweiInfo($filter) {
		//检索SQL
		$sql =  "SELECT A.DWBH,A.DWMCH,A.DIZHI,A.DHHM,A.KOULV,A.FHQBH," . "DECODE(A.XSHXDQ,NULL,0,XSHXDQ) XSHXDQ" . //销售信贷期 
			    " FROM H01DB012106 A" . " WHERE A.QYBH = :QYBH " . //区域编号
				" AND A.DWBH = :DWBH" . //单位编号
				" AND A.FDBSH ='0'" .   //分店标识
				" AND A.SHFXSH = '1'" . //是否销售
				" AND A.KHZHT = '1'";   //客户状态

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
	
	/**
	 * 退货单信息保存
	 * @param  string  $bh:   单据编号
	 * 
	 * @return bool
	 */
	public function saveMain($bh) {
		$data ['QYBH'] = $_SESSION ['auth']->qybh;        //区域编号
		$data ['CGTHDBH'] = $bh;                          //编号
		$data ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$data ['BMBH'] = $_POST ['BMBH'];                 //部门编号
		$data ['YWYBH'] = $_POST ['YWYBH'];               //业务员编号
		$data ['DWBH'] = $_POST ['DWBH'];                 //单位编号
		$data ['DIZHI'] = $_POST ['DIZHI'];               //地址
		$data ['DHHM'] = $_POST ['DHHM'];                 //电话
		$data ['SHFZZHSH'] = $_POST ['SHFZZHSH']=='1'?'1':'0';     //是否增值税    0:否(未选中) 1:是(选中)
		$data ['KOULV'] = $_POST ['KOULV'];               //扣率
		$data ['FKFSH'] = $_POST ['FKFSH'];               //付款方式
		$data ['THDZHT'] = "0";                           //退货单状态
		$data ['SHFPS'] = $_POST ['SHFPS']=='1'?'1':'0';  //是否配送         0:否(未选中) 1:是(选中)
		$data ['BEIZHU'] = $_POST ['BEIZHU'];             //备注
		$data ['SHHZHT'] = "0";                           //审核状态
		$data ['QXBZH'] = "1";                            //取消标志
		$data ['YRKDBH'] = $_POST ['RKDBH'];              //原入库单号
		$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' );  //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId;      //变更者
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId;     //作成者
		$data ['THLX'] =  "2";                            //退货类型  2：不合格品退货

		return $this->_db->insert ( "H01DB012308", $data );     //插入出库单信息
	}
	
	
	/*
	 * 退货单明细保存
	 * @param  string  $bh:   单据编号
	 * 
	 */
	public function saveMingxi($bh) {
		$idx = 1;           //序号自增
		
        //循环所有明细行，保存出库单明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_THSHULIANG] == '0')continue;
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh;        //区域编号
			$data ['CGTHDBH'] = $bh;                          //编号
			$data ['XUHAO'] = $idx ++;                        //序号
			$data ['SHPBH'] = $grid [$this->idx_SHPBH];       //商品编号
			$data ['PIHAO'] = $grid [$this->idx_PIHAO];       //批号
			$data ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" ); //生产日期
			$data ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM')" );    //保质期至
			$data ['SHULIANG'] = $grid [$this->idx_THSHULIANG]; //退货数量
			$data ['DANJIA'] = $grid [$this->idx_DANJIA];     //单价
			$data ['HSHJ'] = $grid [$this->idx_HSHJ];         //含税价
			$data ['KOULV'] = $grid [$this->idx_KOULV];       //扣率
			$data ['JINE'] = $grid [$this->idx_JINE];         //金额
			$data ['HSHJE'] = $grid [$this->idx_HSHJE];       //含税金额
			$data ['SHUIE'] = $grid [$this->idx_SHUIE];       //税额
			$data ['BEIZHU'] = $grid [$this->idx_BEIZHU];     //备注
			$data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' );  //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId;      //变更者
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId;     //作成者
			
			$this->_db->insert ( "H01DB012309", $data );	  //出库单明细表	
		}
	}


	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck() {
		if ($_POST ["KPRQ"] == "" ||            //开票日期
            $_POST ["RKDBH"] == "" ||           //入库单编号
            $_POST ["#grid_mingxi"] == "") {    //明细表格
			return false;
		}
		return true;
	}
	
	
	/*
	 * 数据合法性逻辑性验证
	 */
	public function logicCheck($data) {
		
		//不合格品库存数量是否被他人修改验证
		foreach ( $data ["#grid_mingxi"] as $grid ) {

			//获取数据库中该商品不合格品库存数量
			$sql = "SELECT SHULIANG FROM H01DB012459 ".
			             " WHERE QYBH = :QYBH ".
			             //" AND CKBH = :CKBH " .
			             " AND SHPBH = :SHPBH " .
			             " AND PIHAO = :PIHAO " .
			             " AND RKDBH = :RKDBH " .
			             " AND BZHDWBH = :BZHDWBH ";
			             
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;            //区域编号
			//$bind ['CKBH'] = $_SESSION ['auth']->ckbh;            //仓库编号
			$bind ['SHPBH'] = $grid [$this->idx_SHPBH];           //商品编号
			$bind ['PIHAO'] = $grid [$this->idx_PIHAO];           //批号
			$bind ['BZHDWBH'] = $grid [$this->idx_BZHDWBH];       //包装单位编号
			$bind ['RKDBH'] = $_POST ['RKDBH'];                   //入库单编号
			 
			$CHECKSL = $this->_db->fetchOne( $sql, $bind );       //获取数量
			
			if ( $CHECKSL != $grid [$this->idx_SHULIANG] ) {
				return false;
			}
		}
		
		return true;
	}
	
	
	/*
	 * 根据入库单单编号取得入库单信息
	 */
	public function getInfo($filter) {

		$sql_RKLX = "SELECT RKLX FROM H01DB012460 "
					."WHERE QYBH = :QYBH "               //区域编号
			  		."AND BHGPRKDBH = :BHGPRKDBH ";      //不合格品入库单编号
			  	
		//绑定查询条件
		$bind_RKLX ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind_RKLX ['BHGPRKDBH'] = $filter ['bh'];
		
		$RKLX = $this->_db->fetchOne( $sql_RKLX, $bind_RKLX );
		
		//检索SQL
		$sql = "SELECT A.BHGPRKDBH AS BH,D.DWBH,B.DWMCH,A.YWYBH,C.YGXM AS YWYMCH,D.DHHM,D.DIZHI,"
				."to_char(D.KOULV,'fm990.00') AS KOULV,A.CKDBH,D.SHFZZHSH,D.FKFSH "
			  	."FROM H01DB012460 A ";
			  
		if ( $RKLX == '1' ){
			$sql .= "LEFT JOIN H01DB012306 D ON A.QYBH = D.QYBH AND A.CKDBH = D.CGDBH ";       //采购拒收
		} else {   //$RKLX == '2'
			$sql .= "LEFT JOIN H01DB012406 D ON A.QYBH = D.QYBH AND A.CKDBH = D.RKDBH ";      //合格品库移出
		}
		
		$sql .= "LEFT JOIN H01DB012106 B ON A.QYBH = B.QYBH AND D.DWBH = B.DWBH "
			  	."LEFT JOIN H01DB012113 C ON A.QYBH = C.QYBH AND A.YWYBH = C.YGBH "
				."WHERE A.QYBH = :QYBH "                  //区域编号
				."AND A.BHGPRKDBH = :BHGPRKDBH ";        //不合格品入库单编号
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['BHGPRKDBH'] = $filter ['bh'];
		
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
	
	/*
	 * 根据入库单编号取得入库单明细信息
	 */
	public function getmingxi($filter) {
		//检索SQL
		$sql = "SELECT A.XUHAO,"          //序号
				."A.SHPBH,"      		  //商品编号
				."B.SHPMCH,"     		  //商品名称
				."B.GUIGE,"      		  //规格
				."C.NEIRONG AS BZHDWM,"   //包装单位
				."A.PIHAO,"      		  //批号
				."TO_CHAR(A.SHCHRQ,'yyyy-mm-dd') AS SHCHRQ,"   //生产日期
				."TO_CHAR(A.BZHQZH,'yyyy-mm') AS BZHQZH,"      //保质期至
				."A.BZHSHL,"     		  //包装数量
				."A.LSSHL,"      		  //零散数量
				."A.SHULIANG,"  		  //数量
				."A.DANJIA,"      	      //单价
				."A.HSHJ,"       		  //含税售价
				."A.KOULV,"               //扣率
				."B.SHUILV,"    	 	  //税率
				."A.HSHJE,"     	 	  //含税金额
				."A.JINE,"      	 	  //金额
				."A.SHUIE,"      		  //税额
				."B.LSHJ,"                //零售价
				."B.CHANDI,"     		  //产地
				."A.BEIZHU,"      	      //备注
				."B.BZHDWBH,"    		  //包装单位编号
				."B.TYMCH,"               //通用名
				."B.JLGG "                //计量规格
			  ."FROM H01DB012407 A "
			  ."LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "
			  ."LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' "
			  ."WHERE A.QYBH = :QYBH "      //区域编号
			  ."AND A.RKDBH = :RKDBH "      //入库单编号
			  ."ORDER BY A.XUHAO";

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
		$bind ['RKDBH'] = $filter ['bh'];             //编号
		
		return $this->_db->fetchAll( $sql, $bind );
	}
	
	
	/**
	 * 得到退货单明细列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getRKDMingxiData($filter) {
				//排序用字段名
		$fields = array ("", "SHPBH", "NLSSORT(SHPMCH,'NLS_SORT=SCHINESE_PINYIN_M')");

		$sql = "SELECT * FROM "
			."(SELECT A.SHPBH,"                                      //商品编号
			."B.SHPMCH,"                                             //商品名称
			."B.GUIGE,"                                              //规格
			."C.NEIRONG,"                                            //包装单位
			."A.PIHAO,"                                              //批号
			."TO_CHAR(A.SHCHRQ,'yyyy-mm-dd') AS SHCHRQ,"             //生产日期
			."TO_CHAR(A.BZHQZH,'yyyy-mm') AS BZHQZH,"                //保质期至
			."A.SHULIANG,"                                           //可退货数量
			."D.DANJIA,"      	                                     //单价
			."D.HSHJ,"       		                                 //含税售价
			."D.KOULV,"                                              //扣率
			."B.SHUILV,"    	 	                                 //税率
			."D.HSHJE,"     	 	                                 //含税金额
			."D.JINE,"      	 	                                 //金额
			."D.SHUIE,"      		                                 //税额
			."B.LSHJ,"                                               //零售价
			."B.CHANDI,"     		                                 //产地
			."B.BEIZHU,"      	                                     //备注
			."A.BZHDWBH,"    		                                 //包装单位编号
			."B.TYMCH,"                                              //通用名
			."B.JLGG,"                                               //计量规格
			."A.QYBH,"
			."A.RKDBH "
			."FROM H01DB012459 A "
			."LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "
			."LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND A.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' "
			."LEFT JOIN H01DB012461 D ON A.QYBH = D.QYBH AND A.RKDBH = D.BHGPRKDBH AND A.SHPBH = D.SHPBH AND A.PIHAO = D.PIHAO ) "
			."WHERE QYBH = :QYBH "      //区域编号
			."AND SHULIANG > 0 "        //可退数量
			."AND RKDBH = :RKDBH ";     //入库单编号

		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['RKDBH'] = $filter ["rkdbh"];
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CG_CGTH_RKDMX",$filter['filterParams'],$bind);
				
		//排序
		$sql .= " ORDER BY " . $fields [$filter["orderby"]] . " " . $filter ["direction"];
		
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",SHPBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne( $pagedSql["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll( $pagedSql["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true, $totalCount, $filter["posStart"] );
		
	}
	
	
	/*
	 * 明细信息商品编号自动完成数据取得
	 */
	public function rkdbhAutocompleteData($filter){	
		//检索SQL
		$sql = "SELECT BHGPRKDBH FROM H01DB012460 WHERE QYBH = :QYBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;

		//快速查找条件
		if ($filter ["searchkey"] != "") {
			$bind ['SEARCHKEY'] = $filter ["searchkey"];
			$sql .= " AND lower(BHGPRKDBH) LIKE '%'||:SEARCHKEY||'%' ";
		}
		
		return $this->_db->fetchAll($sql,$bind);
	}


}
