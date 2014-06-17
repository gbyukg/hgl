<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    采购审核(RKSHH)
 * 作成者：ZhangZeliang
 * 作成日：2011/03/28
 * 更新履历：
 *********************************/

class cc_rkshhController extends cc_controllers_baseController {
	/*
	 * 采购审核页面
	 */
	public function indexAction() {
		$this->_view->assign ( "title", "仓储管理-入库审核" );
		$this->_view->assign ( "kprq", date ( "Y-m-d" ) );
		$this->_view->display ( "rkshh_01.php" );
	}
	
	/*
	 * 获取未审核单据数据信息
	 */
	public function getdjinfoAction() {
		$filter ["posStart"] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new cc_models_rkshh ( );
		header ( "Content-type:text/xml" ); //返回XML格式数据
		echo $model->getdjinfo ( $filter );
	}
	
	/*
	 * 获取商品明细信息
	 */
	public function getmxinfoAction() {
		$filter ["posStart"] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); //默认显示数量
		$filter ['yrkdbh'] = $this->_getParam ( 'yrkdbh', '' ); //预入库单 编号
		$model = new cc_models_rkshh ( );
		header ( "Content-type:text/xml" );
		echo $model->getmxinfo ( $filter );
	}
	
	/*
	 * 更新审核状态
	 */
	public function updateverifyAction() {
		$filter ["yrkdbh"] = $this->_getParam ( "yrkdbh" ); //预入库单编号
		$filter ["status"] = $this->_getParam ( "status" ); //审核状态 1"审核通过；2:审核不通过
		$filter ["cgyfhyj"] = $this->_getParam ( "cgyfhyj" ); //审核意见
		

		$model = new cc_models_rkshh ( );
		$model->updateVerify ( $filter );
		echo json_encode ( $filter );
	}
}

?>