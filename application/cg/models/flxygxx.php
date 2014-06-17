<?php
/*********************************
 * 模块：   采购模块(CG)
 * 机能：   采购返利协议信息(供应商)(flxygxx)
 * 作成者：handong
 * 作成日：2011/06/02
 * 更新履历：
 *********************************/
 
class cg_models_flxygxx extends Common_Model_Base {
	/**
	 * 取得用户的姓名与所属部门信息
	 */
    function getYgxx(){
	     //检索SQL
	     $sql = " SELECT YGBH,YGXM,SSBM,SSBMMCH " .
	            " FROM H01VIEW012113 WHERE QYBH = :QYBH AND YGBH = :YGBH";
	     $bind ['QYBH'] = $_SESSION ['auth']->qybh;
	     $bind ['YGBH'] = $_SESSION ['auth']->userId;
	     return $this->_db->fetchRow ( $sql, $bind );
	}
	
	/**
	 *  采购返利协议信息保存
	 */
	function insertFlxygxx($xybh) {
		$data ['QYBH'] = $_SESSION ['auth']->qybh;//区域编号
		$data ['XYBH'] = $xybh ;//采购返利协议编号
		$data ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$data ['BMBH'] = $_POST ['BMBH'];
		$data ['YWYBH']= $_POST ['YWYBH'];
		$data ['KSHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KSHRQ'] . "','YYYY-MM-DD')" ); //起始日期
		$data ['ZHZHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['ZHZHRQ'] . "','YYYY-MM-DD')" ); //终止日期
		$data ['DWBH'] = $_POST ['DWBH'];//单位编号
		$data ['DIZHI'] = $_POST ['DIZHI'];//地址
		$data ['DHHM'] = $_POST ['DHHM']; //电话号码
		$data ['XYLX'] = '0';   //协议类型
		$data ['FLFSH'] = $_POST ['FLFSH'];//返利方式
		$data ['ZHCLJSHL'] = $_POST ['ZHCLJSHL'];//政策累计数量
		$data ['ZHCLJJE'] = $_POST ['ZHCLJJE'];//政策累计金额
		$data ['FLJE'] = $_POST ['FLJE']; //返利金额
		$data ['BEIZHU'] = $_POST ['BEIZHU'];//备注
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$data ['ZHUANGTAI'] = '1';//状态
		return $this->_db->insert ( "H01DB012313", $data );     //插入出库单信息
	}
	
/**
	 * 更新库区信息
	 *
	 * @return bool
	 */
	function updateFlxygxx() {
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss'),BGZH FROM H01DB012313 WHERE QYBH = :QYBH AND XYBH = :XYBH FOR UPDATE WAIT 10";
		$bind1 = array ('QYBH' => $_SESSION ['auth']->qybh,'XYBH' => $_POST ['XYBH']);
		$timestamp = $this->_db->fetchOne ( $sql, $bind1 );
		
		//时间戳已经变更
		
		if ($timestamp != $_POST ['BGRQ']) {
			return false;
		} else {
			$sql = " UPDATE  H01DB012313 SET KSHRQ = TO_DATE(:KSHRQ,'YYYY-MM-DD'),ZHZHRQ = TO_DATE(:ZHZHRQ,'YYYY-MM-DD'),DWBH = :DWBH,DIZHI = :DIZHI,DHHM =:DHHM,FLFSH = :FLFSH, ".
			       " ZHCLJSHL = :ZHCLJSHL,ZHCLJJE = :ZHCLJJE,FLJE = :FLJE, BEIZHU = :BEIZHU,BGRQ = sysdate,BGZH = :BGZH WHERE QYBH =:QYBH AND XYBH =:XYBH";
			
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;//区域编号
		$bind ['XYBH'] = $_POST ['XYBH'] ;//采购返利协议编号	
		$bind ['KSHRQ'] = $_POST ['KSHRQ']; //起始日期
		$bind ['ZHZHRQ'] = $_POST ['ZHZHRQ']; //终止日期
		$bind ['DWBH'] = $_POST ['DWBH'];//单位编号
		$bind ['DIZHI'] = $_POST ['DIZHI'];//地址
		$bind ['DHHM'] = $_POST ['DHHM']; //电话号码
		$bind ['FLFSH'] = $_POST ['FLFSH'];//返利方式
		$bind ['ZHCLJSHL'] = $_POST ['ZHCLJSHL'];//政策累计数量
		$bind ['ZHCLJJE'] = $_POST ['ZHCLJJE'];//政策累计金额
		$bind ['FLJE'] = $_POST ['FLJE']; //返利金额
		$bind ['BEIZHU'] = $_POST ['BEIZHU'];//备注
		$bind ['BGZH'] = $_SESSION ['auth']->userId; //变更者

			$this->_db->query ( $sql, $bind );
			return true;
		}
        
		
	}
	/*
	 * 根据单位编号编号取得单位信息
	 */
	public function getDanweiInfo($filter) {
		//检索SQL
		$sql = "SELECT A.DWBH,A.DWMCH,A.DIZHI,A.DHHM,A.KOULV,A.FHQBH," . "DECODE(A.XSHXDQ,NULL,0,XSHXDQ) XSHXDQ" . //销售信贷期 
				" FROM H01DB012106 A" . " WHERE A.QYBH = :QYBH " . //区域编号
				" AND A.DWBH = :DWBH" . //单位编号
				" AND A.SHFJH = '1'" . //是否采购
				" AND A.KHZHT = '1'"; //客户状态
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
}
?>