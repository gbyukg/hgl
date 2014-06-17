<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    库位批号选择画面
 * 作成者：刘枞
 * 作成日：2010/12/02
 * 更新履历：
 *********************************/
class cc_kwphxzController extends cc_controllers_baseController {
	
	/*
	 * 库存选择弹出画面
	 * 
	 */
	public function listAction(){
		$this->_view->assign('shpbh',$this->_getParam("shpbh"));//商品编号
		$this->_view->display ( "kwphxz_01.php" );
	}
	
	/*
	 * 库位批号选择画面数据取得
	 * 
	 */
	public function getlistdataAction()	{
		//业务相关参数
		$filter ['shpbh'] = $this->_getParam("shpbh"); //商品编号
		
		$model = new cc_models_kwphxz();
		header("Content-type:text/xml");
		echo $model->getListData($filter);		
	}
		
}