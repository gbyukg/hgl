<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   客户特价(khtj)
 * 作成者：李洪波
 * 作成日：2011/01/05
 * 更新履历：
 *********************************/
class jc_models_khtj extends Common_Model_Base {
	private $idx_ROWNUM=0;         // 行号
	private $idx_SHPBH=1;          // 商品编号
	private $idx_SHPMC=2;          // 商品名称
	private $idx_BZHDW=3;          // 包装单位
	private $idx_HSHSHJ=4;         // 含税售价
	private $idx_SHOUJIA=5;        // 售价
	private $idx_HSHSHJTJ=6;       // 含税售价特价
	private $idx_XSTJ=7;           // 销售特价
	private $idx_ZHHSHSHJ=8;       // 最后含税售价
	private $idx_ZHSHJ=9;          // 最后售价
	private $idx_QSHZHXRQ=10;      // 起始执行日期
	private $idx_ZHZHZHXRQ=11;     // 终止执行日期
	private $idx_CHANDI=12;        // 产地
	private $idx_JXDX=13;          // 经销代销
	private $idx_SHUILV=14;        // 税率
	/**
	 * 获取单位名称
	 */
	function getDwmch($dwbh){
		$sql ="SELECT DWBH,DWMCH ".
		      "FROM H01DB012106 ".
		      "WHERE QYBH = :QYBH AND DWBH =:DWBH AND SHFXSH='1'";
		$bind = array( 'QYBH' => $_SESSION ['auth']->qybh, 'DWBH' => $dwbh );
		$Dwmch = $this->_db->fetchRow( $sql, $bind );
		return $Dwmch;
	}
	/**
	 * 检查特价信息是否存在
	 */
	function checkTjxx($filter){
		//检索SQL
		$sql = "SELECT COUNT(*) ".
			   " FROM H01DB012105 ".
			   " WHERE QYBH = :QYBH AND DWBH =:DWBH AND SHPBH =:SHPBH".
			   " AND TO_CHAR(ZHZHZHXRQ,'YYYY-MM-DD') >= :ZHXRQ ".
			   " AND TO_CHAR(QSHZHXRQ,'YYYY-MM-DD') <= :ZHZHRQ ";
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ["dwbh"];
		$bind ['SHPBH'] = $filter ["shpbh"];
		$bind ['ZHXRQ'] = $filter ["zhxrq"] == ""?"1900-01-01":$filter ["zhxrq"];
		$bind ['ZHZHRQ'] = $filter ["zhzhrq"] == ""?"9999-12-31":$filter ["zhzhrq"];
		$temp= $this->_db->fetchOne( $sql, $bind );
		if($temp == 0){
			return false;
		}else{
			return true;
		}
	}
	/**
	 * 得到客户特价数据
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter) {
		$tablefields = array ("SHPBH", "SHPMCH", "BZHDWBH", "HSHSHJ", "SHOUJIA", "HSHSHJTJ", "XSHTJ", "ZHHSHSHJ", "ZHSHJ", "QSHZHXRQ", "ZHZHZHXRQ", "CHANDI", "JXDX" ); //
		
		//检索SQL
		$sql = "SELECT SHPBH,SHPMCH,DWMCH,HSHSHJ,SHOUJIA,HSHSHJTJ,XSHTJ,ZHHSHSHJ,ZHSHJ,TO_CHAR(QSHZHXRQ,'YYYY-MM-DD'),".
			   " TO_CHAR(ZHZHZHXRQ,'YYYY-MM-DD'),CHANDI,JXDX,SHUILV".															
			   " FROM HO1UV012101".
		 	   " WHERE QYBH = :QYBH AND DWBH =:DWBH";
		//执行日期
		if ($filter['searchParams']["ZHXRQ"]!="")
		{
			$sql .= " AND ZHZHZHXRQ >= TO_DATE(:ZHXRQ,'YYYY-MM-DD') ";
			$bind ['ZHXRQ'] = $filter['searchParams']["ZHXRQ"] == ""?"1900-01-01":$filter['searchParams']["ZHXRQ"];
		}
		//终止日期
		if ($filter['searchParams'] ["ZHZHRQ"]!="")
		{
			$sql .= " AND QSHZHXRQ <= TO_DATE(:ZHZHRQ,'YYYY-MM-DD HH24:MI:SS') ";
			$bind ['ZHZHRQ'] = $filter['searchParams']["ZHZHRQ"] == ""?"9999-12-31 23:59:59":$filter['searchParams']["ZHZHRQ"]." 23:59:59";
		}
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("JC_KHTJ",$filter['filterParams'],$bind);
		$sql .= " ORDER BY " . $tablefields [$filter ["orderby"]] . " " . $filter ["direction"];
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter['searchParams']["DWBH"];
		$recs=$this->_db->fetchAll($sql, $bind);
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs);
	}	
	/*
	 * 根据商品编号取得商品信息
	 */
	public function getShangpinInfo($filter) {
		
		//检索SQL
		$sql = "SELECT " .
		       "A.SHPBH," . //商品编号
               "A.SHPMCH," . //商品名称
           	   "B.NEIRONG,A.HSHSHJ,A.SHOUJIA,".
			   "A.CHANDI,A.JXDX,A.SHUILV".
			   " FROM H01DB012101 A".
			   " LEFT JOIN H01DB012001 B ON B.QYBH=A.QYBH AND B.ZIHAOMA=A.BZHDWBH AND B.CHLID='DW'".
		 	   " WHERE A.QYBH = :QYBH AND A.SHPBH =:SHPBH";
		$bind ['SHPBH'] = $filter ['shpbh']; //商品编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		return $this->_db->fetchRow ( $sql, $bind );
	}
	/**
	 * 删除客户特价信息
	 * @return bool
	 */
	function delTejia($dwbh) {
		foreach ( $_POST ["#grid_tejia"] as $grid ) {
			//删除客户特价信息
			$sql = "DELETE FROM H01DB012105 WHERE QYBH = :QYBH AND DWBH = :DWBH".
					" AND SHPBH = :SHPBH".
					" AND QSHZHXRQ <= TO_DATE(:ZHZHZHXRQ,'YYYY-MM-DD')".
					" AND ZHZHZHXRQ >= TO_DATE(:QSHZHXRQ,'YYYY-MM-DD')";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['DWBH'] = $dwbh; //单位编号
			$bind ['SHPBH'] = $grid [$this->idx_SHPBH]; //商品编号
			$bind ['QSHZHXRQ'] = $grid [$this->idx_QSHZHXRQ]; //执行日期
			$bind ['ZHZHZHXRQ'] = $grid [$this->idx_ZHZHZHXRQ]; //终止日期
			$this->_db->query ( $sql , $bind );
		}
	}	
	/*
	 * 客户特价信息保存
	 */
	public function saveTejia($dwbh) {
        //循环所有明细行，保存客户特价信息
		foreach ( $_POST ["#grid_tejia"] as $grid ) {
			//客户特价信息
			$data ['QYBH'] = $_SESSION ['auth']->qybh;              //区域编号
			$data ['DWBH'] = $dwbh;                                 //单位编号
			$data ['SHPBH'] = $grid [$this->idx_SHPBH];             //商品编号
			$data ['HSHSHJTJ'] = $grid [$this->idx_HSHSHJTJ];       //含税售价特价
			$data ['XSHTJ'] = $grid [$this->idx_XSTJ];              //销售特价
			$data ['ZHHSHSHJ'] = $grid [$this->idx_ZHHSHSHJ];       //最后含税售价
			$data ['ZHSHJ'] = $grid [$this->idx_ZHSHJ];            //最后售价
			if ($grid [$this->idx_QSHZHXRQ] != ""){
			$data ['QSHZHXRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_QSHZHXRQ] . "','YYYY-MM-DD')" );
			}
			if ($grid [$this->idx_ZHZHZHXRQ] != ""){
			$data ['ZHZHZHXRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_ZHZHZHXRQ] . "','YYYY-MM-DD')" );
			}
			$data ['BGRQ'] = new Zend_Db_Expr ("SYSDATE"); //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId;   //变更者
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			$this->_db->insert ( "H01DB012105", $data );
		}
	}
}
