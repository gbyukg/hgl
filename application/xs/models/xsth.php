<?php
/*********************************
 * 模块：    销售模块(XS)
 * 机能：    销售退货(XSTH)
 * 作成者：孙宏志
 * 作成日：2011/01/06
 * 更新履历：
 * 更新者：苏迅--2011/07/08--赔偿退货机能对应
 *********************************/
class xs_models_xsth extends Common_Model_Base {
	private $idx_ROWNUM = 0; 	//行号
	private $idx_SHPBH = 1; 	//商品编号
	private $idx_SHPMCH = 2; 	//商品名称
	private $idx_GUIGE = 3; 	//规格
	private $idx_BZHDWM = 4; 	//包装单位名
	private $idx_PIHAO = 5; 	//批号
	private $idx_SHCHRQ = 6; 	//生产日期
	private $idx_BZHQZH = 7; 	//保质期至
	private $idx_JLGG = 8; 		//计量规格
	private $idx_BZHSHL = 9; 	//包装数量
	private $idx_LSSHL = 10; 	//零散数量
	private $idx_KTSHL = 11;	//可退数量
	private $idx_SHULIANG = 12; //数量
	private $idx_DANJIA = 13; 	//单价
	private $idx_HSHJ = 14; 	//含税售价
	private $idx_KOULV = 15; 	//扣率
	private $idx_SHUILV = 16; 	//税率
	private $idx_HSHJE = 17; 	//含税金额
	private $idx_JINE = 18; 	//金额
	private $idx_SHUIE = 19; 	//税额
	private $idx_LSHJ = 20; 	//零售价
	private $idx_ZGSHJ = 21; 	//最高售价
	private $idx_SHPTM = 22; 	//商品条码
	private $idx_FLBM = 23; 	//分类编码
	private $idx_PZHWH = 24; 	//批准文号
	private $idx_JIXINGM = 25; 	//剂型
	private $idx_SHCHCHJ = 26; 	//生产厂家
	private $idx_CHANDI = 27; 	//产地
	private $idx_SHFOTC = 28; 	//是否otc
	private $idx_TYMCH = 29;	//通用名
	private $idx_BZHDWBH = 30;	//包装单位编号
	private $idx_XSHDSL= 31;	//销售单数量
	
	private $rkdbh;			//入库单编号
	private $chkdbh;        //出库单编号
	private $newxshdbh;		//新生成的赔偿销售单编号

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
			  ."A.PSYXJ,"                  //配送优先级			  
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
		$sql = "SELECT ".
				  "A.SHPBH,".
				  "B.SHPMCH,".
				  "B.GUIGE,".
				  "C.NEIRONG AS BZHDWM,".
				  "A.PIHAO,".
				  "TO_CHAR(A.SHCHRQ,'yyyy-mm-dd') AS SHCHRQ,".
				  "TO_CHAR(A.BZHQZH,'yyyy-mm-dd') AS BZHQZH,".
				  "B.JLGG,".
				  "A.BZHSHL,".
				  "A.LSSHL,".
				  "'0' AS KTSHUL,".		
				  "A.SHULIANG,".
				  "HGL_DEC(A.DANJIA),".
				  "HGL_DEC(A.HSHJ),".
				  "HGL_DEC(A.KOULV),".
				  "HGL_DEC(B.SHUILV),".
				  "HGL_DEC(A.HSHJE),".
				  "HGL_DEC(A.JINE),".
				  "HGL_DEC(A.SHUIE),".
				  "HGL_DEC(B.LSHJ),".
				  "B.ZGSHJ,".
				  "B.SHPTM,".
				  "B.FLBM,".
				  "B.PZHWH,".
				  "D.NEIRONG AS JIXING,".
				  "B.SHCHCHJ,".
				  "B.CHANDI,".
				  "E.NEIRONG AS OTC,".
				  "B.TYMCH,B.BZHDWBH ".
			      "FROM H01DB012202 A ".
			    "LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH ".
			    "LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' ".
			    "LEFT JOIN H01DB012001 D ON A.QYBH = D.QYBH AND B.JIXING = D.ZIHAOMA AND D.CHLID = 'JX' ".		  
			    "LEFT JOIN H01DB012001 E ON A.QYBH = E.QYBH AND B.SHFOTC = E.ZIHAOMA AND E.CHLID = 'SHFOTC' ".		  
			  	"WHERE A.QYBH = :QYBH ".       //区域编号
			  	"AND A.XSHDBH = :XSHDBH ";    //销售单编号

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
		$bind ['XSHDBH'] = $filter ['bh'];           //采购退货单编号
		//return Common_Tool::createXml($this->_db->fetchAll( $sql, $bind ),true);
		
	   	$xsd = $this->_db->fetchAll( $sql, $bind );
	 	
		for($i=0; $i<count($this->_db->fetchAll( $sql, $bind )); $i++)
		{
    		//检索SQL
			$sqlthd ="select SUM(A.SHULIANG) AS SHULIANG ".
			  "FROM H01DB012207 A,H01DB012206 B , H01DB012201 C ".
			  "WHERE A.QYBH=:QYBH ".
			  "AND  B.QYBH=:QYBH ".
			  "AND C.QYBH=:QYBH ".
			  "AND A.THDBH=B.THDBH ".
			  "AND B.XSHDBH=C.XSHDBH ".
			  "AND B.XSHDBH=:XSHDBH ".
			  "AND A.SHPBH=:SHPBH ".
			  "AND A.PIHAO=:PIHAO ".
			  "AND TO_CHAR(A.SHCHRQ,'yyyy-mm-dd')=:SHCHRQ ".
			  "GROUP BY A.QYBH,A.SHPBH,A.PIHAO,A.SHCHRQ ";
		
			//绑定查询条件
			$bindthd ['QYBH'] = $_SESSION ['auth']->qybh;
			$bindthd ['XSHDBH'] = $filter ['bh'];
			$bindthd ['SHPBH'] = $xsd[$i]['SHPBH'];
			$bindthd ['PIHAO'] = $xsd[$i]['PIHAO'];
			$bindthd ['SHCHRQ'] = $xsd[$i]['SHCHRQ'];
    		
		   	$ktsl=$this->_db->fetchRow( $sqlthd, $bindthd );
		   	
		   	$xsd[$i]['KTSHUL']= ($xsd[$i]['SHULIANG'] - $ktsl['SHULIANG']);
		}
		
		return Common_Tool::createXml($xsd,true);
	}
	
	/*
	 * 根据销售单编号取得退货单信息
	 */
	public function getthdInfo($filter) {
		//检索SQL
		$sql ="select SUM(A.SHULIANG) AS SHULIANG ".
			  "FROM H01DB012207 A,H01DB012206 B , H01DB012201 C ".
			  "WHERE A.QYBH=:QYBH ".
			  "AND  B.QYBH=:QYBH ".
			  "AND C.QYBH=:QYBH ".
			  "AND A.THDBH=B.THDBH ".
			  "AND B.XSHDBH=C.XSHDBH ".
			  "AND B.XSHDBH=:XSHDBH ".
			  "AND A.SHPBH=:SHPBH ".
			  "AND A.PIHAO=:PIHAO ".
			  "AND TO_CHAR(A.SHCHRQ,'yyyy-mm-dd')=:SHCHRQ ".
			  "GROUP BY A.QYBH,A.SHPBH,A.PIHAO,A.SHCHRQ ";
		

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['XSHDBH'] = $filter ['xshdbh'];
		$bind ['SHPBH'] = $filter ['shpbh'];
		$bind ['PIHAO'] = $filter ['pihao'];
		$bind ['SHCHRQ'] = $filter ['shchrq'];
		return $this->_db->fetchRow( $sql, $bind );
	}
	
	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck(){
		if ($_POST ["KPRQ"] == "" ||           //开票日期
            $_POST ["BMBH"] == "" ||           //部门编号
            $_POST ["XSHDH"] == "" ||          //销售单号       
            $_POST ["YWYBH"] == "" ){          //营业员编号
			return false;
		}

		return true;
	}
	
	/**
	 * 退货单信息保存
	 * @param  string  $thdbh:   退货单编号
	 * 
	 * @return bool
	 */
	public function saveThdMain($thdbh) {
		
		$data ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
		$data ['THDBH'] = $thdbh;                    //退货单编号
		$data ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$data ['BMBH'] = $_POST ['BMBH'];             //部门编号
		$data ['KPYBH'] = $_SESSION ['auth']->userId; //开票员编号
		$data ['YWYBH'] = $_POST ['YWYBH'];           //业务员编号
		$data ['DWBH'] = $_POST ['DANWEIBH'];             //单位编号
		$data ['DIZHI'] = $_POST ['DIZHI'];           //地址
		$data ['DHHM'] = $_POST ['DIANHUA'];          //电话
		$data ['SHFZZHSH'] = $_POST ['SHFZZHSH']=='1'?'1':'0';     //是否增值税    0:否(未选中) 1:是(选中)
		$data ['KOULV'] = $_POST ['KOULV'];           //扣率
		$data ['FHQBH'] = $_POST ['FAHUOQUBIANHAO'];  //发货区
		$data ['BEIZHU'] = $_POST ['BEIZHU'];         //备注
		
		//QAQAQAQA
		$data ['SHHZHT'] = '0';           //审核状态
		$data ['SHHR'] = '';              //审核人
		$data ['SHHYJ'] = '';     		  //审核意见
		//$data ['SHHRQ'] = '';             //扣率

		$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' );        //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId;            //变更者
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$data ['FKFSH'] = $_POST ['FUKUANFANGSHI']; 			//付款方式
		$data ['SHFPS'] = $_POST ['SHIFOUPEISONG']; 			//是否配送
		$data ['XSHDBH'] = $_POST ['XSHDH'];
		$data ['PSYXJ'] = $_POST ['PEISONGYOUXIANJI']; 			//配送优先级
		$data ['THDZHT'] = '0';				//退货单状态
		$data ['THLX'] = $_POST['THLX'];						//退货类型---2011/07/07追加
		return $this->_db->insert( "H01DB012206", $data );      //插入退货单信息
	}
	
	/**
	 * 退货单明细保存
	 * @param  string  $thdbh:   退货单编号
	 * 
	 * @return bool
	 */
	public function saveThdMingxi($thdbh) {
			$idx = 1;           //序号自增
        //循环所有明细行，保存退货单明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_XUANZE] == '0')continue;
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh;        //区域编号
			$data ['THDBH'] = $thdbh;                        //退货单编号
			$data ['XUHAO'] = $idx ++;                        //序号
			$data ['SHPBH'] = $grid [$this->idx_SHPBH];       //商品编号

			$data ['PIHAO'] = $grid [$this->idx_PIHAO];       //批号
			//生产日期
			$data ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" );
			//保质期至
			$data ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM-DD')" ); 
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
			$data ['BEIZHU'] = '';     //备注
			$data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' );  //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId;      //变更者
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			$this->_db->insert ( "H01DB012207", $data );	  //出库单明细表	
		}
	}
	
	 /**
	 * 取得开票员信息
	 * @param  string  
	 * 
	 * @return kpymch kpyid
	 */
	public function getKYPInfo() {
				//检索SQL
		$sql = "SELECT YGBH,YGXM FROM H01DB012113 "         
			  ."WHERE QYBH = :QYBH "      //区域编号
			  ."AND YGBH = :YGBH ";   //员工编号

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['YGBH'] = $_SESSION ['auth']->userId; 
		return $this->_db->fetchRow( $sql, $bind );
	}
	
	/**
	 * 赔偿退货处理
	 * @param  string  $thdbh:   退货单编号
	 * @return bool
	 */
	public function savePcthcl($thdbh) {
		//更改原销售单应付应收
		$this->uptJsxx();
		//生成销售退货入库单,及入库单明细
		$this->insThrkd($thdbh);
		//生成赔偿销售单信息
		$this->pcXsdxx($thdbh);
		//生成销售出库单
		$this->insXsckd();
		//生成销售出库单明细，销售退货入库履历，赔偿销售单出库履历,此三项必须按原始入库单操作！！
		$this->exeLvliCkmx();
		//生成赔偿销售单结算信息
		$this->insJsxx();		
	}
	
	/*
	 * 生成销售出库单明细，销售退货入库履历，赔偿销售单出库履历,必须按原始入库单！！
	 */
	public function exeLvliCkmx(){
		$idx_chukumingxi = 1; //出库单明细信息序号	
		$idx_lvli = 1; //在库移动履历
		
		//循环所有画面明细行
		foreach ( $_POST ["#grid_mingxi"] as $row ) {
			//从履历中找此次销售该商品对应的多个入库单号，及每个入库单号对应的总数量
			$recs = $this->getXiaoshouxinxi($row);
			
			//变量:剩余退货数量 = 画面项目：数量
			$shuliang_shengyu = ( int ) $row [$this->idx_SHULIANG];
			
			//循环上面取到的销售信息
			foreach ( $recs as $rec ) {
				$rkdbh_xiaoshou = $rec['RKDBH'];		//变量：入库单号
				$shuliang_xiaoshou = $rec['SHULIANG'];	//变量：销售数量
				$xshdbh = $rec['XSHDBH'];				//变量：销售单编号
				
				//取处理中入库单的该商品的其它退货单对应的退货数量
				$shuliang_qitatuihuo = $this->getQitatuihuo($row,$xshdbh,$rkdbh_xiaoshou);
				
				//没有其他退货
				if($shuliang_qitatuihuo == false){
					$shuliang_qitatuihuo = 0;
				}
				
				//在库更新数量
				$shuliang_update = 0; 
				
				//变量：销售数量  = 变量：其它退货数量 时,循环下一条rec
				if($shuliang_xiaoshou == $shuliang_qitatuihuo) continue;
				
				//判断 变量：剩余退货数量 <= 变量：销售数量 - 变量：其它退货数量 时
				if($shuliang_shengyu <= $shuliang_xiaoshou - $shuliang_qitatuihuo) {
					$shuliang_update = $shuliang_shengyu;
					$shuliang_shengyu = 0;
				}
				//判断 变量：剩余退货数量 > 变量：销售数量 - 变量：其它退货数量 时
				else{
					$shuliang_update = $shuliang_xiaoshou - $shuliang_qitatuihuo;
					$shuliang_shengyu = $shuliang_shengyu - $shuliang_xiaoshou + $shuliang_qitatuihuo;
				}
				
				//库存是更新2次，所以不用操作，只生成两次履历
				//商品移动履历的新生成,一正一负两次				
				$this->insertLvli($row,$shuliang_update,$rkdbh_xiaoshou,$idx_lvli);
				//生成销售出库单明细
				$this->insCkmx($row,$idx_chukumingxi,$rkdbh_xiaoshou,$shuliang_update);
			
				if($shuliang_shengyu == 0) break;
			}
		}
	}
	
	/*
	 * 生成销售出库单明细
	 */
	public function insCkmx($row,$idx_chukumingxi,$rkdbh_xiaoshou,$shuliang_update){
		$chukdmx["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
		$chukdmx["CHKDBH"] = $this->chkdbh; //
		$chukdmx["XUHAO"] =  $idx_chukumingxi++; //
		$chukdmx["SHPBH"] = $row [$this->idx_SHPBH];
		$chukdmx["RKDBH"] = $rkdbh_xiaoshou;
		$chukdmx["CKBH"] = "PCHYCK";
		$chukdmx["KQBH"] = "PCHYKQ";
		$chukdmx["KWBH"] = "PCHYKW";
		$chukdmx["PIHAO"] = $row [$this->idx_PIHAO];
		$chukdmx["SHCHRQ"] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_SHCHRQ]."','YYYY-MM-DD')"); //生产日期
		$chukdmx["BZHQZH"] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_BZHQZH]."','YYYY-MM-DD')"); //保质期至
		$chukdmx["SHULIANG"] = $shuliang_update; //出库数量
		$chukdmx ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
	    $chukdmx ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
    	$chukdmx['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
    	$chukdmx['BGZH'] = $_SESSION ['auth']->userId; //变更者
        $this->_db->insert ( 'H01DB012409', $chukdmx );	
	}
	
	/*
	 * 移动履历做成
	 * 
	 * @param 	array 	$row:明细
	 * 			string	$rkdbh:新生成的退货入库单编号
	 * 			int		$shuliang_update:更新数量
	 * 			string  $rkdbh_xiaoshou:入库单号
	 * 			int		$idx_lvli:移动履历序号
	 * 
	 * @return 	bool	
	 */
	public function insertLvli($row,$shuliang_update,$rkdbh_xiaoshou,$idx_lvli) {
				
		$lvli_rk['QYBH'] = $_SESSION ['auth']->qybh;
		$lvli_rk['CKBH'] = "PCHYCK";//赔偿用公用仓库
		$lvli_rk['KQBH'] = "PCHYKQ";//赔偿用公用库区
		$lvli_rk['KWBH'] = "PCHYKW";//赔偿用公用库位
		$lvli_rk['SHPBH'] = $row [$this->idx_SHPBH];
		$lvli_rk['PIHAO'] = $row [$this->idx_PIHAO];
		$lvli_rk['RKDBH'] = $rkdbh_xiaoshou;	//销售单对应的原始入库单号
		$lvli_rk['YDDH'] = $this->rkdbh;		//移动单号为新生成的入库单号
		$lvli_rk['XUHAO'] = $idx_lvli++;
		if ($row [$this->idx_SHCHRQ] != ""){
			$lvli_rk['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_SHCHRQ]."','YYYY-MM-DD')"); //生产日期;
		}
		if ($row [$this->idx_BZHQZH] != ""){
			$lvli_rk['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_BZHQZH]."','YYYY-MM-DD')"); //保质期至
		}
		$lvli_rk['CHLSHJ'] = new Zend_Db_Expr('SYSDATE');
		$lvli_rk['SHULIANG'] = $shuliang_update;
		$lvli_rk['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$lvli_rk['ZHYZHL'] = '22';						//22：出库取消
		$lvli_rk['ZKZHT'] = '0';
		$lvli_rk['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
		$lvli_rk['BGZH'] = $_SESSION ['auth']->userId; //变更者		
		$lvli_rk ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$lvli_rk ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		
		$this->_db->insert ( 'H01DB012405', $lvli_rk );
		
		$lvli_chk['QYBH'] = $_SESSION ['auth']->qybh;
		$lvli_chk['CKBH'] = "PCHYCK";//赔偿用公用仓库
		$lvli_chk['KQBH'] = "PCHYKQ";//赔偿用公用库区
		$lvli_chk['KWBH'] = "PCHYKW";//赔偿用公用库位
		$lvli_chk['SHPBH'] = $row [$this->idx_SHPBH];
		$lvli_chk['PIHAO'] = $row [$this->idx_PIHAO];
		$lvli_chk['RKDBH'] = $rkdbh_xiaoshou;			//销售单对应的原始入库单号
		$lvli_chk['YDDH'] = $this->chkdbh;				//移动单号为新生成出库单号
		$lvli_chk['XUHAO'] = $idx_lvli++;
		if ($row [$this->idx_SHCHRQ] != ""){
			$lvli_chk['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_SHCHRQ]."','YYYY-MM-DD')"); //生产日期;
		}
		if ($row [$this->idx_BZHQZH] != ""){
			$lvli_chk['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_BZHQZH]."','YYYY-MM-DD')"); //保质期至
		}
		$lvli_chk['CHLSHJ'] = new Zend_Db_Expr('SYSDATE');
		$lvli_chk['SHULIANG'] = $shuliang_update * (-1);
		$lvli_chk['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$lvli_chk['ZHYZHL'] = '22';						//22：出库取消
		$lvli_chk['ZKZHT'] = '0';
		$lvli_chk['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
		$lvli_chk['BGZH'] = $_SESSION ['auth']->userId; //变更者		
		$lvli_chk ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$lvli_chk ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		
		$this->_db->insert ( 'H01DB012405', $lvli_chk );
		
		
	}
	/*
	 * 取处理中入库单的该商品的其它退货单对应的退货数量
	 * 
	 * @param 	array 	$row:明细
	 * 			string  $xshdbh:销售单编号
	 * 			string  $rkdbh_xiaoshou:入库单号
	 * 
	 * @return 	int		其他退货数量
	 *      or  bool	false(没有其他退货)
	 */
	public function getQitatuihuo ($row,$xshdbh,$rkdbh_xiaoshou) {
		
		//抽取处理中入库单的该商品的其它退货单，保存其数量
		$sql_qitatuihuo = "SELECT SUM(C.SHULIANG)"
						. " FROM H01DB012405 C,H01DB012206 A,H01DB012207 B,H01DB012406 D"
						. " WHERE A.QYBH = :QYBH AND B.QYBH = :QYBH AND C.QYBH = :QYBH AND D.QYBH = :QYBH"
						. " AND A.XSHDBH = :XSHDBH AND B.THDBH = A.THDBH AND B.SHPBH = :SHPBH"
						. " AND B.PIHAO = :PIHAO AND C.YDDH = D.RKDBH AND D.CKDBH = A.THDBH"
						. " AND C.RKDBH = :RKDBH AND C.BZHDWBH = :BZHDWBH AND C.SHPBH = B.SHPBH AND C.PIHAO = B.PIHAO "
						. "AND B.SHCHRQ = C.SHCHRQ AND TO_CHAR(C.SHCHRQ,'YYYY-MM-DD') = :SHCHRQ AND TO_CHAR(C.BZHQZH,'YYYY-MM-DD') = :BZHQZH";
						
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['XSHDBH'] = $xshdbh;
		$bind['SHPBH'] = $row [$this->idx_SHPBH];
		$bind['PIHAO'] = $row [$this->idx_PIHAO];
		$bind['RKDBH'] = $rkdbh_xiaoshou;
		$bind['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$bind['SHCHRQ'] = $row [$this->idx_SHCHRQ];
		$bind['BZHQZH'] = $row [$this->idx_BZHQZH];
		
		return $this->_db->fetchOne ( $sql_qitatuihuo, $bind );
		
	}
	
	/*
	 * 从履历中找此次销售该商品对应的多个入库单号，及每个入库单号对应的总数量
	 * --(由于销售商品时可能销售了多个入库单号的该商品，所以入库单号可能为多个，库位可能不同所以数量得求和)
	 * @param 	array 	$row:明细	
	 * @return 	array ：	1)入库单号
	 * 					2)销售单号
	 * 					3)sum(数量)
	 */
	public function getXiaoshouxinxi ($row) {
		
		//从履历中找此次销售该商品对应的多个入库单号，及每个入库单号对应的总数量	
		$sql_xiaoshou = "SELECT RKDBH,"					//入库单号(多条)
					  . "YDDH AS XSHDBH,"					//销售单号(1条)
					  . "ABS(SUM(SHULIANG)) AS SHULIANG"	//SUM出库数量(负值取绝对值)
					  ." FROM H01DB012405"
					  ." WHERE QYBH = :QYBH AND SHPBH = :SHPBH AND PIHAO = :PIHAO AND YDDH = :XSHDBH" 
					  ." AND BZHDWBH = :BZHDWBH AND TO_CHAR(SHCHRQ,'YYYY-MM-DD') = :SHCHRQ AND TO_CHAR(BZHQZH,'YYYY-MM-DD') = :BZHQZH"
					  ." GROUP BY YDDH,RKDBH ORDER BY RKDBH DESC";
						
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['XSHDBH'] = $_POST['XSHDH'];
		$bind['SHPBH'] = $row [$this->idx_SHPBH];
		$bind['PIHAO'] = $row [$this->idx_PIHAO];
		$bind['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$bind['SHCHRQ'] = $row [$this->idx_SHCHRQ];
		$bind['BZHQZH'] = $row [$this->idx_BZHQZH];
	
		//移动履历中该销售退货单对应销售单的入库信息(入库单号及出库总数量)
		return $this->_db->fetchAll( $sql_xiaoshou, $bind );
	}
	
	/*
	 * 生成赔偿销售单结算信息
	 */
	public function insJsxx(){
		$jsd["QYBH"] = $_SESSION ['auth']->qybh;
		$jsd["XSHDBH"] = $this->newxshdbh; //新生成的赔偿销售单编号
		$jsd["JINE"] = $_POST ['JINE_HEJI']; //金额
		$jsd["HSHJE"] = $_POST ['HANSHUIJINE_HEJI'];//含税金额
		$jsd["YSHJE"] = $_POST ['HANSHUIJINE_HEJI'];//应收金额
		$jsd["SHQJE"] = "0"; //收取金额
		$jsd["JSRQ"] = new Zend_Db_Expr ("TO_DATE('1900-01-01','YYYY-MM-DD')"); //结算日期
		$jsd["JIESUANREN"] = ""; //结算人
		$jsd["JSZHT"] = "0"; //结算状态 未结
		//结算单
		$this->_db->insert("H01DB012208",$jsd);		
	}
	
	/*
	 * 生成销售出库单
	 */
	public function insXsckd() {
	
		$this->chkdbh = Common_Tool::getDanhao('CKD'); //出库单编号
		
		$ckd["QYBH"] = $_SESSION ['auth']->qybh;
		$ckd["CHKDBH"] = $this->chkdbh;//新生成的出库单编号
		$ckd["CKDBH"] = $this->newxshdbh; //参考单编号--新的销售单编号
		$ckd ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$ckd ['BMBH'] = $_SESSION ['auth']->bmbh; //部门编号
		$ckd ['KPYBH'] = $_SESSION ['auth']->userId; //开票员编号
		$ckd ['YWYBH'] = $_POST ['YWYBH']; //业务员编号
		$ckd ['DWBH'] = "99999999"; //单位编号--公共名称(司机赔偿专用)
		$ckd ['JINE'] = $_POST ['JINE_HEJI']; //金额
		$ckd ['SHUIE'] = $_POST ['SHUIE_HEJI']; //税额
		$ckd ['HSHJE'] = $_POST ['HANSHUIJINE_HEJI'];//含税金额
		$ckd ['SHULIANG'] = $_POST ['SHULIANG_HEJI']; //数量
		$ckd["CHKLX"] = '1'; //销售出库 
        $ckd ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$ckd ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$ckd['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
		$ckd['BGZH'] = $_SESSION ['auth']->userId; //变更者	
		$this->_db->insert ( 'H01DB012408', $ckd );
	}
	/*
	 * 生成赔偿销售单信息
	 */
	public function pcXsdxx($thdbh) {
		
		$this->newxshdbh = Common_Tool::getDanhao('XSD'); //销售单编号
		
		$xshd ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$xshd ['XSHDBH'] = $this->newxshdbh; //销售单编号
		$xshd ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$xshd ['BMBH'] = $_SESSION ['auth']->bmbh; //部门编号
		$xshd ['KPYBH'] = $_SESSION ['auth']->userId; //开票员编号
		$xshd ['YWYBH'] = $_POST ['YWYBH']; //业务员编号
		$xshd ['DWBH'] = "99999999"; //单位编号--公共名称(赔偿专用)
		$xshd ['BEIZHU'] = $_POST ['BEIZHU']; //备注
		$xshd ['JINE'] = $_POST ['JINE_HEJI']; //金额
		$xshd ['SHUIE'] = $_POST ['SHUIE_HEJI']; //税额
		$xshd ['HSHJE'] = $_POST ['HANSHUIJINE_HEJI'];//含税金额
		$xshd ['SHULIANG'] = $_POST ['SHULIANG_HEJI']; //数量
		$xshd ['PCHTHDBH'] = $thdbh;//赔偿退货单编号
		$xshd ['QXBZH'] = '1';//取消标志
		$xshd ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$xshd ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$xshd ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$xshd ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		//销售订单信息表
		$this->_db->insert ( "H01DB012201", $xshd );
		
		$idx = 1; //明细序号自增
        //循环所有明细行，保存销售订单明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			$xshdmx ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$xshdmx ['XSHDBH'] = $this->newxshdbh; //销售单编号
			$xshdmx ['XUHAO'] = $idx ++; //序号
			$xshdmx ['SHPBH'] = $grid [$this->idx_SHPBH]; //商品编号
			$xshdmx ['PIHAO'] = $grid [$this->idx_PIHAO]; //批号
			$xshdmx ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" ); //生产日期
			$xshdmx ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM-DD')" ); //保质期至
			$xshdmx ['BZHSHL'] = $grid [$this->idx_BZHSHL]; //包装数量
			$xshdmx ['LSSHL'] =  $grid [$this->idx_LSSHL]; //零散数量
			$xshdmx ['SHULIANG'] = $grid [$this->idx_SHULIANG]; //数量
			$xshdmx ['DANJIA'] = $grid [$this->idx_DANJIA]; //单价
			$xshdmx ['HSHJ'] = $grid [$this->idx_HSHJ]; //含税价
			$xshdmx ['KOULV'] = $grid [$this->idx_KOULV]; //扣率
			$xshdmx ['JINE'] = $grid [$this->idx_JINE]; //金额
			$xshdmx ['HSHJE'] = $grid [$this->idx_HSHJE]; //含税金额
			$xshdmx ['SHUIE'] = $grid [$this->idx_SHUIE]; //税额
			$xshdmx ['BEIZHU'] = $grid [$this->idx_BEIZHU]; //备注
			$xshdmx ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
			$xshdmx ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$xshdmx ['ZCHRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //作成日期
			$xshdmx ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			//销售订单明细表
			$this->_db->insert ( "H01DB012202", $xshdmx );	
		}
	}
	
	/*
	 * 生成销售退货入库单
	 * 
	 * @param 	string 	$thdbh:退货单编号
	 * 
	 * @return 	bool	
	 */
	public function insThrkd($thdbh) {
		
		$this->rkdbh = Common_Tool::getDanhao('RKD'); //入库单编号
		$idx_rukumingxi = 1;
				
		$rukudan['QYBH'] = $_SESSION ['auth']->qybh;	
		$rukudan['RKDBH'] = $this->rkdbh;
		$rukudan['CKDBH'] = $thdbh;
		$rukudan['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$rukudan['BMBH'] = $_POST["BMBH"];
		$rukudan['YWYBH'] = $_POST["YWYBH"];
		$rukudan['DWBH'] = $_POST["DWBH"];
		$rukudan['DIZHI'] = $_POST["DIZHI"];
		$rukudan['DHHM'] = $_POST["DHHM"];
		$rukudan['SHFZZHSH'] = ($_POST ['SHFZZHSH'] == null) ? '0' : '1';//是否增值税
		$rukudan['KOULV'] = $_POST["KOULV"];
		$rukudan['BEIZHU'] = $_POST["BEIZHU"];
		$rukudan['RKLX'] = '2';
		$rukudan ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$rukudan ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$rukudan ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$rukudan ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$rukudan ['FKFSH'] = $_POST['FKFSH'];
		
		$this->_db->insert ( "H01DB012406", $rukudan );
		
		foreach ( $_POST ["#grid_mingxi"] as $row ) {
			
			$data['QYBH'] = $_SESSION ['auth']->qybh;
			$data['RKDBH'] = $this->rkdbh;
			$data['XUHAO'] = $idx_rukumingxi++;
			$data['SHPBH'] = $row [$this->idx_SHPBH];
			$data['BZHSHL'] = $row [$this->idx_BZHSHL];
			$data['LSSHL'] = $row [$this->idx_LSSHL];
			$data['SHULIANG'] = $row [$this->idx_SHULIANG];
			$data['DANJIA'] = $row [$this->idx_DANJIA];
			$data['HSHJ'] = $row [$this->idx_HSHJ];
			$data['KOULV'] = $row [$this->idx_KOULV];
			$data['JINE'] = $row [$this->idx_JINE];
			$data['HSHJE'] = $row [$this->idx_HSHJE];
			$data['SHUIE'] = $row [$this->idx_SHUIE];
			$data['BEIZHU'] = $row [$this->idx_BEIZHU];
			$data['PIHAO'] = $row [$this->idx_PIHAO];
			if ($row [$this->idx_SHCHRQ] != ""){
				$data['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" );
			}
			if ($row [$this->idx_BZHQZH] != ""){
				$data['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_BZHQZH] . "','YYYY-MM-DD')" );
			}
			$data['CKBH'] = "PCHYCK";
			$data['KQBH'] = "PCHYKQ";
			$data['KWBH'] = "PCHYKW";
			$data['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
			$data['BGZH'] = $_SESSION ['auth']->userId; //变更者	
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			$this->_db->insert ( "H01DB012407", $data );
		}
	}
	
	/*
	 * 更改原销售单应付应收
	 */
	public function uptJsxx() {
		
		$sql_update = "UPDATE H01DB012208"
					. " SET JINE = JINE - :JINE,"
					. "HSHJE = HSHJE - :HSHJE,"
					. "YSHJE = YSHJE - :HSHJE"
					. " WHERE QYBH = :QYBH AND XSHDBH = :XSHDBH";
		
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['JINE'] = $_POST['JINE_HEJI'];
		$bind['HSHJE'] = $_POST['HANSHUIJINE_HEJI'];
		$bind['XSHDBH'] = $_POST ['XSHDH'];
			
		$this->_db->query ( $sql_update,$bind );
	}
	
	
}	