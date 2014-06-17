<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   商品编码修改(spbmxg)
 * 作成者：孙宏志
 * 作成日：2010/12/30
 * 更新履历：
 *********************************/
class jc_models_spbmxg extends Common_Model_Base {
	private $idx_ROWNUM = 0; 	//行号
	private $idx_SHPBH = 1; 	//商品编号
	private $idx_SHPMCH = 2; 	//商品名称
	private $idx_SHPGG = 3; 	//商品规格
	private $idx_CHANDI=4;		// 产地
	private $idx_SHPTM=5;		// 商品条码

	/**
	 * 获取商品的名称和规格等必要数据
	 */
	function getSpxx($shpbh){
		$sql ="SELECT SHPBH,SHPMCH,GUIGE,CHANDI,SHPTM ".
		      "FROM H01DB012101 ".
		      "WHERE QYBH = :QYBH AND SHPBH =:SHPBH ";
		$bind = array( 'QYBH' => $_SESSION ['auth']->qybh, 'SHPBH' => $shpbh );
		$Spxx = $this->_db->fetchRow( $sql, $bind );
		return $Spxx;     
	}
	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck() {

		$isHasMingxi = true; //是否存在至少一条明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPTM] == "") {
				$isHasMingxi = false;
			}
		}
		return $isHasMingxi;
	}
	
	/*
	 * 数据合法性逻辑性验证
	 */
	public function logicCheck() {
		
		//一品多价合法性（对应计量单位在商品表和一品多价表中是否存在）
		//$grid = $_POST ["#grid_duotiaoma"];
		$rtnlogic['rtncode'] = 0;
		foreach ( $_POST ["#grid_mingxi"] as $grid ){
			$filter ['shptm'] = $grid [$this->idx_SHPTM];
			$filter ['shpbh'] = $grid [$this->idx_SHPBH];
			if ($this->getShangpinInfo($filter) || $this->getDuotiaomaInfo($filter))
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
		$sql = "SELECT COUNT(*) FROM H01DB012101 WHERE QYBH = :QYBH AND SHPTM = :SHPTM AND SHPBH <> :SHPBH ";

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['SHPTM'] = $filter ['shptm'];		
		$bind ['SHPBH'] = $filter ['shpbh'];		
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
	 * 检查一品多条码表中该商品条码是否存在
	 */
	function getDuotiaomaInfo($filter){
		//检索SQL
		$sql = "SELECT COUNT(*) FROM H01DB012102 WHERE QYBH = :QYBH AND SHPTM = :SHPTM";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['SHPTM'] = $filter ['shptm'];				

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
	
	/*
	 * 商品条码信息保存
	 */
	public function saveTiaoma() {
        //循环所有明细行，保存多条码信息
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			//商品编号，商品条码取得
			$filter ['shptm'] = $grid [$this->idx_SHPTM];
			$filter ['shpbh'] = $grid [$this->idx_SHPBH];
			
			//更新商品条码信息
			$sql_tiaoma = "UPDATE H01DB012101 ".
			             "SET SHPTM = :SHPTM " .
			             " WHERE QYBH = :QYBH ".
			             " AND SHPBH = :SHPBH ";
			
			$bind ['SHPTM'] = $filter ['shptm']; 			             
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] = $filter ['shpbh']; 		
			$this->_db->query ( $sql_tiaoma,$bind );
		}
	}
}
