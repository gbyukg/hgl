<?php
/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：       仓库信息(spcl)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2010/12/03
 ***** 更新履历：
 ******************************************************************/

class cc_models_spcl extends Common_Model_Base {
	
	/**
	 * 获取商品拆零后单位信息
	 */
	function getDanwei($SPBH){
		$sql ="SELECT A.BZHDWBH,B.NEIRONG FROM H01DB012117 A LEFT JOIN H01DB012001 B "
				."ON A.QYBH= B.QYBH AND B.CHLID='DW' AND A.BZHDWBH = B.ZIHAOMA "
				."WHERE A.QYBH = :QYBH AND A.SHPBH = :SHPBH AND A.YJBDBZHJBSHL < A.YJBDBZHDQSHL";
		$bind = array( 'QYBH' => $_SESSION ['auth']->qybh, 'SHPBH' => $SPBH);
		$danwei = $this->_db->fetchPairs( $sql, $bind );
		$danwei['0']='- - 请 选 择 - -'; 
		ksort($danwei);
		return $danwei;     
	}
	
	/**
	 * 获取商品的名称和规格
	 */
	function getspxx($spbh){
		$sql ="SELECT SHPMCH,GUIGE,CHANDI,LSHJ FROM H01DB012101 WHERE SHPBH =:SHPBH AND QYBH = :QYBH";
		$bind = array( 'SHPBH' => $spbh, 'QYBH' => $_SESSION ['auth']->qybh );
		$Spxx = $this->_db->fetchRow( $sql, $bind );
		return $Spxx;     
	}
	
	/**
	 * 获取商品在库信息
	 */
	function getzkxx($spbh){
		$sql ="SELECT CKBH,KQBH,KWBH,PIHAO,BZHDWBH,SHULIANG,SHCHRQ,BZHQZH FROM H01DB012404 WHERE SHPBH =:SHPBH AND QYBH = :QYBH";
		$bind = array( 'SHPBH' => $spbh, 'QYBH' => $_SESSION ['auth']->qybh );
		$zkxx = $this->_db->fetchRow( $sql, $bind );
		return $zkxx;     
	}
	
	//根据商品编号判断在库信息表中是否存在相关商品的数据
	function getSpbh($spbh){
		$sql = "SELECT COUNT(*) FROM H01DB012404 WHERE SHPBH =:SHPBH AND QYBH = :QYBH";
		$bind = array( 'SHPBH' => $spbh, 'QYBH' => $_SESSION ['auth']->qybh );
		$temp = $this->_db->fetchOne( $sql, $bind );
		if($temp == 0){
			return false;
		}else{
			return true;
		}
	}
	
	
	/**
	 * 判断拆零后单位是否现有单位的下级单位
	 * @param  string  $shpbh:   商品编号
	 * @param  string  $clhdw：     拆零后单位编号
	 * @return bool
	 */
	function getclhdw($shpbh,$bzhdwbh,$clhdw){
		$sql1 = "SELECT YJBDBZHDQSHL FROM H01DB012117 WHERE BZHDWBH =:BZHDWBH AND SHPBH =:SHPBH AND QYBH = :QYBH";
		$bind1 = array( 'BZHDWBH' => $bzhdwbh, 'SHPBH' => $shpbh, 'QYBH' => $_SESSION ['auth']->qybh );
		$bzhdwjs = $this->_db->fetchOne( $sql1, $bind1 );
		
		$sql2 = "SELECT YJBDBZHDQSHL FROM H01DB012117 WHERE BZHDWBH =:BZHDWBH AND SHPBH =:SHPBH AND QYBH = :QYBH";
		$bind2 = array( 'BZHDWBH' => $clhdw, 'SHPBH' => $shpbh, 'QYBH' => $_SESSION ['auth']->qybh );
		$clhdwjs = $this->_db->fetchOne( $sql2, $bind2 );
		//拆零后单位的【与基本对比之当前数量】 必须 > 单位的【与基本对比之当前数量】
		if($clhdwjs > $bzhdwjs ){
			return true;
		}else{
			return false;
		}
	}
	
	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck() {
		if ($_POST ['BUMENID'] == "" ||          //部门编号
            $_POST ['YWYID'] == "" ||            //业务员编号
            $_POST ['SPBH'] == "" ||             //商品编号
            $_POST ['CKBH'] == "" ||             //仓库编号
            $_POST ['KQBH'] == "" ||             //库区编号  
            $_POST ['KWBH'] == "" ||             //库位编号
            $_POST ['KWSL'] == "" ||             //库位数量
            $_POST ['PIHAO'] == "" ||            //批号
            $_POST ['BZHDWBH'] == "" ||          //包装后单位编号
            $_POST ['CLSL'] == "" ||             //拆零数量
            $_POST ['CLHSL'] == "" ||            //拆零后数量 
            $_POST ['CHLDCKBH'] == "" ||         //拆零到仓库编号
            $_POST ['CHLDKQBH'] == "" ||         //拆零到库区编号
            $_POST ['CHLDKWBH'] == "" ||         //拆零到库位编号
            $_POST ['CLHDWBH'] == "") {          //拆零单位
			return false;
		}else{
			return true;
		}
	}
	
	
	
	/**
	 * 计算拆零后的商品数量
	 * @param  string  $shpbh:   商品编号
	 * @param  string  $bzhdwbh：商品原包装单位编号
	 * @param  string  $clhdw：     拆零后单位编号
	 * @param  string  $clsl：        准备拆零的数量
	 * @return int
	 */
	function getclhsl($shpbh,$bzhdwbh,$clhdw,$clsl){
		$sql1 = "SELECT YJBDBZHJBSHL,YJBDBZHDQSHL FROM H01DB012117 WHERE BZHDWBH =:BZHDWBH AND SHPBH =:SHPBH AND QYBH = :QYBH";
		$bind1 = array( 'BZHDWBH' => $bzhdwbh, 'SHPBH' => $shpbh, 'QYBH' => $_SESSION ['auth']->qybh );
		$bzhdwjs = $this->_db->fetchRow( $sql1, $bind1 );
		
		$sql2 = "SELECT YJBDBZHJBSHL,YJBDBZHDQSHL FROM H01DB012117 WHERE BZHDWBH =:BZHDWBH AND SHPBH =:SHPBH AND QYBH = :QYBH";
		$bind2 = array( 'BZHDWBH' => $clhdw, 'SHPBH' => $shpbh, 'QYBH' => $_SESSION ['auth']->qybh );
		$clhdwjs = $this->_db->fetchRow( $sql2, $bind2 );
		
		$shuliang = $clsl * $bzhdwjs['YJBDBZHJBSHL'] / $bzhdwjs['YJBDBZHDQSHL'] * $clhdwjs['YJBDBZHDQSHL']/$clhdwjs['YJBDBZHJBSHL'];
		
		return $shuliang;
	}
	
	
	
	/**
	 * 获取对应商品在库信息，并按入库单号升序排列
	 * 
	 * @return array();
	 */
	function getzkshpxx(){
		$sql = "SELECT SHULIANG,RKDBH,SHCHRQ,BZHQZH FROM H01DB012404 A "
			." LEFT JOIN H01DB012403 B ON A.QYBH = B.QYBH AND A.CKBH = B.CKBH AND "
			." A.KQBH = B.KQBH AND A.KWBH = B.KWBH AND B.KWZHT = '1' "
			." WHERE A.QYBH = :QYBH AND A.CKBH =:CKBH AND A.KQBH =:KQBH AND A.KWBH =:KWBH AND A.SHULIANG >0 " 
			." AND A.SHPBH =:SHPBH AND A.PIHAO =:PIHAO AND A.ZKZHT =:ZKZHT AND A.BZHDWBH =:BZHDWBH "
			." ORDER BY A.RKDBH ASC  FOR UPDATE WAIT 10 ";  
		
		$data ['QYBH'] = $_SESSION ['auth']->qybh;   //区域编号
		$data ['CKBH'] = $_POST ['CKBH'];            //仓库编号
		$data ['KQBH'] = $_POST ['KQBH'];            //库区编号
		$data ['KWBH'] = $_POST ['KWBH'];            //库位编号
		$data ['SHPBH'] = $_POST ['SPBH'];           //商品编号	
		$data ['PIHAO'] = $_POST ['PIHAO'];          //批号
		$data ['ZKZHT'] = $_POST ['ZKZHT'];          //在库状态
		$data ['BZHDWBH'] = $_POST ['BZHDWBH'];      //单位编号

		$Zkshpxx = $this->_db->fetchAll( $sql, $data );
		return $Zkshpxx;
	}
	
			
	/**
	 * 获取最新商品拆零单据编号
	 */
	function getSpclbh(){
	//判断是否为当天第一单		
		$date = new Zend_Date();
		$SPCLDBH = "CLD".$date->toString("YYMMdd")."00001";
		$sql = "SELECT COUNT(*) FROM H01DB012419 WHERE DJBH =:DJBH AND QYBH = :QYBH";
		$bind = array( 'DJBH' => $SPCLDBH, 'QYBH' => $_SESSION ['auth']->qybh );
		$temp = $this->_db->fetchOne( $sql, $bind );
		if($temp > 0){
			$sql = "SELECT DJBH FROM H01DB012419 WHERE DJBH like 'CLD".$date->toString("YYMMdd")."%' AND QYBH = :QYBH ORDER BY DJBH DESC";
			$bind = array( 'QYBH' => $_SESSION ['auth']->qybh );
			$BH = $this->_db->fetchOne( $sql, $bind );
			$SPCLDBH = str_pad((int)substr($BH, 9, 5)+1 , 5 , "0" , STR_PAD_LEFT);
			$SPCLDBH = "CLD".$date->toString("YYMMdd").$SPCLDBH;
		}
		return $SPCLDBH;     
	}
	
	
	/**
	 * 拆零后的商品在库信息查询-判断库位是否已有同类商品
	 * @param  string  $RKDH：     当前信息入库单号
	 * @return bool
	 */
	function SelectZkspxx($RKDH){
		$sql = "SELECT COUNT(*) FROM H01DB012404 "
			." WHERE QYBH = :QYBH AND CKBH =:CKBH AND KQBH =:KQBH AND KWBH =:KWBH " 
			." AND SHPBH =:SHPBH AND PIHAO =:PIHAO AND ZKZHT =:ZKZHT AND BZHDWBH =:BZHDWBH "
			." AND RKDBH =:RKDBH AND SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD')";  //FOR UPDATE WAIT 10 
		
		$data ['QYBH'] = $_SESSION ['auth']->qybh;  //区域编号
		$data ['CKBH'] = $_POST ['CHLDCKBH'];       //拆零到仓库编号
		$data ['KQBH'] = $_POST ['CHLDKQBH'];       //拆零到库区编号
		$data ['KWBH'] = $_POST ['CHLDKWBH'];       //拆零到库位编号
		$data ['SHPBH'] = $_POST ['SPBH'];          //商品编号
		$data ['PIHAO'] = $_POST ['PIHAO'];         //批号
		$data ['ZKZHT'] = $_POST ['ZKZHT'];         //在库状态
		$data ['BZHDWBH'] = $_POST ['CLHDWBH'];     //拆零后单位编号
		$data ['SHCHRQ'] = $_POST ['SCRQ'];         //生产日期
		$data ['RKDBH'] = $RKDH;                    //入库单编号

		$temp = $this->_db->fetchOne( $sql, $data );

		if($temp == 0){
			return false;    //无商品
		}else{
			return true;     //已有商品
		}
	}

	
	/**
	 * 新增在库商品信息
	 * @param  int     $SL:     拆零后数量
	 * @param  string  $RKDH：     当前信息入库单号
	 * @return bool
	 */
	function insertZkspxx($CLHSL, $RKDH) {
		$data ['QYBH'] = $_SESSION ['auth']->qybh;       //区域编号
		$data ['CKBH'] = $_POST ['CHLDCKBH'];            //拆零到仓库编号
		$data ['KQBH'] = $_POST ['CHLDKQBH'];            //拆零到库区编号
		$data ['KWBH'] = $_POST ['CHLDKWBH'];            //拆零到库位编号
		$data ['SHPBH'] = $_POST ['SPBH'];               //商品编号	
		$data ['PIHAO'] = $_POST ['PIHAO'];              //批号
		$data ['RKDBH'] = $RKDH;                         //入库单编号
		$data ['ZKZHT'] = $_POST ['ZKZHT'];              //在库状态
		$data ['BZHDWBH'] = $_POST ['CLHDWBH'];          //拆零后单位编号
		$data ['ZZHCHKRQ'] = new Zend_Db_Expr(" to_date('9999-12-31 23:59:59' , 'yyyy-mm-dd hh24:mi:ss') ");
		$data ['SHULIANG'] = $CLHSL;                     //拆零后数量
		$data ['SHCHRQ'] = new Zend_Db_Expr ("TO_DATE('".$_POST ['SCRQ']."','YYYY-MM-DD')"); //生产日期
		$data ['BZHQZH'] = new Zend_Db_Expr ("TO_DATE('".$_POST ['BZHQ']."','YYYY-MM')"); //保质期至
		
		$this->_db->insert ( "H01DB012404", $data );	//新增在库商品信息数据
		return true;
	}
	
	
	/**
	 * 更新在库商品信息(增加在库商品数量)
	 * @param  int     $SL:     拆零后所增加的数量
	 * @param  string  $RKDH：     当前信息入库单号
	 * @return bool
	 */
	function updateZkspxx($CLHSL, $RKDH) {
		$sql = "UPDATE H01DB012404 SET SHULIANG = SHULIANG + :SHULIANG "
			." WHERE QYBH = :QYBH AND CKBH =:CKBH AND KQBH =:KQBH AND KWBH =:KWBH " 
			." AND SHPBH =:SHPBH AND PIHAO =:PIHAO AND ZKZHT =:ZKZHT AND BZHDWBH =:BZHDWBH "
			." AND RKDBH =:RKDBH AND SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD')";		

		$data ['SHULIANG'] = $CLHSL;                     //拆零后数量
		$data ['QYBH'] = $_SESSION ['auth']->qybh;       //区域编号
		$data ['CKBH'] = $_POST ['CHLDCKBH'];            //拆零到仓库编号
		$data ['KQBH'] = $_POST ['CHLDKQBH'];            //拆零到库区编号
		$data ['KWBH'] = $_POST ['CHLDKWBH'];            //拆零到库位编号
		$data ['SHPBH'] = $_POST ['SPBH'];               //商品编号	
		$data ['PIHAO'] = $_POST ['PIHAO'];              //批号
		$data ['ZKZHT'] = $_POST ['ZKZHT'];              //在库状态
		$data ['BZHDWBH'] = $_POST ['CLHDWBH'];          //拆零后单位编号
		$data ['SHCHRQ'] = $_POST ['SCRQ'];              //生产日期
		$data ['RKDBH'] = $RKDH;                         //入库单编号
		
		$this->_db->query( $sql, $data );	//***		
		return true;
	}
	
	
	/**
	 * 更新在库商品信息(减少在库商品数量)
	 * @param  int     $SL:     被拆零的数量
	 * @param  string  $RKDH：     当前信息入库单号
	 * @param  string  $flg     0: 剩余拆零数量<处理中商品在库信息的数量   1: >=
	 * @return bool
	 */
	function updateZksp($SL, $RKDH, $flg) {
		$sql = "UPDATE H01DB012404 SET SHULIANG = :SHULIANG "
			.( $flg=="1" ? ", ZZHCHKRQ = SYSDATE " : "" )
			." WHERE QYBH = :QYBH AND CKBH =:CKBH AND KQBH =:KQBH AND KWBH =:KWBH " 
			." AND SHPBH =:SHPBH AND PIHAO =:PIHAO AND ZKZHT =:ZKZHT AND BZHDWBH =:BZHDWBH "
			." AND RKDBH =:RKDBH AND SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD')";	

		$data ['SHULIANG'] = $SL;
		$data ['QYBH'] = $_SESSION ['auth']->qybh;      //区域编号
		$data ['CKBH'] = $_POST ['CKBH'];               //仓库编号
		$data ['KQBH'] = $_POST ['KQBH'];               //库区编号
		$data ['KWBH'] = $_POST ['KWBH'];               //库位编号
		$data ['SHPBH'] = $_POST ['SPBH'];              //商品编号	
		$data ['PIHAO'] = $_POST ['PIHAO'];             //批号
		$data ['ZKZHT'] = $_POST ['ZKZHT'];             //在库状态
		$data ['BZHDWBH'] = $_POST ['BZHDWBH'];         //包装单位编号
		$data ['SHCHRQ'] = $_POST ['SCRQ'];             //生产日期
		$data ['RKDBH'] = $RKDH;                        //入库单编号
		
		$this->_db->query( $sql, $data );	//***		
		return true;
	}
	
	
	/**
	 * 保存商品拆零单信息
	 *
	 * @return bool
	 */
	function insertSpcldxx($SPCLDBH) {
			$data ['QYBH'] = $_SESSION ['auth']->qybh;        //区域编号	
			$data ['DJBH'] = $SPCLDBH;                        //单据编号
			$data ['KPRQ'] = new Zend_Db_Expr ("TO_DATE('".$_POST ['KPRQ']."','YYYY-MM-DD')"); //开票日期
			$data ['YWYBH'] = $_POST ['YWYID'];               //业务员编号
			$data ['BMBH'] = $_POST ['BUMENID'];              //部门编号
			$data ['SHPBH'] = $_POST ['SPBH'];                //商品编号
			$data ['CKBH'] = $_POST ['CKBH'];                 //仓库编号
			$data ['KQBH'] = $_POST ['KQBH'];                 //库区编号
			$data ['KWBH'] = $_POST ['KWBH'];                 //库位编号
			$data ['KWSHL'] = $_POST ['KWSL'];                //库位数量
			$data ['PIHAO'] = $_POST ['PIHAO'];               //批号
			$data ['GUIGE'] = $_POST ['SPGG'];                //商品规格
			$data ['BZHDWBH'] = $_POST ['BZHDWBH'];           //包装单位编号
			$data ['SHCHRQ'] = new Zend_Db_Expr ("TO_DATE('".$_POST ['SCRQ']."','YYYY-MM-DD')"); //生产日期
			$data ['BZHQZH'] = new Zend_Db_Expr ("TO_DATE('".$_POST ['BZHQ']."','YYYY-MM')");    //保质期至
			$data ['CHBDJ'] = $_POST ['CBDJ'];                //成本单价
			$data ['LSHJ'] = $_POST ['LSHJ'];                 //零售价
			$data ['CHLSHL'] = $_POST ['CLSL'];               //拆零数量
			$data ['CHLHSHL'] = $_POST ['CLHSL'];             //拆零后数量
			$data ['CHLDCKBH'] = $_POST ['CHLDCKBH'];         //拆零到仓库编号
			$data ['CHLDKQBH'] = $_POST ['CHLDKQBH'];         //拆零到库区编号
			$data ['CHLDKWBH'] = $_POST ['CHLDKWBH'];         //拆零到库位编号
			$data ['CHLDW'] = $_POST ['CLHDWBH'];             //拆零单位
			$data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" );  //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId;      //操作用户
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			$this->_db->insert ( "H01DB012419", $data );	  //插入商品拆零信息表
			return true;
	}
	

	/**
	 * 保存商品移动履历信息
	 * @param  int     $SL:     拆零数量
	 * @param  string  $ZHYZHL: 转移种类   41:拆零入库 42:拆零出库
	 * @param  string  $RKDH：     当前信息入库单号
	 * @param  string  $SPCLDBH:商品拆零单据号
	 * @param  int     $XUHAO:  序号
	 * @return bool
	 */
	function insertSpydll($SL,$ZHYZHL,$RKDH,$SPCLDBH,$XUHAO) {
		if($ZHYZHL=='41'){
			$Spydlldata ['ZHYZHL'] = $ZHYZHL;              //转移种类
			$Spydlldata ['SHULIANG'] = $SL;
			$Spydlldata ['CKBH'] = $_POST ['CHLDCKBH'];    //拆零到仓库编号
			$Spydlldata ['KQBH'] = $_POST ['CHLDKQBH'];    //拆零到库区编号
			$Spydlldata ['KWBH'] = $_POST ['CHLDKWBH'];    //拆零到库位编号
			$Spydlldata ['BZHDWBH'] = $_POST ['CLHDWBH'];  //拆零后单位编号
		}else{
			$Spydlldata ['ZHYZHL'] = $ZHYZHL;              //转移种类
			$Spydlldata ['SHULIANG'] = $SL * -1 ;
			$Spydlldata ['CKBH'] = $_POST ['CKBH'];        //原仓库编号
			$Spydlldata ['KQBH'] = $_POST ['KQBH'];        //原库区编号
			$Spydlldata ['KWBH'] = $_POST ['KWBH'];        //原库位编号
			$Spydlldata ['BZHDWBH'] = $_POST ['BZHDWBH'];  //包装单位编号
		}
		$Spydlldata ['QYBH'] = $_SESSION ['auth']->qybh;   //区域编号
		$Spydlldata ['SHPBH'] = $_POST ['SPBH'];           //商品编号
		$Spydlldata ['PIHAO'] = $_POST ['PIHAO'];          //批号
		$Spydlldata ['RKDBH'] = $RKDH;                     //入库单号
		$Spydlldata ['YDDH'] = $SPCLDBH;                   //移动单号=商品拆零单据号
		$Spydlldata ['XUHAO'] = $XUHAO;                    //序号
		$Spydlldata ['ZKZHT'] = $_POST ['ZKZHT'];          //在库状态
		$Spydlldata ['SHCHRQ'] = new Zend_Db_Expr ("TO_DATE('".$_POST ['SCRQ']."','YYYY-MM-DD')"); //生产日期
		$Spydlldata ['BZHQZH'] = new Zend_Db_Expr ("TO_DATE('".$_POST ['BZHQ']."','YYYY-MM')");    //保质期至
		$Spydlldata ['CHLSHJ'] = new Zend_Db_Expr ( "SYSDATE" ); //处理时间=系统当前时间
		$Spydlldata ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" );   //变更日期
		$Spydlldata ['BGZH'] = $_SESSION ['auth']->userId;       //操作用户
		$Spydlldata ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$Spydlldata ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$this->_db->insert ( "H01DB012405", $Spydlldata );       //插入商品移动履历信息表
		return true;
	}

}
