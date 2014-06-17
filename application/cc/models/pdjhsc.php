<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：  盘点计划生成(pdjhsc)
 * 作成者：dltt
 * 作成日：2010/11/10
 * 更新履历：

 *********************************/
class cc_models_pdjhsc extends Common_Model_Base {

	/**
	 * 查找对应库区的状态信息

	 * @param string $ckbh   仓库编号
	 * 

	 * @return bool
	 */
	function getCkstatus( $ckbh){
		
			$sql = "SELECT COUNT(1) AS BHCNT " 
			      . " FROM H01DB012401 " 
			      . " WHERE   QYBH=:QYBH AND  CKBH =:CKBH AND CKZHT <>:CKZHT ";
			
			$bind = array('QYBH' =>$_SESSION ['auth']->qybh ,'CKBH' => $ckbh, 'CKZHT' => 'X');
			
			$recs = $this->_db->fetchOne( $sql, $bind );
			
			if($recs['BHCNT'] == 0){
				return false;
			}else{
				return true;
			}
	}
	
	/**
	 * 查找对应库位的状态信息

	 * @param string $ckbh   仓库编号
	 * @param string $kqbh   库区编号

	 * @return bool
	 */
	function getKcstatus($ckbh, $kqbh){

			$sql = "SELECT COUNT(1) FROM H01DB012402 WHERE   QYBH=:QYBH AND  CKBH =:CKBH AND KQBH=:KQBH AND KQZHT <>:KQZHT";
			$bind = array('QYBH' =>$_SESSION ['auth']->qybh ,'CKBH' => $ckbh, 'KQZHT' => 'X', 'KQBH'=>$kqbh);
			$recs = $this->_db->fetchOne( $sql, $bind );
			
			if($recs['BHCNT'] == 0){
				return false;
			}else{
				return true;
			}
		
	}
	
		/**
	 * 查找对应库位的状态信息

	 * @param string $ckbh   仓库编号
	 * @param string $kqbh   库区编号
	 * @param string $kwbh   库位编号
	 * @return bool
	 */
	
	function getKwstatus( $ckbh, $kqbh ,$kwbh ){
			$sql = "SELECT COUNT(1) FROM H01DB012403 WHERE   QYBH=:QYBH AND  CKBH =:CKBH AND KQBH = :KQBH AND KWBH=:KWBH AND  KWZHT <>:KWZHT";
			$bind = array('QYBH' =>$_SESSION ['auth']->qybh ,'CKBH' => $ckbh, 'KWZHT' => 'X', 'KQBH'=>$kqbh,'KWBH'=>$kwbh);
			$recs = $this->_db->fetchOne( $sql, $bind );
			
			if($recs['BHCNT'] == 0){
				return false;
			}else{
				return true;
			}
	}

	/**
	 * 生成盘点信息
	 *
	 * @param unknown_type $pdjbh
	 * @return bool
	 */
	function insertpdjhsc($pdjbh) {
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			
			$data ['PDJHDH'] = $pdjbh; //盘点计划单号
			$data ['CKBH'] = $_POST ['CKBH_H']; //仓库编号
			$data ['KQBH'] = $_POST ['KQBH_H']; //库区编号
			$data ['KWBH'] = $_POST ['KWBH_H']; //库位编号
			$data ['YJKSHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['YJKSHRQ'] . ' ' . $_POST ['YJKSHRQT'] . "','yyyy/mm/dd hh24:mi:ss')" ); //预计开始日期
			
			$data ['YJJSHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['YJJSHRQ']  . ' ' . $_POST ['YJJSHRQT'] . "','yyyy/mm/dd hh24:mi:ss')" ); //预计结束日期
			
			$data ['TQTZHRSH'] = str_replace(',','',$_POST ['TQTZHRSH']); //提前通知日数
			$data ['YWYBH'] = $_POST ['YEWUYUAN_H']; //业务员
			$data ['BMBH'] = $_POST ['BMBH']; //部门
		    $data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
		    $data ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			//插入盘点信息表

			$this->_db->insert ( "H01DB012416", $data );
			return true;
	}
}
