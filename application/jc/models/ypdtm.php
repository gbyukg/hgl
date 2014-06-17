<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   一品多条码(ypdtm)
 * 作成者：孙宏志
 * 作成日：2010/12/14
 * 更新履历：
 *********************************/
class jc_models_ypdtm extends Common_Model_Base {
	private $idx_ROWNUM = 0; 	//行号
	private $idx_SHPTM = 1; 	//商品条码
	private $idx_JLGG = 2; 		//计量规格
	private $idx_LSHJG = 3; 	//零售价格
	
	/**
	 * 获取商品的名称和规格等必要数据
	 */
	function getSpxx($spbh){
		$sql ="SELECT  A.SHPMCH,A.CHANDI,B.NEIRONG,A.LSHJ,A.GUIGE,A.SHPTM,A.JLGG ".
		      "FROM H01DB012101 A left join H01DB012001 B ".
		      "ON A.QYBH = B.QYBH and B.ZIHAOMA = A.BZHDWBH AND B.CHLID='DW' ".
		      "WHERE A.QYBH = :QYBH AND A.SHPBH =:SHPBH ";
		$bind = array( 'QYBH' => $_SESSION ['auth']->qybh, 'SHPBH' => $spbh );
		$Spxx = $this->_db->fetchRow( $sql, $bind );
		return $Spxx;     
	}
	
	/**
	 * 检查商品编号是否存在
	 */
	function checkSpbh($spbh){
		$sql = "SELECT COUNT(*) FROM H01DB012101 WHERE SHPBH =:SHPBH AND QYBH = :QYBH";
		$bind = array( 'SHPBH' => $spbh, 'QYBH' => $_SESSION ['auth']->qybh );
		$temp= $this->_db->fetchOne( $sql, $bind );
		if($temp == 0){
			return false;
		}else{
			return true;
		}
	}
	
	/**
	 * 得到一品多条码数据
	 *
	 * @param array $filter
	 * @return string xml
	 */

	public function getGridData($filter) {

		//检索SQL
		$sql = "SELECT SHPTM,JLGG,LSHJ FROM H01DB012102 WHERE QYBH = :QYBH AND SHPBH = :SHPBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['SHPBH'] = $filter ["searchkey"];
		$recs=$this->_db->fetchAll($sql, $bind);
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs);
	}
	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck($shpbh) {
		if ($shpbh == ""){  //商品编号
		  //明细表格
			return false;
		}
		
		$isHasMingxi = false; //是否存在至少一条明细
		foreach ( $_POST ["#grid_duotiaoma"] as $grid ) {
			if ($grid [$this->idx_SHPTM] != "") {
				$isHasMingxi = true;
			}
		}
		
		//一条明细也没有输入
		if (!$isHasMingxi) {
			return false;
		}
		
		return true;
	}
	
	/*
	 * 数据合法性逻辑性验证
	 */
	public function logicCheck($shpbh) {
		
		//商品条码合法性（商品条码在商品表和一品多条码表中是否存在）
		//$grid = $_POST ["#grid_duotiaoma"];
		$rtnlogic['rtncode'] = 0;
		foreach ( $_POST ["#grid_duotiaoma"] as $grid ) {
			$filter ['shptm'] = $grid [$this->idx_SHPTM];
			$filter ['shpbh'] = $shpbh;
			if ($this->getShangpinInfo ( $filter ) || $this->getDuotiaomaInfo ( $filter ))
			{
				$rtnlogic['rtncode'] = 1;
				$rtnlogic['shptm'] = $filter ['shptm'];
				return $rtnlogic;
			} 

		}
		return $rtnlogic;
	}
	
	/**
	 * 检查商品资料表中该商品条码是否存在
	 */
	function getShangpinInfo($filter){
		//检索SQL
		$sql = "SELECT COUNT(*) FROM H01DB012101 WHERE QYBH = :QYBH AND SHPTM = :SHPTM";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['SHPBH'] = $filter ['shpbh'];		
		$bind ['SHPTM'] = $filter ["shptm"];
		$tmp = $this->_db->fetchOne( $sql, $bind );
		if($tmp == 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	/**
	 * 检查多条码信息表中该商品条码是否存在
	 */
	function getDuotiaomaInfo($filter){
		//检索SQL
		$sql = "SELECT COUNT(*) FROM H01DB012102 WHERE QYBH = :QYBH AND SHPBH <> :SHPBH AND SHPTM = :SHPTM";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['SHPBH'] = $filter ['shpbh'];				
		$bind ['SHPTM'] = $filter ["shptm"];
		$tmp = $this->_db->fetchOne( $sql, $bind );
		if($tmp == 0)
			{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	/**
	 * 删除条码信息
	 *
	 * @return bool
	 */
	function delTiaoma($spbh) {
		$sql = "DELETE FROM H01DB012102 WHERE QYBH = :QYBH AND SHPBH = :SHPBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'SHPBH' => $spbh );
		return $this->_db->query ( $sql , $bind );
	}
	
	/*
	 * 一品多条码信息保存
	 */
	public function saveTiaoma($spbh) {
        //循环所有明细行，保存多条码信息
		foreach ( $_POST ["#grid_duotiaoma"] as $grid ) {
			if ($grid [$this->idx_SHPTM] == '')continue;
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; 	//区域编号
			$data ['SHPTM'] = $grid [$this->idx_SHPTM]; //条码信息
			$data ['SHPBH'] = $spbh; 					//商品编号
			$data ['JLGG'] = $grid [$this->idx_JLGG]; 	//计量规格
			$data ['LSHJ'] = $grid [$this->idx_LSHJG]; //零售价格
			//一品多条码信息
			$this->_db->insert ( "H01DB012102", $data );	
		}
	}
}
