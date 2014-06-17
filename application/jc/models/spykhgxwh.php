<?php
/*********************************
 * 模块：   基础模块(jc)
 * 机能：    商品与供应商关系维护(SPYKHGXWH)
 * 作成者：姚磊
 * 作成日：2011/1/1
 * 更新履历：
 *********************************/
class jc_models_spykhgxwh extends Common_Model_Base {
	

			private $idx_ROWNUM = 0;// 行号
			private $idx_DWBH = 1;// 单位编号
			private $idx_DWMCH = 2;// 单位名称
			private $idx_JXBZH = 3;// 禁销标志
			private $idx_KOULV=4;//扣率
			
			
			private $idxx_ROWNUM = 0;// 行号
			private $idxx_SHPBH = 1;// 商品编号
			private $idxx_SHPMCH = 2;// 商品名称
			private $idxx_NEIRONG = 3;// 包装单位
			private $idxx_CHANDI=4;//产地
			private $idxx_BZHGG=5;//商品规格
			private $idxx_TYMCH = 6;// 通用名
			private $idxx_JXBZH = 7; // 禁销标志
			private $idxx_KOULV = 8;// 扣率
			
			
	/**
	 *为商品指定客户信息列表
	 *
	 * @param $shpbh
	 * @return 
	 */
		public function getShpbhList($shpbh){
			
			$sql = "SELECT A.SHPBH,A.SHPMCH,A.GUIGE,A.CHANDI,A.TYMCH,C.NEIRONG, DECODE( B.XDBZH,'1','1','0') AS XDBZH ".
			" FROM H01DB012101 A LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH ".  
  			" AND A.BZHDWBH=C.ZIHAOMA  AND  C.CHLID = 'DW' ".
			" LEFT JOIN H01DB012114 B ON A.SHPBH = B.SHPBH AND A.QYBH = B.QYBH ".
  			" WHERE A.QYBH =:QYBH AND A.SHPBH =:SHPBH ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] = $shpbh;
			return $this->_db->fetchRow ( $sql, $bind );
	
			
		}
		
	/**
	 * 得到商品与客户关系维护信息grid
	 *
	 * @param $shpbh
	 * @return 
	 */
		public function getshpinGridData($shpbh){
			
			$sql =" SELECT A.DWBH,B.DWMCH,A.JXBZH,A.KOULV ".
				  " FROM H01DB012114 A LEFT JOIN H01DB012106 B ON ".
				  " A.QYBH =B.QYBH AND A.DWBH = B.DWBH ".
				  " WHERE A.QYBH =:QYBH AND A.SHPBH =:SHPBH AND B.SHFXSH ='1'".
				  " ORDER BY A.DWBH";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] = $shpbh;
			$recs = $this->_db->fetchAll($sql,$bind);
		    return Common_Tool::createXml ( $recs, true );
		}
	/**
	 * 得到客户与商品关系维护信息列表
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
	 * 得到客户与商品关系维护信息grid
	 *
	 * @param $shpbh
	 * @return 
	 */
		public function getdanweiGridData($dwbh){
			
			$sql = " SELECT A.SHPBH,A.SHPMCH,C.NEIRONG,A.CHANDI,A.GUIGE,A.TYMCH,B.JXBZH ,B.KOULV "."
					FROM H01DB012101 A LEFT JOIN H01DB012114 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "."
					LEFT JOIN H01DB012001 C ON A.BZHDWBH = C.ZIHAOMA AND A.QYBH = C.QYBH  AND C.CHLID = 'DW'"."
					WHERE A.QYBH =:QYBH AND B.DWBH =:DWBH  ORDER BY A.SHPBH";
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
		public function delectShpbh($shpbh){
			
			$sql = " DELETE  FROM H01DB012114 WHERE QYBH =:QYBH AND SHPBH =:SHPBH ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] = $shpbh;
			return $this->_db->query( $sql, $bind );
			
		}
		
	/**
	 * 删除商品信息
	 *
	 * @param $shpbh
	 * @return 
	 */
		
		public function delectDwbh($dwbh){
			
			$sql = " DELETE  FROM H01DB012114 WHERE QYBH =:QYBH AND DWBH =:DWBH ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['DWBH'] = $dwbh;
			return $this->_db->query( $sql, $bind );
			
		}
		
		
		
	/**
	 * 保存商品与客户信息维护grid
	 *
	 * @param $shpbh
	 * @return 
	 */
		public  function saveShpbhMingxi($shpbh){
			
		//循环所有明细行，保存商品与供货商明细
		foreach ( $_POST ["#grid_main"] as $grid ) {
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['SHPBH'] = $shpbh; //商品编号
			$data ['DWBH'] = $grid [$this->idx_DWBH]; //单位编号
			$data ['XDBZH'] = ($_POST ['XDBZH'] == null) ? '0' : '1';  //限定标志
			
			$data ['JXBZH'] = $grid [$this->idx_JXBZH]; //禁销标志
			$data ['KOULV'] = $grid [$this->idx_KOULV]; //扣率

			//采购挂单明细表
			$this->_db->insert ( "H01DB012114", $data );
		}
		}
		
	/**
	 * 保存客户与商品信息维护grid
	 *
	 * @param $shpbh
	 * @return 
	 */
		public  function saveDwbhMingxi($dwbh){
			
		//循环所有明细行，保存商品与供货商明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['SHPBH'] = $grid [$this->idxx_SHPBH]; //商品编号
			$data ['DWBH'] = $dwbh; //单位编号
			$data ['XDBZH'] = $_POST['XDBZH']; //限定标志
			$data ['JXBZH'] = $grid [$this->idxx_JXBZH]; //禁销标志
			$data ['KOULV'] = $grid [$this->idxx_KOULV]; //扣率

			//采购挂单明细表
			$this->_db->insert ( "H01DB012114", $data );
		}
		}
		
		/*
		 * 获取商品名称
		 * 
		 */
		public function getshpmcm($filter){
			$sql = " SELECT SHPBH,SHPMCH "."
					FROM H01DB012101 "."
					WHERE QYBH =:QYBH AND SHPBH =:SHPBH ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] =  $filter ['shpbh'];
			return $this->_db->fetchRow ( $sql, $bind );
			
		}
		
			/*
		 * 获取单位名称
		 * 
		 */
	public function getdwmcm($filter){
			$sql = " SELECT DWBH,DWMCH FROM H01DB012106 WHERE QYBH =:QYBH".
				   " AND  DWBH =:DWBH ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['DWBH'] =  $filter ['dwbh'];
			return $this->_db->fetchRow ( $sql, $bind );
			
		}
}