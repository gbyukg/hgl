<?php
/**********************************************************
 * 模块：    仓储模块(CC)
 * 机能：    库间调拨入库详情(KJDBRKXQ)
 * 作成者：刘枞
 * 作成日：2011/01/26
 * 更新履历：
 **********************************************************/

class cc_kjdbrkxqController extends cc_controllers_baseController {
	/*
	 * 库间调拨入库确认初始页面
	 */
	public function loadAction(){
		$bh = $this->_getParam( "bh" );        //库间调拨出库单查询画面传递过来的单据编号
		$model = new cc_models_kjdbrkxq();
		$rec = $model->getinfoData( $bh );
		$this->_view->assign ( "rec", $rec ); 
		$this->_view->assign ( "title", "仓储管理-库间调拨入库详情" );  //标题
		$this->_view->display ( "kjdbrkxq_01.php" );
	}


	/**
     * 取得库间调拨出库单明细信息
     */
	public function getmingxiAction(){
    	$filter ['bh'] = $this->_getParam('bh');         //编号
    	$filter ['ckbh'] = $this->_getParam('ckbh');     //仓库编号
 		$Model = new cc_models_kjdbrkxq();
	    echo Common_Tool::json_encode($Model->getmingxi($filter));
	}
}