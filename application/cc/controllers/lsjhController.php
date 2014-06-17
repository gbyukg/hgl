<?php
/**********************************************************
 * 模     块：    仓储模块(CC)
 * 机     能：  零散拣货(lsjh)
 * 作成者：    姚磊
 * 作成日：    2011/03/22
 * 更新履历：
 **********************************************************/
class cc_lsjhController extends cc_controllers_baseController {
	/*
	 * 库间调拨入库维护初始页面
	 */
	public function indexAction(){
		$this->_view->assign ( "title", "仓储管理-零散拣货" ); //标题
		$this->_view->assign ( "userid", $_SESSION ['auth']->userId ); //登陆者
		$this->_view->display ( "lsjh_01.php" );
	}

	/*
	 * 查询整件拣货信息 返回xml格式
	 */
	public function getdataAction(){
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );       //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 10 ); 		     //默认显示数量
		$filter ['fxrqc'] = $this->_getParam ( "fxrqc", '' ); 	     //分箱日期从
		$filter ['fxrqd'] = $this->_getParam ( "fxrqd", '' ); 	     //分箱日期到
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	     //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new cc_models_lsjh();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData( $filter );
	}
	/*
	 * 更改状态
	 */
	public function updateAction(){
		
		$filter ['dytm'] = $this->_getParam ( "dytm", '');       //对应条码
		$model = new cc_models_lsjh();
		$model->update ($filter );
		//Common_Logger::logToDb ( ($_POST ['bgzht'] == '1' ? "已打印" : "待发送") . " 车牌号码：" . $_POST ['chphm'] );
	}
	/*
	 * 获取明细信息
	 */
	public function getmingxilistdataAction(){
		
		$dytm = $this->_getParam ( "flg" );		//获取对应条码
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式		
		$model = new cc_models_lsjh();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getMingxiGridData ($dytm,$filter );
	}
}