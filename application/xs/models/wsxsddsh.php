<?php
/**********************************************************
 * 模块：    销售模块(XS)
 * 机能：   网上 销售订单审核(WSXSDDSH)
 * 作成者：刘枞
 * 作成日：2011/10/31
 * 更新履历：
 **********************************************************/
class xs_models_wsxsddsh extends Common_Model_Base{
	private $_xsdbh = null;         // 销售单编号
	private $idx_ROWNUM = 0;        // 行号
	private $idx_SHPBH = 1;         // 商品编号
	private $idx_SHPMCH = 2;        // 商品名称
	private $idx_GUIGE = 3;         // 规格
	private $idx_BZHDWM = 4;        // 包装单位
	private $idx_JLGG = 5;          // 计量规格
	private $idx_SHULIANG = 6;      // 数量
	private $idx_DANJIA = 7;        // 单价
	private $idx_HSHJ = 8;          // 含税售价
	private $idx_KOULV = 9;         // 扣率
	private $idx_SHUILV = 10;       // 税率
	private $idx_HSHJE = 11;        // 含税金额
	private $idx_JINE = 12;         // 金额
	private $idx_SHUIE = 13;        // 税额
	private $idx_LSHJ = 14;         // 零售价
	private $idx_SHPTM = 15;        // 商品条码
	private $idx_FLBM = 16;         // 分类编码
	private $idx_PZHWH = 17;        // 批准文号
	private $idx_JIXINGM = 18;      // 剂型
	private $idx_SHCHCHJ = 19;      // 生产厂家
	private $idx_CHANDI = 20;       // 产地
	private $idx_SHFOTC = 21;       // 是否otc
	private $idx_BEIZHU = 22;       // 备注
	private $idx_BZHDWBH = 23;      // 包装单位编号
	
	private $ddbh;                // 订单编号
	private $xuhao=0;             // 明细序号
	
	
	/**
	 * 销售订单信息列表
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ("", "NLSSORT(DWMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "WSHXSHDH");
		//检索SQL
		$sql = "SELECT DWMCH,WSHXSHDH,TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ,DHHM,DIZHI,BEIZHU,FKFSH,SHFZZHSH,SHFPS,SHFYQTPH,DWBH,QYBH,QXBZH,ZHUANGTAI FROM ".
				"(SELECT B.DWMCH,A.WSHXSHDH,A.KPRQ,A.DHHM,A.DIZHI,A.BEIZHU,".
				"A.FKFSH,A.SHFZZHSH,A.SHFPS,A.SHFYQTPH,A.QYBH,A.QXBZH,A.ZHUANGTAI,A.DWBH ".
				"FROM H01DB012215 A ".
				"LEFT JOIN H01DB012106 B ON A.DWBH = B.DWBH AND A.QYBH = B.QYBH ) ".
				"WHERE QYBH = :QYBH ".           //区域编号
				"AND QXBZH = '1' ";              //取消（删除）标志

		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查询条件
		if ($filter ["ksrq"] != "" || $filter ["zzrq"] != "")
		{
			$sql .= " AND :KSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :ZZRQ ";
			$bind ['KSRQ'] = $filter ["ksrq"] == ""?"1900-01-01":$filter ["ksrq"];
			$bind ['ZZRQ'] = $filter ["zzrq"] == ""?"9999-12-31":$filter ["zzrq"];
		}
		
		if ($filter ["dwbh"] != "") {
			$sql .= " AND DWBH LIKE '%' || :DWBH || '%' ";
			$bind ['DWBH'] = $filter ["dwbh"];
		}
		
		if ($filter ["shsj"] == "1") {
			$sql .= " AND ZHUANGTAI = '3' ";
		}else{
			$sql .= " AND ZHUANGTAI = '1' ";
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("XS_WSXSDDSH_DJ",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",DWBH,WSHXSHDH";

		$recs = $this->_db->fetchAll($sql,$bind);
		
		return Common_Tool::createXml( $recs, true );
	}
	

	/**
	 * 销售订单明细信息列表
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getMingxiGridData($filter){
		//检索SQL
		$sql = "SELECT A.SHPBH,B.SHPMCH,B.GUIGE,C.NEIRONG,B.JLGG,A.SHULIANG,".
		 " A.DANJIA,A.HSHJ,A.KOULV,B.SHUILV,A.HSHJE,A.JINE,A.SHUIE,B.LSHJ,B.SHPTM,".
		 " B.FLBM,B.PZHWH,B.JIXING,B.SHCHCHJ,B.CHANDI,B.SHFOTC,A.BEIZHU ". 
		 " FROM H01DB012216 A ".
	     " LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH ".
		 " LEFT JOIN H01DB012001 C ON B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' ".
		 " WHERE A.QYBH = :QYBH ".
		 " AND A.DWBH = :DWBH ".
		 " AND A.WSHXSHDH = :WSHXSHDH ";
		 
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		$bind ['WSHXSHDH'] = $filter ['ddbh'];

		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $bind );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true, $totalCount, $filter ["posStart"] );
	}
	
	
	/**
	 * 销售订单明细信息列表--编辑页面
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getMingxi($filter){
		//检索SQL
		$sql = "SELECT A.SHPBH,B.SHPMCH,B.GUIGE,C.NEIRONG,B.JLGG,A.SHULIANG,".
			 " A.DANJIA,A.HSHJ,A.KOULV,B.SHUILV,A.HSHJE,A.JINE,A.SHUIE,B.LSHJ,B.SHPTM,".
			 " B.FLBM,B.PZHWH,B.JIXING,B.SHCHCHJ,B.CHANDI,B.SHFOTC,A.BEIZHU,B.BZHDWBH ". 
			 " FROM H01DB012216 A " .
		     " LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH " .
			 " LEFT JOIN H01DB012001 C ON B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' " .
			 " WHERE A.QYBH = :QYBH ".
			 " AND A.DWBH = :DWBH ".
			 " AND A.WSHXSHDH = :WSHXSHDH ";
		 
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		$bind ['WSHXSHDH'] = $filter ['ddbh'];

		//当前页数据
		return $this->_db->fetchAll ( $sql, $bind );
	}
	
	
	
	/**
	 * 销售订单信息获取
	 *
	 * @param string $bh
	 * @return array[]
	 */
	function getinfoData($filter){
		//检索SQL
		$sql = "SELECT A.WSHXSHDH,"          //网上销售单号
				."TO_CHAR(A.KPRQ,'YYYY-MM-DD') AS KPRQ,"        //开票日期
				."A.ZCHZH,"                  //作成者
				."A.DWBH,"                   //单位编号
				."B.DWMCH,"                  //单位名称
				."A.DHHM,"      		     //电话号码
				."A.DIZHI,"                  //地址
				."A.FKFSH,"     		     //付款方式
				."A.SHFZZHSH,"               //是否增值税
				."A.SHFPS,"                  //是否配送
				."A.FHQBH,"                  //发货区
				."A.SHFYQTPH,"               //是否要求同批号
				."A.BEIZHU "     		     //备注
			  ."FROM H01DB012215 A "
			  ."LEFT JOIN H01DB012106 B ON A.DWBH = B.DWBH AND A.QYBH = B.QYBH "
			  ."WHERE A.QYBH = :QYBH "       //区域编号
			  ."AND A.DWBH = :DWBH "         //单位编号
			  ."AND A.WSHXSHDH = :WSHXSHDH " //网上销售单号
			  ."AND A.QXBZH != 'X' ";        //取消标志

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
		$bind ['DWBH'] = $filter ["dwbh"];            //单位编号
		$bind ['WSHXSHDH'] = $filter ["ddbh"];        //单据编号

		return $this->_db->fetchRow( $sql, $bind );
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
	 *更新销售订单取消标志
	 */
	function updataxsddzht($filter){
		$sql = "UPDATE H01DB012215 SET QXBZH = 'X' WHERE QYBH =:QYBH AND DWBH = :DWBH AND WSHXSHDH =:WSHXSHDH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		$bind ['WSHXSHDH'] = $filter ['ddbh'];
		
		return $this->_db->query( $sql,$bind );
	}
	
	
	/*
	 * 网上销售订单删除
	 * $xshddata:销售单数据 
	 */
	public function delXshd($xshddata) {
		$sql_DJ = "DELETE FROM H01DB012215 WHERE QYBH =:QYBH AND DWBH = :DWBH AND WSHXSHDH =:WSHXSHDH";
		$sql_MX = "DELETE FROM H01DB012216 WHERE QYBH =:QYBH AND DWBH = :DWBH AND WSHXSHDH =:WSHXSHDH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $xshddata ['DWBH'];
		$bind ['WSHXSHDH'] = $xshddata ['DJBH'];
		
		$this->_db->query( $sql_DJ,$bind );
		$this->_db->query( $sql_MX,$bind );
	}
	
	
	/*
	 * 网上销售订单保存
	 * $xshddata:销售单数据 
	 */
	public function createXshd($xshddata) {
		$xshd ['QYBH'] = $_SESSION ['auth']->qybh;       //区域编号
		$xshd ['DWBH'] = $_POST ['DWBH'];                //单位编号
		$xshd ['WSHXSHDH'] = $_POST['DJBH'];             //网上销售单编号
		$xshd ['ZHUANGTAI'] = '3';                       //状态
		$xshd ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $xshddata ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$xshd ['DIZHI'] = $xshddata ['DIZHI'];           //地址
		$xshd ['DHHM'] = $xshddata ['DHHM'];             //电话
		$xshd ['FKFSH'] = $xshddata ['FKFSH'];           //付款方式
		$xshd ['SHFZZHSH'] = isset($xshddata ['SHFZZHSH'])? $xshddata ['SHFZZHSH'] : '0'; //是否增值税
		$xshd ['SHFPS'] = isset($xshddata ['SHFPS'])? $xshddata ['SHFPS'] : '0';          //是否配送
		$xshd ['SHFYQTPH'] = isset($xshddata ['SHFTPH'])? $xshddata ['SHFTPH'] : '0';     //是否要求同批号
		$xshd ['FHQBH'] = $xshddata ['FAHUOQU'];          //发货区编号
		$xshd ['BEIZHU'] = $xshddata ['BEIZHU'];          //备注
		$xshd ['QXBZH'] = "1";                            //取消标志   1:正常      2:删除 
		$xshd ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$xshd ['ZCHZH'] = $_SESSION ['auth']->userId;     //作成者
		$xshd ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' );  //变更日期
		$xshd ['BGZH'] = $_SESSION ['auth']->userId;      //变更者
		//网上销售订单信息表
		$this->_db->insert ( "H01DB012215", $xshd );
		
		$idx = 1;         //明细序号自增
        //循环所有明细行，保存销售订单明细
		foreach ( $xshddata ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == "")continue;         //忽略空白行
			$xshdmx ['QYBH'] = $_SESSION ['auth']->qybh;         //区域编号
			$xshdmx ['DWBH'] = $_POST ['DWBH'];                  //单位编号
			$xshdmx ['WSHXSHDH'] = $_POST['DJBH'];               //网上销售单编号
			$xshdmx ['XUHAO'] = $idx ++;                         //序号
			$xshdmx ['SHPBH'] = $grid [$this->idx_SHPBH];        //商品编号
			$xshdmx ['SHULIANG'] = $grid [$this->idx_SHULIANG];  //数量
			$xshdmx ['DANJIA'] = $grid [$this->idx_DANJIA];      //单价
			$xshdmx ['HSHJ'] = $grid [$this->idx_HSHJ];          //含税价
			$xshdmx ['KOULV'] = $grid [$this->idx_KOULV];        //扣率
			$xshdmx ['JINE'] = $grid [$this->idx_JINE];          //金额
			$xshdmx ['HSHJE'] = $grid [$this->idx_HSHJE];        //含税金额
			$xshdmx ['SHUIE'] = $grid [$this->idx_SHUIE];        //税额
			$xshdmx ['BEIZHU'] = $grid [$this->idx_BEIZHU];      //备注
			$xshdmx ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' );   //变更日期
			$xshdmx ['BGZH'] = $_SESSION ['auth']->userId;       //变更者
			$xshdmx ['ZCHRQ'] = new Zend_Db_Expr ( 'SYSDATE' );  //作成日期
			$xshdmx ['ZCHZH'] = $_SESSION ['auth']->userId;      //作成者
			//网上销售订单明细表
			$this->_db->insert ( "H01DB012216", $xshdmx );
		}
	}
	
	
	/*
	 * 循环处理网上销售订单确认状态
	 */
	public function queren($data){
			
		//循环所有网上销售订单
		foreach ( $data ["#grid_main"] as $main_grid ) {
		//**********#grid_main列HEADER************
		//			11:	单位编号
		//			10:	是否要求同批号
		//			9:	是否配送
		//			8:	是否增值税
		//			7:	付款方式
		//			6:	备注
		//			5:	地址
		//			4:	电话号码
		//			3:	开票日期
		//			2:	网上销售单编号
		//			1:	单位
		//			0:	行号
		//****************************************
			try {
				
				if ($main_grid[2] == "")continue;         //忽略空白行
				
				//根据当前行网上销售单编号，查询该单的单据信息及明细信息
				$sql_DJ = "select QYBH,DWBH,WSHXSHDH,XSHDBH,ZHUANGTAI,KPRQ,".
							"DHHM,DIZHI,FKFSH,SHFZZHSH,SHFPS,FHQBH,SHFYQTPH,".
							"BEIZHU,QXBZH,ZCHZH,ZCHRQ,BGRQ,BGZH ".
							"FROM H01DB012215 ".
							"WHERE QYBH = :QYBH AND ZHUANGTAI = '1' ".
							"AND WSHXSHDH = :WSHXSHDH ".
							"AND DWBH = :DWBH ";
							
				$sql_MX = "select A.QYBH,A.DWBH,A.WSHXSHDH,A.XUHAO,A.SHPBH,B.SHPMCH,B.JLGG,A.SHULIANG,A.DANJIA,".
							"A.HSHJ,A.KOULV,A.JINE,A.HSHJE,A.SHUIE,A.BEIZHU,A.ZCHZH,A.ZCHRQ,A.BGRQ,A.BGZH ".
							"FROM H01DB012216 A ".
							"LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH ".
							"WHERE A.QYBH = :QYBH ".
							"AND A.WSHXSHDH = :WSHXSHDH ".
							"AND A.DWBH = :DWBH ";
				
				//绑定查询条件
				$bind ['QYBH'] = $_SESSION ['auth']->qybh;
				$bind ['DWBH'] = $main_grid [11];
				$bind ['WSHXSHDH'] = $main_grid [2];
				
				$data_DJ = $this->_db->fetchRow( $sql_DJ,$bind );
				$data_MX = $this->_db->fetchAll( $sql_MX,$bind );
				
				//检验单据信息是否合法，如果不合法跳出循环
				if( $data_DJ == false )break;
				
				$this->beginTransaction();               //事务开始
				
			 	//订单资格审查（证照有效期，信用额度，出货限制数量, 在库商品数量等）
	            $zige = $this->checkQualification($data_DJ,$data_MX);
	            
	            //订单资格审查未通过，需要审批
	            if($zige['status']=="1"){
	            	$result['status'] = '3';             //资格有问题，进入等待审批状态
	            	$result['data'] = $zige["data"];     //需要审批的内容列表
	            	$this->commit();                     //事务提交
	            	continue;
	            }
				
				//生成新的销售单编号
				$xshdbh = Common_Tool::getDanhao('XSD');
				
				//生成销售订单(销售单，销售单明细)
			    $this->insertxshd($xshdbh,$data_DJ,$data_MX);
			    
			    //修改网上销售订单状态为2和加入正式销售单编号
			    $this->updatewsxsdd($xshdbh,$data_DJ);
				
				//生成出库单
				$this->doChuku($xshdbh,$data_DJ,$data_MX);
			    
				//生成结算单
			    $this->createJsd($xshdbh,$data_MX);
			    
				//在库商品出库操作，更新在库商品数量，可能会生成补货单及移动履历
				//商品出库处理(出库单，补货单)
			    $chuku = $this->doChuku($xshdbh,$data_DJ,$data_MX);
			    
				//库存有问题
			    if($chuku['status']!='0'){
			       $result['status'] = '4';               //库存不足
			       $result['data'] = $chuku['data'];      //库存数据
			       $this->rollBack();                     //事务回滚
	               continue;
			    }

			    Common_Logger::logToDb("新建销售订单：".$xshdbh);
			    $this->commit();
		    
			
			} catch ( Exception $e ) {
				//回滚
				$this->rollBack ();
	     		throw $e;
	     		continue;
			}
		}
		
	}
	
	
	/*
	 * 生成销售订单(销售单，销售单明细)
	 * 
	 */
	public function insertxshd($xshdbh,$data_DJ,$data_MX) {
		$result["status"] = "0";
		
		$SUM_JINE = 0;                 //金额合计
		$SUM_SHUIE = 0;                //税额合计
		$SUM_HSHJE = 0;                //含税金额合计
		$SUM_SHULIANG = 0;             //数量合计
		foreach( $data_MX as $grid_mingxi ){
			$SUM_JINE = $SUM_JINE + $grid_mingxi["JINE"];
			$SUM_SHUIE = $SUM_SHUIE + $grid_mingxi["SHUIE"];
			$SUM_HSHJE = $SUM_HSHJE + $grid_mingxi["HSHJE"];
			$SUM_SHULIANG = $SUM_SHULIANG + $grid_mingxi["SHULIANG"];
		}
		
		$xshd ['QYBH'] = $_SESSION ['auth']->qybh;              //区域编号
		$xshd ['XSHDBH'] = $xshdbh;                             //销售单编号
		$xshd ['KPRQ'] = new Zend_Db_Expr ( 'sysdate' );        //开票日期
		$xshd ['BMBH'] = $_SESSION ['auth']->bmbh;              //部门编号
		$xshd ['KPYBH'] = $_SESSION ['auth']->userId;           //开票员编号
		$xshd ['YWYBH'] = $data_DJ ['ZCHZH'];                   //业务员编号
		$xshd ['DWBH'] = $data_DJ ['DWBH'];                     //单位编号
		$xshd ['DIZHI'] = $data_DJ ['DIZHI'];                   //地址
		$xshd ['DHHM'] = $data_DJ ['DHHM'];                     //电话
		$xshd ['SHFZZHSH'] = $data_DJ ['SHFZZHSH'];             //是否增值税
		$xshd ['KOULV'] = $data_DJ ['KOULV'];                   //扣率
		$xshd ['XSHDZHT'] = '0';                                //销售单状态(未出库)
		$xshd ['FHQBH'] = $data_DJ ['FAHUOQU'];                 //发货区
		$xshd ['BEIZHU'] = $data_DJ ['BEIZHU'];                 //备注
		$xshd['SHHZHT'] = "0";                                  //审核状态
        //$xshd['SHHR'] = '';                                   //审核人
		//$xshd['SHHYJ'] = '';                                  //审核意见
		//$xshd['SHHRQ'] = new Zend_Db_Expr("SYSDATE");         //审核日期
		$xshd ['FKFSH'] = $data_DJ ['FKFSH'];                   //付款方式
		$xshd ['SHFPS'] = $data_DJ ['SHFPS'];                   //是否配送
		$xshd ['FPZHT'] = '0';                                  //发票状态  未开
		$xshd ['JINE'] = str_replace(",","",$SUM_JINE);         //金额
		$xshd ['SHUIE'] = str_replace(",","",$SUM_SHUIE);       //税额
		$xshd ['HSHJE'] = str_replace(",","",$SUM_HSHJE);       //含税金额
		$xshd ['SHULIANG'] = str_replace(",","",$SUM_SHULIANG); //数量
		$xshd ['PSYXJ'] = '0';                                  //配送优先级
		$xshd ['QXBZH'] = '1';                                  //取消标志
		$xshd ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' );       //作成日期
		$xshd ['ZCHZH'] = $_SESSION ['auth']->userId;           //作成者
		$xshd ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' );        //变更日期
		$xshd ['BGZH'] = $_SESSION ['auth']->userId;            //变更者
		//销售订单信息表
		$this->_db->insert ( "H01DB012201", $xshd );
		
		$idx = 1; //明细序号自增
        //循环所有明细行，保存销售订单明细
		foreach ( $data_MX as $grid ) {
			if ($grid [$this->idx_SHPBH] == "")continue;        //忽略空白行

			$xshdmx ['QYBH'] = $_SESSION ['auth']->qybh;        //区域编号
			$xshdmx ['XSHDBH'] = $xshdbh;                       //销售单编号
			$xshdmx ['XUHAO'] = $idx ++;                        //序号
			$xshdmx ['SHPBH'] = $grid [$this->idx_SHPBH];       //商品编号
//			$xshdmx ['PIHAO'] = $grid [$this->idx_PIHAO];       //批号
//			$xshdmx ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" ); //生产日期
//			$xshdmx ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM')" );    //保质期至
//			$xshdmx ['BZHSHL'] = $grid [$this->idx_BZHSHL];     //包装数量
//			$xshdmx ['LSSHL'] =  $grid [$this->idx_LSSHL];      //零散数量
//			$xshdmx ['SHULIANG'] = $grid [$this->idx_SHULIANG]; //数量
			$xshdmx ['DANJIA'] = $grid [$this->idx_DANJIA];     //单价
			$xshdmx ['HSHJ'] = $grid [$this->idx_HSHJ];         //含税价
			$xshdmx ['KOULV'] = $grid [$this->idx_KOULV];       //扣率
			$xshdmx ['JINE'] = $grid [$this->idx_JINE];         //金额
			$xshdmx ['HSHJE'] = $grid [$this->idx_HSHJE];       //含税金额
			$xshdmx ['SHUIE'] = $grid [$this->idx_SHUIE];       //税额
			$xshdmx ['BEIZHU'] = $grid [$this->idx_BEIZHU];     //备注
			$xshdmx ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' );  //变更日期
			$xshdmx ['BGZH'] = $_SESSION ['auth']->userId;      //变更者
			$xshdmx ['ZCHRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //作成日期
			$xshdmx ['ZCHZH'] = $_SESSION ['auth']->userId;     //作成者
			//销售订单明细表
			$this->_db->insert ( "H01DB012202", $xshdmx );
		}
		
		return $result;
	}
	
	
	/*
	 * 生成结算单
	 */
	public function createJsd($xshdbh,$data_mingxi){
		$SUM_JINE = 0;                 //金额合计
		$SUM_HSHJE = 0;                //含税金额合计
		foreach( $data_mingxi as $grid_mingxi ){
			$SUM_JINE = $SUM_JINE + $grid_mingxi["JINE"];
			$SUM_HSHJE = $SUM_HSHJE + $grid_mingxi["HSHJE"];
		}
		$jsd["QYBH"] = $_SESSION ['auth']->qybh;
		$jsd["XSHDBH"] = $xshdbh;                                    //销售单编号
		$jsd["JINE"] = str_replace(",","",$SUM_JINE);                //金额
		$jsd["HSHJE"] = str_replace(",","",$SUM_HSHJE);              //含税金额
		$jsd["YSHJE"] = $SUM_HSHJE;                                  //应收金额
		$jsd["SHQJE"] = "0";                                         //收取金额
		$jsd["JSRQ"] = new Zend_Db_Expr ("TO_DATE('1900-01-01','YYYY-MM-DD')");     //结算日期
		$jsd["JIESUANREN"] = "";                                     //结算人
		$jsd["JSZHT"] = "0";                                         //结算状态 未结
		//结算单
		$this->_db->insert("H01DB012208",$jsd);
	}
	
	
	/*
	 * 客户资质验证（证照，资信，数量）
	 * $data:画面提交数据
	 * $xshdbh:销售单编号
	 */
	public function checkQualification($DJ,$MX){
		$zige["status"] = "0";//验证返回值
		$xuhao = 0;
		
		//客户证照资质验证
		$zhzhCheck = $this->checkZhZh( $DJ['DWBH'] );
		if ($zhzhCheck["status"]!="0"){
			//许可证过期
			if($zhzhCheck["data"]["XKZHYXQOK"]=="0"){
				$zige["status"] = "1"; //需要审批
			    $zige["data"][$xuhao++]= "许可证已过期。有效期：" .$zhzhCheck["data"]["XKZHYXQ"];
			}
		    //营业执照过期
		    if($zhzhCheck["data"]["YYZHZHYXQOK"]=="0"){
			    $zige["status"] = "1"; //需要审批
			    $zige["data"][$xuhao++]= "营业执照已过期。有效期：" .$zhzhCheck["YYZHZHYXQ"];
		    }
		}
		
	    //客户帐期（信贷期）验证
		if($DJ["FKFSH"]!="0"){
			$xdqCheck =$this->checkXdq($DJ);
		    if($xdqCheck["status"]!="0"){
			    $zige["status"] = "1"; //需要审批
			    $zige["data"][$xuhao++]="销售信贷期已超期。最长信贷期:".$xdqCheck["xdq"]."，超期天数：".$xdqCheck["xdqover"];
		    }
		}
		
		//客户信贷额验证(包括本次订单销售额)
		if($DJ["FKFSH"]=="1"){
			$xdeCheck =$this->checkXde($DJ,$MX);
		    if($xdeCheck["status"]!="0"){
			    $zige["status"] = "1"; //需要审批
			    $zige["data"][$xuhao++]="销售信贷额已超过限额。最大信贷额:".$xdeCheck["xde"]."，累计已用信贷额：".$xdeCheck["xde_used"];
		    }
		}
		
		//检验商品出货量是否有超出该商品出库限制数量
		$ChKXZSLCheck = $this->checkChKXZSL($MX);
		if($ChKXZSLCheck["status"]!="0"){
			$zige["status"] = "1"; //需要审批
			foreach ($ChKXZSLCheck["data"] as $errdata){
				$zige["data"][$xuhao++] = "超过出库限制数量。商品:".$errdata["SHPBH"].$errdata["SHPMCH"].",出库限制数量:".$errdata["CHKXZHSHL"].",开单数量：".$errdata["SHULIANG"];
			}
		}
		
		//检验商品出货量是否超出该商品在库数量（包括是否需求同批号）
		$ZKSLCheck = $this->checkZKSL($DJ,$MX);
		if($ZKSLCheck["status"]!="0"){
			$zige["status"] = "1"; //需要审批
			foreach ($ZKSLCheck["data"] as $errdata){
				$zige["data"][$xuhao++] = "商品:".$errdata["SHPBH"].$errdata["SHPMCH"].",库存数量或同批号库存数量不足！";
			}
		}
		
		
		//有需要审批的项目
		if($zige["status"]== "1"){
			foreach ($zige["data"] as $xuhao=>$value){
				$shp["QYBH"] = $_SESSION ['auth']->qybh;         //区域编号
			    $shp["WSHXSHDH"] = $DJ["WSHXSHDH"];              //网上销售订单编号
			    $shp["XUHAO"] = $xuhao;                          //序号
			    $shp["SHPYY"] = $value;                          //审批原因
			    $shp["SHPR"] = $_SESSION ['auth']->userId;       //审批人
			    $shp["SHPRQ"] = new Zend_Db_Expr ( 'SYSDATE' );  //审批日期

			    $this->_db->insert("H01DB012217",$shp);
			}

			//修改网上销售订单中的状态
			$sql = "UPDATE H01DB012215 ".
					"SET ZHUANGTAI = '3' ".
					"WHERE QYBH = :QYBH ".
					"AND WSHXSHDH = :WSHXSHDH ".
					"AND DWBH = :DWBH";
			
			//绑定查询条件
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;        //区域编号
			$bind ['WSHXSHDH'] = $DJ["WSHXSHDH"];             //网上销售订单编号
			$bind ['DWBH'] = $DJ["DWBH"];                     //单位编号
			
			$this->_db->query( $sql, $bind );
		}
				
		return $zige;
    }
    
    
	/*
	 * 判断证照是否过期
	 */
	public function checkZhZh($dwbh){
		$zige["status"] = "0";
		
		//许可证有效期，营业执照有效期
		$sql = " SELECT TO_CHAR(XKZHYXQ,'YYYY-MM-DD'),(CASE WHEN XKZHYXQ < SYSDATE THEN 0 WHEN XKZHYXQ IS NULL THEN 0  ELSE 1 END) AS XKZHYXQOK,".
               " TO_CHAR(YYZHZHYXQ,'YYYY-MM-DD'),(CASE WHEN YYZHZHYXQ < SYSDATE THEN 0 WHEN YYZHZHYXQ IS NULL THEN 0  ELSE 1 END) AS YYZHZHYXQOK".
               " FROM H01DB012106  WHERE QYBH =:QYBH AND DWBH = :DWBH";
		
		//绑定查询变量
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $dwbh;
		
		$rec = $this->_db->fetchRow($sql,$bind);
		
		//许可证过期 营业执照过期
		if($rec["XKZHYXQOK"]=="0" || $rec["YYZHZHYXQOK"]=="0"){
			$zige["status"] = "1"; //需要审批
			$zige["data"]=$rec;
		}
		
		return $zige;
	}
	
	
	/*
	 * 判断信贷期是否超期
	 */
	public function checkXdq($DJ) {
		$xdqCheck["status"] = "0";
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $DJ["DWBH"];
		
		//销售信贷期取得
		$sql = "SELECT DECODE(XSHXDQ,NULL,0,XSHXDQ) FROM H01DB012106 WHERE QYBH = :QYBH AND DWBH = :DWBH";
		$xshxdq = $this->_db->fetchOne ( $sql, $bind );

		//账期销售单中尚未结账销售单的最长天数
		$sql = " SELECT DECODE(FLOOR(SYSDATE - min(A.KPRQ)),NULL,0,FLOOR(SYSDATE - min(A.KPRQ)))FROM H01DB012201 A ".
		       " JOIN H01DB012208 B ON A.QYBH = B.QYBH AND A.XSHDBH = B.XSHDBH " .
		       " WHERE A.QYBH = :QYBH AND A.DWBH = :DWBH" . 
		       " AND A.QXBZH ='1' AND A.FKFSH = '1' AND B.JSZHT <> '1' ";
   	    $days = $this->_db->fetchOne ( $sql, $bind );
   				
		//帐期已经超期
		if ($days > $xshxdq) {
			$xdqCheck["status"] = "1";
			$xdqCheck["xdq"] = $days; //信贷期天数
			$xdqCheck["xdqover"] = $days - $xshxdq; //超期天数
		}
		
		return $xdqCheck;
	}
	
	
    /*
	 * 判断信贷额是否超过额度
	 */
	public function checkXde($DJ,$MX) {
		$xdeCheck["status"] = "0";
			
		//销售信贷额取得
		$sql = "SELECT DECODE(XSHXDE,NULL,0,XSHXDE) FROM H01DB012106 WHERE QYBH = :QYBH AND DWBH = :DWBH";
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $DJ["DWBH"];
		
		$xde = $this->_db->fetchOne ( $sql, $bind );

		//账期销售单中尚未结账的合计金额
		$sql = " SELECT DECODE(SUM(B.YSHJE),NULL,0,SUM(B.YSHJE)) FROM H01DB012201 A ".
               " JOIN H01DB012208 B ON A.QYBH = B.QYBH AND A.XSHDBH = B.XSHDBH ".
		       " WHERE A.QYBH = :QYBH AND A.DWBH = :DWBH" . 
		       " AND A.QXBZH ='1' AND A.FKFSH = '1' AND B.JSZHT <> '1' ";
		
		$yshje = $this->_db->fetchOne ( $sql, $bind );   //销售应收金额之和
		
		
		//本次付款为帐期付款，则加上本次金额
		if($DJ["FKFSH"]=="1"){
			$bcje = 0;         //本次金额
			foreach ( $MX as $data_mingxi ) {			//循环所有网上销售订单明细
				$bcje = $bcje + $data_mingxi["HSHJE"];
			}
			$yshje = $yshje + $bcje;
		}
   	    
    	   
   	    //判断合计金额是否超过信贷额
   	    if($yshje > (float)$xde){
   	    	$xdeCheck["status"] = "1";      //超过信贷额
   	    	$xdeCheck["xde"] = (float)$xde; //最大信贷额
   	    	$xdeCheck["xde_used"] = $yshje; //已用信贷额
   	    }
   	    
		return $xdeCheck;
	}
	
	
	/*
	 * 检验商品出货量是否有超出该商品出库限制数量
	 */
	public function checkChKXZSL($MX){
		$result["status"] = "0";
		$xuhao = 0;
		foreach ( $MX as $data_mingxi ) {			//循环所有网上销售订单明细
		
			//取出本单所有超过出库限制数量的商品
			$sql = "SELECT SHPBH,SHPMCH,CHKXZHSHL FROM H01DB012101 ".
	                "WHERE QYBH = :QYBH ".
	                "AND SHPBH = :SHPBH ";
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] = $data_mingxi["SHPBH"];
			
			$recs = $this->_db->fetchRow($sql,$bind);
			
			if( $recs["CHKXZHSHL"] < $data_mingxi["SHULIANG"] ){
				$result["status"] = "1";
				$result["data"][$xuhao] = $recs;
				$result["data"][$xuhao++]["SHULIANG"] = $data_mingxi["SHULIANG"];
			}
			
		}
		return $result;
	}
	
	
	/*
	 * 检验商品出货量是否超出该商品在库数量（包括是否需求同批号）
	 */
	public function checkZKSL($DJ,$MX){
		$result["status"] = "0";
		$xuhao = 0;
		foreach ( $MX as $data_mingxi ) {			//循环所有网上销售订单明细
			
			//验证出库数量是否大于库存数量
			//取得该在库商品每种批号的数量和总数量
			$sql_kcsl = "SELECT A.SHPBH,B.SHPMCH,A.PIHAO,SUM(A.SHULIANG) AS PCSL FROM H01DB012404 A ".
						"LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH ".
		                "WHERE A.QYBH = :QYBH AND A.SHPBH = :SHPBH AND A.ZKZHT != '2' ".
		                "GROUP BY A.SHPBH,B.SHPMCH,A.PIHAO";
			
			$bind_kcsl ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind_kcsl ['SHPBH'] = $data_mingxi["SHPBH"];
			
			$recs_kcsl = $this->_db->fetchAll($sql_kcsl,$bind_kcsl);
			
			$SL = 0;     //商品总数量
			foreach ( $recs_kcsl as $data_kcsl ) {
				$temp = '0';                       //临时变量：记录是否有同批号的商品数量大于销售数量  0：无   1：有
				$SL = $SL + $data_kcsl['PCSL'];    //累计商品总库存数量
				if( $data_kcsl['PCSL'] > $data_mingxi['SHULIANG'] ){
					$temp = '1';
				}
			}
			
			if( $DJ['SHFYQTPH'] == '1' ){      //要求同批号
				if( $temp == '0' ){
					$result["status"] = "1";
					$result["data"][$xuhao]["SHPBH"] = $data_mingxi["SHPBH"];            //商品编号
					$result["data"][$xuhao]["SHPMCH"] = $data_mingxi["SHPMCH"];          //商品名称
					$result["data"][$xuhao]["SHULIANG"] = $data_mingxi["SHULIANG"];      //商品预销售数量
					$result["data"][$xuhao++]["KCSL"] = $SL;                             //商品库存数量
				}
			}else{                            //不要求同批号
				if( $SL < $data_mingxi['SHULIANG'] ){
					$result["status"] = "1";
					$result["data"][$xuhao]["SHPBH"] = $data_mingxi["SHPBH"];            //商品编号
					$result["data"][$xuhao]["SHPMCH"] = $data_mingxi["SHPMCH"];          //商品名称
					$result["data"][$xuhao]["SHULIANG"] = $data_mingxi["SHULIANG"];      //商品预销售数量
					$result["data"][$xuhao++]["KCSL"] = $SL;                             //商品库存数量
				}
			}
		}
		
		return $result;
	}
	
	
	/*
	 * 修改网上销售订单状态为2和加入正式销售单编号
	 */
	public function updatewsxsdd($xshdbh,$DJ){
		
		//修改网上销售订单中的状态
		$sql = "UPDATE H01DB012215 ".
				"SET ZHUANGTAI = '2',XSHDBH = :XSHDBH ".
				"WHERE QYBH = :QYBH ".
				"AND WSHXSHDH = :WSHXSHDH ".
				"AND DWBH = :DWBH";
		
		//绑定查询条件
		$bind ['XSHDBH'] = $xshdbh;                       //销售单编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;        //区域编号
		$bind ['WSHXSHDH'] = $DJ["WSHXSHDH"];             //网上销售订单编号
		$bind ['DWBH'] = $DJ["DWBH"];                     //单位编号
		
		$this->_db->query( $sql, $bind );
		
	}
	
    
	/*
	 * 生成出库单信息
	 */
	public function createCkd($xshdbh){
		$this->chkdbh = Common_Tool::getDanhao('CKD');       //出库单编号
		
		$ckd["QYBH"] = $_SESSION ['auth']->qybh;
		$ckd["CHKDBH"] = $this->chkdbh;
		$ckd["CKDBH"] = $xshdbh;                            //参考单编号
		$ckd["CHKLX"] = '1';                                //销售出库 
		$ckd["CHKDZHT"] = '1';                              //出库单状态（未出库确认）
        $ckd ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' );    //作成日期
		$ckd ['ZCHZH'] = $_SESSION ['auth']->userId;        //作成者
		$ckd['BGRQ'] = new Zend_Db_Expr('SYSDATE');         //变更日期
		$ckd['BGZH'] = $_SESSION ['auth']->userId;          //变更者	
		
		$this->_db->insert ( 'H01DB012408', $ckd );
	}
	
	
	/*
	 * 发货暂存区分配
	 */
	function assignFhzcq($xshdbh,$dwbh,$fhqbh){
		$sql = "SELECT A.FHZCQBH,A.FHZCQMCH,B.FHQBH,B.CHHKBH FROM H01DB012446 A 
                LEFT JOIN H01DB012445 B ON A.QYBH = B.QYBH AND A.CKBH = B.CKBH AND A.CHHKBH = B.CHHKBH 
                LEFT JOIN H01DB012422 C ON A.QYBH = C.QYBH AND B.FHQBH = C.FHQBH
                LEFT JOIN H01DB012106 D ON A.QYBH = D.QYBH AND D.KHJL = A.FHZCQLB 
                WHERE A.QYBH = :QYBH AND C.FHQBH = :FHQBH AND D.DWBH = :DWBH";
		
		//绑定查询变量
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['FHQBH'] = $fhqbh;
		$bind ['DWBH'] = $dwbh;
		//执行查询
		$rec = $this->_db->fetchRow($sql, $bind);
		
		$zancun ["QYBH"] = $_SESSION ['auth']->qybh;                 //区域编号
		$zancun ["XSHDBH"] = $xshdbh;                                //销售单编号
		$zancun ["FHQBH"] = $fhqbh;                                  //发货区编号
		$zancun ["FHZCQBH"] = $rec==FALSE? "00000":$rec['FHZCQBH'];  //发货暂存区编号
		$zancun ["ZHUANGTAI"] = "1";                                 //状态
		
		$this->_db->insert ( 'H01DB012214', $zancun );
	}
	
	
	/*
	 * 在库商品出库处理
	 */
	public function doChuku( $xshdbh, $data_DJ, $data_MX ) {
		$result ['status'] = '0';
		
		//出库单信息生成
		$this->createCkd($xshdbh);
		
		//出货口发货暂存区分配
		$this->assignFhzcq($xshdbh,$data_DJ["DWBH"],$data_DJ["FAHUOQU"]);
		
		//循环所有明细行进行实际在库库存数量检验
		foreach ( $data_MX as $row ) {
			if ($row [$this->idx_SHPBH] == '')continue;
			
			$shpbh = $row[$this->idx_SHPBH];       //商品编号
//			$pihao = $row[$this->idx_PIHAO];       //批号
//			$shchrq = $row[$this->idx_SHCHRQ];     //生产日期
			$recs_bzh = $this->getKucun(0,$shpbh); //整件库存明细
			$recs_ls = $this->getKucun(1,$shpbh);  //零散库存明细
			
		    $bzhshl = 0; //累计在库包装数量
		    $lsshl = 0;  //累计在库零散数量
		    
			//计算库存数量
		    foreach ( $recs_bzh as $rec ) {
				$bzhshl += ( int ) $rec ['SHULIANG'];    //累计在库包装数量
			}
		    foreach ( $recs_ls as $rec ) {
				$lsshl += ( int ) $rec ['SHULIANG'];     //累计在库零散数量
			}
			
			//检校最新库存数量是否满足本次销售
			if(($bzhshl + $lsshl) < (int)$row["SHULIANG"]){
				$result ['status'] = '1';                            //库存不足
				$result ['data']['rIdx'] = (int)$row["XUHAO"];       //定位明细行index
			}
			
			//总库存可以满足本次销售（包含直接可以出库或者通过补货处理可以满足出库两种情况）
			if($result ['status']=="0"){
				
				$shuliang_ls = $row["SHULIANG"] % $row["JLGG"];                //零散出库数量
				$shuliang_bzh = $row["SHULIANG"] - $shuliang_ls;               //整件出库数量
				
			    $this->updateKucun("2", $shuliang_bzh, $recs_bzh, $xshdbh);    //整件包装库位出库处理
			    
			    //如果零散数量不足，则先从包装库位向零散库位补货
			    if( $lsshl < $shuliang_ls ){
			    	//补货处理
			    	$bhshl = 1 * (int)$row["JLGG"];                            //补货数量 1件
			    	$bhdbh = Common_Tool::getDanhao("BHD");                    //补货单编号
                    $recs_bzh = $this->getKucun(0,$shpbh);      //最新包装库存
                    //补货库存更新处理
                    $this->updateKucun("6",$bhshl,$recs_bzh, $xshdbh,$bhdbh);  //补货
			    	//补货完毕之后重新取得零散库存数据
			    	$recs_ls = $this->getKucun(1,$shpbh);       //零散库存明细
			    }
			    
			    //零散库位出库处理
			    $this->updateKucun("2",$shuliang_ls, $recs_ls, $xshdbh );      //出库
			}
		}
					
		return $result;
	}
	
	
	/*
	 * 取得最新库存明细数据
	 */
	function getKucun($flg,$shpbh){
			if($flg==0){
				//取得在库包装库存数据
				$sql = "SELECT QYBH,CKBH,KQBH,KWBH,SHPBH,PIHAO,RKDBH,ZKZHT,BZHDWBH,TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,TO_CHAR(BZHQZH,'YYYY-MM') AS BZHQZH,SHULIANG".
				       " FROM H01UV012005 ".
				       " WHERE QYBH = :QYBH ".
				       " AND SHPBH = :SHPBH ".
				       " AND SHULIANG > 0 ".
				       " AND SHFSHKW = '0'".               //包装
				       " ORDER BY ZKZHT DESC,RKDBH ASC,SHULIANG ".       //在库状态 >入库单>数量
				       " FOR UPDATE OF SHULIANG WAIT 10";
			}elseif($flg==1){
				//取得在库零散库存数据
			    $sql = "SELECT QYBH,CKBH,KQBH,KWBH,SHPBH,PIHAO,RKDBH,ZKZHT,BZHDWBH,TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,TO_CHAR(BZHQZH,'YYYY-MM') AS BZHQZH,SHULIANG".
				       " FROM H01UV012005 ".
				       " WHERE QYBH = :QYBH ".
				       " AND SHPBH = :SHPBH ".
				       " AND SHULIANG > 0 ".
				       " AND SHFSHKW = '1'".               //零散
				       " ORDER BY ZKZHT DESC,RKDBH ASC,SHFGDJ DESC ".    //在库状态 >入库单>周转架
				       " FOR UPDATE OF SHULIANG WAIT 10";
			}
			
			//绑定查询变量
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] = $shpbh;
			
			//执行查询
			$recs = $this->_db->fetchAll( $sql, $bind );
			return $recs;
	}
	
	
	/*
	 * 更新在库和移动履历信息
	 */
	public function updateKucun($flg="2", $shuliang, $kucuns, $xshdbh="", $bhdbh="") {
		if ($shuliang ==0) return;
		$idx = 0;                      //移动履历序号
		$bhdxuhao = 0;                 //补货单序号
	    foreach ( $kucuns as $kucun ) {
			$shuliang_update = 0;      //在库更新数量
	
			//该条在库信息部分出库时 
			if ( $shuliang <= (int)$kucun['SHULIANG'] ) {
				$shuliang_update = (int)$kucun['SHULIANG'] - $shuliang;
				$shuliang_lvli = $shuliang;                //移动履历
				$shuliang = 0;
			} else {                                       //全部出库
				$shuliang_update = 0;
				$shuliang_lvli = (int)$kucun ['SHULIANG'];        //移动履历
				$shuliang = $shuliang - (int)$kucun ['SHULIANG'];
			}
			
			//更新在库信息H01DB012404
			$sql_zaiku = "UPDATE H01DB012404 ".
			             "SET SHULIANG = :SHULIANG ".
			             (($shuliang_update == 0) ? ",ZZHCHKRQ = SYSDATE " : "").
			             " WHERE QYBH = :QYBH ".
			             " AND CKBH = :CKBH " .
			             " AND KQBH = :KQBH ".
			             " AND KWBH = :KWBH ".
			             " AND SHPBH = :SHPBH " .
			             " AND PIHAO = :PIHAO " .
			             " AND RKDBH = :RKDBH " .
			             " AND ZKZHT = :ZKZHT " .
			             " AND BZHDWBH = :BZHDWBH ".
			             " AND SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD')";
			unset($bind);                
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;     //区域编号
			$bind ['CKBH'] = $kucun ['CKBH'];              //仓库
			$bind ['KQBH'] = $kucun ['KQBH'];              //库区
			$bind ['KWBH'] = $kucun ['KWBH'];              //库位
			$bind ['SHPBH'] = $kucun ['SHPBH'];            //商品编号
			$bind ['PIHAO'] = $kucun ['PIHAO'];            //批号
			$bind ['BZHDWBH'] = $kucun ['BZHDWBH'];        //包装单位
			$bind ['SHCHRQ'] = $kucun ['SHCHRQ'];          //生产日期
			$bind ['RKDBH'] = $kucun ['RKDBH'];            //入库单编号
			$bind ['ZKZHT'] = $kucun ['ZKZHT'];            //在库状态
			$bind ['SHULIANG'] = $shuliang_update;
			
			$this->_db->query ( $sql_zaiku,$bind );        //修改商品在库信息
			
			/*生成在库移动履历开始*/
			unset($lvli); 
			$lvli ["QYBH"] = $_SESSION ['auth']->qybh;     //区域编号
			$lvli ["CKBH"] = $kucun ['CKBH'];              //仓库编号
			$lvli ["KQBH"] = $kucun ['KQBH'];              //库区编号
			$lvli ["KWBH"] = $kucun ['KWBH'];              //库位编号
			$lvli ["SHPBH"] = $kucun ['SHPBH'];            //商品编号
			$lvli ["PIHAO"] = $kucun ['PIHAO'];            //批号
			$lvli ["RKDBH"] = $kucun ['RKDBH'];            //入库单号
		    //移动单号
			switch ($flg){
				case "2": //出库
					$lvli ["YDDH"] = $xshdbh;    //移动单号(销售单编号)
					break;
				case "6": //补货出库
					$lvli ["YDDH"] = $bhdbh;     //移动单号(补货单编号)
					break;
			}
			$lvli ["XUHAO"] = $idx ++;           //序号
			$lvli['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$kucun['SHCHRQ']."','YYYY-MM-DD')");    //生产日期
			$lvli['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$kucun['BZHQZH']."','YYYY-MM')");       //保质期至
			$lvli['CHLSHJ'] = new Zend_Db_Expr('SYSDATE');                                         //处理时间
			//移动数量
			switch ($flg){
				case "2": //出库
				case "6": //补货出库
					$lvli ["SHULIANG"] = $shuliang_lvli * - 1;       //移动数量
				break;
			}
			$lvli ["BZHDWBH"] = $kucun ['BZHDWBH'];        //包装单位编号
			//移动种类
		    switch ($flg){
				case "2": //出库
					$lvli ["ZHYZHL"] = "21";               //转移种类  出库
					break;
				case "6": //补货出库
					$lvli ["ZHYZHL"] = "61";               //转移种类  补货出库
					break;
			}
			
			$lvli["BEIZHU"] = '';                                    //备注
			$lvli["ZKZHT"] = $kucun ['ZKZHT'];                       //在库状态
			$lvli["ZCHRQ"] = new Zend_Db_Expr ( 'sysdate' );         //作成日期
			$lvli["ZCHZH"] = $_SESSION ['auth']->userId;             //作成者
			$lvli["BGRQ"] = new Zend_Db_Expr('SYSDATE');             //变更日期
			$lvli["BGZH"] = $_SESSION ['auth']->userId;              //变更者
			
			$this->_db->insert ( 'H01DB012405', $lvli );
			/*在库移动履历生成结束*/
			
			
			//补货
			if($flg=="6"){
				//取得补货目的地库位
				$toolModel = new gt_models_tool();
				$kwinfo = $toolModel->autoAssignKuwei($kucun["SHPBH"],$kucun["PIHAO"],0,$shuliang_lvli);
				//判断库存表中是否存在已有信息
				$sql = "SELECT * FROM H01DB012404 WHERE QYBH=:QYBH AND CKBH = :CKBH AND KQBH = :KQBH AND KWBH = :KWBH 
				        AND SHPBH = :SHPBH AND PIHAO = :PIHAO AND RKDBH = :RKDBH AND ZKZHT = :ZKZHT AND BZHDWBH = :BZHDWBH
				        AND SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD')";
				
				unset($bind);    
				$bind ['QYBH'] = $_SESSION['auth']->qybh;            //区域编号
				$bind ['CKBH'] = $kwinfo[0]['CKBH'];                 //仓库
				$bind ['KQBH'] = $kwinfo[0]['KQBH'];                 //库区
				$bind ['KWBH'] = $kwinfo[0]['KWBH'];                 //库位
				$bind ['SHPBH'] = $kucun['SHPBH'];                   //商品编号
				$bind ['PIHAO'] = $kucun['PIHAO'];                   //批号
				$bind ['BZHDWBH'] = $kucun['BZHDWBH'];               //包装单位
				$bind ['SHCHRQ'] = $kucun['SHCHRQ'];                 //生产日期
				$bind ['RKDBH'] = $kucun['RKDBH'];                   //入库单编号
				$bind ['ZKZHT'] = $kucun['ZKZHT'];                   //在库状态
				
				$kucunRec = $this->_db->fetchRow($sql,$bind);
				
				//如果无既存信息， 则插入一条新的在库信息
	        	if($kucunRec==FALSE){
		        	//生成在库信息H01DB012404
		        	unset($zaiku);
					$zaiku ["QYBH"] = $_SESSION['auth']->qybh;       //区域编号
					$zaiku ["CKBH"] = $kwinfo[0]['CKBH'];            //仓库编号
					$zaiku ["KQBH"] = $kwinfo[0]['KQBH'];            //库区编号
					$zaiku ["KWBH"] = $kwinfo[0]['KWBH'];            //库位编号
					$zaiku ["SHPBH"] = $kucun['SHPBH'];              //商品编号
					$zaiku ["PIHAO"] = $kucun['PIHAO'];              //批号
					$zaiku ["RKDBH"] = $kucun['RKDBH'];              //入库单号
                    $zaiku ["ZKZHT"] = $kucun['ZKZHT'];              //在库状态
                    $zaiku ["BZHDWBH"] = $kucun['BZHDWBH'];          //包装单位编号
                    $zaiku ["ZZHCHKRQ"] = new Zend_Db_Expr("TO_DATE('9999-12-31 23:59:59','YYYY-MM-DD HH24:MI:SS')");  //最终出库日期
                    $zaiku ["SHULIANG"] = $kwinfo[0]["SHULIANG"];    //数量
					$zaiku ['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$kucun['SHCHRQ']."','YYYY-MM-DD')");    //生产日期
					$zaiku ['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$kucun['BZHQZH']."','YYYY-MM')");       //保质期至
					
					$this->_db->insert ( 'H01DB012404', $zaiku );
					
	        	}else{
	        		//在库信息中存在既存的信息，则对库存数量进行更新处理
	        		$sql_zaiku = "UPDATE H01DB012404 ".
					             "SET SHULIANG = SHULIANG + :SHULIANG ," .
					             " ZZHCHKRQ = TO_DATE('9999-12-31','YYYY-MM-DD')".
					             " WHERE QYBH = :QYBH ".
					             " AND CKBH = :CKBH " .
					             " AND KQBH = :KQBH ".
					             " AND KWBH = :KWBH ".
					             " AND SHPBH = :SHPBH " .
					             " AND PIHAO = :PIHAO " .
					             " AND RKDBH = :RKDBH " .
					             " AND ZKZHT = :ZKZHT " .
					             " AND BZHDWBH = :BZHDWBH ".
					             " AND SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD')"; 
					unset($bind);    
					$bind ['QYBH'] = $_SESSION ['auth']->qybh;       //区域编号
					$bind ['CKBH'] = $kwinfo ['CKBH'];               //仓库
					$bind ['KQBH'] = $kwinfo ['KQBH'];               //库区
					$bind ['KWBH'] = $kwinfo ['KWBH'];               //库位
					$bind ['SHPBH'] = $kucun ['SHPBH'];              //商品编号
					$bind ['PIHAO'] = $kucun ['PIHAO'];              //批号
					$bind ['BZHDWBH'] = $kucun ['BZHDWBH'];          //包装单位
					$bind ['SHCHRQ'] = $kucun ['SHCHRQ'];            //生产日期
					$bind ['RKDBH'] = $kucun ['RKDBH'];              //入库单编号
					$bind ['ZKZHT'] = $kucun ['ZKZHT'];              //在库状态
					$bind ['SHULIANG'] = $kwinfo[0]["SHULIANG"];     //数量
					
					$this->_db->query ( $sql_zaiku,$bind );
	        	}
	        	
	        	//补货入库的移动履历生成
	        	unset($lvli); 
		       	$lvli ["QYBH"] = $_SESSION ['auth']->qybh;           //区域编号
				$lvli ["CKBH"] = $kwinfo[0] ['CKBH'];                //仓库编号
				$lvli ["KQBH"] = $kwinfo[0] ['KQBH'];                //库区编号
				$lvli ["KWBH"] = $kwinfo[0] ['KWBH'];                //库位编号
				$lvli ["SHPBH"] = $kucun ['SHPBH'];                  //商品编号
				$lvli ["PIHAO"] = $kucun ['PIHAO'];                  //批号
				$lvli ["RKDBH"] = $kucun ['RKDBH'];                  //入库单号
				$lvli ["YDDH"] = $bhdbh;                             //移动单号(补货单编号)
				$lvli ["XUHAO"] = $idx ++;                           //序号
				$lvli['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$kucun['SHCHRQ']."','YYYY-MM-DD')");     //生产日期
				$lvli['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$kucun['BZHQZH']."','YYYY-MM')");        //保质期至
				$lvli['CHLSHJ'] = new Zend_Db_Expr('SYSDATE');       //处理时间
				$lvli ["SHULIANG"] = $kwinfo[0]["SHULIANG"];         //移动数量
				$lvli ["BZHDWBH"] = $kucun ['BZHDWBH'];              //包装单位编号
				$lvli ["ZHYZHL"] = "62";                             //转移种类  补货入库				
				$lvli["BEIZHU"] = '';                                //备注
				$lvli["ZKZHT"] = $kucun ['ZKZHT'];                   //在库状态
				$lvli["ZCHRQ"] = new Zend_Db_Expr ( 'sysdate' );     //作成日期
				$lvli["ZCHZH"] = $_SESSION ['auth']->userId;         //作成者
				$lvli["BGRQ"] = new Zend_Db_Expr('SYSDATE');         //变更日期
				$lvli["BGZH"] = $_SESSION ['auth']->userId;          //变更者
				
				$this->_db->insert ( 'H01DB012405', $lvli );
			}
			
			/*出库单 补货单生成*/
			switch ($flg){
				case "2": //出库
					/*出库单生成*/
					$chukdmx["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
					$chukdmx["CHKDBH"] = $this->chkdbh; 
					$chukdmx["XUHAO"] =  $this->chkd_xuhao++; 
					$chukdmx["SHPBH"] = $kucun ['SHPBH'];
					$chukdmx["RKDBH"] = $kucun ['RKDBH'];
					$chukdmx["CKBH"] = $kucun ['CKBH'];
					$chukdmx["KQBH"] = $kucun ['KQBH'];
					$chukdmx["KWBH"] = $kucun ['KWBH'];
					$chukdmx["PIHAO"] = $kucun ['PIHAO'];
					$chukdmx["SHCHRQ"] = new Zend_Db_Expr("TO_DATE('".$kucun['SHCHRQ']."','YYYY-MM-DD')");   //生产日期
					$chukdmx["BZHQZH"] = new Zend_Db_Expr("TO_DATE('".$kucun['BZHQZH']."','YYYY-MM')");      //保质期至
					$chukdmx["SHULIANG"] = $shuliang_lvli;                //出库数量
					$chukdmx["CHHQRZHT"] = '1';                           //出货确认状态
					$chukdmx ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' );  //作成日期
				    $chukdmx ['ZCHZH'] = $_SESSION ['auth']->userId;      //作成者
			    	$chukdmx['BGRQ'] = new Zend_Db_Expr('SYSDATE');       //变更日期
			    	$chukdmx['BGZH'] = $_SESSION ['auth']->userId;        //变更者
			    	
	            	$this->_db->insert ( 'H01DB012409', $chukdmx );
	            	break;	
				case "6"://补货出库
					/*补货单生成*/
					$bhd["QYBH"] = $_SESSION ['auth']->qybh;         //区域编号
					$bhd["BHDBH"] = $bhdbh;                          //补货单编号
					$bhd["XUHAO"] = $bhdxuhao++;                     //序号
					$bhd["SHPBH"] = $kucun ['SHPBH'];                //商品编号
					$bhd["PIHAO"] = $kucun ['PIHAO'];                //批号
					$bhd["RKDBH"] = $kucun ['RKDBH'];                //入库单编号
					$bhd["YCHCK"] = $kucun ['CKBH'];                 //移出仓库编号
					$bhd["YCHKQ"] = $kucun ['KQBH'];                 //移出库区编号
					$bhd["YCHKW"] = $kucun ['KWBH'];                 //移出库位编号
					$bhd["YRCK"] = $kwinfo[0] ['CKBH'];              //移入仓库编号
					$bhd["YRKQ"] = $kwinfo[0] ['KQBH'];              //移入库区编号
					$bhd["YRKW"] = $kwinfo[0] ['KWBH'];              //移入库位编号
					$bhd["BHLX"] = "2";                              //补货类型：随单自动补货
					$bhd["XSHDBH"] = $xshdbh;                        //销售单编号
					$bhd["BHSHL"] = $kwinfo[0]["SHULIANG"];          //补货数量
					$bhd["ZHUANGTAI"] = "1";                         //补货状态：未完成
					$bhd["ZCHRQ"] = new Zend_Db_Expr ( 'sysdate' );  //作成日期
				    $bhd["ZCHZH"] = $_SESSION ['auth']->userId;      //作成者
			    	$bhd["BGRQ"] = new Zend_Db_Expr('SYSDATE');      //变更日期
			    	$bhd["BGZH"] = $_SESSION ['auth']->userId;       //变更者
			    	
	            	$this->_db->insert ( 'H01DB012450', $bhd );
	            	break;	
				}

			//剩余数量为零则出库完毕，不再继续循环
			if ($shuliang <= 0) break;
		}
	}
		
}
