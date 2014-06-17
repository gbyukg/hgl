<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    采购退货出库(CKDJCX)
 * 作成者：刘枞
 * 作成日：2010/12/22
 * 更新履历：
 *********************************/
class cc_ckdjcxController extends cc_controllers_baseController {
	
	/*
	 * 采购退货出库初始页面
	 */
	public function indexAction() {
		$this->_view->assign ( "title", "仓储管理-出库单据查询" ); //标题
		$this->_view->display ( "ckdjcx_01.php" );
	}
	
	
	/*
	 * 退货单查询列表xml数据取得
	 */
	public function getthdlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );       //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		     //默认显示数量
		$filter ['ksrqkey'] = $this->_getParam ( "ksrqkey", '' ); 	     //开始日期
		$filter ['zzrqkey'] = $this->_getParam ( "zzrqkey", '' ); 	     //终止日期
		$filter ['ckdkey'] = $this->_getParam ( "ckdkey", '' ); 	     //出库单编号
		$filter ['dwkey'] = $this->_getParam ( "dwkey", '' );            //单位名称
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	     //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'DESC' ); //排序方式
		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cc_ckdjcx_searchParams'] = $_POST;
				unset($_SESSION['cc_ckdjcx_filterParams']); //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cc_ckdjcx_filterParams'] = $_POST;
				unset($_SESSION['cc_ckdjcx_searchParams']); //清空一般查询条件
			}
		}
		
		//取得检索条件
		$filter['filterParams'] = $_SESSION['cc_ckdjcx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['cc_ckdjcx_searchParams'];  //固定查询条件
		
		$model = new cc_models_ckdjcx();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridDanjuData( $filter );
	}
}