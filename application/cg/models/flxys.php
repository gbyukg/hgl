<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：   返利协议商品(FLXYS)
 * 作成者：侯殊佳 
 * 作成日：2011/05/29
 * 更新履历：

 *********************************/
class cg_models_flxys extends Common_Model_Base {
	private $idx_ROWNUM = 0; // 行号
	private $idx_SHPBH = 1; // 商品编号
	private $idx_SHPMCH = 2; // 商品名称
	private $idx_GUIGE = 3; // 商品规格
	private $idx_BZHDWM = 4; // 包装单位
	private $idx_JLGG = 5; // 计量规格
	private $idx_CHANDI = 6; // 产地
	private $idx_QSRQ = 7; // 起始日期
	private $idx_ZZRQ = 8; // 终止日期 
	private $idx_ZCLJSL = 9; // 政策累计数量
	private $idx_XYDJ = 10; // 协议单价
	private $idx_ZCLJJE = 11; // 政策累计金额
	private $idx_FLJE = 12; // 返利金额
	private $idx_BEIZHU = 13; // 备注
	

	/*
	 * 根据单位编号取得单位信息
	 * 
	 * @param array $filter
	 * @return string array
	 */
	public function getDanweiInfo($filter) {
		//检索SQL
		$sql = "SELECT A.DWBH,A.DWMCH,A.DIZHI,A.DHHM" .
		" FROM H01DB012106 A" . " WHERE A.QYBH = :QYBH " . //区域编号
		" AND A.DWBH = :DWBH" . //单位编号
		" AND A.SHFJH = '1'" . //是否采购
		" AND A.KHZHT = '1'"; //客户状态
		

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
	/*
	 * 取得业务员信息
	 * 
	 * @param string 

	 * @return array
	 */
	public function getywybm (){
		$sql = "SELECT 
				A.YGBH,
				A.YGXM,
				A.SSBM,
				B.BMMCH
				FROM H01DB012113 A
				LEFT JOIN H01DB012112 B ON A.QYBH = B.QYBH AND A.SSBM = B.BMBH 
				WHERE A.QYBH = :QYBH AND A.YGBH = :YGBH";
				$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
				$bind ['YGBH'] = $_SESSION ['auth']->userId; //区域编号
				return $this->_db->fetchRow ($sql,$bind);
	}
	
	/*
	 * 根据商品编号取得商品信息
	 */
	public function getShangpinInfo($filter) {
		
		//检索SQL
		$sql = "SELECT " . "SHPBH," . //商品编号
			   "SHPMCH," . //商品名称
				"GUIGE," . //规格
				"BZHDWMCH," . //包装单位
				"JLGG," . //售价
				"CHANDI " . //含税售价
				" FROM H01VIEW012101 " . 
				" WHERE QYBH = :QYBH " .
				 " AND SHPBH = :SHPBH ";		
		$bind ['SHPBH'] = $filter ['shpbh']; //商品编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		

		return $this->_db->fetchRow ( $sql, $bind );
	}
	
	
	/*
	 * 返利协议保存
	 */
	public function saveFlxyMain($flxybh) {
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['XYBH'] = $flxybh; //协议编号
		$data ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$data ['BMBH'] = $_POST ['BMBH']; //部门编号
		$data ['YWYBH'] = $_POST ['YWYBH']; //业务员编号
		$data ['KSHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['QSRQ'] . "','YYYY-MM-DD')" );//起始日期
		$data ['ZHZHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['ZZRQ'] . "','YYYY-MM-DD')" );//终止日期
		$data ['DWBH'] = $_POST ['DWBH']; //单位编号
		$data ['DIZHI'] = $_POST ['DIZHI']; //地址
		$data ['DHHM'] = $_POST ['DHHM']; //电话
		$data ['XYLX'] = '1';//协议类型
		$data ['BEIZHU'] = $_POST ['BEIZHU']; //备注
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZHUANGTAI'] = '1';//状态 
		
		//采购开票单信息表
		return $this->_db->insert ( "H01DB012313", $data );
	}
	
	/*
	 * 返利协议商品明细保存
	 */
	public function saveFlxyMingxi($flxybh) {
		//循环所有明细行，保存采购订单明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == '')
				continue;
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['XYBH'] = $flxybh; //协议编号
			$data ['SHPBH'] = $grid [$this->idx_SHPBH]; //商品编号
			$data ['KSHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_QSRQ] . "','YYYY-MM-DD')" ); //开始日期	
			$data ['ZHZHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_ZZRQ] . "','YYYY-MM-DD')" );; //终止日期	
			$data ['ZHCLJSHL'] = str_replace(",","||",$grid [$this->idx_ZCLJSL]); //政策累计数量
			$data ['XYDJ'] = $grid [$this->idx_XYDJ]; //协议单价	
			$data ['ZHCLJJE'] = $grid [$this->idx_ZCLJJE]; //政策累计金额
			$data ['FLJE'] = $grid [$this->idx_FLJE]; //返利金额	
			$data ['BEIZHU'] = $grid [$this->idx_BEIZHU]; //备注	
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZHUANGTAI'] = '1';//状态 
			//采购开票单明细表
			$this->_db->insert ( "H01DB012314", $data );
		}
	}
	
	/*
	 * 返利协议基本信息修改
	 */
	
	public function updateFlxyMain($xybh){
					$sql = "UPDATE H01DB012313 SET " .
				   " KSHRQ=TO_DATE(:KSHRQ,'YYYY-MM-DD'),".
					" ZHZHRQ=TO_DATE(:ZHZHRQ,'YYYY-MM-DD'),".
				 //  " ZHZHRQ=:ZHZHRQ,".
			       " DHHM = :DHHM," . 
			       " DIZHI = :DIZHI," . 
			       " BEIZHU = :BEIZHU " .
			       " WHERE QYBH = :QYBH AND XYBH =:XYBH";
			$bind ['KSHRQ'] = $_POST ['QSRQ'];//电话号码
			//$bind ['ZHZHRQ'] = $_POST ['ZZRQ'];//电话号码
			$bind ['ZHZHRQ'] = $_POST ['ZZRQ'];//电话号码
			$bind ['DHHM'] = $_POST ['DHHM'];//电话号码
			$bind ['DIZHI'] = $_POST ['DIZHI']; //地址
			$bind ['BEIZHU'] = $_POST ['BEIZHU']; //备注
			
			$bind ['XYBH'] = $xybh; //员工编号
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$this->_db->query ( $sql, $bind );
			
	}
	
	/*
	 * 返利协议商品信息修改
	 */
		public function updateFlxyMingxi($xybh){
			//从旧协议中提取所有商品编号
		$sql = "SELECT  A.SHPBH FROM H01DB012314 A  LEFT  JOIN H01DB012101 B ON A.QYBH  =B.QYBH AND A.SHPBH = B.SHPBH WHERE A.QYBH = :QYBH  AND A.XYBH = :XYBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'XYBH' => $xybh);
		$shpbhcheck = $this->_db->fetchAll ( $sql, $bind );
		
		//循环对比新旧编号 ，相同的修改，不相同的新建
	$var=0;	
		foreach ( $_POST ["#grid_mingxi"] as $grid )
	{
		$var=0;
		$grid_shpbh = $grid [$this->idx_SHPBH];
		
		for($i=0; $i<count($shpbhcheck); $i++)
		{
			
			
			if($shpbhcheck[$i]['SHPBH'] == $grid_shpbh)
			{
				$var++;
				break;
			}
		}
		if($var >0)
		{
			$sql = "UPDATE H01DB012314 SET " .
			       " KSHRQ = TO_DATE(:KSHRQ ,'YYYY-MM-DD')," .
			       " ZHZHRQ = TO_DATE(:ZHZHRQ ,'YYYY-MM-DD')," . 
			       " ZHCLJSHL = :ZHCLJSHL," . 
			       " XYDJ = :XYDJ," . 
			       " ZHCLJJE = :ZHCLJJE," . 
			       " FLJE = :FLJE," . 
			       " BEIZHU = :BEIZHU " .
			       " WHERE QYBH = :QYBH AND XYBH =:XYBH AND SHPBH =:SHPBH";	
			$bind ['KSHRQ'] =  $grid [$this->idx_QSRQ] ; //开始日期	
			$bind ['ZHZHRQ'] = $grid [$this->idx_ZZRQ] ; //终止日期		
			$bind ['ZHCLJSHL'] = $grid [$this->idx_ZCLJSL]; //政策累计数量
			$bind ['XYDJ'] = $grid [$this->idx_XYDJ]; //协议单价	
			$bind ['ZHCLJJE'] = $grid [$this->idx_ZCLJJE]; //政策累计金额
			$bind ['FLJE'] = $grid [$this->idx_FLJE]; //返利金额	
			$bind ['BEIZHU'] = $grid [$this->idx_BEIZHU]; //备注	
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['XYBH'] = $xybh; //协议编号
			$bind ['SHPBH'] = $grid_shpbh; 
			
		
			$this->_db->query ( $sql, $bind );
			
			$a[$grid_shpbh]=$grid_shpbh;
		}else
		{
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['XYBH'] = $xybh; //协议编号
			$data ['SHPBH'] = $grid [$this->idx_SHPBH]; //商品编号
			$data ['KSHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_QSRQ] . "','YYYY-MM-DD')" ); //开始日期	
			$data ['ZHZHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_ZZRQ] . "','YYYY-MM-DD')" );; //终止日期		
			$data ['ZHCLJSHL'] = str_replace(",","||",$grid [$this->idx_ZCLJSL]); //政策累计数量
			$data ['XYDJ'] = $grid [$this->idx_XYDJ]; //协议单价	
			$data ['ZHCLJJE'] = $grid [$this->idx_ZCLJJE]; //政策累计金额
			$data ['FLJE'] = $grid [$this->idx_FLJE]; //返利金额	
			$data ['BEIZHU'] = $grid [$this->idx_BEIZHU]; //备注	
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZHUANGTAI'] = '1';
			
			//采购开票单明细表
			$this->_db->insert ( "H01DB012314", $data );
			$a[$grid_shpbh]=$grid [$this->idx_SHPBH];
		}
	
	}
	$b="";
	foreach ( $a as $gridshpbh )
	{
		if($b != '')
		{
			$b.=','.$gridshpbh;
		}else
		{
			$b.=$gridshpbh;
		}
		
	}
	//被删除的商品状态设为'X'
	unset($bind);
	$sql ="UPDATE H01DB012314 SET ZHUANGTAI ='X' WHERE SHPBH NOT IN ($b ) AND QYBH=:QYBH AND XYBH = :XYBH";
	$bind ['QYBH'] = $_SESSION ['auth']->qybh;
	$bind ['XYBH'] = $xybh; //协议编号
	$this->_db->query($sql, $bind);
	return true;
		}
		
	public function getMingxiData($xybh) {
		//排序用字段名

		

		//检索SQL
		$sql = "SELECT  A.SHPBH  FROM H01DB012314 A WHERE A.QYBH = :QYBH  AND A.XYBH = :XYBH";
		;
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['XYBH'] = $xybh;
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
}