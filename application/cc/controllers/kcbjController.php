<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   库存报警
 * 作成者：侯殊佳
 * 作成日：2011/05/19
 * 更新履历：
 *********************************/
class cc_kcbjController extends cc_controllers_baseController {
	/*
     * 库存报警画面显示
     */
	public function indexAction() {
		$this->_view->assign ( 'title', '仓储管理-库存报警' );
		$this->_view->display ( 'kcbj_01.php' );
	}
	
	/*
	 * 得到库存报警列表数据
	 */
	public function getlistdataAction(){
	//取得分页排序参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
		$model = new cc_models_kcbj();
		header ( "Content-type:text/xml" ); //返回数据格式xml		
		echo $model->getGridData ( $filter );
	}

	
	
}