<?php
/**********************************************************
 * 模块：    仓储模块(XS)
 * 机能：    销售订单详情(XSDDXQ)
 * 作成者：刘枞
 * 作成日：2011/01/28
 * 更新履历：
 **********************************************************/
class xs_xsddxqController extends xs_controllers_baseController {
	/*
	 * 销售订单详情初始页面
	 */
	public function loadAction(){
		$bh = $this->_getParam( "bh" );        //库间调拨出库单查询画面传递过来的单据编号
		$model = new xs_models_xsddxq();
		$filter ['ksrqkey'] = $this->_getParam ( "ksrq", '' ); 	         //开始日期
		$filter ['zzrqkey'] = $this->_getParam ( "zzrq", '' ); 	         //终止日期
		$filter ['dwbhkey'] = $this->_getParam ( "dwbh", '' ); 	         //单位编号
		$filter ['dwmchkey'] = $this->_getParam ( "dwmch", '' );         //单位名称
		$filter ['shsj'] = $this->_getParam ( "shsj", '' );                 //审核标识
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	        //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' );    //排序方式
		$this->_view->assign ( "fahuoqu", $model->getFHQInfo() );           //取得发货区数据，并传到画面
		$this->_view->assign ( "rec", $model->getinfoData( $bh ) ); 
		$this->_view->assign ( "filter", $filter );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "title", "销售管理-销售订单详情" );       //标题
		$this->_view->display ( "xsddxq_01.php" );
	}


	/**
     * 销售订单明细信息
     */
	public function getmingxiAction(){
    	$filter ['bh'] = $this->_getParam('bh');         //编号
 		$Model = new xs_models_xsddxq();
	    echo Common_Tool::json_encode($Model->getmingxi($filter));
	}
	
	
	/*
	 * 取得上下条销售订单详情
	 */
	public function getxinxiAction(){
		$bh = $this->_getParam ( 'bh', '' );
		$filter ['ksrqkey'] = $this->_getParam ( "ksrqkey", '' ); 	     //开始日期
		$filter ['zzrqkey'] = $this->_getParam ( "zzrqkey", '' ); 	     //终止日期
		$filter ['dwbhkey'] = $this->_getParam ( "dwbhkey", '' ); 	     //单位编号
		$filter ['dwmchkey'] = $this->_getParam ( "dwmchkey", '' );      //单位名称
		$filter ['sh'] = $this->_getParam ( "sh", '0' );                 //审核标识
//		$filter ['orderby'] = $this->_getParam ( "orderby", 3 ); 	     //排序列
//		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$flg = $this->_getParam ( 'flg', "current" );                    //检索方向
		
		$filter['filterParams'] = $_SESSION['xs_xskpsh_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['xs_xskpsh_searchParams'];  //固定查询条件
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];        //排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];    //排序
		
		$model = new xs_models_xsddxq();
		$this->_view->assign ( "fahuoqu", $model->getFHQInfo() );   //取得发货区数据，并传到画面
		$rec = $model->getxinxi( $bh, $filter, $flg );
		if ($rec == FALSE) {    //没有找到记录
			echo 'false';
		}else{
			$this->_view->assign ( "rec", $rec );
			echo  $this->_view->fetchPage( "xsddxq_01.php" );
		}
	}
	
}