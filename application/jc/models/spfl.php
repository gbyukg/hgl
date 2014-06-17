<?php

/******************************************************************
 ***** 模         块：       基础模块(JC)
 ***** 机         能：       商品分类(spfl)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2010/11/18
 ***** 更新履历：
 ******************************************************************/

class jc_models_spfl extends Common_Model_Base {
	
/**
	 * 商品分类树形数据取得
	 *
	 * @return xml
	 */
	public function gettreeData($startLevel = '999999') {
		$sql = "select sys_connect_by_path(SHPFL,'/') path,SHPFL,SHPFL||':'||FLMCH AS FLMCH,SHJFL from H01DB012109 WHERE QYBH =:QYBH  " . "start with SHJFL =  " . $startLevel . " connect by prior SHPFL =SHJFL " . "order SIBLINGS by SHPFL ";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh);
		$recs = $this->_db->fetchAll ( $sql,$bind );
		$itemArr = array ();
		$dom = new DOMDocument ( '1.0', 'utf-8' );
		$root = $dom->createElement ( "tree" );
		$root->setAttribute ( "id", "0" );
		$dom->appendChild ( $root );
		$currLevel = 0; //当前级别
		$prevLevel = 0; //前一条级别
		
		foreach ( $recs as $rec ) {
			$currLevel = count ( split ( "/", $rec ["PATH"] ) ) - 2;
			$itemArr [$currLevel] = $dom->createElement ( "item" );
			$itemArr [$currLevel]->setAttribute ( "text", $rec ["FLMCH"] );
			$itemArr [$currLevel]->setAttribute ( "id", $rec ["SHPFL"] );
			if ($currLevel <= $prevLevel) {
				
				if ($currLevel == 0) {
					$root->appendChild ( $itemArr [$currLevel] );
				} else {
					$itemArr [$currLevel - 1]->appendChild ( $itemArr [$currLevel] );
				}
			} elseif ($currLevel > $prevLevel) {
				$itemArr [$prevLevel]->appendChild ( $itemArr [$currLevel] );
			}
			$prevLevel = $currLevel;
		}
		return $dom->saveXML ();
	}
	
	
	/**
	 * 取得商品分类信息
	 *
	 * @param string $shpfl   商品分类编号
	 * @return array 
	 */
	function getShpfl($shpfl) {
		//检索SQL
		$sql = "SELECT SHPFL,SHJFL,FLMCH,BEIZHU FROM H01DB012109 WHERE QYBH =:QYBH AND SHPFL =:SHPFL";
		//绑定查询条件
		$bind = array('QYBH' => $_SESSION ['auth']->qybh, 'SHPFL' => $shpfl );
		return $this->_db->fetchRow( $sql, $bind );
	}
	
	
	/**
	 * 查找子节点和分类商品是否存在
	 * @param string $shpfl   商品分类编号
	 * @return bool
	 */
	function getxinxi( $shpfl ){
			$sql = "SELECT COUNT(*) FROM H01DB012109 WHERE QYBH =:QYBH AND SHJFL =:SHJFL";
			$bind = array('QYBH' => $_SESSION ['auth']->qybh, 'SHJFL' => $shpfl);
			$temp = $this->_db->fetchOne( $sql, $bind );
			if($temp == 0){
				$sql = "SELECT COUNT(*) FROM H01DB012101 WHERE QYBH =:QYBH AND FLBM =:FLBM ";
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
			if($_POST ['node'] == 'fnode'){   //插入根分类
				$data ['SHJFL'] = '999999';              //上级分类编号		
			} else{             //插入子分类
				$data ['SHJFL'] = $_POST ['SHJFL'];      //上级分类编号
			}
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['SHPFL'] = $_POST ['SHPFL']; //分类编号
			$data ['FLMCH'] = $_POST ['FLMCH']; //分类名称
			$data ['BEIZHU'] = $_POST ['BEIZHU']; //分类备注
			//插入仓库信息表
			$this->_db->insert ( "H01DB012109", $data );
			return true;
		}
	}
	
	/**
	 * 修改商品分类信息
	 *
	 * @return bool
	 */
	function update() {
			$sql = "UPDATE H01DB012109 SET FLMCH =:FLMCH, BEIZHU =:BEIZHU WHERE QYBH = :QYBH AND SHPFL =:SHPFL";			
			$data ['FLMCH'] = $_POST ['FLMCH'];           //分类名称
			$data ['BEIZHU'] = $_POST ['BEIZHU'];         //分类备注
			$data ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
			$data ['SHPFL'] = $_POST ['SHPFL'];             //分类编号
			$this->_db->query( $sql, $data );	//***		
			return true;
	}
	
	/**
	 * 删除商品分类信息
	 *
	 * @return bool
	 */
	function delete($shpfl) {
			$sql = "DELETE FROM H01DB012109 WHERE QYBH = :QYBH AND SHPFL =:SHPFL";			
			$bind = array('QYBH' => $_SESSION ['auth']->qybh, 'SHPFL' => $shpfl);            
			$this->_db->query( $sql,$bind );		
			return true;
	}
	
}
