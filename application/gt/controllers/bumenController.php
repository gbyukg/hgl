<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    部门选择Controller
 * 作成者：周义
 * 作成日：2010/12/15
 * 更新履历：
 *********************************/
class gt_bumenController extends gt_controllers_baseController {

	/**
	 * 部门选择弹出画面
	 *flg：0 可用部门 1: 全部部门（包括已删除）
	 */
	public function  treeAction() {
		$this->_view->assign('flg',$this->_getParam('flg','0'));
		$this->_view->display ( "bumen_01.php" );
	}
	
	/**
	 * 部门列表数据取得
	 * flg：0 可用部门 1: 全部部门（包括已删除）
	 */
	public function gettreedataAction()	{
		$flg = $this->_getParam('flg','0');
		$bumen_model = new gt_models_bumen ( );
		header("Content-type:text/xml");
		echo $bumen_model->getTreeData($flg);
	}
	
	/*
	 * 自动完成数据取得
	 */
	public function autocompleteAction(){
		$searchkey = $this->_getParam('q');
        $bumen_model = new gt_models_bumen ( );
	    $result = $bumen_model->getAutocompleteData($searchkey);
	    echo json_encode($result);
	}
}