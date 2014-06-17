<?php
/******************************************************************
 ***** 模块：    采购模块(CG)
 ***** 机能：    采购退货详情(CGTHXQ)
 ***** 作成者：刘枞
 ***** 作成日：2011/01/13
 ***** 更新履历：
 ***** 
 ******************************************************************/

class cg_cgthxqController extends cg_controllers_baseController {
	/**
	 * 采购退货详情初始页面
	 */
	public function loadAction(){
    	$Model = new cg_models_cgthxq();
		$filter ['ksrqkey'] = $this->_getParam ( "ksrqkey", '' ); 	//开始日期
		$filter ['zzrqkey'] = $this->_getParam ( "zzrqkey", '' ); 	//终止日期
		$filter ['dwbhkey'] = $this->_getParam ( "dwbhkey", '' ); 	//单位编号
		$filter ['dwmchkey'] = $this->_getParam ( "dwmchkey", '' ); //单位名称
		$filter ['sh'] = $this->_getParam ( "sh", '' );             //审核标识
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' );    //排序方式
		$bh = $this->_getParam ( "bh", '' ); 	                    //单据编号
		$this->_view->assign ( "title", "采购管理-采购退货详情" );  //标题
		$this->_view->assign ( "filter", $filter );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "rec", $Model->getInfo($bh) );	
		$this->_view->display( "cgthxq_01.php" );
	}


	/**
	 * 退货单明细列表xml数据取得
	 */
	public function getmxdataAction(){
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );       //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		     //默认显示数量
		$filter ['bh'] = $this->_getParam ( "bh", '' ); 	             //编号
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	     //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new cg_models_cgthxq();
		header ( "Content-type:text/xml" );                          //返回数据格式xml
		echo $model->getMingxiData( $filter );
	}
	
	
	/*
	 * 取得上下条采购退货详情
	 */
	public function getxinxiAction(){
		$bh = $this->_getParam ( 'bh', '' );
		$filter ['ksrqkey'] = $this->_getParam ( "ksrqkey", '' ); 	     //开始日期
		$filter ['zzrqkey'] = $this->_getParam ( "zzrqkey", '' ); 	     //终止日期
		$filter ['dwbhkey'] = $this->_getParam ( "dwbhkey", '' ); 	     //单位编号
		$filter ['dwmchkey'] = $this->_getParam ( "dwmchkey", '' );      //单位名称
		$filter ['sh'] = $this->_getParam ( "sh", '' );             //审核标识
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	     //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$filter ['BJ'] = $this->_getParam ( "BJ", '0' );                 //标记    0：查询页面详情      1：审核页面详情
		$flg = $this->_getParam ( 'flg', 'current' );                    //检索方向

		if( $filter ['BJ']=='0' ){
			$filter['filterParams'] = $_SESSION['cg_cgthcx_filterParams'];  //精确查询条件
			$filter['searchParams'] = $_SESSION['cg_cgthcx_searchParams'];  //固定查询条件
			$filter['orderby'] = $_SESSION["sortParams"]["orderby"];        //排序
			$filter['direction'] = $_SESSION["sortParams"]["direction"];    //排序
		}else{
			$filter['filterParams'] = $_SESSION['cg_cgthsh_filterParams'];  //精确查询条件
			$filter['searchParams'] = $_SESSION['cg_cgthsh_searchParams'];  //固定查询条件
			$filter['orderby'] = $_SESSION["sortParams"]["orderby"];        //排序
			$filter['direction'] = $_SESSION["sortParams"]["direction"];    //排序
		}

		$model = new cg_models_cgthxq();
		$rec = $model->getxinxi( $bh, $filter, $flg );
		if ($rec == FALSE) {    //没有找到记录
			echo 'false';
		}else{
			$this->_view->assign ( "rec", $rec );
			echo  $this->_view->fetchPage( "cgthxq_01.php" );
		}
	}

}