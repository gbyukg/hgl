<?php
/*
 * 商品分类选择列表画面
 */
class co_models_shangpinfenlei extends Common_Model_Base  {
	/**
	 * database connection
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $_db = null;
	
	public function __construct() {
		$this->_db = Zend_Registry::get ( "db" );
	}
	
	/**
	 * 树形数据取得
	 *
	 * @return xml
	 */
	public function getXmlData($startLevel = '0') {
		$sql = "select sys_connect_by_path(SHPFL,'/') path,SHPFL,FLMCH,SHJFL from H01DB012109 " . "start with SHJFL =  " . $startLevel . " connect by prior SHPFL =SHJFL " . "order SIBLINGS by SHPFL ";
		
		$recs = $this->_db->fetchAll ( $sql );
		
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
}
