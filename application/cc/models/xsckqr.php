<?php
/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：       销售出库确认(xsckqr)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2010/12/28
 ***** 更新履历：

 ******************************************************************/

class cc_models_xsckqr extends Common_Model_Base {
	private $_xsdbh = null;      //销售单编号
	private $idx_ROWNUM = 0;     // 行号
	private $idx_SHPBH = 1;      // 商品编号
	private $idx_SHPMCH = 2;     // 商品名称
	private $idx_GUIGE = 3;      // 规格
	private $idx_BZHDWM = 4;     // 包装单位
	private $idx_HWMCH = 5;      // 库位
	private $idx_PIHAO = 6;      // 批号
	private $idx_SHCHRQ = 7;     // 生产日期
	private $idx_BZHQZH = 8;     // 保质期至
	private $idx_BZHSHL = 9;     // 包装数量
	private $idx_LSSHL = 10;     // 零散数量
	private $idx_SHULIANG = 11;  // 数量
	private $idx_DANJIA = 12;    // 单价
	private $idx_HSHJ = 13;      // 含税价
	private $idx_KOULV = 14;     // 扣率
	private $idx_SHUILV = 15;    // 税率
	private $idx_JINE = 16;      // 金额
	private $idx_HSHJE = 17;     // 含税金额
	private $idx_SHUIE = 18;     // 税额
	private $idx_BEIZHU = 19;    // 备注
	private $idx_BZHDWBH = 20;   // 包装单位编号
	private $idx_CKBH = 21;      // 仓库编号
	private $idx_KQBH = 22;      // 库区编号
	private $idx_KWBH = 23;      // 库位编号
	private $idx_KWSHL = 24;     // 库位数量
	private $idx_TYMCH = 25;     // 通用名
	private $idx_CHANDI = 26;    // 产地
	
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
	 * 得到销售单列表数据-销售订单选择页
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridDanjuData($filter){
		//排序用字段名
		$fields = array ("", "A.XSHDBH","A.KPRQ","B.BMMCH","C.YGXM","A.DWBH","E.DWMCH");
		
		//检索SQL
		$sql = "SELECT A.XSHDBH,TO_CHAR(A.KPRQ,'YYYY-MM-DD'),B.BMMCH,C.YGXM,A.DWBH,E.DWMCH,"
				."A.DIZHI,A.DHHM,A.SHFZZHSH,A.KOULV,D.FHQMCH,A.BEIZHU,A.BGRQ,A.BGZH "
				."FROM H01DB012201 A "
				."LEFT JOIN H01DB012112 B ON A.QYBH = B.QYBH AND A.BMBH = B.BMBH "
				."LEFT JOIN H01DB012113 C ON A.QYBH = C.QYBH AND A.YWYBH = C.YGBH "
				."LEFT JOIN H01DB012422 D ON A.QYBH = D.QYBH AND A.FHQBH = D.FHQBH "
				."LEFT JOIN H01DB012106 E ON A.QYBH = D.QYBH AND A.DWBH = E.DWBH "
				."WHERE A.QYBH = :QYBH AND A.XSHDZHT = '0' AND (A.SHHZHT = '0' OR A.SHHZHT = '1') "
				."AND A.QXBZH != 'X' AND ((A.FKFSH = '2' AND A.JSZHT = '1') OR (A.FKFSH != '2')) ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter ["ksrqkey"] != "" || $filter ["zzrqkey"] != "")
		{
			$sql .= " AND :KSRQ <= TO_CHAR(A.KPRQ,'YYYY-MM-DD') AND TO_CHAR(A.KPRQ,'YYYY-MM-DD') <= :ZZRQ";
			$bind ['KSRQ'] = $filter ["ksrqkey"] == ""?"1900-01-01":$filter ["ksrqkey"];
			$bind ['ZZRQ'] = $filter ["zzrqkey"] == ""?"9999-12-31":$filter ["zzrqkey"];
		}
		
		//查询条件(单位编号输入)
		if ($filter ["dwbhkey"] != "") {
			$sql .= " AND A.DWBH LIKE '%' || :DWBH || '%'";
			$bind ['DWBH'] = $filter ["dwbhkey"];
		}
		
		//查询条件(单位编号没输入,只输入单位名称)
		if($filter ["dwbhkey"] == "" && $filter ["dwmchkey"] != "") {
			$sql .= " AND E.DWMCH LIKE '%' || :DWMCH || '%'";
			$bind ['DWMCH'] = $filter ["dwmchkey"];
		}
		
		if($filter ["xsdkey"] != "") {
			$sql .= " AND A.XSHDBH LIKE '%' || :XSHDBH || '%'";
			$bind ['XSHDBH'] = $filter ["xsdkey"];
		}
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.XSHDBH";
		
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
	 * 得到销售单明细列表数据-销售订单选择页
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridMingxiData($filter){
		//排序用字段名
		$fields = array ("", "A.SHPBH", "NLSSORT(B.SHPMCH,'NLS_SORT=SCHINESE_PINYIN_M')");
		     
		$sql = "SELECT "          //序号
				."A.SHPBH,"      		  //商品编号
				."B.SHPMCH,"     		  //商品名称
				."B.GUIGE,"      		  //规格
				."C.NEIRONG AS BZHDWM,"   //包装单位
				."A.PIHAO,"      		  //批号
				."D.CKMCH || E.KQMCH || F.KWMCH AS HWMCH,"     //货位名称
				."TO_CHAR(A.SHCHRQ,'yyyy-mm-dd') AS SHCHRQ,"   //生产日期
				."TO_CHAR(A.BZHQZH,'yyyy-mm') AS BZHQZH,"   //保质期至
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
				."A.BEIZHU "      	      //备注
			  ."FROM H01DB012202 A "
			  ."LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "  
			  ."LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' "
			  ."LEFT JOIN H01DB012401 D ON A.QYBH = D.QYBH AND A.CKBH = D.CKBH "
			  ."LEFT JOIN H01DB012402 E ON A.QYBH = E.QYBH AND A.CKBH = E.CKBH AND A.KQBH = E.KQBH "
			  ."LEFT JOIN H01DB012403 F ON A.QYBH = F.QYBH AND A.CKBH = F.CKBH AND A.KQBH = F.KQBH AND A.KWBH = F.KWBH "
			  ."WHERE A.QYBH = :QYBH "       //区域编号
			  ."AND A.XSHDBH = :XSHDBH ";    //销售单编号
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['XSHDBH'] = $filter ["bh"];
				
		//排序
		$sql .= " ORDER BY " . $fields [$filter["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.XSHDBH,A.XUHAO";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne( $pagedSql["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll( $pagedSql["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true,$totalCount, $filter ["posStart"] );
	}
	

	/**
	 * 获取库位状态
	 * 
	 * @return array[]
	 */
	function getKwzht(){
		$result ['status'] = '0';

		$sql = "SELECT A.KWZHT,A.SHFSHKW,B.KQZHT,C.CKZHT FROM H01DB012403 A "
				."LEFT JOIN H01DB012402 B ON A.QYBH = B.QYBH AND A.CKBH = B.CKBH AND A.KQBH = B.KQBH "
				."LEFT JOIN H01DB012401 C ON A.QYBH = C.QYBH AND A.CKBH = C.CKBH "
				."WHERE A.QYBH = :QYBH AND A.CKBH = :CKBH AND A.KQBH = :KQBH AND A.KWBH = :KWBH";

		foreach( $_POST ["#grid_mingxi"] as $grid ){
			$data ['QYBH'] = $_SESSION ['auth']->qybh;         //区域编号
			$data ['CKBH'] = $grid [$this->idx_CKBH];          //仓库编号
			$data ['KQBH'] = $grid [$this->idx_KQBH];          //库区编号
			$data ['KWBH'] = $grid [$this->idx_KWBH];          //库位编号

			$zht = $this->_db->fetchOne( $sql, $data );

			if ( $zht == '0' || $zht == '9' ){
				$result ['status'] = '9';      //库位被冻结
				$result ['data']['rIdx'] = (int)$grid[$this->idx_ROWNUM];  //定位明细行index
			}
		}
		return $result;
	}

	
	/**
	 * 出库单信息保存
	 * @param  string  $chkdbh:   出库单编号
	 * 
	 * @return bool
	 */
	public function saveChkdMain($chkdbh) {
		$data ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
		$data ['CHKDBH'] = $chkdbh;                   //出库单编号
		$data ['CKDBH'] = $_POST ['XSD'];             //参考单编号
		$data ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$data ['BMBH'] = $_POST ['BMBH'];             //部门编号
		$data ['KPYBH'] = $_SESSION ['auth']->userId; //开票员编号
		$data ['YWYBH'] = $_POST ['YWYBH'];           //业务员编号
		$data ['DWBH'] = $_POST ['DWBH'];             //单位编号
		$data ['DIZHI'] = $_POST ['DIZHI'];           //地址
		$data ['DHHM'] = $_POST ['DHHM'];             //电话
		$data ['SHFZZHSH'] = $_POST ['SHFZZHSH'];     //是否增值税    0:否(未选中) 1:是(选中)
		$data ['KOULV'] = $_POST ['KOULV'];           //扣率
		$data ['FHQBH'] = $_POST ['FAHUOQU'];         //发货区
		$data ['FKFSH'] = $_POST ['FKFSH'];           //付款方式
		$data ['SHFPS'] = $_POST ['SHFPS'];           //是否配送         0:否(未选中) 1:是(选中)
		$data ['BEIZHU'] = $_POST ['BEIZHU'];         //备注
		$data ['CHKLX'] = '1';                        //出库类型：1.销售出库
		$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' );        //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId;            //变更者
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		return $this->_db->insert( "H01DB012408", $data );     //插入出库单信息
	}
	
	
	/*
	 * 出库单明细保存
	 * @param  string  $chkdbh:   出库单编号
	 * 
	 */
	public function saveChkdMingxi($chkdbh) {
		$idx = 1;           //序号自增
        //循环所有明细行，保存出库单明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == '')continue;
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh;        //区域编号
			$data ['CHKDBH'] = $chkdbh;                       //出库单编号
			$data ['XUHAO'] = $idx ++;                        //序号
			$data ['SHPBH'] = $grid [$this->idx_SHPBH];       //商品编号
			$data ['CKBH'] = $grid [$this->idx_CKBH];         //仓库编号
			$data ['KQBH'] = $grid [$this->idx_KQBH];         //库区编号
			$data ['KWBH'] = $grid [$this->idx_KWBH];         //库位编号
			$data ['PIHAO'] = $grid [$this->idx_PIHAO];       //批号
			//生产日期
			$data ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" );
			//保质期至
			$data ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM')" ); 
			//包装数量
			$data ['BZHSHL'] = ($grid [$this->idx_BZHSHL] == null) ? 0 : $grid [$this->idx_BZHSHL]; 
			//零散数量
			$data ['LSSHL'] = ($grid [$this->idx_LSSHL] == null) ? 0 : $grid [$this->idx_LSSHL]; 
			//数量
			$data ['SHULIANG'] = ($grid [$this->idx_SHULIANG] == null) ? 0 : $grid [$this->idx_SHULIANG]; 
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
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			$this->_db->insert ( "H01DB012409", $data );	  //出库单明细表	
		}
	}
	
	
	/*
	 * 更新销售单状态为1：已出库
	 */
	public function updatexsdzht(){
		$sql = "UPDATE H01DB012201 "
             ." SET XSHDZHT = '1' "               //更新销售单状态为1：已出库
             ." WHERE QYBH = :QYBH "
             ." AND XSHDBH = :XSHDBH " ;

			$bind ['QYBH'] = $_SESSION ['auth']->qybh;   //区域编号
			$bind ['XSHDBH'] = $_POST ['XSD'];        //销售单编号

			$this->_db->query( $sql,$bind );
	}

	
	/**
	 * 取得销售单状态
	 * @param 	string 	$bh	编号
	 * 
	 * @return 	array 
	 */
	public function getzht($bh) {
		$sql = "SELECT XSHDZHT,FKFSH,JSZHT FROM H01DB012201 WHERE QYBH = :QYBH AND XSHDBH = :XSHDBH ";
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['XSHDBH'] = $bh;

		return $this->_db->fetchRow( $sql, $bind );	
	}
	

	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck(){
		if ($_POST ["KPRQ"] == "" ||           //开票日期
            $_POST ["BMBH"] == "" ||           //部门编号
            $_POST ["XSD"] == "" ||            //销售单号
            $_POST ["YWYBH"] == "" ){          //营业员编号
			return false;
		}

		return true;
	}
	
	
	/*
	 * 自动完成数据取得
	 */
	public function getAutocompleteData($filter){	
		//检索SQL
		$sql = "SELECT XSHDBH FROM H01DB012201 " 
		       ."WHERE QYBH = :QYBH "         //区域编号
			   ."AND (SHHZHT = '0' OR SHHZHT = '1') "    //审核状态
			   ."AND QXBZH != 'X' ";          //取消标志

		if ($filter ['flg'] == '0') {         //销售单状态未出库
			$sql .= " AND XSHDZHT = '0'";
		} elseif ($filter ['flg'] == '1') {   //销售单状态已出库
			$sql .= " AND XSHDZHT = '1'";
		}

		//快速查找条件
		if ($filter ["searchkey"] != "") {
			$bind ['SEARCHKEY'] = $filter ["searchkey"];
			$sql .= " AND lower(XSHDBH) LIKE '%'||:SEARCHKEY||'%' ";
		}

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		return $this->_db->fetchAll($sql,$bind);
	}
	
	
	/*
	 * 根据销售单编号取得销售单信息
	 */
	public function getxsdInfo($filter) {
		//检索SQL
		$sql = "SELECT A.XSHDBH,"          //销售单编号
			  ."A.DWBH,"                   //单位编号
			  ."B.DWMCH,"                  //单位名称
			  ."A.DHHM,"                   //电话号码
			  ."A.DIZHI,"                  //地址
			  ."A.KOULV,"                  //扣率
			  ."A.SHFZZHSH,"               //是否增值税
			  ."A.FHQBH,"                  //发货区编号
			  ."A.SHFPS,"                  //是否配送
			  ."A.FKFSH,"                  //付款方式
			  ."A.BEIZHU "                 //备注
			  ."FROM H01DB012201 A "
			  ."LEFT JOIN H01DB012106 B ON A.QYBH = B.QYBH AND A.DWBH = B.DWBH "
			  ."WHERE A.QYBH = :QYBH "      //区域编号
			  ."AND A.XSHDBH = :XSHDBH ";   //销售单编号

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['XSHDBH'] = $filter ['bh'];
		return $this->_db->fetchRow( $sql, $bind );
	}
	
	
	/*
	 * 根据销售单编号取得销售单明细信息
	 */
	public function getxsdmingxi($filter) {
		//检索SQL
		$sql = "SELECT " 
				."A.SHPBH,"      		  //商品编号
				."B.SHPMCH,"     		  //商品名称
				."B.GUIGE,"      		  //规格
				."C.NEIRONG AS BZHDWM,"   //包装单位
				."D.CKMCH || E.KQMCH || F.KWMCH AS HWMCH,"     //货位名称
				."A.PIHAO,"      		  //批号
				."TO_CHAR(A.SHCHRQ,'yyyy-mm-dd') AS SHCHRQ,"   //生产日期
				."TO_CHAR(A.BZHQZH,'yyyy-mm') AS BZHQZH,"   //保质期至
				."A.BZHSHL,"     		  //包装数量
				."A.LSSHL,"      		  //零散数量
				."A.SHULIANG,"  		  //数量
				."A.DANJIA,"      	      //单价
				."A.HSHJ,"       		  //含税价
				."A.KOULV,"               //扣率
				."B.SHUILV,"    	 	  //税率
				."A.JINE,"      	 	  //金额
				."A.HSHJE,"     	 	  //含税金额
				."A.SHUIE,"      		  //税额
				."A.BEIZHU,"      	      //备注
				."B.BZHDWBH,"    		  //包装单位编号
				."A.CKBH,"                //仓库编号
				."A.KQBH,"                //库区编号
				."A.KWBH,"                //库位编号
				."B.TYMCH,"               //通用名
				."B.CHANDI "              //产地
			  ."FROM H01DB012202 A "
			  ."LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "  
			  ."LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' "
			  ."LEFT JOIN H01DB012401 D ON A.QYBH = D.QYBH AND A.CKBH = D.CKBH "
			  ."LEFT JOIN H01DB012402 E ON A.QYBH = E.QYBH AND A.CKBH = E.CKBH AND A.KQBH = E.KQBH "
			  ."LEFT JOIN H01DB012403 F ON A.QYBH = F.QYBH AND A.CKBH = F.CKBH AND A.KQBH = F.KQBH AND A.KWBH = F.KWBH "
			  ."WHERE A.QYBH = :QYBH "       //区域编号
			  ."AND A.XSHDBH = :XSHDBH ";    //销售单编号
			  
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
		$bind ['XSHDBH'] = $filter ['bh'];           //采购退货单编号
		
		return $this->_db->fetchAll( $sql, $bind );
	}
	
	
	/*
	 * 根据单位编号编号取得单位信息
	 */
	public function getDanweiInfo($filter) {
		//检索SQL
		$sql =  "SELECT A.DWBH,A.DWMCH,A.DIZHI,A.DHHM,A.KOULV,A.FHQBH,DECODE(A.XSHXDQ,NULL,0,XSHXDQ) XSHXDQ" . //销售信贷期 
			    " FROM H01DB012106 A WHERE A.QYBH = :QYBH " . //区域编号
				" AND A.DWBH = :DWBH" . //单位编号
				" AND A.FDBSH ='0'" . //分店标识
				" AND A.SHFXSH = '1'" . //是否销售
				" AND A.KHZHT = '1'"; //客户状态

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
}
		