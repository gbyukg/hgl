<?php
/*
 * 部门选择列表画面
 */
class co_models_bumen extends Common_Model_Base  {
	/**
	 * database connection
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $_db = null;
	
	public function __construct() {
		$this->_db = Zend_Registry::get ( "db" );
	}
	
	/**
	 * 部门树形数据取得
	 *
	 * @return xml
	 */
	public function getTreeData($startLevel = '000000') {
		$sql = "select sys_connect_by_path(BMBH,'/') AS PATH,BMBH,BMMCH,SHJBM FROM H01DB012112 " . "start with SHJBM =  " . $startLevel . " connect by prior BMBH = SHJBM " . "order SIBLINGS by BMBH ";
		
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
			$itemArr [$currLevel]->setAttribute ( "text", $rec ["BMMCH"] );
			$itemArr [$currLevel]->setAttribute ( "id", $rec ["BMBH"] );
			
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
