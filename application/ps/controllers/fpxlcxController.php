<?php
/*********************************
 * 模块：    配送模块(PS)
 * 机能：   分配线路查询(FPXLCX)
 * 作成者：刘枞
 * 作成日：2011/08/19
 * 更新履历：
 *********************************/
class ps_fpxlcxController extends ps_controllers_baseController {
	
	/*
	 * 初始页面
	 */
	public function indexAction() {
		$Model = new ps_models_fpxlcx();

		$this->_view->assign ( "fahuoqu", $Model->getFHQ() ); //取得发货区数据，并传到	画面
		$this->_view->display ( "fpxlcx_01.php" );
	}
	
	
	/*
	 * 列表xml数据取得
	 */
	public function getthdlistdataAction() {
		$model = new ps_models_fpxlcx();
		header ( "Content-type:text/xml" );                 //返回数据格式xml
		echo $model->getGridDanjuData( $_POST );
	}

	
}