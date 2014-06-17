<?php
/**********************************************************
 * 模     块：    仓储模块(CC)
 * 机     能：    库间调拨入库维护(KJDBRKWH)
 * 作成者：    刘枞
 * 作成日：    2011/01/25
 * 更新履历：
 **********************************************************/
class cc_kjdbrkwhController extends cc_controllers_baseController {
	/*
	 * 库间调拨入库维护初始页面
	 */
	public function indexAction(){
		$this->_view->assign ( "title", "仓储管理-库间调拨入库维护" ); //标题
		$this->_view->display ( "kjdbrkwh_01.php" );
	}
	
	
	/*
	 * 库间调拨出库单选择页面
	 */
	public function showdbckdAction(){
		$this->_view->display ( "kjdbrkwh_02.php" );
	}
	
	
	/*
	 * 库间调拨入库维护列表xml数据取得
	 */
	public function getthdlistdataAction(){
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );       //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		     //默认显示数量
		$filter ['ksrqkey'] = $this->_getParam ( "ksrqkey", '' ); 	     //开始日期
		$filter ['zzrqkey'] = $this->_getParam ( "zzrqkey", '' ); 	     //终止日期
		$filter ['dbrkd'] = $this->_getParam ( "dbrkd", '' ); 	         //调拨入库单编号
		$filter ['dbckd'] = $this->_getParam ( "dbckd", '' );            //调拨出库单编号
		$filter ['dcck'] = $this->_getParam ( "dcck", '' ); 	         //调出仓库编号
		$filter ['drck'] = $this->_getParam ( "drck", '' );              //调入仓库编号
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	     //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new cc_models_kjdbrkwh();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridDanjuData( $filter );
	}
	
	
	/*
	 * 退货单列表xml数据取得(退货单选择页面)
	 */
	public function getthddataAction(){
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );       //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		     //默认显示数量
		$filter ['ksrqkey'] = $this->_getParam ( "ksrqkey", '' ); 	     //开始日期
		$filter ['zzrqkey'] = $this->_getParam ( "zzrqkey", '' ); 	     //终止日期
		$filter ['dcck'] = $this->_getParam ( "dcck", '' ); 	         //调出仓库编号
		$filter ['drck'] = $this->_getParam ( "drck", '' );              //调入仓库编号
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	     //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new cc_models_kjdbrkwh();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridthdData( $filter );
	}
	
}