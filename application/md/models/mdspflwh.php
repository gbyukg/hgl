<?php
/******************************************************************
 ***** 模         块：       门店模块(MD)
 ***** 机         能：       门店商品分类维护(mdspflwh)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/02/10
 ***** 更新履历：
 ******************************************************************/

class md_models_mdspflwh extends Common_Model_Base {
	
	/**
	 * 商品分类树形数据取得
	 *
	 * @return xml
	 */
	public function gettreeData($mdbh) {
		$sql = "select sys_connect_by_path(SHPFL,'/') path,SHPFL,SHPFL||':'||FLMCH AS FLMCH,SHJFL FROM H01DB012508 WHERE QYBH = :QYBH AND MDBH = :MDBH " . "start with SHPFL = :SHPFL connect by prior SHPFL = SHJFL " . "order SIBLINGS by SHPFL ";
		$bind = array('QYBH' => $_SESSION ['auth']->qybh , 'MDBH' => $mdbh ,'SHPFL' => '999999' );
		$recs = $this->_db->fetchAll( $sql,$bind );
		return Common_Tool::createTreeXml($recs,'SHPFL','FLMCH');
	}
	
	
	/**
	 * 取得商品分类信息
	 *
	 * @param string $shpfl   商品分类编号
	 * @return array 
	 */
	function getShpfl($shpfl,$mdbh) {
		//检索SQL
		$sql = "SELECT SHPFL,SHJFL,FLMCH,BEIZHU FROM H01DB012508 WHERE QYBH =:QYBH AND SHPFL =:SHPFL AND MDBH = :MDBH";
		//绑定查询条件
		$bind = array('QYBH' => $_SESSION ['auth']->qybh, 'SHPFL' => $shpfl, 'MDBH' => $mdbh );
		return $this->_db->fetchRow( $sql, $bind );
	}
	
	
	/**
	 * 查找子节点和分类商品是否存在
	 * @param string $shpfl   商品分类编号
	 * @return bool
	 */
	function getxinxi( $shpfl,$mdbh ){
		$sql = "SELECT COUNT(*) FROM H01DB012508 WHERE QYBH =:QYBH AND SHJFL =:SHJFL AND MDBH = :MDBH";
		$bind = array('QYBH' => $_SESSION ['auth']->qybh, 'SHJFL' => $shpfl, 'MDBH' => $mdbh);
		$temp = $this->_db->fetchOne( $sql, $bind );
		if($temp == 0){
			$sql = "SELECT COUNT(*) FROM H01DB012101 WHERE QYBH =:QYBH AND FLBM =:FLBM";
			$bind = array('QYBH' => $_SESSION ['auth']->qybh, 'FLBM' => $shpfl);
			$temp = $this->_db->fetchOne( $sql, $bind );
			if($temp == 0){
				return true;
			}else{
				return false;
			}
		}else{    //编号存在
			return false;
		}
	}
	
	
	/**
	 * 生成商品分类信息
	 *
	 * @return bool
	 */
	function insert() {
		//判断编号是否存在
		if ($this->getShpfl( $_POST ['SHPFL'] ) != FALSE) {
			return false;
		} else {
			if($_POST ['node'] == 'fnode'){              //插入根分类
				$data ['SHJFL'] = '999999';              //上级分类编号		
			} else{  //插入子分类
				$data ['SHJFL'] = $_POST ['SHJFL'];      //上级分类编号
			}
			$data ['QYBH'] = $_SESSION ['auth']->qybh;   //区域编号
			$data ['MDBH'] = $_POST ['MDBH'];            //门店编号
			$data ['SHPFL'] = $_POST ['SHPFL'];          //分类编号
			$data ['FLMCH'] = $_POST ['FLMCH'];          //分类名称
			$data ['BEIZHU'] = $_POST ['BEIZHU'];        //分类备注
			$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' );    //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId;        //变更者
			//插入仓库信息表
			$this->_db->insert ( "H01DB012508", $data );
			return true;
		}
	}
	
	
	/**
	 * 修改商品分类信息
	 *
	 * @return bool
	 */
	function update() {
		$sql = "UPDATE H01DB012508 SET FLMCH =:FLMCH, BEIZHU =:BEIZHU, BGRQ = SYSDATE, BGZH =:BGZH WHERE QYBH = :QYBH AND SHPFL =:SHPFL AND MDBH = :MDBH";			
		$data ['FLMCH'] = $_POST ['FLMCH'];           //分类名称
		$data ['BEIZHU'] = $_POST ['BEIZHU'];         //分类备注
		$data ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
		$data ['SHPFL'] = $_POST ['SHPFL'];           //分类编号
		$data ['MDBH'] = $_POST ['MDBH'];             //门店编号
		$data ['BGZH'] = $_SESSION ['auth']->userId;  //变更者
		$this->_db->query( $sql, $data );	          //***		
		return true;
	}
	
	
	/**
	 * 删除商品分类信息
	 *
	 * @return bool
	 */
	function delete($shpfl,$mdbh) {
		$sql = "DELETE FROM H01DB012508 WHERE QYBH = :QYBH AND MDBH = :MDBH AND SHPFL =:SHPFL";			
		$bind = array('QYBH' => $_SESSION ['auth']->qybh, 'MDBH' => $mdbh, 'SHPFL' => $shpfl );            
		$this->_db->query( $sql,$bind );		
		return true;
	}
	
}
