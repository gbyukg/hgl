<?php
/*********************************
 * 模块：    销售模块(XS)
 * 机能：    一品多价画面
 * 作成者：周义
 * 作成日：2010/08/05
 * 更新履历：
 *********************************/
class xs_ypdjController extends xs_controllers_baseController {
	/*
	 * 一品多价画面
	 */
	public function listAction(){
		$this->_view->assign("shpbh",$this->_getParam('shpbh',''));
		$this->_view->assign("shpmch",$this->_getParam('shpmch',''));
		$this->_view->assign("dwbh",$this->_getParam('dwbh',''));
		$this->_view->display ( "ypdj_01.php" );
	}
	
	/*
	 * 列表数据取得
	 */
	public function getlistdataAction()
	{
		$filter ['shpbh'] = $this->_getParam("shpbh");  //商品编号
		$filter ['dwbh'] = $this->_getParam("dwbh");  //客户编号
			
		$model = new xs_models_ypdj();
		header("Content-type:text/xml");
		echo $model->getListData($filter);
	}
}