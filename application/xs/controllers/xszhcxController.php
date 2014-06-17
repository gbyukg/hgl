<?php
/***************************************************************
 * 模块：    销售模块(XS)
 * 机能：    销售综合查询(XSZHCX)
 * 作成者：刘枞
 * 作成日：2011/12/13
 * 更新履历：
 ***************************************************************/
class xs_xszhcxController extends xs_controllers_baseController {
	/*
	 * 销售综合查询初始页面
	 */
	public function indexAction() { 	
		$this->_view->assign ( "title", "销售管理-销售综合查询" ); //标题
		$this->_view->display ( "xszhcx_01.php" );
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
		$filter ['shpbh'] = $this->_getParam ( "shpbh" );
		$filter ['orderby'] = $this->_getParam ( "orderby", 3 );         //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式	

		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];

		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['xs_xszhcx_searchParams'] = $_POST;
				unset($_SESSION['xs_xszhcx_filterParams']); //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['xs_xszhcx_filterParams'] = $_POST;
				unset($_SESSION['xs_xszhcx_searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['xs_xszhcx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['xs_xszhcx_searchParams'];  //固定查询条件

		$model = new xs_models_xszhcx();
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
		$model = new xs_models_xszhcx();
		header ( "Content-type:text/xml" );         //返回数据格式xml
		echo $model->getMingxiGridData($filter);
 	}
 	
 	
	/**
     * 取得单位信息
     */
	public function getdanweiinfoAction(){
    	$filter ['dwbh'] = $this->_getParam('dwbh');   //单位编号
 		$Model = new xs_models_xszhcx();
	    echo Common_Tool::json_encode($Model->getDanweiInfo($filter));
	}
	
	
	/*
     * 获取商品信息
     */
	public function getspxxAction() {
		$model = new xs_models_xszhcx();
		$result = $model->getSpxx($this->_getParam('spbh'));
		echo Common_Tool::json_encode($result);
	}
	
}