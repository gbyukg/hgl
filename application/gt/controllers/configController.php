<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    部门选择Controller
 * 作成者：周义
 * 作成日：2010/12/15
 * 更新履历：
 *********************************/
class gt_configController extends gt_controllers_baseController {

	public function  gridAction() {
		$this->_view->display ( "config_01.php" );
	}
	
	public function getgridoptionsAction(){
		$gridId = $this->_getParam("gridid","");
		$model = new gt_models_config();
		$grid["Layout"] = $model->getGridLayout($gridId);
		$grid["Filter"] = $model->getGridFilter($gridId);
		echo json_encode($grid);
		
	}
	
	/*
	 * 表格项目隐藏设定保存
	 */
	public function savegridhiddencolsAction(){
		$model = new gt_models_config();
		$model->SaveGridHiddenCols($_POST);
	}
	
}