<?php
/*********************************
 * 模块：   基础模块(jc)
 * 机能：  客户业务员及联系人维护(YWYJLXRWH)
 * 作成者：姚磊
 * 作成日：2011/1/10
 * 更新履历：
 *********************************/
class jc_models_ywyjlxrwh extends Common_Model_Base {
	
			private $idx_ROWNUM = 0;// 行号
			private $idx_YGBH = 1;// 员工编号
			private $idx_YGXM = 2;// 员工名称
			private $idx_SSBM = 3;// 所属部门
			private $idx_ZHJM=4;//助记码
			private $idx_YOUXIANJI=5;//优先级
			private $idx_DHHM = 6;// 电话号码
			private $idx_SHJHM = 7;// 手机号码
			private $idx_DZYJ = 8;// 电子邮件
			
			private $idxx_ROWNUM = 0;// 行号
			private $idxx_LXRXM = 1;// 联系人姓名
			private $idxx_SSBM = 2;// 所属部门
			private $idxx_YOUXIANJI = 3;// 优先级
			private $idxx_DHHM=4;//电话号码
			private $idxx_SHJHM=5;//手机号码
			private $idxx_DZYJ = 6;// 电子邮件
			
			
	/**
	 * 得到商品与供应商关系维护信息列表数据
	 *
	 * @param $shpbh
	 * @return 
	 */
		public function getShpbhList($shpbh){
			
			$sql = "SELECT A.SHPBH,A.SHPMCH,A.GUIGE,A.CHANDI,A.TYMCH,C.NEIRONG ".
			" FROM H01DB012101 A LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH ".  
  			" AND A.BZHDWBH=C.ZIHAOMA AND  C.CHLID = 'DW' WHERE A.QYBH =:QYBH AND A.SHPBH =:SHPBH  ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] = $shpbh;
			return $this->_db->fetchRow ( $sql, $bind );
	
			
		}
		
	/**
	 * 得到商品与供应商关系维护信息grid
	 *
	 * @param $shpbh
	 * @return 
	 */
		public function getshpinGridData($dwbh){
			
			$sql =" SELECT LXRXM,SSBMMCH,YOUXIANJI,DHHM,SHJHM,DZYJ,XUHAO FROM H01DB012111 WHERE QYBH =:QYBH AND  LXRQF ='X' AND DWBH =:DWBH ORDER BY XUHAO";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['DWBH'] = $dwbh;
			$recs = $this->_db->fetchAll($sql,$bind);
		    return Common_Tool::createXml ( $recs, true );
		}
	/**
	 * 得到供应商与商品关系维护信息列表
	 *
	 * @param $shpbh
	 * @return 
	 */
		public function getDanweibhList($dwbh){
			
			$sql = "SELECT DWBH,DWMCH FROM H01DB012106 WHERE QYBH =:QYBH".
				   " AND  DWBH =:DWBH AND SHFXSH ='1'";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['DWBH'] = $dwbh;
			return $this->_db->fetchRow ( $sql, $bind );
		}
	/**
	 * 得到供应商与商品关系维护信息grid
	 *
	 * @param $shpbh
	 * @return 
	 */
		public function getdanweiGridData($dwbh){
			
			$sql = " SELECT A.YGBH,B.YGXM,B.SSBM,B.ZHJM,A.YOUXIANJI,B.DHHM,B.SHJHM,B.DZYJ FROM H01DB012110 A LEFT JOIN H01DB012113 B ".
				   " ON A.QYBH = B.QYBH AND A.YGBH = B.YGBH WHERE A.QYBH =:QYBH AND A.DWBH =:DWBH AND A.YGQF ='X' ORDER BY A.YGBH";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['DWBH'] = $dwbh;
			$recs = $this->_db->fetchAll($sql,$bind);
		    return Common_Tool::createXml ( $recs, true );
		}
	/**
	 * 删除客商信息
	 *
	 * @param $shpbh
	 * @return 
	 */
		public function delectShpbh($dwbh){
			
			$sql = " DELETE  FROM H01DB012110 WHERE QYBH =:QYBH AND DWBH =:DWBH AND YGQF ='X'";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['DWBH'] = $dwbh;
			return $this->_db->query( $sql, $bind );
			
		}
		
	/**
	 * 删除联系人信息
	 *
	 * @param $shpbh
	 * @return 
	 */
		
		public function delectDwbh($dwbh){
			
			$sql = " DELETE  FROM H01DB012111 WHERE QYBH =:QYBH AND DWBH =:DWBH AND LXRQF ='X'";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['DWBH'] = $dwbh;
			return $this->_db->query( $sql, $bind );
			
		}
		
		
		
	/**
	 * 保存商品与供货商信息维护grid
	 *
	 * @param $shpbh
	 * @return 
	 */
		public  function saveShpbhMingxi($dwbh){
			
		//循环所有明细行，保存商品与供货商明细
		foreach ( $_POST ["#grid_main"] as $grid ) {
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['YGBH'] = $grid [$this->idx_YGBH]; //员工编号
			$data ['DWBH'] = $dwbh; //单位编号
			$data ['YOUXIANJI'] = $grid [$this->idx_YOUXIANJI]; //优先级
			$data ['YGQF'] = 'X'; //员工区分

			//采购挂单明细表
			$this->_db->insert ( "H01DB012110", $data );
		}
		}
		
	/**
	 * 保存供货商与商品信息维护grid
	 *
	 * @param $shpbh
	 * @return 
	 */
		public  function saveDwbhMingxi($dwbh){
			$idx = 1; //序号自增	
		//循环所有明细行，保存商品与供货商明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			
			$data ['DWBH'] = $dwbh; //单位编号
			$data ['XUHAO'] = $idx ++; //序号
			$data ['LXRQF'] = 'X'; //联系人区分
			$data ['LXRXM'] = $grid [$this->idxx_LXRXM]; //联系人姓名
		
			$data ['SSBMMCH'] = $grid [$this->idxx_SSBM]; //所属部门
			$data ['DZYJ'] = $grid [$this->idxx_DZYJ]; //电子邮件
			$data ['DHHM'] = $grid [$this->idxx_DHHM]; //电话号码
			$data ['SHJHM'] = $grid [$this->idxx_SHJHM]; //手机号码
			$data ['YOUXIANJI'] = $grid [$this->idxx_YOUXIANJI]; //优先级
			//采购挂单明细表
			$this->_db->insert ( "H01DB012111", $data );
		}
		}
		
	/**
	 * 得到员工信息grid
	 *
	 * @param $shpbh
	 * @return 
	 */
		public function getYuangongInfo($filter){
			
			$sql = "SELECT A.YGBH,A.YGXM,A.SSBM,B.BMMCH AS SSBMMCH,A.DHHM,A.SHJHM,A.DZYJ FROM H01DB012113 A ".
		       "LEFT JOIN H01DB012112 B ON A.QYBH = B.QYBH AND A.SSBM = B.BMBH ". 
		       "WHERE A.QYBH =:QYBH AND A.YGZHT = '1' AND A.YGBH =:YGBH";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['YGBH'] =  $filter ['ygbh'];
			return $this->_db->fetchRow ( $sql, $bind );
		}

}