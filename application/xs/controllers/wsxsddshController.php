<?php
/***************************************************************
 * 模块：    销售模块(XS)
 * 机能：    网上销售订单审核(WSXSDDSH)
 * 作成者：刘枞
 * 作成日：2011/11/04
 * 更新履历：
 ***************************************************************/
class xs_wsxsddshController extends xs_controllers_baseController {
	/*
	 * 销售订单维护初始页面
	 */
	public function indexAction() { 	
		$this->_view->assign ( "title", "销售管理-销售订单维护" ); //标题
		$this->_view->display ( "wsxsddsh_01.php" );
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
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 );         //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式	

		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];

		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['xs_wsxsddsh_searchParams'] = $_POST;
				unset($_SESSION['xs_wsxsddsh_filterParams']); //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['xs_wsxsddsh_filterParams'] = $_POST;
				unset($_SESSION['xs_wsxsddsh_searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['xs_wsxsddsh_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['xs_wsxsddsh_searchParams'];  //固定查询条件

		$model = new xs_models_wsxsddsh();
		header ( "Content-type:text/xml" );          //返回数据格式xml
		echo $model->getGridData( $filter );
 	}
 	
 	
 	/*
 	 *  获取销售订单明细信息列表
 	 */
 	public function getmingxilistdataAction(){
		$filter ['dwbh'] = $this->_getParam ( "dwbh" );                    //单位编号
		$filter ['ddbh'] = $this->_getParam ( "ddbh" );                    //订单编号
		$model = new xs_models_wsxsddsh();
		header ( "Content-type:text/xml" );         //返回数据格式xml
		echo $model->getMingxiGridData($filter);
 	}
 	
 	
 	/*
 	 *  获取销售订单明细信息列表-编辑页面
 	 */
 	public function getmingxiAction(){
		$filter ['dwbh'] = $this->_getParam ( "dwbh" );                    //单位编号
		$filter ['ddbh'] = $this->_getParam ( "ddbh" );                    //订单编号
		$model = new xs_models_wsxsddsh();
		echo Common_Tool::json_encode($model->getMingxi($filter));
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
    	$Model = new xs_models_wsxsddsh();
		$filter ['ddbh'] = $this->_getParam ( "ddbh", '' ); 	            //订单编号
		$filter ['dwbh'] = $this->_getParam ( "dwbh", '' ); 	            //单位编号
		$this->_view->assign ( "title", "销售管理-网上销售订单详情" );           //标题
		$this->_view->assign ( "rec", $Model->getinfoData($filter) );	
		$this->_view->display( "wsxsddsh_02.php" );
	}
	
	
	/*
	 * 作废销售订单
	 */
	public function deletexsddAction(){
		try{
			$Model = new xs_models_wsxsddsh();
		    $Model->beginTransaction ();	   //开始一个事务
			$filter ['dwbh'] = $this->_getParam ( "dwbh" );                    //单位编号
			$filter ['ddbh'] = $this->_getParam ( "ddbh" );                    //订单编号
			$Model->updataxsddzht($filter);        //更新销售订单取消标志
			Common_Logger::logToDb( "网上销售订单删除  订单编号：".$filter ['ddbh'] );
			$Model->commit();                  //事务提交
		}catch( Exception $e ){
			$Model->rollBack();		           //事务回滚
     		throw $e;
		}
	}
	
	
	/*
	 * 销售订单数据保存
	 */
	public function saveAction() {
		try {
			$Model = new xs_models_wsxsddsh();
			//开始一个事务
		    $Model->beginTransaction ();
		    //删除旧的网上销售订单(销售单，销售单明细)
		    $Model->delXshd($_POST);
            //生成新的网上销售订单(销售单，销售单明细)
		    $Model->createXshd($_POST);
		    $Model->commit();
		    Common_Logger::logToDb("网上销售订单修改  订单编号：".$_POST['DJBH']);
		} catch ( Exception $e ) {
			$Model->rollBack ();			//回滚
     		throw $e;
		}
	}
	
	
	/*
	 * 网上销售订单批量确认
	 */
	public function querenAction() {
		$Model = new xs_models_wsxsddsh();
		
	    $Model->queren($_POST);  //循环处理网上销售订单确认
	}
	
}