<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   一品多价(ypdj)
 * 作成者：孙宏志
 * 作成日：2010/12/24
 * 更新履历：
 *********************************/
class jc_models_ypdj extends Common_Model_Base {
	private $idx_ROWNUM = 0; 	//行号
	private $idx_JLDW = 1; 		//计量单位
	private $idx_JLGG = 2; 		//计量规格
	private $idx_BHSHJ = 3; 	//不含税价格
	private $idx_HSHJ=4;		// 含税价格
	private $idx_KHDJ=5;		// 客户等级
	private $idx_BEIZHU=6;		// 备注
	
	/**
	 * 获取商品的名称和规格等必要数据
	 */
	function getSpxx($spbh){
		$sql ="SELECT SHPMCH,GUIGE,CHANDI,SHUILV ".
		      "FROM H01DB012101 ".
		      "WHERE QYBH = :QYBH AND SHPBH =:SHPBH ";
		$bind = array( 'QYBH' => $_SESSION ['auth']->qybh, 'SHPBH' => $spbh );
		$Spxx = $this->_db->fetchRow( $sql, $bind );
		return $Spxx;     
	}
	
	/**
	 * 获取商品的名称和规格等必要数据
	 */
	function getJldw($spbh)
	{
		//商品编号的默认包装单位取得
		$sqlsp = "SELECT A.BZHDWBH,B.NEIRONG ".
		       "FROM H01DB012101 A left join H01DB012001 B ".
			   "ON A.QYBH = B.QYBH and B.ZIHAOMA = A.BZHDWBH AND B.CHLID='DW' ".
		       "WHERE A.QYBH = :QYBH AND A.SHPBH =:SHPBH ";
		$bindsp = array( 'QYBH' => $_SESSION ['auth']->qybh, 'SHPBH' => $spbh );
		$Jldwsp = $this->_db->fetchAll( $sqlsp, $bindsp );
		
		$sql = "SELECT A.BZHDWBH,B.NEIRONG ".
		       "FROM H01DB012117 A left join H01DB012001 B ".
			   "ON A.QYBH = B.QYBH and B.ZIHAOMA = A.BZHDWBH AND B.CHLID='DW' ".
		       "WHERE A.QYBH = :QYBH AND A.SHPBH =:SHPBH AND A.YJBDBZHJBSHL <= A.YJBDBZHDQSHL ".
		       "ORDER BY A.YJBDBZHDQSHL ";
		$bind = array( 'QYBH' => $_SESSION ['auth']->qybh, 'SHPBH' => $spbh );
		$Jldw = $this->_db->fetchAll( $sql, $bind );
		if(count($Jldw)==0)
		{
			return $Jldwsp;
		}
		else 
		{
			for($i=0; $i<count($Jldw); $i++)
			{
			   	if($Jldw[$i]['BZHDWBH']==$Jldwsp[0]['BZHDWBH'])
			   	{
			   		return $Jldw;
			   	}
			}
			$Jldw[$i]['BZHDWBH']=$Jldwsp[0]['BZHDWBH'];
			$Jldw[$i]['NEIRONG']=$Jldwsp[0]['NEIRONG'];	
			return $Jldw;
		}
/*		//数组合并
		$resultdw = array_merge($Jldwsp,$Jldw);
		//去掉数组中的重复值
		$result = array_unique($resultdw);		
		return $result;   */  
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
	 * 得到一品多价数据
	 *
	 * @param array $filter
	 * @return string xml
	 */

	public function getGridData($filter) {

		//检索SQL
		$sql = "SELECT A.JLDW ,A.JLGG,A.BHSHJG,A.HSHJ,A.KHDJ,A.BEIZHU ".
		       "FROM H01DB012104 A left join H01DB012001 B ".
			   "ON A.QYBH = B.QYBH and B.ZIHAOMA = A.JLDW AND B.CHLID='DW' ".
		       "WHERE A.QYBH = :QYBH AND A.SHPBH =:SHPBH ";
		
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
		foreach ( $_POST ["#grid_duojia"] as $grid ) {
			if ($grid [$this->idx_JLDW] != "") {
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
	public function logicCheck($shpbh) {
		
		//一品多价合法性（对应计量单位在商品表和一品多价表中是否存在）
		//$grid = $_POST ["#grid_duotiaoma"];
		$rtnlogic['rtncode'] = 0;
		foreach ( $_POST ["#grid_duojia"] as $grid ) {
			$filter ['jldw'] = $grid [$this->idx_JLDW];
			$filter ['shpbh'] = $shpbh;
			if ($this->getShangpinInfo($filter) && $this->getBaozhuangdwInfo($filter))
			{
				$rtnlogic['rtncode'] = 1;
				$rtnlogic['jldw'] = $filter ['jldw'];
				return $rtnlogic;
			} 

		}
		return $rtnlogic;
	}
	
	/**
	 * 检查商品资料表中该商品包装单位是否存在
	 */
	function getShangpinInfo($filter){
		//检索SQL
		$sql = "SELECT COUNT(*) FROM H01DB012101 WHERE QYBH = :QYBH AND SHPBH = :SHPBH AND BZHDWBH = :BZHDW";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['SHPBH'] = $filter ['shpbh'];		
		$bind ['BZHDW'] = $filter ["jldw"];
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
	 * 检查商品拆散单位信息表中该包装单位是否存在
	 */
	function getBaozhuangdwInfo($filter){
		//检索SQL
		$sql = "SELECT COUNT(*) FROM H01DB012117 WHERE QYBH = :QYBH AND SHPBH = :SHPBH AND BZHDWBH = :BZHDW";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['SHPBH'] = $filter ['shpbh'];				
		$bind ['BZHDW'] = $filter ["jldw"];
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
	 * 删除一品多价信息
	 *
	 * @return bool
	 */
	function delDuojia($spbh) {
		$sql = "DELETE FROM H01DB012104 WHERE QYBH = :QYBH AND SHPBH = :SHPBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'SHPBH' => $spbh );
		return $this->_db->query ( $sql , $bind );
	}
	
	/*
	 * 一品多条码信息保存
	 */
	public function saveDuojia($spbh) {
        //循环所有明细行，保存多条码信息
		foreach ( $_POST ["#grid_duojia"] as $grid ) {
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; 		//区域编号
			$data ['SHPBH'] = $spbh; 						//商品编号
			$data ['JLDW'] = $grid [$this->idx_JLDW];		//计量单位编号
			$data ['JLGG'] = $grid [$this->idx_JLGG];		//计量规格
			$data ['KHDJ'] = $grid [$this->idx_KHDJ];		//客户等级
			$data ['BHSHJG'] = $grid [$this->idx_BHSHJ];	//不含税价			
			$data ['BEIZHU'] = $grid [$this->idx_BEIZHU]; 	//备注
			$data ['HSHJ'] = $grid [$this->idx_HSHJ]; 		//客户等级
			$data ['BGRQ'] = new Zend_Db_Expr ("SYSDATE");	//变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; 	//变更者
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			//一品多条码信息
			$this->_db->insert ( "H01DB012104", $data );	
		}
	}
}
