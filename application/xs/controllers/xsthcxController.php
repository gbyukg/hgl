<?php
/*********************************
 * 模块：    销售模块(XS)
 * 机能：    销售退货查询(XSTH)
 * 作成者：孙宏志
 * 作成日：2011/01/14
 * 更新履历：
 *********************************/
class xs_xsthcxController extends xs_controllers_baseController {

	/*
	 * 销售退货初始页面
	 */
	public function indexAction() {
		$this->_view->assign ( "kshrq", date('Y-m-d',strtotime('-14 day')));  //开票日期
		$this->_view->assign ( "zhzhrq", date("Y-m-d"));   //开票日期		
		$this->_view->assign ( "title", "销售管理-销售退货查询" ); //标题
		$this->_view->display ( "xsthcx_01.php" );
	}
	
	
	 /**
     * 取得单位信息
     */
	public function getdanweiinfoAction(){
    	$filter ['dwbh'] = $this->_getParam('dwbh');   //单位编号
 		$xskpModel = new xs_models_xsthcx();
	    echo Common_Tool::json_encode($xskpModel->getDanweiInfo($filter));
	}
	
	
	/**
     * 取得退货单信息
     */
	public function getthdAction(){
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 10 ); //默认显示数量
    	$filter ['dwbh'] = $this->_getParam('dwbh');
    	$filter ['kshrq'] = $this->_getParam('kshrq');
    	$filter ['zhzhrq'] = $this->_getParam('zhzhrq');
    	$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['xs_xsthcx_searchParams'] = $_POST;
				unset($_SESSION['xs_xsthcx_filterParams']); //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['xs_xsthcx_filterParams'] = $_POST;
				unset($_SESSION['xs_xsthcx_searchParams']); //清空一般查询条件
			}
		}
		
		//取得检索条件
		$filter['filterParams'] = $_SESSION['xs_xsthcx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['xs_xsthcx_searchParams'];  //固定查询条件
		
 		$Model = new xs_models_xsthcx();
 		echo $Model->getTHD($filter);
	}
	
	
	/**
     * 取得退货单详细信息
     */
	public function getthdxxAction(){
    	$filter ['thdh'] = $this->_getParam('thdh');
  		$Model = new xs_models_xsthcx();
 		echo $Model->getTHDXX($filter);
	}
	
	
	/*
     * 入库单据详情画面
     */
	public function detailAction() {
		$model = new xs_models_xsthcx();
		$thdbh = $this->_getParam ( "thdbh" );//入库单编号
		$rec = $model->getTHDNR( $thdbh, '', 'current' );
		//查询画面传递过来的查询条件(带查询的上下条用)
		$this->_view->assign ( "ksrqkey", $this->_getParam ( "ksrqkey", '' ) ); //开始日期检索条件
		$this->_view->assign ( "zzrqkey", $this->_getParam ( "zzrqkey", '' ) ); //终止日期检索条件
		$this->_view->assign ( "thshzt", $this->_getParam ( "thshzt", '' ) ); //退货审核状态
		$this->_view->assign ( "dwbhkey", $this->_getParam ( "dwbhkey", '' ) ); //单位编号检索条件
		$this->_view->assign ( "orderby", $this->_getParam ( "orderby", '' ) ); //排序列
		$this->_view->assign ( "direction", $this->_getParam ( "direction", '' ) ); //排序方式
		$this->_view->assign ( "title", '退货单据详情');
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "rec", $rec );
		$this->_view->display ( 'xsthcx_02.php' );
	}
	
	
	/*
	 * 取得入库单据信息上下条
	 */
	public function getthAction() {
		$thdbh = $this->_getParam ( "thdbh" );

		//检索条件
		$filter ['rkdbhkey'] = $this->_getParam ( "rkdbhkey", '' );      //入库单据号
		$filter ['dwbhkey'] = $this->_getParam ( "dwbhkey", '' );        //单位编号
		$filter ['ksrqkey'] = $this->_getParam ( "ksrqkey", '' );        //开始日期
		$filter ['thshzt'] = $this->_getParam ( "thshzt", '' );          //退货审核状态
		$filter ['zzrqkey'] = $this->_getParam ( "zzrqkey", '' );        //终止日期
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 );         //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$filter ['BJ'] = $this->_getParam ( "BJ", '0' );                 //标记    0：查询页面详情      1：审核页面详情
		$flg = $this->_getParam ( 'flg', 'current' );                    //检索方向

		if( $filter ['BJ']=='0' ){
			$filter['filterParams'] = $_SESSION['xs_xsthcx_filterParams'];  //精确查询条件
			$filter['searchParams'] = $_SESSION['xs_xsthcx_searchParams'];  //固定查询条件
			$filter['orderby'] = $_SESSION["sortParams"]["orderby"];        //排序
			$filter['direction'] = $_SESSION["sortParams"]["direction"];    //排序
		}else{
			$filter['filterParams'] = $_SESSION['xs_xsthsh_filterParams'];  //精确查询条件
			$filter['searchParams'] = $_SESSION['xs_xsthsh_searchParams'];  //固定查询条件
			$filter['orderby'] = $_SESSION["sortParams"]["orderby"];        //排序
			$filter['direction'] = $_SESSION["sortParams"]["direction"];    //排序
		}

		$model = new xs_models_xsthcx();
		$rec = $model->getTHDNR ( $thdbh, $filter, $flg );
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "rec", $rec );
			echo json_encode ( $this->_view->fetchPage ( "xsthcx_02.php" ) );
		}
	}
	
	
	/**
     * 取得画面详情
     */
	public function getxiangqinginfo(){
		$filter ['spbh'] = $this->_getParam('spbh');
		$filter ['thdbh'] = $this->_getParam('thdbh');
 		$Model = new xs_models_xsthcx();
 		echo $Model->getXiangQing($filter);
	}
}