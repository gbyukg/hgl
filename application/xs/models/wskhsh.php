<?php
/******************************************************************
 ***** 模         块：       销售模块(XS)
 ***** 机         能：       网上客户审核(wskhsh)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/11/16
 ***** 更新履历：
 ******************************************************************/

class xs_models_wskhsh extends Common_Model_Base {
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
	
	/*
	 * 根据单位编号编号取得单位信息
	 */
	public function getDanweiInfo(){
		//检索单位编号
		$sql = "SELECT DWBH " .
		      " FROM H01DB012107 " .
		      " WHERE QYBH = :QYBH " .  //区域编号
		      " AND YHID = :YHID ".     //用户ID
		      " AND YHLX = '2' " .      //用户类型      1:员工      2:单位
		      " AND YHZHT ='1' ".       //用户状态
			  " AND YXQKSH <= SYSDATE ".
			  " AND SYSDATE <= YXQJSH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['YHID'] = $_SESSION ['auth']->userId;
		
		$dwbh = $this->_db->fetchOne( $sql, $bind );
		
		
        //检索单位信息
		$dwsql = "SELECT DWBH,DWMCH,(SZSHMCH || SZSHIMCH || DIZHI) AS DIZHI,".
				 "DHHM,KOULV,FHQBH,FHQMCH,DECODE(XSHXDQ,NULL,0,XSHXDQ) AS XSHXDQ,SHFZZHSH" .
		      	 " FROM H01VIEW012106 " . 
		         " WHERE QYBH = :QYBH " .   //区域编号
		         " AND DWBH = :DWBH " .     //单位编号
		         " AND KHZHT = '1' " ;
				
		//绑定查询条件
		$dwbind ['QYBH'] = $_SESSION ['auth']->qybh;
		$dwbind ['DWBH'] = $dwbh;
		
		return $this->_db->fetchRow( $dwsql, $dwbind );
	}
	
	
	/**
	 * 得到单据列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridDanjuData($filter){
				//排序用字段名
		$fields = array ("", "NLSSORT(DWMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "WSHXSHDH");
		//检索SQL
		$sql = "SELECT DWMCH,WSHXSHDH,TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ,DHHM,DIZHI,BEIZHU,FKFSH,SHFZZHSH,SHFPS,SHFYQTPH,DWBH,QYBH,QXBZH,ZHUANGTAI FROM ".
				"(SELECT B.DWMCH,A.WSHXSHDH,A.KPRQ,A.DHHM,A.DIZHI,A.BEIZHU,".
				"A.FKFSH,A.SHFZZHSH,A.SHFPS,A.SHFYQTPH,A.QYBH,A.QXBZH,A.ZHUANGTAI,A.DWBH ".
				"FROM H01DB012215 A ".
				"LEFT JOIN H01DB012106 B ON A.DWBH = B.DWBH AND A.QYBH = B.QYBH ) ".
				"WHERE QYBH = :QYBH ".           //区域编号
				"AND DWBH = :DWBH ".             //单位编号
				"AND QXBZH = '1' ";              //取消（删除）标志
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter['dwbh'];
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("XS_WSKHSH_DJ",$filter['filterParams'],$bind);
			
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",DWBH,WSHXSHDH";

		$recs = $this->_db->fetchAll($sql,$bind);
		
		return Common_Tool::createXml( $recs, true );
	}
	
	
	/**
	 * 检索销售订单审批原因
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getSHPGridData($filter) {	     
		$sql = "SELECT SHPYY "                    //审批原因
			  ."FROM H01DB012217 "
			  ."WHERE QYBH = :QYBH "              //区域编号
			  ."AND WSHXSHDH = :WSHXSHDH "        //销售单编号
			  ."ORDER BY XUHAO";
			  
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['WSHXSHDH'] = $filter ["bh"];
		
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
	 * 得到单据明细列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridMingxiData($filter) {	     
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
		$bind ['WSHXSHDH'] = $filter ['bh'];

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
	
}
