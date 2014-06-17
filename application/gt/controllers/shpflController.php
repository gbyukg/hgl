<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：   商品分类选择画面
 * 作成者：周义
 * 作成日：2010/12/02
 * 更新履历：
 *********************************/
class gt_shpflController extends gt_controllers_baseController {

	/**
	 * 商品分类弹出画面
	 * flg:0 无根节点  1:有根节点
	 */
	public function treeAction(){
		$this->_view->assign("flg",$this->_getParam('flg','0'));
		$this->_view->display ( "shpfl_01.php" );
	}
		
    /**
	 * 商品分类树形列表xml
	 * flg:0 无根节点  1:有根节点
	 */
	public function gettreedataAction(){
		$filter['flg'] = $this->_getParam('flg','0');
		$shpfl_model = new gt_models_shpfl ( );
		header("Content-type:text/xml");
		echo $shpfl_model->getTreeData($filter);
	}
}