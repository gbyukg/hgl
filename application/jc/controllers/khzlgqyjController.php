<?php
/**********************************************************
 * 模块：    基础模块(JC)
 * 机能：    客户资料过期预警(KHZLGQYJ)
 * 作成者：刘枞
 * 作成日：2011/08/12
 * 更新履历：
 **********************************************************/
class jc_khzlgqyjController extends jc_controllers_baseController {
	
	/*
	 * 设备登录查询初始页面
	 */
	public function indexAction() {
		$this->_view->assign ( "title", "基础管理-客户资料过期预警" ); //标题
		$this->_view->display ( "khzlgqyj_01.php" );
	}
	
	
	/*
	 * 设备登录查询列表xml数据取得
	 */
	public function getthdlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );       //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		     //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	     //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		
		$model = new jc_models_khzlgqyj();
		header ( "Content-type:text/xml" );            //返回数据格式xml
		echo $model->getGridDanjuData( $filter );
	}
}