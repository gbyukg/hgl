<?php
/******************************************************************
 ***** 模块：    仓储模块(CC)
 ***** 机能：    销售出库详情(XSCKXQ)
 ***** 作成者：刘枞
 ***** 作成日：2010/12/29
 ***** 更新履历：
 ***** 
 ******************************************************************/

class cc_xsckxqController extends cc_controllers_baseController {
	/**
	 * 采购退货出库初始页面
	 */
	public function loadAction(){
    	$Model = new cc_models_xsckxq();
		$this->_view->assign ( "fahuoqu", $Model->getFHQInfo() );      //取得发货区数据，并传到画面
		$this->_view->assign ( "title", "仓储管理-销售出库详情" );      //标题
		//销售出库单信息获取并传递
		$this->_view->assign ( "rec", $Model->getxsckInfo($this->_getParam("ckdbh", '')));
		$this->_view->display( "xsckxq_01.php" );
	}
	
	
	/**
	 * 退货单明细列表xml数据取得
	 */
	public function getckdmxdataAction(){
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );       //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		     //默认显示数量
		$filter ['ckdbh'] = $this->_getParam ( "ckdbh", '' ); 	         //开始日期
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	     //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new cc_models_xsckxq();
		header ( "Content-type:text/xml" );                          //返回数据格式xml
		echo $model->getMingxiData( $filter );
	}
	
}