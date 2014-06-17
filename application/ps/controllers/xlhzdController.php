<?php
/*********************************
 * 模块：    配送模块(PS)
 * 机能：   线路汇总单(XLHZD)
 * 作成者：刘枞
 * 作成日：2011/08/19
 * 更新履历：
 *********************************/
class ps_xlhzdController extends ps_controllers_baseController {
	
	/*
	 * 初始页面
	 */
	public function indexAction() {
		$Model = new ps_models_xlhzd();
		$this->_view->assign ( "fahuoqu", $Model->getFHQ() ); //取得发货区数据，并传到	画面
		$this->_view->display ( "xlhzd_01.php" );
	}
	
	
	/*
	 * 列表xml数据取得
	 */
	public function getthdlistdataAction() {
		$model = new ps_models_xlhzd();
		header ( "Content-type:text/xml" );                 //返回数据格式xml
		echo $model->getGridDanjuData( $_POST );
	}

	
}