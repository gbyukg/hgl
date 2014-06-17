<?php
/******************************************************************
 ***** 模块：    采购模块(CG)
 ***** 机能：    采购退货详情(CGKPXQ)
 ***** 作成者：刘枞
 ***** 作成日：2011/03/14
 ***** 更新履历：
 ***** 
 ******************************************************************/

class cg_cgkpxqController extends cg_controllers_baseController {
	/**
	 * 采购开票详情初始页面
	 */
	public function loadAction(){
    	$Model = new cg_models_cgkpxq();
		$filter ['JS'] = $this->_getParam ( "JS", '0' );             //警示标识
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' );    //排序方式
		$bh = $this->_getParam ( "bh", '' ); 	                    //单据编号
		$this->_view->assign ( "title", "采购管理-采购订单详情" );   //标题
		$this->_view->assign ( "filter", $filter );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "rec", $Model->getInfo($bh) );	
		$this->_view->display( "cgkpxq_01.php" );
	}


	/**
	 * 退货单明细列表xml数据取得
	 */
	public function getmxdataAction(){
		//取得列表参数
		$bh = $this->_getParam ( "bh", '' ); 	             //编号
		$model = new cg_models_cgkpxq();
		header ( "Content-type:text/xml" );                  //返回数据格式xml
		echo $model->getMingxiData( $bh );
	}
	
	
	/*
	 * 取得上下条采购退货详情
	 */
	public function getxinxiAction(){
		$bh = $this->_getParam ( 'bh', '' );
		$JS = $this->_getParam ( 'JS', '' );
//		$filter ['ksrqkey'] = $this->_getParam ( "ksrqkey", '' ); 	     //开始日期
//		$filter ['zzrqkey'] = $this->_getParam ( "zzrqkey", '' ); 	     //终止日期
//		$filter ['dwbhkey'] = $this->_getParam ( "dwbhkey", '' ); 	     //单位编号
//		$filter ['dwmchkey'] = $this->_getParam ( "dwmchkey", '' );      //单位名称
//		$filter ['sh'] = $this->_getParam ( "sh", '' );                  //审核标识
//		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	     //排序列
//		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$flg = $this->_getParam ( 'flg', "current" );                    //检索方向

		$model = new cg_models_cgkpxq();
		$rec = $model->getxinxi( $bh, $JS, $flg );
		if ($rec == FALSE) {    //没有找到记录
			echo 'false';
		}else{
			$this->_view->assign ( "rec", $rec );
			echo  $this->_view->fetchPage( "cgkpxq_01.php" );
		}
	}

}