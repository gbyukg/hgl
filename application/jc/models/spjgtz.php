<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   商品价格调整(spjgtz)
 * 作成者：孙宏志
 * 作成日：2011/1/4
 * 更新履历：
 *********************************/
class jc_models_spjgtz extends Common_Model_Base {
	private $idx_ROWNUM = 0; 	// 行号
	private $idx_SHPBH = 1; 	// 商品编号
	private $idx_SHPMCH = 2; 	// 商品名称
	private $idx_SHPGG = 3; 	// 商品规格
	private $idx_DANWEI = 4;	// 单位
	private $idx_JINJIA = 5;	// 进价
	private $idx_HSHJJ = 6; 	// 含税进价
	private $idx_SHOUJIA = 7;	// 售价
	private $idx_HSHSHJ = 8;	// 含税售价
	private $idx_LSHJ = 9; 		// 零售价
	private $idx_SHILV = 10;	// 税率
	private $idx_CHANDI = 11;	// 产地
	
	/**
	 * 获取商品的名称和规格等必要数据
	 */
	function getSpxx($shpbh){
		$sql ="SELECT A.SHPBH,A.SHPMCH,A.GUIGE,B.NEIRONG,A.JINJIA,A.HSHJJ,A.SHOUJIA,A.HSHSHJ,A.LSHJ,A.SHUILV,A.CHANDI ".
		      "FROM H01DB012101 A left join H01DB012001 B ".
		      "ON A.QYBH = B.QYBH and B.ZIHAOMA = A.BZHDWBH AND B.CHLID='DW' ".
		      "WHERE A.QYBH = :QYBH AND A.SHPBH =:SHPBH ";
		$bind = array( 'QYBH' => $_SESSION ['auth']->qybh, 'SHPBH' => $shpbh );
		$Spxx = $this->_db->fetchRow( $sql, $bind );
		return $Spxx;     
	}
	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck() {

		$isHasMingxi = false; //是否存在至少一条明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] != "") {
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
	 * 商品调价信息保存
	 */
	public function saveTiaojia() {
        //循环所有明细行，保存商品调价信息
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			//商品编号，商品价格信息取得
			$data ['shpbh'] = $grid [$this->idx_SHPBH];		//商品编号
			$data ['jinjia'] = $grid [$this->idx_JINJIA];	//进价
			$data ['hshjj'] = $grid [$this->idx_HSHJJ];		//含税进价
			$data ['shoujia'] = $grid [$this->idx_SHOUJIA];	//售价
			$data ['hshshj'] = $grid [$this->idx_HSHSHJ];	//含税售价
			$data ['lshj'] = $grid [$this->idx_LSHJ];		//零售价
			
			//更新商品价格信息
			$sql_tiaoma = "UPDATE H01DB012101 ".
			             "SET JINJIA = :JINJIA, " .		//进价
						 "HSHJJ = :HSHJJ, ".			//含税进价
						 "SHOUJIA = :SHOUJIA, ".		//售价
						 "HSHSHJ = :HSHSHJ, ".			//含税售价
						 "LSHJ = :LSHJ, ".				//零售价
						 "BGZH = :BGZH, ".				//变更者
						 "BGRQ = SYSDATE ".				//变更日期
			             " WHERE QYBH = :QYBH ".		
			             " AND SHPBH = :SHPBH ";
			
			$bind ['JINJIA'] = $data ['jinjia']; 
			$bind ['HSHJJ'] = $data ['hshjj']; 
			$bind ['SHOUJIA'] = $data ['shoujia']; 			
			$bind ['HSHSHJ'] = $data ['hshshj']; 			
			$bind ['LSHJ'] = $data ['lshj']; 
			$bind ['BGZH'] = $_SESSION ['auth']->userId;
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] = $data ['shpbh']; 		
			$this->_db->query ( $sql_tiaoma,$bind );
		}
	}
}
