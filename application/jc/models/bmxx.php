<?php

/******************************************************************
 ***** 模         块：       基础模块(JC)
 ***** 机         能：       商品分类(bmxx)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2010/11/24
 ***** 更新履历：
 ******************************************************************/

class jc_models_bmxx extends Common_Model_Base {
	
	/**
	 * 部门树形数据取得
	 * @param  string $flg  选择范围  0: 仅可用  1:全部
	 * @param 
	 * @return xml
	 */
	public function getTreeData($flg) {
		$sql = "SELECT SYS_CONNECT_BY_PATH(BMBH,'/') AS PATH,BMBH,".
			   "BMBH||':'||BMMCH||DECODE(BMZHT,'0','（已禁用）','') AS BMMCH,SHJBM FROM H01DB012112 ".
		       "WHERE QYBH =:QYBH  ".
		        ($flg=="0"? " AND BMZHT = '1'":"").
		       "START WITH SHJBM = '999999' CONNECT BY PRIOR BMBH = SHJBM " .
		       "ORDER SIBLINGS BY BMBH ";

		//绑定查询变量       
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh);
		$recs = $this->_db->fetchAll ( $sql,$bind);
		return Common_Tool::createTreeXml($recs,'BMBH','BMMCH');
	}
	
	
	/**
	 * 取得部门信息
	 *
	 * @param string $bmbh   部门编号
	 * @return array 
	 */
	function getBmxx($bmbh) {
		//检索SQL
		$sql = "SELECT BMBH,BMMCH,SHJBM,ZHJM,BMZHT,to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') AS BGRQ,BGZH FROM H01DB012112 WHERE QYBH =:QYBH AND BMBH =:BMBH";
		//绑定查询条件
		$bind = array('QYBH' => $_SESSION ['auth']->qybh, 'BMBH' => $bmbh );
		return $this->_db->fetchRow( $sql, $bind );
	}
	
	
	/**
	 * 判断是否禁用
	 *
	 * @param string $bmbh   部门编号
	 * @return array 
	 */
	function checkstatus($bmbh) {
		//检索SQL
		$sql = "SELECT BMBH,BMMCH,SHJBM FROM H01DB012112 WHERE BMZHT='0' AND QYBH =:QYBH AND BMBH =:BMBH";
		//绑定查询条件
		$bind = array('QYBH' => $_SESSION ['auth']->qybh, 'BMBH' => $bmbh );
		return $this->_db->fetchRow( $sql, $bind );
	}
	
	
	/**
	 * 判断是否有未禁用的下级部门
	 *
	 * @param string $bmbh   部门编号
	 * @return array 
	 */
	function lowerstatus($bmbh) {
		//检索SQL
		$sql = "SELECT BMBH,BMMCH,SHJBM FROM H01DB012112 WHERE BMZHT='1' AND QYBH =:QYBH AND SHJBM =:BMBH";
		//绑定查询条件
		$bind = array('QYBH' => $_SESSION ['auth']->qybh, 'BMBH' => $bmbh );
		return $this->_db->fetchRow( $sql, $bind );
	}
	
	
	/**
	 * 判断是否有被禁用的上级部门
	 *
	 * @param string $bmbh   部门编号
	 * @return array 
	 */
	function superiorstatus($bmbh) {
		//检索SQL
		$sql = "SELECT BMBH,BMMCH,SHJBM FROM H01DB012112 WHERE BMZHT='0' AND QYBH =:QYBH AND BMBH =(SELECT SHJBM FROM H01DB012112 WHERE QYBH =:QYBH AND BMBH =:BMBH)";
		//绑定查询条件
		$bind = array('QYBH' => $_SESSION ['auth']->qybh, 'BMBH' => $bmbh );
		return $this->_db->fetchRow( $sql, $bind );
	}
	
	
	/**
	 * 生成部门信息
	 *
	 * @return bool
	 */
	function insert() {
		//判断编号是否存在
		if ($this->getBmxx( $_POST ['BMBH'] ) != FALSE) {
			return false;
		} else {
			if($_POST ['node'] == 'fnode'){        //插入根分类
				$data ['SHJBM'] = '999999';                  //上级分类编号		
			} else{                                //插入子分类
				$data ['SHJBM'] = $_POST ['SHJBM'];          //上级分类编号
			}
			$data ['QYBH'] = $_SESSION ['auth']->qybh;       //区域编号
			$data ['BMBH'] = $_POST ['BMBH'];                //编号
			$data ['BMMCH'] = $_POST ['BMMCH'];              //名称
			$data ['ZHJM'] = $_POST ['ZHJM'];                //注记码
			$data ['BMZHT'] = '1';                           //部门状态
			$data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId;     //操作用户
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			//插入仓库信息表
			$this->_db->insert ( "H01DB012112", $data );
			return true;
		}
	}
	
	/**
	 * 修改部门信息
	 *
	 * @return bool
	 */
	function update() {
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') FROM H01DB012112 WHERE QYBH = :QYBH AND BMBH = :BMBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'BMBH' => $_POST ['BMBH'] );
		$timestamp = $this->_db->fetchOne ( $sql, $bind );
		//时间戳已经变更
		if ($timestamp != $_POST ['BGRQ']) {
			return false;                                 //时间戳发生变动
		} else {
			$sql = "UPDATE H01DB012112 SET BMMCH = :BMMCH, ZHJM = :ZHJM, BGRQ = SYSDATE, BGZH = :BGZH  WHERE QYBH = :QYBH AND BMBH =:BMBH";			
			$data ['BMMCH'] = $_POST ['BMMCH'];           //仓库名称
			$data ['ZHJM'] = $_POST ['ZHJM'];             //助记码
			$data ['BGZH'] = $_SESSION ['auth']->userId;  //操作用户	
			$data ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
			$data ['BMBH'] = $_POST ['BMBH'];             //部门编号
			$this->_db->query( $sql, $data );			
			return true;
		}
	}
	
	/**
	 * 部门信息（变更状态）
	 * @return bool
	 */
	function updateStatus($bmbh,$bmzht) {
			$sql = "UPDATE H01DB012112 SET BMZHT =:BMZHT WHERE QYBH = :QYBH AND BMBH =:BMBH";			
			$bind = array('BMZHT' => $bmzht, 'QYBH' => $_SESSION ['auth']->qybh, 'BMBH' => $bmbh);            
			$this->_db->query( $sql,$bind );		
			return true;
	}
	
	
}
