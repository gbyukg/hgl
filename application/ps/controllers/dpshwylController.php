<?php
/*********************************
 * 模块：   配送模块(PS)
 * 机能：  待配送货物预览(dpshwyl)
 * 作成者：梁兆新
 * 作成日：2011/1/28
 * 更新履历：
 *********************************/
class ps_dpshwylController extends ps_controllers_baseController {
	/*
     * 待配送货物预览
     */
	public function indexAction() {
		$model = new ps_models_dpshwyl();
		$this->_view->assign ( 'title', '待配送货物预览' );
		$this->_view->assign ( "kprq", date("Y-m-d"));  //获取当前时间
		$this->_view->assign ( 'fhqlist',$model->getquhao());//得到发货区信息
		$this->_view->display ( 'dpshwyl_01.php' );
	}	
	
	/*
	 * 获取 出货单 货物列表
	 */
	public function getlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 10 ); //默认显示数量
		$filter ['serchstime'] = $this->_getParam ( "serchstime" );//开始时间
		$filter ['serchetime'] = $this->_getParam ( "serchetime" );//终止时间
		$filter ['serchfhqbh'] = $this->_getParam ( "serchfhqbh" );//发货区编号
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式		
		$model = new ps_models_dpshwyl();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );
	}
}
?>
