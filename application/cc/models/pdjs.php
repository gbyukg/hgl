<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：  盘点结束(pdjs)
 * 作成者：dltt
 * 作成日：2010/11/10
 * 更新履历：

 *********************************/
class cc_models_pdjs extends Common_Model_Base {

	
/**
	 * 查找对应库区的状态信息

	 * @param string $ckbh   仓库编号
	 * 

	 * @return bool
	 */
	function getPdjsOne( $djbh){
		
			$sql = "SELECT CKMCH,KQMCH,KWMCH,QYBH,DJBH,PDLX,TO_CHAR(PDKSHSHJ,'YYYY-MM-DD') AS PDKSHSHJ,TO_CHAR(PDJSHSHJ,'YYYY-MM-DD') AS PDJSHSHJ,PDJHDH,CKBH,KQBH,KWBH,ZHMSHLTJ,DJBZH,YWYBH,BMBH,SHPYWY,SHPBM,JSHYWY,JSHBM,PDZHT,JZHZHT,ZHMJEHJ,SHPJEHJ,SYJEHJ,BEIZHU,TO_CHAR(BGRQ,'YYYY-MM-DD HH24:MI:SS') AS BGRQ,BGZH " 
			      ." FROM H01VIEW012417" 
//			      ." LEFT JOIN H01DB012401 B ON A.CKBH = B.CKBH AND A.QYBH =B.QYBH "
//				  ." LEFT JOIN H01DB012402 C ON A.CKBH = C.CKBH AND A.KQBH = C.KQBH AND A.QYBH =C.QYBH "
//				  ." LEFT JOIN H01DB012403 D ON A.CKBH = D.CKBH AND A.KQBH = D.KQBH AND A.KWBH = D.KWBH AND A.QYBH =D.QYBH  "
			      ." WHERE QYBH =:QYBH AND DJBH =:DJBH AND PDZHT = :PDZHT";
			
//			$bind = array('QYBH'=>$_SESSION ['auth']->qybh, 'DJBH' => $djbh ,'PDZHT'=>'1');
			$bind["QYBH"] = $_SESSION['auth']->qybh;
			$bind["DJBH"] = $djbh;
			$bind["PDZHT"] = '1';
			
			$recs = $this->_db->fetchRow($sql,$bind);
	
			return $recs;


	}
	/*判断盘点信息发生了变化
     * @param string $djbh   单据编号
	 * @param string $bgzh   变更者
	 * @param string $bgrq   变更日期
	 * @return bool
	 */
	function checkPdwhUpdate( $djbh,$bgzh,$bgrq){
			$sql = "SELECT BGZH,BGRQ  " 
			      . " FROM H01DB012417 A" 
			      . " WHERE A.QYBH =:QYBH AND A.DJBH =:DJBH AND A.BGZH = :BGZH AND TO_CHAR(A.BGRQ,'YYYY-MM-DD hh24:mi:ss') = :BGRQ FOR UPDATE";
			
			$bind = array('QYBH'=>$_SESSION ['auth']->qybh, 'DJBH' => $djbh ,'BGZH'=>$bgzh,'BGRQ'=>$bgrq);
			
			$recs = $this->_db->fetchOne( $sql, $bind );
			
			if($recs == false){
				return false;
			}else{
				return true;
			}
		
	}
	
	/*以区域编号，单据编号为条件，更新盘点信息 
     * @param string $bumen_h      单据编号
	 * @param string $yewuyuan_h   变更者
	 * @return bool
	 */
	function updatePdjs( $bumen_h,$yewuyuan_h, $djbh,$bgzh,$bgrq){
		try {
			$this->beginTransaction();
			
			if ($this->checkPdwhUpdate($djbh,$bgzh,$bgrq)==false){
				
				return false;
			}
			
	 		$sql = " UPDATE H01DB012417 SET " 
				. " PDJSHSHJ = SYSDATE,"
				. " JSHYWY = :JSHYWY,"
				. " JSHBM = :JSHBM,"
				. " PDZHT = :PDZHT,"
				. " BGRQ = SYSDATE, " 
				. " BGZH = :BGZH"
				. " WHERE QYBH = :QYBH AND DJBH = :DJBH";
			
			$bind ['DJBH'] = $_POST ['DJBH_H']; //单据编号
			$bind ['JSHYWY'] = $yewuyuan_h; //结束业务员
			$bind ['JSHBM'] = $bumen_h; //结束部门
			$bind ['PDZHT'] = '2';
    		$bind ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			$bind ['QYBH']= $_SESSION ['auth']->qybh;
			$this->_db->query($sql, $bind);
			
			$this->updateKwxinxi();
			
			$this->commit();
			
		} catch ( Exception $e ) {
			//回滚
			$this->rollBack ();
     		throw $e;
		
		}
		
	 	return true;
	}
	
	/*将对象库位变为盘点冻结状态
     * @param string $bumen_h      单据编号
	 * @param string $yewuyuan_h   变更者
	 * @return bool
	 */
	function updateKwxinxi(){
			
	 
	 $sql = " UPDATE H01DB012403 SET " 
			. " KWZHT = YZHT,"
			. " YZHT = NULL "
			. " WHERE QYBH = :QYBH AND CKBH =:CKBH AND KQBH = :KQBH";

			$bind ['CKBH'] = $_POST ['CKBH_H']; //仓库编号
			$bind ['KQBH'] = $_POST ['KQBH_H']; //库区编号
			
			if ($_POST ['KWBH_H'] != ''){
				$sql .= " AND KWBH=:KWBH ";
				$bind ['KWBH'] = $_POST ['KWBH_H']; //库位编号
			}
			$bind ['QYBH']= $_SESSION ['auth']->qybh;
	
			$this->_db->query($sql,$bind);
			
			return true;
		
	}
/*
	 * 画面必须输入项验证
	 */
	public function inputCheck() {
		if ($_POST ["JSHBM"] == "" || $_POST["BUMEN_H"] == "" ||  //部门编号
            $_POST ["YEWUYUAN"] == "" || $_POST ["YEWUYUAN_H"] == "")//员工编号
            { //明细表格
			return false;
		}
		
		return true;
	}
}
