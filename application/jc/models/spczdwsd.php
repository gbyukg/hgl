<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   商品拆装单位设定(spczdwsd)
 * 作成者：魏峰
 * 作成日：2011/01/05
 * 更新履历：
 *********************************/
class jc_models_spczdwsd extends Common_Model_Base {
	private $idx_ROWNUM = 0; 	//行号
	private $idx_DWMCH=1;		// 单位名称
	private $idx_JBDW=2;	    // 可否为基本单位
	private $idx_XJDW=3;		// 下级单位
	private $idx_XJDWSHL=4;		// 下级单位数量
	private $idx_KXYF=5;	    // 可销与否	
	private $idx_YJBDBZHJBSHL=6; // 与基本对比之基本数量
    private $idx_YJBDBZHDQSHL=7; // 与基本对比之当前数量
	
	/**
	 * 获取商品的名称和规格等必要数据
	 */
	function getSpxx($spbh){
		$sql ="select SHPmch,chandi,bzhdwbh,BZHDWMCH,JLGG,GUIGE,DBZHCH,DBZHK,DBZHG".
		      " from H01VIEW012101".
		      " WHERE qybh = :QYBH AND SHPBH =:SHPBH ";
		$bind = array( 'QYBH' => $_SESSION ['auth']->qybh, 'SHPBH' => $spbh );
		$Spxx = $this->_db->fetchRow( $sql, $bind );
		return $Spxx;     
	}	

	public function getGridData($filter) {

		//检索SQL
		$sql = "SELECT BZHDWBH DWMCH,CASE WHEN SHFWJBDW = '1' THEN '是' ELSE '否' END SHFWJBDW,XJDW XJDWMCH,XJDWSHL,KXYF KXYF".
		       " FROM H01VIEW012117".		
		       " WHERE QYBH = :QYBH AND SHPBH =:SHPBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['SHPBH'] = $filter ["searchkey"];
		$recs=$this->_db->fetchAll($sql, $bind);
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs);
	}
	
	/**
	 * 获取单位名称的下拉列表
	 */
	function getDwmch()
	{
		$sql = "SELECT ZIHAOMA,NEIRONG".
		       " FROM H01DB012001".
			   " WHERE QYBH = :QYBH AND CHLID = 'DW'";
		$bind = array( 'QYBH' => $_SESSION ['auth']->qybh);
		$Dwmch = $this->_db->fetchAll( $sql, $bind );
		return $Dwmch;     
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
		foreach ( $_POST ["#grid_czhdw"] as $grid ) {
			if ($grid [$this->idx_DWMCH] != "") {
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
	 * 数据合法性逻辑性验证
	 */
	public function logicCheck() {
		
		//商品拆装合法性（对应单位名称，下级单位在常量管理信息表中是否存在）
		$rtnlogic['rtncode'] = 0;
		foreach ( $_POST ["#grid_czhdw"] as $grid ) {
			$filter ['dwbh'] = $grid [$this->idx_DWMCH];
			$filter ['xjdw'] = $grid [$this->idx_XJDW];
			if ($this->getDwbhInfo($filter))
			{
				$rtnlogic['rtncode'] = 1;
				$rtnlogic['dwbh'] = $filter ['dwbh'];
				return $rtnlogic;
			} 
			if ($this->getXjdwInfo($filter))
			{
				$rtnlogic['rtncode'] = 2;
				$rtnlogic['xjdw'] = $filter ['xjdw'];
				return $rtnlogic;
			} 		
		}
		return $rtnlogic;
	} 
	
	/**
	 * 检查常量管理信息表中该单位名称是否存在
	 */
	function getDwbhInfo($filter){
		//检索SQL
		$sql = "SELECT COUNT(*) FROM H01DB012001 WHERE QYBH = :QYBH AND CHLID = 'DW' AND ZIHAOMA = :ZIHAOMA";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;	
		$bind ['ZIHAOMA'] = $filter ["dwbh"];
		$tmp = $this->_db->fetchOne( $sql, $bind );
		if($tmp == 0)
			{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * 检查常量管理信息表中该下级单位是否存在
	 */
	function getXjdwInfo($filter){
		if ($filter ["xjdw"] == ""){
			return false;
		}
		//检索SQL
		$sql = "SELECT COUNT(*) FROM H01DB012001 WHERE QYBH = :QYBH AND CHLID = 'DW' AND ZIHAOMA = :ZIHAOMA";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;			
		$bind ['ZIHAOMA'] = $filter ["xjdw"];
		$tmp = $this->_db->fetchOne( $sql, $bind );
		if($tmp == 0)
			{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * 删除拆装单位信息
	 *
	 * @return bool
	 */
	function delChaizhuang($spbh) {
		$sql = "DELETE FROM H01DB012117 WHERE QYBH = :QYBH AND SHPBH = :SHPBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'SHPBH' => $spbh );
		return $this->_db->query ( $sql , $bind );
	}
	
	/*
	 * 拆装单位信息保存
	 */
	public function saveChaizhuang($spbh) {
        //循环所有明细行，保存拆装单位信息
		foreach ( $_POST ["#grid_czhdw"] as $grid ) {
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; 		            //区域编号
			$data ['SHPBH'] = $spbh; 						            //商品编号
			$data ['BZHDWBH'] = $grid [$this->idx_DWMCH];		        //包装单位编号
			//是否为基本单位
			if ($grid [$this->idx_JBDW] == "是"){
				$data ['SHFWJBDW'] = "1";
			}else{
				$data ['SHFWJBDW'] = "0";
			}
			$data ['XJDW'] = $grid [$this->idx_XJDW];		            //下级单位
			$data ['XJDWSHL'] = $grid [$this->idx_XJDWSHL];	            //下级单位数量		
			$data ['YJBDBZHJBSHL'] = $grid [$this->idx_YJBDBZHJBSHL];   //与基本对比之基本数量
			$data ['YJBDBZHDQSHL'] = $grid [$this->idx_YJBDBZHDQSHL]; 	//与基本对比之当前数量
			$data ['KXYF'] = $grid [$this->idx_KXYF];                   //可销与否
			//拆装单位信息
			$this->_db->insert ( "H01DB012117", $data );	
		}
	}
}
