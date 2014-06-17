<?php
/*********************************
 * 模块：   基础模块(jc)
 * 机能：    商品与供应商关系维护(SPYGYSGXWH)
 * 作成者：姚磊
 * 作成日：2011/1/1
 * 更新履历：
 *********************************/
class jc_models_spygysgxwh extends Common_Model_Base {
	
			private $idx_ROWNUM = 0;// 行号
			private $idx_DWBH = 1;// 单位编号
			private $idx_DWMCH = 2;// 单位名称
			private $idx_YOUXIANJI = 3;// 优先级
			private $idx_KOULV=4;//扣率
			private $idx_ZHCHCHCHJ=5;//正常出厂价
			private $idx_ZHXDJ = 6;// 执行单位
	
			private $idxx_ROWNUM = 0;// 行号
			private $idxx_SHPBH = 1;// 商品编号
			private $idxx_SHPMCH = 2;// 商品名称
			private $idxx_NEIRONG = 3;// 包装单位
			private $idxx_CHANDI=4;//产地
			private $idxx_BZHGG=5;//商品规格
			private $idxx_TYMCH = 6;// 通用名
			private $idxx_YOUXIANJI = 7;// 优先级
			private $idxx_KOULV = 8;// 扣率
			private $idxx_ZHXDJ = 9;// 执行单价
			private $idxx_ZHCHCHCHJ = 10;// 正常出厂价
			
			
	/**
	 * 得到商品与供应商关系维护信息列表数据
	 *
	 * @param $shpbh
	 * @return 
	 */
		public function getShpbhList($shpbh){
			
			$sql = "SELECT A.SHPBH,A.SHPMCH,A.GUIGE,A.CHANDI,A.TYMCH,C.NEIRONG ".
			" FROM H01DB012101 A LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH  AND  C.CHLID = 'DW'".  
  			" AND A.BZHDWBH=C.ZIHAOMA  WHERE A.QYBH =:QYBH AND A.SHPBH =:SHPBH ";
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
		public function getshpinGridData($filter,$shpbh){
			$fields = array ("", "A.DWBH", "B.DWMCH", "A.YOUXIANJI","A.KOULV","A.ZHCHCHCHJ","A.ZHXDJ" ); 
			$sql =" SELECT A.DWBH,B.DWMCH,A.YOUXIANJI,A.KOULV,A.ZHCHCHCHJ,A.ZHXDJ ".
				  " FROM H01DB012103 A LEFT JOIN H01DB012106 B ON ".
				  " A.QYBH =B.QYBH AND A.DWBH = B.DWBH ".
				  " WHERE A.QYBH =:QYBH AND A.SHPBH =:SHPBH AND B.SHFJH ='1'";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] = $shpbh;
		$sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 项目最后添加主键
		$sql .=",A.DWBH ";	
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
			
		return Common_Tool::createXml ( $recs, true,$totalCount, $filter ["posStart"] );	
		}
	/**
	 * 得到供应商与商品关系维护信息列表
	 *
	 * @param $shpbh
	 * @return 
	 */
		public function getDanweibhList($dwbh){
			
			$sql = "SELECT DWBH,DWMCH FROM H01DB012106 WHERE QYBH =:QYBH".
				   " AND  DWBH =:DWBH AND SHFJH ='1'";
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
			
			$sql = " SELECT A.SHPBH,A.SHPMCH,C.NEIRONG,A.CHANDI,A.GUIGE,A.TYMCH,B.YOUXIANJI,B.KOULV,B.ZHCHCHCHJ,B.ZHXDJ "."
					FROM H01DB012101 A LEFT JOIN H01DB012103 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "."
					LEFT JOIN H01DB012001 C ON A.BZHDWBH = C.ZIHAOMA AND A.QYBH = C.QYBH  AND C.CHLID = 'DW' "."
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
			
			$sql = " DELETE  FROM H01DB012103 WHERE QYBH =:QYBH AND SHPBH =:SHPBH ";
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
			
			$sql = " DELETE  FROM H01DB012103 WHERE QYBH =:QYBH AND DWBH =:DWBH ";
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
		public  function saveShpbhMingxi($shpbh){
			
		//循环所有明细行，保存商品与供货商明细
		foreach ( $_POST ["#grid_main"] as $grid ) {
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['SHPBH'] = $shpbh; //商品编号
			$data ['DWBH'] = $grid [$this->idx_DWBH]; //单位编号
			$data ['YOUXIANJI'] = $grid [$this->idx_YOUXIANJI]; //优先级
			$data ['KOULV'] = $grid [$this->idx_KOULV]; //扣率
			$data ['ZHCHCHCHJ'] = $grid [$this->idx_ZHCHCHCHJ]; //正常出厂价
			$data ['ZHXDJ'] = $grid [$this->idx_ZHXDJ]; //执行单价
			
			$this->_db->insert ( "H01DB012103", $data );
		}
		}
		
	/**
	 * 保存供货商与商品信息维护grid
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
			$data ['YOUXIANJI'] = $grid [$this->idxx_YOUXIANJI]; //优先级
			$data ['KOULV'] = $grid [$this->idxx_KOULV]; //扣率
			$data ['ZHCHCHCHJ'] = $grid [$this->idxx_ZHCHCHCHJ]; //正常出厂价
			$data ['ZHXDJ'] = $grid [$this->idxx_ZHXDJ]; //执行单价
			
			$this->_db->insert ( "H01DB012103", $data );
		}
		}
		
		/*
		 * 获取商品名称
		 * 
		 */
		public function getshpmcm($filter){
			$sql = "SELECT A.SHPBH,A.SHPMCH,A.GUIGE,A.CHANDI,A.TYMCH,C.NEIRONG ".
			" FROM H01DB012101 A LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH  AND  C.CHLID = 'DW' ".  
  			" AND A.BZHDWBH=C.ZIHAOMA  WHERE A.QYBH =:QYBH AND A.SHPBH =:SHPBH ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] =  $filter ['shpbh'];
			return $this->_db->fetchRow ( $sql, $bind );
			
		}
		
			/*
		 * 获取单位名称
		 * 
		 */
	public function getdwmcm($filter){
			$sql = " SELECT DWBH,DWMCH,KOULV FROM H01DB012106 WHERE QYBH =:QYBH".
				   " AND  DWBH =:DWBH ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['DWBH'] =  $filter ['dwbh'];
			return $this->_db->fetchRow ( $sql, $bind );
			
		}
		
		
		
}