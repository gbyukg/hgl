<?php
/*********************************
 * 模块：    销售模块(XS)
 * 机能：    销售订单维护(XSDDWH)
 * 作成者：刘枞
 * 作成日：2011/01/29
 * 更新履历：
 *********************************/
class xs_xsddwhController extends xs_controllers_baseController {
	/*
	 * 销售订单维护初始页面
	 */
	public function indexAction() { 	
		$this->_view->assign ( "title", "销售管理-销售订单维护" ); //标题
		$this->_view->display ( "xsddwh_01.php" );
	}
 	
 	/*
 	 *  获取销售订单维护单据信息列表
 	 */
 	public function getlistdataAction(){
 		//取得列表参数				
 		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );      //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 );           //默认显示数量
		$filter ['ksrq'] = $this->_getParam ( "ksrq" );
		$filter ['zzrq'] = $this->_getParam ( "zzrq" );
		$filter ['dwbh'] = $this->_getParam ( "dwbh" );
		$filter ['dwmch'] = $this->_getParam ( "dwmch" );
		$filter ['shsj'] = $this->_getParam ( "shsj" );
		$filter ['orderby'] = $this->_getParam ( "orderby", 3 );         //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式	

		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];

		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['xs_xsddwh_searchParams'] = $_POST;
				unset($_SESSION['xs_xsddwh_filterParams']); //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['xs_xsddwh_filterParams'] = $_POST;
				unset($_SESSION['xs_xsddwh_searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['xs_xsddwh_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['xs_xsddwh_searchParams'];  //固定查询条件

		$model = new xs_models_xsddwh();
		header ( "Content-type:text/xml" );          //返回数据格式xml
		echo $model->getGridData( $filter );
 	}
 	
 	/*
 	 *  获取销售订单明细信息列表
 	 */
 	public function getmingxilistdataAction(){
 		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );         //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 );              //默认显示数量
		$filter ['bh'] = $this->_getParam ( "bh" );
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 );           //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' );   //排序方式		
		$model = new xs_models_xsddwh();
		header ( "Content-type:text/xml" );         //返回数据格式xml
		echo $model->getMingxiGridData($filter);
 	}
 	
	/**
     * 取得单位信息
     */
	public function getdanweiinfoAction(){
    	$filter ['dwbh'] = $this->_getParam('dwbh');   //单位编号
 		$xskpModel = new cg_models_cgddwh() ;
	    echo Common_Tool::json_encode($xskpModel->getDanweiInfo($filter));
	}
	
	/**
	 * 销售订单详情初始页面
	 */
	public function xiangqingAction(){
    	$Model = new xs_models_xsddwh();
		$filter ['ksrqkey'] = $this->_getParam ( "ksrq", '' ); 	            //开始日期
		$filter ['zzrqkey'] = $this->_getParam ( "zzrq", '' );            	//终止日期
		$filter ['dwbhkey'] = $this->_getParam ( "dwbh", '' ); 	            //单位编号
		$filter ['dwmchkey'] = $this->_getParam ( "dwmch", '' );            //单位名称
		$filter ['shsj'] = $this->_getParam ( "shsj", '' );                 //审核标识
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	        //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' );    //排序方式
		$bh = $this->_getParam ( "bh", '' ); 	                            //单据编号
		$this->_view->assign ( "title", "销售管理-销售订单详情" );           //标题
		$this->_view->assign ( "filter", $filter );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "fahuoqu", $Model->getFHQInfo() );   //取得发货区数据，并传到画面
		$this->_view->assign ( "rec", $Model->getinfoData($bh) );	
		$this->_view->display( "xsddwh_02.php" );
	}
	
	/**
     * 销售订单详情明细信息
     */
	public function getmingxiAction(){
    	$filter ['bh'] = $this->_getParam('bh');         //编号
 		$Model = new xs_models_xsddwh();
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
		$filter ['shsj'] = $this->_getParam ( "shsj", '' );              //审核标识
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	     //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$flg = $this->_getParam ( 'flg', "current" );                    //检索方向
		
		$filter['filterParams'] = $_SESSION['xs_xsddwh_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['xs_xsddwh_searchParams'];  //固定查询条件
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];        //排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];    //排序
		
		$model = new xs_models_xsddwh();
		$this->_view->assign ( "fahuoqu", $model->getFHQInfo() );   //取得发货区数据，并传到画面
		$rec = $model->getxinxi( $bh, $filter, $flg );
		if ($rec == FALSE) {    //没有找到记录
			echo 'false';
		}else{
			$this->_view->assign ( "rec", $rec );
			echo  $this->_view->fetchPage( "xsddwh_02.php" );
		}
	}
	
	
	/*
	 * 作废销售订单
	 */
	public function deletexsddAction(){
		try{
			$Model = new xs_models_xsddwh();
		    $Model->beginTransaction ();	   //开始一个事务
			$bh = $this->_getParam('bh');      //获取编号
		    $Model->updateKucun($bh);          //库存相关数据更新（库存数量更新，商品移动履历）
			$Model->updataxsddzht($bh);        //更新销售订单取消标志
			Common_Logger::logToDb( "销售订单删除  编号：".$bh );
			$Model->commit();                  //事务提交
		}catch( Exception $e ){
			$Model->rollBack();		           //事务回滚
     		throw $e;
		}
	}
}