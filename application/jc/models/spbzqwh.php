<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   商品保质期维护(spbzqwh)
 * 作成者：梁兆新
 * 作成日：2011/1/5
 * 更新履历：
 *********************************/
class jc_models_spbzqwh extends Common_Model_Base {
	
	private $idx_ROWNUM=0;		// 行号
	private $idx_SHPBH=1;        //商品编号
	private $idx_SHPMCH=2;       //商品名称
	private $idx_BZHQFSH=3;        //商品保质期方式
	private $idx_BZHQTSH=4;       //商品保质期天数
	private $idx_YJTSH=5;        //保质期预警天数
	/**
	 * 获取商品的名称和规格等必要数据
	 */
	function getSpxx($shpbh){
		$sql ="SELECT SHPBH,SHPMCH,BZHQFSH,BZHQYSH,YJYSH FROM H01DB012101 ".
		      "WHERE QYBH = :QYBH AND SHPBH =:SHPBH ";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'SHPBH' => $shpbh );
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
	/*
	 * 必须输入项验证
	 */
	public function inputCheck() {
		$isHasMingxi = false; //是否存在至少一条明细
		foreach($_POST["#grid_bzqwh"] as $grid ){
			if (isset($grid [$this->idx_SHPBH]) && !empty($grid [$this->idx_SHPBH])) {
				$isHasMingxi = true;
			}
		}
		
		//一条明细也没有输入
		if (! $isHasMingxi) {
			return false;
		}
		
		return true;
	}
	
	/*
	 * 数据合法性（包含商品编号是否存在）
	 */
	public function logicCheck() {
		//检测数据在商品保质期中是否合法
		$rtnlogic = '0';
		foreach ( $_POST ["#grid_bzqwh"] as $grid ) {
//			$filter ['bzhqtsh'] = $grid [$this->idx_BZHQTSH];
//			$filter ['yjtsh'] = $grid [$this->idx_YJTSH];
			$sql ="SELECT SHPBH FROM H01DB012101 WHERE QYBH = :QYBH AND SHPBH =:SHPBH ";
			
			$bind = array( 'QYBH' => $_SESSION ['auth']->qybh, 'SHPBH' => $grid [$this->idx_SHPBH] );
			
			$tmp = $this->_db->fetchOne( $sql, $bind );
			
			if($tmp != FALSE )
			{
				$rtnlogic = '1';
			}
		}
		return $rtnlogic;
	}
	

	
	/*
	 * 商品保质期信息保存
	 */
	public function savebz() {
        //循环所有明细行，保存多商品保质期信息
		foreach ( $_POST ["#grid_bzqwh"] as $grid ) {
			//商品编号，商品条码取得
			$filter ['bzhqfsh'] = $grid [$this->idx_BZHQFSH];
			$filter ['bzhqtsh'] = $grid [$this->idx_BZHQTSH];
			$filter ['yjtsh'] = $grid [$this->idx_YJTSH];
			$filter ['shpbh'] = $grid [$this->idx_SHPBH];
			
			//更新商品保质期信息
			$sql_tiaoma = "UPDATE H01DB012101 ".
			             "SET BZHQFSH = :BZHQFSH " .
						 ", BZHQYSH = :BZHQYSH " .
						 ", YJYSH = :YJYSH " .
						 ", BGZH = :BGZH " .
						 ", BGRQ = TO_DATE(:BGRQ,'YYYY-MM-DD') " .
			             " WHERE QYBH = :QYBH ".
			             " AND SHPBH = :SHPBH ";
			
			$bind ['BZHQFSH'] = $filter ['bzhqfsh'];
			$bind ['BZHQFSH'] = $filter ['bzhqfsh'];
			$bind ['BZHQYSH'] = $filter ['bzhqtsh'];
			$bind ['YJYSH'] = $filter ['yjtsh'];
			$bind ['BGZH'] = $_SESSION ['auth']->userId;
			$bind ['BGRQ'] = date('Y-m-d'); //变更日期
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] = $filter ['shpbh'];
			
			$this->_db->query ( $sql_tiaoma,$bind );
		}
	}
}
