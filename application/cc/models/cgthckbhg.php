<?php
/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：       采购退货出库-不合格品出库(cgthckbhg)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/08/05
 ***** 更新履历：
 ******************************************************************/

class cc_models_cgthckbhg extends Common_Model_Base {
	private $_cgthckbh = null;        // 采购退货出库单编号
	private $idx_ROWNUM=0;            // 行号
	private $idx_SHPBH=1;             // 商品编号
	private $idx_SHPMCH=2;            // 商品名称
	private $idx_GUIGE=3;             // 规格
	private $idx_BZHDWM=4;            // 包装单位
	private $idx_PIHAO=5;             // 批号
	private $idx_SHCHRQ=6;            // 生产日期
	private $idx_BZHQZH=7;            // 保质期至
	private $idx_SHULIANG=8;          // 数量
	private $idx_DANJIA=9;            // 单价
	private $idx_HSHJ=10;             // 含税价
	private $idx_KOULV=11;            // 扣率
	private $idx_SHUILV=12;           // 税率
	private $idx_HSHJE=13;            // 含税金额
	private $idx_JINE=14;             // 金额
	private $idx_SHUIE=15;            // 税额
	private $idx_CHANDI=16;           // 产地
	private $idx_BEIZHU=17;           // 备注
	private $idx_BZHDWBH = 18;        // 包装单位编号
	private $idx_TYMCH=19;            // 通用名称
	private $idx_JLGG=20;             // 计量规格


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
		$fields = array ("", "CGTHDBH", "KPRQ", "DWBH", "NLSSORT(DWMCH,'NLS_SORT=SCHINESE_PINYIN_M')",
						 "YRKDBH", "KPRQ", "NLSSORT(BMMCH,'NLS_SORT=SCHINESE_PINYIN_M')", 
						 "NLSSORT(YGXM,'NLS_SORT=SCHINESE_PINYIN_M')");
		
		//检索SQL
		$sql = "SELECT CGTHDBH,TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ,DWBH,DWMCH,YRKDBH,TO_CHAR(YRKDRQ,'YYYY-MM-DD') AS YRKDRQ,BMMCH,YWYXM,QYBH,THDZHT,QXBZH FROM "
				."(SELECT A.QYBH,A.THDZHT,A.QXBZH,A.CGTHDBH,A.KPRQ,A.DWBH,A.DWMCH,A.YRKDBH,B.KPRQ AS YRKDRQ,A.BMMCH,A.YWYXM,A.SHHZHT,A.THLX "
				."FROM H01VIEW012308 A "
				."LEFT JOIN H01DB012406 B ON A.QYBH = B.QYBH AND A.YRKDBH = B.RKDBH) "
				."WHERE QYBH = :QYBH AND THDZHT = '0' AND QXBZH != 'X' AND SHHZHT = '1' AND THLX = '2' ";

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;

		//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter ['searchParams']["KSRQKEY"] != "" || $filter ['searchParams']["ZZRQKEY"] != "")
		{
			$sql .= " AND :KSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :ZZRQ";
			$bind ['KSRQ'] = $filter ['searchParams']["KSRQKEY"] == ""?"1900-01-01":$filter ['searchParams']["KSRQKEY"];
			$bind ['ZZRQ'] = $filter ['searchParams']["ZZRQKEY"] == ""?"9999-12-31":$filter ['searchParams']["ZZRQKEY"];
		}
		
		//查询条件(单位编号输入)
		if ($filter ['searchParams']["DWBHKEY"] != "") {
			$sql .= " AND DWBH LIKE '%' || :DWBH || '%'";
			$bind ['DWBH'] = $filter ['searchParams']["DWBHKEY"];
		}
		
		//查询条件(单位编号没输入,只输入单位名称)
		if($filter ['searchParams']["DWBHKEY"] == "" && $filter ['searchParams']["DWMCHKEY"] != "") {
			$sql .= " AND DWMCH LIKE '%' || :DWMCH || '%'";
			$bind ['DWMCH'] = $filter ['searchParams']["DWMCHKEY"];
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_CGTHCK_DJ",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",CGTHDBH";
		
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
	 * 得到退货单明细列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridMingxiData($filter) {
				//排序用字段名
		$fields = array ("", "SHPBH", "NLSSORT(SHPMCH,'NLS_SORT=SCHINESE_PINYIN_M')");
		     
		$sql = "SELECT SHPBH,SHPMCH,GUIGE,BZHDWM,PIHAO,SHCHRQ,BZHQZH,SHULIANG,DANJIA,HSHJ," 
				."KOULV,SHUILV,HSHJE,JINE,SHUIE,CHANDI,BEIZHU,BZHDWBH,TYMCH,JLGG,QYBH,CGTHDBH FROM " 
				."(SELECT A.QYBH,"
				."A.CGTHDBH,"        
				."A.SHPBH,"      		  //商品编号
				."B.SHPMCH,"     		  //商品名称
				."B.GUIGE,"      		  //规格
				."C.NEIRONG AS BZHDWM,"   //包装单位
				."A.PIHAO,"      		  //批号
				."TO_CHAR(A.SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,"     //生产日期
				."TO_CHAR(A.BZHQZH,'YYYY-MM-DD') AS BZHQZH,"     //保质期至
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
				."B.JLGG,"                //计量规格
				."A.CKBH,"                //仓库编号
				."A.KQBH,"                //库区编号
				."A.KWBH "                //库位编号
			  ."FROM H01DB012309 A "
			  ."LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "  
			  ."LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW') "
			  ."WHERE QYBH = :QYBH "       //区域编号
			  ."AND CGTHDBH = :CGTHDBH ";  //采购退货单编号  例：CGT10121300001
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGTHDBH'] = $filter ["thdbh"];
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_CGTHCK_THDSHP",$filter['filterParams'],$bind);
				
		//排序
		$sql .= " ORDER BY " . $fields [$filter["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",CGTHDBH";
		
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
			    " FROM H01DB012106 A" . 
			    " WHERE A.QYBH = :QYBH " . //区域编号
				" AND A.DWBH = :DWBH" . //单位编号
				" AND A.FDBSH ='0'" . //分店标识
				" AND A.SHFXSH = '1'" . //是否销售
				" AND A.KHZHT = '1'"; //客户状态

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
	
	/**
	 * 出库单信息保存
	 * @param  string  $chkdbh:   出库单编号
	 * 
	 * @return bool
	 */
	public function saveChkdMain($chkdbh) {
		$data ['QYBH'] = $_SESSION ['auth']->qybh;              //区域编号
		$data ['BHGPCHKDBH'] = $chkdbh;                         //不合格品出库单编号
		$data ['CKDBH'] = $_POST ['CGTHD'];                     //参考单编号：原采购退货单号
		$data ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$data ['BMBH'] = $_POST ['BMBH'];                       //部门编号
		$data ['KPYBH'] = $_SESSION ['auth']->userId;           //开票员编号
		$data ['YWYBH'] = $_POST ['YWYBH'];                     //业务员编号
		$data ['BEIZHU'] = $_POST ['BEIZHU'];                   //备注
		$data ['CHKLX'] = '2';                                  //出库类型：2 采购退货出库
		$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' );        //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId;            //变更者
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' );       //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId;           //作成者
		
		return $this->_db->insert ( "H01DB012462", $data );     //插入出库单信息
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
			$data ['BHGPCHKDBH'] = $chkdbh;                   //不合格品出库单编号
			$data ['XUHAO'] = $idx ++;                        //序号
			$data ['SHPBH'] = $grid [$this->idx_SHPBH];       //商品编号
			$data ['CKBH'] = $_SESSION ['auth']->ckbh;        //仓库编号
			$data ['PIHAO'] = $grid [$this->idx_PIHAO];       //批号
			$data ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" ); //生产日期
			$data ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM-DD')" ); //保质期至
			$data ['SHULIANG'] = $grid [$this->idx_SHULIANG]; //数量
			$data ['DANJIA'] = $grid [$this->idx_DANJIA];     //单价
			$data ['HSHJ'] = $grid [$this->idx_HSHJ];         //含税价
			$data ['KOULV'] = $grid [$this->idx_KOULV];       //扣率
			$data ['JINE'] = $grid [$this->idx_JINE];         //金额
			$data ['HSHJE'] = $grid [$this->idx_HSHJE];       //含税金额
			$data ['SHUIE'] = $grid [$this->idx_SHUIE];       //税额
			$data ['BEIZHU'] = $grid [$this->idx_BEIZHU];     //备注
			$data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' );  //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId;      //变更者
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId;     //作成者
			
			$this->_db->insert ( "H01DB012463", $data );	  //出库单明细表	
		}
	}
	
	
	/*
	 * 出库单保存处理
	 */
	public function updateKucun() {
		$result ['status'] = '0';
		
		//循环所有明细行进行库存数量检验
		foreach ( $_POST ["#grid_mingxi"] as $row ) {
			if ($row [$this->idx_SHPBH] == '')continue;
			//取得即时库存信息
			$sql = "SELECT A.QYBH,A.CKBH,A.SHPBH,A.PIHAO,A.RKDBH,A.BZHDWBH,A.SHULIANG,".
				   "TO_CHAR(A.SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,".
			 	   "TO_CHAR(A.BZHQZH,'YYYY-MM') AS BZHQZH".
			       " FROM H01DB012459 A " .
			       " WHERE A.QYBH = :QYBH " .          //区域编号
                   " AND A.CKBH = :CKBH " .            //仓库编号
                   " AND A.SHPBH = :SHPBH " .          //商品编号
                   " AND A.PIHAO = :PIHAO " .          //批号
				   " AND A.RKDBH = :RKDBH " .          //入库单编号
                   " AND A.BZHDWBH = :BZHDWBH " .      //包装单位
                   " FOR UPDATE WAIT 10 ";             //对象库存数据锁定
			
			//绑定查询变量
			$bind1 ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind1 ['CKBH'] = $_SESSION ['auth']->ckbh;
			$bind1 ['SHPBH'] = $row [$this->idx_SHPBH];
			$bind1 ['PIHAO'] = $row [$this->idx_PIHAO];
			$bind1 ['RKDBH'] = $_POST ['YRKDBH'];
			$bind1 ['BZHDWBH'] = $row [$this->idx_BZHDWBH];

			//当前明细行在库信息
			$recs = $this->_db->fetchRow( $sql, $bind1 );
			
			$shuliang_zaiku = (int)$recs['SHULIANG'];           //在库数量

			//当前库存数量不足
			if ($shuliang_zaiku < ( int ) $row [$this->idx_SHULIANG]) {
				$result ['status'] = '1';                       //库存不足
				$result ['data']['rIdx'] = ( int ) $row [$this->idx_ROWNUM];    //定位明细行index
				$result ['data']['shuliang'] = $shuliang_zaiku; //最新在库数量
//				$kucunModel = new gt_models_kucun();            //库存不足时取得最新库存数据，返回页面用
//				$result ['data']['kucundata'] = $kucunModel->getKucunData( array('shpbh'=>$row [$this->idx_SHPBH],'shfshkw'=> $row [$this->idx_SHFSHKW]));
			}

			//库存数量充足
			if($result['status']=='0'){
				//更新在库和移动履历信息
			    $this->updateZaiku( $row, $recs );
			}
		}

		return $result;
	}
	
	
	/*
	 * 更新在库和移动履历信息
	 */
	public function updateZaiku($row,$kucun) {

		$shuliang_TH = ( int ) $row [$this->idx_SHULIANG];              //预退货数量
		$shuliang = ( int ) $kucun ['SHULIANG'] - $shuliang_TH;         //退货后剩余数量
		
		//更新在库信息
		$sql_zaiku = "UPDATE H01DB012459 ".
		             "SET SHULIANG = :SHULIANG ".
		             " WHERE QYBH = :QYBH ".
		             " AND CKBH = :CKBH ".
		             " AND SHPBH = :SHPBH ".
		             " AND PIHAO = :PIHAO ".
		             " AND RKDBH = :RKDBH ".
		             " AND BZHDWBH = :BZHDWBH ";
		             
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;           //区域编号
		$bind ['CKBH'] = $kucun ['CKBH'];                    //仓库编号
		$bind ['SHPBH'] = $kucun ['SHPBH'];                  //商品编号
		$bind ['PIHAO'] = $kucun ['PIHAO'];                  //批号
		$bind ['BZHDWBH'] = $kucun ['BZHDWBH'];              //包装单位编号
		$bind ['RKDBH'] = $kucun ['RKDBH'];                  //入库单编号
		$bind ['SHULIANG'] = $shuliang;                      //退货后剩余数量

		$this->_db->query ( $sql_zaiku,$bind );
		
	}
	
	
	/*
	 * 更新采购退货单出库状态
	 */
	public function updateCgthzht(){
		$sql = "UPDATE H01DB012308 "
             ." SET THDZHT = '1' "      //1:已经出库
             ." WHERE QYBH = :QYBH "
             ." AND CGTHDBH = :CGTHDBH " ;
		             
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['CGTHDBH'] = $_POST ['CGTHD']; 
			           
			$this->_db->query( $sql,$bind );
	}
	
	
	
	/*
	 * 获取不合格商品的入库类型
	 */
	public function getRKLX(){
		$sql = "SELECT RKLX FROM H01DB012460 "
	             ." WHERE QYBH = :QYBH "
	             ." AND BHGPRKDBH = :BHGPRKDBH ";

		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['BHGPRKDBH'] = $_POST ['YRKDBH']; 

		return $this->_db->fetchOne( $sql, $bind );
	}
	
	
	
	/*
	 * 更新采购结算信息
	 */
	public function updateCGJS(){
		$sql1 = "UPDATE H01DB012310 "
             ." SET HSHJE = HSHJE - :HSHJE "
             ." ,JINE = JINE - :JINE "
             ." ,YFJE = HSHJE - ZHFJE "
             ." WHERE QYBH = :QYBH "
             ." AND CKDBH = :CKDBH " ;

        $bind1 ['HSHJE'] = $_POST ['JEHJ'];
        $bind1 ['JINE'] = $_POST ['BHSHJEHJ'];
		$bind1 ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind1 ['CKDBH'] = $_POST ['FKFSH'] == '4' ? $_POST ['CGTHD'] : $_POST ['YRKDBH'] ; 
		           
		$this->_db->query( $sql1,$bind1 );

			
		$sql2 = "SELECT YFJE FROM H01DB012310 "
             ." WHERE QYBH = :QYBH "
             ." AND CKDBH = :CKDBH " ;

		$bind2 ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind2 ['CKDBH'] = $_POST ['FKFSH'] == '4' ? $_POST ['CGTHD'] : $_POST ['YRKDBH'] ; 
			           
		$YFJE = $this->_db->fetchOne( $sql2,$bind2 );	
		
		if( $YFJE == 0 ){
			
			$sql3 = "UPDATE H01DB012310 "
		             ." SET JSZHT = '1' "
		             ." WHERE QYBH = :QYBH "
		             ." AND CKDBH = :CKDBH " ;

			$bind3 ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind3 ['CKDBH'] = $_POST ['FKFSH'] == '4' ? $_POST ['CGTHD'] : $_POST ['YRKDBH'] ; 
			           
			$this->_db->query( $sql3,$bind3 );
			
		}else{
			
			$sql3 = "UPDATE H01DB012310 "
		             ." SET JSZHT = '2' "
		             ." WHERE QYBH = :QYBH "
		             ." AND CKDBH = :CKDBH " ;

			$bind3 ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind3 ['CKDBH'] = $_POST ['FKFSH'] == '4' ? $_POST ['CGTHD'] : $_POST ['YRKDBH'] ; 

			$this->_db->query( $sql3,$bind3 );
			
		}
	}
	
	
	/*
	 * 重新计算成本
	 */
	public function updateJSCB(){
		
		//循环所有明细行进行
		foreach ( $_POST ["#grid_mingxi"] as $row ) {
			if ($row [$this->idx_SHPBH] == '')continue;
			
			$sql1 = "SELECT CHBJS FROM H01DB012101 "
		             ." WHERE QYBH = :QYBH "
		             ." AND SHPBH = :SHPBH " ;
	
			$bind1 ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind1 ['SHPBH'] = $row[$this->idx_SHPBH]; 

			$CHBJS = $this->_db->fetchOne( $sql1,$bind1 );	
			
			if( $CHBJS == '001' ){            //按商品累计
				
				$sql2 = "UPDATE H01DB012440 "
		             ." SET LJSHL = LJSHL - :THSHL "
		             ." ,LJJE = LJJE - :THJINE "
		             ." ,CHBDJ = LJJE/LJSHL "
		             ." WHERE QYBH = :QYBH "
		             ." AND SHPBH = :SHPBH " ;
		
		        $bind2 ['THSHL'] = $row[$this->idx_SHULIANG];
		        $bind2 ['THJINE'] = (int)$row[$this->idx_SHULIANG] * (float)$row[$this->idx_HSHJ];
				$bind2 ['QYBH'] = $_SESSION ['auth']->qybh;
				$bind2 ['SHPBH'] = $row[$this->idx_SHPBH]; 
				           
				$this->_db->query( $sql2,$bind2 );
				
			}elseif( $CHBJS == '002' ){       //按批号累计
				
				$sql2 = "UPDATE H01DB012441 "
		             ." SET LJSHL = LJSHL - :THSHL "
		             ." ,LJJE = LJJE - :THJINE "
		             ." ,CHBDJ = LJJE/LJSHL "
		             ." WHERE QYBH = :QYBH "
		             ." AND SHPBH = :SHPBH "
		             ." AND PIHAO = :PIHAO";
		
		        $bind2 ['THSHL'] = $row[$this->idx_SHULIANG];
		        $bind2 ['THJINE'] = (int)$row[$this->idx_SHULIANG] * (float)$row[$this->idx_HSHJ];
				$bind2 ['QYBH'] = $_SESSION ['auth']->qybh;
				$bind2 ['SHPBH'] = $row[$this->idx_SHPBH]; 
				$bind2 ['PIHAO'] = $row[$this->idx_PIHAO];

				$this->_db->query( $sql2,$bind2 );
				
			}
		}
	}
	
	
	/*
	 * 更新返利协议的累计数量或累计金额(如果有协议的情况)
	 */
	public function updateFLXY(){
		
		//循环所有明细行
		foreach ( $_POST ["#grid_mingxi"] as $row ) {
			if ($row [$this->idx_SHPBH] == '')continue;

			$sql1 = "SELECT T1.XYBH FROM H01DB012314 T1,H01DB012313 T2 "
	             ." WHERE T1.QYBH = T2.QYBH AND T1.QYBH = :QYBH "
	             ." AND T1.SHPBH = :SHPBH "
	             ." AND T1.XYBH = T2.XYBH "
	             ." AND T2.DWBH = :DWBH "
	             ." AND T1.KSHRQ <= SYSDATE AND SYSDATE <= T1.ZHZHRQ ";

			$bind1 ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind1 ['SHPBH'] = $row[$this->idx_SHPBH]; 
			$bind1 ['DWBH'] = $_POST ['DWBH']; 

			$XYBH = $this->_db->fetchOne( $sql1,$bind1 );


			if( $XYBH != false ){        //有返利协议
				$sql2 = "UPDATE H01DB012314 "
		             ." SET LJSHL = LJSHL - :SHLHJ "
		             ." ,LJJE = LJJE - :JINEHJ "
		             ." WHERE QYBH = :QYBH "
		             ." AND XYBH = :XYBH ";
		
		        $bind2 ['SHLHJ'] = $_POST ['SHULIANGHJ'];
		        $bind2 ['JINEHJ'] = $_POST ['BHSHJEHJ'];
				$bind2 ['QYBH'] = $_SESSION ['auth']->qybh;
				$bind2 ['XYBH'] = $XYBH;
				           
				$this->_db->query( $sql2,$bind2 );
			}
			
		}
		
		$sql3 = "SELECT XYBH FROM H01DB012313 "
             ." WHERE QYBH = :QYBH "
             ." AND XYLX = '0' "
             ." AND DWBH = :DWBH "
             ." AND KSHRQ <= SYSDATE AND SYSDATE <= ZHZHRQ ";

		$bind3 ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind3 ['DWBH'] = $_POST ['DWBH']; 

		$XYBH = $this->_db->fetchOne( $sql3,$bind3 );	

		if( $XYBH != false ){        //有返利协议

			$sql4 = "UPDATE H01DB012313 "
	             ." SET LJSHL = LJSHL - :SHLHJ "
	             ." ,LJJE = LJJE - :JINEHJ "
	             ." WHERE QYBH = :QYBH "
	             ." AND XYBH = :XYBH ";
	
	        $bind4 ['SHLHJ'] = $_POST ['SHULIANGHJ'];
	        $bind4 ['JINEHJ'] = $_POST ['BHSHJEHJ'];
			$bind4 ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind4 ['XYBH'] = $XYBH;

			$this->_db->query( $sql4,$bind4 );
		}
	}


	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck() {
		if ($_POST ["KPRQ"] == "" || //开票日期
            $_POST ["CGTHD"] == "" || //采购退货单
            $_POST ["FAHUOQU"] == "" || //发货区
            $_POST ["#grid_mingxi"] == "") { //明细表格
			return false;
		}
		
		$isHasMingxi = false; //是否存在至少一条明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] != "") {
				$isHasMingxi = true;
				if ($grid [$this->idx_PIHAO] == "" || //批号
					$grid [$this->idx_SHULIANG] == "" || //数量
					$grid [$this->idx_SHULIANG] == "0" ){
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
		
		//获取数据库中该商品冻结信息的数量
		$sql = "SELECT THDZHT,SHHZHT FROM H01DB012308 ".
		             " WHERE QYBH = :QYBH ".
		             " AND CGTHDBH = :CGTHDBH ";
		             
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGTHDBH'] = $_POST ['CGTHD'];                   //入库单编号
		 
		$ZHT = $this->_db->fetchRow( $sql, $bind );             //获取退货单的状态
		
		if ( $ZHT['THDZHT'] != '0' || $ZHT['SHHZHT'] != '1' ){
			return false;          //退货单状态必须是未出库，审核状态为审核通过。不然不可以出库。
		}
		
		return true;
	}
	
	
	/*
	 * 自动完成数据取得
	 */
	public function getAutocompleteData($filter){	
		//检索SQL
		$sql = "SELECT A.CGTHDBH,B.DWMCH FROM H01DB012308 A " 
			   ."LEFT JOIN H01DB012106 B ON A.QYBH=B.QYBH AND A.DWBH=B.DWBH "
		       ."WHERE A.QYBH = :QYBH "      //区域编号
			   ."AND A.QXBZH != 'X'";        //未删除

		if ($filter ['flg'] == '0') {         //未出库
			$sql .= " AND THDZHT = '0'";
		} elseif ($filter ['flg'] == '1') {   //已出库
			$sql .= " AND THDZHT = '1'";
		}

		//快速查找条件
		if ($filter ["searchkey"] != "") {
			$bind ['SEARCHKEY'] = $filter ["searchkey"];
			$sql .= " AND lower(CGTHDBH) LIKE '%'||:SEARCHKEY||'%' ";
		}

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		return $this->_db->fetchAll($sql,$bind);
	}
	
	
	/*
	 * 明细信息商品编号自动完成数据取得
	 */
	public function getshangpinAutocompleteData($filter){	
		//检索SQL
		$sql = "SELECT SHPBH FROM H01DB012309 "
			  ."WHERE QYBH = :QYBH "      //区域编号
			  ."AND CGTHDBH = :CGTHDBH ";  //采购退货单编号  例：CGT10121300001

		//快速查找条件
		if ($filter ["searchkey"] != "") {
			$bind ['SEARCHKEY'] = $filter ["searchkey"];
			$sql .= " AND lower(SHPBH) LIKE '%'||:SEARCHKEY||'%' ";
		}
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGTHDBH'] = $filter ["cgthdh"];
		
		return $this->_db->fetchAll($sql,$bind);
	}
	
	
	/*
	 * 根据采购退货单编号取得采购退货单信息
	 */
	public function getcgthdInfo($filter) {
		//检索SQL
		$sql = "SELECT A.CGTHDBH,A.DWBH,B.DWMCH,A.DHHM,A.DIZHI,to_char(A.KOULV,'fm990.00') AS KOULV,"
			  ."A.YRKDBH,A.SHFZZHSH,A.FKFSH,A.YWYBH,C.YGXM AS YWYXM "
			  ."FROM H01DB012308 A "
			  ."LEFT JOIN H01DB012106 B ON A.QYBH = B.QYBH AND A.DWBH = B.DWBH "
			  ."LEFT JOIN H01DB012113 C ON A.QYBH = C.QYBH AND A.YWYBH = C.YGBH "
			  ."WHERE A.QYBH = :QYBH "         //区域编号
			  ."AND A.CGTHDBH = :CGTHDBH ";    //采购退货单编号

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGTHDBH'] = $filter ['bh'];
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
	
	/*
	 * 根据采购退货单编号取得采购退货单信息
	 */
	public function getcgthdmingxi($filter) {
		//检索SQL
		$sql = "SELECT A.XUHAO,"          //序号
				."A.SHPBH,"      		  //商品编号
				."B.SHPMCH,"     		  //商品名称
				."B.GUIGE,"      		  //规格
				."C.NEIRONG AS BZHDWM,"   //包装单位
				."A.PIHAO,"      		  //批号
				."TO_CHAR(A.SHCHRQ,'yyyy-mm-dd') AS SHCHRQ,"   //生产日期
				."TO_CHAR(A.BZHQZH,'yyyy-mm-dd') AS BZHQZH,"   //保质期至
				."A.SHULIANG,"  		  //数量
				."A.DANJIA,"      	      //单价
				."A.HSHJ,"       		  //含税售价
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
			  ."FROM H01DB012309 A "
			  ."LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "
			  ."LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' "
			  ."WHERE A.QYBH = :QYBH "      //区域编号
			  ."AND A.CGTHDBH = :CGTHDBH "  //采购退货单编号  例：CGT10121300001
			  ."ORDER BY A.XUHAO";

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
		$bind ['CGTHDBH'] = $filter ['bh'];           //采购退货单编号
		
		return $this->_db->fetchAll( $sql, $bind );
	}
	
	
	/*
	 * 商品库位/批号选择画面列表数据取得（xml格式）
	 */
	function getkuweiData($filter) {
		//检索SQL
		$sql = "SELECT B.CKMCH,C.KQMCH,D.KWMCH,A.PIHAO,A.SHULIANG,TO_CHAR(A.BZHQZH, 'YYYY-MM') AS BZHQZH,TO_CHAR(A.SHCHRQ, 'YYYY-MM-DD') AS SHCHRQ,"
				."A.CKBH,A.KQBH,A.KWBH,D.SHFSHKW,DECODE(D.SHFSHKW,'1','散货库位','0','包装库位','库位类型未知') AS SHFSHKWMCH FROM H01DB012404 A "
				."LEFT JOIN H01DB012401 B ON A.QYBH = B.QYBH AND A.CKBH = B.CKBH "
				."LEFT JOIN H01DB012402 C ON A.QYBH = C.QYBH AND A.CKBH = C.CKBH AND A.KQBH = C.KQBH "
				."LEFT JOIN H01DB012403 D ON A.QYBH = D.QYBH AND A.CKBH = D.CKBH AND A.KQBH = D.KQBH AND A.KWBH = D.KWBH "
				."LEFT JOIN H01DB012001 E ON A.QYBH = E.QYBH AND A.BZHDWBH = E.ZIHAOMA AND E.CHLID = 'DW' "
				."WHERE A.QYBH = :QYBH "             //区域编号
				."AND A.SHPBH = :SHPBH "             //商品编号
				."AND A.RKDBH = :RKDBH "             //入库单编号
				."AND A.PIHAO = :PIHAO "             //批号
				."AND A.BZHDWBH = :BZHDWBH "         //包装单位编号
				."AND A.SHULIANG > 0 "               //且数量大于零
				."AND A.ZKZHT = '2'";                //已冻结

		//排序
		$sql .=" ORDER BY SHFSHKW,ZKZHT DESC,PIHAO,RKDBH,CKBH,KQBH,KWBH";
		
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['SHPBH'] = $filter['shpbh'];
		$bind['PIHAO'] = $filter['pihao'];
		$bind['RKDBH'] = $filter['rkdbh'];
		$bind['BZHDWBH'] = $filter ['bzhdwbh'];
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"],$bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"],$bind );
		
		return Common_Tool::createXml ( $recs,false, $totalCount, $filter ["posStart"] );
	}
	
	
	/**
	 * 取得采购退货单状态
	 * @param 	string 	$bh	编号
	 * 
	 * @return 	array 
	 */
	public function getzht($bh) {
		$sql = "SELECT THDZHT FROM H01DB012308 WHERE QYBH = :QYBH AND CGTHDBH = :CGTHDBH";
			
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGTHDBH'] = $bh;
		
		return $this->_db->fetchRow( $sql, $bind );	
	}
	
}
	
	
	